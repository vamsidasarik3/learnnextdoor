<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddClassEndTimeToListings extends Migration
{
    public function up()
    {
        $this->forge->addColumn('listings', [
            'class_end_time' => [
                'type' => 'TIME',
                'null' => true,
                'after' => 'class_time'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('listings', 'class_end_time');
    }
}
