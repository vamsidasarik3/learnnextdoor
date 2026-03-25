<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * FeedbackModel
 * Table: feedbacks
 * Stores Contact Us form submissions.
 */
class FeedbackModel extends BaseModel
{
    protected $table      = 'feedbacks';
    protected $primaryKey = 'id';
    protected $returnType = 'object';

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $allowedFields = ['user_id', 'message', 'status'];

    // ── Admin helpers ───────────────────────────────────────────

    /**
     * All new (unread) feedback submissions.
     */
    public function getPending(): array
    {
        return $this->db->table('feedbacks f')
            ->select('f.*, u.name AS user_name, u.email AS user_email')
            ->join('users u', 'u.id = f.user_id', 'left')
            ->where('f.status', 'new')
            ->orderBy('f.created_at', 'DESC')
            ->get()
            ->getResultObject();
    }

    /**
     * All feedback with optional status filter.
     */
    public function getAll(?string $status = null, int $limit = 50, int $offset = 0): array
    {
        $builder = $this->db->table('feedbacks f')
            ->select('f.*, u.name AS user_name, u.email AS user_email')
            ->join('users u', 'u.id = f.user_id', 'left')
            ->orderBy('f.created_at', 'DESC');

        if ($status !== null) {
            $builder->where('f.status', $status);
        }

        return $builder->limit($limit, $offset)->get()->getResultObject();
    }

    /**
     * Mark feedback as read.
     */
    public function markRead(int $id): void
    {
        $this->updateById($id, ['status' => 'read']);
    }
}
