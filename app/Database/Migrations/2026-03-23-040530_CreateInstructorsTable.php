<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateInstructorsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'provider_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'experience' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'social_links' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'kyc_doc' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'kyc_status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'verified', 'rejected'],
                'default'    => 'pending',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('provider_id');
        $this->forge->createTable('instructors');

        // Add instructor_id to listings
        $this->forge->addColumn('listings', [
            'instructor_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
                'after'    => 'subcategory_id',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('instructors');
        $this->forge->dropColumn('listings', 'instructor_id');
    }
}
