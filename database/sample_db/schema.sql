CREATE DATABASE IF NOT EXISTS agromarketplace
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_0900_ai_ci;

USE agromarketplace;

CREATE TABLE IF NOT EXISTS users (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  role ENUM('buyer', 'farmer', 'delivery', 'admin') NOT NULL,
  username VARCHAR(80) NOT NULL,
  password VARCHAR(255) NOT NULL,
  email VARCHAR(254) NOT NULL,
  fullname VARCHAR(150) DEFAULT NULL,
  phone VARCHAR(32) DEFAULT NULL,
  address TEXT DEFAULT NULL,
  profile_description TEXT DEFAULT NULL,
  market_preferences VARCHAR(255) DEFAULT NULL,
  business_name VARCHAR(180) DEFAULT NULL,
  preferred_buying_category VARCHAR(100) DEFAULT NULL,
  typical_order_volume VARCHAR(80) DEFAULT NULL,
  land_area VARCHAR(80) DEFAULT NULL,
  primary_crops TEXT DEFAULT NULL,
  verification_documents TEXT DEFAULT NULL,
  vehicle_capacity VARCHAR(100) DEFAULT NULL,
  vehicle_license_plate VARCHAR(32) DEFAULT NULL,
  account_status ENUM('pending', 'active', 'suspended', 'closed') NOT NULL DEFAULT 'active',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_users_username (username),
  UNIQUE KEY uq_users_email (email),
  KEY ix_users_role_status (role, account_status)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS categories (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(100) NOT NULL,
  description VARCHAR(500) DEFAULT NULL,
  is_active BOOLEAN NOT NULL DEFAULT TRUE,
  PRIMARY KEY (id),
  UNIQUE KEY uq_categories_name (name)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS inventory_items (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  farmer_id BIGINT UNSIGNED NOT NULL,
  category_id BIGINT UNSIGNED DEFAULT NULL,
  name VARCHAR(180) NOT NULL,
  quantity DECIMAL(14,3) NOT NULL DEFAULT 0,
  unit VARCHAR(20) NOT NULL DEFAULT 'MT',
  harvest_date DATE DEFAULT NULL,
  quality_label VARCHAR(180) DEFAULT NULL,
  notes TEXT DEFAULT NULL,
  status ENUM('available', 'reserved', 'depleted', 'archived') NOT NULL DEFAULT 'available',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY ix_inventory_farmer_status (farmer_id, status),
  KEY ix_inventory_category (category_id),
  CONSTRAINT fk_inventory_farmer FOREIGN KEY (farmer_id) REFERENCES users (id),
  CONSTRAINT fk_inventory_category FOREIGN KEY (category_id) REFERENCES categories (id) ON DELETE SET NULL,
  CONSTRAINT chk_inventory_quantity CHECK (quantity >= 0)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS listings (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  farmer_id BIGINT UNSIGNED NOT NULL,
  inventory_item_id BIGINT UNSIGNED DEFAULT NULL,
  category_id BIGINT UNSIGNED DEFAULT NULL,
  title VARCHAR(180) NOT NULL,
  image_path VARCHAR(500) DEFAULT NULL,
  description TEXT DEFAULT NULL,
  quantity_available DECIMAL(14,3) NOT NULL,
  quantity_unit VARCHAR(20) NOT NULL DEFAULT 'MT',
  price_per_unit DECIMAL(14,2) NOT NULL,
  currency CHAR(3) NOT NULL DEFAULT 'BDT',
  harvest_date DATE DEFAULT NULL,
  quality_label VARCHAR(180) DEFAULT NULL,
  delivery_notes TEXT DEFAULT NULL,
  status ENUM('draft', 'active', 'paused', 'sold', 'archived') NOT NULL DEFAULT 'draft',
  closes_at DATETIME DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY ix_listings_market (status, category_id, created_at),
  KEY ix_listings_farmer (farmer_id, status),
  CONSTRAINT fk_listings_farmer FOREIGN KEY (farmer_id) REFERENCES users (id),
  CONSTRAINT fk_listings_inventory FOREIGN KEY (inventory_item_id) REFERENCES inventory_items (id) ON DELETE SET NULL,
  CONSTRAINT fk_listings_category FOREIGN KEY (category_id) REFERENCES categories (id) ON DELETE SET NULL,
  CONSTRAINT chk_listings_quantity CHECK (quantity_available >= 0),
  CONSTRAINT chk_listings_price CHECK (price_per_unit >= 0)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS bids (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  listing_id BIGINT UNSIGNED NOT NULL,
  buyer_id BIGINT UNSIGNED NOT NULL,
  parent_bid_id BIGINT UNSIGNED DEFAULT NULL,
  quantity DECIMAL(14,3) NOT NULL,
  price_per_unit DECIMAL(14,2) NOT NULL,
  delivery_date DATE DEFAULT NULL,
  message TEXT DEFAULT NULL,
  status ENUM('pending', 'countered', 'accepted', 'rejected', 'withdrawn', 'expired') NOT NULL DEFAULT 'pending',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY ix_bids_listing_status (listing_id, status, created_at),
  KEY ix_bids_buyer_status (buyer_id, status),
  CONSTRAINT fk_bids_listing FOREIGN KEY (listing_id) REFERENCES listings (id),
  CONSTRAINT fk_bids_buyer FOREIGN KEY (buyer_id) REFERENCES users (id),
  CONSTRAINT fk_bids_parent FOREIGN KEY (parent_bid_id) REFERENCES bids (id) ON DELETE SET NULL,
  CONSTRAINT chk_bids_quantity CHECK (quantity > 0),
  CONSTRAINT chk_bids_price CHECK (price_per_unit >= 0)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS orders (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  order_number VARCHAR(40) NOT NULL,
  buyer_id BIGINT UNSIGNED NOT NULL,
  farmer_id BIGINT UNSIGNED NOT NULL,
  listing_id BIGINT UNSIGNED DEFAULT NULL,
  accepted_bid_id BIGINT UNSIGNED DEFAULT NULL,
  quantity DECIMAL(14,3) NOT NULL,
  quantity_unit VARCHAR(20) NOT NULL DEFAULT 'MT',
  price_per_unit DECIMAL(14,2) NOT NULL,
  subtotal DECIMAL(14,2) NOT NULL,
  commission_amount DECIMAL(14,2) NOT NULL DEFAULT 0,
  total_amount DECIMAL(14,2) NOT NULL,
  currency CHAR(3) NOT NULL DEFAULT 'BDT',
  status ENUM('pending', 'confirmed', 'processing', 'ready_for_pickup', 'in_transit', 'delivered', 'completed', 'cancelled', 'disputed') NOT NULL DEFAULT 'pending',
  payment_status ENUM('unpaid', 'pending', 'escrowed', 'partially_refunded', 'refunded', 'released') NOT NULL DEFAULT 'unpaid',
  expected_delivery_at DATETIME DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_orders_number (order_number),
  KEY ix_orders_buyer_status (buyer_id, status, created_at),
  KEY ix_orders_farmer_status (farmer_id, status, created_at),
  CONSTRAINT fk_orders_buyer FOREIGN KEY (buyer_id) REFERENCES users (id),
  CONSTRAINT fk_orders_farmer FOREIGN KEY (farmer_id) REFERENCES users (id),
  CONSTRAINT fk_orders_listing FOREIGN KEY (listing_id) REFERENCES listings (id) ON DELETE SET NULL,
  CONSTRAINT fk_orders_bid FOREIGN KEY (accepted_bid_id) REFERENCES bids (id) ON DELETE SET NULL,
  CONSTRAINT chk_orders_quantity CHECK (quantity > 0),
  CONSTRAINT chk_orders_amounts CHECK (subtotal >= 0 AND commission_amount >= 0 AND total_amount >= 0)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS order_status_history (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  order_id BIGINT UNSIGNED NOT NULL,
  changed_by BIGINT UNSIGNED DEFAULT NULL,
  status VARCHAR(40) NOT NULL,
  note TEXT DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY ix_order_history (order_id, created_at),
  CONSTRAINT fk_order_history_order FOREIGN KEY (order_id) REFERENCES orders (id) ON DELETE CASCADE,
  CONSTRAINT fk_order_history_user FOREIGN KEY (changed_by) REFERENCES users (id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS invoices (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  invoice_number VARCHAR(40) NOT NULL,
  order_id BIGINT UNSIGNED NOT NULL,
  issued_by BIGINT UNSIGNED DEFAULT NULL,
  billed_to BIGINT UNSIGNED NOT NULL,
  subtotal DECIMAL(14,2) NOT NULL,
  commission_amount DECIMAL(14,2) NOT NULL DEFAULT 0,
  total_amount DECIMAL(14,2) NOT NULL,
  currency CHAR(3) NOT NULL DEFAULT 'BDT',
  status ENUM('draft', 'issued', 'paid', 'void', 'overdue') NOT NULL DEFAULT 'issued',
  issued_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  due_at DATETIME DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_invoices_number (invoice_number),
  KEY ix_invoices_billed_status (billed_to, status),
  CONSTRAINT fk_invoices_order FOREIGN KEY (order_id) REFERENCES orders (id),
  CONSTRAINT fk_invoices_issuer FOREIGN KEY (issued_by) REFERENCES users (id) ON DELETE SET NULL,
  CONSTRAINT fk_invoices_billed_to FOREIGN KEY (billed_to) REFERENCES users (id),
  CONSTRAINT chk_invoices_amounts CHECK (subtotal >= 0 AND commission_amount >= 0 AND total_amount >= 0)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS payments (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  order_id BIGINT UNSIGNED NOT NULL,
  invoice_id BIGINT UNSIGNED DEFAULT NULL,
  payer_id BIGINT UNSIGNED DEFAULT NULL,
  payee_id BIGINT UNSIGNED DEFAULT NULL,
  transaction_type ENUM('escrow_fund', 'escrow_release', 'commission', 'refund', 'payout', 'adjustment') NOT NULL,
  amount DECIMAL(14,2) NOT NULL,
  currency CHAR(3) NOT NULL DEFAULT 'BDT',
  status ENUM('pending', 'authorized', 'completed', 'failed', 'cancelled') NOT NULL DEFAULT 'pending',
  payment_method VARCHAR(60) DEFAULT NULL,
  reference VARCHAR(120) DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  completed_at DATETIME DEFAULT NULL,
  PRIMARY KEY (id),
  KEY ix_payments_order (order_id, created_at),
  KEY ix_payments_payer (payer_id, status),
  KEY ix_payments_payee (payee_id, status),
  CONSTRAINT fk_payments_order FOREIGN KEY (order_id) REFERENCES orders (id),
  CONSTRAINT fk_payments_invoice FOREIGN KEY (invoice_id) REFERENCES invoices (id) ON DELETE SET NULL,
  CONSTRAINT fk_payments_payer FOREIGN KEY (payer_id) REFERENCES users (id) ON DELETE SET NULL,
  CONSTRAINT fk_payments_payee FOREIGN KEY (payee_id) REFERENCES users (id) ON DELETE SET NULL,
  CONSTRAINT chk_payments_amount CHECK (amount >= 0)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS delivery_jobs (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  order_id BIGINT UNSIGNED NOT NULL,
  delivery_user_id BIGINT UNSIGNED DEFAULT NULL,
  pickup_address TEXT DEFAULT NULL,
  dropoff_address TEXT DEFAULT NULL,
  status ENUM('unassigned', 'assigned', 'accepted', 'picked_up', 'in_transit', 'delivered', 'failed', 'cancelled') NOT NULL DEFAULT 'unassigned',
  commission_amount DECIMAL(14,2) NOT NULL DEFAULT 0,
  pickup_otp_hash VARCHAR(255) DEFAULT NULL,
  handover_otp_hash VARCHAR(255) DEFAULT NULL,
  scheduled_at DATETIME DEFAULT NULL,
  picked_up_at DATETIME DEFAULT NULL,
  delivered_at DATETIME DEFAULT NULL,
  delivery_notes TEXT DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY ix_delivery_jobs_driver_status (delivery_user_id, status),
  KEY ix_delivery_jobs_order (order_id, status),
  CONSTRAINT fk_delivery_jobs_order FOREIGN KEY (order_id) REFERENCES orders (id),
  CONSTRAINT fk_delivery_jobs_user FOREIGN KEY (delivery_user_id) REFERENCES users (id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS delivery_damage_reports (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  delivery_job_id BIGINT UNSIGNED NOT NULL,
  reported_by BIGINT UNSIGNED NOT NULL,
  damage_type VARCHAR(80) NOT NULL,
  affected_quantity VARCHAR(120) DEFAULT NULL,
  description TEXT NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY ix_delivery_damage_job (delivery_job_id, created_at),
  CONSTRAINT fk_delivery_damage_job FOREIGN KEY (delivery_job_id) REFERENCES delivery_jobs (id) ON DELETE CASCADE,
  CONSTRAINT fk_delivery_damage_reporter FOREIGN KEY (reported_by) REFERENCES users (id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS delivery_damage_photos (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  report_id BIGINT UNSIGNED NOT NULL,
  mime_type VARCHAR(80) NOT NULL,
  image_data LONGBLOB NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY ix_delivery_damage_photos_report (report_id),
  CONSTRAINT fk_delivery_damage_photos_report FOREIGN KEY (report_id) REFERENCES delivery_damage_reports (id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS conversations (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  order_id BIGINT UNSIGNED DEFAULT NULL,
  created_by BIGINT UNSIGNED DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY ix_conversations_order (order_id),
  CONSTRAINT fk_conversations_order FOREIGN KEY (order_id) REFERENCES orders (id) ON DELETE SET NULL,
  CONSTRAINT fk_conversations_creator FOREIGN KEY (created_by) REFERENCES users (id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS conversation_participants (
  conversation_id BIGINT UNSIGNED NOT NULL,
  user_id BIGINT UNSIGNED NOT NULL,
  joined_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  last_read_at DATETIME DEFAULT NULL,
  PRIMARY KEY (conversation_id, user_id),
  KEY ix_conversation_participants_user (user_id),
  CONSTRAINT fk_conversation_participants_conversation FOREIGN KEY (conversation_id) REFERENCES conversations (id) ON DELETE CASCADE,
  CONSTRAINT fk_conversation_participants_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS messages (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  conversation_id BIGINT UNSIGNED NOT NULL,
  sender_id BIGINT UNSIGNED NOT NULL,
  body TEXT NOT NULL,
  attachment_url VARCHAR(500) DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY ix_messages_conversation (conversation_id, created_at),
  CONSTRAINT fk_messages_conversation FOREIGN KEY (conversation_id) REFERENCES conversations (id) ON DELETE CASCADE,
  CONSTRAINT fk_messages_sender FOREIGN KEY (sender_id) REFERENCES users (id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS reviews (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  order_id BIGINT UNSIGNED NOT NULL,
  reviewer_id BIGINT UNSIGNED NOT NULL,
  reviewee_id BIGINT UNSIGNED NOT NULL,
  listing_id BIGINT UNSIGNED DEFAULT NULL,
  review_type ENUM('farmer', 'product', 'delivery') NOT NULL,
  rating TINYINT UNSIGNED NOT NULL,
  comments TEXT DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_reviews_order_target (order_id, reviewer_id, reviewee_id, review_type),
  KEY ix_reviews_reviewee (reviewee_id, review_type, created_at),
  CONSTRAINT fk_reviews_order FOREIGN KEY (order_id) REFERENCES orders (id),
  CONSTRAINT fk_reviews_reviewer FOREIGN KEY (reviewer_id) REFERENCES users (id),
  CONSTRAINT fk_reviews_reviewee FOREIGN KEY (reviewee_id) REFERENCES users (id),
  CONSTRAINT fk_reviews_listing FOREIGN KEY (listing_id) REFERENCES listings (id) ON DELETE SET NULL,
  CONSTRAINT chk_reviews_rating CHECK (rating BETWEEN 1 AND 5)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS disputes (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  order_id BIGINT UNSIGNED NOT NULL,
  opened_by BIGINT UNSIGNED NOT NULL,
  against_user_id BIGINT UNSIGNED DEFAULT NULL,
  assigned_admin_id BIGINT UNSIGNED DEFAULT NULL,
  reason VARCHAR(180) NOT NULL,
  description TEXT DEFAULT NULL,
  status ENUM('open', 'under_review', 'resolved', 'rejected', 'closed') NOT NULL DEFAULT 'open',
  resolution TEXT DEFAULT NULL,
  resolved_at DATETIME DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY ix_disputes_status (status, created_at),
  CONSTRAINT fk_disputes_order FOREIGN KEY (order_id) REFERENCES orders (id),
  CONSTRAINT fk_disputes_opened_by FOREIGN KEY (opened_by) REFERENCES users (id),
  CONSTRAINT fk_disputes_against FOREIGN KEY (against_user_id) REFERENCES users (id) ON DELETE SET NULL,
  CONSTRAINT fk_disputes_admin FOREIGN KEY (assigned_admin_id) REFERENCES users (id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS user_verifications (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id BIGINT UNSIGNED NOT NULL,
  document_type VARCHAR(100) NOT NULL,
  document_reference VARCHAR(500) DEFAULT NULL,
  status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
  reviewed_by BIGINT UNSIGNED DEFAULT NULL,
  review_notes TEXT DEFAULT NULL,
  submitted_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  reviewed_at DATETIME DEFAULT NULL,
  PRIMARY KEY (id),
  KEY ix_user_verifications_queue (status, submitted_at),
  CONSTRAINT fk_user_verifications_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
  CONSTRAINT fk_user_verifications_reviewer FOREIGN KEY (reviewed_by) REFERENCES users (id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS harvest_events (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  farmer_id BIGINT UNSIGNED NOT NULL,
  inventory_item_id BIGINT UNSIGNED DEFAULT NULL,
  crop_name VARCHAR(180) NOT NULL,
  expected_harvest_date DATE NOT NULL,
  expected_quantity DECIMAL(14,3) DEFAULT NULL,
  quantity_unit VARCHAR(20) NOT NULL DEFAULT 'MT',
  status ENUM('planned', 'ready', 'harvested', 'cancelled') NOT NULL DEFAULT 'planned',
  notes TEXT DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY ix_harvest_events_farmer_date (farmer_id, expected_harvest_date),
  CONSTRAINT fk_harvest_events_farmer FOREIGN KEY (farmer_id) REFERENCES users (id),
  CONSTRAINT fk_harvest_events_inventory FOREIGN KEY (inventory_item_id) REFERENCES inventory_items (id) ON DELETE SET NULL,
  CONSTRAINT chk_harvest_quantity CHECK (expected_quantity IS NULL OR expected_quantity >= 0)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS commission_rules (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  rule_type ENUM('sale', 'delivery') NOT NULL DEFAULT 'sale',
  category_id BIGINT UNSIGNED DEFAULT NULL,
  rate_percent DECIMAL(5,2) NOT NULL DEFAULT 0,
  fixed_amount DECIMAL(14,2) NOT NULL DEFAULT 0,
  currency CHAR(3) NOT NULL DEFAULT 'BDT',
  effective_from DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  effective_until DATETIME DEFAULT NULL,
  is_active BOOLEAN NOT NULL DEFAULT TRUE,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY ix_commission_rules_active (is_active, effective_from),
  CONSTRAINT fk_commission_rules_category FOREIGN KEY (category_id) REFERENCES categories (id) ON DELETE SET NULL,
  CONSTRAINT chk_commission_values CHECK (rate_percent >= 0 AND fixed_amount >= 0)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS admin_actions (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  assigned_admin_id BIGINT UNSIGNED DEFAULT NULL,
  action_type VARCHAR(80) NOT NULL,
  target_type VARCHAR(80) NOT NULL,
  target_id BIGINT UNSIGNED DEFAULT NULL,
  title VARCHAR(180) NOT NULL,
  details TEXT DEFAULT NULL,
  priority ENUM('low', 'normal', 'high', 'urgent') NOT NULL DEFAULT 'normal',
  status ENUM('open', 'in_progress', 'completed', 'dismissed') NOT NULL DEFAULT 'open',
  due_at DATETIME DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  completed_at DATETIME DEFAULT NULL,
  PRIMARY KEY (id),
  KEY ix_admin_actions_queue (status, priority, due_at),
  CONSTRAINT fk_admin_actions_admin FOREIGN KEY (assigned_admin_id) REFERENCES users (id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS notifications (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id BIGINT UNSIGNED NOT NULL,
  notification_type VARCHAR(80) NOT NULL,
  title VARCHAR(180) NOT NULL,
  body TEXT DEFAULT NULL,
  target_url VARCHAR(500) DEFAULT NULL,
  read_at DATETIME DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY ix_notifications_user_unread (user_id, read_at, created_at),
  CONSTRAINT fk_notifications_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT IGNORE INTO categories (name) VALUES
  ('Grains'),
  ('Rice'),
  ('Wheat'),
  ('Vegetables'),
  ('Pulse & Seeds'),
  ('Other');
