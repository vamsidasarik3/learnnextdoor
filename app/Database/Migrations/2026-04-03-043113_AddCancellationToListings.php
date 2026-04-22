<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCancellationToListings extends Migration
{
    public function up()
    {
        $fields = [
            'holidays' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'batches'
            ],
            'is_cancelled' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'after' => 'holidays'
            ],
            'cancellation_date' => [
                'type' => 'DATE',
                'null' => true,
                'after' => 'is_cancelled'
            ]
        ];
        $this->forge->addColumn('listings', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('listings', ['holidays', 'is_cancelled', 'cancellation_date']);
    }
}
