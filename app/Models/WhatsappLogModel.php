<?php
 
namespace App\Models;
 
use CodeIgniter\Model;
 
class WhatsappLogModel extends Model
{
    protected $table      = 'whatsapp_logs';
    protected $primaryKey = 'id';
    protected $returnType = 'object';
 
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
 
    protected $allowedFields = [
        'wa_message_id', 'recipient_id', 'status', 'error_message', 'raw_payload'
    ];
}
