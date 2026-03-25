-- =============================================================
-- Class Next Door — Demo Data Seed  (003_demo_data.sql)
-- Database : custom_new
-- Covers   : providers, listings (regular/workshop/course),
--            availability, reviews, featured_carousels,
--            commission setting, parent users, bookings
-- Location : Hyderabad, Telangana (lat≈17.38, lng≈78.48)
--            All listings within ~15km so distance filter works.
-- Run AFTER 001 + 002 scripts.
-- =============================================================

USE `custom_new`;
SET FOREIGN_KEY_CHECKS = 0;

-- ── Commission setting ───────────────────────────────────────
INSERT IGNORE INTO `settings` (`key`, `value`) VALUES
  ('commission_pct',   '15'),        -- 15% admin commission
  ('commission_mode',  'percent'),   -- 'percent' or 'flat'
  ('commission_flat',  '0'),
  ('gst_pct',          '18'),
  ('min_withdrawal',   '500');

-- ── Category IDs (from 001 schema seed) ─────────────────────
-- 1=Music, 2=Dance, 3=Sports, 4=Art & Craft,
-- 5=Coding, 6=Academics, 7=Yoga & Fitness,
-- 8=Language, 9=Theatre, 10=Chess, 11=Other

-- ── Provider users (role=2) ──────────────────────────────────
-- Password hash = bcrypt of "Demo@1234"
INSERT IGNORE INTO `users`
  (`id`,`name`,`email`,`password`,`phone`,`role`,`status`,`address`) VALUES
(10,'Meera Sharma','meera@classnextdoor.dev',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 '9876501001',2,1,'Banjara Hills, Hyderabad'),
(11,'Ravi Kumar','ravi@classnextdoor.dev',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 '9876501002',2,1,'Jubilee Hills, Hyderabad'),
(12,'Priya Nair','priya@classnextdoor.dev',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 '9876501003',2,1,'Kondapur, Hyderabad'),
(13,'Sanjay Reddy','sanjay@classnextdoor.dev',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 '9876501004',2,1,'Madhapur, Hyderabad'),
(14,'Anitha Rao','anitha@classnextdoor.dev',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 '9876501005',2,1,'Gachibowli, Hyderabad');

-- ── Parent / test users (role=3) ─────────────────────────────
INSERT IGNORE INTO `users`
  (`id`,`name`,`email`,`password`,`phone`,`role`,`status`) VALUES
(20,'Arun Parent','parent1@test.dev',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 '9000000001',3,1),
(21,'Sunita Parent','parent2@test.dev',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 '9000000002',3,1),
(22,'Deepak Parent','parent3@test.dev',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 '9000000003',3,1);

-- =============================================================
-- LISTINGS
-- type=regular  → monthly fee, continuous classes
-- type=workshop → one-day event, pay once
-- type=course   → fixed duration (1–3 months), pay per month
--
-- price_breakdown JSON:
--   regular  : {"billing":"monthly","fee_per_month":1500,"sessions_per_week":3}
--   workshop : {"billing":"once","total_fee":799,"duration_hours":4}
--   course   : {"billing":"monthly","months":2,"fee_per_month":2500,"total_fee":5000}
-- =============================================================

INSERT IGNORE INTO `listings`
(`id`,`provider_id`,`category_id`,`title`,`description`,`type`,
 `address`,`latitude`,`longitude`,
 `price`,`price_breakdown`,`free_trial`,
 `registration_end_date`,`early_bird_date`,`early_bird_slots`,`early_bird_price`,
 `experience_details`,`status`,`review_status`,`total_students`) VALUES

-- ══ MUSIC ══════════════════════════════════════════════════════
-- Regular (id=1)
(1,10,1,'Meera''s Carnatic Vocal Academy',
 'Learn Carnatic classical vocal from a trained professional with 12 years of teaching experience. Classes for all age groups from 6 years. Covers swaras, varnams, kritis, and raga alapana. Individual attention guaranteed.',
 'regular',
 'Plot 22, Road 12, Banjara Hills, Hyderabad',17.41200,78.44800,
 1500.00,'{"billing":"monthly","fee_per_month":1500,"sessions_per_week":3,"session_duration_mins":60}',
 1,'2026-12-31',NULL,0,NULL,
 '12 years teaching, Grade 8 Trinity College London, performed at Saptagiri Festival',
 'active','approved',42),

-- Workshop (id=2)
(2,10,1,'Bollywood Beats Singing Workshop',
 'One-day intensive workshop on film song techniques — breath control, sur-taal, mic handling, and stage confidence. Open to beginners. Certificate provided.',
 'workshop',
 'Shilpakala Vedika, Tank Bund Rd, Hyderabad',17.40900,78.47200,
 799.00,'{"billing":"once","total_fee":799,"duration_hours":5,"date":"2026-03-15"}',
 0,'2026-03-10',NULL,0,NULL,
 'Trained under Ustad Rahman Khan, 8 years experience',
 'active','approved',65),

-- Course (id=3)
(3,11,1,'Guitar for Kids — Summer Course',
 '2-month structured guitar course for children aged 8–15. From basic chords to simple songs. Includes instrument rental. Batch size limited to 8.',
 'course',
 '5-A Lotus Colony, Jubilee Hills, Hyderabad',17.43100,78.40600,
 2500.00,'{"billing":"monthly","months":2,"fee_per_month":2500,"total_fee":5000,"duration_weeks":8}',
 1,'2026-03-20','2026-03-01',4,2000.00,
 'Trinity Grade 5 guitar, performed at Hard Rock Cafe Hyderabad',
 'active','approved',18),

-- ══ DANCE ══════════════════════════════════════════════════════
-- Regular (id=4)
(4,11,2,'Ravi''s Bharatanatyam Studio',
 'Classical Bharatanatyam training for children and adults. Curriculum follows Kalakshetra tradition. Annual arangetram program available. All levels welcome.',
 'regular',
 'Lane 7, Jubilee Hills Check Post, Hyderabad',17.43500,78.40200,
 1800.00,'{"billing":"monthly","fee_per_month":1800,"sessions_per_week":3,"session_duration_mins":75}',
 1,'2026-12-31',NULL,0,NULL,
 '15 years, trained under Padmashri Yamini Krishnamurti school',
 'active','approved',78),

-- Workshop (id=5)
(5,12,2,'Zumba Fitness Party Workshop',
 'High-energy 3-hour Zumba workshop combining Latin dance and fitness. No experience needed. Great for stress relief and calorie burning.',
 'workshop',
 'Inorbit Mall Community Hall, HITECH City, Hyderabad',17.43700,78.38100,
 499.00,'{"billing":"once","total_fee":499,"duration_hours":3,"date":"2026-03-22"}',
 0,'2026-03-20',NULL,0,NULL,
 'Licensed Zumba instructor since 2018, 500+ class hours',
 'active','approved',110),

-- Course (id=6)
(6,12,2,'Contemporary Dance — 3-Month Course',
 'Intensive 3-month contemporary dance course. Learn floor work, partnering, improvisation, and composition. Ends with a showcase performance.',
 'course',
 'Kondapur Community Centre, Hyderabad',17.45800,78.36300,
 3000.00,'{"billing":"monthly","months":3,"fee_per_month":3000,"total_fee":9000,"duration_weeks":12}',
 0,'2026-03-25','2026-03-05',6,2500.00,
 '10 years professional dancer, performed at NSD, Delhi',
 'active','approved',28),

-- ══ SPORTS ═════════════════════════════════════════════════════
-- Regular (id=7)
(7,13,3,'Elite Cricket Academy — Weekly Batches',
 'Professional cricket coaching for ages 8–18. Focus on batting technique, bowling mechanics, fielding drills, and match simulation. Coaches are BCCI certified.',
 'regular',
 'Gymkhana Grounds, Secunderabad, Hyderabad',17.44500,78.50100,
 2000.00,'{"billing":"monthly","fee_per_month":2000,"sessions_per_week":4,"session_duration_mins":90}',
 1,'2026-12-31',NULL,0,NULL,
 'BCCI Level-2 certified, coached U-19 state team players',
 'active','approved',95),

-- Workshop (id=8)
(8,13,3,'Swimming Survival Skills Workshop',
 'One-day water safety and basic swimming workshop for non-swimmers. Conducted by a certified lifeguard. Suitable for ages 6 and above.',
 'workshop',
 'Gachibowli Olympic-size Pool, Hyderabad',17.44200,78.37700,
 600.00,'{"billing":"once","total_fee":600,"duration_hours":4,"date":"2026-04-05"}',
 0,'2026-04-03',NULL,0,NULL,
 'National lifesaving award, 9 years aquatic coaching',
 'active','approved',40),

-- Course (id=9)
(9,14,3,'Badminton Skills — 2-Month Summer Camp',
 '2-month badminton camp covering footwork, net play, smash, and defensive play. Includes racket and shuttle kit. Batch of 10 max.',
 'course',
 'Lal Bahadur Stadium Annexe, Hyderabad',17.38900,78.47600,
 2200.00,'{"billing":"monthly","months":2,"fee_per_month":2200,"total_fee":4400,"duration_weeks":8}',
 1,'2026-03-28','2026-03-08',5,1800.00,
 'State-level player, Dronacharya nominated coach',
 'active','approved',22),

-- ══ ART & CRAFT ════════════════════════════════════════════════
-- Regular (id=10)
(10,14,4,'Little Picassos — Kids Art Studio',
 'Weekly art classes for children aged 4–14. Covers pencil sketching, watercolours, clay modelling, and craft. Portfolio created. Friendly, creative environment.',
 'regular',
 'Shop 8, Rainbow Arcade, Madhapur, Hyderabad',17.44800,78.38500,
 1200.00,'{"billing":"monthly","fee_per_month":1200,"sessions_per_week":2,"session_duration_mins":60}',
 1,'2026-12-31',NULL,0,NULL,
 'BFA from College of Fine Arts Hyderabad, 8 years teaching',
 'active','approved',56),

-- Workshop (id=11)
(11,10,4,'Mandala Art & Dot Painting Workshop',
 'Half-day creative workshop on traditional mandala designs and dot painting. All materials provided. Take home your framed artwork.',
 'workshop',
 'Artzone Studio, Road No. 45, Jubilee Hills, Hyderabad',17.43300,78.40900,
 450.00,'{"billing":"once","total_fee":450,"duration_hours":3.5,"date":"2026-03-29"}',
 0,'2026-03-28',NULL,0,NULL,
 'Self-taught mandala artist with 50k Instagram followers',
 'active','approved',85),

-- Course (id=12)
(12,11,4,'Digital Illustration — 1-Month Crash Course',
 '1-month intensive digital illustration course using Procreate on iPad. Character design, backgrounds, and portfolio building. Laptop/iPad required.',
 'course',
 'Design Hub, HITECH City, Hyderabad',17.44600,78.37200,
 4500.00,'{"billing":"monthly","months":1,"fee_per_month":4500,"total_fee":4500,"duration_weeks":4}',
 0,'2026-04-01','2026-03-15',3,3800.00,
 'Lead illustrator at Tata Elxsi for 5 years',
 'active','approved',14),

-- ══ CODING ═════════════════════════════════════════════════════
-- Regular (id=13)
(13,12,5,'Python for Beginners — Weekend Batch',
 'Weekend Python classes for ages 10+. Covers variables, loops, functions, OOP, mini-projects. Progress tracked on shared dashboard. CBSE CS syllabus aligned.',
 'regular',
 'IndiHub, Gachibowli, Hyderabad',17.44100,78.36900,
 2500.00,'{"billing":"monthly","fee_per_month":2500,"sessions_per_week":2,"session_duration_mins":90}',
 1,'2026-12-31',NULL,0,NULL,
 'Senior software engineer, 7 years, Google Developer Expert',
 'active','approved',67),

-- Workshop (id=14)
(14,12,5,'Build Your First Android App — 1-Day Hackathon',
 'Hands-on 1-day workshop to build and deploy a real Android app using MIT App Inventor. No coding experience needed. Laptops provided.',
 'workshop',
 'T-Hub Phase 2, Raidurg, Hyderabad',17.44900,78.36100,
 999.00,'{"billing":"once","total_fee":999,"duration_hours":7,"date":"2026-04-12"}',
 0,'2026-04-10','2026-03-31',10,799.00,
 'Published 4 apps on Play Store, Google certified trainer',
 'active','approved',48),

-- Course (id=15)
(15,13,5,'Full-Stack Web Dev — 3-Month Bootcamp',
 '3-month bootcamp covering HTML, CSS, JavaScript, React, Node.js, and MongoDB. Live projects, code reviews, and job assistance. Weekend + weekday batches.',
 'course',
 'Cyberabad Tech Park, HITECH City, Hyderabad',17.44300,78.38000,
 5000.00,'{"billing":"monthly","months":3,"fee_per_month":5000,"total_fee":15000,"duration_weeks":12}',
 0,'2026-04-05','2026-03-20',5,4200.00,
 'CTO of EdTech startup, 10 years full-stack experience',
 'active','approved',31),

-- ══ ACADEMICS / TUITIONS ═══════════════════════════════════════
-- Regular (id=16)
(16,13,6,'IIT-JEE Foundation Classes — Grades 9 & 10',
 'Foundation coaching for IIT-JEE aspirants in grades 9 and 10. Covers Physics, Chemistry, and Maths. Small batches of 8 students. Mock tests every month.',
 'regular',
 'Aakash Lane, Himayath Nagar, Hyderabad',17.39700,78.47900,
 3500.00,'{"billing":"monthly","fee_per_month":3500,"sessions_per_week":5,"session_duration_mins":90}',
 1,'2026-12-31',NULL,0,NULL,
 'IIT Bombay alumnus, AIR 87 in JEE Advanced 2014',
 'active','approved',120),

-- Workshop (id=17)
(17,14,6,'NEET Crash Course — 1-Day Speed Revision',
 'Intensive 1-day NEET revision workshop covering Biology, Physics, and Chemistry high-weightage topics. MCQ practice sheets included.',
 'workshop',
 'Allen Institute, Somajiguda, Hyderabad',17.42200,78.45900,
 1200.00,'{"billing":"once","total_fee":1200,"duration_hours":8,"date":"2026-03-30"}',
 0,'2026-03-28',NULL,0,NULL,
 '10 years NEET coaching, 95% students clear in first attempt',
 'active','approved',200),

-- Course (id=18)
(18,10,6,'Class 10 CBSE Maths Mastery — 2-Month Course',
 '2-month focused CBSE Maths course for Class 10 students. Chapter-wise tests, doubt sessions, and NCERT solutions. Guaranteed 95+ score or fee refund.',
 'course',
 'Saraswathi Vidya Mandir Lane, Banjara Hills, Hyderabad',17.41500,78.44300,
 2000.00,'{"billing":"monthly","months":2,"fee_per_month":2000,"total_fee":4000,"duration_weeks":8}',
 0,'2026-03-22','2026-03-01',10,1500.00,
 'M.Sc Mathematics, 14 years school teaching experience',
 'active','approved',38),

-- ══ YOGA & FITNESS ═════════════════════════════════════════════
-- Regular (id=19)
(19,14,7,'Sunrise Yoga — Morning Batch',
 'Daily morning yoga sessions covering Hatha and Pranayama for all fitness levels. Suitable from age 12 upward. Outdoor and indoor options. Specialised kids sessions on weekends.',
 'regular',
 'Durgam Cheruvu Lake Park, Hyderabad',17.44700,78.38600,
 1000.00,'{"billing":"monthly","fee_per_month":1000,"sessions_per_week":6,"session_duration_mins":60}',
 1,'2026-12-31',NULL,0,NULL,
 'RYT-500 certified, 10 years practice, 7 years teaching',
 'active','approved',88),

-- Workshop (id=20)
(20,14,7,'Aerial Yoga Introduction Workshop',
 'Exciting 2-hour introduction to aerial yoga using silk hammocks. No prior experience needed. Age 10+. All equipment provided at our air-conditioned studio.',
 'workshop',
 'FlyYoga Studio, Road 36, Jubilee Hills, Hyderabad',17.43000,78.41100,
 800.00,'{"billing":"once","total_fee":800,"duration_hours":2,"date":"2026-04-06"}',
 0,'2026-04-05',NULL,0,NULL,
 'Certified aerial yoga instructor, studied in Rishikesh',
 'active','approved',55),

-- Course (id=21)
(21,10,7,'6-Week Zumba Toning Program',
 '6-week structured Zumba toning program. Merges dance fitness with targeted muscle conditioning. 3 sessions per week. Diet plan included.',
 'course',
 'Gold''s Gym Annex, Banjara Hills Road 2, Hyderabad',17.41000,78.44000,
 3500.00,'{"billing":"monthly","months":1,"fee_per_month":3500,"total_fee":3500,"duration_weeks":6}',
 0,'2026-04-10','2026-03-25',5,2800.00,
 'Licensed Zumba instructor B1, ACE certified personal trainer',
 'active','approved',24),

-- ══ LANGUAGE ═══════════════════════════════════════════════════
-- Regular (id=22)
(22,11,8,'Spoken English Fluency Club',
 'Weekly spoken English sessions focusing on pronunciation, grammar in use, and public speaking. Role play, group discussions, and debates. All proficiency levels.',
 'regular',
 'British Council Lane, Somajiguda, Hyderabad',17.42800,78.45200,
 1200.00,'{"billing":"monthly","fee_per_month":1200,"sessions_per_week":3,"session_duration_mins":60}',
 1,'2026-12-31',NULL,0,NULL,
 'CELTA certified, 8 years ESL teaching, Cambridge examiner',
 'active','approved',72),

-- Workshop (id=23)
(23,11,8,'Japanese for Absolute Beginners — 1-Day Intro',
 'Spend a fun day learning hiragana, basic phrases, and Japanese culture. Great starting point for JLPT N5 prep. Study materials and snacks included.',
 'workshop',
 'Japan Information Centre, Somajiguda, Hyderabad',17.42600,78.45500,
 700.00,'{"billing":"once","total_fee":700,"duration_hours":6,"date":"2026-04-19"}',
 0,'2026-04-18','2026-04-01',8,550.00,
 'N2 certified, lived in Tokyo 3 years, Japanese language tutor since 2019',
 'active','approved',30),

-- Course (id=24)
(24,12,8,'French A1 + A2 — 2-Month Course',
 '2-month beginner French course aligned with DELF A1/A2 standards. Reading, writing, speaking, and listening. Small batches of 6.',
 'course',
 'Alliance Française Hyderabad, Banjara Hills',17.41800,78.44500,
 2800.00,'{"billing":"monthly","months":2,"fee_per_month":2800,"total_fee":5600,"duration_weeks":8}',
 1,'2026-04-08','2026-03-20',4,2300.00,
 'Diplômée de l''Université de Paris, Alliance Française certified teacher',
 'active','approved',16),

-- ══ CHESS ══════════════════════════════════════════════════════
-- Regular (id=25)
(25,13,10,'Champions Chess Academy',
 'Rated chess coaching for children and adults at all FIDE skill levels. Covers openings, middle-game tactics, end-game strategy, and tournament preparation.',
 'regular',
 'Chess Park, Madhapur, Hyderabad',17.44400,78.39000,
 1500.00,'{"billing":"monthly","fee_per_month":1500,"sessions_per_week":2,"session_duration_mins":90}',
 1,'2026-12-31',NULL,0,NULL,
 'FIDE Candidate Master, State Chess Champion 2019',
 'active','approved',48),

-- Workshop (id=26)
(26,13,10,'Chess Tactics & Puzzle Workshop',
 'Full-day interactive workshop on tactical patterns — pins, forks, skewers, discovered attacks. 100 curated puzzles. For players rated 800–1500.',
 'workshop',
 'TSCA Venue, Nampally, Hyderabad',17.38500,78.47000,
 500.00,'{"billing":"once","total_fee":500,"duration_hours":6,"date":"2026-04-26"}',
 0,'2026-04-24',NULL,0,NULL,
 'FIDE Candidate Master, 200+ students trained to 1800+ Elo',
 'active','approved',32),

-- ══ THEATRE ════════════════════════════════════════════════════
-- Regular (id=27)
(27,14,9,'Young Actors'' Theatre Workshop Series',
 'Ongoing theatre training for youth aged 10–20. Covers voice modulation, improvisation, script reading, character building, and stage performance. Annual play production.',
 'regular',
 'Shilpakala Vedika Backstage, Hyderabad',17.40700,78.47500,
 1800.00,'{"billing":"monthly","fee_per_month":1800,"sessions_per_week":2,"session_duration_mins":120}',
 0,'2026-12-31',NULL,0,NULL,
 'NSD Delhi alumnus, directed 20+ stage productions',
 'active','approved',35),

-- Course (id=28)
(28,14,9,'Screenplay Writing Masterclass — 1-Month',
 '1-month intensive screenplay writing course. Learn three-act structure, character arcs, dialogue, and industry formatting. Script evaluated by a working writer.',
 'course',
 'Film Chamber Building, Filmnagar, Hyderabad',17.41100,78.39700,
 6000.00,'{"billing":"monthly","months":1,"fee_per_month":6000,"total_fee":6000,"duration_weeks":4}',
 0,'2026-04-15','2026-04-01',3,5000.00,
 'Written for Amazon Prime India and Star Vijay, 15 years industry experience',
 'active','approved',9);

-- =============================================================
-- LISTING AVAILABILITIES
-- Regular = recurring weekly slots for next 8 weeks from 2026-03-01
-- Workshop = single date slot
-- Course   = recurring weekly slots for course duration
-- =============================================================

-- Helper: We insert representative rows (Mon/Wed/Fri patterns)
-- Regular class availabilities (listing 1 — Carnatic Vocal)
INSERT IGNORE INTO `listing_availabilities`
  (`listing_id`,`available_date`,`available_time`) VALUES
(1,'2026-03-02','16:00:00'),(1,'2026-03-04','16:00:00'),(1,'2026-03-06','16:00:00'),
(1,'2026-03-09','16:00:00'),(1,'2026-03-11','16:00:00'),(1,'2026-03-13','16:00:00'),
(1,'2026-03-16','16:00:00'),(1,'2026-03-18','16:00:00'),(1,'2026-03-20','16:00:00'),
(1,'2026-03-23','16:00:00'),(1,'2026-03-25','16:00:00'),(1,'2026-03-27','16:00:00');

-- Workshop date (listing 2)
INSERT IGNORE INTO `listing_availabilities`
  (`listing_id`,`available_date`,`available_time`) VALUES
(2,'2026-03-15','10:00:00');

-- Guitar course (listing 3) — Sat/Sun
INSERT IGNORE INTO `listing_availabilities`
  (`listing_id`,`available_date`,`available_time`) VALUES
(3,'2026-03-21','10:00:00'),(3,'2026-03-22','10:00:00'),
(3,'2026-03-28','10:00:00'),(3,'2026-03-29','10:00:00'),
(3,'2026-04-04','10:00:00'),(3,'2026-04-05','10:00:00'),
(3,'2026-04-11','10:00:00'),(3,'2026-04-12','10:00:00');

-- Bharatanatyam (listing 4)
INSERT IGNORE INTO `listing_availabilities`
  (`listing_id`,`available_date`,`available_time`) VALUES
(4,'2026-03-02','17:00:00'),(4,'2026-03-04','17:00:00'),(4,'2026-03-07','17:00:00'),
(4,'2026-03-09','17:00:00'),(4,'2026-03-11','17:00:00'),(4,'2026-03-14','17:00:00'),
(4,'2026-03-16','17:00:00'),(4,'2026-03-18','17:00:00'),(4,'2026-03-21','17:00:00');

-- Zumba Workshop (listing 5)
INSERT IGNORE INTO `listing_availabilities`
  (`listing_id`,`available_date`,`available_time`) VALUES
(5,'2026-03-22','09:00:00');

-- Cricket (listing 7)
INSERT IGNORE INTO `listing_availabilities`
  (`listing_id`,`available_date`,`available_time`) VALUES
(7,'2026-03-02','07:00:00'),(7,'2026-03-04','07:00:00'),
(7,'2026-03-07','07:00:00'),(7,'2026-03-09','07:00:00'),
(7,'2026-03-11','07:00:00'),(7,'2026-03-14','07:00:00'),
(7,'2026-03-16','07:00:00'),(7,'2026-03-18','07:00:00');

-- Swimming Workshop (listing 8)
INSERT IGNORE INTO `listing_availabilities`
  (`listing_id`,`available_date`,`available_time`) VALUES
(8,'2026-04-05','08:00:00');

-- Badminton course (listing 9) — Tue/Thu/Sat
INSERT IGNORE INTO `listing_availabilities`
  (`listing_id`,`available_date`,`available_time`) VALUES
(9,'2026-03-29','06:30:00'),(9,'2026-04-02','06:30:00'),(9,'2026-04-05','06:30:00'),
(9,'2026-04-07','06:30:00'),(9,'2026-04-09','06:30:00'),(9,'2026-04-12','06:30:00');

-- Art Studio (listing 10) — Sat/Sun
INSERT IGNORE INTO `listing_availabilities`
  (`listing_id`,`available_date`,`available_time`) VALUES
(10,'2026-03-01','10:00:00'),(10,'2026-03-02','10:00:00'),
(10,'2026-03-08','10:00:00'),(10,'2026-03-09','10:00:00'),
(10,'2026-03-15','10:00:00'),(10,'2026-03-16','10:00:00');

-- IIT-JEE Tuitions (listing 16) — Mon–Fri
INSERT IGNORE INTO `listing_availabilities`
  (`listing_id`,`available_date`,`available_time`) VALUES
(16,'2026-03-02','15:00:00'),(16,'2026-03-03','15:00:00'),(16,'2026-03-04','15:00:00'),
(16,'2026-03-05','15:00:00'),(16,'2026-03-06','15:00:00'),
(16,'2026-03-09','15:00:00'),(16,'2026-03-10','15:00:00'),(16,'2026-03-11','15:00:00');

-- NEET Workshop (listing 17)
INSERT IGNORE INTO `listing_availabilities`
  (`listing_id`,`available_date`,`available_time`) VALUES
(17,'2026-03-30','08:00:00');

-- Yoga (listing 19) — Mon–Sat
INSERT IGNORE INTO `listing_availabilities`
  (`listing_id`,`available_date`,`available_time`) VALUES
(19,'2026-03-02','06:00:00'),(19,'2026-03-03','06:00:00'),(19,'2026-03-04','06:00:00'),
(19,'2026-03-05','06:00:00'),(19,'2026-03-06','06:00:00'),(19,'2026-03-07','06:00:00'),
(19,'2026-03-09','06:00:00'),(19,'2026-03-10','06:00:00'),(19,'2026-03-11','06:00:00');

-- Aerial Yoga Workshop (listing 20)
INSERT IGNORE INTO `listing_availabilities`
  (`listing_id`,`available_date`,`available_time`) VALUES
(20,'2026-04-06','10:00:00');

-- Spoken English (listing 22)
INSERT IGNORE INTO `listing_availabilities`
  (`listing_id`,`available_date`,`available_time`) VALUES
(22,'2026-03-02','18:30:00'),(22,'2026-03-04','18:30:00'),(22,'2026-03-06','18:30:00'),
(22,'2026-03-09','18:30:00'),(22,'2026-03-11','18:30:00'),(22,'2026-03-13','18:30:00');

-- Chess Academy (listing 25)
INSERT IGNORE INTO `listing_availabilities`
  (`listing_id`,`available_date`,`available_time`) VALUES
(25,'2026-03-03','17:00:00'),(25,'2026-03-05','17:00:00'),
(25,'2026-03-10','17:00:00'),(25,'2026-03-12','17:00:00'),
(25,'2026-03-17','17:00:00'),(25,'2026-03-19','17:00:00');

-- =============================================================
-- REVIEWS (multiple parents per listing, realistic ratings)
-- =============================================================
INSERT IGNORE INTO `reviews`
  (`listing_id`,`user_id`,`rating`,`review_text`) VALUES
-- Carnatic Vocal (1)
(1,20,5,'Meera teaches with so much patience. My daughter has improved tremendously in just 3 months!'),
(1,21,5,'Best classical music classes in Hyderabad. Highly recommended for serious students.'),
(1,22,4,'Very structured curriculum. Meera tracks every student individually.'),
-- Bollywood Singing Workshop (2)
(2,20,5,'Amazing workshop! The energy was incredible and I actually learned real techniques.'),
(2,21,4,'Good workshop. Could have covered more in the time given but overall great experience.'),
-- Guitar Course (3)
(3,22,5,'My son went from zero to playing full songs in 2 months. Brilliant teaching.'),
(3,21,4,'Great curriculum. The free instrument rental was a huge plus.'),
-- Bharatanatyam (4)
(4,20,5,'Ravi Sir is exceptional. My daughter''s arangetram preparation is going superbly.'),
(4,22,5,'Proper classical training with the right values. A gem of a class in Jubilee Hills.'),
(4,21,4,'Good classes. Parking near the studio can be tricky.'),
-- Zumba Workshop (5)
(5,20,5,'So much fun! Lost count of calories burned. Will attend every Zumba event by Priya.'),
(5,21,5,'Best workout disguised as a dance party I''ve ever attended!'),
-- Cricket Academy (7)
(7,20,5,'My son''s batting has transformed. Coach explains the technical aspects beautifully.'),
(7,21,4,'Very professional setup. BCCI-certified coaches and proper ground nets.'),
(7,22,5,'Enrolled my twins. Both have made the school cricket team now. 10/10!'),
-- Art Studio (10)
(10,20,5,'My shy daughter now confidently shows her artwork at school. Love this studio.'),
(10,21,4,'Creative sessions every week. Kids never want to miss a class.'),
-- Android Workshop (14)
(14,20,5,'My 14-year-old built her first app in one day! Incredible workshop.'),
(14,22,4,'Well-organized. Priya is very knowledgeable and makes complex things simple.'),
-- Python Classes (13)
(13,21,5,'Best Python classes for kids. My son is now building his own games.'),
(13,22,5,'Structured, practical, and fun. My daughter loves every session.'),
-- IIT-JEE (16)
(16,20,5,'My son''s Physics marks jumped from 60 to 88 in one term. Results speak.'),
(16,22,5,'Sanjay Sir is outstanding. Concepts explained from fundamentals up.'),
(16,21,4,'Small batch ensures individual attention. Worth every rupee.'),
-- Yoga (19)
(19,20,5,'Anitha''s sunrise yoga is the best start to any day. Calm, focused, and powerful.'),
(19,21,5,'My back pain is completely gone after 2 months. Life changing.'),
(19,22,4,'Wonderful outdoor sessions by the lake. The environment itself is healing.'),
-- Spoken English (22)
(22,20,5,'My confidence in speaking English has multiplied. The debate sessions are brilliant.'),
(22,21,4,'Structured syllabus, friendly atmosphere. Saw improvement in 6 weeks.'),
-- Chess (25)
(25,22,5,'My son went from a beginner to winning his school district tournament after 4 months.'),
(25,21,4,'Great teaching methodology. Covers openings systematically.'),
-- Theatre (27)
(27,20,5,'My introvert son is now performing on stage confidently. Theatre is magic here.'),
(27,21,4,'Real NSD-trained director. The annual production was phenomenal.');

-- =============================================================
-- SAMPLE BOOKINGS (for transaction/revenue testing)
-- =============================================================
INSERT IGNORE INTO `bookings`
  (`id`,`listing_id`,`parent_id`,`student_name`,`student_age`,
   `booking_type`,`class_date`,`class_time`,
   `payment_amount`,`payment_id`,`payment_status`,`booking_status`)
VALUES
(1,1,20,'Ananya Arun',10,'regular','2026-03-02','16:00:00',
 1500.00,'pay_demo_001','paid','confirmed'),
(2,4,21,'Vikram Sunita',12,'regular','2026-03-02','17:00:00',
 1800.00,'pay_demo_002','paid','confirmed'),
(3,7,22,'Roshan Deepak',14,'regular','2026-03-02','07:00:00',
 2000.00,'pay_demo_003','paid','confirmed'),
(4,2,20,'Ananya Arun',10,'regular','2026-03-15','10:00:00',
 799.00,'pay_demo_004','paid','confirmed'),
(5,16,21,'Arjun Sunita',16,'regular','2026-03-02','15:00:00',
 3500.00,'pay_demo_005','paid','confirmed'),
(6,13,22,'Shruti Deepak',13,'regular','2026-03-01','10:00:00',
 2500.00,'pay_demo_006','paid','confirmed');

-- =============================================================
-- TRANSACTIONS (payment records with commission split)
-- amount = full fee,  platform cuts 15% commission
-- =============================================================
INSERT IGNORE INTO `transactions`
  (`booking_id`,`user_id`,`amount`,`transaction_type`,`razorpay_id`,`status`,`settled_at`)
VALUES
(1,20,1500.00,'payment','rzp_demo_001','success','2026-03-02 16:30:00'),
(2,21,1800.00,'payment','rzp_demo_002','success','2026-03-02 17:30:00'),
(3,22,2000.00,'payment','rzp_demo_003','success','2026-03-02 07:30:00'),
(4,20, 799.00,'payment','rzp_demo_004','success','2026-03-15 11:00:00'),
(5,21,3500.00,'payment','rzp_demo_005','success','2026-03-02 15:30:00'),
(6,22,2500.00,'payment','rzp_demo_006','success','2026-03-01 10:30:00');

-- =============================================================
-- FEATURED CAROUSELS
-- State = 'Telangana' (home state of Hyderabad)
-- Also add 'ALL' national entries so they show regardless
-- =============================================================
INSERT IGNORE INTO `featured_carousels` (`state`,`listing_id`,`position`) VALUES
('Telangana', 1, 1),   -- Carnatic Vocal
('Telangana', 4, 2),   -- Bharatanatyam
('Telangana', 7, 3),   -- Cricket Academy
('Telangana',13, 4),   -- Python Classes
('Telangana',19, 5),   -- Sunrise Yoga
('ALL',        2, 1),  -- Bollywood Singing Workshop
('ALL',        5, 2);  -- Zumba Workshop

-- =============================================================
-- RE-ENABLE FK
-- =============================================================
SET FOREIGN_KEY_CHECKS = 1;

SELECT CONCAT(
  (SELECT COUNT(*) FROM listings WHERE status='active' AND review_status='approved'),
  ' active listings seeded across ',
  (SELECT COUNT(DISTINCT category_id) FROM listings WHERE status='active'),
  ' categories.'
) AS seed_summary;
