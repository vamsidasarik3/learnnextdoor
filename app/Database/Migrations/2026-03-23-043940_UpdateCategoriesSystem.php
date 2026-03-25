<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateCategoriesSystem extends Migration
{
    public function up()
    {
        // 1. Add icon column to categories if not exists
        if (!$this->db->fieldExists('icon', 'categories')) {
            $this->forge->addColumn('categories', [
                'icon' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '255',
                    'null'       => true,
                    'after'      => 'name'
                ],
            ]);
        }

        // 2. Add icon/image column to subcategories if not exists
        if (!$this->db->fieldExists('icon', 'subcategories')) {
            $this->forge->addColumn('subcategories', [
                'icon' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '255',
                    'null'       => true,
                    'after'      => 'name'
                ],
            ]);
        }

        // --- DATA MIGRATION BEGIN ---
        
        // Backup current listing category/subcategory assignments by name
        // This is crucial because IDs will change after truncation.
        $listingsToMigrate = $this->db->table('listings l')
            ->select('l.id, c.name as cat_name')
            ->join('categories c', 'c.id = l.category_id', 'left')
            ->get()
            ->getResultArray();

        foreach ($listingsToMigrate as &$l) {
            $subs = $this->db->table('listing_subcategories lsc')
                ->select('sc.name')
                ->join('subcategories sc', 'sc.id = lsc.subcategory_id')
                ->where('lsc.listing_id', $l['id'])
                ->get()
                ->getResultArray();
            $l['sub_names'] = array_column($subs, 'name');
        }

        // 3. To avoid orphaned listings, truncate assignments first
        $this->db->table('listing_subcategories')->truncate();
        
        // Truncate existing categories and subcategories safely
        $this->db->query('SET FOREIGN_KEY_CHECKS = 0;');
        $this->db->table('subcategories')->truncate();
        $this->db->table('categories')->truncate();
        $this->db->query('SET FOREIGN_KEY_CHECKS = 1;');
        
        // 4. Seed new structure (Calling CategorySeeder logic)
        $seeder = \Config\Database::seeder();
        $seeder->call('CategorySeeder');

        // 5. Fetch new categories and subcategories for mapping
        $newCategories = $this->db->table('categories')->get()->getResultArray();
        $newSubs = $this->db->table('subcategories')->get()->getResultArray();

        // 6. Perform mapping
        foreach ($listingsToMigrate as $oldData) {
            $listingId = $oldData['id'];
            $oldCatName = $oldData['cat_name'];
            $oldSubNames = $oldData['sub_names'];

            $newCatId = null;
            $newSubIds = [];

            // Find matching category
            if ($oldCatName) {
                foreach ($newCategories as $nc) {
                    if (stripos($nc['name'], $oldCatName) !== false || stripos($oldCatName, $nc['name']) !== false) {
                        $newCatId = $nc['id'];
                        break;
                    }
                }
            }

            // Fallback category if none found: "Academic Support" or first available
            if (!$newCatId && !empty($newCategories)) {
                $newCatId = $newCategories[0]['id']; // Default to first (usually Sports or Academic)
            }

            // Find matching subcategories
            if ($newCatId) {
                $catSubs = array_filter($newSubs, function($s) use ($newCatId) { return $s['category_id'] == $newCatId; });
                
                foreach ($oldSubNames as $osn) {
                    reset($catSubs);
                    foreach ($catSubs as $ns) {
                        if (strcasecmp($ns['name'], $osn) === 0) {
                            $newSubIds[] = $ns['id'];
                            break;
                        }
                    }
                }

                // Edge Handling: If no subcategory match found, assign to "Others" under this category
                if (empty($newSubIds)) {
                    foreach ($catSubs as $ns) {
                        if (strcasecmp($ns['name'], 'Others') === 0) {
                            $newSubIds[] = $ns['id'];
                            break;
                        }
                    }
                }
            }

            // Update Listing
            $primarySubId = !empty($newSubIds) ? $newSubIds[0] : null;
            $this->db->table('listings')->where('id', $listingId)->update([
                'category_id' => $newCatId,
                'subcategory_id' => $primarySubId
            ]);

            // Update Pivot
            if (!empty($newSubIds)) {
                $pivotData = [];
                foreach ($newSubIds as $sid) {
                    $pivotData[] = [
                        'listing_id' => $listingId,
                        'subcategory_id' => $sid,
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                }
                $this->db->table('listing_subcategories')->insertBatch($pivotData);
            }
        }
        // --- DATA MIGRATION END ---
    }

    public function down()
    {
        // Not dropping columns in down to keep UI data, but could drop if preferred.
    }
}
