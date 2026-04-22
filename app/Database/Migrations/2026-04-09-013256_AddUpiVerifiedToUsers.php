<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUpiVerifiedToUsers extends Migration
{
    public function up()
    {
        $this->forge->addColumn('users', [
            'upi_verified' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'after'      => 'upi_id'
            ],
            'upi_verified_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'upi_verified'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('users', ['upi_verified', 'upi_verified_at']);
    }
}
