<?php

namespace App\Services;

use App\Models\FeaturedCarouselModel;
use App\Models\ListingModel;
use Config\Database;

class CarouselService
{
    /**
     * Get final 5 listings for a state's carousel.
     * Logic: Use explicitly featured ones first, then fill gaps with top rated active listings from the state.
     */
    public function getCarouselForState(?string $state): array
    {
        $carouselModel = new FeaturedCarouselModel();
        $db = Database::connect();

        $results = [];
        $finalIds = [];

        // 1. Get explicitly featured if state is provided
        if (!empty($state)) {
            $featured = $carouselModel->getCarouselData($state);
            $finalIds = array_column($featured, 'listing_id');
            // Tag admin-picked slides with source='admin'
            foreach ($featured as &$f) {
                if (is_object($f)) {
                    $f->source = 'admin';
                } else {
                    $f['source'] = 'admin';
                }
            }
            unset($f);
            $results = $featured;
        }

        // 2. If less than 5, fill gaps
        if (count($results) < 5) {
            $limit = 5 - count($results);
            
            // Subquery or Join for average ratings
            $builder = $db->table('listings l')
                ->select('l.id as listing_id, l.title, l.price, l.address, l.state, l.type, c.name as category_name, 
                          (SELECT GROUP_CONCAT(sc.name SEPARATOR ", ") FROM listing_subcategories lsc JOIN subcategories sc ON sc.id = lsc.subcategory_id WHERE lsc.listing_id = l.id) AS subcategory_names,
                          u.is_verified as provider_verified, COALESCE(AVG(r.rating), 0) as avg_rating, COUNT(r.id) as review_count')
                ->join('categories c', 'c.id = l.category_id', 'left')
                ->join('users u', 'u.id = l.provider_id', 'left')
                ->join('reviews r', 'r.listing_id = l.id', 'left')
                ->where('l.status',  'active')
                ->where('l.review_status', 'approved');
            
            /* 
            if (!empty($state)) {
                $builder->where('l.state', $state);
            }
            */
 
            if (!empty($finalIds)) {
                $builder->whereNotIn('l.id', $finalIds);
            }
 
            $alternatives = $builder->groupBy('l.id')
                ->orderBy('avg_rating', 'DESC')
                ->orderBy('l.total_students', 'DESC')
                ->orderBy('l.created_at', 'DESC')
                ->limit($limit)
                ->get()->getResult();
 
            foreach ($alternatives as $alt) {
                $results[] = (object)[
                    'listing_id'    => $alt->listing_id,
                    'title'         => $alt->title,
                    'price'         => $alt->price,
                    'address'       => $alt->address,
                    'state'         => $alt->state,
                    'type'          => $alt->type,
                    'category_name' => $alt->category_name,
                    'subcategory_names' => $alt->subcategory_names,
                    'provider_verified' => $alt->provider_verified,
                    'avg_rating'    => $alt->avg_rating,
                    'review_count'  => $alt->review_count,
                    'is_auto'       => true,
                    'source'        => 'algo'
                ];
            }
        }

        return array_slice($results, 0, 5);
    }
}
