SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


CREATE TABLE `aggregates` (
  `id` int(11) NOT NULL,
  `timestamp` int(11) NOT NULL,
  `aggregate_type_id` int(11) NOT NULL,
  `partial` tinyint(4) NOT NULL,
  `esp8266id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `aggregate_types` (
  `id` int(11) NOT NULL,
  `name` varchar(16) NOT NULL,
  `type` varchar(16) NOT NULL,
  `rotation_days` int(11) NOT NULL,
  `window_size_minutes` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `aggregate_types` (`id`, `name`, `type`, `rotation_days`, `window_size_minutes`) VALUES
(1, 'day', 'average', 2, 3),
(2, 'week', 'average', 14, 15),
(3, 'month', 'average', 62, 90),
(4, 'year', 'average', 732, 720);

CREATE TABLE `parameters` (
  `id` int(11) NOT NULL,
  `name` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `parameters` (`id`, `name`) VALUES
(1, 'pm10'),
(2, 'pm25'),
(3, 'temperature'),
(4, 'humidity'),
(5, 'pressure'),
(6, 'heater_temperature'),
(7, 'heater_humidity');

CREATE TABLE `records` (
  `id` int(11) NOT NULL,
  `timestamp` int(11) NOT NULL,
  `aggregate_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `record_values` (
  `record_id` int(11) NOT NULL,
  `parameter_id` int(11) NOT NULL,
  `value` decimal(6,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `aggregates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `aggregate_type_id` (`aggregate_type_id`);

ALTER TABLE `aggregate_types`
  ADD PRIMARY KEY (`id`),
  ADD KEY `name` (`name`);

ALTER TABLE `parameters`
  ADD PRIMARY KEY (`id`),
  ADD KEY `name` (`name`);

ALTER TABLE `records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `aggregate_id` (`aggregate_id`);

ALTER TABLE `record_values`
  ADD PRIMARY KEY (`record_id`,`parameter_id`),
  ADD KEY `parameter_id` (`parameter_id`);


ALTER TABLE `aggregates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `aggregate_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

ALTER TABLE `parameters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

ALTER TABLE `records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;


ALTER TABLE `aggregates`
  ADD CONSTRAINT `aggregates_type` FOREIGN KEY (`aggregate_type_id`) REFERENCES `aggregate_types` (`id`);

ALTER TABLE `records`
  ADD CONSTRAINT `records_aggregate` FOREIGN KEY (`aggregate_id`) REFERENCES `aggregates` (`id`) ON DELETE CASCADE;

ALTER TABLE `record_values`
  ADD CONSTRAINT `record_values_parameter` FOREIGN KEY (`parameter_id`) REFERENCES `parameters` (`id`),
  ADD CONSTRAINT `record_values_parent_record` FOREIGN KEY (`record_id`) REFERENCES `records` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
