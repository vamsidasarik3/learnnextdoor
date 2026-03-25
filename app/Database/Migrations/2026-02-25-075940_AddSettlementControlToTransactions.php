<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSettlementControlToTransactions extends Migration
{
    public function up()
    {
        $this->forge->addColumn('transactions', [
            'is_blocked' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'after'      => 'status'
            ],
            'block_reason' => [
                'type'       => 'TEXT',
                'null'       => true,
                'after'      => 'is_blocked'
            ],
            'transfer_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'razorpay_id'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('transactions', ['is_blocked', 'block_reason', 'transfer_id']);
    }
}
