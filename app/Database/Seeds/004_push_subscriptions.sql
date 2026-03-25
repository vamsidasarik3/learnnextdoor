-- =============================================================
-- Class Next Door — Web Push Subscriptions Table
-- Run after 001_create_schema.sql
-- =============================================================

USE `custom_new`;

-- -------------------------------------------------------------
-- push_subscriptions
-- Stores Web Push API subscription objects.
-- One phone can have multiple devices (e.g. mobile + desktop).
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `push_subscriptions` (
  `id`         INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  `phone`      VARCHAR(20)     NOT NULL,          -- parent phone (used as identity for guests)
  `user_id`    INT UNSIGNED    DEFAULT NULL,       -- if logged-in parent (future)
  `endpoint`   TEXT            NOT NULL,           -- push service URL
  `p256dh`     VARCHAR(200)    NOT NULL,           -- client public key (base64url)
  `auth`       VARCHAR(50)     NOT NULL,           -- auth secret (base64url)
  `user_agent` VARCHAR(300)    DEFAULT NULL,       -- browser hint
  `created_at` DATETIME        DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME        DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  -- One endpoint per phone (upsert-safe)
  UNIQUE KEY `uq_ps_phone_endpoint` (`phone`(20), `endpoint`(200)),
  KEY `idx_ps_phone`   (`phone`),
  KEY `idx_ps_user`    (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
