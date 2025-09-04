-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Aug 02, 2025 at 08:39 AM
-- Server version: 5.7.34
-- PHP Version: 8.0.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kidmate_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `role` varchar(20) DEFAULT 'admin',
  `permissions` text,
  `is_active` tinyint(1) DEFAULT '1',
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`id`, `username`, `email`, `password_hash`, `full_name`, `role`, `permissions`, `is_active`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@kidmate.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', 'super_admin', NULL, 1, NULL, '2025-07-29 21:13:28', '2025-07-29 21:13:28');

-- --------------------------------------------------------

--
-- Table structure for table `alembic_version`
--

CREATE TABLE `alembic_version` (
  `version_num` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `alembic_version`
--

INSERT INTO `alembic_version` (`version_num`) VALUES
('15fdf959beff');

-- --------------------------------------------------------

--
-- Table structure for table `grades`
--

CREATE TABLE `grades` (
  `id` int(11) NOT NULL,
  `kid_id` int(11) NOT NULL,
  `subject` varchar(100) NOT NULL,
  `grade` varchar(10) NOT NULL,
  `remarks` text,
  `comments` text,
  `date_recorded` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `grades`
--

INSERT INTO `grades` (`id`, `kid_id`, `subject`, `grade`, `remarks`, `comments`, `date_recorded`) VALUES
(1, 1, 'English', 'B+', 'Good work', 'Needs improvement in writing', '2024-01-15'),
(2, 1, 'Science', 'A-', 'Very good', 'Shows interest in experiments', '2024-01-15'),
(3, 2, 'Mathematics', 'C+', 'Average', 'Needs more practice', '2024-01-15'),
(4, 2, 'English', 'B', 'Satisfactory', 'Good reading skills', '2024-01-15');

-- --------------------------------------------------------

--
-- Table structure for table `kids`
--

CREATE TABLE `kids` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `kids`
--

INSERT INTO `kids` (`id`, `name`, `image`, `parent_id`) VALUES
(2, 'Sophia', NULL, 2),
(3, 'Kwabena Kuffour', NULL, 3),
(4, 'Akosua Oseiooo', NULL, 4),
(5, 'Hanna Yay', '[value-3]', 1),
(6, 'Ama Doe', NULL, 6),
(7, 'Yaw Mensah', NULL, 7),
(8, 'Kojo Owusu', NULL, 8),
(9, 'Esi Yeboah', NULL, 9),
(10, 'Kofi Boateng', NULL, 10),
(11, 'Emmanuel Johnson', NULL, 1),
(12, 'Abou Ama', NULL, 7),
(13, 'yayro', NULL, 2),
(14, 'ff', NULL, 1),
(15, 'Dornyoh David k', NULL, 1),
(16, 'test', NULL, 2);

-- --------------------------------------------------------

--
-- Table structure for table `parents`
--

CREATE TABLE `parents` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `push_token` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `occupation` varchar(100) DEFAULT NULL,
  `relationship` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `parents`
--

INSERT INTO `parents` (`id`, `name`, `phone`, `email`, `image`, `push_token`, `address`, `occupation`, `relationship`) VALUES
(1, 'Fatima Mahamanj', '02469146001', NULL, '[value-4]', '[value-5]', NULL, NULL, NULL),
(2, 'Michael Smith', '+233202223344', NULL, NULL, NULL, NULL, NULL, NULL),
(3, 'Linda Kuffour', '+233203334455', NULL, NULL, NULL, NULL, NULL, NULL),
(4, 'Kwame Osei', '+233204445566', NULL, NULL, NULL, NULL, NULL, NULL),
(5, 'Fatima Mahama', '+233205556677', NULL, NULL, NULL, NULL, NULL, NULL),
(6, 'John Doe', '+233206667788', NULL, NULL, NULL, NULL, NULL, NULL),
(7, 'Sarah Mensah', '+233207778899', NULL, NULL, NULL, NULL, NULL, NULL),
(8, 'Peter Owusu', '+233208889900', NULL, NULL, NULL, NULL, NULL, NULL),
(9, 'Grace Yeboah', '+233209990011', NULL, NULL, NULL, NULL, NULL, NULL),
(10, 'Daniel Boateng', '+233210101112', NULL, NULL, NULL, NULL, NULL, NULL),
(11, 'Alice Johnson', '+233201112233', NULL, NULL, NULL, NULL, NULL, NULL),
(12, 'david dornyoh', '26850370', NULL, NULL, NULL, NULL, NULL, NULL),
(13, 'david dornyoh', '26850370', NULL, NULL, NULL, NULL, NULL, NULL),
(14, 'david dornyoh', '26850370', NULL, NULL, NULL, NULL, NULL, NULL),
(15, 'david dornyoh', '26850370', NULL, NULL, NULL, NULL, NULL, NULL),
(16, 'david dornyoh', '26850370', NULL, NULL, NULL, NULL, NULL, NULL),
(17, 'david dornyoh', '26850370', NULL, NULL, NULL, NULL, NULL, NULL),
(18, 'david dornyohn', '2685037089', NULL, NULL, NULL, NULL, NULL, NULL),
(19, 'david dornyohn', '2685037089', NULL, NULL, NULL, NULL, NULL, NULL),
(20, 'good', '768900', NULL, NULL, NULL, NULL, NULL, NULL),
(21, 'jj', '77788', NULL, NULL, NULL, NULL, NULL, NULL),
(22, 'david dornyohn', '2685037089', NULL, NULL, NULL, NULL, NULL, NULL),
(23, 'jj', '77788', NULL, NULL, NULL, NULL, NULL, NULL),
(24, 'jj', '77788', NULL, NULL, NULL, NULL, NULL, NULL),
(25, 'jj', '77788', NULL, NULL, NULL, NULL, NULL, NULL),
(26, 'jj', '77788', NULL, NULL, NULL, NULL, NULL, NULL),
(27, 'david dornyohn', '2685037089', NULL, NULL, NULL, NULL, NULL, NULL),
(28, 'david dornyohn', '2685037089', NULL, NULL, NULL, NULL, NULL, NULL),
(29, 'jooo', '26850370', NULL, NULL, NULL, NULL, NULL, NULL),
(30, 'jooo', '26850370', NULL, NULL, NULL, NULL, NULL, NULL),
(31, 'jooo', '26850370', NULL, NULL, NULL, NULL, NULL, NULL),
(32, 'jooo', '26850370', NULL, NULL, NULL, NULL, NULL, NULL),
(33, 'david dornyohn', '2685037089', NULL, NULL, NULL, NULL, NULL, NULL),
(34, 'david dornyohj', '26850370', NULL, NULL, NULL, NULL, NULL, NULL),
(35, 'kokj', 'ccc', NULL, NULL, NULL, NULL, NULL, NULL),
(36, 'david dornyoh', '26850370', NULL, NULL, NULL, NULL, NULL, NULL),
(37, 'david dornyohf', '26850376', NULL, NULL, NULL, 'lapaz', 'ffff', 'Father'),
(38, 'david dornyohf', '26850376', NULL, NULL, NULL, 'lapaz', 'ffff', 'Father');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `payment_id` varchar(36) NOT NULL,
  `parent_id` varchar(36) NOT NULL,
  `child_id` varchar(36) NOT NULL,
  `amount` float NOT NULL,
  `currency` varchar(3) DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `journey_date` date NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `payment_id`, `parent_id`, `child_id`, `amount`, `currency`, `status`, `payment_method`, `description`, `journey_date`, `created_at`, `updated_at`) VALUES
(1, '9b14f2ce-f483-4818-babe-c88866cf3c12', 'parent-001', 'child-001', 25, 'USD', 'completed', 'card', 'School pickup service - Week 1', '2025-07-29', '2025-07-29 19:42:47', '2025-07-29 19:42:47'),
(2, '807b52e4-5eb5-49fb-8c43-d78c221c6bac', 'parent-001', 'child-001', 30, 'USD', 'pending', 'mobile_money', 'School pickup service - Week 2', '2025-07-29', '2025-07-29 19:42:47', '2025-07-29 19:42:47'),
(3, '58145902-97fa-4ad1-ad6b-71a6ec58002c', 'parent-002', 'child-002', 20, 'USD', 'completed', 'cash', 'After-school pickup', '2025-07-29', '2025-07-29 19:42:47', '2025-07-29 19:42:47'),
(4, 'fe10a7bb-182e-4a7b-8825-fcac2f6730cf', 'parent-001', 'child-001', 35, 'USD', 'failed', 'card', 'Weekend pickup service', '2025-07-29', '2025-07-29 19:42:47', '2025-07-29 19:42:47'),
(5, 'a701ebd3-3941-4d14-9c31-f8ba3fda39fc', 'parent-003', 'child-003', 40, 'USD', 'refunded', 'card', 'Holiday pickup service', '2025-07-29', '2025-07-29 19:42:47', '2025-07-29 19:42:47');

-- --------------------------------------------------------

--
-- Table structure for table `pickup_journey`
--

CREATE TABLE `pickup_journey` (
  `id` int(11) NOT NULL,
  `pickup_id` varchar(36) NOT NULL,
  `parent_id` varchar(36) NOT NULL,
  `child_id` varchar(36) NOT NULL,
  `pickup_person_id` varchar(36) NOT NULL,
  `status` varchar(20) NOT NULL,
  `timestamp` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `pickup_journey`
--

INSERT INTO `pickup_journey` (`id`, `pickup_id`, `parent_id`, `child_id`, `pickup_person_id`, `status`, `timestamp`) VALUES
(58, 'JWT0G3U3', 'david1100', 'child-001', 'person-001', 'pending', '2025-07-29 19:31:26'),
(59, 'JWT0G3U3', 'david1100', 'child-001', 'person-001', 'picked', '2025-07-29 19:31:29'),
(60, 'JWT0G3U3', 'david1100', 'child-001', 'person-001', 'dropoff', '2025-07-29 19:31:37'),
(61, 'JWT0G3U3', 'david1100', 'child-001', 'person-001', 'completed', '2025-07-29 19:31:40');

-- --------------------------------------------------------

--
-- Table structure for table `pickup_persons`
--

CREATE TABLE `pickup_persons` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `pickup_id` varchar(100) DEFAULT NULL,
  `kid_id` int(11) DEFAULT NULL,
  `uuid` varchar(36) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `pickup_persons`
--

INSERT INTO `pickup_persons` (`id`, `name`, `image`, `pickup_id`, `kid_id`, `uuid`) VALUES
(2, 'tend', 'uploads/images/photo.jpg', '1234', 5, 'a7f560ae-218a-46b3-974b-767c5ef71336'),
(3, 'Ghosh\'s', 'uploads/images/photo.jpg', '1234', 5, '954247de-b22f-4800-a343-d6f7bd03029c'),
(4, 'the', 'uploads/pickup.jpg', '6665', 5, 'b8e84255-2ebc-41ca-95eb-bc86d38a63ed'),
(6, 'Ghg', 'uploads/images/EBE23214-5E74-44EC-8141-1C95C757955C.jpg', '12', 5, '7724239c-c478-45dd-9ca7-8b1226e67715'),
(7, 'Dan’s', 'uploads/images/57DB34CE-79A2-4D9B-8B3B-23738E29B99F.jpg', '33', 5, 'aedd65e0-7bdc-45cd-a7ca-821af1f02f82'),
(8, 'Didi’s', 'uploads/images/496EBDB3-0589-4433-9292-3554BDBE269A.jpg', '3748484', 5, '66d7c2d3-771c-4ce6-9806-bc845c439406'),
(9, 'David for ', 'uploads/images/profile.jpg', '55', 5, 'c2a793db-5454-494f-aa9c-0472ce8293bf');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `role` varchar(50) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `push_token` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `password_hash`, `role`, `image`, `push_token`) VALUES
(1, 'JoDDhn', 'test22gmail.com', '12345678930', 'scrypt:32768:8:1$otDpFvDGTvi89SXq$97b5def825e785f4f140e64c37cf2eeae7d28f9dc44f89c445f0cc9dc3026775ed48d52fcf8270a09261a169436cb260ed1bc2d3208edfdf45b1eb2d762a0fc3', 'Parent', NULL, NULL),
(2, 'JoDDhvn', 'test22@gmail.com', '123456789310', 'scrypt:32768:8:1$CqFl1uafVHP8YLFM$93a83c4d6f4e80fa3632b8d598fa59d0c95673599326f4b43809a21d1b7c2893f657b2991966b7f546d81501233b905d3e1793fd3f06acb0576dccee03f27b0b', 'Parent', NULL, NULL),
(3, 'JoDDhvcn', 'test22c@gmail.com', '123489310', 'scrypt:32768:8:1$NlWY5kiDTKQyaQqA$e813e5e1d1fa9bd23470499c33d11892f1de06703c61eb2f6c38c20bcb08e8f69d5546e106cd8685138c4036c5f9709928cbeb38f28a34f8c83bebb1b3a76fb8', 'Parent', NULL, NULL),
(4, 'JoDDhn', 'tet2@gmail.com', '1234310', 'scrypt:32768:8:1$fjlyGbO7xDKtjSI0$649cf53aebfd95f002b480776a1069c5fa2b1a5837dc27cd347bd70daebab0dccf3d3af80d16ced107a52a685d36f877f55ba61097066fce86e6ac9c6e3c2c22', 'Parent', NULL, NULL),
(5, 'vufpuv-guCzig-4binnu', 'David@hmail.com', 'Dhfbfn', 'scrypt:32768:8:1$38EfdRoaeL7AXPQZ$c8d60f41b90f4307489a4a2274dc6e6386edec99c4935c964813ff263602ad3d975551a379850ec1b08b2086802f1021413713e906ccda8f4e7863fd79c933ce', 'Parent', NULL, NULL),
(6, 'JoDDfhn', 'tet2f@gmail.com', '1234f310', 'scrypt:32768:8:1$hynq2v02EjqtDA9y$58d8d3e99ccb9428bec4c8326b294fec88f5ac0c4cd0c387382c7dc09739e610b79b681a3319a96408e04f072ea16924d97e745e8947186fd86b057d5e177940', 'Parent', NULL, NULL),
(7, 'jesGiw-9kabwi-qycmym', 'Dabb@hhh.com', 'Ggv', 'scrypt:32768:8:1$nFDiMVMQhxn6cosY$646ec59801359c2395b890f70085826fad328004b4eaad4ec032c48728ff68fd74159ef7618ac36617632093615acc9661a6fe8d97db7c583119826f8fc6b88e', 'Parent', NULL, NULL),
(8, 'JoDDfhnk', 'test@gmail.com', '1234fk310', '$2y$10$24uqkKZDIMYmXSR3YaG.dOoi0lp.EocefJkAqpS.qBuvX9ANe5wzK', 'Parent', NULL, NULL),
(9, 'fusnUk-4hitne-notfeb', 'afinourudy@yahoo.com', 'Gokbb', 'scrypt:32768:8:1$on996XxRYzxi1zoY$2bd40014b32a4b5eb93e2c99a42623dc003ccd2e88f552fb7646188a818f25f182ad9e6cfb725dd6421bf04d810cdd73d4216359c6141c4f2fb17815b6e23e0b', 'Parent', NULL, NULL),
(10, '+233246914600', 'davidgh@gmail.com', 'David Dornyoh', 'scrypt:32768:8:1$tmouJTqO1oKcWB9f$8a2573909fa20296435d9f14c3195d17b9bb121e927530ff7dbcc7e64fd3740fcba11d3e450caa265971910bc116f7c6c366a3873054666cef8d4cc88edb7c1b', 'Parent', NULL, NULL),
(11, '+2332469146', 'davidgd@gmail.com', 'David Dornyohg', 'scrypt:32768:8:1$2t8LXAlcGaRT7We0$02c1f7df952e684c00944d72412c4b69fb8552a21823c0927bfd2d8387cc7943986dc664b88834f9519f7bf7e5be63bb57648bcb9bcc60f397a01efaafd6537c', 'Parent', NULL, NULL),
(12, '+2332469146', 'davidgddd@gmail.com', 'David Hdhd', 'scrypt:32768:8:1$UlOiCsbk4VSERMBD$03d453e15e688148b9617040c99f38aa80318db712f1bc20bbc4c4db3589a8586a96794ac971b617497737547b3a240cf1e9a805fc97a159eb61dc67ad803fd4', 'Parent', NULL, NULL),
(13, '+2332469146', 'davidgdddxbx@gmail.com', 'David Ds', 'scrypt:32768:8:1$nQic0xUMWaIEgk0k$67cc34fb6d37ceca02a8581918f4154c17aabfe7cfc04ce023c456f2bd648f52bf27319e1e66198c3c44adb5c50b54b51b181da96fb56fa67c0ebe53f7e94752', 'Parent', NULL, NULL),
(14, '+2332469146', 'davidgddg@gmail.com', 'David Dscv', 'scrypt:32768:8:1$8p9BNvSMpMK8mm4j$efddcf3b72877ce7663ab02594599cc538ac7c133b8107c676332a7636ff56d4c7c9e246447bb2a04fbaf9dc14870143028558db041cd74b1c6eea59431b2305', 'Parent', NULL, NULL),
(15, '+2332469146', 'davidgdv@gmail.com', 'David Hdbbd', 'scrypt:32768:8:1$nXUjA80TePRJPCZ1$d8b52f5874cbe80788c110aab941608d0468ab7bd6581b766db6d10ac2f9fc8630dc95e300ad23a18e7213833b06c049db9634697b234474794583f37e14a539', 'Parent', NULL, NULL),
(16, 'david1100', 'david1100@gmail.com', '1234fk3160', 'scrypt:32768:8:1$uhZY2EXPw1U9yPo2$49836c9fe35d388f0bcb8fd9435dd30328d2e3ed95f2eb6d130a3b0d72554f7d007cdc5f8f79125d214d483813a075bc8aabe09484cd461a853bbb71a52c23cb', 'Parent', NULL, NULL),
(17, '5558', 'davs@gmail.com', 'David Dors', 'scrypt:32768:8:1$UkbdItRd64mGo5Or$61c4adad0820be0750fd3b72699487c74f87efa91eee67b3fc901f5688af85d0c84d9575418170f76fe80044bf189fd1659ad91e57a69f33a25008eac92c66ca', 'Parent', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `alembic_version`
--
ALTER TABLE `alembic_version`
  ADD PRIMARY KEY (`version_num`);

--
-- Indexes for table `grades`
--
ALTER TABLE `grades`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kids`
--
ALTER TABLE `kids`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Indexes for table `parents`
--
ALTER TABLE `parents`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `payment_id` (`payment_id`);

--
-- Indexes for table `pickup_journey`
--
ALTER TABLE `pickup_journey`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pickup_persons`
--
ALTER TABLE `pickup_persons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uuid` (`uuid`),
  ADD KEY `kid_id` (`kid_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `phone` (`phone`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `grades`
--
ALTER TABLE `grades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `kids`
--
ALTER TABLE `kids`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `parents`
--
ALTER TABLE `parents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `pickup_journey`
--
ALTER TABLE `pickup_journey`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `pickup_persons`
--
ALTER TABLE `pickup_persons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `kids`
--
ALTER TABLE `kids`
  ADD CONSTRAINT `kids_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `parents` (`id`);

--
-- Constraints for table `pickup_persons`
--
ALTER TABLE `pickup_persons`
  ADD CONSTRAINT `pickup_persons_ibfk_1` FOREIGN KEY (`kid_id`) REFERENCES `kids` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
