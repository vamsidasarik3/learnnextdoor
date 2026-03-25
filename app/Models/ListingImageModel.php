<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * ListingImageModel
 * Table: listing_images
 */
class ListingImageModel extends BaseModel
{
    protected $table      = 'listing_images';
    protected $primaryKey = 'id';
    protected $returnType = 'object';

    protected $useTimestamps = false; // no updated_at column

    protected $allowedFields = ['listing_id', 'image_path', 'position'];

    // ── Custom helpers ──────────────────────────────────────────

    /**
     * All images for a listing, cover image (position=0) first.
     */
    public function getByListing(int $listingId): array
    {
        return $this->where('listing_id', $listingId)
                    ->orderBy('position', 'ASC')
                    ->findAll();
    }

    /**
     * Cover image (position = 0) for a listing.
     */
    public function getCoverImage(int $listingId): ?object
    {
        return $this->where('listing_id', $listingId)
                    ->where('position', 0)
                    ->first();
    }

    /**
     * Delete all images for a listing (used when listing is deleted).
     */
    public function deleteByListing(int $listingId): void
    {
        $this->where('listing_id', $listingId)->delete();
    }
}
