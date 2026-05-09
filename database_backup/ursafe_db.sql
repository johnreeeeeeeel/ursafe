-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 09, 2026 at 05:55 PM
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
CREATE DEFINER=`root`@`localhost` PROCEDURE `acceptLockerApplication` (IN `p_application_id` INT)   BEGIN
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `activateUserAccount` (IN `p_id` VARCHAR(255), IN `p_lastname` VARCHAR(255), IN `p_firstname` VARCHAR(255), IN `p_middlename` VARCHAR(255), IN `p_sex` VARCHAR(6), IN `p_dob` DATE, IN `p_institute` VARCHAR(255), IN `p_program` VARCHAR(255), IN `p_username` VARCHAR(255), IN `p_email` VARCHAR(255), IN `p_password` VARCHAR(255))   BEGIN
    INSERT INTO users (
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
    )
    VALUES (
        p_id,
        p_lastname,
        p_firstname,
        p_middlename,
        p_sex,
        p_dob,
        p_institute,
        p_program,
        p_username,
        p_email,
        p_password
    );
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `applyLocker` (IN `u_user_id` VARCHAR(255), IN `u_slot_id` INT)   BEGIN
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `cancelLockerApplication` (IN `p_application_id` INT)   BEGIN
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `getLockerApplication` ()   BEGIN
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `getLockerLogs` (IN `p_limit` INT, IN `p_offset` INT)   BEGIN
    SELECT
        id,
        action,
        description,
        created_at
    FROM locker_logs
    ORDER BY created_at DESC
    LIMIT p_offset, p_limit;
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `getMyLockerApplications` (IN `p_user_id` VARCHAR(255))   BEGIN
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `getSearchFilterLockerApplication` (IN `p_search` VARCHAR(255), IN `p_filter` VARCHAR(10))   BEGIN
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

    WHERE 
        (p_search IS NULL OR p_search = '')
        OR CONCAT(u.firstname, ' ', IFNULL(CONCAT(u.middlename, ' '), ''), u.lastname)
        LIKE CONCAT('%', p_search, '%')

    ORDER BY
        CASE 
            WHEN p_filter = 'desc' THEN 
                CONCAT(u.firstname, ' ', IFNULL(CONCAT(u.middlename, ' '), ''), u.lastname)
        END DESC,

        CASE 
            WHEN p_filter = 'asc' OR p_filter IS NULL OR p_filter = '' THEN 
                CONCAT(u.firstname, ' ', IFNULL(CONCAT(u.middlename, ' '), ''), u.lastname)
        END ASC;

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

CREATE DEFINER=`root`@`localhost` PROCEDURE `getSearchFilterLockerSizes` (IN `p_search` VARCHAR(255), IN `p_filter` VARCHAR(10))   BEGIN
    SELECT id, size, price
    FROM locker_sizes
    WHERE (p_search IS NULL OR p_search = ''
           OR size LIKE CONCAT('%', p_search, '%'))
    ORDER BY
        CASE
            WHEN p_filter = 'desc' THEN size
        END DESC,
        CASE
            WHEN p_filter = 'asc' OR p_filter IS NULL OR p_filter = '' THEN size
        END ASC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getSearchFilterMyLockerApplication` (IN `p_user_id` VARCHAR(255), IN `p_search` VARCHAR(255), IN `p_filter` VARCHAR(10))   BEGIN
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
    AND (
        p_search IS NULL
        OR p_search = ''
        OR ll.location LIKE CONCAT('%', p_search, '%')
    )

    ORDER BY
        CASE
            WHEN p_filter = 'desc' THEN ll.location
        END DESC,

        CASE
            WHEN p_filter = 'asc'
              OR p_filter IS NULL
              OR p_filter = ''
            THEN ll.location
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `getUserAccountLogs` (IN `p_limit` INT, IN `p_offset` INT)   BEGIN
    SELECT
        id,
		action,
        description,
        created_at
    FROM user_account_logs
    ORDER BY created_at DESC
    LIMIT p_offset, p_limit;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getUserByEmail` (IN `p_email` VARCHAR(255))   BEGIN
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
    WHERE email = p_email
    LIMIT 1;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getUserById` (IN `p_id` VARCHAR(255))   BEGIN
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
    WHERE id = p_id
    LIMIT 1;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getUserLockerApplicationDetails` (IN `p_slot_id` INT)   BEGIN
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `getUserLockerApplicationId` (IN `p_application_id` INT)   BEGIN
    SELECT
        slot_id,
        user_id
    FROM locker_applications
    WHERE id = p_application_id
    LIMIT 1;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getUsers` ()   BEGIN
    SELECT
        id,
        firstname,
        middlename,
        lastname,
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `rejectLockerApplication` (IN `p_application_id` INT)   BEGIN
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `revokeLockerApplication` (IN `p_application_id` INT)   BEGIN
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `updateUserPassword` (IN `p_email` VARCHAR(255), IN `p_password` VARCHAR(255))   BEGIN
    UPDATE users
    SET password = p_password
    WHERE email = p_email
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

--
-- Triggers `locker_applications`
--
DELIMITER $$
CREATE TRIGGER `after_locker_acception` AFTER UPDATE ON `locker_applications` FOR EACH ROW BEGIN
    DECLARE action VARCHAR(255) DEFAULT 'Acception';
    DECLARE v_slot_number INT;
    DECLARE v_location VARCHAR(255);
    DECLARE description TEXT;

	IF NEW.status = 'Accepted' AND OLD.status <> 'Accepted' THEN
        SELECT ls.slot_number, ll.location INTO v_slot_number, v_location FROM locker_slots ls
        INNER JOIN locker_locations ll ON ls.location_id = ll.id
        WHERE ls.id = NEW.slot_id
        LIMIT 1;

        SET description = CONCAT(NEW.user_id, ' application on slot #', v_slot_number, ' at ', v_location, ' has been accepted.');

        INSERT INTO locker_logs (
            action,
            description
        ) VALUES (
            action,
            description
        );
	END IF;

END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_locker_application` AFTER INSERT ON `locker_applications` FOR EACH ROW BEGIN
    DECLARE action VARCHAR(255) DEFAULT 'Application';
    DECLARE v_slot_number INT;
    DECLARE v_location VARCHAR(255);
    DECLARE description TEXT;

	IF NEW.status = 'Pending' THEN
        SELECT ls.slot_number, ll.location INTO v_slot_number, v_location FROM locker_slots ls
        INNER JOIN locker_locations ll ON ls.location_id = ll.id
        WHERE ls.id = NEW.slot_id
        LIMIT 1;

        SET description = CONCAT(NEW.user_id, ' has applied on slot #', v_slot_number, ' at ', v_location, '.');

        INSERT INTO locker_logs (
            action,
            description
        ) VALUES (
            action,
            description
        );
	END IF;
    
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_locker_cancellation` AFTER UPDATE ON `locker_applications` FOR EACH ROW BEGIN
    DECLARE action VARCHAR(255) DEFAULT 'Cancellation';
    DECLARE v_slot_number INT;
    DECLARE v_location VARCHAR(255);
    DECLARE description TEXT;

	IF NEW.status = 'Cancelled' AND OLD.status <> 'Cancelled' THEN
        SELECT ls.slot_number, ll.location INTO v_slot_number, v_location FROM locker_slots ls
        INNER JOIN locker_locations ll ON ls.location_id = ll.id
        WHERE ls.id = NEW.slot_id
        LIMIT 1;

        SET description = CONCAT(NEW.user_id, ' has cancelled slot #', v_slot_number, ' at ', v_location, '.');

        INSERT INTO locker_logs (
            action,
            description
        ) VALUES (
            action,
            description
        );
	END IF;

END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_locker_rejection` AFTER UPDATE ON `locker_applications` FOR EACH ROW BEGIN
    DECLARE action VARCHAR(255) DEFAULT 'Rejection';
    DECLARE v_slot_number INT;
    DECLARE v_location VARCHAR(255);
    DECLARE description TEXT;

	IF NEW.status = 'Rejected' AND OLD.status <> 'Rejected' THEN
        SELECT ls.slot_number, ll.location INTO v_slot_number, v_location FROM locker_slots ls
        INNER JOIN locker_locations ll ON ls.location_id = ll.id
        WHERE ls.id = NEW.slot_id
        LIMIT 1;

        SET description = CONCAT(NEW.user_id, ' application on slot #', v_slot_number, ' at ', v_location, ' has been rejected.');

        INSERT INTO locker_logs (
            action,
            description
        ) VALUES (
            action,
            description
        );
	END IF;

END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_locker_revoking` AFTER UPDATE ON `locker_applications` FOR EACH ROW BEGIN
    DECLARE action VARCHAR(255) DEFAULT 'Revoking';
    DECLARE v_slot_number INT;
    DECLARE v_location VARCHAR(255);
    DECLARE description TEXT;

	IF NEW.status = 'Revoked' AND OLD.status <> 'Revoked' THEN
        SELECT ls.slot_number, ll.location INTO v_slot_number, v_location FROM locker_slots ls
        INNER JOIN locker_locations ll ON ls.location_id = ll.id
        WHERE ls.id = NEW.slot_id
        LIMIT 1;

        SET description = CONCAT(NEW.user_id, ' application on slot #', v_slot_number, ' at ', v_location, ' has been revoked.');

        INSERT INTO locker_logs (
            action,
            description
        ) VALUES (
            action,
            description
        );
	END IF;

END
$$
DELIMITER ;

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

--
-- Triggers `locker_locations`
--
DELIMITER $$
CREATE TRIGGER `after_locker_location_deletion` AFTER DELETE ON `locker_locations` FOR EACH ROW BEGIN
    DECLARE action VARCHAR(255) DEFAULT 'Deleted';
    DECLARE description VARCHAR(255);

    SET description = CONCAT('Location ', OLD.location, ' has been deleted.');

    INSERT INTO locker_logs (
        action,
        description
    ) VALUES (
        action,
        description
    );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_locker_location_insertion` AFTER INSERT ON `locker_locations` FOR EACH ROW BEGIN
    DECLARE action VARCHAR(255) DEFAULT 'Added';
    DECLARE description VARCHAR(255);

    SET description = CONCAT('Location ', NEW.location, ' has been added.');

    INSERT INTO locker_logs (
        action,
        description
    ) VALUES (
        action,
        description
    );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_locker_location_updation` AFTER UPDATE ON `locker_locations` FOR EACH ROW BEGIN
    DECLARE action VARCHAR(255) DEFAULT 'Updated';
    DECLARE description VARCHAR(255);

    SET description = CONCAT('Location ', OLD.location, ' has been updated to ', NEW.location, '.');

    INSERT INTO locker_logs (
        action,
        description
    ) VALUES (
        action,
        description
    );
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `locker_logs`
--

CREATE TABLE `locker_logs` (
  `id` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

--
-- Triggers `locker_sizes`
--
DELIMITER $$
CREATE TRIGGER `after_locker_sizes_deletion` AFTER DELETE ON `locker_sizes` FOR EACH ROW BEGIN
    DECLARE action VARCHAR(255) DEFAULT 'Deleted';
    DECLARE description VARCHAR(255);

    SET description = CONCAT('Size ', OLD.size, ' with price ₱', OLD.price, ' has been deleted.');

    INSERT INTO locker_logs (
        action,
        description
    ) VALUES (
        action,
        description
    );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_locker_sizes_insertion` AFTER INSERT ON `locker_sizes` FOR EACH ROW BEGIN
    DECLARE action VARCHAR(255) DEFAULT 'Added';
    DECLARE description VARCHAR(255);

    SET description = CONCAT('Size ', NEW.size, ' with price ₱', NEW.price, ' has been added.');

    INSERT INTO locker_logs (
        action,
        description
    ) VALUES (
        action,
        description
    );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_locker_sizes_updation` AFTER UPDATE ON `locker_sizes` FOR EACH ROW BEGIN
    DECLARE action VARCHAR(255) DEFAULT 'Updated';
    DECLARE description VARCHAR(255);

	IF OLD.size <> NEW.size THEN
		SET description = CONCAT('Size ', OLD.size, ' has been updated to ', NEW.size, '.');
	ELSEIF OLD.price <> NEW.price THEN
		SET description = CONCAT('Price ', OLD.price, ' has been updated to ', NEW.price, ' on ', NEW.size, '.');
	END IF;

    INSERT INTO locker_logs (
        action,
        description
    ) VALUES (
        action,
        description
    );
END
$$
DELIMITER ;

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

--
-- Triggers `locker_slots`
--
DELIMITER $$
CREATE TRIGGER `after_locker_slot_deletion` AFTER DELETE ON `locker_slots` FOR EACH ROW BEGIN
    DECLARE action VARCHAR(255) DEFAULT 'Deleted';
    DECLARE description VARCHAR(255);

    SET description = CONCAT('Slot #', OLD.slot_number, ' has been deleted (Location ID: ', OLD.location_id, ', Size ID: ', OLD.size_id, ').');

    INSERT INTO locker_logs (
        action,
        description
    ) VALUES (
        action,
        description
    );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_locker_slot_insertion` AFTER INSERT ON `locker_slots` FOR EACH ROW BEGIN
    DECLARE action VARCHAR(255) DEFAULT 'Added';
    DECLARE description VARCHAR(255);

    SET description = CONCAT('Slot #', NEW.slot_number, ' has been added (Location ID: ', NEW.location_id, ', Size ID: ', NEW.size_id, ').');

    INSERT INTO locker_logs (
        action,
        description
    ) VALUES (
        action,
        description
    );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_locker_slot_updation` AFTER UPDATE ON `locker_slots` FOR EACH ROW BEGIN
    DECLARE action VARCHAR(255) DEFAULT 'Updated';
    DECLARE description VARCHAR(255);

	IF OLD.slot_number <> NEW.slot_number THEN
		SET description = CONCAT('Slot #', OLD.slot_number, ' has been updated to ', 'Slot #', NEW.slot_number, '.');
	ELSEIF OLD.status <> NEW.status THEN
		SET description = CONCAT('Slot #', NEW.slot_number, ' status of ', OLD.status, ' has been updated to ', NEW.status, '.');
	END IF;

    INSERT INTO locker_logs (
        action,
        description
    ) VALUES (
        action,
        description
    );
END
$$
DELIMITER ;

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
('2024-11468', 'Getalla', 'Joviet', 'Batang', 'Male', '2003-02-19', 'Institute of Computing', 'Bachelor of Science in Information System', 'jovietgetalla', 'getalla.joviet@dnscedu.onmicrosoft.com', '$2y$10$5iPKtTZvlnC2AX5J90TJ9eLPLs/CSpp5aI6C2PICsBxzE8Zj4uRkW'),
('2024-31214', 'Malbino', 'Feby Johnrel', 'Roferos', 'Male', '2006-02-11', 'Institute of Computing', 'Bachelor of Science in Information Technology', 'febyjohnrelmalbino', 'malbino.febyjohnrel@dnscedu.onmicrosoft.com', '$2y$10$pzfD2ClxgQoTh..z1xAy2.M/6OFnSVTlve5pvQLJ8n9mXIt2tMU2i');

--
-- Triggers `users`
--
DELIMITER $$
CREATE TRIGGER `after_user_activation` AFTER INSERT ON `users` FOR EACH ROW BEGIN
	DECLARE action VARCHAR(255) DEFAULT 'Activated';
    	DECLARE description VARCHAR(255);

	SET description = CONCAT(NEW.username, ' (', '#', NEW.id, ') ', 'account has been activated.');
    
	INSERT INTO user_account_logs (
        action,
        description
    ) VALUES (
        action,
        description
    );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_user_deletion` AFTER DELETE ON `users` FOR EACH ROW BEGIN
	DECLARE action VARCHAR(255) DEFAULT 'Deleted';
   	DECLARE description VARCHAR(255);

	SET description = CONCAT(OLD.username, ' (', '#', OLD.id, ') ', 'account has been deleted.');
    
	INSERT INTO user_account_logs (
        action,
        description
    ) VALUES (
        action,
        description
    );
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `user_account_logs`
--

CREATE TABLE `user_account_logs` (
  `id` int(11) NOT NULL,
  `user_id` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `action` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
-- Indexes for table `locker_logs`
--
ALTER TABLE `locker_logs`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `user_account_logs`
--
ALTER TABLE `user_account_logs`
  ADD PRIMARY KEY (`id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `locker_locations`
--
ALTER TABLE `locker_locations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `locker_logs`
--
ALTER TABLE `locker_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `locker_sizes`
--
ALTER TABLE `locker_sizes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `locker_slots`
--
ALTER TABLE `locker_slots`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `user_account_logs`
--
ALTER TABLE `user_account_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

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
