<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRemarksToTables extends Migration
{
    public function up()
    {
        $this->forge->addColumn('listings', [
            'admin_remarks' => ['type' => 'TEXT', 'null' => true, 'after' => 'review_status']
        ]);
        $this->forge->addColumn('users', [
            'status_remarks' => ['type' => 'TEXT', 'null' => true, 'after' => 'status']
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('listings', 'admin_remarks');
        $this->forge->dropColumn('users', 'status_remarks');
    }
}
