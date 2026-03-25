<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * CategoryModel
 * Table: categories
 */
class CategoryModel extends BaseModel
{
    protected $table      = 'categories';
    protected $primaryKey = 'id';
    protected $returnType = 'object';

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $allowedFields = ['name', 'description', 'status'];

    // ── Custom helpers ──────────────────────────────────────────

    /**
     * Return all categories as a simple id → name array for dropdowns.
     */
    public function getDropdown(): array
    {
        $rows = $this->orderBy('name', 'ASC')->findAll();
        $out  = [];
        foreach ($rows as $row) {
            $out[$row->id] = $row->name;
        }
        return $out;
    }
}
