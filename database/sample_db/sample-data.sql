USE agromarketplace;

START TRANSACTION;

-- Example Login
-- All sample buyer, farmer, delivery, and admin accounts use DemoPass123!. For example, log in with demo.buyer.01@agromarketplace.test.

-- Local demo accounts all use the password: DemoPass123!
INSERT IGNORE INTO users (
  role, username, password, email, fullname, phone, address,
  profile_description, market_preferences, business_name,
  preferred_buying_category, typical_order_volume, land_area, primary_crops,
  verification_documents, vehicle_capacity, vehicle_license_plate
) VALUES
('farmer', 'demo_farmer_01', '$2y$12$3MF0g68fEllTxYKoj8WtTO2e.Zb6U01Mb18vjUUvr4DNGfOsVPQIe', 'demo.farmer.01@agromarketplace.test', 'Rahim Uddin', '+8801700000101', 'Mymensingh, Bangladesh', 'Family farm supplying clean, graded rice and grains.', 'Seasonal direct contracts', 'Mymensingh Grain House', NULL, NULL, '18 acres', 'Rice, wheat, lentils', 'NID and land record', NULL, NULL),
('farmer', 'demo_farmer_02', '$2y$12$3MF0g68fEllTxYKoj8WtTO2e.Zb6U01Mb18vjUUvr4DNGfOsVPQIe', 'demo.farmer.02@agromarketplace.test', 'Jamal Mia', '+8801700000102', 'Dinajpur, Bangladesh', 'Bulk grain grower with scheduled harvests.', 'Bulk lots and warehouse pickup', 'Dinajpur Harvest Co.', NULL, NULL, '24 acres', 'Wheat, corn, mustard', 'NID and farm certificate', NULL, NULL),
('farmer', 'demo_farmer_03', '$2y$12$3MF0g68fEllTxYKoj8WtTO2e.Zb6U01Mb18vjUUvr4DNGfOsVPQIe', 'demo.farmer.03@agromarketplace.test', 'Aminul Sarker', '+8801700000103', 'Bogra, Bangladesh', 'Produce supplier focused on fresh vegetables and pulses.', 'Weekly wholesale supply', 'Bogra Fresh Fields', NULL, NULL, '12 acres', 'Potatoes, chili, lentils', 'NID and cooperative record', NULL, NULL),
('farmer', 'demo_farmer_04', '$2y$12$3MF0g68fEllTxYKoj8WtTO2e.Zb6U01Mb18vjUUvr4DNGfOsVPQIe', 'demo.farmer.04@agromarketplace.test', 'Mizan Agro', '+8801700000104', 'Rajshahi, Bangladesh', 'Orchard and oilseed produce with harvest notes.', 'Regional produce contracts', 'Mizan Agro', NULL, NULL, '30 acres', 'Mango, mustard, onion, ginger', 'NID and business certificate', NULL, NULL),
('buyer', 'demo_buyer_01', '$2y$12$3MF0g68fEllTxYKoj8WtTO2e.Zb6U01Mb18vjUUvr4DNGfOsVPQIe', 'demo.buyer.01@agromarketplace.test', 'Ayesha Rahman', '+8801700000201', 'Dhaka, Bangladesh', NULL, 'Direct sourcing and seasonal contracts', 'Dhaka Food Supply', 'Rice', '5 MT monthly', NULL, NULL, NULL, NULL, NULL),
('buyer', 'demo_buyer_02', '$2y$12$3MF0g68fEllTxYKoj8WtTO2e.Zb6U01Mb18vjUUvr4DNGfOsVPQIe', 'demo.buyer.02@agromarketplace.test', 'Hasan Mahmud', '+8801700000202', 'Gazipur, Bangladesh', NULL, 'Graded milling wheat', 'North Star Mills', 'Wheat', '8 MT monthly', NULL, NULL, NULL, NULL, NULL),
('buyer', 'demo_buyer_03', '$2y$12$3MF0g68fEllTxYKoj8WtTO2e.Zb6U01Mb18vjUUvr4DNGfOsVPQIe', 'demo.buyer.03@agromarketplace.test', 'Sadia Haque', '+8801700000203', 'Narayanganj, Bangladesh', NULL, 'Fresh weekly delivery', 'Green Basket Wholesale', 'Vegetables', '3 MT weekly', NULL, NULL, NULL, NULL, NULL),
('buyer', 'demo_buyer_04', '$2y$12$3MF0g68fEllTxYKoj8WtTO2e.Zb6U01Mb18vjUUvr4DNGfOsVPQIe', 'demo.buyer.04@agromarketplace.test', 'Karim Traders', '+8801700000204', 'Chattogram, Bangladesh', NULL, 'Bulk produce with verified quality', 'Karim Commodity House', 'Pulse & Seeds', '10 MT monthly', NULL, NULL, NULL, NULL, NULL),
('buyer', 'demo_buyer_05', '$2y$12$3MF0g68fEllTxYKoj8WtTO2e.Zb6U01Mb18vjUUvr4DNGfOsVPQIe', 'demo.buyer.05@agromarketplace.test', 'Nusrat Jahan', '+8801700000205', 'Sylhet, Bangladesh', NULL, 'Organic and export-quality lots', 'Sylhet Market Link', 'Other', '2 MT monthly', NULL, NULL, NULL, NULL, NULL),
('delivery', 'demo_driver_01', '$2y$12$3MF0g68fEllTxYKoj8WtTO2e.Zb6U01Mb18vjUUvr4DNGfOsVPQIe', 'demo.driver.01@agromarketplace.test', 'Rashed Ahmed', '+8801700000301', 'Dhaka, Bangladesh', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '5 MT truck', 'DHA-TA-1101'),
('delivery', 'demo_driver_02', '$2y$12$3MF0g68fEllTxYKoj8WtTO2e.Zb6U01Mb18vjUUvr4DNGfOsVPQIe', 'demo.driver.02@agromarketplace.test', 'Farzana Akter', '+8801700000302', 'Rajshahi, Bangladesh', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '3 MT van', 'RAJ-VA-2202'),
('admin', 'demo_admin_01', '$2y$12$3MF0g68fEllTxYKoj8WtTO2e.Zb6U01Mb18vjUUvr4DNGfOsVPQIe', 'demo.admin.01@agromarketplace.test', 'Demo Administrator', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

SET @farmer_01 = (SELECT id FROM users WHERE username = 'demo_farmer_01');
SET @farmer_02 = (SELECT id FROM users WHERE username = 'demo_farmer_02');
SET @farmer_03 = (SELECT id FROM users WHERE username = 'demo_farmer_03');
SET @farmer_04 = (SELECT id FROM users WHERE username = 'demo_farmer_04');
SET @buyer_01 = (SELECT id FROM users WHERE username = 'demo_buyer_01');
SET @buyer_02 = (SELECT id FROM users WHERE username = 'demo_buyer_02');
SET @buyer_03 = (SELECT id FROM users WHERE username = 'demo_buyer_03');
SET @buyer_04 = (SELECT id FROM users WHERE username = 'demo_buyer_04');
SET @buyer_05 = (SELECT id FROM users WHERE username = 'demo_buyer_05');
SET @driver_01 = (SELECT id FROM users WHERE username = 'demo_driver_01');
SET @driver_02 = (SELECT id FROM users WHERE username = 'demo_driver_02');
SET @grains = (SELECT id FROM categories WHERE name = 'Grains');
SET @rice = (SELECT id FROM categories WHERE name = 'Rice');
SET @wheat = (SELECT id FROM categories WHERE name = 'Wheat');
SET @vegetables = (SELECT id FROM categories WHERE name = 'Vegetables');
SET @pulses = (SELECT id FROM categories WHERE name = 'Pulse & Seeds');
SET @other = (SELECT id FROM categories WHERE name = 'Other');

INSERT INTO inventory_items (farmer_id, category_id, name, quantity, unit, harvest_date, quality_label, notes)
SELECT source.farmer_id, source.category_id, source.name, source.quantity, source.unit,
       source.harvest_date, source.quality_label, source.notes
FROM (
  SELECT @farmer_01 farmer_id, @rice category_id, 'Premium Nazirshail Rice' name, 50.000 quantity, 'MT' unit, '2026-10-12' harvest_date, 'Grade A · low moisture' quality_label, 'Sealed 50 kg bags; warehouse pickup available.' notes
  UNION ALL SELECT @farmer_01, @wheat, 'Winter Wheat', 24.000, 'MT', '2026-10-20', 'Milling grade', 'Cleaned and ready for sampling.'
  UNION ALL SELECT @farmer_01, @pulses, 'Red Lentils', 16.000, 'MT', '2026-10-08', 'Sortex cleaned', 'Stored in dry warehouse.'
  UNION ALL SELECT @farmer_01, @vegetables, 'Green Chili', 3.500, 'MT', '2026-10-05', 'Fresh harvest', 'Packed in ventilated crates.'
  UNION ALL SELECT @farmer_02, @wheat, 'Dinajpur Winter Wheat', 38.000, 'MT', '2026-10-18', 'Grade A', 'Bulk loading by appointment.'
  UNION ALL SELECT @farmer_02, @grains, 'Yellow Corn', 22.000, 'MT', '2026-10-25', 'Feed grade', 'Moisture report available.'
  UNION ALL SELECT @farmer_02, @other, 'Mustard Seeds', 12.000, 'MT', '2026-10-16', 'Clean whole seed', 'Jute sacks, 50 kg each.'
  UNION ALL SELECT @farmer_02, @vegetables, 'Fresh Potatoes', 30.000, 'MT', '2026-10-04', 'Table grade', 'Cooled storage, mixed sizes.'
  UNION ALL SELECT @farmer_03, @vegetables, 'Organic Potatoes', 18.000, 'MT', '2026-10-03', 'Organic · Grade A', 'Harvested this week.'
  UNION ALL SELECT @farmer_03, @vegetables, 'Assorted Green Chili', 7.000, 'MT', '2026-10-06', 'Fresh and sorted', 'Local varieties.'
  UNION ALL SELECT @farmer_03, @pulses, 'Split Red Lentils', 9.000, 'MT', '2026-10-09', 'Food grade', 'Cleaned and bagged.'
  UNION ALL SELECT @farmer_03, @vegetables, 'Red Onion', 14.000, 'MT', '2026-10-14', 'Medium bulb', 'Dry skin, ventilated store.'
  UNION ALL SELECT @farmer_04, @other, 'Rajshahi Mangoes', 8.000, 'MT', '2026-10-02', 'Premium table fruit', 'Mixed ripeness, hand sorted.'
  UNION ALL SELECT @farmer_04, @other, 'Cold-Pressed Mustard Oil', 4.000, 'MT', '2026-10-11', 'Food grade', 'Sealed food-safe containers.'
  UNION ALL SELECT @farmer_04, @vegetables, 'White Onion', 11.000, 'MT', '2026-10-15', 'Export sort', 'Stored in dry ventilated room.'
  UNION ALL SELECT @farmer_04, @other, 'Fresh Ginger', 5.000, 'MT', '2026-10-07', 'Washed and graded', 'Crates available for pickup.'
) source
WHERE NOT EXISTS (
  SELECT 1 FROM inventory_items existing
  WHERE existing.farmer_id = source.farmer_id AND existing.name = source.name
);

UPDATE inventory_items
SET quantity = CASE name
  WHEN 'Premium Nazirshail Rice' THEN 42.000
  WHEN 'Winter Wheat' THEN 19.000
  WHEN 'Yellow Corn' THEN 16.000
  WHEN 'Mustard Seeds' THEN 9.000
  WHEN 'Organic Potatoes' THEN 14.000
  WHEN 'Red Onion' THEN 11.500
  WHEN 'Rajshahi Mangoes' THEN 6.000
  WHEN 'Cold-Pressed Mustard Oil' THEN 3.000
  ELSE quantity
END
WHERE farmer_id IN (@farmer_01, @farmer_02, @farmer_03, @farmer_04)
  AND name IN ('Premium Nazirshail Rice', 'Winter Wheat', 'Yellow Corn', 'Mustard Seeds', 'Organic Potatoes', 'Red Onion', 'Rajshahi Mangoes', 'Cold-Pressed Mustard Oil');

INSERT INTO listings (
  farmer_id, inventory_item_id, category_id, title, description, quantity_available,
  quantity_unit, price_per_unit, currency, harvest_date, quality_label,
  delivery_notes, status, closes_at
)
SELECT i.farmer_id, i.id, i.category_id, i.name,
       CONCAT(i.name, ' from a verified demo farm. Contact the farmer for a sample and delivery schedule.'),
       i.quantity, i.unit, prices.price, 'BDT', i.harvest_date, i.quality_label,
       i.notes, 'active', DATE_ADD(NOW(), INTERVAL 14 DAY)
FROM inventory_items i
JOIN (
  SELECT 'Premium Nazirshail Rice' name, 68000.00 price
  UNION ALL SELECT 'Winter Wheat', 31900.00
  UNION ALL SELECT 'Red Lentils', 76500.00
  UNION ALL SELECT 'Green Chili', 69500.00
  UNION ALL SELECT 'Dinajpur Winter Wheat', 32500.00
  UNION ALL SELECT 'Yellow Corn', 28500.00
  UNION ALL SELECT 'Mustard Seeds', 73500.00
  UNION ALL SELECT 'Fresh Potatoes', 24000.00
  UNION ALL SELECT 'Organic Potatoes', 25500.00
  UNION ALL SELECT 'Assorted Green Chili', 71000.00
  UNION ALL SELECT 'Split Red Lentils', 74800.00
  UNION ALL SELECT 'Red Onion', 38500.00
  UNION ALL SELECT 'Rajshahi Mangoes', 42000.00
  UNION ALL SELECT 'Cold-Pressed Mustard Oil', 58000.00
  UNION ALL SELECT 'White Onion', 36500.00
  UNION ALL SELECT 'Fresh Ginger', 88000.00
) prices ON prices.name = i.name
WHERE i.farmer_id IN (@farmer_01, @farmer_02, @farmer_03, @farmer_04)
  AND NOT EXISTS (SELECT 1 FROM listings existing WHERE existing.inventory_item_id = i.id);

UPDATE listings
SET image_path = CASE title
  WHEN 'Premium Nazirshail Rice' THEN 'assets/nazirshail rice.jpeg'
  WHEN 'Winter Wheat' THEN 'assets/winter wheat.jpeg'
  WHEN 'Red Lentils' THEN 'assets/red lentils.jpeg'
  WHEN 'Green Chili' THEN 'assets/chili.jpeg'
  WHEN 'Dinajpur Winter Wheat' THEN 'assets/Organic Soft White Wheat.jpeg'
  WHEN 'Yellow Corn' THEN 'assets/yellow corn.jpeg'
  WHEN 'Mustard Seeds' THEN 'assets/sunflower seeds.jpg'
  WHEN 'Fresh Potatoes' THEN 'assets/organic potatoes.jpeg'
  WHEN 'Organic Potatoes' THEN 'assets/organic potatoes.jpeg'
  WHEN 'Assorted Green Chili' THEN 'assets/chili.jpeg'
  WHEN 'Split Red Lentils' THEN 'assets/red lentils.jpeg'
  WHEN 'Red Onion' THEN 'assets/onions.webp'
  WHEN 'White Onion' THEN 'assets/onions.webp'
  WHEN 'Rajshahi Mangoes' THEN 'assets/banana.webp'
  WHEN 'Fresh Ginger' THEN 'assets/organic potatoes.jpeg'
  WHEN 'Red Carrots' THEN 'assets/organic potatoes.jpeg'
  WHEN 'Cold-Pressed Mustard Oil' THEN 'assets/sunflower seeds.jpg'
  ELSE image_path
END
WHERE farmer_id IN (@farmer_01, @farmer_02, @farmer_03, @farmer_04)
  AND image_path IS NULL;

UPDATE listings l
JOIN inventory_items i ON i.id = l.inventory_item_id
SET l.quantity_available = i.quantity,
    l.status = IF(i.quantity > 0, 'active', 'sold')
WHERE l.farmer_id IN (@farmer_01, @farmer_02, @farmer_03, @farmer_04);

INSERT INTO bids (listing_id, buyer_id, quantity, price_per_unit, delivery_date, message, status)
SELECT l.id, b.id, sample.quantity, sample.price, DATE_ADD(CURDATE(), INTERVAL sample.delivery_days DAY), sample.message, sample.status
FROM (
  SELECT 'Premium Nazirshail Rice' title, 'demo_buyer_01' buyer, 8.000 quantity, 67500.00 price, 7 delivery_days, 'Can collect in sealed bags next week.' message, 'pending' status
  UNION ALL SELECT 'Premium Nazirshail Rice', 'demo_buyer_02', 10.000, 68000.00, 10, 'Offer for a full truck load.', 'countered'
  UNION ALL SELECT 'Winter Wheat', 'demo_buyer_02', 5.000, 31500.00, 8, 'Please include a moisture report.', 'pending'
  UNION ALL SELECT 'Winter Wheat', 'demo_buyer_03', 6.000, 31900.00, 12, 'Milling contract, repeat orders possible.', 'pending'
  UNION ALL SELECT 'Red Lentils', 'demo_buyer_04', 4.000, 75000.00, 9, 'Interested in a graded sample first.', 'pending'
  UNION ALL SELECT 'Red Lentils', 'demo_buyer_01', 3.000, 76500.00, 6, 'Can arrange pickup from your warehouse.', 'countered'
  UNION ALL SELECT 'Green Chili', 'demo_buyer_03', 1.500, 68000.00, 3, 'Weekly restaurant supply inquiry.', 'pending'
  UNION ALL SELECT 'Green Chili', 'demo_buyer_05', 2.000, 69000.00, 5, 'Please confirm crate sizes.', 'pending'
  UNION ALL SELECT 'Dinajpur Winter Wheat', 'demo_buyer_02', 12.000, 32000.00, 11, 'Bulk order; delivery date flexible.', 'pending'
  UNION ALL SELECT 'Yellow Corn', 'demo_buyer_04', 8.000, 28000.00, 7, 'Feed production lot.', 'pending'
  UNION ALL SELECT 'Yellow Corn', 'demo_buyer_05', 5.000, 28500.00, 10, 'Need the quality report with shipment.', 'pending'
  UNION ALL SELECT 'Mustard Seeds', 'demo_buyer_01', 4.000, 72500.00, 9, 'Clean seed for oil pressing.', 'pending'
  UNION ALL SELECT 'Fresh Potatoes', 'demo_buyer_03', 10.000, 23500.00, 4, 'Please reserve table grade.', 'pending'
  UNION ALL SELECT 'Organic Potatoes', 'demo_buyer_05', 5.000, 25000.00, 3, 'Organic certification requested.', 'pending'
  UNION ALL SELECT 'Assorted Green Chili', 'demo_buyer_01', 2.000, 70000.00, 5, 'Trial order for wholesale market.', 'pending'
  UNION ALL SELECT 'Split Red Lentils', 'demo_buyer_04', 3.000, 74000.00, 8, 'Sample inspection before dispatch.', 'pending'
  UNION ALL SELECT 'Red Onion', 'demo_buyer_03', 6.000, 37500.00, 6, 'Weekly supply if quality is consistent.', 'pending'
  UNION ALL SELECT 'Rajshahi Mangoes', 'demo_buyer_05', 2.000, 41000.00, 3, 'Mixed ripeness is acceptable.', 'pending'
  UNION ALL SELECT 'Cold-Pressed Mustard Oil', 'demo_buyer_04', 1.000, 57000.00, 8, 'Need food-grade sealed containers.', 'pending'
  UNION ALL SELECT 'White Onion', 'demo_buyer_02', 5.000, 36000.00, 7, 'Export sort preferred.', 'pending'
  UNION ALL SELECT 'Fresh Ginger', 'demo_buyer_01', 1.000, 86000.00, 5, 'Washed and graded lot.', 'pending'
) sample
JOIN listings l ON l.title = sample.title AND l.status = 'active'
JOIN users b ON b.username = sample.buyer
WHERE NOT EXISTS (
  SELECT 1 FROM bids existing
  WHERE existing.listing_id = l.id AND existing.buyer_id = b.id
    AND existing.quantity = sample.quantity AND existing.price_per_unit = sample.price
    AND existing.parent_bid_id IS NULL
);

INSERT IGNORE INTO orders (
  order_number, buyer_id, farmer_id, listing_id, quantity, quantity_unit,
  price_per_unit, subtotal, commission_amount, total_amount, currency,
  status, payment_status, expected_delivery_at, created_at
)
SELECT sample.order_number, buyer.id, farmer.id, listing.id,
       sample.quantity, listing.quantity_unit, listing.price_per_unit,
       ROUND(sample.quantity * listing.price_per_unit, 2),
       ROUND(sample.quantity * listing.price_per_unit * 0.03, 2),
       ROUND(sample.quantity * listing.price_per_unit * 1.03, 2), 'BDT',
       sample.status, sample.payment_status,
       DATE_ADD(NOW(), INTERVAL sample.delivery_days DAY),
       DATE_SUB(NOW(), INTERVAL sample.age_days DAY)
FROM (
  SELECT 'DEMO-2026-1001' order_number, 'demo_buyer_01' buyer, 'demo_farmer_01' farmer, 'Premium Nazirshail Rice' title, 8.000 quantity, 'in_transit' status, 'escrowed' payment_status, 2 age_days, 2 delivery_days
  UNION ALL SELECT 'DEMO-2026-1002', 'demo_buyer_02', 'demo_farmer_01', 'Winter Wheat', 5.000, 'completed', 'released', 16, -9
  UNION ALL SELECT 'DEMO-2026-1003', 'demo_buyer_03', 'demo_farmer_02', 'Yellow Corn', 6.000, 'delivered', 'released', 11, -5
  UNION ALL SELECT 'DEMO-2026-1004', 'demo_buyer_04', 'demo_farmer_03', 'Organic Potatoes', 4.000, 'processing', 'pending', 3, 3
  UNION ALL SELECT 'DEMO-2026-1005', 'demo_buyer_01', 'demo_farmer_04', 'Rajshahi Mangoes', 2.000, 'confirmed', 'escrowed', 1, 4
  UNION ALL SELECT 'DEMO-2026-1006', 'demo_buyer_02', 'demo_farmer_02', 'Mustard Seeds', 3.000, 'pending', 'unpaid', 0, 8
  UNION ALL SELECT 'DEMO-2026-1007', 'demo_buyer_03', 'demo_farmer_03', 'Red Onion', 2.500, 'disputed', 'partially_refunded', 20, -12
  UNION ALL SELECT 'DEMO-2026-1008', 'demo_buyer_04', 'demo_farmer_04', 'Cold-Pressed Mustard Oil', 1.000, 'ready_for_pickup', 'escrowed', 2, 1
) sample
JOIN users buyer ON buyer.username = sample.buyer
JOIN users farmer ON farmer.username = sample.farmer
JOIN listings listing ON listing.farmer_id = farmer.id AND listing.title = sample.title;

INSERT IGNORE INTO invoices (
  invoice_number, order_id, issued_by, billed_to, subtotal,
  commission_amount, total_amount, currency, status, issued_at, due_at
)
SELECT CONCAT('DEMO-INV-', RIGHT(o.order_number, 4)), o.id, o.farmer_id,
       o.buyer_id, o.subtotal, o.commission_amount, o.total_amount, o.currency,
       CASE WHEN o.payment_status = 'released' THEN 'paid' ELSE 'issued' END,
       o.created_at, DATE_ADD(o.created_at, INTERVAL 14 DAY)
FROM orders o WHERE o.order_number LIKE 'DEMO-2026-%';

INSERT INTO order_status_history (order_id, changed_by, status, note, created_at)
SELECT o.id, o.buyer_id, o.status, CONCAT('Demo order status: ', o.status), o.created_at
FROM orders o
WHERE o.order_number LIKE 'DEMO-2026-%'
  AND NOT EXISTS (SELECT 1 FROM order_status_history h WHERE h.order_id = o.id AND h.status = o.status);

INSERT INTO delivery_jobs (order_id, delivery_user_id, pickup_address, dropoff_address, status, scheduled_at)
SELECT o.id,
       CASE WHEN o.status IN ('in_transit', 'delivered', 'completed', 'ready_for_pickup', 'processing', 'confirmed') THEN
         CASE RIGHT(o.order_number, 1) WHEN '1' THEN @driver_01 WHEN '2' THEN @driver_02 WHEN '3' THEN @driver_01 WHEN '4' THEN @driver_02 WHEN '5' THEN @driver_02 WHEN '6' THEN @driver_01 WHEN '7' THEN @driver_01 ELSE @driver_02 END
       ELSE NULL END,
       farmer.address, buyer.address,
       CASE o.status WHEN 'in_transit' THEN 'in_transit' WHEN 'delivered' THEN 'delivered' WHEN 'completed' THEN 'delivered' WHEN 'ready_for_pickup' THEN 'assigned' WHEN 'processing' THEN 'accepted' WHEN 'confirmed' THEN 'assigned' ELSE 'unassigned' END,
       o.expected_delivery_at
FROM orders o JOIN users farmer ON farmer.id = o.farmer_id JOIN users buyer ON buyer.id = o.buyer_id
WHERE o.order_number LIKE 'DEMO-2026-%'
  AND NOT EXISTS (SELECT 1 FROM delivery_jobs d WHERE d.order_id = o.id);

INSERT INTO conversations (order_id, created_by)
SELECT o.id, o.buyer_id FROM orders o
WHERE o.order_number LIKE 'DEMO-2026-%'
  AND NOT EXISTS (SELECT 1 FROM conversations c WHERE c.order_id = o.id);

INSERT IGNORE INTO conversation_participants (conversation_id, user_id)
SELECT c.id, participant.user_id
FROM conversations c
JOIN orders o ON o.id = c.order_id
JOIN delivery_jobs d ON d.order_id = o.id AND d.status IN ('assigned', 'accepted', 'picked_up', 'in_transit')
JOIN (
  SELECT id user_id FROM users WHERE username IN ('demo_buyer_01', 'demo_buyer_02', 'demo_buyer_03', 'demo_buyer_04', 'demo_farmer_01', 'demo_farmer_02', 'demo_farmer_03', 'demo_farmer_04', 'demo_driver_01', 'demo_driver_02')
) participant
WHERE o.order_number LIKE 'DEMO-2026-%'
  AND participant.user_id IN (o.buyer_id, o.farmer_id, d.delivery_user_id);

INSERT INTO messages (conversation_id, sender_id, body, created_at)
SELECT c.id, sender.id, sample.body, DATE_SUB(NOW(), INTERVAL sample.minutes_ago MINUTE)
FROM (
  SELECT 'DEMO-2026-1001' order_number, 'demo_buyer_01' sender, 'Could you confirm the pickup window for the rice shipment?' body, 80 minutes_ago
  UNION ALL SELECT 'DEMO-2026-1001', 'demo_farmer_01', 'Pickup is available from 9 AM. The sealed bags are ready.', 65
  UNION ALL SELECT 'DEMO-2026-1002', 'demo_buyer_02', 'The wheat arrived in good condition. Thank you.', 1440
  UNION ALL SELECT 'DEMO-2026-1002', 'demo_farmer_01', 'Thank you for confirming delivery.', 1380
  UNION ALL SELECT 'DEMO-2026-1003', 'demo_buyer_03', 'Please send the next corn availability date.', 360
  UNION ALL SELECT 'DEMO-2026-1003', 'demo_farmer_02', 'The next lot should be ready early next month.', 300
  UNION ALL SELECT 'DEMO-2026-1004', 'demo_buyer_04', 'Please keep the potato sacks dry during loading.', 180
  UNION ALL SELECT 'DEMO-2026-1004', 'demo_farmer_03', 'Noted. We will use the covered loading bay.', 150
  UNION ALL SELECT 'DEMO-2026-1005', 'demo_buyer_01', 'Can you share a sample photo before dispatch?', 90
  UNION ALL SELECT 'DEMO-2026-1005', 'demo_farmer_04', 'Yes, I will send the crate photos this afternoon.', 75
  UNION ALL SELECT 'DEMO-2026-1006', 'demo_buyer_02', 'I am checking the mustard seed invoice.', 40
  UNION ALL SELECT 'DEMO-2026-1006', 'demo_farmer_02', 'Let me know if you need the lab certificate.', 30
  UNION ALL SELECT 'DEMO-2026-1007', 'demo_buyer_03', 'Some of the onion bags were short on arrival.', 800
  UNION ALL SELECT 'DEMO-2026-1007', 'demo_farmer_03', 'I have asked support to review the delivery record.', 760
  UNION ALL SELECT 'DEMO-2026-1008', 'demo_buyer_04', 'Our store team can receive the oil tomorrow morning.', 20
  UNION ALL SELECT 'DEMO-2026-1008', 'demo_farmer_04', 'The containers are sealed and ready for handover.', 10
) sample
JOIN orders o ON o.order_number = sample.order_number
JOIN conversations c ON c.order_id = o.id
JOIN users sender ON sender.username = sample.sender
WHERE NOT EXISTS (SELECT 1 FROM messages existing WHERE existing.conversation_id = c.id AND existing.sender_id = sender.id AND existing.body = sample.body);

INSERT INTO payments (order_id, invoice_id, payer_id, payee_id, transaction_type, amount, currency, status, payment_method, reference, completed_at)
SELECT o.id, invoice.id, payer.id, payee.id, payment.transaction_type,
       payment.amount, 'BDT', payment.status, 'Demo bank transfer', payment.reference,
       CASE WHEN payment.status = 'completed' THEN DATE_SUB(NOW(), INTERVAL 1 DAY) ELSE NULL END
FROM (
  SELECT 'DEMO-2026-1001' order_number, 'demo_buyer_01' payer, 'demo_farmer_01' payee, 'escrow_fund' transaction_type, 544000.00 amount, 'completed' status, 'DEMO-PAY-1001-A' reference
  UNION ALL SELECT 'DEMO-2026-1001', 'demo_buyer_01', 'demo_farmer_01', 'commission', 16320.00, 'completed', 'DEMO-PAY-1001-B'
  UNION ALL SELECT 'DEMO-2026-1002', 'demo_buyer_02', 'demo_farmer_01', 'escrow_release', 164285.00, 'completed', 'DEMO-PAY-1002-A'
  UNION ALL SELECT 'DEMO-2026-1003', 'demo_buyer_03', 'demo_farmer_02', 'payout', 176130.00, 'completed', 'DEMO-PAY-1003-A'
  UNION ALL SELECT 'DEMO-2026-1004', 'demo_buyer_04', 'demo_farmer_03', 'escrow_fund', 105060.00, 'pending', 'DEMO-PAY-1004-A'
  UNION ALL SELECT 'DEMO-2026-1005', 'demo_buyer_01', 'demo_farmer_04', 'escrow_fund', 86520.00, 'authorized', 'DEMO-PAY-1005-A'
  UNION ALL SELECT 'DEMO-2026-1007', 'demo_buyer_03', 'demo_farmer_03', 'refund', 25000.00, 'completed', 'DEMO-PAY-1007-A'
  UNION ALL SELECT 'DEMO-2026-1008', 'demo_buyer_04', 'demo_farmer_04', 'escrow_fund', 59740.00, 'completed', 'DEMO-PAY-1008-A'
) payment
JOIN orders o ON o.order_number = payment.order_number
JOIN users payer ON payer.username = payment.payer
JOIN users payee ON payee.username = payment.payee
LEFT JOIN invoices invoice ON invoice.order_id = o.id
WHERE NOT EXISTS (SELECT 1 FROM payments existing WHERE existing.reference = payment.reference);

UPDATE payments
SET amount = CASE reference
  WHEN 'DEMO-PAY-1001-A' THEN 544000.00
  WHEN 'DEMO-PAY-1003-A' THEN 176130.00
  WHEN 'DEMO-PAY-1004-A' THEN 105060.00
END
WHERE reference IN ('DEMO-PAY-1001-A', 'DEMO-PAY-1003-A', 'DEMO-PAY-1004-A');

INSERT IGNORE INTO reviews (order_id, reviewer_id, reviewee_id, listing_id, review_type, rating, comments)
SELECT o.id, buyer.id, farmer.id, listing.id, sample.review_type, sample.rating, sample.comments
FROM (
  SELECT 'DEMO-2026-1002' order_number, 'farmer' review_type, 5 rating, 'On-time delivery and clean grain.' comments
  UNION ALL SELECT 'DEMO-2026-1002', 'product', 5, 'The wheat matched the listing quality.'
  UNION ALL SELECT 'DEMO-2026-1003', 'farmer', 4, 'Good communication and careful packing.'
  UNION ALL SELECT 'DEMO-2026-1003', 'product', 5, 'Corn was dry and consistent.'
) sample
JOIN orders o ON o.order_number = sample.order_number
JOIN users buyer ON buyer.id = o.buyer_id
JOIN users farmer ON farmer.id = o.farmer_id
JOIN listings listing ON listing.id = o.listing_id;

INSERT INTO disputes (order_id, opened_by, against_user_id, reason, description, status, resolution)
SELECT o.id, o.buyer_id, o.farmer_id, sample.reason, sample.description, sample.status, sample.resolution
FROM (
  SELECT 'DEMO-2026-1007' order_number, 'Short quantity' reason, 'Buyer reported two missing onion sacks.' description, 'under_review' status, NULL resolution
  UNION ALL SELECT 'DEMO-2026-1004', 'Delivery timing', 'Buyer asked to reschedule receiving slot.', 'open', NULL
  UNION ALL SELECT 'DEMO-2026-1002', 'Resolved packaging question', 'A question about the wheat sack labels was reviewed.', 'resolved', 'Labels matched the agreed order.'
) sample JOIN orders o ON o.order_number = sample.order_number
WHERE NOT EXISTS (SELECT 1 FROM disputes existing WHERE existing.order_id = o.id AND existing.reason = sample.reason);

INSERT INTO user_verifications (user_id, document_type, document_reference, status, review_notes, reviewed_at)
SELECT u.id, sample.document_type, sample.document_reference, sample.status, sample.review_notes,
       CASE WHEN sample.status = 'pending' THEN NULL ELSE DATE_SUB(NOW(), INTERVAL 5 DAY) END
FROM (
  SELECT 'demo_farmer_01' username, 'National ID' document_type, 'DEMO-DOC-F01-NID' document_reference, 'approved' status, 'Demo identity verified.' review_notes
  UNION ALL SELECT 'demo_farmer_01', 'Land record', 'DEMO-DOC-F01-LAND', 'approved', 'Demo farm record reviewed.'
  UNION ALL SELECT 'demo_farmer_02', 'National ID', 'DEMO-DOC-F02-NID', 'approved', 'Demo identity verified.'
  UNION ALL SELECT 'demo_farmer_02', 'Farm certificate', 'DEMO-DOC-F02-FARM', 'pending', NULL
  UNION ALL SELECT 'demo_farmer_03', 'National ID', 'DEMO-DOC-F03-NID', 'approved', 'Demo identity verified.'
  UNION ALL SELECT 'demo_farmer_03', 'Cooperative record', 'DEMO-DOC-F03-COOP', 'pending', NULL
  UNION ALL SELECT 'demo_farmer_04', 'National ID', 'DEMO-DOC-F04-NID', 'approved', 'Demo identity verified.'
  UNION ALL SELECT 'demo_buyer_01', 'Business certificate', 'DEMO-DOC-B01-BIZ', 'approved', 'Demo business reviewed.'
  UNION ALL SELECT 'demo_buyer_02', 'Business certificate', 'DEMO-DOC-B02-BIZ', 'pending', NULL
  UNION ALL SELECT 'demo_driver_01', 'Driving license', 'DEMO-DOC-D01-LIC', 'approved', 'Demo driver credential reviewed.'
) sample JOIN users u ON u.username = sample.username
WHERE NOT EXISTS (SELECT 1 FROM user_verifications existing WHERE existing.user_id = u.id AND existing.document_reference = sample.document_reference);

INSERT INTO harvest_events (farmer_id, crop_name, expected_harvest_date, expected_quantity, quantity_unit, status, notes)
SELECT u.id, sample.crop_name, sample.harvest_date, sample.quantity, sample.unit, sample.status, sample.notes
FROM (
  SELECT 'demo_farmer_01' username, 'Nazirshail Rice' crop_name, '2026-10-12' harvest_date, 18.000 quantity, 'MT' unit, 'ready' status, 'Harvest crew booked.' notes
  UNION ALL SELECT 'demo_farmer_01', 'Winter Wheat', '2026-11-02', 24.000, 'MT', 'planned', 'Expected after the next dry spell.'
  UNION ALL SELECT 'demo_farmer_01', 'Red Lentils', '2026-11-18', 9.000, 'MT', 'planned', 'Field inspection complete.'
  UNION ALL SELECT 'demo_farmer_02', 'Winter Wheat', '2026-10-18', 22.000, 'MT', 'ready', 'Dry storage is available.'
  UNION ALL SELECT 'demo_farmer_02', 'Yellow Corn', '2026-11-05', 16.000, 'MT', 'planned', 'Moisture test scheduled.'
  UNION ALL SELECT 'demo_farmer_02', 'Mustard Seeds', '2026-11-22', 8.000, 'MT', 'planned', 'Seed cleaning planned.'
  UNION ALL SELECT 'demo_farmer_03', 'Organic Potatoes', '2026-10-03', 10.000, 'MT', 'harvested', 'First field already harvested.'
  UNION ALL SELECT 'demo_farmer_03', 'Green Chili', '2026-10-20', 4.000, 'MT', 'planned', 'Harvest in two rounds.'
  UNION ALL SELECT 'demo_farmer_03', 'Red Onion', '2026-11-15', 7.000, 'MT', 'planned', 'Curing period included.'
  UNION ALL SELECT 'demo_farmer_04', 'Rajshahi Mangoes', '2026-10-02', 5.000, 'MT', 'harvested', 'Sorted into three size grades.'
  UNION ALL SELECT 'demo_farmer_04', 'Mustard Oil', '2026-10-11', 2.000, 'MT', 'ready', 'Bottling batch ready.'
  UNION ALL SELECT 'demo_farmer_04', 'Fresh Ginger', '2026-11-09', 3.000, 'MT', 'planned', 'Crates reserved.'
) sample JOIN users u ON u.username = sample.username
WHERE NOT EXISTS (SELECT 1 FROM harvest_events existing WHERE existing.farmer_id = u.id AND existing.crop_name = sample.crop_name AND existing.expected_harvest_date = sample.harvest_date);

INSERT INTO commission_rules (category_id, rate_percent, fixed_amount, currency, is_active)
SELECT sample.category_id, sample.rate_percent, sample.fixed_amount, 'BDT', TRUE
FROM (
  SELECT @grains category_id, 3.00 rate_percent, 0.00 fixed_amount
  UNION ALL SELECT @rice, 2.50, 0.00
  UNION ALL SELECT @wheat, 2.75, 0.00
  UNION ALL SELECT @vegetables, 3.25, 0.00
  UNION ALL SELECT @pulses, 3.00, 0.00
  UNION ALL SELECT @other, 3.50, 0.00
) sample
WHERE NOT EXISTS (SELECT 1 FROM commission_rules existing WHERE existing.category_id = sample.category_id AND existing.is_active = TRUE);

INSERT INTO admin_actions (action_type, target_type, target_id, title, details, priority, status, due_at)
SELECT 'review', sample.target_type, sample.target_id, sample.title, sample.details, sample.priority, sample.status, DATE_ADD(NOW(), INTERVAL sample.due_days DAY)
FROM (
  SELECT 'verification_review' action_type, 'user' target_type, @farmer_02 target_id, 'Review farmer certificate' title, 'Check the pending farm certificate for demo_farmer_02.' details, 'high' priority, 'open' status, 1 due_days
  UNION ALL SELECT 'dispute', 'order', (SELECT id FROM orders WHERE order_number = 'DEMO-2026-1007'), 'Review onion shortage dispute', 'Compare buyer notes with the delivery record.', 'urgent', 'in_progress', 0
  UNION ALL SELECT 'listing_review', 'listing', (SELECT id FROM listings WHERE title = 'Organic Potatoes' AND farmer_id = @farmer_03), 'Review organic listing documents', 'Confirm the demo organic verification documents.', 'normal', 'open', 3
  UNION ALL SELECT 'payment_audit', 'order', (SELECT id FROM orders WHERE order_number = 'DEMO-2026-1004'), 'Check pending order escrow', 'Review payment authorization for the demo order.', 'normal', 'open', 2
  UNION ALL SELECT 'verification', 'user', @buyer_02, 'Review buyer business certificate', 'Review the pending business certificate.', 'low', 'open', 6
  UNION ALL SELECT 'marketplace', 'listing', (SELECT id FROM listings WHERE title = 'Cold-Pressed Mustard Oil' AND farmer_id = @farmer_04), 'Check product category mapping', 'Confirm the listing is under the right marketplace category.', 'low', 'completed', -2
) sample
WHERE sample.target_id IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM admin_actions existing WHERE existing.title = sample.title);

INSERT INTO notifications (user_id, notification_type, title, body, target_url)
SELECT u.id, sample.notification_type, sample.title, sample.body, sample.target_url
FROM (
  SELECT 'demo_buyer_01' username, 'order' notification_type, 'Order DEMO-2026-1001 is in transit' title, 'Your rice order has been picked up.' body, '/buyer/orders.php' target_url
  UNION ALL SELECT 'demo_buyer_01', 'message', 'New message from Mymensingh Grain House', 'The farmer confirmed the pickup window.', '/buyer/chat.php'
  UNION ALL SELECT 'demo_buyer_02', 'invoice', 'Invoice for DEMO-2026-1002 is paid', 'Your wheat order receipt is ready.', '/buyer/invoices.php'
  UNION ALL SELECT 'demo_buyer_03', 'dispute', 'Support is reviewing your onion report', 'Your report is under review.', '/buyer/orders.php'
  UNION ALL SELECT 'demo_buyer_04', 'delivery', 'Mustard oil is ready for pickup', 'The farmer has prepared the sealed containers.', '/buyer/orders.php'
  UNION ALL SELECT 'demo_farmer_01', 'bid', 'New bid for Premium Nazirshail Rice', 'A buyer submitted a new offer.', '/farmer/bids.php'
  UNION ALL SELECT 'demo_farmer_01', 'order', 'New order DEMO-2026-1001', 'The buyer placed an order for rice.', '/farmer/sales-ledger.php'
  UNION ALL SELECT 'demo_farmer_02', 'verification', 'Farm certificate is pending review', 'Your demo certificate is in the verification queue.', '/farmer/profile/profile-self.php'
  UNION ALL SELECT 'demo_farmer_03', 'dispute', 'An order was reported for review', 'Check the onion delivery report.', '/farmer/sales-ledger.php'
  UNION ALL SELECT 'demo_farmer_04', 'order', 'Mango order confirmed', 'A buyer confirmed the mango order.', '/farmer/sales-ledger.php'
  UNION ALL SELECT 'demo_driver_01', 'delivery', 'New delivery job assigned', 'Rice shipment DEMO-2026-1001 is in transit.', '/delivery/jobs.php'
  UNION ALL SELECT 'demo_driver_02', 'delivery', 'New wheat delivery job', 'Wheat shipment DEMO-2026-1002 is complete.', '/delivery/jobs.php'
) sample JOIN users u ON u.username = sample.username
WHERE NOT EXISTS (SELECT 1 FROM notifications existing WHERE existing.user_id = u.id AND existing.notification_type = sample.notification_type AND existing.title = sample.title);

COMMIT;

SELECT 'demo users' AS sample_type, COUNT(*) AS total FROM users WHERE email LIKE 'demo.%@agromarketplace.test'
UNION ALL SELECT 'inventory items', COUNT(*) FROM inventory_items i JOIN users u ON u.id = i.farmer_id WHERE u.email LIKE 'demo.farmer.%@agromarketplace.test'
UNION ALL SELECT 'listings', COUNT(*) FROM listings l JOIN users u ON u.id = l.farmer_id WHERE u.email LIKE 'demo.farmer.%@agromarketplace.test'
UNION ALL SELECT 'bids', COUNT(*) FROM bids b JOIN users u ON u.id = b.buyer_id WHERE u.email LIKE 'demo.buyer.%@agromarketplace.test'
UNION ALL SELECT 'orders', COUNT(*) FROM orders WHERE order_number LIKE 'DEMO-2026-%';
