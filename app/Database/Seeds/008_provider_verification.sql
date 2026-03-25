-- =============================================================
-- Migration 008: Provider Verification & Payout Columns
-- =============================================================

USE `custom_new`;

-- ── users: add verification and payout columns ────────────────
ALTER TABLE `users`
  ADD COLUMN IF NOT EXISTS `phone_verified`   TINYINT(1)   NOT NULL DEFAULT 0 AFTER `phone`,
  ADD COLUMN IF NOT EXISTS `email_verified`   TINYINT(1)   NOT NULL DEFAULT 0 AFTER `email`,
  ADD COLUMN IF NOT EXISTS `bank_name`        VARCHAR(150) DEFAULT NULL AFTER `status`,
  ADD COLUMN IF NOT EXISTS `bank_account_no`  VARCHAR(50)  DEFAULT NULL AFTER `bank_name`,
  ADD COLUMN IF NOT EXISTS `bank_ifsc`        VARCHAR(20)  DEFAULT NULL AFTER `bank_account_no`,
  ADD COLUMN IF NOT EXISTS `upi_id`           VARCHAR(100) DEFAULT NULL AFTER `bank_ifsc`;

-- ── Ensure user_documents ENUM is comprehensive ───────────────
ALTER TABLE `user_documents`
  MODIFY COLUMN `document_type` ENUM('aadhaar','pan','passport','gst','portfolio','other') NOT NULL;

SELECT 'Verification schema applied' AS status;
