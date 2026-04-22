<?php

namespace App\Models;

use App\Models\BaseModel;

class ConcernModel extends BaseModel
{
    protected $table      = 'provider_concerns';
    protected $primaryKey = 'id';
    protected $returnType = 'object';

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $allowedFields = [
        'provider_id', 'issue', 'status', 'resolution_note', 'resolved_at'
    ];

    /**
     * Get All Concerns for Admin
     */
    public function getAllWithProvider()
    {
        return $this->db->table('provider_concerns c')
            ->select('c.*, u.name as provider_name, u.email as provider_email, u.phone as provider_phone')
            ->join('users u', 'u.id = c.provider_id', 'left')
            ->orderBy('c.status', 'ASC') // open first
            ->orderBy('c.created_at', 'DESC')
            ->get()->getResultObject();
    }

    /**
     * Cleanup old resolved concerns (7 days)
     */
    public function cleanupOldResolved()
    {
        $limitDate = date('Y-m-d H:i:s', strtotime('-7 days'));
        return $this->where('status', 'resolved')
                    ->where('resolved_at <', $limitDate)
                    ->delete();
    }
}
