<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * ListingAvailabilityModel
 * Table: listing_availabilities
 */
class ListingAvailabilityModel extends BaseModel
{
    protected $table      = 'listing_availabilities';
    protected $primaryKey = 'id';
    protected $returnType = 'object';

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $allowedFields = [
        'listing_id', 'available_date', 'available_time', 'is_disabled',
    ];

    // ── Custom helpers ──────────────────────────────────────────

    /**
     * All availability slots for a listing (enabled only).
     */
    public function getAvailableSlots(int $listingId): array
    {
        return $this->where('listing_id', $listingId)
                    ->where('is_disabled', 0)
                    ->where('available_date >=', date('Y-m-d'))
                    ->orderBy('available_date', 'ASC')
                    ->orderBy('available_time', 'ASC')
                    ->findAll();
    }

    /**
     * Available slots for a specific date.
     */
    public function getAvailable(int $listingId, string $date): array
    {
        return $this->where('listing_id', $listingId)
                    ->where('available_date', $date)
                    ->where('is_disabled', 0)
                    ->orderBy('available_time', 'ASC')
                    ->findAll();
    }

    /**
     * All slots for provider to manage (including disabled).
     */
    public function getByListing(int $listingId): array
    {
        return $this->where('listing_id', $listingId)
                    ->orderBy('available_date', 'ASC')
                    ->orderBy('available_time', 'ASC')
                    ->findAll();
    }

    /**
     * Toggle disabled state for a specific slot.
     */
    public function toggleDisabled(int $id): void
    {
        $slot = $this->getById($id);
        if ($slot) {
            $this->updateById($id, ['is_disabled' => (int) !$slot->is_disabled]);
        }
    }
}
