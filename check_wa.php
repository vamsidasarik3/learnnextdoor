<?php
require 'public/index.php';
$db = \Config\Database::connect();
$msgId = 'wamid.HBgMOTE5OTg5Mjg0ODA0FQIAERgSQkU2ODRBRUFFMzQ5MDc5NjQzAA==';
$row = $db->table('whatsapp_logs')->where('message_id', $msgId)->get()->getRow();
header('Content-Type: application/json');
echo json_encode($row, JSON_PRETTY_PRINT);
