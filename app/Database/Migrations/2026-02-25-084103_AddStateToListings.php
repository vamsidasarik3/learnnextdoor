<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStateToListings extends Migration
{
    public function up()
    {
        $this->forge->addColumn('listings', [
            'state' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'address'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('listings', 'state');
    }
}
