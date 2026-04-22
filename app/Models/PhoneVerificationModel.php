<?php
namespace App\Models;

class PhoneVerificationModel extends BaseModel
{
    protected $table = 'phone_verifications';
    protected $primaryKey = 'id';
    protected $returnType = 'object';

    protected $allowedFields = [
        'user_id', 'phone', 'token', 'expires_at', 'created_at'
    ];

    public function deleteByUser(int $userId): void
    {
        $this->where('user_id', $userId)->delete();
    }
}
