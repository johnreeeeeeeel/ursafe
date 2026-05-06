-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 06, 2026 at 06:26 PM
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
CREATE DEFINER=`root`@`localhost` PROCEDURE `acceptLockerSlotApplication` (IN `p_application_id` INT)   BEGIN
    DECLARE v_slot_id INT;
    DECLARE v_slot_status VARCHAR(50);

    START TRANSACTION;

    SELECT slot_id
    INTO v_slot_id
    FROM locker_applications
    WHERE id = p_application_id;

    SELECT status
    INTO v_slot_status
    FROM locker_slots
    WHERE id = v_slot_id;

    IF v_slot_status IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Slot not found';

    ELSEIF v_slot_status <> 'Available' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Slot is no longer available';
    END IF;

    UPDATE locker_applications
    SET status = 'Accepted'
    WHERE id = p_application_id;

    UPDATE locker_slots
    SET status = 'Occupied'
    WHERE id = v_slot_id;

    COMMIT;
END$$

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

CREATE DEFINER=`root`@`localhost` PROCEDURE `applyLockerSlot` (IN `u_user_id` VARCHAR(255), IN `u_slot_id` INT)   BEGIN
    DECLARE active_application_count INT DEFAULT 0;
    DECLARE slot_status VARCHAR(50);

    SELECT status
    INTO slot_status
    FROM locker_slots
    WHERE id = u_slot_id;

    IF slot_status IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Slot not found';

    ELSEIF slot_status <> 'Available' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'This slot is not available for application';
    END IF;

    SELECT COUNT(*)
    INTO active_application_count
    FROM locker_applications
    WHERE user_id = u_user_id
      AND slot_id = u_slot_id
      AND status IN ('Pending', 'Accepted');

    IF active_application_count > 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'You already have an active application for this slot';
    ELSE
        INSERT INTO locker_applications (
            user_id,
            slot_id,
            status
        )
        VALUES (
            u_user_id,
            u_slot_id,
            'Pending'
        );
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `cancelLockerSlotApplication` (IN `p_application_id` INT)   BEGIN
    UPDATE locker_applications 
    SET status = 'Cancelled'
    WHERE id = p_application_id
      AND status = 'Pending';
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `getLockerApplicationById` (IN `p_application_id` INT)   BEGIN
    SELECT 
        user_id,
        slot_id
    FROM locker_applications
    WHERE id = p_application_id
    LIMIT 1;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getLockerLocations` ()   BEGIN
    SELECT id, location FROM locker_locations;
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `getLockerSlotApplication` ()   BEGIN
    SELECT
        la.id AS application_id,
        la.user_id,
        la.slot_id,
        la.status,

        TRIM(CONCAT(
            u.firstname, ' ',
            IFNULL(CONCAT(u.middlename, ' '), ''),
            u.lastname
        )) AS fullname,
        u.email,

        ls.slot_number,

        ll.location,

        ls.size_id,
        lsz.size,
        lsz.price

    FROM locker_applications la

    INNER JOIN users u ON la.user_id = u.id

    INNER JOIN locker_slots ls ON la.slot_id = ls.id

    INNER JOIN locker_locations ll ON ls.location_id = ll.id

    INNER JOIN locker_sizes lsz ON ls.size_id = lsz.id

    ORDER BY la.id DESC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getSearchFilterLockerLocation` (IN `p_search` VARCHAR(255), IN `p_filter` VARCHAR(10))   BEGIN
    SELECT id, location
    FROM locker_locations
    WHERE (p_search IS NULL OR p_search = '' 
           OR location LIKE CONCAT('%', p_search, '%'))
    ORDER BY
        CASE 
            WHEN p_filter = 'desc' THEN location
        END DESC,
        CASE 
            WHEN p_filter = 'asc' OR p_filter IS NULL OR p_filter = '' THEN location
        END ASC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getSearchFilterUsers` (IN `searchTerm` VARCHAR(255), IN `sortOrder` VARCHAR(255))   BEGIN
    SELECT
        id,
        firstname,
        middlename,
        lastname,
        TRIM(CONCAT(firstname, ' ',
            IFNULL(CONCAT(middlename, ' '), ''),
            lastname
        )) AS fullname,
        sex,
        dob,
        institute,
        program,
        username,
        email
    FROM users
    WHERE (
        searchTerm IS NULL OR searchTerm = ''
        OR id LIKE CONCAT('%', searchTerm, '%')
        OR firstname LIKE CONCAT('%', searchTerm, '%')
        OR middlename LIKE CONCAT('%', searchTerm, '%')
        OR lastname LIKE CONCAT('%', searchTerm, '%')
        OR username LIKE CONCAT('%', searchTerm, '%')
        OR email LIKE CONCAT('%', searchTerm, '%')
        OR TRIM(CONCAT(firstname, ' ',
            IFNULL(CONCAT(middlename, ' '), ''),
            lastname
        )) LIKE CONCAT('%', searchTerm, '%')
    )
    ORDER BY
        CASE
            WHEN sortOrder = 'desc' THEN TRIM(CONCAT(firstname, ' ',
                IFNULL(CONCAT(middlename, ' '), ''),
                lastname))
        END DESC,

        CASE
            WHEN sortOrder = 'asc'
              OR sortOrder = ''
              OR sortOrder IS NULL THEN TRIM(CONCAT(firstname, ' ',
                IFNULL(CONCAT(middlename, ' '), ''),
                lastname))
        END ASC;
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

        TRIM(CONCAT(
            firstname, ' ',
            IFNULL(CONCAT(middlename, ' '), ''),
            lastname
        )) AS fullname,

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

CREATE DEFINER=`root`@`localhost` PROCEDURE `getUserLockerSlotApplicationDetails` (IN `p_slot_id` INT)   BEGIN
    SELECT
        ll.location,
        ls.slot_number,
        sz.size,
        sz.price
    FROM locker_slots ls
    INNER JOIN locker_locations ll ON ls.location_id = ll.id
    INNER JOIN locker_sizes sz ON ls.size_id = sz.id
    WHERE ls.id = p_slot_id
    LIMIT 1;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getUserLockerSlotApplicationId` (IN `p_application_id` INT)   BEGIN
    SELECT
        slot_id,
        user_id
    FROM locker_applications
    WHERE id = p_application_id
    LIMIT 1;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getUserLockerSlotApplications` (IN `p_user_id` VARCHAR(255))   BEGIN
    SELECT
        la.id AS application_id,
        la.status,

        ls.slot_number,

        ll.location,

        sz.size,
        sz.price

    FROM locker_applications la

    INNER JOIN locker_slots ls ON la.slot_id = ls.id

    INNER JOIN locker_locations ll ON ls.location_id = ll.id

    INNER JOIN locker_sizes sz ON ls.size_id = sz.id

    WHERE la.user_id = p_user_id

    ORDER BY la.id DESC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getUsers` ()   BEGIN
    SELECT
        id,
        TRIM(CONCAT(firstname, ' ', IFNULL(CONCAT(middlename, ' '), ''), lastname)) AS fullname,
        sex,
        dob,
        institute,
        program,
        username,
        email
    FROM users
    ORDER BY lastname ASC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `rejectLockerSlotApplication` (IN `p_application_id` INT)   BEGIN
    DECLARE v_status VARCHAR(50);

    SELECT status
    INTO v_status
    FROM locker_applications
    WHERE id = p_application_id;

    IF v_status IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Application not found';

    ELSEIF v_status <> 'Pending' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Only pending applications can be rejected';
    END IF;

    UPDATE locker_applications
    SET status = 'Rejected'
    WHERE id = p_application_id;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `revokeLockerSlotApplication` (IN `p_application_id` INT)   BEGIN
    DECLARE v_slot_id INT;
    DECLARE v_status VARCHAR(50);

    START TRANSACTION;

    SELECT slot_id, status
    INTO v_slot_id, v_status
    FROM locker_applications
    WHERE id = p_application_id;

    IF v_status IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Application not found';

    ELSEIF v_status <> 'Accepted' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Only accepted applications can be revoked';
    END IF;

    UPDATE locker_applications
    SET status = 'Revoked'
    WHERE id = p_application_id;

    UPDATE locker_slots
    SET status = 'Available'
    WHERE id = v_slot_id;

    COMMIT;

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
-- Table structure for table `locker_applications`
--

CREATE TABLE `locker_applications` (
  `id` int(11) NOT NULL,
  `user_id` varchar(255) NOT NULL,
  `slot_id` int(11) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(5, 'AB 1st Floor'),
(2, 'AB 2nd Floor'),
(3, 'AB 3rd Floor'),
(4, 'AB 4th Floor');

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
(1, 'Small', 80.00),
(2, 'Medium ', 100.00),
(3, 'Large', 150.00);

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
(3, 1, 5, 1, 'Available'),
(4, 2, 5, 1, 'Available'),
(5, 3, 5, 1, 'Available'),
(6, 1, 2, 1, 'Available'),
(7, 2, 2, 1, 'Available'),
(8, 3, 2, 1, 'Available'),
(9, 1, 3, 1, 'Available'),
(10, 2, 3, 1, 'Available'),
(11, 3, 3, 1, 'Available'),
(12, 1, 4, 1, 'Available'),
(13, 2, 4, 1, 'Available'),
(14, 3, 4, 1, 'Available');

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
('2024-11468', 'Getalla', 'Joviet', 'Batang', 'Male', '2003-02-19', 'Institute of Computing', 'Bachelor of Science in Information System', 'jovietgetalla', 'getalla.joviet@dnscedu.onmicrosoft.com', '$2y$10$qB0M/V3TV1qettojErSu4OP5hZTFEkoHPDlfanT2Xu/S6vMRNbrdO'),
('2024-31214', 'Malbino', 'Feby Johnrel', 'Roferos', 'Male', '2006-02-11', 'Institute of Computing', 'Bachelor of Science in Information Technology', 'febyjohnrelmalbino', 'malbino.febyjohnrel@dnscedu.onmicrosoft.com', '$2y$10$/10IDBQmyGIRXuLvzy3hy.GY.VHdsB9w1.r4Xg7dA31jyc7uKIWVO'),
('2024-98026', 'Bulay-og', 'Jason', 'Dy', 'Male', '2004-11-23', 'Institute of Computing', 'Bachelor of Science in Information Technology', 'jasonbulay-og', 'bulay-og.jason@dnscedu.onmicrosoft.com', '$2y$10$8uuGsxbl7lAatQXWmpBHZOgPEEbzDpV2sBVy5lJTJDfhP.KDqXyM2');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `locker_applications`
--
ALTER TABLE `locker_applications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `UserID` (`user_id`),
  ADD KEY `LockerSlotID` (`slot_id`);

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
-- AUTO_INCREMENT for table `locker_applications`
--
ALTER TABLE `locker_applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `locker_locations`
--
ALTER TABLE `locker_locations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `locker_sizes`
--
ALTER TABLE `locker_sizes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `locker_slots`
--
ALTER TABLE `locker_slots`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `locker_applications`
--
ALTER TABLE `locker_applications`
  ADD CONSTRAINT `LockerSlotID` FOREIGN KEY (`slot_id`) REFERENCES `locker_slots` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `UserID` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

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
