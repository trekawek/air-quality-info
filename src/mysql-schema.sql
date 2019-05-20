SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

CREATE DATABASE IF NOT EXISTS `air_quality_info` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `air_quality_info`;

CREATE TABLE `devices` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `esp8266_id` int(11) NOT NULL,
  `position` int(11) NOT NULL,
  `http_username` varchar(256) NOT NULL,
  `http_password` varchar(256) NOT NULL,
  `name` varchar(256) NOT NULL,
  `description` varchar(256) NOT NULL,
  `hidden` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `device_mapping` (
  `id` int(11) NOT NULL,
  `device_id` int(11) NOT NULL,
  `db_name` varchar(32) NOT NULL,
  `json_name` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `json_updates` (
  `device_id` int(11) NOT NULL,
  `timestamp` int(11) NOT NULL,
  `data` varchar(2048) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `records` (
  `device_id` int(11) NOT NULL,
  `timestamp` int(11) NOT NULL,
  `pm25` decimal(6,2) DEFAULT NULL,
  `pm10` decimal(6,2) DEFAULT NULL,
  `temperature` decimal(5,2) DEFAULT NULL,
  `humidity` decimal(5,2) DEFAULT NULL,
  `pressure` decimal(6,2) DEFAULT NULL,
  `heater_temperature` decimal(5,2) DEFAULT NULL,
  `heater_humidity` decimal(5,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(254) NOT NULL,
  `password_hash` varchar(128) NOT NULL,
  `domain` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `devices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `esp8266_id` (`esp8266_id`);

ALTER TABLE `device_mapping`
  ADD PRIMARY KEY (`id`),
  ADD KEY `device_id` (`device_id`);

ALTER TABLE `json_updates`
  ADD PRIMARY KEY (`device_id`,`timestamp`),
  ADD KEY `timestamp` (`timestamp`),
  ADD KEY `device_id` (`device_id`);

ALTER TABLE `records`
  ADD PRIMARY KEY (`device_id`,`timestamp`),
  ADD KEY `timestamp` (`timestamp`),
  ADD KEY `device_id` (`device_id`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `devices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `device_mapping`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
