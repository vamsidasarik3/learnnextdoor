<?php

namespace App\Models;

use App\Models\BaseModel;

class ActivityLogModel extends BaseModel
{
    protected $table      = 'activity_logs';
    protected $primaryKey = 'id';
    protected $returnType = 'object';

    protected $allowedFields = ['title', 'user', 'ip_address', 'old_value', 'new_value', 'is_read', 'created_at'];

    public function add($message, $user_id = 0, $ip_address = false, $old = null, $new = null)
    {
        return $this->insert([
            'title'      => $message,
            'user'       => ($user_id == 0) ? logged('id') : $user_id,
            'ip_address' => !empty($ip_address) ? $ip_address : ip_address(),
            'old_value'  => $old ? (is_string($old) ? $old : json_encode($old)) : null,
            'new_value'  => $new ? (is_string($new) ? $new : json_encode($new)) : null,
            'created_at' => date('Y-m-d H:i:s'),
            'is_read'    => 0
        ]);
    }

    public function getAllWithUser($limit = 500)
    {
        return $this->db->table('activity_logs al')
            ->select('al.*, u.name as user_name, u.email as user_email')
            ->join('users u', 'u.id = al.user', 'left')
            ->orderBy('al.id', 'DESC')
            ->limit($limit)
            ->get()->getResultObject();
    }

    public function getUnreadCount($user_id)
    {
        return $this->where('user', (int)$user_id)
                    ->where('is_read', 0)
                    ->countAllResults();
    }
}