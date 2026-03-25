<?php

namespace App\Models;

use CodeIgniter\Model;

class InstructorModel extends Model
{
    protected $table      = 'instructors';
    protected $primaryKey = 'id';
    protected $returnType = 'object';

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $allowedFields = [
        'provider_id', 'name', 'experience', 'social_links', 'kyc_doc', 'kyc_status'
    ];

    public function getByProvider(int $providerId)
    {
        return $this->where('provider_id', $providerId)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }
}
