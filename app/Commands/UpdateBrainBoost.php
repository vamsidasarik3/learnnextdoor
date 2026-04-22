<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class UpdateBrainBoost extends BaseCommand
{
    /**
     * The Command's Group
     *
     * @var string
     */
    protected $group = 'CodeIgniter';

    /**
     * The Command's Name
     *
     * @var string
     */
    protected $name = 'categories:update-brain-boost';
    protected $description = 'Renames Brain Development category to Brain Boost and adds Stock Market subcategory';

    public function run(array $params)
    {
        $db = \Config\Database::connect();
        
        // 1. Rename Category
        $cat = $db->table('categories')->where('name', 'Brain Development & Competitive Skills')->get()->getRow();
        
        if ($cat) {
            $db->table('categories')->where('id', $cat->id)->update(['name' => 'Brain Boost']);
            CLI::write("Renamed: 'Brain Development & Competitive Skills' -> 'Brain Boost'", 'green');
            $catId = $cat->id;
        } else {
            // Check if already renamed
            $cat = $db->table('categories')->where('name', 'Brain Boost')->get()->getRow();
            if ($cat) {
                CLI::write("Category 'Brain Boost' already exists.", 'yellow');
                $catId = $cat->id;
            } else {
                CLI::error("Category 'Brain Development & Competitive Skills' not found.");
                return;
            }
        }

        // 2. Add Subcategory
        $sub = $db->table('subcategories')
                  ->where('category_id', $catId)
                  ->where('name', 'Stock Market')
                  ->get()->getRow();
        
        if (!$sub) {
            $db->table('subcategories')->insert([
                'category_id' => $catId,
                'name'        => 'Stock Market',
                'status'      => 1,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s')
            ]);
            CLI::write("Added Subcategory: 'Stock Market'", 'green');
        } else {
            CLI::write("Subcategory 'Stock Market' already exists.", 'yellow');
        }

        CLI::write("Operation completed successfully!", 'green');
    }
}
