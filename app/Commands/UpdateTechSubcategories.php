<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class UpdateTechSubcategories extends BaseCommand
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
    protected $name = 'categories:update-tech';
    protected $description = 'Renames subcategories under Technology & Coding';

    public function run(array $params)
    {
        $db = \Config\Database::connect();
        
        $cat = $db->table('categories')->where('name', 'Technology & Coding')->get()->getRow();
        
        if (!$cat) {
            CLI::error("Category 'Technology & Coding' not found.");
            return;
        }

        $newSubcats = [
            'Coding', 
            'MS office',
            'AI Courses',
            'Digital Marketting',
            'UI/UX/VFX',
            'Others'
        ];

        // 1. Clear existing
        $db->table('subcategories')->where('category_id', $cat->id)->delete();
        
        // 2. Insert new
        foreach ($newSubcats as $name) {
            $db->table('subcategories')->insert([
                'category_id' => $cat->id,
                'name'        => $name,
                'status'      => 1,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s')
            ]);
            CLI::write("Added: $name", 'green');
        }

        CLI::write("Subcategories for 'Technology & Coding' updated successfully!", 'green');
    }
}
