<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddNewClassFields extends Migration
{
    public function up()
    {
        $this->forge->addColumn('listings', [
            'course_duration' => [
                'type'       => 'INT',
                'constraint' => 5,
                'null'       => true,
            ],
            'course_duration_type' => [
                'type'       => 'ENUM',
                'constraint' => ['weeks', 'months'],
                'null'       => true,
            ],
            'instructor_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'instructor_kyc_status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'approved', 'rejected'],
                'default'    => 'pending',
            ],
            'institute_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'manual_address' => [
                'type'       => 'TEXT',
                'null'       => true,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('listings', ['course_duration', 'course_duration_type', 'instructor_name', 'instructor_kyc_status', 'institute_name', 'manual_address']);
    }
}
