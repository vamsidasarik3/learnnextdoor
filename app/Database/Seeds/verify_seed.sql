SELECT c.name AS category, l.type, COUNT(*) AS cnt
FROM listings l
JOIN categories c ON c.id = l.category_id
WHERE l.status='active' AND l.review_status='approved'
GROUP BY c.name, l.type
ORDER BY c.name, l.type;

SELECT l.type, COUNT(*) AS total FROM listings
WHERE status='active' AND review_status='approved'
GROUP BY l.type;

SELECT COUNT(*) AS total_reviews FROM reviews;
SELECT COUNT(*) AS total_bookings FROM bookings;
SELECT * FROM featured_carousels ORDER BY state, position;
SELECT `key`, `value` FROM settings WHERE `key` LIKE 'commission%';
