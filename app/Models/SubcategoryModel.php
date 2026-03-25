<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * SubcategoryModel
 * Table: subcategories
 */
class SubcategoryModel extends BaseModel
{
    protected $table      = 'subcategories';
    protected $primaryKey = 'id';
    protected $returnType = 'object';

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $allowedFields = [
        'category_id', 'name', 'slug', 'description', 'status'
    ];

    /** Find subcategories by category ID */
    public function getByCategory(int $categoryId): array
    {
        return $this->where('category_id', $categoryId)
                    ->orderBy('name', 'ASC')
                    ->findAll();
    }
}
