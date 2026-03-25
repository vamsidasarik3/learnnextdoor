<?php

namespace App\Services;

use Config\Email;

/**
 * EmailService
 * ─────────────────────────────────────────────────────────────────────
 * Responsibilities:
 *  1. Email OTP generation & session-based verification.
 *  2. Sending verification emails using CI4 Email library (SMTP/SendGrid).
 */
class EmailService
{
    private $email;
    private const OTP_TTL = 600; // 10 minutes

    public function __construct()
    {
        $this->email = \Config\Services::email();
    }

    /**
     * Generate a 6-digit OTP, store it in session, and send it via email.
     */
    public function sendOtp(string $recipientEmail): bool
    {
        $otp = (string) random_int(100000, 999999);

        // Store in session
        $session = session();
        $session->set('cnd_email_otp_' . md5($recipientEmail), [
            'otp'     => $otp,
            'expires' => time() + self::OTP_TTL,
            'email'   => $recipientEmail,
        ]);

        // Send email
        $this->email->setTo($recipientEmail);
        $this->email->setSubject('Verify your email - Class Next Door');
        $this->email->setMessage("
            <h2>Email Verification</h2>
            <p>Your verification code is: <strong>{$otp}</strong></p>
            <p>This code is valid for 10 minutes. Use it to verify your email and download your certificates.</p>
            <br>
            <p>Thanks,<br>Team Class Next Door</p>
        ");

        if ($this->email->send()) {
            return true;
        }

        // Log error if sending fails
        log_message('error', '[EmailService] Failed to send OTP to ' . $recipientEmail . ': ' . $this->email->printDebugger(['headers']));
        return false;
    }

    /**
     * Verify the OTP entered by the user.
     */
    public function verifyOtp(string $recipientEmail, string $inputOtp): bool
    {
        $session = session();
        $key     = 'cnd_email_otp_' . md5($recipientEmail);
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

    /**
     * Send a general HTML email notification.
     */
    public function sendHTML(string $to, string $subject, string $htmlMessage): bool
    {
        $this->email->setTo($to);
        $this->email->setSubject($subject);
        $this->email->setMessage($htmlMessage);

        if ($this->email->send()) {
            return true;
        }

        log_message('error', '[EmailService] Failed to send HTML email to ' . $to);
        return false;
    }
}
