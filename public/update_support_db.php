<?php
$db = \Config\Database::connect();

// 1. Update provider_concerns to include category and priority if missing
$sqlFields = [
    "ALTER TABLE `provider_concerns` ADD COLUMN `category` varchar(100) DEFAULT NULL AFTER `provider_id`;",
    "ALTER TABLE `provider_concerns` ADD COLUMN `priority` varchar(20) DEFAULT 'Medium' AFTER `status`;",
    "ALTER TABLE `provider_concerns` ADD COLUMN `listing_id` int(11) DEFAULT NULL AFTER `category`;"
];

foreach($sqlFields as $sql) {
    try { $db->query($sql); } catch(\Exception $e) {} // Ignore if exists
}

// 2. Create support_messages table
$sqlMsg = "CREATE TABLE IF NOT EXISTS `support_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `concern_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `role` varchar(20) NOT NULL COMMENT 'provider or admin',
  `message` text NOT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `concern_id` (`concern_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

try {
    $db->query($sqlMsg);
    echo "Support structures updated successfully!";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
