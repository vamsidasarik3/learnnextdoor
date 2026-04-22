<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSettlementsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'auto_increment' => true,
            ],
            'provider_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'listing_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true, // Can be for whole provider or specific listing
            ],
            'period_start' => [
                'type' => 'DATE',
            ],
            'period_end' => [
                'type' => 'DATE',
            ],
            'payout_date' => [
                'type' => 'DATE',
            ],
            'gross_amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0.00,
            ],
            'commission_amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0.00,
            ],
            'refund_deductions' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0.00,
            ],
            'net_amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0.00,
            ],
            'upi_id' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['upcoming', 'calculated', 'pending', 'paid', 'failed', 'held'],
                'default'    => 'upcoming',
            ],
            'retry_count' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'student_breakdown' => [
                'type' => 'JSON', // Stores IDs and amounts for drill-down
                'null' => true,
            ],
            'batch_aggregation' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('provider_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('settlements');
    }

    public function down()
    {
        $this->forge->dropTable('settlements');
    }
}
