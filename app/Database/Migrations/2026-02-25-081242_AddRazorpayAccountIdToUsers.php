<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRazorpayAccountIdToUsers extends Migration
{
    public function up()
    {
        $this->forge->addColumn('users', [
            'razorpay_account_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'img_type'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('users', 'razorpay_account_id');
    }
}
