<?php

namespace App\Models;

class EmailVerificationModel extends BaseModel
{
    protected $table = 'email_verifications';
    protected $primaryKey = 'id';
    protected $returnType = 'object';

    protected $allowedFields = [
        'user_id', 'email', 'token', 'expires_at', 'created_at'
    ];

    public function findByToken(string $token): ?object
    {
        return $this->where('token', $token)->first();
    }

    public function deleteByUser(int $userId): void
    {
        $this->where('user_id', $userId)->delete();
    }
}
