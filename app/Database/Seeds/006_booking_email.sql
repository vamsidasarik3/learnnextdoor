-- Add email verification columns to bookings table
-- Used for certificate downloads (Subtask 1.8)

ALTER TABLE `bookings` 
ADD COLUMN `parent_email` VARCHAR(255) DEFAULT NULL AFTER `parent_phone`,
ADD COLUMN `email_verified` TINYINT(1) DEFAULT 0 AFTER `parent_email`;

CREATE INDEX `idx_bk_email` ON `bookings` (`parent_email`);
