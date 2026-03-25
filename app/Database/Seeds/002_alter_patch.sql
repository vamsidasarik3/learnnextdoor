-- =============================================================
-- Class Next Door — Schema Alignment PATCH
-- Run AFTER 001_create_schema.sql
-- Safe: Only adds missing ENUM values / columns to existing tables.
-- Does NOT drop columns or change existing column types.
-- =============================================================

USE `custom_new`;

-- ── listings.status: add 'draft' value ───────────────────────
ALTER TABLE `listings`
  MODIFY COLUMN `status`
    ENUM('active','inactive','suspended','draft')
    NOT NULL DEFAULT 'draft';

-- ── listings: add missing columns if not already present ─────
ALTER TABLE `listings`
  MODIFY COLUMN `longitude` DECIMAL(11,8) DEFAULT NULL;

-- ── bookings.payment_status: add 'refunded' value ────────────
ALTER TABLE `bookings`
  MODIFY COLUMN `payment_status`
    ENUM('pending','paid','failed','refunded')
    NOT NULL DEFAULT 'pending';

-- ── bookings.booking_type: keep existing + ensure 'regular','trial' present ──
-- (existing enum already has regular/trial/workshop/course — OK, no change needed)

-- NOTE: users.status left as INT (value 1=active, 0=inactive) to
-- preserve compatibility with existing AuthFilter / login logic.
-- The UserModel ENUM in our docs is aspirational; real column stays INT.

SELECT 'Schema patch applied successfully.' AS status;
