-- Migration 007: Review & Feedback Enhancements
-- 1. Support guest reviews (phone-based)
-- 2. Add reminder tracking for reviews

ALTER TABLE `reviews`
  MODIFY COLUMN `user_id` INT UNSIGNED DEFAULT NULL,
  ADD COLUMN `parent_phone` VARCHAR(20) DEFAULT NULL AFTER `user_id`,
  DROP INDEX `uq_reviews_user_listing`,
  ADD UNIQUE KEY `uq_reviews_identity_listing` (`listing_id`, `user_id`, `parent_phone`);

ALTER TABLE `bookings`
  ADD COLUMN `review_reminders` TINYINT UNSIGNED NOT NULL DEFAULT 0 
      COMMENT 'Count of how many times the user was prompted for a review'
      AFTER `reminder_sent`;

-- Feedbacks index
CREATE INDEX IF NOT EXISTS `idx_fb_created` ON `feedbacks` (`created_at`);
