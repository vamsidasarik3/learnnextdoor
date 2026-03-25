<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddInstructorKycDoc extends Migration
{
    public function up()
    {
        $this->forge->addColumn('listings', [
            'instructor_kyc_doc' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('listings', 'instructor_kyc_doc');
    }
}
