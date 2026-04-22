<?php
require 'app/Config/Paths.php';
$paths = new Config\Paths();
require $paths->systemDirectory . '/bootstrap.php';

$db = \Config\Database::connect();
// Let's check status types and fix any that are string 'active' to integer 1
$existing = $db->query("SELECT id, status FROM users WHERE status = 'active' OR status = '0'")->getResult();
foreach ($existing as $row) {
    if ($row->status === 'active' || $row->status === '0') {
        $db->query("UPDATE users SET status = 1 WHERE id = ?", [$row->id]);
        echo "Updated User ID {$row->id} from status '{$row->status}' to 1.\n";
    }
}
// Specifically check user ID 36
$u36 = $db->query("SELECT status FROM users WHERE id = 36")->getRow();
if ($u36) {
    echo "User 36 status is now: {$u36->status}\n";
    if ((int)$u36->status === 0) {
        $db->query("UPDATE users SET status = 1 WHERE id = 36");
        echo "Fixed User 36 status to 1.\n";
    }
}
echo "Database status update complete.\n";
