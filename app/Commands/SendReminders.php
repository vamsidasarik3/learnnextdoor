<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\PushSubscriptionModel;
use App\Services\WebPushService;
use App\Services\NotificationService;

/**
 * SendReminders
 * ─────────────────────────────────────────────────────────────────────
 * Cron-driven command: scans upcoming bookings in the next 60–75 minutes
 * and dispatches both Web Push and WhatsApp reminders.
 *
 * Usage (PHP CLI):
 *   php spark reminders:send
 *
 * Recommended cron entry (runs every 15 minutes):
 *   * /15 * * * * /opt/lampp/bin/php /path/to/public_html/spark reminders:send >> /tmp/cnd_reminders.log 2>&1
 *
 * Windows Task Scheduler equivalent:
 *   Program : C:\xampp\php\php.exe
 *   Arguments: G:\xampp\htdocs\custom\public_html\spark reminders:send
 *   Trigger  : Every 15 minutes
 *
 * Idempotency: bookings are marked reminder_sent=1 after processing,
 * so repeated runs in the same window will not re-send.
 */
class SendReminders extends BaseCommand
{
    protected $group       = 'Reminders';
    protected $name        = 'reminders:send';
    protected $description = 'Send push + WhatsApp reminders for bookings starting in ~60 minutes.';

    private WebPushService    $push;
    private NotificationService $wa;
    private \CodeIgniter\Database\BaseConnection $db;

    /** Look 60–75 minutes ahead (15-minute cron window) */
    private const WINDOW_MIN_AHEAD = 60;
    private const WINDOW_SIZE      = 15;

    public function run(array $params): void
    {
        $this->push = new WebPushService();
        $this->wa   = new NotificationService();
        $this->db   = \Config\Database::connect();

        $from = date('Y-m-d H:i:s', strtotime('+' . self::WINDOW_MIN_AHEAD . ' minutes'));
        $to   = date('Y-m-d H:i:s', strtotime('+' . (self::WINDOW_MIN_AHEAD + self::WINDOW_SIZE) . ' minutes'));

        CLI::write('[' . date('Y-m-d H:i:s') . '] Scanning bookings between ' . $from . ' and ' . $to, 'cyan');

        // ── 1. Fetch bookings that need a reminder ─────────────────
        $bookings = $this->fetchDueBookings($from, $to);

        if (empty($bookings)) {
            CLI::write('No reminders to send.', 'green');
            return;
        }

        CLI::write('Found ' . count($bookings) . ' booking(s) — processing…', 'yellow');

        $sentPush = 0;
        $sentWa   = 0;
        $failed   = 0;

        foreach ($bookings as $row) {
            $bookingId = (int)$row['id'];

            // ── 2. Web Push ────────────────────────────────────────
            $pushRows = $this->fetchPushSubs($row['parent_phone'] ?? '');
            foreach ($pushRows as $sub) {
                $ok = $this->push->send($sub, $this->buildPushPayload($row));
                if ($ok) {
                    $sentPush++;
                    CLI::write("  ✅ Push → booking #{$bookingId} endpoint …" . substr($sub['endpoint'], -30), 'green');
                } else {
                    // 410 Gone — subscription expired, clean it up
                    if (!empty($sub['id'])) {
                        $this->db->table('push_subscriptions')->where('id', $sub['id'])->delete();
                        CLI::write("  🗑 Deleted expired subscription #{$sub['id']}", 'yellow');
                    }
                    $failed++;
                }
            }

            // ── 3. WhatsApp ────────────────────────────────────────
            $phone = $row['parent_phone'] ?? '';
            if ($phone) {
                $waOk = $this->wa->sendBookingReminder($phone, $row);
                if ($waOk) {
                    $sentWa++;
                    CLI::write("  ✅ WhatsApp → booking #{$bookingId} phone " . substr($phone, 0, 4) . '***', 'green');
                } else {
                    CLI::write("  ⚠  WhatsApp failed for booking #{$bookingId}", 'yellow');
                }
            }

            // ── 4. Mark reminder sent ──────────────────────────────
            $this->db->table('bookings')
                ->where('id', $bookingId)
                ->update(['reminder_sent' => 1, 'updated_at' => date('Y-m-d H:i:s')]);
        }

        CLI::newLine();
        CLI::write("Reminders done: {$sentPush} push, {$sentWa} WhatsApp, {$failed} failed.", 'green');
    }

    // ── Fetch ─────────────────────────────────────────────────────────

    /**
     * Get confirmed bookings whose class starts in the look-ahead window
     * and haven't had a reminder sent yet.
     */
    private function fetchDueBookings(string $from, string $to): array
    {
        return $this->db->table('bookings b')
            ->select('b.id, b.student_name, b.class_date, b.class_time,
                      b.payment_amount, b.booking_type, b.parent_phone,
                      l.title AS listing_title, l.address AS listing_address')
            ->join('listings l', 'l.id = b.listing_id', 'left')
            ->where('b.booking_status', 'confirmed')
            ->whereIn('b.payment_status', ['paid', 'pending'])
            ->where('b.reminder_sent', 0)
            ->where("CONCAT(b.class_date, ' ', b.class_time) >=", $from)
            ->where("CONCAT(b.class_date, ' ', b.class_time) <=", $to)
            ->orderBy('b.class_date', 'ASC')
            ->orderBy('b.class_time', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Fetch push subscriptions for a given phone number.
     */
    private function fetchPushSubs(string $phone): array
    {
        if (!$phone) return [];
        return $this->db->table('push_subscriptions')
            ->where('phone', $phone)
            ->get()
            ->getResultArray();
    }

    // ── Payload builders ──────────────────────────────────────────────

    private function buildPushPayload(array $booking): array
    {
        $date    = !empty($booking['class_date'])
                     ? date('D d M', strtotime($booking['class_date']))
                     : 'Today';
        $time    = !empty($booking['class_time'])
                     ? date('g:i A', strtotime($booking['class_time']))
                     : '';
        $student = $booking['student_name'] ?? 'your child';
        $title   = $booking['listing_title'] ?? 'class';

        return [
            'title'  => "⏰ 1-hour reminder!",
            'body'   => "{$student}'s {$title} starts at {$time} on {$date}.",
            'icon'   => '/assets/frontend/img/icon-192.png',
            'badge'  => '/assets/frontend/img/icon-72.png',
            'url'    => '/activity',
            'tag'    => 'cnd-reminder-' . $booking['id'],
        ];
    }
}
