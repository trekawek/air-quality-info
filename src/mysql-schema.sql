-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 10.135.14.82
-- Generation Time: Feb 08, 2022 at 09:32 PM
-- Server version: 8.0.28-0ubuntu0.20.04.3
-- PHP Version: 7.4.20

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `air_quality_info`
--

-- --------------------------------------------------------

--
-- Table structure for table `aggregates`
--

CREATE TABLE `aggregates` (
  `device_id` int NOT NULL,
  `timestamp` int NOT NULL,
  `resolution` int NOT NULL,
  `pm25` decimal(6,2) DEFAULT NULL,
  `pm10` decimal(6,2) DEFAULT NULL,
  `pm1` decimal(6,2) DEFAULT NULL,
  `pm4` decimal(6,2) DEFAULT NULL,
  `n05` decimal(6,2) DEFAULT NULL,
  `n1` decimal(6,2) DEFAULT NULL,
  `n25` decimal(6,2) DEFAULT NULL,
  `n4` decimal(6,2) DEFAULT NULL,
  `n10` decimal(6,2) DEFAULT NULL,
  `co2` decimal(6,2) DEFAULT NULL,
  `temperature` decimal(5,2) DEFAULT NULL,
  `humidity` decimal(5,2) DEFAULT NULL,
  `pressure` decimal(6,2) DEFAULT NULL,
  `heater_temperature` decimal(5,2) DEFAULT NULL,
  `heater_humidity` decimal(5,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `attachments`
--

CREATE TABLE `attachments` (
  `user_id` int NOT NULL,
  `name` varchar(256) NOT NULL,
  `filename` varchar(256) NOT NULL,
  `length` int NOT NULL,
  `mime` varchar(64) NOT NULL,
  `data` mediumblob NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `custom_domains`
--

CREATE TABLE `custom_domains` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `fqdn` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `devices`
--

CREATE TABLE `devices` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `update_mode` enum('push','pull') NOT NULL DEFAULT 'push',
  `esp8266_id` bigint DEFAULT NULL,
  `http_username` varchar(256) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `http_password` varchar(256) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `api_key` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `name` varchar(256) NOT NULL,
  `description` varchar(256) NOT NULL,
  `extra_description` varchar(512) DEFAULT NULL,
  `default_device` tinyint(1) NOT NULL,
  `location_provided` tinyint(1) NOT NULL DEFAULT '0',
  `expose_location` tinyint(1) NOT NULL DEFAULT '0',
  `lat` decimal(17,14) DEFAULT NULL,
  `lng` decimal(17,14) DEFAULT NULL,
  `radius` decimal(5,1) NOT NULL DEFAULT '250.0',
  `elevation` int DEFAULT NULL,
  `temperature_offset` decimal(4,2) NOT NULL DEFAULT '0.00',
  `csv_fields` varchar(1024) NOT NULL DEFAULT 'pm25,pm10,temperature,pressure,humidity,heater_temperature,heater_humidity',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_update` int DEFAULT NULL,
  `assign_token` varchar(32) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `device_hierarchy`
--

CREATE TABLE `device_hierarchy` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `parent_id` int DEFAULT NULL,
  `position` int NOT NULL,
  `name` varchar(256) DEFAULT NULL,
  `description` varchar(256) DEFAULT NULL,
  `device_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `device_mapping`
--

CREATE TABLE `device_mapping` (
  `id` int NOT NULL,
  `device_id` int NOT NULL,
  `db_name` varchar(32) NOT NULL,
  `json_name` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `device_sensors`
--

CREATE TABLE `device_sensors` (
  `device_id` int NOT NULL,
  `sensor_id` int NOT NULL,
  `type` enum('sensor.community','smogtok','syngeos','gios') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'sensor.community'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `json_updates`
--

CREATE TABLE `json_updates` (
  `device_id` int NOT NULL,
  `timestamp` int NOT NULL,
  `data` varchar(2048) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `records`
--

CREATE TABLE `records` (
  `device_id` int NOT NULL,
  `timestamp` int NOT NULL,
  `pm25` decimal(6,2) DEFAULT NULL,
  `pm10` decimal(6,2) DEFAULT NULL,
  `pm1` decimal(6,2) DEFAULT NULL,
  `pm4` decimal(6,2) DEFAULT NULL,
  `n05` decimal(6,2) DEFAULT NULL,
  `n1` decimal(6,2) DEFAULT NULL,
  `n25` decimal(6,2) DEFAULT NULL,
  `n4` decimal(6,2) DEFAULT NULL,
  `n10` decimal(6,2) DEFAULT NULL,
  `co2` decimal(6,2) DEFAULT NULL,
  `temperature` decimal(5,2) DEFAULT NULL,
  `humidity` decimal(5,2) DEFAULT NULL,
  `pressure` decimal(6,2) DEFAULT NULL,
  `heater_temperature` decimal(5,2) DEFAULT NULL,
  `heater_humidity` decimal(5,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `templates`
--

CREATE TABLE `templates` (
  `user_id` int NOT NULL,
  `template_name` varchar(32) NOT NULL,
  `template` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `email` varchar(254) NOT NULL,
  `password_hash` varchar(128) NOT NULL,
  `domain` varchar(256) NOT NULL,
  `sensor_widget` tinyint(1) NOT NULL DEFAULT '0',
  `all_widget` tinyint(1) NOT NULL DEFAULT '0',
  `redirect_root` varchar(255) DEFAULT NULL,
  `timezone` varchar(64) NOT NULL DEFAULT 'Europe/Warsaw',
  `allow_sensor_community` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password_hash`, `domain`, `sensor_widget`, `all_widget`, `redirect_root`, `timezone`, `allow_sensor_community`, `created_at`) VALUES
(9999, 'trekawek+nam@gmail.com', '', '', 0, 0, NULL, 'Europe/Warsaw', 0, '2022-04-17 18:31:36');

-- --------------------------------------------------------

--
-- Table structure for table `user_tokens`
--

CREATE TABLE `user_tokens` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `token` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `valid_until` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `widgets`
--

CREATE TABLE `widgets` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `title` varchar(512) NOT NULL,
  `template` enum('horizontal','vertical') NOT NULL DEFAULT 'vertical'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `aggregates`
--
ALTER TABLE `aggregates`
  ADD PRIMARY KEY (`device_id`,`timestamp`,`resolution`),
  ADD KEY `timestamp` (`timestamp`),
  ADD KEY `device_id` (`device_id`),
  ADD KEY `resolution` (`resolution`);

--
-- Indexes for table `attachments`
--
ALTER TABLE `attachments`
  ADD PRIMARY KEY (`user_id`,`name`);

--
-- Indexes for table `custom_domains`
--
ALTER TABLE `custom_domains`
  ADD PRIMARY KEY (`id`),
  ADD KEY `custom_domains_user_id` (`user_id`);

--
-- Indexes for table `devices`
--
ALTER TABLE `devices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `api_key` (`api_key`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `esp8266_id` (`esp8266_id`),
  ADD KEY `default_device` (`default_device`),
  ADD KEY `id` (`id`,`user_id`);

--
-- Indexes for table `device_hierarchy`
--
ALTER TABLE `device_hierarchy`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `device_id` (`device_id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Indexes for table `device_mapping`
--
ALTER TABLE `device_mapping`
  ADD PRIMARY KEY (`id`),
  ADD KEY `device_id` (`device_id`);

--
-- Indexes for table `device_sensors`
--
ALTER TABLE `device_sensors`
  ADD PRIMARY KEY (`device_id`,`sensor_id`),
  ADD KEY `device_id` (`device_id`);

--
-- Indexes for table `json_updates`
--
ALTER TABLE `json_updates`
  ADD PRIMARY KEY (`device_id`,`timestamp`),
  ADD KEY `timestamp` (`timestamp`),
  ADD KEY `device_id` (`device_id`);

--
-- Indexes for table `records`
--
ALTER TABLE `records`
  ADD PRIMARY KEY (`device_id`,`timestamp`),
  ADD KEY `timestamp` (`timestamp`),
  ADD KEY `device_id` (`device_id`);

--
-- Indexes for table `templates`
--
ALTER TABLE `templates`
  ADD PRIMARY KEY (`user_id`,`template_name`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `domain` (`domain`);

--
-- Indexes for table `user_tokens`
--
ALTER TABLE `user_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `user_tokens_user_id_fkey` (`user_id`);

--
-- Indexes for table `widgets`
--
ALTER TABLE `widgets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `widgets_user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `custom_domains`
--
ALTER TABLE `custom_domains`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `devices`
--
ALTER TABLE `devices`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `device_hierarchy`
--
ALTER TABLE `device_hierarchy`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `device_mapping`
--
ALTER TABLE `device_mapping`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_tokens`
--
ALTER TABLE `user_tokens`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `widgets`
--
ALTER TABLE `widgets`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attachments`
--
ALTER TABLE `attachments`
  ADD CONSTRAINT `attachments_user_id_fkey` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `custom_domains`
--
ALTER TABLE `custom_domains`
  ADD CONSTRAINT `custom_domains_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `devices`
--
ALTER TABLE `devices`
  ADD CONSTRAINT `devices_user_id_fkey` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Constraints for table `device_hierarchy`
--
ALTER TABLE `device_hierarchy`
  ADD CONSTRAINT `device_hierarchy_device_id` FOREIGN KEY (`device_id`) REFERENCES `devices` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `device_hierarchy_parent_id` FOREIGN KEY (`parent_id`) REFERENCES `device_hierarchy` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `device_hierarchy_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `device_mapping`
--
ALTER TABLE `device_mapping`
  ADD CONSTRAINT `device_mapping_device_id_fkey` FOREIGN KEY (`device_id`) REFERENCES `devices` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Constraints for table `device_sensors`
--
ALTER TABLE `device_sensors`
  ADD CONSTRAINT `device_sensors_device_id` FOREIGN KEY (`device_id`) REFERENCES `devices` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `json_updates`
--
ALTER TABLE `json_updates`
  ADD CONSTRAINT `json_updates_device_id_fkey` FOREIGN KEY (`device_id`) REFERENCES `devices` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Constraints for table `records`
--
ALTER TABLE `records`
  ADD CONSTRAINT `records_device_id_fkey` FOREIGN KEY (`device_id`) REFERENCES `devices` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Constraints for table `templates`
--
ALTER TABLE `templates`
  ADD CONSTRAINT `templates_user_id_fkey` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_tokens`
--
ALTER TABLE `user_tokens`
  ADD CONSTRAINT `user_tokens_user_id_fkey` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `widgets`
--
ALTER TABLE `widgets`
  ADD CONSTRAINT `widgets_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;
