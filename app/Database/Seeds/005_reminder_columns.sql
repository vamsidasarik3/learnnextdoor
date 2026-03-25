-- =============================================================
-- Migration 005: Reminder support columns
-- Run once after 001_create_schema.sql is applied.
-- Safe to run multiple times (IF NOT EXISTS / IF EXISTS guards).
-- =============================================================

USE `custom_new`;

-- ── bookings: add parent_phone and reminder_sent ──────────────────────
-- parent_phone: OTP-verified phone used for guest bookings (parent_id may be 0)
-- reminder_sent: flag set to 1 after the cron sends the 1-hour reminder

ALTER TABLE `bookings`
  ADD COLUMN IF NOT EXISTS `parent_phone`  VARCHAR(20)  DEFAULT NULL
      COMMENT 'OTP-verified phone, used to look up push subscriptions'
      AFTER `parent_id`,
  ADD COLUMN IF NOT EXISTS `reminder_sent` TINYINT(1)   NOT NULL DEFAULT 0
      COMMENT '1 after the 1-hour advance reminder has been dispatched'
      AFTER `booking_status`,
  ADD INDEX IF NOT EXISTS `idx_bk_reminder`
      (`booking_status`, `payment_status`, `reminder_sent`, `class_date`);

-- ── push_subscriptions: already in 004_push_subscriptions.sql ─────────
-- Included here as a fallback in case 004 was not run.
CREATE TABLE IF NOT EXISTS `push_subscriptions` (
  `id`         INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  `phone`      VARCHAR(20)     NOT NULL,
  `user_id`    INT UNSIGNED    DEFAULT NULL,
  `endpoint`   TEXT            NOT NULL,
  `p256dh`     VARCHAR(200)    NOT NULL,
  `auth`       VARCHAR(50)     NOT NULL,
  `user_agent` VARCHAR(300)    DEFAULT NULL,
  `created_at` DATETIME        DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME        DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_ps_phone_endpoint` (`phone`(20), `endpoint`(200)),
  KEY `idx_ps_phone` (`phone`),
  KEY `idx_ps_user`  (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
