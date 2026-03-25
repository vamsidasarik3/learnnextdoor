<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * UserModel
 * Table: users
 */
class UserModel extends BaseModel
{
    protected $table      = 'users';
    protected $primaryKey = 'id';
    protected $returnType = 'object';

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $allowedFields = [
        'name', 'username', 'email', 'password', 'phone', 'address',
        'last_login', 'role', 'reset_token', 'status', 'img_type',
        'phone_verified', 'email_verified', 'email_verified_at',
        'bank_name', 'bank_account_no', 'bank_ifsc', 'upi_id', 'status_remarks',
        'razorpay_account_id', 'provider', 'provider_id', 'is_verified',
        'provider_verification_status', 'provider_verification_message', 'provider_submitted_at', 'provider_verified_at',
    ];

    // ── Custom helpers ──────────────────────────────────────────

    /** Find a user by email address. */
    public function findByEmail(string $email): ?object
    {
        return $this->where('email', $email)->first();
    }

    /** Find a user by phone number. */
    public function findByPhone(string $phone): ?object
    {
        return $this->where('phone', $phone)->first();
    }

    /** Find a user by reset token. */
    public function findByResetToken(string $token): ?object
    {
        return $this->where('reset_token', $token)->first();
    }

    /** All users with a specific role (e.g. 2 = provider). */
    public function getByRole(int $role): array
    {
        return $this->where('role', $role)->findAll();
    }
}