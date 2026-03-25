<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddScheduleFieldsToListings extends Migration
{
    public function up()
    {
        // start_date : Regular & Course — when the class starts
        // end_date   : Course only   — when the course ends
        // class_time : Regular & Course — daily scheduled time (e.g. "09:00:00")
        $fields = [
            'start_date' => [
                'type'       => 'DATE',
                'null'       => true,
                'default'    => null,
                'after'      => 'portfolio_url',
            ],
            'end_date' => [
                'type'       => 'DATE',
                'null'       => true,
                'default'    => null,
                'after'      => 'start_date',
            ],
            'class_time' => [
                'type'       => 'TIME',
                'null'       => true,
                'default'    => null,
                'after'      => 'end_date',
            ],
        ];

        $this->forge->addColumn('listings', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('listings', ['start_date', 'end_date', 'class_time']);
    }
}
