<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RenameListingsFieldsForStandardization extends Migration
{
    public function up()
    {
        $fields = [
            'experience_details' => [
                'name' => 'experience',
                'type' => 'TEXT',
                'null' => true,
            ],
            'portfolio_url' => [
                'name' => 'social_links',
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'max_students' => [
                'name' => 'batch_size',
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
        ];
        $this->forge->modifyColumn('listings', $fields);
    }

    public function down()
    {
        $fields = [
            'experience' => [
                'name' => 'experience_details',
                'type' => 'TEXT',
                'null' => true,
            ],
            'social_links' => [
                'name' => 'portfolio_url',
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'batch_size' => [
                'name' => 'max_students',
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
        ];
        $this->forge->modifyColumn('listings', $fields);
    }
}
