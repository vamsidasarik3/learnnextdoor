<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * ReviewModel
 * Table: reviews
 */
class ReviewModel extends BaseModel
{
    protected $table      = 'reviews';
    protected $primaryKey = 'id';
    protected $returnType = 'object';

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $allowedFields = ['listing_id', 'user_id', 'parent_phone', 'rating', 'review_text'];

    // ── Custom helpers ──────────────────────────────────────────

    /**
     * All reviews for a listing with reviewer name.
     */
    public function getByListing(int $listingId): array
    {
        return $this->db->table('reviews r')
            ->select('r.*, u.name AS user_name')
            ->join('users u', 'u.id = r.user_id', 'left')
            ->where('r.listing_id', $listingId)
            ->orderBy('r.created_at', 'DESC')
            ->get()
            ->getResultObject();
    }

    /**
     * Average rating (1–5) for a listing.
     * Returns 0.0 if no reviews exist.
     */
    public function getAverageRating(int $listingId): float
    {
        $result = $this->db->table('reviews')
            ->selectAvg('rating', 'avg_rating')
            ->where('listing_id', $listingId)
            ->get()
            ->getRowObject();

        return round((float) ($result->avg_rating ?? 0), 1);
    }

    /**
     * Rating distribution for a listing (counts per star).
     * Returns: ['5' => n, '4' => n, ... '1' => n]
     */
    public function getRatingDistribution(int $listingId): array
    {
        $rows = $this->db->table('reviews')
            ->select('rating, COUNT(*) AS count')
            ->where('listing_id', $listingId)
            ->groupBy('rating')
            ->get()
            ->getResultObject();

        $dist = ['5' => 0, '4' => 0, '3' => 0, '2' => 0, '1' => 0];
        foreach ($rows as $row) {
            $dist[(string)$row->rating] = (int) $row->count;
        }
        return $dist;
    }

    /**
     * Check if a user / phone has already reviewed a listing.
     */
    public function hasReviewed(int $listingId, ?int $userId = null, ?string $phone = null): bool
    {
        $builder = $this->where('listing_id', $listingId);
        
        if ($userId) {
            $builder->where('user_id', $userId);
        } elseif ($phone) {
            $builder->where('parent_phone', $phone);
        } else {
            return false;
        }

        return $builder->countAllResults() > 0;
    }
}
