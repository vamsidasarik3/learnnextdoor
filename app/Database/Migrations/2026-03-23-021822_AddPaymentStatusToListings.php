<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPaymentStatusToListings extends Migration
{
    public function up()
    {
        $this->forge->addColumn('listings', [
            'payment' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'success', 'failed'],
                'default'    => 'pending',
                'after'      => 'review_status',
            ],
        ]);
        
        // Mark existing approved/active listings as success so they don't disappear after migration
        $this->db->query("UPDATE listings SET payment = 'success' WHERE status = 'active' AND review_status = 'approved'");
    }

    public function down()
    {
        $this->forge->dropColumn('listings', 'payment');
    }
}
