<?php
require 'vendor/autoload.php';
$config = new \Config\Database();
$db = \CodeIgniter\Database\Config::connect($config->default);
$rows = $db->table('listings')->select('id, title, category_id')->get()->getResult();
echo json_encode($rows);
