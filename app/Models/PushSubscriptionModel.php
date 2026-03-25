<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * PushSubscriptionModel
 * Table: push_subscriptions
 *
 * Stores Web Push API subscription objects (endpoint + keys).
 * Indexed by phone (for guest users) so we can look up all
 * devices for a phone number when sending a reminder.
 */
class PushSubscriptionModel extends Model
{
    protected $table         = 'push_subscriptions';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $allowedFields = [
        'phone', 'user_id', 'endpoint', 'p256dh', 'auth', 'user_agent',
    ];

    // ── Write ─────────────────────────────────────────────────────────

    /**
     * Upsert a push subscription.
     * If the same phone+endpoint already exists, update keys.
     */
    public function upsert(string $phone, array $sub, ?int $userId = null, ?string $ua = null): void
    {
        $existing = $this->where('phone', $phone)
                         ->where('endpoint', $sub['endpoint'])
                         ->first();

        $data = [
            'phone'      => $phone,
            'endpoint'   => $sub['endpoint'],
            'p256dh'     => $sub['keys']['p256dh']  ?? '',
            'auth'       => $sub['keys']['auth']     ?? '',
            'user_agent' => $ua,
            'user_id'    => $userId,
        ];

        if ($existing) {
            $this->update($existing['id'], $data);
        } else {
            $this->insert($data);
        }
    }

    /**
     * Delete all subscriptions for a phone (unsubscribe).
     */
    public function deleteByPhone(string $phone): void
    {
        $this->where('phone', $phone)->delete();
    }

    // ── Read ──────────────────────────────────────────────────────────

    /**
     * All subscriptions for a given phone  (one phone, many devices).
     */
    public function getByPhone(string $phone): array
    {
        return $this->where('phone', $phone)->findAll();
    }

    /**
     * Fetch all bookings within the next [60–75] minute window
     * alongside their subscription rows.
     *
     * Returns an array of rows with booking + subscription columns.
     * Used by the cron command to batch-send reminders.
     */
    public function getUpcomingWithSubscriptions(int $minutesAhead = 60, int $windowMinutes = 15): array
    {
        $from = date('Y-m-d H:i:s', strtotime("+{$minutesAhead} minutes"));
        $to   = date('Y-m-d H:i:s', strtotime('+' . ($minutesAhead + $windowMinutes) . ' minutes'));

        // We join bookings → listing → push_subscriptions via the phone stored at booking time.
        // For guest bookings, parent_phone is stored in the bookings table.
        // The query builds a combined datetime for comparison using class_date + class_time.

        $sql = <<<SQL
            SELECT
                b.id           AS booking_id,
                b.student_name,
                b.class_date,
                b.class_time,
                b.payment_amount,
                b.booking_type,
                l.title        AS listing_title,
                l.address      AS listing_address,
                ps.id          AS sub_id,
                ps.phone,
                ps.endpoint,
                ps.p256dh,
                ps.auth
            FROM bookings b
            INNER JOIN listings l              ON l.id  = b.listing_id
            INNER JOIN push_subscriptions ps   ON ps.phone = b.parent_phone
            WHERE b.booking_status  = 'confirmed'
              AND b.payment_status IN ('paid', 'pending')
              AND CONCAT(b.class_date, ' ', b.class_time) BETWEEN ? AND ?
              AND b.reminder_sent   = 0
            ORDER BY b.class_date ASC, b.class_time ASC
        SQL;

        return $this->db->query($sql, [$from, $to])->getResultArray();
    }
}
