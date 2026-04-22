<?php

namespace App\Models;

use CodeIgniter\Model;

class SupportMessageModel extends Model
{
    protected $table      = 'support_messages';
    protected $primaryKey = 'id';
    protected $returnType = 'object';

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = ''; // No updated_at for messages

    protected $allowedFields = [
        'concern_id', 'sender_id', 'role', 'message'
    ];

    public function getByConcern($concernId)
    {
        return $this->where('concern_id', $concernId)
                    ->orderBy('created_at', 'ASC')
                    ->findAll();
    }
}
