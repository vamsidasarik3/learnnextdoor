<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddEnrollmentEndDateToBookings extends Migration
{
    public function up()
    {
        $fields = [
            'enrollment_end_date' => [
                'type' => 'DATE',
                'null' => true,
                'after' => 'batch_start_date'
            ]
        ];
        $this->forge->addColumn('bookings', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('bookings', 'enrollment_end_date');
    }
}
