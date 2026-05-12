-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 07, 2026 at 10:45 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `expense_tracker_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `budgets`
--

CREATE TABLE `budgets` (
  `id` int(11) NOT NULL,
  `userId` int(11) NOT NULL COMMENT 'Reference to user who owns this budget',
  `month` tinyint(4) NOT NULL COMMENT 'Month (1-12)',
  `year` year(4) NOT NULL COMMENT 'Year',
  `budgetAmount` decimal(10,2) NOT NULL COMMENT 'Planned budget amount',
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Creation timestamp',
  `updatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Last update timestamp'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Monthly budget records for spending comparison and tracking';

--
-- Dumping data for table `budgets`
--

INSERT INTO `budgets` (`id`, `userId`, `month`, `year`, `budgetAmount`, `createdAt`, `updatedAt`) VALUES
(1, 3, 5, '2026', 300.00, '2026-05-05 00:09:42', '2026-05-05 00:09:42'),
(2, 6, 5, '2026', 1000000.00, '2026-05-05 01:58:21', '2026-05-05 01:59:00');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `userId` int(11) NOT NULL COMMENT 'Reference to user who owns this category',
  `name` varchar(150) NOT NULL COMMENT 'Category name - supports Arabic',
  `icon` varchar(50) DEFAULT NULL COMMENT 'Category icon or emoji',
  `color` varchar(20) DEFAULT NULL COMMENT 'Hex color code for UI display',
  `description` text DEFAULT NULL COMMENT 'Category description - supports Arabic',
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Creation timestamp',
  `updatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Last update timestamp'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Expense categories for organizing spending';

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `userId`, `name`, `icon`, `color`, `description`, `createdAt`, `updatedAt`) VALUES
(1, 1, 'طعام', '🍔', '#FF5733', 'مصاريف الأكل', '2026-05-05 00:09:41', '2026-05-05 00:09:41'),
(2, 1, 'مواصلات', '🚗', '#33C1FF', 'تكاليف النقل', '2026-05-05 00:09:41', '2026-05-05 00:09:41'),
(3, 2, 'Shopping', '🛍️', '#8E44AD', 'شراء ملابس وأشياء', '2026-05-05 00:09:41', '2026-05-05 00:09:41'),
(4, 3, 'Food', NULL, NULL, NULL, '2026-05-05 00:09:41', '2026-05-05 00:09:41'),
(5, 2, ' طعام', '', '#2563eb', NULL, '2026-05-05 00:41:56', '2026-05-05 00:41:56'),
(6, 6, 'food', '🍔 ', '#1a62ff', NULL, '2026-05-05 02:01:20', '2026-05-05 02:01:20');

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id` int(11) NOT NULL,
  `userId` int(11) NOT NULL COMMENT 'Reference to user who made this expense',
  `categoryId` int(11) NOT NULL COMMENT 'Reference to expense category',
  `amount` decimal(10,2) NOT NULL COMMENT 'Expense amount',
  `description` text DEFAULT NULL COMMENT 'Expense description - supports Arabic',
  `expenseDate` date NOT NULL COMMENT 'Date when expense occurred',
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Record creation timestamp',
  `updatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Last update timestamp'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Daily expense records with amount, category, and date';

--
-- Dumping data for table `expenses`
--

INSERT INTO `expenses` (`id`, `userId`, `categoryId`, `amount`, `description`, `expenseDate`, `createdAt`, `updatedAt`) VALUES
(1, 1, 1, 15.50, 'وجبة غداء', '2026-05-05', '2026-05-05 00:09:41', '2026-05-05 00:09:41'),
(2, 1, 2, 5.00, 'مواصلات بالباص', '2026-05-06', '2026-05-05 00:09:41', '2026-05-05 00:09:41'),
(3, 2, 3, 120.00, 'شراء ملابس', '2026-05-05', '2026-05-05 00:09:41', '2026-05-05 00:09:41'),
(4, 3, 4, 20.00, NULL, '2026-05-05', '2026-05-05 00:09:41', '2026-05-05 00:09:41'),
(6, 2, 5, 7000.00, 'غداء', '2026-05-04', '2026-05-05 01:39:25', '2026-05-05 01:39:25'),
(7, 6, 6, 100.00, 'غداء، مواصلات، فاتورة', '2026-05-05', '2026-05-05 02:20:41', '2026-05-05 02:20:41');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `openId` varchar(64) DEFAULT NULL,
  `name` text DEFAULT NULL COMMENT 'User full name - supports Arabic',
  `email` varchar(320) DEFAULT NULL COMMENT 'User email address',
  `password` varchar(255) NOT NULL,
  `loginMethod` varchar(64) DEFAULT NULL COMMENT 'Login method (e.g., manus)',
  `role` enum('user','admin') NOT NULL DEFAULT 'user' COMMENT 'User role',
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Account creation timestamp',
  `updatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Last update timestamp',
  `lastSignedIn` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Last sign-in timestamp'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Core user table for authentication and account management';

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `openId`, `name`, `email`, `password`, `loginMethod`, `role`, `createdAt`, `updatedAt`, `lastSignedIn`) VALUES
(1, NULL, 'Test User', 'test@example.com', 'hashedpassword', NULL, 'user', '2026-05-04 23:20:35', '2026-05-04 23:20:35', '2026-05-04 23:20:35'),
(2, NULL, 'expense-tracker3', 'Hafed@gmail.com', '$2y$10$dNlInufC4qO4qhrZAMJJauV0Fiya3/usRY4/HnaMT.28UYnD7OrEy', NULL, 'user', '2026-05-04 23:36:30', '2026-05-04 23:36:30', '2026-05-04 23:36:30'),
(3, 'user_001', 'أحمد محمد', 'ahmed@example.com', '', 'manus', 'user', '2026-05-05 00:09:02', '2026-05-05 00:09:02', '2026-05-05 00:09:02'),
(4, 'user_002', 'Sara Ali', 'sara@example.com', '', 'manus', 'admin', '2026-05-05 00:09:02', '2026-05-05 00:09:02', '2026-05-05 00:09:02'),
(5, 'user_003', 'Ali', NULL, '', NULL, 'user', '2026-05-05 00:09:41', '2026-05-05 00:09:41', '2026-05-05 00:09:41'),
(6, NULL, 'Hafed Mohammed', 'Hafed123@gmail.com', '$2y$10$tKQkkEsuyVspA3Jt5yDxXONOSUftjp.iUbWTJXCM6ERNH/ButRWRi', NULL, 'user', '2026-05-05 01:56:11', '2026-05-05 01:56:11', '2026-05-05 01:56:11');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `budgets`
--
ALTER TABLE `budgets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_budget` (`userId`,`month`,`year`),
  ADD KEY `idx_userId` (`userId`),
  ADD KEY `idx_userMonthYear` (`userId`,`month`,`year`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_category` (`userId`,`name`),
  ADD KEY `idx_userId` (`userId`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_userId` (`userId`),
  ADD KEY `idx_categoryId` (`categoryId`),
  ADD KEY `idx_expenseDate` (`expenseDate`),
  ADD KEY `idx_userDate` (`userId`,`expenseDate`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `openId` (`openId`),
  ADD KEY `idx_openId` (`openId`),
  ADD KEY `idx_email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `budgets`
--
ALTER TABLE `budgets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `budgets`
--
ALTER TABLE `budgets`
  ADD CONSTRAINT `fk_budgets_user` FOREIGN KEY (`userId`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `fk_categories_user` FOREIGN KEY (`userId`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `expenses`
--
ALTER TABLE `expenses`
  ADD CONSTRAINT `fk_expenses_category` FOREIGN KEY (`categoryId`) REFERENCES `categories` (`id`),
  ADD CONSTRAINT `fk_expenses_user` FOREIGN KEY (`userId`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
