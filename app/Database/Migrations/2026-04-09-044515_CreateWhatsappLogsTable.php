<?php
 
namespace App\Database\Migrations;
 
use CodeIgniter\Database\Migration;
 
class CreateWhatsappLogsTable extends Migration
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
            'wa_message_id' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'unique'     => true,
            ],
            'recipient_id' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'null'       => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['sent', 'delivered', 'read', 'failed', 'deleted', 'accepted'],
                'default'    => 'sent',
            ],
            'error_message' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'raw_payload' => [
                'type' => 'LONGTEXT',
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
        $this->forge->createTable('whatsapp_logs');
    }
 
    public function down()
    {
        $this->forge->dropTable('whatsapp_logs');
    }
}
