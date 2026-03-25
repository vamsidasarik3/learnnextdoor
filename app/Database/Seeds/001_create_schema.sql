-- =============================================================
-- Class Next Door — Full Schema
-- Database : custom_new
-- Generated: 2026-02-24
-- Safe to run multiple times (CREATE TABLE IF NOT EXISTS)
-- Run order respects FK dependencies
-- =============================================================

CREATE DATABASE IF NOT EXISTS `custom_new`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `custom_new`;

SET FOREIGN_KEY_CHECKS = 0;

-- -------------------------------------------------------------
-- 1. roles
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `roles` (
  `id`    INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_roles_title` (`title`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Default roles seed
INSERT IGNORE INTO `roles` (`id`, `title`) VALUES
  (1, 'admin'),
  (2, 'provider'),
  (3, 'parent');

-- -------------------------------------------------------------
-- 2. permissions
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `permissions` (
  `id`    INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(200) NOT NULL,
  `code`  VARCHAR(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_permissions_code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- 3. users
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
  `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`         VARCHAR(150) NOT NULL,
  `username`     VARCHAR(100) DEFAULT NULL,
  `email`        VARCHAR(200) NOT NULL,
  `password`     VARCHAR(255) NOT NULL,
  `phone`        VARCHAR(20)  DEFAULT NULL,
  `address`      TEXT         DEFAULT NULL,
  `last_login`   DATETIME     DEFAULT NULL,
  `role`         INT UNSIGNED NOT NULL DEFAULT 3,   -- 3 = parent
  `reset_token`  VARCHAR(255) DEFAULT NULL,
  `status`       ENUM('active','inactive','banned') NOT NULL DEFAULT 'active',
  `img_type`     VARCHAR(10)  DEFAULT 'png',
  `created_at`   DATETIME     DEFAULT CURRENT_TIMESTAMP,
  `updated_at`   DATETIME     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_users_email`    (`email`),
  UNIQUE KEY `uq_users_username` (`username`),
  KEY `idx_users_role`   (`role`),
  KEY `idx_users_phone`  (`phone`),
  CONSTRAINT `fk_users_role` FOREIGN KEY (`role`) REFERENCES `roles` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- 4. role_permissions
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `role_permissions` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `role`       INT UNSIGNED NOT NULL,
  `permission` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_role_perm` (`role`, `permission`),
  CONSTRAINT `fk_rp_role`       FOREIGN KEY (`role`)       REFERENCES `roles`       (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_rp_permission` FOREIGN KEY (`permission`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- 5. email_templates
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `email_templates` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(200) NOT NULL,
  `code`       VARCHAR(100) NOT NULL,
  `data`       LONGTEXT     DEFAULT NULL,   -- HTML body with {shortcode} placeholders
  `created_at` DATETIME     DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_email_templates_code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- 6. activity_logs  (note: BRD spells it 'acivity_logs' — using correct spelling)
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `activity_logs` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title`      VARCHAR(500) NOT NULL,
  `user`       INT UNSIGNED DEFAULT NULL,
  `ip_address` VARCHAR(45)  DEFAULT NULL,
  `created_at` DATETIME     DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_al_user` (`user`),
  CONSTRAINT `fk_al_user` FOREIGN KEY (`user`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- 7. settings
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `settings` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `key`        VARCHAR(200) NOT NULL,
  `value`      TEXT         DEFAULT NULL,
  `created_at` DATETIME     DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_settings_key` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Default settings seed
INSERT IGNORE INTO `settings` (`key`, `value`) VALUES
  ('company_name',    'Class Next Door'),
  ('default_lang',    'en'),
  ('currency',        'INR'),
  ('currency_symbol', '₹'),
  ('razorpay_key',    ''),
  ('razorpay_secret', ''),
  ('whatsapp_token',  '');

-- -------------------------------------------------------------
-- 8. categories
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `categories` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(150) NOT NULL,
  `created_at` DATETIME     DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_categories_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Default categories seed
INSERT IGNORE INTO `categories` (`name`) VALUES
  ('Music'), ('Dance'), ('Sports'), ('Art & Craft'),
  ('Coding'), ('Academics'), ('Yoga & Fitness'),
  ('Language'), ('Theatre'), ('Chess'), ('Other');

-- -------------------------------------------------------------
-- 9. listings
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `listings` (
  `id`                   INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `provider_id`          INT UNSIGNED NOT NULL,
  `category_id`          INT UNSIGNED NOT NULL,
  `title`                VARCHAR(300) NOT NULL,
  `description`          TEXT         DEFAULT NULL,
  `type`                 ENUM('regular','workshop','course') NOT NULL DEFAULT 'regular',
  `address`              TEXT         DEFAULT NULL,
  `latitude`             DECIMAL(10,8) DEFAULT NULL,
  `longitude`            DECIMAL(11,8) DEFAULT NULL,
  -- price = total course/workshop fee; per-session breakdown stored below
  `price`                DECIMAL(10,2) DEFAULT 0.00,
  `price_breakdown`      TEXT         DEFAULT NULL, -- JSON: {"sessions":10,"per_session":500}
  `free_trial`           TINYINT(1)   NOT NULL DEFAULT 0,
  `registration_end_date` DATE         DEFAULT NULL,
  `early_bird_date`      DATE         DEFAULT NULL,
  `early_bird_slots`     INT UNSIGNED DEFAULT 0,
  `early_bird_price`     DECIMAL(10,2) DEFAULT NULL,
  `experience`         TEXT         DEFAULT NULL,
  `linkedin_url`         VARCHAR(500) DEFAULT NULL,
  `social_links`        VARCHAR(500) DEFAULT NULL,
  `status`               ENUM('active','inactive','draft') NOT NULL DEFAULT 'draft',
  `review_status`        ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `total_students`       INT UNSIGNED NOT NULL DEFAULT 0,
  `created_at`           DATETIME     DEFAULT CURRENT_TIMESTAMP,
  `updated_at`           DATETIME     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_listings_provider`      (`provider_id`),
  KEY `idx_listings_category`      (`category_id`),
  KEY `idx_listings_type`          (`type`),
  KEY `idx_listings_status`        (`status`),
  KEY `idx_listings_review_status` (`review_status`),
  KEY `idx_listings_location`      (`latitude`, `longitude`),
  CONSTRAINT `fk_listings_provider` FOREIGN KEY (`provider_id`) REFERENCES `users`       (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_listings_category` FOREIGN KEY (`category_id`) REFERENCES `categories`  (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- 10. listing_images
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `listing_images` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `listing_id`  INT UNSIGNED NOT NULL,
  `image_path`  VARCHAR(500) NOT NULL,
  `position`    TINYINT UNSIGNED NOT NULL DEFAULT 0,  -- 0 = cover image
  `created_at`  DATETIME     DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_li_listing` (`listing_id`),
  CONSTRAINT `fk_li_listing` FOREIGN KEY (`listing_id`) REFERENCES `listings` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- 11. listing_availabilities
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `listing_availabilities` (
  `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `listing_id`     INT UNSIGNED NOT NULL,
  `available_date` DATE         NOT NULL,
  `available_time` TIME         NOT NULL,
  `is_disabled`    TINYINT(1)   NOT NULL DEFAULT 0,
  `created_at`     DATETIME     DEFAULT CURRENT_TIMESTAMP,
  `updated_at`     DATETIME     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_la_listing` (`listing_id`),
  KEY `idx_la_date`    (`available_date`),
  CONSTRAINT `fk_la_listing` FOREIGN KEY (`listing_id`) REFERENCES `listings` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- 12. bookings
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `bookings` (
  `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `listing_id`     INT UNSIGNED NOT NULL,
  `parent_id`      INT UNSIGNED NOT NULL,
  `student_name`   VARCHAR(150) NOT NULL,
  `student_age`    TINYINT UNSIGNED DEFAULT NULL,
  `booking_type`   ENUM('trial','regular') NOT NULL DEFAULT 'regular',
  `class_date`     DATE         DEFAULT NULL,
  `class_time`     TIME         DEFAULT NULL,
  `payment_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `payment_id`     VARCHAR(200) DEFAULT NULL,    -- Razorpay payment ID
  `payment_status` ENUM('pending','paid','failed','refunded') NOT NULL DEFAULT 'pending',
  `booking_status` ENUM('confirmed','cancelled','completed') NOT NULL DEFAULT 'confirmed',
  `completed_at`   DATETIME     DEFAULT NULL,
  `created_at`     DATETIME     DEFAULT CURRENT_TIMESTAMP,
  `updated_at`     DATETIME     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_bookings_listing`        (`listing_id`),
  KEY `idx_bookings_parent`         (`parent_id`),
  KEY `idx_bookings_payment_status` (`payment_status`),
  KEY `idx_bookings_booking_status` (`booking_status`),
  KEY `idx_bookings_class_date`     (`class_date`),
  CONSTRAINT `fk_bookings_listing` FOREIGN KEY (`listing_id`) REFERENCES `listings` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `fk_bookings_parent`  FOREIGN KEY (`parent_id`)  REFERENCES `users`    (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- 13. reviews
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `reviews` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `listing_id`  INT UNSIGNED NOT NULL,
  `user_id`     INT UNSIGNED NOT NULL,
  `rating`      TINYINT UNSIGNED NOT NULL DEFAULT 5, -- 1–5
  `review_text` TEXT         DEFAULT NULL,
  `created_at`  DATETIME     DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  DATETIME     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_reviews_user_listing` (`user_id`, `listing_id`), -- one review per user per listing
  KEY `idx_reviews_listing` (`listing_id`),
  CONSTRAINT `fk_reviews_listing` FOREIGN KEY (`listing_id`) REFERENCES `listings` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_reviews_user`    FOREIGN KEY (`user_id`)    REFERENCES `users`    (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- 14. feedbacks  (Contact-Us submissions)
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `feedbacks` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`    INT UNSIGNED DEFAULT NULL,   -- NULL = guest/non-logged-in
  `message`    TEXT         NOT NULL,
  `status`     ENUM('new','read','replied') NOT NULL DEFAULT 'new',
  `created_at` DATETIME     DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_feedbacks_user`   (`user_id`),
  KEY `idx_feedbacks_status` (`status`),
  CONSTRAINT `fk_feedbacks_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- 15. user_documents  (KYC / verification docs)
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `user_documents` (
  `id`              INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`         INT UNSIGNED NOT NULL,
  `document_type`   ENUM('aadhaar','pan','gst','portfolio','other') NOT NULL,
  `file_path`       VARCHAR(500) NOT NULL,
  `verified_status` ENUM('pending','verified','rejected') NOT NULL DEFAULT 'pending',
  `created_at`      DATETIME     DEFAULT CURRENT_TIMESTAMP,
  `updated_at`      DATETIME     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_ud_user`            (`user_id`),
  KEY `idx_ud_verified_status` (`verified_status`),
  CONSTRAINT `fk_ud_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- 16. transactions
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `transactions` (
  `id`               INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `booking_id`       INT UNSIGNED NOT NULL,
  `user_id`          INT UNSIGNED NOT NULL,
  `amount`           DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `transaction_type` ENUM('payment','refund','payout') NOT NULL DEFAULT 'payment',
  `razorpay_id`      VARCHAR(200) DEFAULT NULL,
  `status`           ENUM('pending','success','failed') NOT NULL DEFAULT 'pending',
  `settled_at`       DATETIME     DEFAULT NULL,
  `created_at`       DATETIME     DEFAULT CURRENT_TIMESTAMP,
  `updated_at`       DATETIME     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_txn_booking`  (`booking_id`),
  KEY `idx_txn_user`     (`user_id`),
  KEY `idx_txn_status`   (`status`),
  KEY `idx_txn_type`     (`transaction_type`),
  CONSTRAINT `fk_txn_booking` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `fk_txn_user`    FOREIGN KEY (`user_id`)    REFERENCES `users`    (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- 17. featured_carousels  (Admin-curated homepage carousel, per state)
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `featured_carousels` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `state`      VARCHAR(100) NOT NULL,        -- Indian state name e.g. 'Karnataka'
  `listing_id` INT UNSIGNED NOT NULL,
  `position`   TINYINT UNSIGNED NOT NULL DEFAULT 0,  -- display order, lower = first
  `created_at` DATETIME     DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_fc_state_listing`  (`state`, `listing_id`),  -- one listing per state carousel
  KEY `idx_fc_state`    (`state`),
  KEY `idx_fc_listing`  (`listing_id`),
  KEY `idx_fc_position` (`state`, `position`),
  CONSTRAINT `fk_fc_listing` FOREIGN KEY (`listing_id`) REFERENCES `listings` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- =============================================================
-- END OF SCHEMA
-- =============================================================

