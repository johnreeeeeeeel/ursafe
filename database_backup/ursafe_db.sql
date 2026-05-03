-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 03, 2026 at 05:49 PM
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
-- Database: `ursafe_db`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `addLockerLocation` (IN `p_location` VARCHAR(255))   BEGIN
    INSERT INTO locker_locations (location)
    VALUES (p_location);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `addLockerSize` (IN `p_size` VARCHAR(255), IN `p_price` DOUBLE(10,2))   BEGIN
    INSERT INTO locker_sizes (size, price)
    VALUES (p_size, p_price);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `addLockerSlot` (IN `p_slot_number` INT, IN `p_location_id` INT, IN `p_size_id` INT)   BEGIN
    INSERT INTO locker_slots (
        slot_number,
        location_id,
        size_id,
        status
    )
    VALUES (
        p_slot_number,
        p_location_id,
        p_size_id,
        'Available'
    );
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `deleteLockerLocation` (IN `p_id` INT)   BEGIN
    DELETE FROM locker_locations
    WHERE id = p_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `deleteLockerSize` (IN `p_id` INT)   BEGIN
    DELETE FROM locker_sizes
    WHERE id = p_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `deleteLockerSlot` (IN `p_id` INT)   BEGIN
    DELETE FROM locker_slots
    WHERE id = p_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getAdminByEmail` (IN `p_email` VARCHAR(255))   BEGIN
    SELECT
        id,
        NULL AS lastname,
        NULL AS firstname,
        NULL AS middlename,
        NULL AS sex,
        NULL AS dob,
        NULL AS institute,
        NULL AS program,
        username,
        email,
        password
    FROM admin
    WHERE email = p_email
    LIMIT 1;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getLockerLocations` ()   BEGIN
    SELECT id, location FROM locker_locations;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getLockerLocationsASC` ()   BEGIN
    SELECT id, location
    FROM locker_locations
    ORDER BY location ASC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getLockerLocationsDESC` ()   BEGIN
    SELECT id, location
    FROM locker_locations
    ORDER BY location DESC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getLockers` ()   BEGIN
    SELECT * FROM locker_slots_view;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getLockersByLocation` (IN `p_location_id` INT)   BEGIN
    SELECT 
        ls.id,
        ls.slot_number,
        lsz.size,
        lsz.price,
        ls.status
    FROM locker_slots ls
    INNER JOIN locker_sizes lsz ON ls.size_id = lsz.id
    WHERE ls.location_id = p_location_id
    ORDER BY ls.slot_number ASC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getLockerSizes` ()   BEGIN
    SELECT id, size, price
    FROM locker_sizes
    ORDER BY id ASC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getUserByEmail` (IN `u_email` VARCHAR(255))   BEGIN
    SELECT
        id,
        lastname,
        firstname,
        middlename,
        sex,
        dob,
        institute,
        program,
        username,
        email,
        password
    FROM users
    WHERE email = u_email
    LIMIT 1;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getUserById` (IN `u_id` VARCHAR(255))   BEGIN
    SELECT
        id,
        lastname,
        firstname,
        middlename,
        sex,
        dob,
        institute,
        program,
        username,
        email,
        password
    FROM users
    WHERE id = u_id
    LIMIT 1;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `searchFilterUsers` (IN `u_search` VARCHAR(255), IN `u_filter` VARCHAR(10))   BEGIN
    SELECT *
    FROM view_users
    WHERE
        (u_search IS NULL OR u_search = '' OR
            firstname LIKE CONCAT('%', u_search, '%')
            OR middlename LIKE CONCAT('%', u_search, '%')
            OR lastname LIKE CONCAT('%', u_search, '%')
        )

    ORDER BY
        CASE
            WHEN u_filter = 'old' THEN id
            ELSE NULL
        END ASC,

        CASE
            WHEN u_filter = 'new' THEN id
            ELSE NULL
        END DESC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `searchLockerLocation` (IN `searchTerm` VARCHAR(100))   BEGIN
    SELECT id, location
    FROM locker_locations
    WHERE location LIKE CONCAT('%', searchTerm, '%')
    ORDER BY location ASC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `updateLockerLocation` (IN `p_id` INT, IN `p_location` VARCHAR(255))   BEGIN
    UPDATE locker_locations
    SET location = p_location
    WHERE id = p_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `updateLockerSize` (IN `p_id` INT, IN `p_size` VARCHAR(255), IN `p_price` DOUBLE)   BEGIN
    UPDATE locker_sizes
    SET size = p_size,
        price = p_price
    WHERE id = p_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `updateLockerSlot` (IN `p_id` INT, IN `p_slot_number` INT, IN `p_status` VARCHAR(255))   BEGIN
    UPDATE locker_slots
    SET 
        slot_number = p_slot_number,
        status = p_status
    WHERE id = p_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `updateUserPassword` (IN `u_email` VARCHAR(255), IN `u_password` VARCHAR(255))   BEGIN
    UPDATE users
    SET password = u_password
    WHERE email = u_email
    LIMIT 1;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `email`, `password`) VALUES
(1, 'admin', 'admin@dnsc.xxx.xx', '$2y$10$fSWQMOkwfhp3X58u/S6izO39RaWc5rkn1/CZObpM4qiM35NJkau5e');

-- --------------------------------------------------------

--
-- Table structure for table `locker_locations`
--

CREATE TABLE `locker_locations` (
  `id` int(11) NOT NULL,
  `location` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `locker_locations`
--

INSERT INTO `locker_locations` (`id`, `location`) VALUES
(19, 'AB 1st Floor'),
(20, 'AB 2nd Floor'),
(21, 'AB 3rd Floor'),
(22, 'AB 4th Floor');

-- --------------------------------------------------------

--
-- Table structure for table `locker_sizes`
--

CREATE TABLE `locker_sizes` (
  `id` int(11) NOT NULL,
  `size` varchar(255) NOT NULL,
  `price` double(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `locker_sizes`
--

INSERT INTO `locker_sizes` (`id`, `size`, `price`) VALUES
(2, 'Medium', 100.00),
(3, 'Large', 150.00),
(9, 'Small', 80.00);

-- --------------------------------------------------------

--
-- Table structure for table `locker_slots`
--

CREATE TABLE `locker_slots` (
  `id` int(11) NOT NULL,
  `slot_number` int(11) NOT NULL,
  `location_id` int(11) NOT NULL,
  `size_id` int(11) NOT NULL,
  `status` varchar(255) DEFAULT 'Available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `locker_slots`
--

INSERT INTO `locker_slots` (`id`, `slot_number`, `location_id`, `size_id`, `status`) VALUES
(17, 1, 19, 9, 'Available'),
(18, 2, 19, 9, 'Available');

-- --------------------------------------------------------

--
-- Stand-in structure for view `locker_slots_view`
-- (See below for the actual view)
--
CREATE TABLE `locker_slots_view` (
`id` int(11)
,`slot_number` int(11)
,`location` varchar(255)
,`size` varchar(255)
,`price` double(10,2)
,`status` varchar(255)
);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `middlename` varchar(255) DEFAULT NULL,
  `sex` varchar(255) NOT NULL,
  `dob` date NOT NULL,
  `institute` varchar(255) NOT NULL,
  `program` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `lastname`, `firstname`, `middlename`, `sex`, `dob`, `institute`, `program`, `username`, `email`, `password`) VALUES
('2024-01172', 'Malbino', 'Feby Johnrel', 'Roferos', 'Male', '2006-02-11', '1', '1', 'febyjohnrelmalbino', 'malbino.febyjohnrel@dnscedu.onmicrosoft.com', '$2y$10$2V5En3qUiY0JP19.V7jzBu4LUqAqv4H3c1d4PWUcKz2n0xJMfjIKi');

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_users`
-- (See below for the actual view)
--
CREATE TABLE `view_users` (
`id` varchar(255)
,`fullname` text
,`firstname` varchar(255)
,`lastname` varchar(255)
,`middlename` varchar(255)
,`sex` varchar(255)
,`dob` date
,`institute_id` varchar(255)
,`institute` varchar(255)
,`program_id` varchar(255)
,`program` varchar(255)
,`username` varchar(255)
,`email` varchar(255)
);

-- --------------------------------------------------------

--
-- Structure for view `locker_slots_view`
--
DROP TABLE IF EXISTS `locker_slots_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `locker_slots_view`  AS SELECT `ls`.`id` AS `id`, `ls`.`slot_number` AS `slot_number`, `ll`.`location` AS `location`, `lsz`.`size` AS `size`, `lsz`.`price` AS `price`, `ls`.`status` AS `status` FROM ((`locker_slots` `ls` join `locker_locations` `ll` on(`ls`.`location_id` = `ll`.`id`)) join `locker_sizes` `lsz` on(`ls`.`size_id` = `lsz`.`id`)) ;

-- --------------------------------------------------------

--
-- Structure for view `view_users`
--
DROP TABLE IF EXISTS `view_users`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_users`  AS SELECT `u`.`id` AS `id`, trim(concat(`u`.`firstname`,' ',ifnull(concat(`u`.`middlename`,' '),''),`u`.`lastname`)) AS `fullname`, `u`.`firstname` AS `firstname`, `u`.`lastname` AS `lastname`, `u`.`middlename` AS `middlename`, `u`.`sex` AS `sex`, `u`.`dob` AS `dob`, `u`.`institute` AS `institute_id`, `i`.`description` AS `institute`, `u`.`program` AS `program_id`, `p`.`description` AS `program`, `u`.`username` AS `username`, `u`.`email` AS `email` FROM ((`users` `u` left join `campus_db`.`institutes` `i` on(`u`.`institute` = `i`.`id`)) left join `campus_db`.`programs` `p` on(`u`.`program` = `p`.`id`)) ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `locker_locations`
--
ALTER TABLE `locker_locations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `location` (`location`);

--
-- Indexes for table `locker_sizes`
--
ALTER TABLE `locker_sizes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `size` (`size`);

--
-- Indexes for table `locker_slots`
--
ALTER TABLE `locker_slots`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_slot_number` (`slot_number`,`location_id`),
  ADD KEY `slot_location` (`location_id`),
  ADD KEY `slot_size` (`size_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD UNIQUE KEY `uniqueStudentID` (`id`),
  ADD UNIQUE KEY `uniqueUsername` (`username`),
  ADD UNIQUE KEY `uniqueEmail` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `locker_locations`
--
ALTER TABLE `locker_locations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `locker_sizes`
--
ALTER TABLE `locker_sizes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `locker_slots`
--
ALTER TABLE `locker_slots`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `locker_slots`
--
ALTER TABLE `locker_slots`
  ADD CONSTRAINT `slot_location` FOREIGN KEY (`location_id`) REFERENCES `locker_locations` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `slot_size` FOREIGN KEY (`size_id`) REFERENCES `locker_sizes` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
