<?php

namespace App\Models;

use CodeIgniter\Model;

class TestimonialModel extends Model
{
    protected $table      = 'testimonials';
    protected $primaryKey = 'id';
    protected $returnType = 'object';

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $allowedFields = [
        'user_name', 'user_image', 'rating', 'feedback', 'page', 'status', 'position'
    ];

    /**
     * Get active testimonials for specific page
     */
    public function getForPage(string $page, int $limit = 10)
    {
        return $this->where('page', $page)
                    ->where('status', 'active')
                    ->orderBy('position', 'ASC')
                    ->limit($limit)
                    ->findAll();
    }
}
