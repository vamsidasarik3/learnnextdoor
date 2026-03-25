-- =============================================================
-- Class Next Door — Delhi Demo Data Seed (009_delhi_data.sql)
-- Location : New Delhi (lat≈28.61, lng≈77.21)
-- =============================================================

USE `custom_new`;
SET FOREIGN_KEY_CHECKS = 0;

-- ── Providers for Delhi ───────────────────────────────────────
-- Using IDs 30-35 to avoid conflicts
INSERT IGNORE INTO `users`
  (`id`,`name`,`email`,`password`,`phone`,`role`,`status`,`address`) VALUES
(30,'Arjun Delhi','arjun.delhi@classnextdoor.dev',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 '9811000001',2,1,'Connaught Place, New Delhi'),
(31,'Sonia Delhi','sonia.delhi@classnextdoor.dev',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 '9811000002',2,1,'Saket, New Delhi'),
(32,'Rahul Delhi','rahul.delhi@classnextdoor.dev',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 '9811000003',2,1,'Vasant Kunj, New Delhi'),
(33,'Pooja Delhi','pooja.delhi@classnextdoor.dev',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 '9811000004',2,1,'Hauz Khas, New Delhi'),
(34,'Vikram Delhi','vikram.delhi@classnextdoor.dev',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 '9811000005',2,1,'Dwarka, New Delhi');

-- ── Listings for Delhi ────────────────────────────────────────
-- 30 listings across different categories and types
INSERT IGNORE INTO `listings`
(`id`,`provider_id`,`category_id`,`title`,`description`,`type`,
 `address`,`latitude`,`longitude`,
 `price`,`price_breakdown`,`free_trial`,
 `registration_end_date`,`status`,`review_status`,`total_students`) VALUES

-- Music (Cat 1)
(101,30,1,'Delhi Academy of Music','Classical and contemporary music training.','regular','Connaught Place, New Delhi',28.63150,77.21670,1800.00,'{"billing":"monthly","fee_per_month":1800,"sessions_per_week":3}',1,'2026-12-31','active','approved',12),
(102,30,1,'Sitar Workshop','One day intensive sitar workshop.','workshop','Mandala House, CP, Delhi',28.62950,77.21850,999.00,'{"billing":"once","total_fee":999,"duration_hours":4,"date":"2026-04-10"}',0,'2026-04-05','active','approved',5),
(103,31,1,'Electronic Music Course','3-month course on music production.','course','Saket District Centre, Delhi',28.52440,77.21000,5000.00,'{"billing":"monthly","months":3,"fee_per_month":5000,"total_fee":15000}',0,'2026-03-31','active','approved',8),

-- Dance (Cat 2)
(104,31,2,'Saket Dance Studio','Zumba, Salsa and Hip Hop for all.','regular','M-Block Market, Saket, Delhi',28.52600,77.21200,2000.00,'{"billing":"monthly","fee_per_month":2000,"sessions_per_week":3}',1,'2026-12-31','active','approved',25),
(105,32,2,'Kathak for Beginners','Explore the beauty of Kathak.','course','Vasant Kunj Sector C, Delhi',28.52930,77.15190,2500.00,'{"billing":"monthly","months":2,"fee_per_month":2500,"total_fee":5000}',1,'2026-04-01','active','approved',15),
(106,32,2,'Hip Hop Intensive Workshop','Full day session on hip hop moves.','workshop','Vasant Square Mall, Delhi',28.53100,77.15400,1200.00,'{"billing":"once","total_fee":1200,"duration_hours":6,"date":"2026-05-15"}',0,'2026-05-10','active','approved',10),

-- Sports (Cat 3)
(107,33,3,'South Delhi Cricket Academy','Professional coaching for future stars.','regular','Hauz Khas Sports Complex, Delhi',28.54940,77.19030,3000.00,'{"billing":"monthly","fee_per_month":3000,"sessions_per_week":4}',1,'2026-12-31','active','approved',40),
(108,33,3,'Football Weekend Camp','2-month football training camp.','course','Green Park, Delhi',28.55800,77.20200,2500.00,'{"billing":"monthly","months":2,"fee_per_month":2500,"total_fee":5000}',0,'2026-03-25','active','approved',30),
(109,34,3,'Swimming Fast Pass','Learn to swim in 15 days.','workshop','Dwarka Sector 10, Delhi',28.58230,77.05000,1500.00,'{"billing":"once","total_fee":1500,"duration_hours":15,"date":"2026-06-01"}',0,'2026-05-25','active','approved',20),

-- Art (Cat 4)
(110,34,4,'Dwarka Art Hub','Drawing and painting for kids.','regular','Dwarka Sector 6, Delhi',28.58500,77.05500,1200.00,'{"billing":"monthly","fee_per_month":1200,"sessions_per_week":2}',1,'2026-12-31','active','approved',18),
(111,30,4,'Pottery Workshop 101','Learn the art of pottery.','workshop','CP Outer Circle, Delhi',28.63300,77.22000,800.00,'{"billing":"once","total_fee":800,"duration_hours":3,"date":"2026-04-20"}',0,'2026-04-15','active','approved',10),
(112,31,4,'Fine Arts Diploma Course','Certified course in fine arts.','course','PVR Saket Road, Delhi',28.52300,77.21500,4000.00,'{"billing":"monthly","months":6,"fee_per_month":4000,"total_fee":24000}',0,'2026-03-15','active','approved',5),

-- Coding (Cat 5)
(113,32,5,'Vasant Kunj Coding Club','Python and Web Dev for kids.','regular','DLF Promenade, Delhi',28.54300,77.15600,3500.00,'{"billing":"monthly","fee_per_month":3500,"sessions_per_week":2}',1,'2026-12-31','active','approved',20),
(114,33,5,'AI and Robotics Workshop','Build your first robot.','workshop','IIT Delhi area, Delhi',28.54500,77.19500,2000.00,'{"billing":"once","total_fee":2000,"duration_hours":8,"date":"2026-04-25"}',0,'2026-04-20','active','approved',25),
(115,34,5,'Full Stack Bootcamp','3-month intensive coding bootcamp.','course','Dwarka Sector 21, Delhi',28.55200,77.06200,8000.00,'{"billing":"monthly","months":3,"fee_per_month":8000,"total_fee":24000}',0,'2026-04-05','active','approved',12),

-- Academics (Cat 6)
(116,30,6,'CP Toppers Institute','Maths and Science tuitions.','regular','Scindia House, CP, Delhi',28.63000,77.21800,2500.00,'{"billing":"monthly","fee_per_month":2500,"sessions_per_week":5}',1,'2026-12-31','active','approved',50),
(117,31,6,'English Speaking Course','Improve your confidence in English.','course','Saket J-Block, Delhi',28.52700,77.21100,2000.00,'{"billing":"monthly","months":2,"fee_per_month":2000,"total_fee":4000}',1,'2026-04-10','active','approved',30),
(118,32,6,'Vedic Maths Workshop','Learn speed calculation.','workshop','Vasant Kunj B-Block, Delhi',28.53500,77.16000,500.00,'{"billing":"once","total_fee":500,"duration_hours":4,"date":"2026-05-01"}',0,'2026-04-25','active','approved',45),

-- Yoga (Cat 7)
(119,33,7,'Hauz Khas Yoga Studio','Yoga for health and peace.','regular','Deer Park Road, Delhi',28.55200,77.18500,1500.00,'{"billing":"monthly","fee_per_month":1500,"sessions_per_week":3}',1,'2026-12-31','active','approved',35),
(120,34,7,'Kids Yoga Camp','Summer yoga camp for children.','course','Dwarka Sector 12, Delhi',28.59000,77.04500,1000.00,'{"billing":"monthly","months":1,"fee_per_month":1000,"total_fee":1000}',1,'2026-05-15','active','approved',20),
(121,30,7,'Meditation Workshop','Find your inner calm.','workshop','CP Inner Circle, Delhi',28.63250,77.21950,300.00,'{"billing":"once","total_fee":300,"duration_hours":2,"date":"2026-04-15"}',0,'2026-04-10','active','approved',60),

-- More Miscellaneous to reach 30
(122,31,8,'Spoken French Delhi','Level A1 beginner course.','course','Malviya Nagar, Delhi',28.53000,77.20000,3000.00,'{"billing":"monthly","months":3,"fee_per_month":3000,"total_fee":9000}',0,'2026-04-01','active','approved',10),
(123,32,10,'Grandmaster Chess Academy','Learn chess from experts.','regular','Vasant Kunj, Delhi',28.53800,77.15500,2000.00,'{"billing":"monthly","fee_per_month":2000,"sessions_per_week":2}',1,'2026-12-31','active','approved',15),
(124,33,9,'Theatre for Kids','Speech and drama classes.','regular','Hauz Khas Village, Delhi',28.55400,77.19400,1800.00,'{"billing":"monthly","fee_per_month":1800,"sessions_per_week":2}',1,'2026-12-31','active','approved',20),
(125,34,11,'Photography Basics','Learn to handle your DSLR.','workshop','Dwarka Sector 19, Delhi',28.57500,77.06500,1500.00,'{"billing":"once","total_fee":1500,"duration_hours":6,"date":"2026-05-20"}',0,'2026-05-15','active','approved',12),
(126,30,1,'Rock Guitar Intensive','Learn rock guitar solos.','course','CP, New Delhi',28.63100,77.21700,3500.00,'{"billing":"monthly","months":2,"fee_per_month":3500,"total_fee":7000}',0,'2026-04-05','active','approved',6),
(127,31,2,'Contemporary Dance Workshop','Express yourself through dance.','workshop','Saket, Delhi',28.52500,77.21300,600.00,'{"billing":"once","total_fee":600,"duration_hours":3,"date":"2026-04-12"}',0,'2026-04-10','active','approved',15),
(128,32,3,'Tennis Fundamentals','Coaching for all ages.','regular','Vasant Kunj, Delhi',28.52800,77.15000,2500.00,'{"billing":"monthly","fee_per_month":2500,"sessions_per_week":3}',1,'2026-12-31','active','approved',22),
(129,33,4,'Canvas Painting Course','Master the oil on canvas.','course','Hauz Khas, Delhi',28.55000,77.19100,3000.00,'{"billing":"monthly","months":3,"fee_per_month":3000,"total_fee":9000}',0,'2026-04-10','active','approved',8),
(130,34,5,'Game Development for Kids','Build your first game in Unity.','regular','Dwarka, Delhi',28.58000,77.05200,4000.00,'{"billing":"monthly","fee_per_month":4000,"sessions_per_week":2}',1,'2026-12-31','active','approved',14);

SET FOREIGN_KEY_CHECKS = 1;
