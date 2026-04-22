<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePhoneVerificationsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
            ],
            'phone' => [
                'type'           => 'VARCHAR',
                'constraint'     => '20',
            ],
            'token' => [
                'type'           => 'VARCHAR',
                'constraint'     => '10',
            ],
            'expires_at' => [
                'type'           => 'DATETIME',
                'null'           => true,
            ],
            'created_at' => [
                'type'           => 'DATETIME',
                'null'           => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('phone_verifications');
    }

    public function down()
    {
        $this->forge->dropTable('phone_verifications');
    }
}
