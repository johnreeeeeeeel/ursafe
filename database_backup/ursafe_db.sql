-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 19, 2026 at 05:39 PM
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
    SET status = 'Accepted', updated_at = NOW()
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `addLocker` (IN `p_slot_number` INT, IN `p_location_id` INT, IN `p_size_id` INT, IN `p_start_at` DATE, IN `p_end_at` DATE)   BEGIN
    INSERT INTO locker_slots (
        slot_number,
        location_id,
        size_id,
        start_at,
        end_at,
        status
    )
    VALUES (
        p_slot_number,
        p_location_id,
        p_size_id,
        p_start_at,
        p_end_at,
        'Available'
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `applyLocker` (IN `u_user_id` VARCHAR(255), IN `u_slot_id` INT)   BEGIN
    DECLARE active_application_count INT DEFAULT 0;
    DECLARE slot_status VARCHAR(50);
    DECLARE v_start_at DATE;
    DECLARE v_end_at DATE;
    DECLARE slot_exists INT DEFAULT 1;

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET slot_exists = 0;

    SELECT status, start_at, end_at
    INTO slot_status, v_start_at, v_end_at
    FROM locker_slots
    WHERE id = u_slot_id;

    IF slot_exists = 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Slot not found';
    END IF;

    IF slot_status <> 'Available' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'This slot is not available for application';
    END IF;

    IF CURDATE() < v_start_at THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Application has not started yet';

    ELSEIF CURDATE() >= v_end_at THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Application period has ended';
    END IF;

    SELECT COUNT(*)
    INTO active_application_count
    FROM locker_applications
    WHERE user_id = u_user_id
      AND slot_id = u_slot_id
      AND status IN ('Pending', 'Accepted');

    IF active_application_count > 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'You already applied for this slot';

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
    SET status = 'Cancelled', updated_at = NOW()
    WHERE id = p_application_id
      AND status = 'Pending';
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `deleteLocker` (IN `p_id` INT)   BEGIN
    DELETE FROM locker_slots
    WHERE id = p_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `deleteLockerLocation` (IN `p_id` INT)   BEGIN
    DELETE FROM locker_locations
    WHERE id = p_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `deleteLockerSize` (IN `p_id` INT)   BEGIN
    DELETE FROM locker_sizes
    WHERE id = p_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `endLockerApplication` ()   BEGIN
    UPDATE locker_slots SET status = 'Available'
    WHERE end_at <= CURDATE() AND status = 'Occupied';

    UPDATE locker_applications la
    JOIN locker_slots ls ON la.slot_id = ls.id
    SET la.status = 'Ended', la.updated_at = NOW()
    WHERE ls.end_at <= CURDATE() AND la.status IN ('Pending', 'Accepted');
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getAcceptedLockerApplications` (IN `p_limit` INT, IN `p_offset` INT)   BEGIN
    SELECT
        la.id AS application_id,
        la.user_id,
        la.slot_id,
        la.status,
	la.payment,
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
        lsz.price,
        DATE_FORMAT(ls.start_at, '%M %d, %Y') AS start_at,
        DATE_FORMAT(ls.end_at, '%M %d, %Y') AS end_at,
        DATE_FORMAT(la.updated_at, '%M %d, %Y') AS updated_at
    FROM locker_applications la
    INNER JOIN users u ON la.user_id = u.id
    INNER JOIN locker_slots ls ON la.slot_id = ls.id
    INNER JOIN locker_locations ll ON ls.location_id = ll.id
    INNER JOIN locker_sizes lsz ON ls.size_id = lsz.id
    WHERE la.status = 'Accepted'
    ORDER BY la.updated_at DESC
    LIMIT p_limit OFFSET p_offset;

    SELECT COUNT(*) AS acceptedTotal
    FROM locker_applications
    WHERE status = 'Accepted';

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

CREATE DEFINER=`root`@`localhost` PROCEDURE `getEndedLockerApplications` (IN `p_limit` INT, IN `p_offset` INT)   BEGIN
    SELECT
        la.id AS application_id,
        la.user_id,
        la.slot_id,
        la.status,
		la.payment,
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
        lsz.price,
        DATE_FORMAT(ls.start_at, '%M %d, %Y') AS start_at,
        DATE_FORMAT(ls.end_at, '%M %d, %Y') AS end_at,
        DATE_FORMAT(la.updated_at, '%M %d, %Y') AS updated_at
    FROM locker_applications la
    INNER JOIN users u ON la.user_id = u.id
    INNER JOIN locker_slots ls ON la.slot_id = ls.id
    INNER JOIN locker_locations ll ON ls.location_id = ll.id
    INNER JOIN locker_sizes lsz ON ls.size_id = lsz.id
    WHERE la.status = 'Ended'
    AND la.payment = 'Unpaid'
    ORDER BY la.updated_at DESC
    LIMIT p_limit OFFSET p_offset;

    SELECT COUNT(*) AS acceptedTotal
    FROM locker_applications
    WHERE status = 'Ended'
    AND payment = 'Unpaid';

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getLockerApplicationHistory` (IN `p_limit` INT, IN `p_offset` INT)   BEGIN
    SELECT
        la.id AS application_id,
        la.user_id,
        la.slot_id,
        la.status,
        la.payment,
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
        lsz.price,
        DATE_FORMAT(ls.start_at, '%M %d, %Y') AS start_at,
        DATE_FORMAT(ls.end_at, '%M %d, %Y') AS end_at,
        DATE_FORMAT(la.updated_at, '%M %d, %Y') AS updated_at
    FROM locker_applications la
    INNER JOIN users u ON la.user_id = u.id
    INNER JOIN locker_slots ls ON la.slot_id = ls.id
    INNER JOIN locker_locations ll ON ls.location_id = ll.id
    INNER JOIN locker_sizes lsz ON ls.size_id = lsz.id
    WHERE la.status IN ('Cancelled', 'Rejected', 'Revoked', 'Ended')
    AND la.payment = 'Paid'
    ORDER BY la.updated_at DESC
    LIMIT p_limit OFFSET p_offset;

    SELECT COUNT(*) AS historyTotal
    FROM locker_applications la
    WHERE la.status IN ('Cancelled', 'Rejected', 'Revoked', 'Ended')
    AND la.payment = 'Paid';

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getLockerLocations` ()   BEGIN
    SELECT id,
	location,
	DATE_FORMAT(created_at, '%M %d, %Y %h:%i:%s %p') AS created_at
	FROM locker_locations
    ORDER BY created_at DESC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getLockerLogs` (IN `p_limit` INT, IN `p_offset` INT)   BEGIN
    SELECT
        id,
        action,
        description,
        DATE_FORMAT(created_at, '%M %d, %Y %h:%i:%s %p') AS created_at
    FROM locker_logs
    ORDER BY created_at DESC
    LIMIT p_offset, p_limit;

    SELECT COUNT(*) AS lockerLogsTotal
    FROM locker_logs;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getLockersByLocation` (IN `p_location_id` INT)   BEGIN
    SELECT
        ls.id,
        ls.slot_number,
        lsz.size,
        DATE_FORMAT(ls.start_at, '%M %d, %Y') AS start_at,
        DATE_FORMAT(ls.end_at, '%M %d, %Y') AS end_at,
        lsz.price,
        ls.status
    FROM locker_slots ls
    INNER JOIN locker_sizes lsz ON ls.size_id = lsz.id
    WHERE ls.location_id = p_location_id
    ORDER BY ls.slot_number ASC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getLockerSizes` ()   BEGIN
    SELECT id,
        size,
        price,
        DATE_FORMAT(created_at, '%M %d, %Y %h:%i:%s %p') AS created_at
        FROM locker_sizes
    ORDER BY created_at DESC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getMyAcceptedLockerApplications` (IN `p_user_id` VARCHAR(255), IN `p_limit` INT, IN `p_offset` INT)   BEGIN
    SELECT
        la.id AS application_id,
        la.status,
	la.payment,
        ls.slot_number,
        ll.location,
        sz.size,
        sz.price,
        DATE_FORMAT(ls.start_at, '%M %d, %Y') AS start_at,
        DATE_FORMAT(ls.end_at, '%M %d, %Y') AS end_at,
        DATE_FORMAT(la.updated_at, '%M %d, %Y') AS updated_at
    FROM locker_applications la
    INNER JOIN locker_slots ls ON la.slot_id = ls.id
    INNER JOIN locker_locations ll ON ls.location_id = ll.id
    INNER JOIN locker_sizes sz ON ls.size_id = sz.id
    WHERE la.user_id = p_user_id
      AND la.status = 'Accepted'
    ORDER BY la.updated_at DESC
    LIMIT p_limit OFFSET p_offset;

    SELECT COUNT(*) AS myAcceptedTotal
    FROM locker_applications
    WHERE user_id = p_user_id
      AND status = 'Accepted';

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getMyEndedLockerApplications` (IN `p_user_id` VARCHAR(255), IN `p_limit` INT, IN `p_offset` INT)   BEGIN
    SELECT
        la.id AS application_id,
        la.status,
	 	la.payment,
        ls.slot_number,
        ll.location,
        sz.size,
        sz.price,
        DATE_FORMAT(ls.start_at, '%M %d, %Y') AS start_at,
        DATE_FORMAT(ls.end_at, '%M %d, %Y') AS end_at,
        DATE_FORMAT(la.updated_at, '%M %d, %Y') AS updated_at
    FROM locker_applications la
    INNER JOIN locker_slots ls ON la.slot_id = ls.id
    INNER JOIN locker_locations ll ON ls.location_id = ll.id
    INNER JOIN locker_sizes sz ON ls.size_id = sz.id
    WHERE la.user_id = p_user_id
      AND la.status = 'Ended'
      AND la.payment = 'Unpaid'
    ORDER BY la.updated_at DESC
    LIMIT p_limit OFFSET p_offset;

    SELECT COUNT(*) AS myEndedTotal
    FROM locker_applications
    WHERE user_id = p_user_id
      AND status = 'Ended'
      AND payment = 'Unpaid';

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getMyLockerApplicationHistory` (IN `p_user_id` VARCHAR(255), IN `p_limit` INT, IN `p_offset` INT)   BEGIN
    SELECT
        la.id AS application_id,
        la.status,
	la.payment,
        ls.slot_number,
        ll.location,
        sz.size,
        sz.price,
        DATE_FORMAT(ls.start_at, '%M %d, %Y') AS start_at,
        DATE_FORMAT(ls.end_at, '%M %d, %Y') AS end_at,
        DATE_FORMAT(la.updated_at, '%M %d, %Y') AS updated_at
    FROM locker_applications la
    INNER JOIN locker_slots ls ON la.slot_id = ls.id
    INNER JOIN locker_locations ll ON ls.location_id = ll.id
    INNER JOIN locker_sizes sz ON ls.size_id = sz.id
    WHERE la.user_id = p_user_id
      AND la.status IN ('Cancelled', 'Rejected', 'Revoked', 'Ended')
    ORDER BY la.updated_at DESC
    LIMIT p_limit OFFSET p_offset;

    SELECT COUNT(*) AS myHistoryTotal
    FROM locker_applications
    WHERE user_id = p_user_id
      AND status IN ('Cancelled', 'Rejected', 'Revoked', 'Ended');

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getMyPendingLockerApplications` (IN `p_user_id` VARCHAR(255), IN `p_limit` INT, IN `p_offset` INT)   BEGIN
    SELECT
        la.id AS application_id,
        la.status,
	la.payment,
        ls.slot_number,
        ll.location,
        sz.size,
        sz.price,
        DATE_FORMAT(ls.start_at, '%M %d, %Y') AS start_at,
        DATE_FORMAT(ls.end_at, '%M %d, %Y') AS end_at,
        DATE_FORMAT(la.created_at, '%M %d, %Y') AS created_at
    FROM locker_applications la
    INNER JOIN locker_slots ls ON la.slot_id = ls.id
    INNER JOIN locker_locations ll ON ls.location_id = ll.id
    INNER JOIN locker_sizes sz ON ls.size_id = sz.id
    WHERE la.user_id = p_user_id
      AND la.status = 'Pending'
    ORDER BY la.created_at DESC
    LIMIT p_limit OFFSET p_offset;

    SELECT COUNT(*) AS myPendingTotal
    FROM locker_applications
    WHERE user_id = p_user_id
      AND status = 'Pending';

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getPendingLockerApplications` (IN `p_limit` INT, IN `p_offset` INT)   BEGIN
    SELECT
        la.id AS application_id,
        la.user_id,
        la.slot_id,
        la.status,
	la.payment,
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
        lsz.price,
        DATE_FORMAT(ls.start_at, '%M %d, %Y') AS start_at,
        DATE_FORMAT(ls.end_at, '%M %d, %Y') AS end_at,
        DATE_FORMAT(la.created_at, '%M %d, %Y') AS created_at
    FROM locker_applications la
    INNER JOIN users u ON la.user_id = u.id
    INNER JOIN locker_slots ls ON la.slot_id = ls.id
    INNER JOIN locker_locations ll ON ls.location_id = ll.id
    INNER JOIN locker_sizes lsz ON ls.size_id = lsz.id
    WHERE la.status = 'Pending'
    ORDER BY la.created_at DESC
    LIMIT p_limit OFFSET p_offset;

    SELECT COUNT(*) AS pendingTotal
    FROM locker_applications
    WHERE status = 'Pending';

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getSearchFilterAcceptedLockerApplications` (IN `searchTerm` VARCHAR(255), IN `filterTerm` VARCHAR(255), IN `p_limit` INT, IN `p_offset` INT)   BEGIN
    SELECT
        la.id AS application_id,
        la.user_id,
        la.slot_id,
        la.status,
	la.payment,
        TRIM(CONCAT(
            u.firstname, ' ',
            IFNULL(CONCAT(u.middlename, ' '), ''),
            u.lastname
        )) AS fullname,
        u.email,
        ls.slot_number,
        ll.location,
        lsz.size,
        lsz.price,
        DATE_FORMAT(ls.start_at, '%M %d, %Y') AS start_at,
        DATE_FORMAT(ls.end_at, '%M %d, %Y') AS end_at,
        DATE_FORMAT(la.updated_at, '%M %d, %Y') AS updated_at
    FROM locker_applications la
    INNER JOIN users u ON la.user_id = u.id
    INNER JOIN locker_slots ls ON la.slot_id = ls.id
    INNER JOIN locker_locations ll ON ls.location_id = ll.id
    INNER JOIN locker_sizes lsz ON ls.size_id = lsz.id
    WHERE la.status = 'Accepted'
    AND (
        searchTerm IS NULL OR searchTerm = ''
        OR la.id LIKE CONCAT('%', searchTerm, '%')
        OR u.firstname LIKE CONCAT('%', searchTerm, '%')
        OR u.lastname LIKE CONCAT('%', searchTerm, '%')
        OR u.email LIKE CONCAT('%', searchTerm, '%')
        OR ll.location LIKE CONCAT('%', searchTerm, '%')
        OR ls.slot_number LIKE CONCAT('%', searchTerm, '%')
    )
    ORDER BY
        CASE WHEN filterTerm = 'newest' THEN la.updated_at END DESC,
        CASE WHEN filterTerm = 'oldest' THEN la.updated_at END ASC,
        la.updated_at DESC
    LIMIT p_limit OFFSET p_offset;

    SELECT COUNT(*) AS acceptedTotal
    FROM locker_applications la
    INNER JOIN users u ON la.user_id = u.id
    INNER JOIN locker_slots ls ON la.slot_id = ls.id
    INNER JOIN locker_locations ll ON ls.location_id = ll.id
    WHERE la.status = 'Accepted'
    AND (
        searchTerm IS NULL OR searchTerm = ''
        OR la.id LIKE CONCAT('%', searchTerm, '%')
        OR u.firstname LIKE CONCAT('%', searchTerm, '%')
        OR u.lastname LIKE CONCAT('%', searchTerm, '%')
        OR u.email LIKE CONCAT('%', searchTerm, '%')
        OR ll.location LIKE CONCAT('%', searchTerm, '%')
        OR ls.slot_number LIKE CONCAT('%', searchTerm, '%')
    );

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getSearchFilterEndedLockerApplications` (IN `searchTerm` VARCHAR(255), IN `filterTerm` VARCHAR(255), IN `p_limit` INT, IN `p_offset` INT)   BEGIN
    SELECT
        la.id AS application_id,
        la.user_id,
        la.slot_id,
        la.status,
		la.payment,
        TRIM(CONCAT(
            u.firstname, ' ',
            IFNULL(CONCAT(u.middlename, ' '), ''),
            u.lastname
        )) AS fullname,
        u.email,
        ls.slot_number,
        ll.location,
        lsz.size,
        lsz.price,
        DATE_FORMAT(ls.start_at, '%M %d, %Y') AS start_at,
        DATE_FORMAT(ls.end_at, '%M %d, %Y') AS end_at,
        DATE_FORMAT(la.updated_at, '%M %d, %Y') AS updated_at
    FROM locker_applications la
    INNER JOIN users u ON la.user_id = u.id
    INNER JOIN locker_slots ls ON la.slot_id = ls.id
    INNER JOIN locker_locations ll ON ls.location_id = ll.id
    INNER JOIN locker_sizes lsz ON ls.size_id = lsz.id
    WHERE la.status = 'Ended'
    AND la.payment = 'Unpaid'
    AND (
        searchTerm IS NULL OR searchTerm = ''
        OR la.id LIKE CONCAT('%', searchTerm, '%')
        OR u.firstname LIKE CONCAT('%', searchTerm, '%')
        OR u.lastname LIKE CONCAT('%', searchTerm, '%')
        OR u.email LIKE CONCAT('%', searchTerm, '%')
        OR ll.location LIKE CONCAT('%', searchTerm, '%')
        OR ls.slot_number LIKE CONCAT('%', searchTerm, '%')
    )
    ORDER BY
        CASE WHEN filterTerm = 'newest' THEN la.updated_at END DESC,
        CASE WHEN filterTerm = 'oldest' THEN la.updated_at END ASC,
        la.updated_at DESC
    LIMIT p_limit OFFSET p_offset;

    SELECT COUNT(*) AS acceptedTotal
    FROM locker_applications la
    INNER JOIN users u ON la.user_id = u.id
    INNER JOIN locker_slots ls ON la.slot_id = ls.id
    INNER JOIN locker_locations ll ON ls.location_id = ll.id
    WHERE la.status = 'Ended'
    AND payment = 'Unpaid'
    AND (
        searchTerm IS NULL OR searchTerm = ''
        OR la.id LIKE CONCAT('%', searchTerm, '%')
        OR u.firstname LIKE CONCAT('%', searchTerm, '%')
        OR u.lastname LIKE CONCAT('%', searchTerm, '%')
        OR u.email LIKE CONCAT('%', searchTerm, '%')
        OR ll.location LIKE CONCAT('%', searchTerm, '%')
        OR ls.slot_number LIKE CONCAT('%', searchTerm, '%')
    );

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getSearchFilterLockerApplicationHistory` (IN `searchTerm` VARCHAR(255), IN `filterTerm` VARCHAR(255), IN `p_limit` INT, IN `p_offset` INT)   BEGIN
    SELECT
        la.id AS application_id,
        la.user_id,
        la.slot_id,
        la.status,
        la.payment,
        TRIM(CONCAT(
            u.firstname, ' ',
            IFNULL(CONCAT(u.middlename, ' '), ''),
            u.lastname
        )) AS fullname,
        u.email,
        ls.slot_number,
        ll.location,
        lsz.size,
        lsz.price,
        DATE_FORMAT(ls.start_at, '%M %d, %Y') AS start_at,
        DATE_FORMAT(ls.end_at, '%M %d, %Y') AS end_at,
        DATE_FORMAT(la.updated_at, '%M %d, %Y') AS updated_at
    FROM locker_applications la
    INNER JOIN users u ON la.user_id = u.id
    INNER JOIN locker_slots ls ON la.slot_id = ls.id
    INNER JOIN locker_locations ll ON ls.location_id = ll.id
    INNER JOIN locker_sizes lsz ON ls.size_id = lsz.id
    WHERE la.status IN ('Cancelled', 'Rejected', 'Revoked', 'Ended')
    AND la.payment = 'Paid'
    AND (
        searchTerm IS NULL OR searchTerm = ''
        OR CAST(la.id AS CHAR) LIKE CONCAT('%', searchTerm, '%')
        OR u.firstname LIKE CONCAT('%', searchTerm, '%')
        OR u.lastname LIKE CONCAT('%', searchTerm, '%')
        OR u.email LIKE CONCAT('%', searchTerm, '%')
        OR ll.location LIKE CONCAT('%', searchTerm, '%')
        OR ls.slot_number LIKE CONCAT('%', searchTerm, '%')
    )
    ORDER BY
        CASE WHEN filterTerm = 'newest' THEN la.updated_at END DESC,
        CASE WHEN filterTerm = 'oldest' THEN la.updated_at END ASC,
        la.updated_at DESC
    LIMIT p_limit OFFSET p_offset;

    SELECT COUNT(*) AS historyTotal
    FROM locker_applications la
    INNER JOIN users u ON la.user_id = u.id
    INNER JOIN locker_slots ls ON la.slot_id = ls.id
    INNER JOIN locker_locations ll ON ls.location_id = ll.id
    WHERE la.status IN ('Cancelled', 'Rejected', 'Revoked', 'Ended')
    AND la.payment = 'Paid'
    AND (
        searchTerm IS NULL OR searchTerm = ''
        OR CAST(la.id AS CHAR) LIKE CONCAT('%', searchTerm, '%')
        OR u.firstname LIKE CONCAT('%', searchTerm, '%')
        OR u.lastname LIKE CONCAT('%', searchTerm, '%')
        OR u.email LIKE CONCAT('%', searchTerm, '%')
        OR ll.location LIKE CONCAT('%', searchTerm, '%')
        OR ls.slot_number LIKE CONCAT('%', searchTerm, '%')
    );

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getSearchFilterLockerLocation` (IN `searchTerm` VARCHAR(255), IN `filterTerm` VARCHAR(255))   BEGIN
    SELECT id,
    location,
    DATE_FORMAT(created_at, '%M %d, %Y %h:%i:%s %p') AS created_at
    FROM locker_locations

    WHERE
    	searchTerm IS NULL
        OR searchTerm = ''
        OR location LIKE CONCAT('%', searchTerm, '%')
 
    ORDER BY
        CASE
            WHEN filterTerm = 'a-z' THEN location
        END ASC,
 
        CASE
            WHEN filterTerm = 'z-a' THEN location
        END DESC,
 
        CASE
            WHEN filterTerm = 'oldest' THEN created_at
        END ASC,
 
        CASE
            WHEN filterTerm = 'newest' THEN created_at
        END DESC;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getSearchFilterLockerSizes` (IN `searchTerm` VARCHAR(255), IN `filterTerm` VARCHAR(255))   BEGIN

    SELECT
        id,
        size,
        price,
        DATE_FORMAT(created_at, '%M %d, %Y %h:%i:%s %p') AS created_at
    FROM locker_sizes

    WHERE (
        searchTerm IS NULL
        OR searchTerm = ''
        OR size LIKE CONCAT('%', searchTerm, '%')
        OR price LIKE CONCAT('%', searchTerm, '%')
    )

    ORDER BY
        CASE
            WHEN filterTerm = 'a-z' THEN size
        END ASC,

        CASE
            WHEN filterTerm = 'z-a' THEN size
        END DESC,

        CASE
            WHEN filterTerm = 'oldest' THEN created_at
        END ASC,

        CASE
            WHEN filterTerm = 'newest' THEN created_at
        END DESC,

        CASE
            WHEN filterTerm = 'cheaper' THEN price
        END ASC,

        CASE
            WHEN filterTerm = 'expensive' THEN price
        END DESC;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getSearchFilterPendingLockerApplications` (IN `searchTerm` VARCHAR(255), IN `filterTerm` VARCHAR(255), IN `p_limit` INT, IN `p_offset` INT)   BEGIN
    SELECT
        la.id AS application_id,
        la.user_id,
        la.slot_id,
        la.status,
	la.payment,
        TRIM(CONCAT(
            u.firstname, ' ',
            IFNULL(CONCAT(u.middlename, ' '), ''),
            u.lastname
        )) AS fullname,
        u.email,
        ls.slot_number,
        ll.location,
        lsz.size,
        lsz.price,
        DATE_FORMAT(ls.start_at, '%M %d, %Y') AS start_at,
        DATE_FORMAT(ls.end_at, '%M %d, %Y') AS end_at,
        DATE_FORMAT(la.created_at, '%M %d, %Y') AS created_at
    FROM locker_applications la
    INNER JOIN users u ON la.user_id = u.id
    INNER JOIN locker_slots ls ON la.slot_id = ls.id
    INNER JOIN locker_locations ll ON ls.location_id = ll.id
    INNER JOIN locker_sizes lsz ON ls.size_id = lsz.id
    WHERE la.status = 'Pending'
    AND (
        searchTerm IS NULL
        OR searchTerm = ''
        OR la.id LIKE CONCAT('%', searchTerm, '%')
        OR u.firstname LIKE CONCAT('%', searchTerm, '%')
        OR u.lastname LIKE CONCAT('%', searchTerm, '%')
        OR u.email LIKE CONCAT('%', searchTerm, '%')
        OR ll.location LIKE CONCAT('%', searchTerm, '%')
        OR ls.slot_number LIKE CONCAT('%', searchTerm, '%')
    )
    ORDER BY
        CASE WHEN filterTerm = 'newest' THEN la.created_at END DESC,
        CASE WHEN filterTerm = 'oldest' THEN la.created_at END ASC,
        la.created_at DESC
    LIMIT p_limit OFFSET p_offset;

    SELECT COUNT(*) AS pendingTotal
    FROM locker_applications la
    INNER JOIN users u ON la.user_id = u.id
    INNER JOIN locker_slots ls ON la.slot_id = ls.id
    INNER JOIN locker_locations ll ON ls.location_id = ll.id
    WHERE la.status = 'Pending'
    AND (
        searchTerm IS NULL
        OR searchTerm = ''
        OR la.id LIKE CONCAT('%', searchTerm, '%')
        OR u.firstname LIKE CONCAT('%', searchTerm, '%')
        OR u.lastname LIKE CONCAT('%', searchTerm, '%')
        OR u.email LIKE CONCAT('%', searchTerm, '%')
        OR ll.location LIKE CONCAT('%', searchTerm, '%')
        OR ls.slot_number LIKE CONCAT('%', searchTerm, '%')
    );

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getSearchFilterUsers` (IN `searchTerm` VARCHAR(255), IN `sortOrder` VARCHAR(255), IN `p_limit` INT, IN `p_offset` INT)   BEGIN
    SELECT
        id,
        firstname,
        middlename,
        lastname,
        TRIM(CONCAT(
            firstname, ' ',
            IFNULL(CONCAT(middlename, ' '), ''),
            lastname
        )) AS fullname,
        sex,
        DATE_FORMAT(dob, '%M %d, %Y') AS dob,
        institute,
        program,
        username,
        email,
        DATE_FORMAT(created_at, '%M %d, %Y %h:%i:%s %p') AS created_at
    FROM users

    WHERE
        searchTerm IS NULL
        OR searchTerm = ''
        OR id LIKE CONCAT('%', searchTerm, '%')
        OR firstname LIKE CONCAT('%', searchTerm, '%')
        OR middlename LIKE CONCAT('%', searchTerm, '%')
        OR lastname LIKE CONCAT('%', searchTerm, '%')
        OR username LIKE CONCAT('%', searchTerm, '%')
        OR email LIKE CONCAT('%', searchTerm, '%')

    ORDER BY
        CASE WHEN sortOrder = 'a-z' THEN firstname END ASC,
        CASE WHEN sortOrder = 'z-a' THEN firstname END DESC,
        CASE WHEN sortOrder = 'oldest' THEN created_at END ASC,
        CASE WHEN sortOrder = 'newest' THEN created_at END DESC

    LIMIT p_limit OFFSET p_offset;

    SELECT COUNT(*) AS usersTotal
    FROM users
    WHERE
        searchTerm IS NULL
        OR searchTerm = ''
        OR id LIKE CONCAT('%', searchTerm, '%')
        OR firstname LIKE CONCAT('%', searchTerm, '%')
        OR middlename LIKE CONCAT('%', searchTerm, '%')
        OR lastname LIKE CONCAT('%', searchTerm, '%')
        OR username LIKE CONCAT('%', searchTerm, '%')
        OR email LIKE CONCAT('%', searchTerm, '%');

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getUserAccountLogs` (IN `p_limit` INT, IN `p_offset` INT)   BEGIN
    SELECT
        id,
        action,
        description,
        DATE_FORMAT(created_at, '%M %d, %Y %h:%i:%s %p') AS created_at
    FROM user_account_logs
    ORDER BY created_at DESC
    LIMIT p_offset, p_limit;

    SELECT COUNT(*) AS userLogsTotal
    FROM user_account_logs;

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

CREATE DEFINER=`root`@`localhost` PROCEDURE `getUsers` (IN `p_limit` INT, IN `p_offset` INT)   BEGIN
    SELECT
        id,
        firstname,
        middlename,
        lastname,
        TRIM(CONCAT(firstname, ' ', IFNULL(CONCAT(middlename, ' '), ''), lastname)) AS fullname,
        sex,
        DATE_FORMAT(dob, '%M %d, %Y') AS dob,
        institute,
        program,
        username,
        email,
        DATE_FORMAT(created_at, '%M %d, %Y') AS created_at
    FROM users
    ORDER BY created_at DESC
    LIMIT p_limit OFFSET p_offset;

    SELECT COUNT(*) AS usersTotal
    FROM users;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `get_accepted_locker_applications_count` ()   BEGIN
    SELECT COUNT(*) AS accepted_locker_applications_count
    FROM locker_applications
    WHERE status = 'Accepted';
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `get_available_lockers_count` ()   BEGIN
    SELECT COUNT(*) AS available_lockers_count
    FROM locker_slots
    WHERE status = 'Available';
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `get_cancelled_locker_applications_count` ()   BEGIN
    SELECT COUNT(*) AS cancelled_locker_applications_count
    FROM locker_applications
    WHERE status = 'Cancelled';
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `get_occupied_lockers_count` ()   BEGIN
    SELECT COUNT(*) AS occupied_lockers_count
    FROM locker_slots
    WHERE status = 'Occupied';
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `get_pending_locker_applications_count` ()   BEGIN
    SELECT COUNT(*) AS pending_locker_applications_count
    FROM locker_applications
    WHERE status = 'Pending';
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `get_recent_locker_application` ()   BEGIN
    SELECT
    	id,
        user_id,
        firstname,
        middlename,
        lastname,
        location,
        slot_number,
        size,
        price,
        DATE_FORMAT(created_at, '%M %d, %Y %h:%i:%s %p') AS created_at
    FROM recent_locker_application;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `get_recent_user_account_activation` ()   BEGIN
    SELECT
        id,
        username,
        lastname,
	firstname,
	middlename,
        DATE_FORMAT(created_at, '%M %d, %Y %h:%i:%s %p') AS created_at
    FROM recent_user_account_activation;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `get_rejected_locker_applications_count` ()   BEGIN
    SELECT COUNT(*) AS rejected_locker_applications_count
    FROM locker_applications
    WHERE status = 'Rejected';
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `get_revoked_locker_applications_count` ()   BEGIN
    SELECT COUNT(*) AS revoked_locker_applications_count
    FROM locker_applications
    WHERE status = 'Revoked';
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `get_total_lockers_count` ()   BEGIN
    SELECT COUNT(*) AS total_lockers_count
    FROM locker_slots;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `get_total_locker_applications_count` ()   BEGIN
    SELECT COUNT(*) AS total_locker_applications_count
    FROM locker_applications;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `get_users_count` ()   BEGIN
    SELECT COUNT(*) AS users_count
    FROM users;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `paidLockerApplication` (IN `p_application_id` INT)   BEGIN
    DECLARE v_slot_id INT;
    DECLARE v_status VARCHAR(255);
    DECLARE v_payment VARCHAR(255);

    START TRANSACTION;

    SELECT slot_id, status, payment
    INTO v_slot_id, v_status, v_payment
    FROM locker_applications
    WHERE id = p_application_id;

    IF v_status IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Application not found';

    ELSEIF v_status <> 'Ended' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Only ended applications can be paid';
    END IF;

    UPDATE locker_applications
    SET payment = 'Paid', updated_at = NOW()
    WHERE id = p_application_id;

    COMMIT;

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
    SET status = 'Rejected', updated_at = NOW()
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
    SET status = 'Revoked', updated_at = NOW()
    WHERE id = p_application_id;

    UPDATE locker_slots
    SET status = 'Available'
    WHERE id = v_slot_id;

    COMMIT;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `updateLocker` (IN `p_id` INT, IN `p_slot_number` INT, IN `p_start_date` DATE, IN `p_end_date` DATE, IN `p_status` VARCHAR(255))   BEGIN
    UPDATE locker_slots
    SET
        slot_number = p_slot_number,
        start_at = p_start_date,
        end_at = p_end_date,
        status = p_status,
        updated_at = NOW()
    WHERE id = p_id;
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
  `status` varchar(255) NOT NULL DEFAULT 'Pending',
  `payment` varchar(255) NOT NULL DEFAULT 'Unpaid',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `locker_applications`
--

INSERT INTO `locker_applications` (`id`, `user_id`, `slot_id`, `status`, `payment`, `created_at`, `updated_at`) VALUES
(1, '2024-31214', 2, 'Ended', 'Unpaid', '2026-05-18 14:36:38', '2026-05-19 04:34:53'),
(2, '2024-31214', 1, 'Ended', 'Paid', '2026-05-18 14:36:46', '2026-05-19 15:18:47'),
(3, '2024-31214', 4, 'Ended', 'Unpaid', '2026-05-18 14:40:16', '2026-05-19 04:34:53'),
(4, '2024-31214', 3, 'Ended', 'Unpaid', '2026-05-18 14:40:22', '2026-05-19 04:34:53'),
(5, '2024-31214', 2, 'Accepted', 'Unpaid', '2026-05-19 06:24:45', '2026-05-19 06:51:18'),
(6, '2024-31214', 1, 'Accepted', 'Unpaid', '2026-05-19 06:24:56', '2026-05-19 06:51:16'),
(7, '2024-31214', 4, 'Accepted', 'Unpaid', '2026-05-19 06:24:59', '2026-05-19 06:51:13'),
(8, '2024-31214', 3, 'Accepted', 'Unpaid', '2026-05-19 06:25:04', '2026-05-19 06:51:10'),
(9, '2024-11468', 2, 'Pending', 'Unpaid', '2026-05-19 06:25:14', NULL),
(10, '2024-11468', 1, 'Pending', 'Unpaid', '2026-05-19 06:25:17', NULL),
(11, '2024-11468', 4, 'Pending', 'Unpaid', '2026-05-19 06:25:24', NULL),
(12, '2024-11468', 3, 'Pending', 'Unpaid', '2026-05-19 06:25:27', NULL),
(13, '2024-98026', 2, 'Pending', 'Unpaid', '2026-05-19 06:50:22', NULL),
(14, '2024-98026', 1, 'Pending', 'Unpaid', '2026-05-19 06:50:26', NULL),
(15, '2024-98026', 4, 'Pending', 'Unpaid', '2026-05-19 06:50:28', NULL),
(16, '2024-98026', 3, 'Pending', 'Unpaid', '2026-05-19 06:50:32', NULL);

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
  `location` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `locker_locations`
--

INSERT INTO `locker_locations` (`id`, `location`, `created_at`) VALUES
(1, 'AB 1st Floor', '2026-05-18 14:35:15'),
(2, 'AB 2nd Floor', '2026-05-18 14:35:19'),
(3, 'AB 3rd Floor', '2026-05-18 14:35:22'),
(4, 'AB 4th Floor', '2026-05-18 14:35:24');

--
-- Triggers `locker_locations`
--
DELIMITER $$
CREATE TRIGGER `after_locker_location_deletion` AFTER DELETE ON `locker_locations` FOR EACH ROW BEGIN
    DECLARE action VARCHAR(255) DEFAULT 'Deletion';
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
    DECLARE action VARCHAR(255) DEFAULT 'Addition';
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
    DECLARE action VARCHAR(255) DEFAULT 'Updation';
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

--
-- Dumping data for table `locker_logs`
--

INSERT INTO `locker_logs` (`id`, `action`, `description`, `created_at`) VALUES
(253, 'Deletion', 'Location AB 1st Floor has been deleted.', '2026-05-18 14:27:14'),
(254, 'Deletion', 'Location AB 2nd Floor has been deleted.', '2026-05-18 14:27:14'),
(255, 'Deletion', 'Location AB 3rd Floor has been deleted.', '2026-05-18 14:27:14'),
(256, 'Deletion', 'Location AB 4th Floor has been deleted.', '2026-05-18 14:27:14'),
(257, 'Addition', 'Location AB 1st Floor has been added.', '2026-05-18 14:35:15'),
(258, 'Addition', 'Location AB 2nd Floor has been added.', '2026-05-18 14:35:19'),
(259, 'Addition', 'Location AB 3rd Floor has been added.', '2026-05-18 14:35:22'),
(260, 'Addition', 'Location AB 4th Floor has been added.', '2026-05-18 14:35:24'),
(261, 'Addition', 'Size Small with price ₱80.00 has been added.', '2026-05-18 14:35:31'),
(262, 'Addition', 'Size Medium  with price ₱100.00 has been added.', '2026-05-18 14:35:35'),
(263, 'Addition', 'Size Large with price ₱150.00 has been added.', '2026-05-18 14:35:41'),
(264, 'Addition', 'Slot #1 has been added (Location ID: 1, Size ID: 1).', '2026-05-18 14:36:09'),
(265, 'Addition', 'Slot #2 has been added (Location ID: 1, Size ID: 1).', '2026-05-18 14:36:20'),
(266, 'Application', '2024-31214 has applied on slot #2 at AB 1st Floor.', '2026-05-18 14:36:38'),
(267, 'Application', '2024-31214 has applied on slot #1 at AB 1st Floor.', '2026-05-18 14:36:46'),
(268, 'Addition', 'Slot #1 has been added (Location ID: 2, Size ID: 1).', '2026-05-18 14:40:01'),
(269, 'Addition', 'Slot #2 has been added (Location ID: 2, Size ID: 1).', '2026-05-18 14:40:10'),
(270, 'Application', '2024-31214 has applied on slot #2 at AB 2nd Floor.', '2026-05-18 14:40:16'),
(271, 'Application', '2024-31214 has applied on slot #1 at AB 2nd Floor.', '2026-05-18 14:40:22'),
(272, 'Acception', '2024-31214 application on slot #2 at AB 1st Floor has been accepted.', '2026-05-18 14:40:54'),
(273, 'Updation', 'Slot #2 status of Available has been updated to Occupied.', '2026-05-18 14:40:54'),
(274, 'Acception', '2024-31214 application on slot #1 at AB 1st Floor has been accepted.', '2026-05-18 14:41:04'),
(275, 'Updation', 'Slot #1 status of Available has been updated to Occupied.', '2026-05-18 14:41:04'),
(276, 'Updation', 'Slot #1 status of Occupied has been updated to Available.', '2026-05-19 04:34:53'),
(277, 'Updation', 'Slot #2 status of Occupied has been updated to Available.', '2026-05-19 04:34:53'),
(278, 'Updation', 'Locker updated.', '2026-05-19 06:23:18'),
(279, 'Updation', 'Locker updated.', '2026-05-19 06:23:22'),
(280, 'Updation', 'Locker updated.', '2026-05-19 06:23:26'),
(281, 'Updation', 'Locker updated.', '2026-05-19 06:23:30'),
(282, 'Application', '2024-31214 has applied on slot #2 at AB 1st Floor.', '2026-05-19 06:24:45'),
(283, 'Application', '2024-31214 has applied on slot #1 at AB 1st Floor.', '2026-05-19 06:24:56'),
(284, 'Application', '2024-31214 has applied on slot #2 at AB 2nd Floor.', '2026-05-19 06:24:59'),
(285, 'Application', '2024-31214 has applied on slot #1 at AB 2nd Floor.', '2026-05-19 06:25:04'),
(286, 'Application', '2024-11468 has applied on slot #2 at AB 1st Floor.', '2026-05-19 06:25:14'),
(287, 'Application', '2024-11468 has applied on slot #1 at AB 1st Floor.', '2026-05-19 06:25:17'),
(288, 'Application', '2024-11468 has applied on slot #2 at AB 2nd Floor.', '2026-05-19 06:25:24'),
(289, 'Application', '2024-11468 has applied on slot #1 at AB 2nd Floor.', '2026-05-19 06:25:27'),
(290, 'Application', '2024-98026 has applied on slot #2 at AB 1st Floor.', '2026-05-19 06:50:22'),
(291, 'Application', '2024-98026 has applied on slot #1 at AB 1st Floor.', '2026-05-19 06:50:26'),
(292, 'Application', '2024-98026 has applied on slot #2 at AB 2nd Floor.', '2026-05-19 06:50:28'),
(293, 'Application', '2024-98026 has applied on slot #1 at AB 2nd Floor.', '2026-05-19 06:50:32'),
(294, 'Acception', '2024-31214 application on slot #1 at AB 2nd Floor has been accepted.', '2026-05-19 06:51:10'),
(295, 'Updation', 'Slot #1 status of Available has been updated to Occupied.', '2026-05-19 06:51:10'),
(296, 'Acception', '2024-31214 application on slot #2 at AB 2nd Floor has been accepted.', '2026-05-19 06:51:13'),
(297, 'Updation', 'Slot #2 status of Available has been updated to Occupied.', '2026-05-19 06:51:13'),
(298, 'Acception', '2024-31214 application on slot #1 at AB 1st Floor has been accepted.', '2026-05-19 06:51:16'),
(299, 'Updation', 'Slot #1 status of Available has been updated to Occupied.', '2026-05-19 06:51:16'),
(300, 'Acception', '2024-31214 application on slot #2 at AB 1st Floor has been accepted.', '2026-05-19 06:51:18'),
(301, 'Updation', 'Slot #2 status of Available has been updated to Occupied.', '2026-05-19 06:51:18'),
(302, 'Addition', 'Slot #1 has been added (Location ID: 3, Size ID: 2).', '2026-05-19 15:05:10'),
(303, 'Addition', 'Slot #2 has been added (Location ID: 3, Size ID: 2).', '2026-05-19 15:05:19');

-- --------------------------------------------------------

--
-- Table structure for table `locker_sizes`
--

CREATE TABLE `locker_sizes` (
  `id` int(11) NOT NULL,
  `size` varchar(255) NOT NULL,
  `price` double(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `locker_sizes`
--

INSERT INTO `locker_sizes` (`id`, `size`, `price`, `created_at`) VALUES
(1, 'Small', 80.00, '2026-05-18 14:35:31'),
(2, 'Medium ', 100.00, '2026-05-18 14:35:35'),
(3, 'Large', 150.00, '2026-05-18 14:35:41');

--
-- Triggers `locker_sizes`
--
DELIMITER $$
CREATE TRIGGER `after_locker_sizes_deletion` AFTER DELETE ON `locker_sizes` FOR EACH ROW BEGIN
    DECLARE action VARCHAR(255) DEFAULT 'Deletion';
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
    DECLARE action VARCHAR(255) DEFAULT 'Addition';
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
    DECLARE action VARCHAR(255) DEFAULT 'Updation';
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
  `status` varchar(255) DEFAULT 'Available',
  `start_at` date NOT NULL,
  `end_at` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `locker_slots`
--

INSERT INTO `locker_slots` (`id`, `slot_number`, `location_id`, `size_id`, `status`, `start_at`, `end_at`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 'Occupied', '2026-05-01', '2026-05-31', '2026-05-18 14:36:09', '2026-05-19 06:23:22'),
(2, 2, 1, 1, 'Occupied', '2026-05-01', '2026-05-31', '2026-05-18 14:36:20', '2026-05-19 06:23:18'),
(3, 1, 2, 1, 'Occupied', '2026-05-01', '2026-05-31', '2026-05-18 14:40:01', '2026-05-19 06:23:30'),
(4, 2, 2, 1, 'Occupied', '2026-05-01', '2026-05-31', '2026-05-18 14:40:10', '2026-05-19 06:23:26'),
(5, 1, 3, 2, 'Available', '2026-05-01', '2026-05-31', '2026-05-19 15:05:10', NULL),
(6, 2, 3, 2, 'Available', '2026-05-01', '2026-05-31', '2026-05-19 15:05:19', NULL);

--
-- Triggers `locker_slots`
--
DELIMITER $$
CREATE TRIGGER `after_locker_slot_deletion` AFTER DELETE ON `locker_slots` FOR EACH ROW BEGIN
    DECLARE action VARCHAR(255) DEFAULT 'Deletion';
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
    DECLARE action VARCHAR(255) DEFAULT 'Addition';
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
    DECLARE action VARCHAR(255) DEFAULT 'Updation';
    DECLARE description VARCHAR(255) DEFAULT 'Locker updated.';

    IF OLD.slot_number <> NEW.slot_number THEN

        SET description = CONCAT(
            'Slot #',
            OLD.slot_number,
            ' has been updated to Slot #',
            NEW.slot_number,
            '.'
        );

    ELSEIF OLD.status <> NEW.status THEN

        SET description = CONCAT(
            'Slot #',
            NEW.slot_number,
            ' status of ',
            OLD.status,
            ' has been updated to ',
            NEW.status,
            '.'
        );

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
-- Stand-in structure for view `recent_locker_application`
-- (See below for the actual view)
--
CREATE TABLE `recent_locker_application` (
`id` int(11)
,`user_id` varchar(255)
,`firstname` varchar(255)
,`middlename` varchar(255)
,`lastname` varchar(255)
,`location` varchar(255)
,`slot_number` int(11)
,`size` varchar(255)
,`price` double(10,2)
,`created_at` timestamp
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `recent_user_account_activation`
-- (See below for the actual view)
--
CREATE TABLE `recent_user_account_activation` (
`id` varchar(255)
,`username` varchar(255)
,`lastname` varchar(255)
,`firstname` varchar(255)
,`middlename` varchar(255)
,`created_at` timestamp
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
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `lastname`, `firstname`, `middlename`, `sex`, `dob`, `institute`, `program`, `username`, `email`, `password`, `created_at`) VALUES
('2024-11468', 'Getalla', 'Joviet', 'Batang', 'Male', '2003-02-19', 'Institute of Computing', 'Bachelor of Science in Information System', 'jovietgetalla', 'getalla.joviet@dnscedu.onmicrosoft.com', '$2y$10$8jdAiYSG4P9OLjqkCfZMF.qmJU4GONHqBxOgGqsMJE/fwSSIsljNu', '2026-05-19 04:29:03'),
('2024-31214', 'Malbino', 'Feby Johnrel', 'Roferos', 'Male', '2006-02-11', 'Institute of Computing', 'Bachelor of Science in Information Technology', 'febyjohnrelmalbino', 'malbino.febyjohnrel@dnscedu.onmicrosoft.com', '$2y$10$64btR9Y3CFUeqi0c.xQf/Ono03Ap8BySNOXruU723sr7qmcvQDO2O', '2026-05-13 09:57:23'),
('2024-98026', 'Bulay-og', 'Jason', 'Dy', 'Male', '2004-11-23', 'Institute of Computing', 'Bachelor of Science in Information Technology', 'jasonbulay-og', 'bulay-og.jason@dnscedu.onmicrosoft.com', '$2y$10$bhAYMy9nOgr4WGSS8CtJauCUFf5UFISILtN8IuHdNMdCTpBcrM316', '2026-05-19 06:50:06');

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
-- Dumping data for table `user_account_logs`
--

INSERT INTO `user_account_logs` (`id`, `user_id`, `email`, `action`, `description`, `created_at`) VALUES
(1, '', '', 'Activated', 'jovietgetalla (#2024-11468) account has been activated.', '2026-05-19 04:29:03'),
(2, '', '', 'Activated', 'jasonbulay-og (#2024-98026) account has been activated.', '2026-05-19 06:50:06');

-- --------------------------------------------------------

--
-- Structure for view `recent_locker_application`
--
DROP TABLE IF EXISTS `recent_locker_application`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `recent_locker_application`  AS SELECT `la`.`id` AS `id`, `la`.`user_id` AS `user_id`, `u`.`firstname` AS `firstname`, `u`.`middlename` AS `middlename`, `u`.`lastname` AS `lastname`, `ll`.`location` AS `location`, `ls`.`slot_number` AS `slot_number`, `lsz`.`size` AS `size`, `lsz`.`price` AS `price`, `la`.`created_at` AS `created_at` FROM ((((`locker_applications` `la` join `users` `u` on(`la`.`user_id` = `u`.`id`)) join `locker_slots` `ls` on(`la`.`slot_id` = `ls`.`id`)) join `locker_locations` `ll` on(`ls`.`location_id` = `ll`.`id`)) join `locker_sizes` `lsz` on(`ls`.`size_id` = `lsz`.`id`)) WHERE `la`.`status` = 'Pending' ORDER BY `la`.`created_at` DESC LIMIT 0, 8 ;

-- --------------------------------------------------------

--
-- Structure for view `recent_user_account_activation`
--
DROP TABLE IF EXISTS `recent_user_account_activation`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `recent_user_account_activation`  AS SELECT `users`.`id` AS `id`, `users`.`username` AS `username`, `users`.`lastname` AS `lastname`, `users`.`firstname` AS `firstname`, `users`.`middlename` AS `middlename`, `users`.`created_at` AS `created_at` FROM `users` ORDER BY `users`.`created_at` DESC LIMIT 0, 1 ;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `locker_locations`
--
ALTER TABLE `locker_locations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `locker_logs`
--
ALTER TABLE `locker_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=304;

--
-- AUTO_INCREMENT for table `locker_sizes`
--
ALTER TABLE `locker_sizes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `locker_slots`
--
ALTER TABLE `locker_slots`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `user_account_logs`
--
ALTER TABLE `user_account_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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

DELIMITER $$
--
-- Events
--
CREATE DEFINER=`root`@`localhost` EVENT `auto_end_locker` ON SCHEDULE EVERY 1 DAY STARTS '2026-05-19 12:34:22' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
    CALL endLockerApplication();
END$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
