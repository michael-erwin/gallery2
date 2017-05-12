-- phpMyAdmin SQL Dump
-- version 4.6.5.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 08, 2017 at 03:41 PM
-- Server version: 10.1.21-MariaDB
-- PHP Version: 7.1.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gallery-copy`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(255) NOT NULL,
  `level` int(11) NOT NULL,
  `type` varchar(32) NOT NULL DEFAULT 'photo',
  `title` varchar(64) NOT NULL,
  `icon` text NOT NULL,
  `icon_default` tinyint(1) NOT NULL DEFAULT '0',
  `description` varchar(128) NOT NULL,
  `parent_id` int(255) NOT NULL,
  `published` enum('yes','no') NOT NULL,
  `share_level` text NOT NULL,
  `pvt_share_id` varchar(32) NOT NULL,
  `core` enum('yes','no') NOT NULL DEFAULT 'no',
  `date_added` bigint(255) NOT NULL,
  `date_modified` bigint(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `level`, `type`, `title`, `icon`, `icon_default`, `description`, `parent_id`, `published`, `share_level`, `pvt_share_id`, `core`, `date_added`, `date_modified`) VALUES
(1, 1, 'all', 'Uncategorized', '', 0, 'Default category.', 0, 'no', 'private', '', 'yes', 0, 1475427438),
(42, 1, 'all', 'Animals', 'assets/img/category_icons/animals.jpg', 0, '', 0, 'yes', 'public', '', 'no', 1486197197, 1488443869),
(43, 2, 'photo', 'Birds', 'assets/img/category_icons/birds-blue-sky.jpg', 0, '', 42, 'yes', 'private', 'f3025b0468bf6f5c3446c88406ce6e25', 'no', 1486199632, 1486877304),
(46, 2, 'photo', 'Mamals', '', 0, '', 42, 'yes', 'public', '', 'no', 1486201075, 1486635199),
(47, 2, 'video', 'Birds', 'assets/img/category_icons/birds-sunset.jpg', 1, '', 42, 'yes', 'public', '', 'no', 1486201090, 1486314688),
(49, 1, 'all', 'People', 'assets/img/category_icons/people.jpg', 0, '', 0, 'yes', 'private', 'c6ca4ea7dcc0fa1d7fa9f71f5f85c68e', 'no', 1486209123, 1486314833),
(50, 2, 'photo', 'Male', '', 0, '', 49, 'yes', 'public', '', 'no', 1486209142, 1486316954),
(51, 2, 'photo', 'Female', '', 0, '', 49, 'yes', 'private', '61ea6c1283af903792c9d7b8d9215f7c', 'no', 1486209155, 1486285586),
(52, 2, 'photo', 'Group', '', 0, '', 49, 'yes', 'public', '', 'no', 1486209201, 1486316702),
(54, 1, 'all', 'Places', 'assets/img/category_icons/places.jpg', 0, '', 0, 'yes', 'public', '', 'no', 1486214443, 1486316942),
(55, 2, 'video', 'Male', '', 0, '', 49, 'yes', 'public', '', 'no', 1486308141, 1486308181),
(57, 2, 'video', 'Reptiles', '', 0, '', 42, 'yes', 'public', '', 'no', 1486314762, 1486355144),
(58, 2, 'photo', 'Reptiles', '', 0, '', 42, 'yes', 'public', '', 'no', 1486355652, 0),
(64, 1, 'all', 'Others', '', 0, '', 0, 'yes', 'public', '', 'no', 1486365475, 0),
(65, 2, 'video', 'Street', '', 0, '', 54, 'yes', 'public', '', 'no', 1486556654, 0),
(66, 2, 'photo', 'Street', '', 0, '', 54, 'yes', 'public', '', 'no', 1486556691, 0),
(67, 2, 'video', 'Female', '', 0, '', 49, 'yes', 'public', '', 'no', 1486624198, 0),
(68, 2, 'video', 'Mamals', '', 0, '', 42, 'yes', 'public', '', 'no', 1488438203, 0),
(69, 2, 'photo', 'Test', '', 0, '', 64, 'yes', 'public', '', 'no', 1491722322, 0),
(70, 2, 'photo', 'Test 1', '', 0, 'Description update.', 64, 'yes', 'public', '', 'no', 1492813327, 0),
(71, 2, 'video', 'Test', '', 0, '', 64, 'yes', 'private', '', 'no', 1492753697, 0),
(72, 1, 'all', 'Secret', '', 0, '', 0, 'yes', 'public', '', 'no', 1492954234, 0),
(73, 2, 'photo', 'Entry 1', '', 0, '', 72, 'yes', 'public', '', 'no', 1492954282, 0);

-- --------------------------------------------------------

--
-- Table structure for table `favorites`
--

CREATE TABLE `favorites` (
  `id` int(250) NOT NULL,
  `user_id` int(250) NOT NULL,
  `media_id` int(250) NOT NULL,
  `media_type` enum('photo','video') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` int(11) NOT NULL,
  `name` varchar(32) NOT NULL,
  `description` text NOT NULL,
  `entity` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `description`, `entity`) VALUES
(1, 'admin_access', 'Ability to access backend administration console.', 'system'),
(2, 'role_add', 'Ability to add new role entry.', 'role'),
(3, 'role_view', 'Ability to see role entries.', 'role'),
(4, 'role_edit', 'Ability to change role details.', 'role'),
(5, 'role_delete', 'Ability to remove a role entry.', 'role'),
(6, 'user_add', 'Ability to add new user.', 'user'),
(7, 'user_view', 'Ability to view all user entries.', 'user'),
(8, 'user_edit', 'Ability to change all user details.', 'user'),
(9, 'user_delete', 'Ability to remove user entry.', 'user'),
(10, 'media_view', 'Ability to view all media types including original.', 'media'),
(11, 'photo_add', 'Ability to add new photo entries by means of upload.', 'photo'),
(12, 'photo_edit', 'Ability to change photo details.', 'photo'),
(13, 'photo_change_category', 'Ability to change photo category.', 'photo'),
(14, 'photo_delete', 'Ability to delete photo entries.', 'photo'),
(15, 'video_add', 'Ability to add new video entries by means of upload.', 'video'),
(16, 'video_edit', 'Ability to change video details.', 'video'),
(17, 'video_change_category', 'Ability to change video category.', 'video'),
(18, 'video_delete', 'Ability to remove video entries.', 'video'),
(19, 'category_view', 'Ability to view categories.', ''),
(20, 'category_add', 'Ability to add new category for all media types.', 'category'),
(21, 'category_edit', 'Ability to change details of category entries of all media types.', 'category'),
(22, 'category_delete', 'Ability to remove category entry.', 'category'),
(23, 'profile_view', 'Ability to see own profile details.', 'profile'),
(24, 'profile_edit_name', 'Ability to change own profile name when logged in.', 'profile'),
(25, 'profile_edit_email', 'Ability to change own email when logged in.', 'profile'),
(26, 'profile_edit_password', 'Ability to change or own password when logged in.', 'profile'),
(27, 'presentation_edit', 'Ability to edit presentation title, description and associated contents.', ''),
(28, 'presetnation_add', 'Ability to add new presentation entry.', ''),
(29, 'presentation_delete', 'Ability to delete presentation entry and associated contents.', '');

-- --------------------------------------------------------

--
-- Table structure for table `photos`
--

CREATE TABLE `photos` (
  `id` bigint(255) NOT NULL,
  `category_id` int(255) NOT NULL DEFAULT '1',
  `title` varchar(64) CHARACTER SET utf8 NOT NULL,
  `description` text CHARACTER SET utf8 NOT NULL,
  `tags` text CHARACTER SET utf8 NOT NULL,
  `uid` varchar(32) CHARACTER SET utf8 NOT NULL,
  `width` smallint(32) NOT NULL,
  `height` smallint(32) NOT NULL,
  `file_size` int(255) NOT NULL,
  `has_zip` tinyint(1) NOT NULL DEFAULT '0',
  `checksum` varchar(32) NOT NULL,
  `share_level` text CHARACTER SET utf8 NOT NULL,
  `mc_share_level` text NOT NULL,
  `sc_share_level` text NOT NULL,
  `pvt_share_id` varchar(32) NOT NULL,
  `date_added` bigint(255) NOT NULL,
  `date_modified` bigint(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `presentations`
--

CREATE TABLE `presentations` (
  `id` int(255) NOT NULL,
  `title` varchar(128) NOT NULL,
  `description` text NOT NULL,
  `items` text NOT NULL,
  `share_level` text NOT NULL,
  `pvt_share_id` varchar(32) NOT NULL,
  `date_added` bigint(20) NOT NULL,
  `date_modified` bigint(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Table structure for table `presentation_items`
--

CREATE TABLE `presentation_items` (
  `id` int(128) NOT NULL,
  `uid` varchar(32) NOT NULL,
  `caption` text NOT NULL,
  `parent_id` int(128) NOT NULL,
  `date_added` bigint(20) NOT NULL,
  `date_modified` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `name` varchar(16) NOT NULL,
  `description` text NOT NULL,
  `permissions` text NOT NULL,
  `core` enum('yes','no') NOT NULL DEFAULT 'no'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `description`, `permissions`, `core`) VALUES
(1, 'Administrator', 'Admin with access to all areas.', 'all', 'yes'),
(2, 'Default User', 'Fallback role with basic access.', 'profile_view,profile_edit_password', 'yes'),
(3, 'Testor', '', 'admin_access,category_add,category_delete,category_edit,photo_add,photo_change_category,photo_delete,photo_edit,profile_edit_email,profile_edit_name,profile_edit_password,profile_view,role_add,role_delete,role_edit,role_view,user_add,user_delete,user_edit,user_view,video_add,video_change_category,video_delete,video_edit', 'no');

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

CREATE TABLE `tags` (
  `id` bigint(20) NOT NULL,
  `name` varchar(36) NOT NULL,
  `date_added` bigint(20) NOT NULL,
  `date_modified` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(250) NOT NULL,
  `email` varchar(64) NOT NULL,
  `first_name` varchar(64) NOT NULL,
  `last_name` varchar(64) NOT NULL,
  `password` varchar(100) NOT NULL,
  `status` enum('active','inactive') NOT NULL,
  `role_id` varchar(128) NOT NULL,
  `token` varchar(60) NOT NULL,
  `token_time` bigint(250) NOT NULL,
  `date_added` bigint(20) NOT NULL,
  `date_modified` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `first_name`, `last_name`, `password`, `status`, `role_id`, `token`, `token_time`, `date_added`, `date_modified`) VALUES
(319, 'admin@local.host', 'John', 'Doe', '$2y$10$m6YEhClpM/vsauNekTpYeezYrOY011tVHo3lUqh7VwbGbUScq4bXy', 'active', '1', '', 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `videos`
--

CREATE TABLE `videos` (
  `id` bigint(250) NOT NULL,
  `category_id` int(250) NOT NULL,
  `title` varchar(64) NOT NULL,
  `description` text NOT NULL,
  `tags` text NOT NULL,
  `uid` varchar(32) NOT NULL,
  `width` smallint(11) NOT NULL,
  `height` smallint(11) NOT NULL,
  `file_size` int(250) NOT NULL,
  `duration` float NOT NULL,
  `checksum` varchar(32) NOT NULL,
  `share_level` text NOT NULL,
  `mc_share_level` text NOT NULL,
  `sc_share_level` text NOT NULL,
  `pvt_share_id` varchar(32) NOT NULL,
  `complete` tinyint(1) NOT NULL DEFAULT '0',
  `date_added` int(250) NOT NULL,
  `date_modified` int(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `photos`
--
ALTER TABLE `photos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uid` (`uid`);
ALTER TABLE `photos` ADD FULLTEXT KEY `title` (`title`,`description`,`tags`);

--
-- Indexes for table `presentations`
--
ALTER TABLE `presentations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `presentation_items`
--
ALTER TABLE `presentation_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UID` (`uid`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tags`
--
ALTER TABLE `tags`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `videos`
--
ALTER TABLE `videos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`uid`),
  ADD KEY `pvt_share_id` (`pvt_share_id`);
ALTER TABLE `videos` ADD FULLTEXT KEY `title` (`title`,`description`,`tags`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;
--
-- AUTO_INCREMENT for table `favorites`
--
ALTER TABLE `favorites`
  MODIFY `id` int(250) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;
--
-- AUTO_INCREMENT for table `photos`
--
ALTER TABLE `photos`
  MODIFY `id` bigint(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=260;
--
-- AUTO_INCREMENT for table `presentations`
--
ALTER TABLE `presentations`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `presentation_items`
--
ALTER TABLE `presentation_items`
  MODIFY `id` int(128) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `tags`
--
ALTER TABLE `tags`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=135;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(250) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=320;
--
-- AUTO_INCREMENT for table `videos`
--
ALTER TABLE `videos`
  MODIFY `id` bigint(250) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
