<?php

namespace App\Services;

/**
 * NotificationService
 * ─────────────────────────────────────────────────────────────────────
 * Responsibilities:
 *  1. OTP generation & session-based verification (dev mode: console log)
 *  2. WhatsApp Business Cloud API — booking confirmation message
 *
 * Env keys used:
 *   WHATSAPP_TOKEN          — Meta Cloud API bearer token
 *   WHATSAPP_PHONE_ID       — WhatsApp business phone number ID
 *   WHATSAPP_TEMPLATE_BOOK  — Template name (default: 'booking_confirmation')
 */
class NotificationService
{
    private string $waToken;
    private string $waPhoneId;
    private string $waTemplate;
    private string $waTemplateOtp;
    private string $waTemplateRefund;

    // OTP TTL in seconds (5 minutes)
    private const OTP_TTL = 300;

    public function __construct()
    {
        helper('basic');
        $this->waToken       = env('WHATSAPP_TOKEN', '');
        $this->waPhoneId     = env('WHATSAPP_PHONE_NUMBER_ID', '');
        $this->waTemplate    = env('WHATSAPP_TEMPLATE_BOOK', 'booking_confirmation');
        $this->waTemplateOtp = env('WHATSAPP_TEMPLATE_OTP', $this->waTemplate);
        $this->waTemplateRefund = env('WHATSAPP_TEMPLATE_REFUND', 'refund_processed');
    }

    /**
     * Send Refund Alert via WhatsApp
     */
    public function sendRefundAlert(string $phone, float $amount, string $listingTitle, string $txnId): bool
    {
        if (!$this->waToken || !$this->waPhoneId) return false;

        $to = $this->normaliseMsisdn($phone);
        $body = [
            'messaging_product' => 'whatsapp',
            'to'                => $to,
            'type'              => 'template',
            'template'          => [
                'name'       => $this->waTemplateRefund,
                'language'   => ['code' => 'en_US'],
                'components' => [
                    [
                        'type'       => 'body',
                        'parameters' => [
                            ['type' => 'text', 'text' => "₹" . number_format($amount, 2)],
                            ['type' => 'text', 'text' => $listingTitle],
                            ['type' => 'text', 'text' => $txnId]
                        ]
                    ]
                ]
            ]
        ];

        $resp = cnd_http_request('POST', "https://graph.facebook.com/v13.0/{$this->waPhoneId}/messages", $body, [
            "Authorization: Bearer {$this->waToken}",
            "Content-Type: application/json"
        ]);

        return ($resp->code >= 200 && $resp->code < 300);
    }

    // ── OTP ──────────────────────────────────────────────────────────────

    /**
     * Generate a 6-digit OTP, store it in session with TTL, and "send" it.
     * In dev/test (no real SMS provider), OTP is returned to the caller so it
     * can be shown on the UI.  Set WHATSAPP_TOKEN in .env to use real WA OTP.
     *
     * @param  string $phone  E.164 format preferred, e.g. +919876543210
     * @return array  ['otp'=>'123456', 'sent'=>bool]
     */
    public function sendOtp(string $phone): array
    {
        $otp = (string) random_int(100000, 999999);

        // Store in session keyed by phone
        $session = session();
        $session->set('cnd_otp_' . md5($phone), [
            'otp'     => $otp,
            'expires' => time() + self::OTP_TTL,
            'phone'   => $phone,
        ]);

        // Try to send via WhatsApp OTP template (if configured)
        $sent = false;
        if ($this->waToken && $this->waPhoneId) {
            $sent = $this->sendWhatsAppOtp($phone, $otp);
        } else {
            $this->lastError = "Configuration missing: WHATSAPP_TOKEN or WHATSAPP_PHONE_NUMBER_ID is not set in .env";
        }

        return ['otp' => $otp, 'sent' => $sent];
    }

    /**
     * Verify an OTP entered by the user.
     * Returns true and clears the OTP on success.
     */
    public function verifyOtp(string $phone, string $inputOtp): bool
    {
        $session = session();
        $key     = 'cnd_otp_' . md5($phone);
        $stored  = $session->get($key);

        if (!$stored || time() > $stored['expires']) {
            $session->remove($key);
            return false;
        }

        if (hash_equals($stored['otp'], trim($inputOtp))) {
            $session->remove($key);
            return true;
        }

        return false;
    }

    // ── WhatsApp OTP (plain text — works without a special OTP template) ─

    private function sendWhatsAppOtp(string $phone, string $otp): bool
    {
        $to = $this->normaliseMsisdn($phone);
        
        // Using a Template is required for starting a conversation or if the 24h window is closed.
        // Your template 'learnnextdoorv1' is an AUTHENTICATION template with 1 variable for OTP.
        log_message('info', "[NotificationService] Sending OTP (template: {$this->waTemplateOtp}) to " . $to);

        $body = [
            'messaging_product' => 'whatsapp',
            'to'                => $to,
            'type'              => 'template',
            'template'          => [
                'name'     => $this->waTemplateOtp,
                'language' => ['code' => 'en'], // Your template is in 'en'
                'components' => [
                    [
                        'type'       => 'body',
                        'parameters' => [
                            ['type' => 'text', 'text' => $otp]
                        ]
                    ],
                    [
                        'type'       => 'button',
                        'sub_type'   => 'url',
                        'index'      => '0',
                        'parameters' => [
                            ['type' => 'text', 'text' => $otp]
                        ]
                    ]
                ]
            ]
        ];

        return $this->postWhatsApp($body);
    }

    // ── Booking Confirmation WhatsApp ─────────────────────────────────────

    /**
     * Send WhatsApp booking confirmation to the parent's phone.
     *
     * @param  string $phone      Parent phone (raw digits or E.164)
     * @param  array  $booking    Row from BookingModel::getWithDetails()
     */
    public function sendBookingConfirmation(string $phone, array $booking): bool
    {
        if (!$this->waToken || !$this->waPhoneId) {
            log_message('info', '[NotificationService] WA skipped for #' . ($booking['id'] ?? '?') . ' phone=' . $phone);
            return false;
        }

        $title = $booking['listing_title'] ?? 'your class';
        $type  = $booking['listing_type'] ?? 'regular';
        
        // Schedule string
        $schedule = 'TBD';
        $d1 = !empty($booking['class_date']) ? date('d M Y', strtotime($booking['class_date'])) : null;
        $t1 = !empty($booking['class_time']) ? date('g:i A', strtotime($booking['class_time'])) : null;
        
        if ($type === 'course' && !empty($booking['listing_end_date'])) {
            $d2 = date('d M Y', strtotime($booking['listing_end_date']));
            $schedule = "$d1 to $d2" . ($t1 ? " at $t1" : "");
        } elseif ($d1) {
            $schedule = ($type === 'workshop' ? "Workshop on " : "") . $d1 . ($t1 ? " at $t1" : "");
        }

        $address = $booking['listing_address'] ?? 'Check the app for details';
        $ref     = '#' . str_pad($booking['id'] ?? 0, 6, '0', STR_PAD_LEFT);

        $text = "✅ *Booking Confirmed — Class Next Door*\n\n"
              . "*Class:* {$title}\n"
              . "*Schedule:* {$schedule}\n"
              . "*Venue:* {$address}\n"
              . "*Ref:* {$ref}\n\n"
              . "Show this message at the venue. Enjoy! 🎉";

        return $this->postWhatsApp([
            'messaging_product' => 'whatsapp',
            'to'                => $this->normaliseMsisdn($phone),
            'type'              => 'text',
            'text'              => ['body' => $text],
        ]);
    }

    /**
     * Notify provider about a new booking via WhatsApp.
     */
    public function notifyProviderNewBooking(string $phone, array $booking): bool
    {
        if (!$this->waToken || !$this->waPhoneId) return false;

        $title   = $booking['listing_title'] ?? 'your class';
        $student = $booking['student_name']    ?? 'A student';
        $type    = $booking['listing_type']    ?? 'regular';

        // Schedule string
        $schedule = 'TBD';
        $d1 = !empty($booking['class_date']) ? date('d M Y', strtotime($booking['class_date'])) : null;
        $t1 = !empty($booking['class_time']) ? date('g:i A', strtotime($booking['class_time'])) : null;
        
        if ($type === 'course' && !empty($booking['listing_end_date'])) {
            $d2 = date('d M Y', strtotime($booking['listing_end_date']));
            $schedule = "$d1 to $d2" . ($t1 ? " at $t1" : "");
        } elseif ($d1) {
            $schedule = $d1 . ($t1 ? " at $t1" : "");
        }

        $text = "🎉 *New Booking! — Class Next Door*\n\n"
              . "Good news! *{$student}* has booked your class: *{$title}*.\n"
              . "*Schedule:* {$schedule}\n\n"
              . "View details in your Provider Dashboard.";

        return $this->postWhatsApp([
            'messaging_product' => 'whatsapp',
            'to'                => $this->normaliseMsisdn($phone),
            'type'              => 'text',
            'text'              => ['body' => $text],
        ]);
    }

    /**
     * Notify provider that their listing is under review.
     */
    public function notifyListingUnderReview(string $phone, string $title): bool
    {
        if (!$this->waToken || !$this->waPhoneId) return false;

        $text = "📝 *Listing Under Review — Class Next Door*\n\n"
              . "Your class *'{$title}'* has been submitted and is currently under review by our team.\n\n"
              . "We'll notify you once it's live! usually within 24 hours.";

        $body = [
            'messaging_product' => 'whatsapp',
            'to'                => $this->normaliseMsisdn($phone),
            'type'              => 'text',
            'text'              => ['body' => $text],
        ];

        return $this->postWhatsApp($body);
    }

    /**
     * Notify provider that their listing has been approved/published.
     */
    public function notifyListingPublished(string $phone, string $title): bool
    {
        if (!$this->waToken || !$this->waPhoneId) return false;

        $text = "🚀 *Class is Live! — Class Next Door*\n\n"
              . "Congratulations! Your class *'{$title}'* has been approved and is now visible to parents.\n\n"
              . "Get ready for enrollments! 🌟";

        $body = [
            'messaging_product' => 'whatsapp',
            'to'                => $this->normaliseMsisdn($phone),
            'type'              => 'text',
            'text'              => ['body' => $text],
        ];

        return $this->postWhatsApp($body);
    }

    /**
     * Notify Admin of a new listing (Subtask 3.1).
     */
    public function notifyAdminNewListing(string $adminPhone, string $providerName, string $title): bool
    {
        if (!$this->waToken || !$this->waPhoneId) return false;

        $text = "🔔 *Admin Alert: New Listing — Class Next Door*\n\n"
              . "Provider: *{$providerName}*\n"
              . "Class: *{$title}*\n\n"
              . "Please review and approve the listing in the admin panel.";

        $body = [
            'messaging_product' => 'whatsapp',
            'to'                => $this->normaliseMsisdn($adminPhone),
            'type'              => 'text',
            'text'              => ['body' => $text],
        ];

        return $this->postWhatsApp($body);
    }

    /**
     * Notify Admin of new customer feedback/report (Subtask 3.1).
     */
    public function notifyAdminNewFeedback(string $adminPhone, string $fromName, string $subject): bool
    {
        if (!$this->waToken || !$this->waPhoneId) return false;

        $text = "📩 *Admin Alert: New Feedback — Class Next Door*\n\n"
              . "From: *{$fromName}*\n"
              . "Subject: *{$subject}*\n\n"
              . "Check the dashboard for full details.";

        $body = [
            'messaging_product' => 'whatsapp',
            'to'                => $this->normaliseMsisdn($adminPhone),
            'type'              => 'text',
            'text'              => ['body' => $text],
        ];

        return $this->postWhatsApp($body);
    }

    // ── Booking Reminder WhatsApp ─────────────────────────────────────────

    /**
     * Send a 1-hour advance reminder via WhatsApp.
     * Called by the reminders:send cron command.
     *
     * @param  string $phone    Parent phone
     * @param  array  $booking  Booking row from SendReminders::fetchDueBookings()
     */
    public function sendBookingReminder(string $phone, array $booking): bool
    {
        if (!$this->waToken || !$this->waPhoneId) {
            log_message('info', '[NotificationService] WA reminder skipped (not configured). booking #'
                . ($booking['id'] ?? '?'));
            return false;
        }

        $title   = $booking['listing_title'] ?? 'your class';
        $date    = !empty($booking['class_date'])
                     ? date('D, d M Y', strtotime($booking['class_date']))
                     : 'today';
        $time    = !empty($booking['class_time'])
                     ? date('g:i A', strtotime($booking['class_time']))
                     : '';
        $address = $booking['listing_address'] ?? '';
        $student = $booking['student_name']    ?? 'your child';

        $text = "⏰ *Reminder — Class Next Door*\n\n"
              . "*{$student}'s {$title}* starts in about *1 hour*!\n"
              . "*Date:* {$date}\n"
              . "*Time:* {$time}\n"
              . ($address ? "*Venue:* {$address}\n" : '')
              . "\nGet ready and have a great class! 🎒";

        $body = [
            'messaging_product' => 'whatsapp',
            'to'                => $this->normaliseMsisdn($phone),
            'type'              => 'text',
            'text'              => ['body' => $text],
        ];

        return $this->postWhatsApp($body);
    }

    /**
     * Send a generic WhatsApp text message.
     */
    public function sendWhatsApp(string $phone, string $message): bool
    {
        if (!$this->waToken || !$this->waPhoneId) return false;

        $body = [
            'messaging_product' => 'whatsapp',
            'to'                => $this->normaliseMsisdn($phone),
            'type'              => 'text',
            'text'              => ['body' => $message],
        ];

        return $this->postWhatsApp($body);
    }

    /**
     * Notify parent of class cancellation and refund.
     */
    public function sendCancellationMessage(int $bookingId, string $classTitle, float $refundAmount): bool
    {
        $db = \Config\Database::connect();
        $booking = $db->table('bookings')->where('id', $bookingId)->get()->getRow();
        if (!$booking) return false;

        $msg = "📢 *Class Cancellation — Class Next Door*\n\n"
             . "We regret to inform you that *'{$classTitle}'* has been cancelled.\n";
        
        if ($refundAmount > 0) {
            $msg .= "💰 A pro-rata refund of *₹{$refundAmount}* has been initiated and will reach your account within 5-7 working days.\n";
        }

        $msg .= "\nWe apologize for the inconvenience. 🎒";

        return $this->sendWhatsApp($booking->parent_phone, $msg);
    }

    // ── Internal helpers ─────────────────────────────────────────────────


    private string $lastError = '';

    public function getLastError(): string
    {
        return $this->lastError;
    }

    /**
     * Store WhatsApp API calls in a dedicated log file for monitoring.
     */
    private function logWhatsAppApi(string $url, string $request, int $code, string $response, string $curlErr = ''): void
    {
        $logDir = WRITEPATH . 'logs/';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }

        $logFile = $logDir . 'whatsapp_api.log';
        $timestamp = date('Y-m-d H:i:s');
        
        $entry = "[$timestamp] WhatsApp API Call\n";
        $entry .= "URL: $url\n";
        $entry .= "REQUEST: $request\n";
        $entry .= "HTTP_CODE: $code\n";
        if ($curlErr) $entry .= "CURL_ERROR: $curlErr\n";
        $entry .= "RESPONSE: $response\n";
        $entry .= str_repeat('-', 60) . "\n\n";
        
        @file_put_contents($logFile, $entry, FILE_APPEND);
    }

    private function postWhatsApp(array $payload): bool
    {
        $url = "https://graph.facebook.com/v25.0/{$this->waPhoneId}/messages";
        $json = json_encode($payload);

        // Use a direct cURL call to ensure SSL settings and error visibility
        if (function_exists('curl_init')) {
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => $json,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT        => 20,
                CURLOPT_CONNECTTIMEOUT => 10,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_HTTPHEADER     => [
                    'Authorization: Bearer ' . $this->waToken,
                    'Content-Type: application/json',
                ],
            ]);
            $body  = curl_exec($ch);
            $code  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlErr = curl_error($ch);
            curl_close($ch);
        } else {
            // Fallback to stream context
            $context = stream_context_create([
                'http' => [
                    'method'  => 'POST',
                    'header'  => "Authorization: Bearer {$this->waToken}\r\nContent-Type: application/json\r\nContent-Length: " . strlen($json),
                    'content' => $json,
                    'timeout' => 20,
                    'ignore_errors' => true,
                ],
                'ssl' => ['verify_peer' => false, 'verify_peer_name' => false],
            ]);
            $body    = @file_get_contents($url, false, $context);
            $curlErr = '';
            if ($body === false) {
                $curlErr = 'Failed to connect to WhatsApp API via stream.';
            }
            $code    = 200;
            if (isset($http_response_header) && preg_match('{HTTP/\S+\s+(\d+)}', $http_response_header[0], $m)) {
                $code = (int)$m[1];
            }
        }

        if ($curlErr) {
            $this->lastError = $curlErr;
            log_message('error', '[NotificationService] WA request failed: ' . $curlErr);
            $this->logWhatsAppApi($url, $json, 0, 'No response', $curlErr);
            return false;
        }

        // Log the complete transaction for monitoring
        $this->logWhatsAppApi($url, $json, $code, $body);

        if ($code < 200 || $code >= 300) {
            $this->lastError = "WhatsApp API error (HTTP $code)";
            $err = json_decode($body, true);
            if (isset($err['error']['message'])) {
                $this->lastError = $err['error']['message'];
            } else {
                $this->lastError .= " | Response: " . substr($body, 0, 100);
            }
            log_message('error', '[NotificationService] WA API Error: ' . $this->lastError);
            return false;
        }

        // Extract message ID to track status later via Webhook
        $respData = json_decode($body, true);
        $wamid    = $respData['messages'][0]['id'] ?? null;
        
        if ($wamid) {
            try {
                $logModel = new \App\Models\WhatsappLogModel();
                $logModel->insert([
                    'wa_message_id' => $wamid,
                    'recipient_id'  => $payload['to'] ?? null,
                    'status'        => 'accepted',
                    'raw_payload'   => $body
                ]);
            } catch (\Exception $e) {
                log_message('error', '[NotificationService] Failed to insert log to DB: ' . $e->getMessage());
                // We do NOT return false here because the message was already sent successfully to Meta
            }
        }

        log_message('info', '[NotificationService] WA message accepted. HTTP ' . $code . ' | wamid: ' . ($wamid ?? 'unknown'));
        return true;
    }

    /** Normalise phone to E.164 (add +91 if missing country code) */
    private function normaliseMsisdn(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone);
        if (strlen($digits) === 10) {
            return '91' . $digits; // India default
        }
        return ltrim($digits, '+');
    }
}
