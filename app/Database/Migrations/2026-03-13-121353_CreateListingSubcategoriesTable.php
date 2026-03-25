<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateListingSubcategoriesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'listing_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'subcategory_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['listing_id', 'subcategory_id']);
        $this->forge->createTable('listing_subcategories');

        // Migrate existing data from listings table to listing_subcategories
        $db = \Config\Database::connect();
        $listings = $db->table('listings')->select('id, subcategory_id')->where('subcategory_id IS NOT NULL')->where('subcategory_id >', 0)->get()->getResult();
        
        if (!empty($listings)) {
            $batchData = [];
            foreach ($listings as $listing) {
                $batchData[] = [
                    'listing_id'     => $listing->id,
                    'subcategory_id' => $listing->subcategory_id,
                    'created_at'     => date('Y-m-d H:i:s'),
                ];
            }
            $db->table('listing_subcategories')->insertBatch($batchData);
        }
    }

    public function down()
    {
        $this->forge->dropTable('listing_subcategories');
    }
}
