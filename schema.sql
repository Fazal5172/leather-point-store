-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 04, 2026 at 05:04 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `leather_point_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `created_at`) VALUES
(1, 'Bags', '2026-06-27 12:28:04'),
(2, 'Jackets', '2026-06-27 12:28:04'),
(3, 'Wallets', '2026-06-27 12:28:04'),
(4, 'Belts', '2026-06-27 12:28:04');

-- --------------------------------------------------------

--
-- Table structure for table `feedbacks`
--

CREATE TABLE `feedbacks` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedbacks`
--

INSERT INTO `feedbacks` (`id`, `name`, `email`, `message`, `created_at`) VALUES
(1, 'John Doe', 'user@gmail.com', 'Awesome products with quick delivery', '2026-06-27 12:48:49');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `payment_method` enum('credit_card','cod') NOT NULL,
  `status` enum('pending','approved','canceled') DEFAULT 'pending',
  `shipping_address` text NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(150) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total_amount`, `payment_method`, `status`, `shipping_address`, `phone`, `email`, `created_at`) VALUES
(1, 1, 249.99, 'credit_card', 'pending', 'House11,Mianwali,Pakistan', '+923129876543', 'user@gmail.com', '2026-06-27 12:36:30'),
(2, 1, 129.50, 'credit_card', 'pending', 'Mehirshahwali,Mianwali,Pakistan', '+923129876543', 'user@gmail.com', '2026-06-27 12:38:10'),
(3, 1, 249.99, 'credit_card', 'pending', 'Kamar Mushani, Mianwali, Pakistan', '+923037982761', 'user@gmail.com', '2026-06-27 12:41:51'),
(4, 1, 289.00, 'cod', 'approved', '5th House ,2nd Street,Blue coloni, Mianwali,pakistan', '+923037982761', 'fazalabbas5172@gmail.com', '2026-06-27 12:46:37'),
(5, 2, 129.50, 'credit_card', 'canceled', 'Mehirshawali, Tehsil isa khel , Mianwali, pakistan', '+923001234567', 'admin@leatherpoint.com', '2026-06-27 13:51:25'),
(6, 1, 35.00, 'credit_card', 'approved', 'Kamar house near rabi plaza mianwali', '+923129876544', 'user@gmail.com', '2026-06-28 15:28:12'),
(7, 1, 129.50, 'credit_card', 'pending', 'Mehirshahwali, kamar mushani ,Mianwali', '+923037982761', 'fazalabbas5172@gmail.com', '2026-06-29 09:16:21'),
(8, 1, 149.99, 'cod', 'pending', 'Alrabi plaza, mianwali,pakistan', '+923129876544', 'user@gmail.com', '2026-07-02 18:26:37');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `price`, `quantity`) VALUES
(1, 1, 3, 249.99, 1),
(2, 2, 2, 129.50, 1),
(3, 3, 3, 249.99, 1),
(4, 4, 4, 289.00, 1),
(5, 5, 2, 129.50, 1),
(6, 6, 6, 35.00, 1),
(7, 7, 2, 129.50, 1),
(8, 8, 1, 149.99, 1);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `subcategory_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `color` varchar(50) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category_id`, `subcategory_id`, `name`, `description`, `price`, `color`, `stock`, `image`, `created_at`) VALUES
(1, 1, 1, 'Classic Leather Backpack', 'Handcrafted from full-grain brown leather, featuring durable straps and multiple zip compartments. Perfect for daily commutes or traveling.', 149.99, 'Brown', 14, 'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?auto=format&fit=crop&q=80&w=600', '2026-06-27 12:28:04'),
(2, 1, 2, 'Vintage Leather Messenger Bag', 'A premium black leather messenger bag featuring an adjustable shoulder strap and padded laptop compartment.', 129.50, 'Black', 5, 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?auto=format&fit=crop&q=80&w=600', '2026-06-27 12:28:04'),
(3, 2, 4, 'Classic Tan Bomber Jacket', 'Crafted from authentic high-quality leather, this tan bomber jacket comes with ribbed cuffs and comfortable inner lining.', 249.99, 'Tan', 10, 'https://images.unsplash.com/photo-1591047139829-d91aecb6caea?w=400&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8Mnx8Q2xhc3NpYyUyMFRhbiUyMEJvbWJlciUyMEphY2tldHxlbnwwfHwwfHx8MA%3D%3D', '2026-06-27 12:28:04'),
(4, 2, 5, 'Urban Black Biker Jacket', 'An edgy black biker jacket with premium metal zippers, double lining, and water-resistant leather styling.', 289.00, 'Black', 5, 'https://images.unsplash.com/photo-1727515546577-f7d82a47b51d?w=400&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1yZWxhdGVkfDE3fHx8ZW58MHx8fHx8', '2026-06-27 12:28:04'),
(5, 3, 6, 'Luxury Slim Wallet', 'Elegant mahogany bi-fold wallet. Features RFID blocking technology and holds up to 8 credit cards and cash.', 45.00, 'Brown', 25, 'https://images.unsplash.com/photo-1627123424574-724758594e93?auto=format&fit=crop&q=80&w=600', '2026-06-27 12:28:04'),
(6, 4, 8, 'Italian Full-Grain Belt', 'Genuine mahogany Italian leather belt with a brushed nickel buckle. Highly durable and versatile.', 35.00, 'Mahogany', 29, 'https://images.unsplash.com/photo-1660382114340-6f736c81da2d?w=400&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MXx8SXRhbGlhbiUyMEZ1bGwtR3JhaW4lMjBCZWx0fGVufDB8fDB8fHww', '2026-06-27 12:28:04');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` between 1 and 5),
  `review_text` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `user_id`, `product_id`, `rating`, `review_text`, `created_at`) VALUES
(1, 1, 6, 5, 'Best product', '2026-06-28 15:27:13');

-- --------------------------------------------------------

--
-- Table structure for table `subcategories`
--

CREATE TABLE `subcategories` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subcategories`
--

INSERT INTO `subcategories` (`id`, `category_id`, `name`, `created_at`) VALUES
(1, 1, 'Backpacks', '2026-06-27 12:28:04'),
(2, 1, 'Messenger Bags', '2026-06-27 12:28:04'),
(3, 1, 'Travel Bags', '2026-06-27 12:28:04'),
(4, 2, 'Bomber Jackets', '2026-06-27 12:28:04'),
(5, 2, 'Biker Jackets', '2026-06-27 12:28:04'),
(6, 3, 'Bi-Fold Wallets', '2026-06-27 12:28:04'),
(7, 3, 'Cardholders', '2026-06-27 12:28:04'),
(8, 4, 'Formal Belts', '2026-06-27 12:28:04'),
(9, 4, 'Casual Belts', '2026-06-27 12:28:04');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `phone`, `role`, `created_at`) VALUES
(1, 'John Doe', 'user@gmail.com', '$2a$12$0IUfFSgA9lR8mWDSutal2OKqt1iu4bIoyAK3skphvpu8vcG/yPei2', '+923129876544', 'user', '2026-06-27 12:28:04'),
(2, 'Admin Fazal', 'admin@leatherpoint.com', '$2a$12$s8fYRrLLhpmFRrww8K0F8OR3gU34Eje4DbHfbHoWKPCpJpawetAUW', '+923001234567', 'admin', '2026-06-27 12:28:04');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `feedbacks`
--
ALTER TABLE `feedbacks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `subcategory_id` (`subcategory_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `subcategories`
--
ALTER TABLE `subcategories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `feedbacks`
--
ALTER TABLE `feedbacks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `subcategories`
--
ALTER TABLE `subcategories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `products_ibfk_2` FOREIGN KEY (`subcategory_id`) REFERENCES `subcategories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `subcategories`
--
ALTER TABLE `subcategories`
  ADD CONSTRAINT `subcategories_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
