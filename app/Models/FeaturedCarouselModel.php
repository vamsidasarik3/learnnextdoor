<?php

namespace App\Models;

use CodeIgniter\Model;

class FeaturedCarouselModel extends Model
{
    protected $table      = 'featured_carousels';
    protected $primaryKey = 'id';
    protected $returnType = 'object';

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $allowedFields = [
        'state', 'listing_id', 'position'
    ];

    /**
     * Get featured listings with distance filtering.
     * Used by API /v1/listings/carousel
     */
    public function getCarouselData(string $state, ?float $lat = null, ?float $lng = null, float $radius = 25, int $limit = 5)
    {
        $builder = $this->db->table('featured_carousels fc')
            ->select('l.*, l.id AS listing_id, fc.position as featured_position, c.name AS category_name,
                      (SELECT AVG(rating) FROM reviews WHERE listing_id = l.id) AS avg_rating,
                      (SELECT COUNT(*) FROM reviews WHERE listing_id = l.id) AS review_count,
                      u.is_verified AS provider_verified')
            ->join('listings l',    'l.id = fc.listing_id')
            ->join('categories c',  'c.id = l.category_id', 'left')
            ->join('users u',       'u.id = l.provider_id', 'left')
            ->where('l.status',        'active')
            ->where('l.review_status', 'approved')
            ->where('l.payment',       'success');

        if ($state && $state !== 'ALL') {
            $builder->where('fc.state', $state);
        }

        if ($lat && $lng) {
            $builder->select("(6371 * ACOS(COS(RADIANS($lat)) * COS(RADIANS(l.latitude)) * COS(RADIANS(l.longitude) - RADIANS($lng)) + SIN(RADIANS($lat)) * SIN(RADIANS(l.latitude)))) AS distance_km");
            $builder->having('distance_km <=', $radius);
        }

        return $builder->orderBy('fc.position', 'ASC')
                       ->limit($limit)
                       ->get()
                       ->getResultArray();
    }
}
