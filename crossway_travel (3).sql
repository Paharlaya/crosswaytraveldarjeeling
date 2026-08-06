-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 31, 2026 at 03:37 PM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `crossway_travel`
--

-- --------------------------------------------------------

--
-- Table structure for table `about_settings`
--

CREATE TABLE `about_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `about_settings`
--

INSERT INTO `about_settings` (`id`, `setting_key`, `setting_value`, `description`, `updated_at`) VALUES
(1, 'mission', 'To provide authentic, safe, and memorable travel experiences that connect people with the natural wonders and cultural heritage of the Himalayas.', 'Mission Statement', '2026-07-31 06:53:12'),
(2, 'vision', 'To become the most trusted and preferred travel partner in the region, known for quality service and genuine hospitality.', 'Vision Statement', '2026-07-31 06:53:12'),
(3, 'values', 'Integrity, Customer First, Quality Service, Innovation, Sustainability', 'Core Values', '2026-07-31 06:53:12'),
(4, 'story_title', 'Our Story', 'Story Section Title', '2026-07-31 06:53:12'),
(5, 'story_content', 'Founded with a passion for travel and a deep love for the Himalayas, CrossWay Darjeeling Travel has been creating unforgettable journeys since 2010. Our team of local experts knows every hidden gem, every scenic viewpoint, and every cultural experience that makes this region truly special.', 'Story Content', '2026-07-31 06:53:12'),
(6, 'team_title', 'Meet Our Team', 'Team Section Title', '2026-07-31 06:53:12'),
(7, 'team_description', 'We are a team of passionate travel experts dedicated to making your Himalayan journey unforgettable.', 'Team Description', '2026-07-31 06:53:12'),
(8, 'why_choose_us', 'Local expertise and insider knowledge, Customized itineraries tailored to your preferences, Premium fleet of vehicles for comfortable travel, 24/7 customer support throughout your journey, Competitive prices without compromising quality', 'Why Choose Us - Comma separated', '2026-07-31 06:53:12'),
(9, 'stats_customers', '5000+', 'Happy Customers Stat', '2026-07-31 06:53:12'),
(10, 'stats_packages', '100+', 'Tour Packages Stat', '2026-07-31 06:53:12'),
(11, 'stats_destinations', '50+', 'Destinations Stat', '2026-07-31 06:53:12'),
(12, 'stats_experience', '10+', 'Years Experience Stat', '2026-07-31 06:53:12');

-- --------------------------------------------------------

--
-- Table structure for table `about_team`
--

CREATE TABLE `about_team` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `position` varchar(100) NOT NULL,
  `bio` text DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `about_team`
--

INSERT INTO `about_team` (`id`, `name`, `position`, `bio`, `image_path`, `sort_order`, `active`, `created_at`, `updated_at`) VALUES
(1, 'Rajesh Gurung', 'Founder & CEO', 'With over 15 years of experience in the travel industry, Rajesh founded CrossWay Travel with a vision to showcase the beauty of the Himalayas to the world.', NULL, 1, 1, '2026-07-31 06:53:12', '2026-07-31 06:53:12'),
(2, 'Priya Sharma', 'Operations Manager', 'Priya ensures every journey is perfectly planned and executed, handling all logistics and customer relations with utmost care.', NULL, 2, 1, '2026-07-31 06:53:12', '2026-07-31 06:53:12'),
(3, 'Suresh Tamang', 'Senior Tour Guide', 'A local expert with deep knowledge of Darjeeling and Sikkim, Suresh has been guiding travelers through the Himalayas for over 10 years.', NULL, 3, 1, '2026-07-31 06:53:12', '2026-07-31 06:53:12'),
(4, 'Anita Rai', 'Customer Relations', 'Anita is dedicated to ensuring every traveler feels welcomed and supported throughout their journey with CrossWay Travel.', NULL, 4, 1, '2026-07-31 06:53:12', '2026-07-31 06:53:12');

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

CREATE TABLE `contacts` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('unread','read','replied') DEFAULT 'unread'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contacts`
--

INSERT INTO `contacts` (`id`, `name`, `email`, `phone`, `subject`, `message`, `submitted_at`, `status`) VALUES
(1, '1', '2@k.m', '1122', '1111112', '2222222', '2026-07-30 12:40:15', 'replied'),
(2, '1', '2@k.m', '1122', '1111112', '2222222', '2026-07-30 12:41:57', 'replied');

-- --------------------------------------------------------

--
-- Table structure for table `gallery`
--

CREATE TABLE `gallery` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image_path` varchar(255) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `upload_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `sort_order` int(11) DEFAULT 0,
  `active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gallery`
--

INSERT INTO `gallery` (`id`, `title`, `description`, `image_path`, `category`, `upload_date`, `sort_order`, `active`) VALUES
(1, 'Tea Garden', 'Tea Garden', 'images/1.jpeg', 'Tea Gardena', '2026-07-24 05:34:59', 0, 1),
(5, 'Lamahatta Eco Park', 'Lamahatta Eco Park', 'images/gallery/1785414491_2.jpeg', 'Lamahatta Eco Park', '2026-07-30 12:28:11', 1, 1),
(6, 'Zoological Park', 'Zoological Park', 'images/gallery/1785414733_3.jpeg', 'Zoological Park', '2026-07-30 12:32:13', 2, 1),
(7, 'GHoom Monastery', 'GHoom Monastery', 'images/gallery/1785414911_4.jpeg', 'GHoom Monastery', '2026-07-30 12:35:11', 3, 1),
(8, 'Batasia Loop', 'Batasia Loop', 'images/gallery/1785415062_14.jpeg', 'Batasia Loop', '2026-07-30 12:37:42', 4, 1),
(9, 'Rock Garden', 'Rock Garden', 'images/gallery/1785415461_7.jpeg', 'Rock Garden', '2026-07-30 12:44:21', 5, 1);

-- --------------------------------------------------------

--
-- Table structure for table `packages`
--

CREATE TABLE `packages` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `duration` varchar(50) NOT NULL,
  `destination` varchar(255) NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `inclusions` text DEFAULT NULL,
  `exclusions` text DEFAULT NULL,
  `availability` tinyint(1) DEFAULT 1,
  `featured` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `packages`
--

INSERT INTO `packages` (`id`, `name`, `description`, `price`, `duration`, `destination`, `image_path`, `inclusions`, `exclusions`, `availability`, `featured`, `created_at`, `updated_at`) VALUES
(1, 'Crossway Package', 'Experience the city with our package', '1299.00', '5 Days', 'Darjeeling', NULL, 'Hotel, Breakfast, City Tour, Seine River Cruise', 'Flight, Insurance', 1, 1, '2026-07-24 05:34:59', '2026-07-30 12:55:22'),
(5, 'Darjeeling Packages', 'Darjeeling', '122222.00', '3', 'Darjeeling', '', '', '', 1, 0, '2026-07-30 13:00:02', '2026-07-30 13:00:02'),
(7, 'Darjeeling Packages', 'a famous hill station in West Bengal, India, known for Tiger Hill, the Darjeeling Himalayan Railway (Toy Train), and world-class tea. Located at an elevation of 2,045 meters (6,709 feet) in the Himalayas, it offers grand views of Mount Kanchenjunga.', '1234.00', '5', 'Darjeeling', 'images/packages/1785417027_6a6b4d432f350.jpeg', '', '', 1, 1, '2026-07-30 13:10:27', '2026-07-31 05:49:49');

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

CREATE TABLE `pages` (
  `id` int(11) NOT NULL,
  `page_name` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `meta_description` varchar(255) DEFAULT NULL,
  `meta_keywords` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pages`
--

INSERT INTO `pages` (`id`, `page_name`, `title`, `content`, `meta_description`, `meta_keywords`, `updated_at`) VALUES
(1, 'home', 'Welcome to CrossWay Travel', '<h1>Explore the World with CrossWay Travel</h1><p>Your journey begins here. We offer unforgettable travel experiences to the most beautiful destinations around the globe.</p>', NULL, NULL, '2026-07-24 05:34:59'),
(2, 'about', 'About CrossWay Travel', '<h2>Welcome to CrossWay Darjeeling Travel</h2>\n<p>Your trusted partner for unforgettable Himalayan journeys. Based in the picturesque hill station of Darjeeling, West Bengal, we specialize in crafting exceptional travel experiences across the Eastern Himalayas.</p>\n<h3>Our Story</h3>\n<p>Founded with a passion for travel and a deep love for the Himalayas, CrossWay Darjeeling Travel has been creating unforgettable journeys since 2010. Our team of local experts knows every hidden gem, every scenic viewpoint, and every cultural experience that makes this region truly special.</p>\n<h3>Why Choose Us</h3>\n<ul>\n<li>Local expertise and insider knowledge</li>\n<li>Customized itineraries tailored to your preferences</li>\n<li>Premium fleet of vehicles for comfortable travel</li>\n<li>24/7 customer support throughout your journey</li>\n<li>Competitive prices without compromising quality</li>\n</ul>', 'About CrossWay Darjeeling Travel - Your trusted travel partner for Darjeeling, Sikkim, and Himalayan tours. Professional travel services with local expertise.', 'about us, darjeeling travel, sikkim tours, himalayan travel, crossway travel', '2026-07-31 06:52:29'),
(3, 'gallery', 'Travel Gallery', '<h1>Our Travel Gallery</h1><p>Explore our collection of travel moments captured by our clients and team members.</p>', NULL, NULL, '2026-07-24 05:34:59'),
(4, 'packages', 'Travel Packages', '<h1>Our Travel Packages</h1><p>Discover our curated travel packages designed to give you the best value and unforgettable experiences.</p>', NULL, NULL, '2026-07-24 05:34:59'),
(5, 'contact', 'Contact Us', '<h1>Get in Touch</h1><p>We\'d love to hear from you! Reach out to us for inquiries, bookings, or any questions you might have.</p>', NULL, NULL, '2026-07-24 05:34:59');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text NOT NULL,
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `description`) VALUES
(1, 'site_name', 'CrossWay Travel', 'Website name'),
(2, 'site_email', 'crosswaydarjeelingtravel@gmail.com', 'Contact email'),
(3, 'site_phone', '7797970234', 'Contact phone'),
(4, 'address', 'Darjeeling, West Bengal', 'Company address'),
(5, 'facebook', '', 'Facebook URL'),
(6, 'twitter', '', 'Twitter URL'),
(7, 'instagram', '', 'Instagram URL'),
(8, 'about_mission', 'To provide authentic, safe, and memorable travel experiences that connect people with the natural wonders and cultural heritage of the Himalayas.', 'About Page - Mission Statement'),
(9, 'about_vision', 'To become the most trusted and preferred travel partner in the region, known for quality service and genuine hospitality.', 'About Page - Vision Statement'),
(10, 'about_values', 'Integrity, Customer First, Quality Service, Innovation, Sustainability', 'About Page - Core Values');

-- --------------------------------------------------------

--
-- Table structure for table `sightseeing`
--

CREATE TABLE `sightseeing` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `icon` varchar(50) DEFAULT 'fa-mountain',
  `badge` varchar(50) DEFAULT 'Must Visit',
  `color` varchar(50) DEFAULT '#1f6332',
  `points` text NOT NULL,
  `sort_order` int(11) DEFAULT 0,
  `active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sightseeing`
--

INSERT INTO `sightseeing` (`id`, `title`, `icon`, `badge`, `color`, `points`, `sort_order`, `active`, `created_at`, `updated_at`) VALUES
(1, '7 Points', 'fa-mountain', 'Must Visit', '#1f6332', 'Zoo & HMI\nMuseum & Tea Garden\nTenzing Rock & Japanese Temple\nPeace Pagoda', 0, 1, '2026-07-31 06:25:51', '2026-07-31 06:25:51'),
(2, '3 Points', 'fa-sun', 'Scenic', '#f57c00', 'Tiger Hill\nBatasia Loop\nMonastery', 1, 1, '2026-07-31 06:25:51', '2026-07-31 06:25:51'),
(3, 'Extra & Out of Town', 'fa-map-signs', 'Explore', '#00838f', 'Rock Garden & Ropeway\nLamahatta & Tinchule\nMirik Lake & Nepal Border', 2, 1, '2026-07-31 06:25:51', '2026-07-31 06:25:51');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` enum('admin','editor') DEFAULT 'editor',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `role`, `created_at`, `last_login`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@crosswaytravel.com', 'admin', '2026-07-24 05:34:59', '2026-07-31 03:51:23');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `about_settings`
--
ALTER TABLE `about_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `about_team`
--
ALTER TABLE `about_team`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gallery`
--
ALTER TABLE `gallery`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `packages`
--
ALTER TABLE `packages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `page_name` (`page_name`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `sightseeing`
--
ALTER TABLE `sightseeing`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `about_settings`
--
ALTER TABLE `about_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `about_team`
--
ALTER TABLE `about_team`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `gallery`
--
ALTER TABLE `gallery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `packages`
--
ALTER TABLE `packages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `pages`
--
ALTER TABLE `pages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `sightseeing`
--
ALTER TABLE `sightseeing`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
