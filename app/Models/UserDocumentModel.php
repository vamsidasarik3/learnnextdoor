<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * UserDocumentModel
 * Table: user_documents
 * Stores KYC and verification documents for providers.
 */
class UserDocumentModel extends BaseModel
{
    protected $table      = 'user_documents';
    protected $primaryKey = 'id';
    protected $returnType = 'object';

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $allowedFields = [
        'user_id', 'document_type', 'file_path', 'verified_status',
    ];

    // ── Custom helpers ──────────────────────────────────────────

    /**
     * All documents uploaded by a user.
     */
    public function getByUser(int $userId): array
    {
        return $this->where('user_id', $userId)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    /**
     * All documents pending admin verification.
     */
    public function getPendingVerification(): array
    {
        return $this->db->table('user_documents ud')
            ->select('ud.*, u.name AS user_name, u.email AS user_email')
            ->join('users u', 'u.id = ud.user_id', 'left')
            ->where('ud.verified_status', 'pending')
            ->orderBy('ud.created_at', 'ASC')
            ->get()
            ->getResultObject();
    }

    /**
     * Check if a specific document type is verified for a user.
     */
    public function isVerified(int $userId, string $type): bool
    {
        return $this->where('user_id', $userId)
                    ->where('document_type', $type)
                    ->where('verified_status', 'verified')
                    ->countAllResults() > 0;
    }

    /**
     * Update verification status for a document.
     */
    public function setVerificationStatus(int $docId, string $status): void
    {
        // $status must be 'pending'|'verified'|'rejected'
        $this->updateById($docId, ['verified_status' => $status]);
    }
}
