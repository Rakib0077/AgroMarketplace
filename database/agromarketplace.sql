-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Oct 03, 2026 at 08:17 PM
-- Server version: 8.4.11-0ubuntu0.26.04.1
-- PHP Version: 8.5.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `agromarketplace`
--

DROP DATABASE IF EXISTS `agromarketplace`;
CREATE DATABASE `agromarketplace`;
USE `agromarketplace`;

-- --------------------------------------------------------

--
-- Table structure for table `admin_actions`
--

CREATE TABLE `admin_actions` (
  `id` bigint UNSIGNED NOT NULL,
  `assigned_admin_id` bigint UNSIGNED DEFAULT NULL,
  `action_type` varchar(80) NOT NULL,
  `target_type` varchar(80) NOT NULL,
  `target_id` bigint UNSIGNED DEFAULT NULL,
  `title` varchar(180) NOT NULL,
  `details` text,
  `priority` enum('low','normal','high','urgent') NOT NULL DEFAULT 'normal',
  `status` enum('open','in_progress','completed','dismissed') NOT NULL DEFAULT 'open',
  `due_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `completed_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `admin_actions`
--

INSERT INTO `admin_actions` (`id`, `assigned_admin_id`, `action_type`, `target_type`, `target_id`, `title`, `details`, `priority`, `status`, `due_at`, `created_at`, `completed_at`) VALUES
(1, NULL, 'review', 'user', 2, 'Review farmer certificate', 'Check the pending farm certificate for demo_farmer_02.', 'high', 'open', '2026-10-05 02:16:36', '2026-10-03 20:16:36', NULL),
(2, NULL, 'review', 'order', 7, 'Review onion shortage dispute', 'Compare buyer notes with the delivery record.', 'urgent', 'in_progress', '2026-10-04 02:16:36', '2026-10-03 20:16:36', NULL),
(3, NULL, 'review', 'listing', 9, 'Review organic listing documents', 'Confirm the demo organic verification documents.', 'normal', 'open', '2026-10-07 02:16:36', '2026-10-03 20:16:36', NULL),
(4, NULL, 'review', 'order', 4, 'Check pending order escrow', 'Review payment authorization for the demo order.', 'normal', 'open', '2026-10-06 02:16:36', '2026-10-03 20:16:36', NULL),
(5, NULL, 'review', 'user', 6, 'Review buyer business certificate', 'Review the pending business certificate.', 'low', 'open', '2026-10-10 02:16:36', '2026-10-03 20:16:36', NULL),
(6, NULL, 'review', 'listing', 14, 'Check product category mapping', 'Confirm the listing is under the right marketplace category.', 'low', 'completed', '2026-10-02 02:16:36', '2026-10-03 20:16:36', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `bids`
--

CREATE TABLE `bids` (
  `id` bigint UNSIGNED NOT NULL,
  `listing_id` bigint UNSIGNED NOT NULL,
  `buyer_id` bigint UNSIGNED NOT NULL,
  `parent_bid_id` bigint UNSIGNED DEFAULT NULL,
  `quantity` decimal(14,3) NOT NULL,
  `price_per_unit` decimal(14,2) NOT NULL,
  `delivery_date` date DEFAULT NULL,
  `message` text,
  `status` enum('pending','countered','accepted','rejected','withdrawn','expired') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ;

--
-- Dumping data for table `bids`
--

INSERT INTO `bids` (`id`, `listing_id`, `buyer_id`, `parent_bid_id`, `quantity`, `price_per_unit`, `delivery_date`, `message`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 5, NULL, 8.000, 67500.00, '2026-10-11', 'Can collect in sealed bags next week.', 'pending', '2026-10-03 20:16:35', '2026-10-03 20:16:35'),
(2, 1, 6, NULL, 10.000, 68000.00, '2026-10-14', 'Offer for a full truck load.', 'countered', '2026-10-03 20:16:35', '2026-10-03 20:16:35'),
(3, 2, 6, NULL, 5.000, 31500.00, '2026-10-12', 'Please include a moisture report.', 'pending', '2026-10-03 20:16:35', '2026-10-03 20:16:35'),
(4, 2, 7, NULL, 6.000, 31900.00, '2026-10-16', 'Milling contract, repeat orders possible.', 'pending', '2026-10-03 20:16:35', '2026-10-03 20:16:35'),
(5, 3, 8, NULL, 4.000, 75000.00, '2026-10-13', 'Interested in a graded sample first.', 'pending', '2026-10-03 20:16:35', '2026-10-03 20:16:35'),
(6, 3, 5, NULL, 3.000, 76500.00, '2026-10-10', 'Can arrange pickup from your warehouse.', 'countered', '2026-10-03 20:16:35', '2026-10-03 20:16:35'),
(7, 4, 7, NULL, 1.500, 68000.00, '2026-10-07', 'Weekly restaurant supply inquiry.', 'pending', '2026-10-03 20:16:35', '2026-10-03 20:16:35'),
(8, 4, 9, NULL, 2.000, 69000.00, '2026-10-09', 'Please confirm crate sizes.', 'pending', '2026-10-03 20:16:35', '2026-10-03 20:16:35'),
(9, 5, 6, NULL, 12.000, 32000.00, '2026-10-15', 'Bulk order; delivery date flexible.', 'pending', '2026-10-03 20:16:35', '2026-10-03 20:16:35'),
(10, 6, 8, NULL, 8.000, 28000.00, '2026-10-11', 'Feed production lot.', 'pending', '2026-10-03 20:16:35', '2026-10-03 20:16:35'),
(11, 6, 9, NULL, 5.000, 28500.00, '2026-10-14', 'Need the quality report with shipment.', 'pending', '2026-10-03 20:16:35', '2026-10-03 20:16:35'),
(12, 7, 5, NULL, 4.000, 72500.00, '2026-10-13', 'Clean seed for oil pressing.', 'pending', '2026-10-03 20:16:35', '2026-10-03 20:16:35'),
(13, 8, 7, NULL, 10.000, 23500.00, '2026-10-08', 'Please reserve table grade.', 'pending', '2026-10-03 20:16:35', '2026-10-03 20:16:35'),
(14, 9, 9, NULL, 5.000, 25000.00, '2026-10-07', 'Organic certification requested.', 'pending', '2026-10-03 20:16:35', '2026-10-03 20:16:35'),
(15, 10, 5, NULL, 2.000, 70000.00, '2026-10-09', 'Trial order for wholesale market.', 'pending', '2026-10-03 20:16:35', '2026-10-03 20:16:35'),
(16, 11, 8, NULL, 3.000, 74000.00, '2026-10-12', 'Sample inspection before dispatch.', 'pending', '2026-10-03 20:16:35', '2026-10-03 20:16:35'),
(17, 12, 7, NULL, 6.000, 37500.00, '2026-10-10', 'Weekly supply if quality is consistent.', 'pending', '2026-10-03 20:16:35', '2026-10-03 20:16:35'),
(18, 13, 9, NULL, 2.000, 41000.00, '2026-10-07', 'Mixed ripeness is acceptable.', 'pending', '2026-10-03 20:16:35', '2026-10-03 20:16:35'),
(19, 14, 8, NULL, 1.000, 57000.00, '2026-10-12', 'Need food-grade sealed containers.', 'pending', '2026-10-03 20:16:35', '2026-10-03 20:16:35'),
(20, 15, 6, NULL, 5.000, 36000.00, '2026-10-11', 'Export sort preferred.', 'pending', '2026-10-03 20:16:35', '2026-10-03 20:16:35'),
(21, 16, 5, NULL, 1.000, 86000.00, '2026-10-09', 'Washed and graded lot.', 'pending', '2026-10-03 20:16:35', '2026-10-03 20:16:35');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` varchar(500) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `is_active`) VALUES
(1, 'Grains', NULL, 1),
(2, 'Rice', NULL, 1),
(3, 'Wheat', NULL, 1),
(4, 'Vegetables', NULL, 1),
(5, 'Pulse & Seeds', NULL, 1),
(6, 'Other', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `commission_rules`
--

CREATE TABLE `commission_rules` (
  `id` bigint UNSIGNED NOT NULL,
  `rule_type` enum('sale','delivery') NOT NULL DEFAULT 'sale',
  `category_id` bigint UNSIGNED DEFAULT NULL,
  `rate_percent` decimal(5,2) NOT NULL DEFAULT '0.00',
  `fixed_amount` decimal(14,2) NOT NULL DEFAULT '0.00',
  `currency` char(3) NOT NULL DEFAULT 'BDT',
  `effective_from` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `effective_until` datetime DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ;

--
-- Dumping data for table `commission_rules`
--

INSERT INTO `commission_rules` (`id`, `rule_type`, `category_id`, `rate_percent`, `fixed_amount`, `currency`, `effective_from`, `effective_until`, `is_active`, `created_at`) VALUES
(1, 'sale', 1, 3.00, 0.00, 'BDT', '2026-10-04 02:16:36', NULL, 1, '2026-10-03 20:16:36'),
(2, 'sale', 2, 2.50, 0.00, 'BDT', '2026-10-04 02:16:36', NULL, 1, '2026-10-03 20:16:36'),
(3, 'sale', 3, 2.75, 0.00, 'BDT', '2026-10-04 02:16:36', NULL, 1, '2026-10-03 20:16:36'),
(4, 'sale', 4, 3.25, 0.00, 'BDT', '2026-10-04 02:16:36', NULL, 1, '2026-10-03 20:16:36'),
(5, 'sale', 5, 3.00, 0.00, 'BDT', '2026-10-04 02:16:36', NULL, 1, '2026-10-03 20:16:36'),
(6, 'sale', 6, 3.50, 0.00, 'BDT', '2026-10-04 02:16:36', NULL, 1, '2026-10-03 20:16:36');

-- --------------------------------------------------------

--
-- Table structure for table `conversations`
--

CREATE TABLE `conversations` (
  `id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED DEFAULT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `conversations`
--

INSERT INTO `conversations` (`id`, `order_id`, `created_by`, `created_at`) VALUES
(1, 1, 5, '2026-10-03 20:16:36'),
(2, 2, 6, '2026-10-03 20:16:36'),
(3, 3, 7, '2026-10-03 20:16:36'),
(4, 4, 8, '2026-10-03 20:16:36'),
(5, 5, 5, '2026-10-03 20:16:36'),
(6, 6, 6, '2026-10-03 20:16:36'),
(7, 7, 7, '2026-10-03 20:16:36'),
(8, 8, 8, '2026-10-03 20:16:36');

-- --------------------------------------------------------

--
-- Table structure for table `conversation_participants`
--

CREATE TABLE `conversation_participants` (
  `conversation_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `joined_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_read_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `conversation_participants`
--

INSERT INTO `conversation_participants` (`conversation_id`, `user_id`, `joined_at`, `last_read_at`) VALUES
(1, 1, '2026-10-03 20:16:36', NULL),
(1, 5, '2026-10-03 20:16:36', NULL),
(1, 10, '2026-10-03 20:16:36', NULL),
(4, 3, '2026-10-03 20:16:36', NULL),
(4, 8, '2026-10-03 20:16:36', NULL),
(4, 11, '2026-10-03 20:16:36', NULL),
(5, 4, '2026-10-03 20:16:36', NULL),
(5, 5, '2026-10-03 20:16:36', NULL),
(5, 11, '2026-10-03 20:16:36', NULL),
(8, 4, '2026-10-03 20:16:36', NULL),
(8, 8, '2026-10-03 20:16:36', NULL),
(8, 11, '2026-10-03 20:16:36', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `delivery_damage_photos`
--

CREATE TABLE `delivery_damage_photos` (
  `id` bigint UNSIGNED NOT NULL,
  `report_id` bigint UNSIGNED NOT NULL,
  `mime_type` varchar(80) NOT NULL,
  `image_data` longblob NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `delivery_damage_reports`
--

CREATE TABLE `delivery_damage_reports` (
  `id` bigint UNSIGNED NOT NULL,
  `delivery_job_id` bigint UNSIGNED NOT NULL,
  `reported_by` bigint UNSIGNED NOT NULL,
  `damage_type` varchar(80) NOT NULL,
  `affected_quantity` varchar(120) DEFAULT NULL,
  `description` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `delivery_jobs`
--

CREATE TABLE `delivery_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED NOT NULL,
  `delivery_user_id` bigint UNSIGNED DEFAULT NULL,
  `pickup_address` text,
  `dropoff_address` text,
  `status` enum('unassigned','assigned','accepted','picked_up','in_transit','delivered','failed','cancelled') NOT NULL DEFAULT 'unassigned',
  `commission_amount` decimal(14,2) NOT NULL DEFAULT '0.00',
  `pickup_otp_hash` varchar(255) DEFAULT NULL,
  `handover_otp_hash` varchar(255) DEFAULT NULL,
  `scheduled_at` datetime DEFAULT NULL,
  `picked_up_at` datetime DEFAULT NULL,
  `delivered_at` datetime DEFAULT NULL,
  `delivery_notes` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `delivery_jobs`
--

INSERT INTO `delivery_jobs` (`id`, `order_id`, `delivery_user_id`, `pickup_address`, `dropoff_address`, `status`, `commission_amount`, `pickup_otp_hash`, `handover_otp_hash`, `scheduled_at`, `picked_up_at`, `delivered_at`, `delivery_notes`, `created_at`, `updated_at`) VALUES
(1, 1, 10, 'Mymensingh, Bangladesh', 'Dhaka, Bangladesh', 'in_transit', 0.00, NULL, NULL, '2026-10-06 02:16:35', NULL, NULL, NULL, '2026-10-03 20:16:36', '2026-10-03 20:16:36'),
(2, 2, 11, 'Mymensingh, Bangladesh', 'Gazipur, Bangladesh', 'delivered', 0.00, NULL, NULL, '2026-09-25 02:16:35', NULL, NULL, NULL, '2026-10-03 20:16:36', '2026-10-03 20:16:36'),
(3, 3, 10, 'Dinajpur, Bangladesh', 'Narayanganj, Bangladesh', 'delivered', 0.00, NULL, NULL, '2026-09-29 02:16:35', NULL, NULL, NULL, '2026-10-03 20:16:36', '2026-10-03 20:16:36'),
(4, 4, 11, 'Bogra, Bangladesh', 'Chattogram, Bangladesh', 'accepted', 0.00, NULL, NULL, '2026-10-07 02:16:35', NULL, NULL, NULL, '2026-10-03 20:16:36', '2026-10-03 20:16:36'),
(5, 5, 11, 'Rajshahi, Bangladesh', 'Dhaka, Bangladesh', 'assigned', 0.00, NULL, NULL, '2026-10-08 02:16:35', NULL, NULL, NULL, '2026-10-03 20:16:36', '2026-10-03 20:16:36'),
(6, 6, NULL, 'Dinajpur, Bangladesh', 'Gazipur, Bangladesh', 'unassigned', 0.00, NULL, NULL, '2026-10-12 02:16:35', NULL, NULL, NULL, '2026-10-03 20:16:36', '2026-10-03 20:16:36'),
(7, 7, NULL, 'Bogra, Bangladesh', 'Narayanganj, Bangladesh', 'unassigned', 0.00, NULL, NULL, '2026-09-22 02:16:35', NULL, NULL, NULL, '2026-10-03 20:16:36', '2026-10-03 20:16:36'),
(8, 8, 11, 'Rajshahi, Bangladesh', 'Chattogram, Bangladesh', 'assigned', 0.00, NULL, NULL, '2026-10-05 02:16:35', NULL, NULL, NULL, '2026-10-03 20:16:36', '2026-10-03 20:16:36');

-- --------------------------------------------------------

--
-- Table structure for table `disputes`
--

CREATE TABLE `disputes` (
  `id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED NOT NULL,
  `opened_by` bigint UNSIGNED NOT NULL,
  `against_user_id` bigint UNSIGNED DEFAULT NULL,
  `assigned_admin_id` bigint UNSIGNED DEFAULT NULL,
  `reason` varchar(180) NOT NULL,
  `description` text,
  `status` enum('open','under_review','resolved','rejected','closed') NOT NULL DEFAULT 'open',
  `resolution` text,
  `resolved_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `disputes`
--

INSERT INTO `disputes` (`id`, `order_id`, `opened_by`, `against_user_id`, `assigned_admin_id`, `reason`, `description`, `status`, `resolution`, `resolved_at`, `created_at`, `updated_at`) VALUES
(1, 7, 7, 3, NULL, 'Short quantity', 'Buyer reported two missing onion sacks.', 'under_review', NULL, NULL, '2026-10-03 20:16:36', '2026-10-03 20:16:36'),
(2, 4, 8, 3, NULL, 'Delivery timing', 'Buyer asked to reschedule receiving slot.', 'open', NULL, NULL, '2026-10-03 20:16:36', '2026-10-03 20:16:36'),
(3, 2, 6, 1, NULL, 'Resolved packaging question', 'A question about the wheat sack labels was reviewed.', 'resolved', 'Labels matched the agreed order.', NULL, '2026-10-03 20:16:36', '2026-10-03 20:16:36');

-- --------------------------------------------------------

--
-- Table structure for table `harvest_events`
--

CREATE TABLE `harvest_events` (
  `id` bigint UNSIGNED NOT NULL,
  `farmer_id` bigint UNSIGNED NOT NULL,
  `inventory_item_id` bigint UNSIGNED DEFAULT NULL,
  `crop_name` varchar(180) NOT NULL,
  `expected_harvest_date` date NOT NULL,
  `expected_quantity` decimal(14,3) DEFAULT NULL,
  `quantity_unit` varchar(20) NOT NULL DEFAULT 'MT',
  `status` enum('planned','ready','harvested','cancelled') NOT NULL DEFAULT 'planned',
  `notes` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ;

--
-- Dumping data for table `harvest_events`
--

INSERT INTO `harvest_events` (`id`, `farmer_id`, `inventory_item_id`, `crop_name`, `expected_harvest_date`, `expected_quantity`, `quantity_unit`, `status`, `notes`, `created_at`) VALUES
(1, 1, NULL, 'Nazirshail Rice', '2026-10-12', 18.000, 'MT', 'ready', 'Harvest crew booked.', '2026-10-03 20:16:36'),
(2, 1, NULL, 'Winter Wheat', '2026-11-02', 24.000, 'MT', 'planned', 'Expected after the next dry spell.', '2026-10-03 20:16:36'),
(3, 1, NULL, 'Red Lentils', '2026-11-18', 9.000, 'MT', 'planned', 'Field inspection complete.', '2026-10-03 20:16:36'),
(4, 2, NULL, 'Winter Wheat', '2026-10-18', 22.000, 'MT', 'ready', 'Dry storage is available.', '2026-10-03 20:16:36'),
(5, 2, NULL, 'Yellow Corn', '2026-11-05', 16.000, 'MT', 'planned', 'Moisture test scheduled.', '2026-10-03 20:16:36'),
(6, 2, NULL, 'Mustard Seeds', '2026-11-22', 8.000, 'MT', 'planned', 'Seed cleaning planned.', '2026-10-03 20:16:36'),
(7, 3, NULL, 'Organic Potatoes', '2026-10-03', 10.000, 'MT', 'harvested', 'First field already harvested.', '2026-10-03 20:16:36'),
(8, 3, NULL, 'Green Chili', '2026-10-20', 4.000, 'MT', 'planned', 'Harvest in two rounds.', '2026-10-03 20:16:36'),
(9, 3, NULL, 'Red Onion', '2026-11-15', 7.000, 'MT', 'planned', 'Curing period included.', '2026-10-03 20:16:36'),
(10, 4, NULL, 'Rajshahi Mangoes', '2026-10-02', 5.000, 'MT', 'harvested', 'Sorted into three size grades.', '2026-10-03 20:16:36'),
(11, 4, NULL, 'Mustard Oil', '2026-10-11', 2.000, 'MT', 'ready', 'Bottling batch ready.', '2026-10-03 20:16:36'),
(12, 4, NULL, 'Fresh Ginger', '2026-11-09', 3.000, 'MT', 'planned', 'Crates reserved.', '2026-10-03 20:16:36');

-- --------------------------------------------------------

--
-- Table structure for table `inventory_items`
--

CREATE TABLE `inventory_items` (
  `id` bigint UNSIGNED NOT NULL,
  `farmer_id` bigint UNSIGNED NOT NULL,
  `category_id` bigint UNSIGNED DEFAULT NULL,
  `name` varchar(180) NOT NULL,
  `quantity` decimal(14,3) NOT NULL DEFAULT '0.000',
  `unit` varchar(20) NOT NULL DEFAULT 'MT',
  `harvest_date` date DEFAULT NULL,
  `quality_label` varchar(180) DEFAULT NULL,
  `notes` text,
  `status` enum('available','reserved','depleted','archived') NOT NULL DEFAULT 'available',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ;

--
-- Dumping data for table `inventory_items`
--

INSERT INTO `inventory_items` (`id`, `farmer_id`, `category_id`, `name`, `quantity`, `unit`, `harvest_date`, `quality_label`, `notes`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 'Premium Nazirshail Rice', 42.000, 'MT', '2026-10-12', 'Grade A · low moisture', 'Sealed 50 kg bags; warehouse pickup available.', 'available', '2026-10-03 20:16:35', '2026-10-03 20:16:35'),
(2, 1, 3, 'Winter Wheat', 19.000, 'MT', '2026-10-20', 'Milling grade', 'Cleaned and ready for sampling.', 'available', '2026-10-03 20:16:35', '2026-10-03 20:16:35'),
(3, 1, 5, 'Red Lentils', 16.000, 'MT', '2026-10-08', 'Sortex cleaned', 'Stored in dry warehouse.', 'available', '2026-10-03 20:16:35', '2026-10-03 20:16:35'),
(4, 1, 4, 'Green Chili', 3.500, 'MT', '2026-10-05', 'Fresh harvest', 'Packed in ventilated crates.', 'available', '2026-10-03 20:16:35', '2026-10-03 20:16:35'),
(5, 2, 3, 'Dinajpur Winter Wheat', 38.000, 'MT', '2026-10-18', 'Grade A', 'Bulk loading by appointment.', 'available', '2026-10-03 20:16:35', '2026-10-03 20:16:35'),
(6, 2, 1, 'Yellow Corn', 16.000, 'MT', '2026-10-25', 'Feed grade', 'Moisture report available.', 'available', '2026-10-03 20:16:35', '2026-10-03 20:16:35'),
(7, 2, 6, 'Mustard Seeds', 9.000, 'MT', '2026-10-16', 'Clean whole seed', 'Jute sacks, 50 kg each.', 'available', '2026-10-03 20:16:35', '2026-10-03 20:16:35'),
(8, 2, 4, 'Fresh Potatoes', 30.000, 'MT', '2026-10-04', 'Table grade', 'Cooled storage, mixed sizes.', 'available', '2026-10-03 20:16:35', '2026-10-03 20:16:35'),
(9, 3, 4, 'Organic Potatoes', 14.000, 'MT', '2026-10-03', 'Organic · Grade A', 'Harvested this week.', 'available', '2026-10-03 20:16:35', '2026-10-03 20:16:35'),
(10, 3, 4, 'Assorted Green Chili', 7.000, 'MT', '2026-10-06', 'Fresh and sorted', 'Local varieties.', 'available', '2026-10-03 20:16:35', '2026-10-03 20:16:35'),
(11, 3, 5, 'Split Red Lentils', 9.000, 'MT', '2026-10-09', 'Food grade', 'Cleaned and bagged.', 'available', '2026-10-03 20:16:35', '2026-10-03 20:16:35'),
(12, 3, 4, 'Red Onion', 11.500, 'MT', '2026-10-14', 'Medium bulb', 'Dry skin, ventilated store.', 'available', '2026-10-03 20:16:35', '2026-10-03 20:16:35'),
(13, 4, 6, 'Rajshahi Mangoes', 6.000, 'MT', '2026-10-02', 'Premium table fruit', 'Mixed ripeness, hand sorted.', 'available', '2026-10-03 20:16:35', '2026-10-03 20:16:35'),
(14, 4, 6, 'Cold-Pressed Mustard Oil', 3.000, 'MT', '2026-10-11', 'Food grade', 'Sealed food-safe containers.', 'available', '2026-10-03 20:16:35', '2026-10-03 20:16:35'),
(15, 4, 4, 'White Onion', 11.000, 'MT', '2026-10-15', 'Export sort', 'Stored in dry ventilated room.', 'available', '2026-10-03 20:16:35', '2026-10-03 20:16:35'),
(16, 4, 6, 'Fresh Ginger', 5.000, 'MT', '2026-10-07', 'Washed and graded', 'Crates available for pickup.', 'available', '2026-10-03 20:16:35', '2026-10-03 20:16:35');

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `id` bigint UNSIGNED NOT NULL,
  `invoice_number` varchar(40) NOT NULL,
  `order_id` bigint UNSIGNED NOT NULL,
  `issued_by` bigint UNSIGNED DEFAULT NULL,
  `billed_to` bigint UNSIGNED NOT NULL,
  `subtotal` decimal(14,2) NOT NULL,
  `commission_amount` decimal(14,2) NOT NULL DEFAULT '0.00',
  `total_amount` decimal(14,2) NOT NULL,
  `currency` char(3) NOT NULL DEFAULT 'BDT',
  `status` enum('draft','issued','paid','void','overdue') NOT NULL DEFAULT 'issued',
  `issued_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `due_at` datetime DEFAULT NULL
) ;

--
-- Dumping data for table `invoices`
--

INSERT INTO `invoices` (`id`, `invoice_number`, `order_id`, `issued_by`, `billed_to`, `subtotal`, `commission_amount`, `total_amount`, `currency`, `status`, `issued_at`, `due_at`) VALUES
(1, 'DEMO-INV-1001', 1, 1, 5, 544000.00, 16320.00, 560320.00, 'BDT', 'issued', '2026-10-02 02:16:35', '2026-10-16 02:16:35'),
(2, 'DEMO-INV-1002', 2, 1, 6, 159500.00, 4785.00, 164285.00, 'BDT', 'paid', '2026-09-18 02:16:35', '2026-10-02 02:16:35'),
(3, 'DEMO-INV-1003', 3, 2, 7, 171000.00, 5130.00, 176130.00, 'BDT', 'paid', '2026-09-23 02:16:35', '2026-10-07 02:16:35'),
(4, 'DEMO-INV-1004', 4, 3, 8, 102000.00, 3060.00, 105060.00, 'BDT', 'issued', '2026-10-01 02:16:35', '2026-10-15 02:16:35'),
(5, 'DEMO-INV-1005', 5, 4, 5, 84000.00, 2520.00, 86520.00, 'BDT', 'issued', '2026-10-03 02:16:35', '2026-10-17 02:16:35'),
(6, 'DEMO-INV-1006', 6, 2, 6, 220500.00, 6615.00, 227115.00, 'BDT', 'issued', '2026-10-04 02:16:35', '2026-10-18 02:16:35'),
(7, 'DEMO-INV-1007', 7, 3, 7, 96250.00, 2887.50, 99137.50, 'BDT', 'issued', '2026-09-14 02:16:35', '2026-09-28 02:16:35'),
(8, 'DEMO-INV-1008', 8, 4, 8, 58000.00, 1740.00, 59740.00, 'BDT', 'issued', '2026-10-02 02:16:35', '2026-10-16 02:16:35');

-- --------------------------------------------------------

--
-- Table structure for table `listings`
--

CREATE TABLE `listings` (
  `id` bigint UNSIGNED NOT NULL,
  `farmer_id` bigint UNSIGNED NOT NULL,
  `inventory_item_id` bigint UNSIGNED DEFAULT NULL,
  `category_id` bigint UNSIGNED DEFAULT NULL,
  `title` varchar(180) NOT NULL,
  `image_path` varchar(500) DEFAULT NULL,
  `description` text,
  `quantity_available` decimal(14,3) NOT NULL,
  `quantity_unit` varchar(20) NOT NULL DEFAULT 'MT',
  `price_per_unit` decimal(14,2) NOT NULL,
  `currency` char(3) NOT NULL DEFAULT 'BDT',
  `harvest_date` date DEFAULT NULL,
  `quality_label` varchar(180) DEFAULT NULL,
  `delivery_notes` text,
  `status` enum('draft','active','paused','sold','archived') NOT NULL DEFAULT 'draft',
  `closes_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ;

--
-- Dumping data for table `listings`
--

INSERT INTO `listings` (`id`, `farmer_id`, `inventory_item_id`, `category_id`, `title`, `image_path`, `description`, `quantity_available`, `quantity_unit`, `price_per_unit`, `currency`, `harvest_date`, `quality_label`, `delivery_notes`, `status`, `closes_at`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 2, 'Premium Nazirshail Rice', 'assets/nazirshail rice.jpeg', 'Premium Nazirshail Rice from a verified demo farm. Contact the farmer for a sample and delivery schedule.', 42.000, 'MT', 68000.00, 'BDT', '2026-10-12', 'Grade A · low moisture', 'Sealed 50 kg bags; warehouse pickup available.', 'active', '2026-10-18 02:16:35', '2026-10-03 20:16:35', '2026-10-03 20:16:35'),
(2, 1, 2, 3, 'Winter Wheat', 'assets/winter wheat.jpeg', 'Winter Wheat from a verified demo farm. Contact the farmer for a sample and delivery schedule.', 19.000, 'MT', 31900.00, 'BDT', '2026-10-20', 'Milling grade', 'Cleaned and ready for sampling.', 'active', '2026-10-18 02:16:35', '2026-10-03 20:16:35', '2026-10-03 20:16:35'),
(3, 1, 3, 5, 'Red Lentils', 'assets/red lentils.jpeg', 'Red Lentils from a verified demo farm. Contact the farmer for a sample and delivery schedule.', 16.000, 'MT', 76500.00, 'BDT', '2026-10-08', 'Sortex cleaned', 'Stored in dry warehouse.', 'active', '2026-10-18 02:16:35', '2026-10-03 20:16:35', '2026-10-03 20:16:35'),
(4, 1, 4, 4, 'Green Chili', 'assets/chili.jpeg', 'Green Chili from a verified demo farm. Contact the farmer for a sample and delivery schedule.', 3.500, 'MT', 69500.00, 'BDT', '2026-10-05', 'Fresh harvest', 'Packed in ventilated crates.', 'active', '2026-10-18 02:16:35', '2026-10-03 20:16:35', '2026-10-03 20:16:35'),
(5, 2, 5, 3, 'Dinajpur Winter Wheat', 'assets/Organic Soft White Wheat.jpeg', 'Dinajpur Winter Wheat from a verified demo farm. Contact the farmer for a sample and delivery schedule.', 38.000, 'MT', 32500.00, 'BDT', '2026-10-18', 'Grade A', 'Bulk loading by appointment.', 'active', '2026-10-18 02:16:35', '2026-10-03 20:16:35', '2026-10-03 20:16:35'),
(6, 2, 6, 1, 'Yellow Corn', 'assets/yellow corn.jpeg', 'Yellow Corn from a verified demo farm. Contact the farmer for a sample and delivery schedule.', 16.000, 'MT', 28500.00, 'BDT', '2026-10-25', 'Feed grade', 'Moisture report available.', 'active', '2026-10-18 02:16:35', '2026-10-03 20:16:35', '2026-10-03 20:16:35'),
(7, 2, 7, 6, 'Mustard Seeds', 'assets/sunflower seeds.jpg', 'Mustard Seeds from a verified demo farm. Contact the farmer for a sample and delivery schedule.', 9.000, 'MT', 73500.00, 'BDT', '2026-10-16', 'Clean whole seed', 'Jute sacks, 50 kg each.', 'active', '2026-10-18 02:16:35', '2026-10-03 20:16:35', '2026-10-03 20:16:35'),
(8, 2, 8, 4, 'Fresh Potatoes', 'assets/organic potatoes.jpeg', 'Fresh Potatoes from a verified demo farm. Contact the farmer for a sample and delivery schedule.', 30.000, 'MT', 24000.00, 'BDT', '2026-10-04', 'Table grade', 'Cooled storage, mixed sizes.', 'active', '2026-10-18 02:16:35', '2026-10-03 20:16:35', '2026-10-03 20:16:35'),
(9, 3, 9, 4, 'Organic Potatoes', 'assets/organic potatoes.jpeg', 'Organic Potatoes from a verified demo farm. Contact the farmer for a sample and delivery schedule.', 14.000, 'MT', 25500.00, 'BDT', '2026-10-03', 'Organic · Grade A', 'Harvested this week.', 'active', '2026-10-18 02:16:35', '2026-10-03 20:16:35', '2026-10-03 20:16:35'),
(10, 3, 10, 4, 'Assorted Green Chili', 'assets/chili.jpeg', 'Assorted Green Chili from a verified demo farm. Contact the farmer for a sample and delivery schedule.', 7.000, 'MT', 71000.00, 'BDT', '2026-10-06', 'Fresh and sorted', 'Local varieties.', 'active', '2026-10-18 02:16:35', '2026-10-03 20:16:35', '2026-10-03 20:16:35'),
(11, 3, 11, 5, 'Split Red Lentils', 'assets/red lentils.jpeg', 'Split Red Lentils from a verified demo farm. Contact the farmer for a sample and delivery schedule.', 9.000, 'MT', 74800.00, 'BDT', '2026-10-09', 'Food grade', 'Cleaned and bagged.', 'active', '2026-10-18 02:16:35', '2026-10-03 20:16:35', '2026-10-03 20:16:35'),
(12, 3, 12, 4, 'Red Onion', 'assets/onions.webp', 'Red Onion from a verified demo farm. Contact the farmer for a sample and delivery schedule.', 11.500, 'MT', 38500.00, 'BDT', '2026-10-14', 'Medium bulb', 'Dry skin, ventilated store.', 'active', '2026-10-18 02:16:35', '2026-10-03 20:16:35', '2026-10-03 20:16:35'),
(13, 4, 13, 6, 'Rajshahi Mangoes', 'assets/banana.webp', 'Rajshahi Mangoes from a verified demo farm. Contact the farmer for a sample and delivery schedule.', 6.000, 'MT', 42000.00, 'BDT', '2026-10-02', 'Premium table fruit', 'Mixed ripeness, hand sorted.', 'active', '2026-10-18 02:16:35', '2026-10-03 20:16:35', '2026-10-03 20:16:35'),
(14, 4, 14, 6, 'Cold-Pressed Mustard Oil', 'assets/sunflower seeds.jpg', 'Cold-Pressed Mustard Oil from a verified demo farm. Contact the farmer for a sample and delivery schedule.', 3.000, 'MT', 58000.00, 'BDT', '2026-10-11', 'Food grade', 'Sealed food-safe containers.', 'active', '2026-10-18 02:16:35', '2026-10-03 20:16:35', '2026-10-03 20:16:35'),
(15, 4, 15, 4, 'White Onion', 'assets/onions.webp', 'White Onion from a verified demo farm. Contact the farmer for a sample and delivery schedule.', 11.000, 'MT', 36500.00, 'BDT', '2026-10-15', 'Export sort', 'Stored in dry ventilated room.', 'active', '2026-10-18 02:16:35', '2026-10-03 20:16:35', '2026-10-03 20:16:35'),
(16, 4, 16, 6, 'Fresh Ginger', 'assets/organic potatoes.jpeg', 'Fresh Ginger from a verified demo farm. Contact the farmer for a sample and delivery schedule.', 5.000, 'MT', 88000.00, 'BDT', '2026-10-07', 'Washed and graded', 'Crates available for pickup.', 'active', '2026-10-18 02:16:35', '2026-10-03 20:16:35', '2026-10-03 20:16:35');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` bigint UNSIGNED NOT NULL,
  `conversation_id` bigint UNSIGNED NOT NULL,
  `sender_id` bigint UNSIGNED NOT NULL,
  `body` text NOT NULL,
  `attachment_url` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `conversation_id`, `sender_id`, `body`, `attachment_url`, `created_at`) VALUES
(1, 1, 5, 'Could you confirm the pickup window for the rice shipment?', NULL, '2026-10-03 18:56:36'),
(2, 1, 1, 'Pickup is available from 9 AM. The sealed bags are ready.', NULL, '2026-10-03 19:11:36'),
(3, 2, 6, 'The wheat arrived in good condition. Thank you.', NULL, '2026-10-02 20:16:36'),
(4, 2, 1, 'Thank you for confirming delivery.', NULL, '2026-10-02 21:16:36'),
(5, 3, 7, 'Please send the next corn availability date.', NULL, '2026-10-03 14:16:36'),
(6, 3, 2, 'The next lot should be ready early next month.', NULL, '2026-10-03 15:16:36'),
(7, 4, 8, 'Please keep the potato sacks dry during loading.', NULL, '2026-10-03 17:16:36'),
(8, 4, 3, 'Noted. We will use the covered loading bay.', NULL, '2026-10-03 17:46:36'),
(9, 5, 5, 'Can you share a sample photo before dispatch?', NULL, '2026-10-03 18:46:36'),
(10, 5, 4, 'Yes, I will send the crate photos this afternoon.', NULL, '2026-10-03 19:01:36'),
(11, 6, 6, 'I am checking the mustard seed invoice.', NULL, '2026-10-03 19:36:36'),
(12, 6, 2, 'Let me know if you need the lab certificate.', NULL, '2026-10-03 19:46:36'),
(13, 7, 7, 'Some of the onion bags were short on arrival.', NULL, '2026-10-03 06:56:36'),
(14, 7, 3, 'I have asked support to review the delivery record.', NULL, '2026-10-03 07:36:36'),
(15, 8, 8, 'Our store team can receive the oil tomorrow morning.', NULL, '2026-10-03 19:56:36'),
(16, 8, 4, 'The containers are sealed and ready for handover.', NULL, '2026-10-03 20:06:36');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `notification_type` varchar(80) NOT NULL,
  `title` varchar(180) NOT NULL,
  `body` text,
  `target_url` varchar(500) DEFAULT NULL,
  `read_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `notification_type`, `title`, `body`, `target_url`, `read_at`, `created_at`) VALUES
(1, 5, 'order', 'Order DEMO-2026-1001 is in transit', 'Your rice order has been picked up.', '/buyer/orders.php', NULL, '2026-10-03 20:16:36'),
(2, 5, 'message', 'New message from Mymensingh Grain House', 'The farmer confirmed the pickup window.', '/buyer/chat.php', NULL, '2026-10-03 20:16:36'),
(3, 6, 'invoice', 'Invoice for DEMO-2026-1002 is paid', 'Your wheat order receipt is ready.', '/buyer/invoices.php', NULL, '2026-10-03 20:16:36'),
(4, 7, 'dispute', 'Support is reviewing your onion report', 'Your report is under review.', '/buyer/orders.php', NULL, '2026-10-03 20:16:36'),
(5, 8, 'delivery', 'Mustard oil is ready for pickup', 'The farmer has prepared the sealed containers.', '/buyer/orders.php', NULL, '2026-10-03 20:16:36'),
(6, 1, 'bid', 'New bid for Premium Nazirshail Rice', 'A buyer submitted a new offer.', '/farmer/bids.php', NULL, '2026-10-03 20:16:36'),
(7, 1, 'order', 'New order DEMO-2026-1001', 'The buyer placed an order for rice.', '/farmer/sales-ledger.php', NULL, '2026-10-03 20:16:36'),
(8, 2, 'verification', 'Farm certificate is pending review', 'Your demo certificate is in the verification queue.', '/farmer/profile/profile-self.php', NULL, '2026-10-03 20:16:36'),
(9, 3, 'dispute', 'An order was reported for review', 'Check the onion delivery report.', '/farmer/sales-ledger.php', NULL, '2026-10-03 20:16:36'),
(10, 4, 'order', 'Mango order confirmed', 'A buyer confirmed the mango order.', '/farmer/sales-ledger.php', NULL, '2026-10-03 20:16:36'),
(11, 10, 'delivery', 'New delivery job assigned', 'Rice shipment DEMO-2026-1001 is in transit.', '/delivery/jobs.php', NULL, '2026-10-03 20:16:36'),
(12, 11, 'delivery', 'New wheat delivery job', 'Wheat shipment DEMO-2026-1002 is complete.', '/delivery/jobs.php', NULL, '2026-10-03 20:16:36');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` bigint UNSIGNED NOT NULL,
  `order_number` varchar(40) NOT NULL,
  `buyer_id` bigint UNSIGNED NOT NULL,
  `farmer_id` bigint UNSIGNED NOT NULL,
  `listing_id` bigint UNSIGNED DEFAULT NULL,
  `accepted_bid_id` bigint UNSIGNED DEFAULT NULL,
  `quantity` decimal(14,3) NOT NULL,
  `quantity_unit` varchar(20) NOT NULL DEFAULT 'MT',
  `price_per_unit` decimal(14,2) NOT NULL,
  `subtotal` decimal(14,2) NOT NULL,
  `commission_amount` decimal(14,2) NOT NULL DEFAULT '0.00',
  `total_amount` decimal(14,2) NOT NULL,
  `currency` char(3) NOT NULL DEFAULT 'BDT',
  `status` enum('pending','confirmed','processing','ready_for_pickup','in_transit','delivered','completed','cancelled','disputed') NOT NULL DEFAULT 'pending',
  `payment_status` enum('unpaid','pending','escrowed','partially_refunded','refunded','released') NOT NULL DEFAULT 'unpaid',
  `expected_delivery_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `order_number`, `buyer_id`, `farmer_id`, `listing_id`, `accepted_bid_id`, `quantity`, `quantity_unit`, `price_per_unit`, `subtotal`, `commission_amount`, `total_amount`, `currency`, `status`, `payment_status`, `expected_delivery_at`, `created_at`, `updated_at`) VALUES
(1, 'DEMO-2026-1001', 5, 1, 1, NULL, 8.000, 'MT', 68000.00, 544000.00, 16320.00, 560320.00, 'BDT', 'in_transit', 'escrowed', '2026-10-06 02:16:35', '2026-10-01 20:16:35', '2026-10-03 20:16:35'),
(2, 'DEMO-2026-1002', 6, 1, 2, NULL, 5.000, 'MT', 31900.00, 159500.00, 4785.00, 164285.00, 'BDT', 'completed', 'released', '2026-09-25 02:16:35', '2026-09-17 20:16:35', '2026-10-03 20:16:35'),
(3, 'DEMO-2026-1003', 7, 2, 6, NULL, 6.000, 'MT', 28500.00, 171000.00, 5130.00, 176130.00, 'BDT', 'delivered', 'released', '2026-09-29 02:16:35', '2026-09-22 20:16:35', '2026-10-03 20:16:35'),
(4, 'DEMO-2026-1004', 8, 3, 9, NULL, 4.000, 'MT', 25500.00, 102000.00, 3060.00, 105060.00, 'BDT', 'processing', 'pending', '2026-10-07 02:16:35', '2026-09-30 20:16:35', '2026-10-03 20:16:35'),
(5, 'DEMO-2026-1005', 5, 4, 13, NULL, 2.000, 'MT', 42000.00, 84000.00, 2520.00, 86520.00, 'BDT', 'confirmed', 'escrowed', '2026-10-08 02:16:35', '2026-10-02 20:16:35', '2026-10-03 20:16:35'),
(6, 'DEMO-2026-1006', 6, 2, 7, NULL, 3.000, 'MT', 73500.00, 220500.00, 6615.00, 227115.00, 'BDT', 'pending', 'unpaid', '2026-10-12 02:16:35', '2026-10-03 20:16:35', '2026-10-03 20:16:35'),
(7, 'DEMO-2026-1007', 7, 3, 12, NULL, 2.500, 'MT', 38500.00, 96250.00, 2887.50, 99137.50, 'BDT', 'disputed', 'partially_refunded', '2026-09-22 02:16:35', '2026-09-13 20:16:35', '2026-10-03 20:16:35'),
(8, 'DEMO-2026-1008', 8, 4, 14, NULL, 1.000, 'MT', 58000.00, 58000.00, 1740.00, 59740.00, 'BDT', 'ready_for_pickup', 'escrowed', '2026-10-05 02:16:35', '2026-10-01 20:16:35', '2026-10-03 20:16:35');

-- --------------------------------------------------------

--
-- Table structure for table `order_status_history`
--

CREATE TABLE `order_status_history` (
  `id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED NOT NULL,
  `changed_by` bigint UNSIGNED DEFAULT NULL,
  `status` varchar(40) NOT NULL,
  `note` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `order_status_history`
--

INSERT INTO `order_status_history` (`id`, `order_id`, `changed_by`, `status`, `note`, `created_at`) VALUES
(1, 1, 5, 'in_transit', 'Demo order status: in_transit', '2026-10-01 20:16:35'),
(2, 2, 6, 'completed', 'Demo order status: completed', '2026-09-17 20:16:35'),
(3, 3, 7, 'delivered', 'Demo order status: delivered', '2026-09-22 20:16:35'),
(4, 4, 8, 'processing', 'Demo order status: processing', '2026-09-30 20:16:35'),
(5, 5, 5, 'confirmed', 'Demo order status: confirmed', '2026-10-02 20:16:35'),
(6, 6, 6, 'pending', 'Demo order status: pending', '2026-10-03 20:16:35'),
(7, 7, 7, 'disputed', 'Demo order status: disputed', '2026-09-13 20:16:35'),
(8, 8, 8, 'ready_for_pickup', 'Demo order status: ready_for_pickup', '2026-10-01 20:16:35');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED NOT NULL,
  `invoice_id` bigint UNSIGNED DEFAULT NULL,
  `payer_id` bigint UNSIGNED DEFAULT NULL,
  `payee_id` bigint UNSIGNED DEFAULT NULL,
  `transaction_type` enum('escrow_fund','escrow_release','commission','refund','payout','adjustment') NOT NULL,
  `amount` decimal(14,2) NOT NULL,
  `currency` char(3) NOT NULL DEFAULT 'BDT',
  `status` enum('pending','authorized','completed','failed','cancelled') NOT NULL DEFAULT 'pending',
  `payment_method` varchar(60) DEFAULT NULL,
  `reference` varchar(120) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `completed_at` datetime DEFAULT NULL
) ;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `order_id`, `invoice_id`, `payer_id`, `payee_id`, `transaction_type`, `amount`, `currency`, `status`, `payment_method`, `reference`, `created_at`, `completed_at`) VALUES
(1, 1, 1, 5, 1, 'escrow_fund', 544000.00, 'BDT', 'completed', 'Demo bank transfer', 'DEMO-PAY-1001-A', '2026-10-03 20:16:36', '2026-10-03 02:16:36'),
(2, 1, 1, 5, 1, 'commission', 16320.00, 'BDT', 'completed', 'Demo bank transfer', 'DEMO-PAY-1001-B', '2026-10-03 20:16:36', '2026-10-03 02:16:36'),
(3, 2, 2, 6, 1, 'escrow_release', 164285.00, 'BDT', 'completed', 'Demo bank transfer', 'DEMO-PAY-1002-A', '2026-10-03 20:16:36', '2026-10-03 02:16:36'),
(4, 3, 3, 7, 2, 'payout', 176130.00, 'BDT', 'completed', 'Demo bank transfer', 'DEMO-PAY-1003-A', '2026-10-03 20:16:36', '2026-10-03 02:16:36'),
(5, 4, 4, 8, 3, 'escrow_fund', 105060.00, 'BDT', 'pending', 'Demo bank transfer', 'DEMO-PAY-1004-A', '2026-10-03 20:16:36', NULL),
(6, 5, 5, 5, 4, 'escrow_fund', 86520.00, 'BDT', 'authorized', 'Demo bank transfer', 'DEMO-PAY-1005-A', '2026-10-03 20:16:36', NULL),
(7, 7, 7, 7, 3, 'refund', 25000.00, 'BDT', 'completed', 'Demo bank transfer', 'DEMO-PAY-1007-A', '2026-10-03 20:16:36', '2026-10-03 02:16:36'),
(8, 8, 8, 8, 4, 'escrow_fund', 59740.00, 'BDT', 'completed', 'Demo bank transfer', 'DEMO-PAY-1008-A', '2026-10-03 20:16:36', '2026-10-03 02:16:36');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED NOT NULL,
  `reviewer_id` bigint UNSIGNED NOT NULL,
  `reviewee_id` bigint UNSIGNED NOT NULL,
  `listing_id` bigint UNSIGNED DEFAULT NULL,
  `review_type` enum('farmer','product','delivery') NOT NULL,
  `rating` tinyint UNSIGNED NOT NULL,
  `comments` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `order_id`, `reviewer_id`, `reviewee_id`, `listing_id`, `review_type`, `rating`, `comments`, `created_at`) VALUES
(1, 2, 6, 1, 2, 'farmer', 5, 'On-time delivery and clean grain.', '2026-10-03 20:16:36'),
(2, 2, 6, 1, 2, 'product', 5, 'The wheat matched the listing quality.', '2026-10-03 20:16:36'),
(3, 3, 7, 2, 6, 'farmer', 4, 'Good communication and careful packing.', '2026-10-03 20:16:36'),
(4, 3, 7, 2, 6, 'product', 5, 'Corn was dry and consistent.', '2026-10-03 20:16:36');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `role` enum('buyer','farmer','delivery','admin') NOT NULL,
  `username` varchar(80) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(254) NOT NULL,
  `fullname` varchar(150) DEFAULT NULL,
  `phone` varchar(32) DEFAULT NULL,
  `address` text,
  `profile_description` text,
  `market_preferences` varchar(255) DEFAULT NULL,
  `business_name` varchar(180) DEFAULT NULL,
  `preferred_buying_category` varchar(100) DEFAULT NULL,
  `typical_order_volume` varchar(80) DEFAULT NULL,
  `land_area` varchar(80) DEFAULT NULL,
  `primary_crops` text,
  `verification_documents` text,
  `vehicle_capacity` varchar(100) DEFAULT NULL,
  `vehicle_license_plate` varchar(32) DEFAULT NULL,
  `account_status` enum('pending','active','suspended','closed') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `role`, `username`, `password`, `email`, `fullname`, `phone`, `address`, `profile_description`, `market_preferences`, `business_name`, `preferred_buying_category`, `typical_order_volume`, `land_area`, `primary_crops`, `verification_documents`, `vehicle_capacity`, `vehicle_license_plate`, `account_status`, `created_at`, `updated_at`) VALUES
(1, 'farmer', 'demo_farmer_01', '$2y$12$3MF0g68fEllTxYKoj8WtTO2e.Zb6U01Mb18vjUUvr4DNGfOsVPQIe', 'demo.farmer.01@agromarketplace.test', 'Rahim Uddin', '+8801700000101', 'Mymensingh, Bangladesh', 'Family farm supplying clean, graded rice and grains.', 'Seasonal direct contracts', 'Mymensingh Grain House', NULL, NULL, '18 acres', 'Rice, wheat, lentils', 'NID and land record', NULL, NULL, 'active', '2026-10-03 20:16:35', '2026-10-03 20:16:35'),
(2, 'farmer', 'demo_farmer_02', '$2y$12$3MF0g68fEllTxYKoj8WtTO2e.Zb6U01Mb18vjUUvr4DNGfOsVPQIe', 'demo.farmer.02@agromarketplace.test', 'Jamal Mia', '+8801700000102', 'Dinajpur, Bangladesh', 'Bulk grain grower with scheduled harvests.', 'Bulk lots and warehouse pickup', 'Dinajpur Harvest Co.', NULL, NULL, '24 acres', 'Wheat, corn, mustard', 'NID and farm certificate', NULL, NULL, 'active', '2026-10-03 20:16:35', '2026-10-03 20:16:35'),
(3, 'farmer', 'demo_farmer_03', '$2y$12$3MF0g68fEllTxYKoj8WtTO2e.Zb6U01Mb18vjUUvr4DNGfOsVPQIe', 'demo.farmer.03@agromarketplace.test', 'Aminul Sarker', '+8801700000103', 'Bogra, Bangladesh', 'Produce supplier focused on fresh vegetables and pulses.', 'Weekly wholesale supply', 'Bogra Fresh Fields', NULL, NULL, '12 acres', 'Potatoes, chili, lentils', 'NID and cooperative record', NULL, NULL, 'active', '2026-10-03 20:16:35', '2026-10-03 20:16:35'),
(4, 'farmer', 'demo_farmer_04', '$2y$12$3MF0g68fEllTxYKoj8WtTO2e.Zb6U01Mb18vjUUvr4DNGfOsVPQIe', 'demo.farmer.04@agromarketplace.test', 'Mizan Agro', '+8801700000104', 'Rajshahi, Bangladesh', 'Orchard and oilseed produce with harvest notes.', 'Regional produce contracts', 'Mizan Agro', NULL, NULL, '30 acres', 'Mango, mustard, onion, ginger', 'NID and business certificate', NULL, NULL, 'active', '2026-10-03 20:16:35', '2026-10-03 20:16:35'),
(5, 'buyer', 'demo_buyer_01', '$2y$12$3MF0g68fEllTxYKoj8WtTO2e.Zb6U01Mb18vjUUvr4DNGfOsVPQIe', 'demo.buyer.01@agromarketplace.test', 'Ayesha Rahman', '+8801700000201', 'Dhaka, Bangladesh', NULL, 'Direct sourcing and seasonal contracts', 'Dhaka Food Supply', 'Rice', '5 MT monthly', NULL, NULL, NULL, NULL, NULL, 'active', '2026-10-03 20:16:35', '2026-10-03 20:16:35'),
(6, 'buyer', 'demo_buyer_02', '$2y$12$3MF0g68fEllTxYKoj8WtTO2e.Zb6U01Mb18vjUUvr4DNGfOsVPQIe', 'demo.buyer.02@agromarketplace.test', 'Hasan Mahmud', '+8801700000202', 'Gazipur, Bangladesh', NULL, 'Graded milling wheat', 'North Star Mills', 'Wheat', '8 MT monthly', NULL, NULL, NULL, NULL, NULL, 'active', '2026-10-03 20:16:35', '2026-10-03 20:16:35'),
(7, 'buyer', 'demo_buyer_03', '$2y$12$3MF0g68fEllTxYKoj8WtTO2e.Zb6U01Mb18vjUUvr4DNGfOsVPQIe', 'demo.buyer.03@agromarketplace.test', 'Sadia Haque', '+8801700000203', 'Narayanganj, Bangladesh', NULL, 'Fresh weekly delivery', 'Green Basket Wholesale', 'Vegetables', '3 MT weekly', NULL, NULL, NULL, NULL, NULL, 'active', '2026-10-03 20:16:35', '2026-10-03 20:16:35'),
(8, 'buyer', 'demo_buyer_04', '$2y$12$3MF0g68fEllTxYKoj8WtTO2e.Zb6U01Mb18vjUUvr4DNGfOsVPQIe', 'demo.buyer.04@agromarketplace.test', 'Karim Traders', '+8801700000204', 'Chattogram, Bangladesh', NULL, 'Bulk produce with verified quality', 'Karim Commodity House', 'Pulse & Seeds', '10 MT monthly', NULL, NULL, NULL, NULL, NULL, 'active', '2026-10-03 20:16:35', '2026-10-03 20:16:35'),
(9, 'buyer', 'demo_buyer_05', '$2y$12$3MF0g68fEllTxYKoj8WtTO2e.Zb6U01Mb18vjUUvr4DNGfOsVPQIe', 'demo.buyer.05@agromarketplace.test', 'Nusrat Jahan', '+8801700000205', 'Sylhet, Bangladesh', NULL, 'Organic and export-quality lots', 'Sylhet Market Link', 'Other', '2 MT monthly', NULL, NULL, NULL, NULL, NULL, 'active', '2026-10-03 20:16:35', '2026-10-03 20:16:35'),
(10, 'delivery', 'demo_driver_01', '$2y$12$3MF0g68fEllTxYKoj8WtTO2e.Zb6U01Mb18vjUUvr4DNGfOsVPQIe', 'demo.driver.01@agromarketplace.test', 'Rashed Ahmed', '+8801700000301', 'Dhaka, Bangladesh', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '5 MT truck', 'DHA-TA-1101', 'active', '2026-10-03 20:16:35', '2026-10-03 20:16:35'),
(11, 'delivery', 'demo_driver_02', '$2y$12$3MF0g68fEllTxYKoj8WtTO2e.Zb6U01Mb18vjUUvr4DNGfOsVPQIe', 'demo.driver.02@agromarketplace.test', 'Farzana Akter', '+8801700000302', 'Rajshahi, Bangladesh', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '3 MT van', 'RAJ-VA-2202', 'active', '2026-10-03 20:16:35', '2026-10-03 20:16:35'),
(12, 'admin', 'demo_admin_01', '$2y$12$3MF0g68fEllTxYKoj8WtTO2e.Zb6U01Mb18vjUUvr4DNGfOsVPQIe', 'demo.admin.01@agromarketplace.test', 'Demo Administrator', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'active', '2026-10-03 20:16:35', '2026-10-03 20:16:35');

-- --------------------------------------------------------

--
-- Table structure for table `user_verifications`
--

CREATE TABLE `user_verifications` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `document_type` varchar(100) NOT NULL,
  `document_reference` varchar(500) DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `reviewed_by` bigint UNSIGNED DEFAULT NULL,
  `review_notes` text,
  `submitted_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `reviewed_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user_verifications`
--

INSERT INTO `user_verifications` (`id`, `user_id`, `document_type`, `document_reference`, `status`, `reviewed_by`, `review_notes`, `submitted_at`, `reviewed_at`) VALUES
(1, 1, 'National ID', 'DEMO-DOC-F01-NID', 'approved', NULL, 'Demo identity verified.', '2026-10-03 20:16:36', '2026-09-29 02:16:36'),
(2, 1, 'Land record', 'DEMO-DOC-F01-LAND', 'approved', NULL, 'Demo farm record reviewed.', '2026-10-03 20:16:36', '2026-09-29 02:16:36'),
(3, 2, 'National ID', 'DEMO-DOC-F02-NID', 'approved', NULL, 'Demo identity verified.', '2026-10-03 20:16:36', '2026-09-29 02:16:36'),
(4, 2, 'Farm certificate', 'DEMO-DOC-F02-FARM', 'pending', NULL, NULL, '2026-10-03 20:16:36', NULL),
(5, 3, 'National ID', 'DEMO-DOC-F03-NID', 'approved', NULL, 'Demo identity verified.', '2026-10-03 20:16:36', '2026-09-29 02:16:36'),
(6, 3, 'Cooperative record', 'DEMO-DOC-F03-COOP', 'pending', NULL, NULL, '2026-10-03 20:16:36', NULL),
(7, 4, 'National ID', 'DEMO-DOC-F04-NID', 'approved', NULL, 'Demo identity verified.', '2026-10-03 20:16:36', '2026-09-29 02:16:36'),
(8, 5, 'Business certificate', 'DEMO-DOC-B01-BIZ', 'approved', NULL, 'Demo business reviewed.', '2026-10-03 20:16:36', '2026-09-29 02:16:36'),
(9, 6, 'Business certificate', 'DEMO-DOC-B02-BIZ', 'pending', NULL, NULL, '2026-10-03 20:16:36', NULL),
(10, 10, 'Driving license', 'DEMO-DOC-D01-LIC', 'approved', NULL, 'Demo driver credential reviewed.', '2026-10-03 20:16:36', '2026-09-29 02:16:36');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_actions`
--
ALTER TABLE `admin_actions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ix_admin_actions_queue` (`status`,`priority`,`due_at`),
  ADD KEY `fk_admin_actions_admin` (`assigned_admin_id`);

--
-- Indexes for table `bids`
--
ALTER TABLE `bids`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ix_bids_listing_status` (`listing_id`,`status`,`created_at`),
  ADD KEY `ix_bids_buyer_status` (`buyer_id`,`status`),
  ADD KEY `fk_bids_parent` (`parent_bid_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_categories_name` (`name`);

--
-- Indexes for table `commission_rules`
--
ALTER TABLE `commission_rules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ix_commission_rules_active` (`is_active`,`effective_from`),
  ADD KEY `fk_commission_rules_category` (`category_id`);

--
-- Indexes for table `conversations`
--
ALTER TABLE `conversations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ix_conversations_order` (`order_id`),
  ADD KEY `fk_conversations_creator` (`created_by`);

--
-- Indexes for table `conversation_participants`
--
ALTER TABLE `conversation_participants`
  ADD PRIMARY KEY (`conversation_id`,`user_id`),
  ADD KEY `ix_conversation_participants_user` (`user_id`);

--
-- Indexes for table `delivery_damage_photos`
--
ALTER TABLE `delivery_damage_photos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ix_delivery_damage_photos_report` (`report_id`);

--
-- Indexes for table `delivery_damage_reports`
--
ALTER TABLE `delivery_damage_reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ix_delivery_damage_job` (`delivery_job_id`,`created_at`),
  ADD KEY `fk_delivery_damage_reporter` (`reported_by`);

--
-- Indexes for table `delivery_jobs`
--
ALTER TABLE `delivery_jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ix_delivery_jobs_driver_status` (`delivery_user_id`,`status`),
  ADD KEY `ix_delivery_jobs_order` (`order_id`,`status`);

--
-- Indexes for table `disputes`
--
ALTER TABLE `disputes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ix_disputes_status` (`status`,`created_at`),
  ADD KEY `fk_disputes_order` (`order_id`),
  ADD KEY `fk_disputes_opened_by` (`opened_by`),
  ADD KEY `fk_disputes_against` (`against_user_id`),
  ADD KEY `fk_disputes_admin` (`assigned_admin_id`);

--
-- Indexes for table `harvest_events`
--
ALTER TABLE `harvest_events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ix_harvest_events_farmer_date` (`farmer_id`,`expected_harvest_date`),
  ADD KEY `fk_harvest_events_inventory` (`inventory_item_id`);

--
-- Indexes for table `inventory_items`
--
ALTER TABLE `inventory_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ix_inventory_farmer_status` (`farmer_id`,`status`),
  ADD KEY `ix_inventory_category` (`category_id`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_invoices_number` (`invoice_number`),
  ADD KEY `ix_invoices_billed_status` (`billed_to`,`status`),
  ADD KEY `fk_invoices_order` (`order_id`),
  ADD KEY `fk_invoices_issuer` (`issued_by`);

--
-- Indexes for table `listings`
--
ALTER TABLE `listings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ix_listings_market` (`status`,`category_id`,`created_at`),
  ADD KEY `ix_listings_farmer` (`farmer_id`,`status`),
  ADD KEY `fk_listings_inventory` (`inventory_item_id`),
  ADD KEY `fk_listings_category` (`category_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ix_messages_conversation` (`conversation_id`,`created_at`),
  ADD KEY `fk_messages_sender` (`sender_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ix_notifications_user_unread` (`user_id`,`read_at`,`created_at`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_orders_number` (`order_number`),
  ADD KEY `ix_orders_buyer_status` (`buyer_id`,`status`,`created_at`),
  ADD KEY `ix_orders_farmer_status` (`farmer_id`,`status`,`created_at`),
  ADD KEY `fk_orders_listing` (`listing_id`),
  ADD KEY `fk_orders_bid` (`accepted_bid_id`);

--
-- Indexes for table `order_status_history`
--
ALTER TABLE `order_status_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ix_order_history` (`order_id`,`created_at`),
  ADD KEY `fk_order_history_user` (`changed_by`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ix_payments_order` (`order_id`,`created_at`),
  ADD KEY `ix_payments_payer` (`payer_id`,`status`),
  ADD KEY `ix_payments_payee` (`payee_id`,`status`),
  ADD KEY `fk_payments_invoice` (`invoice_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_reviews_order_target` (`order_id`,`reviewer_id`,`reviewee_id`,`review_type`),
  ADD KEY `ix_reviews_reviewee` (`reviewee_id`,`review_type`,`created_at`),
  ADD KEY `fk_reviews_reviewer` (`reviewer_id`),
  ADD KEY `fk_reviews_listing` (`listing_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_users_username` (`username`),
  ADD UNIQUE KEY `uq_users_email` (`email`),
  ADD KEY `ix_users_role_status` (`role`,`account_status`);

--
-- Indexes for table `user_verifications`
--
ALTER TABLE `user_verifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ix_user_verifications_queue` (`status`,`submitted_at`),
  ADD KEY `fk_user_verifications_user` (`user_id`),
  ADD KEY `fk_user_verifications_reviewer` (`reviewed_by`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_actions`
--
ALTER TABLE `admin_actions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `bids`
--
ALTER TABLE `bids`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `commission_rules`
--
ALTER TABLE `commission_rules`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `conversations`
--
ALTER TABLE `conversations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `delivery_damage_photos`
--
ALTER TABLE `delivery_damage_photos`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `delivery_damage_reports`
--
ALTER TABLE `delivery_damage_reports`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `delivery_jobs`
--
ALTER TABLE `delivery_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `disputes`
--
ALTER TABLE `disputes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `harvest_events`
--
ALTER TABLE `harvest_events`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory_items`
--
ALTER TABLE `inventory_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `listings`
--
ALTER TABLE `listings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_status_history`
--
ALTER TABLE `order_status_history`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `user_verifications`
--
ALTER TABLE `user_verifications`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin_actions`
--
ALTER TABLE `admin_actions`
  ADD CONSTRAINT `fk_admin_actions_admin` FOREIGN KEY (`assigned_admin_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `bids`
--
ALTER TABLE `bids`
  ADD CONSTRAINT `fk_bids_buyer` FOREIGN KEY (`buyer_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `fk_bids_listing` FOREIGN KEY (`listing_id`) REFERENCES `listings` (`id`),
  ADD CONSTRAINT `fk_bids_parent` FOREIGN KEY (`parent_bid_id`) REFERENCES `bids` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `commission_rules`
--
ALTER TABLE `commission_rules`
  ADD CONSTRAINT `fk_commission_rules_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `conversations`
--
ALTER TABLE `conversations`
  ADD CONSTRAINT `fk_conversations_creator` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_conversations_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `conversation_participants`
--
ALTER TABLE `conversation_participants`
  ADD CONSTRAINT `fk_conversation_participants_conversation` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_conversation_participants_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `delivery_damage_photos`
--
ALTER TABLE `delivery_damage_photos`
  ADD CONSTRAINT `fk_delivery_damage_photos_report` FOREIGN KEY (`report_id`) REFERENCES `delivery_damage_reports` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `delivery_damage_reports`
--
ALTER TABLE `delivery_damage_reports`
  ADD CONSTRAINT `fk_delivery_damage_job` FOREIGN KEY (`delivery_job_id`) REFERENCES `delivery_jobs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_delivery_damage_reporter` FOREIGN KEY (`reported_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `delivery_jobs`
--
ALTER TABLE `delivery_jobs`
  ADD CONSTRAINT `fk_delivery_jobs_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `fk_delivery_jobs_user` FOREIGN KEY (`delivery_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `disputes`
--
ALTER TABLE `disputes`
  ADD CONSTRAINT `fk_disputes_admin` FOREIGN KEY (`assigned_admin_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_disputes_against` FOREIGN KEY (`against_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_disputes_opened_by` FOREIGN KEY (`opened_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `fk_disputes_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`);

--
-- Constraints for table `harvest_events`
--
ALTER TABLE `harvest_events`
  ADD CONSTRAINT `fk_harvest_events_farmer` FOREIGN KEY (`farmer_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `fk_harvest_events_inventory` FOREIGN KEY (`inventory_item_id`) REFERENCES `inventory_items` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `inventory_items`
--
ALTER TABLE `inventory_items`
  ADD CONSTRAINT `fk_inventory_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_inventory_farmer` FOREIGN KEY (`farmer_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `fk_invoices_billed_to` FOREIGN KEY (`billed_to`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `fk_invoices_issuer` FOREIGN KEY (`issued_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_invoices_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`);

--
-- Constraints for table `listings`
--
ALTER TABLE `listings`
  ADD CONSTRAINT `fk_listings_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_listings_farmer` FOREIGN KEY (`farmer_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `fk_listings_inventory` FOREIGN KEY (`inventory_item_id`) REFERENCES `inventory_items` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `fk_messages_conversation` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_messages_sender` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `fk_notifications_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_bid` FOREIGN KEY (`accepted_bid_id`) REFERENCES `bids` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_orders_buyer` FOREIGN KEY (`buyer_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `fk_orders_farmer` FOREIGN KEY (`farmer_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `fk_orders_listing` FOREIGN KEY (`listing_id`) REFERENCES `listings` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `order_status_history`
--
ALTER TABLE `order_status_history`
  ADD CONSTRAINT `fk_order_history_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_order_history_user` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `fk_payments_invoice` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_payments_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `fk_payments_payee` FOREIGN KEY (`payee_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_payments_payer` FOREIGN KEY (`payer_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `fk_reviews_listing` FOREIGN KEY (`listing_id`) REFERENCES `listings` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_reviews_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `fk_reviews_reviewee` FOREIGN KEY (`reviewee_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `fk_reviews_reviewer` FOREIGN KEY (`reviewer_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `user_verifications`
--
ALTER TABLE `user_verifications`
  ADD CONSTRAINT `fk_user_verifications_reviewer` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_user_verifications_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
