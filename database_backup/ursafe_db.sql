-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 23, 2026 at 06:04 PM
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `addAcademicYear` (IN `p_academic_year` VARCHAR(255), IN `p_semester` VARCHAR(255), IN `p_start_at` DATE, IN `p_end_at` DATE)   BEGIN
    INSERT INTO academic_calendar (academic_year, semester, start_at, end_at)
    VALUES (p_academic_year, p_semester, p_start_at, p_end_at);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `addLocker` (IN `p_slot_number` INT, IN `p_location_id` INT, IN `p_size_id` INT, IN `p_academic_year_id` INT)   BEGIN
    DECLARE EXIT HANDLER FOR 1062
    BEGIN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Cannot add: Slot number already exists.';
    END;

    INSERT INTO locker_slots (
        slot_number,
        location_id,
        size_id,
        academic_year_id,
        status
    )
    VALUES (
        p_slot_number,
        p_location_id,
        p_size_id,
        p_academic_year_id,
        'Available'
    );
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `addLockerLocation` (IN `p_location` VARCHAR(255))   BEGIN
	DECLARE EXIT HANDLER FOR 1062
 
    BEGIN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Cannot add: Location already exist.';
    END;
	
    INSERT INTO locker_locations (location)
    VALUES (p_location);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `addLockerSize` (IN `p_size` VARCHAR(255), IN `p_price` DOUBLE(10,2))   BEGIN
	DECLARE EXIT HANDLER FOR 1062
 
    BEGIN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Cannot add: Size already exist.';
    END;
    
    INSERT INTO locker_sizes (size, price)
    VALUES (p_size, p_price);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `applyLocker` (IN `u_user_id` VARCHAR(255), IN `u_slot_id` INT)   BEGIN
    DECLARE active_application_count INT DEFAULT 0;
    DECLARE slot_status VARCHAR(50);
    DECLARE v_start_at DATE;
    DECLARE v_end_at DATE;
    DECLARE slot_exists INT DEFAULT 0;

    -- Check slot exists + get data
    SELECT 
        ls.status,
        ac.start_at,
        ac.end_at
    INTO 
        slot_status,
        v_start_at,
        v_end_at
    FROM locker_slots ls
    LEFT JOIN academic_calendar ac 
        ON ls.academic_year_id = ac.id
    WHERE ls.id = u_slot_id;

    -- If no row found
    IF slot_status IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Slot not found';
    END IF;

    -- Availability check
    IF slot_status <> 'Available' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'This slot is not available for application';
    END IF;

    -- Date validation
    IF CURDATE() < v_start_at THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Application has not started yet';

    ELSEIF CURDATE() >= v_end_at THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Application period has ended';
    END IF;

    -- Duplicate application check
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `deleteAcademicYear` (IN `p_id` INT)   BEGIN
	DECLARE EXIT HANDLER FOR 1451
 
    BEGIN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Cannot delete: Academic year still assigned to slots.';
    END;
 
    DELETE FROM academic_calendar
    WHERE id = p_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `deleteLocker` (IN `p_id` INT)   BEGIN
	DECLARE EXIT HANDLER FOR 1451
    
    BEGIN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Cannot delete: Slot already has an application.';
    END;
    
    DELETE FROM locker_slots
    WHERE id = p_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `deleteLockerLocation` (IN `p_id` INT)   BEGIN
    DECLARE EXIT HANDLER FOR 1451
    
    BEGIN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Cannot delete: Location still contains slots.';
    END;
    
    DELETE FROM locker_locations
    WHERE id = p_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `deleteLockerSize` (IN `p_id` INT)   BEGIN
	DECLARE EXIT HANDLER FOR 1451
 
    BEGIN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Cannot delete: Size still assigned to slots.';
    END;
    
    DELETE FROM locker_sizes
    WHERE id = p_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `endLockerApplication` ()   BEGIN

    UPDATE locker_slots SET status = 'Available'
    WHERE status = 'Occupied' AND end_at <= CURRENT_DATE;

    UPDATE locker_applications la INNER JOIN locker_slots ls ON la.slot_id = ls.id
    SET
        la.status = 'Ended',
        la.payment = 'Unpaid',
        la.updated_at = NOW()
    WHERE la.status = 'Accepted' AND ls.end_at <= CURRENT_DATE;

    UPDATE locker_applications la INNER JOIN locker_slots ls
        ON la.slot_id = ls.id
    SET
        la.status = 'Cancelled',
        la.updated_at = NOW()
    WHERE la.status = 'Pending' AND ls.end_at <= CURRENT_DATE;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getAcademicCalendar` (IN `p_limit` INT, IN `p_offset` INT)   BEGIN
    SELECT 
    	id,
        academic_year,
        semester,
        DATE_FORMAT(start_at, '%M %d, %Y') AS start_at,
        DATE_FORMAT(end_at, '%M %d, %Y') AS end_at,
        DATE_FORMAT(created_at, '%M %d, %Y %h:%i:%s %p') AS created_at,
		DATE_FORMAT(updated_at, '%M %d, %Y %h:%i:%s %p') AS updated_at
    	FROM academic_calendar
    ORDER BY created_at DESC LIMIT p_limit OFFSET p_offset;
    
    SELECT COUNT(*) AS academicCalendarTotal
    FROM academic_calendar;
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
        ac.academic_year,
        ac.semester,
        DATE_FORMAT(ac.start_at, '%M %d, %Y') AS start_at,
        DATE_FORMAT(ac.end_at, '%M %d, %Y') AS end_at,
        DATE_FORMAT(la.updated_at, '%M %d, %Y') AS updated_at
    FROM locker_applications la

    INNER JOIN users u ON la.user_id = u.id
    INNER JOIN locker_slots ls ON la.slot_id = ls.id
    INNER JOIN locker_locations ll ON ls.location_id = ll.id
    INNER JOIN locker_sizes lsz ON ls.size_id = lsz.id
    LEFT JOIN academic_calendar ac ON ls.academic_year_id = ac.id

    WHERE la.status = 'Accepted'
    ORDER BY la.updated_at DESC LIMIT p_limit OFFSET p_offset;

    SELECT COUNT(*) AS acceptedTotal FROM locker_applications
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
        ac.academic_year,
        ac.semester,
        DATE_FORMAT(ac.start_at, '%M %d, %Y') AS start_at,
        DATE_FORMAT(ac.end_at, '%M %d, %Y') AS end_at,
        DATE_FORMAT(la.updated_at, '%M %d, %Y') AS updated_at
    FROM locker_applications la

    INNER JOIN users u ON la.user_id = u.id
    INNER JOIN locker_slots ls ON la.slot_id = ls.id
    INNER JOIN locker_locations ll ON ls.location_id = ll.id
    INNER JOIN locker_sizes lsz ON ls.size_id = lsz.id
    LEFT JOIN academic_calendar ac ON ls.academic_year_id = ac.id

    WHERE la.status = 'Ended' AND la.payment = 'Unpaid'
    ORDER BY la.updated_at DESC LIMIT p_limit OFFSET p_offset;

    SELECT COUNT(*) AS endedTotal FROM locker_applications
    WHERE status = 'Ended' AND payment = 'Unpaid';

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
        ac.academic_year,
        ac.semester,
        DATE_FORMAT(ac.start_at, '%M %d, %Y') AS start_at,
        DATE_FORMAT(ac.end_at, '%M %d, %Y') AS end_at,
        DATE_FORMAT(la.updated_at, '%M %d, %Y') AS updated_at
    FROM locker_applications la

    INNER JOIN users u ON la.user_id = u.id
    INNER JOIN locker_slots ls ON la.slot_id = ls.id
    INNER JOIN locker_locations ll ON ls.location_id = ll.id
    INNER JOIN locker_sizes lsz ON ls.size_id = lsz.id
    LEFT JOIN academic_calendar ac ON ls.academic_year_id = ac.id

    WHERE la.status IN ('Cancelled', 'Rejected', 'Revoked', 'Ended')
    ORDER BY la.updated_at DESC LIMIT p_limit OFFSET p_offset;

    SELECT COUNT(*) AS historyTotal FROM locker_applications
    WHERE status IN ('Cancelled', 'Rejected', 'Revoked', 'Ended');

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getLockerLocations` (IN `p_limit` INT, IN `p_offset` INT)   BEGIN
    SELECT 
        id,
        location,
        DATE_FORMAT(created_at, '%M %d, %Y %h:%i:%s %p') AS created_at,
        DATE_FORMAT(updated_at, '%M %d, %Y %h:%i:%s %p') AS updated_at
	FROM locker_locations
    ORDER BY created_at DESC LIMIT p_limit OFFSET p_offset;
    
    SELECT COUNT(*) AS lockerLocationsTotal
    FROM locker_locations;

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

    SELECT COUNT(*) AS lockerLogsTotal
    FROM locker_logs;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getLockersByLocation` (IN `p_location_id` INT)   BEGIN
    SELECT
        ls.id,
        ls.slot_number,
        ls.size_id,
        lsz.size,
        lsz.price,
        ls.status,
        ls.academic_year_id,
        ac.academic_year,
        ac.semester,
        DATE_FORMAT(ac.start_at, '%M %d, %Y') AS start_at,
        DATE_FORMAT(ac.end_at, '%M %d, %Y') AS end_at,
        la.user_id
    FROM locker_slots ls

    INNER JOIN locker_sizes lsz ON ls.size_id = lsz.id
    LEFT JOIN academic_calendar ac ON ls.academic_year_id = ac.id
    LEFT JOIN locker_applications la ON ls.id = la.slot_id AND la.status = 'Accepted'

    WHERE ls.location_id = p_location_id
    ORDER BY ls.slot_number ASC;
    
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getLockerSizes` (IN `p_limit` INT, IN `p_offset` INT)   BEGIN
    SELECT 
    	id,
        size,
        price,
        DATE_FORMAT(created_at, '%M %d, %Y %h:%i:%s %p') AS created_at,
		DATE_FORMAT(updated_at, '%M %d, %Y %h:%i:%s %p') AS updated_at
    	FROM locker_sizes
    ORDER BY created_at DESC LIMIT p_limit OFFSET p_offset;
    
    SELECT COUNT(*) AS lockerSizesTotal
    FROM locker_sizes;
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
	ac.academic_year,
	ac.semester,
        ac.start_at,
        ac.end_at,
        DATE_FORMAT(la.updated_at, '%M %d, %Y') AS updated_at
    FROM locker_applications la

    INNER JOIN locker_slots ls ON la.slot_id = ls.id
    INNER JOIN locker_locations ll ON ls.location_id = ll.id
    INNER JOIN locker_sizes sz ON ls.size_id = sz.id
    LEFT JOIN academic_calendar ac ON ls.academic_year_id = ac.id

    WHERE la.user_id = p_user_id AND la.status = 'Accepted'
    ORDER BY la.updated_at DESC LIMIT p_limit OFFSET p_offset;

    SELECT COUNT(*) AS myAcceptedTotal FROM locker_applications
    WHERE user_id = p_user_id AND status = 'Accepted';

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
	ac.academic_year,
	ac.semester,
        ac.start_at,
        ac.end_at,
        DATE_FORMAT(la.updated_at, '%M %d, %Y') AS updated_at
    FROM locker_applications la

    INNER JOIN locker_slots ls ON la.slot_id = ls.id
    INNER JOIN locker_locations ll ON ls.location_id = ll.id
    INNER JOIN locker_sizes sz ON ls.size_id = sz.id
    LEFT JOIN academic_calendar ac ON ls.academic_year_id = ac.id

    WHERE la.user_id = p_user_id AND la.status = 'Ended' AND la.payment = 'Unpaid'

    ORDER BY la.updated_at DESC LIMIT p_limit OFFSET p_offset;

    SELECT COUNT(*) AS myEndedTotal FROM locker_applications
    WHERE user_id = p_user_id AND status = 'Ended' AND payment = 'Unpaid';

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
	ac.academic_year,
	ac.semester,
        ac.start_at,
        ac.end_at,
        DATE_FORMAT(la.updated_at, '%M %d, %Y') AS updated_at
    FROM locker_applications la

    INNER JOIN locker_slots ls ON la.slot_id = ls.id
    INNER JOIN locker_locations ll ON ls.location_id = ll.id
    INNER JOIN locker_sizes sz ON ls.size_id = sz.id
    LEFT JOIN academic_calendar ac ON ls.academic_year_id = ac.id

    WHERE la.user_id = p_user_id AND la.status IN ('Cancelled', 'Rejected', 'Revoked', 'Ended') AND la.payment IN ('Paid', 'Unpaid')
    ORDER BY la.updated_at DESC LIMIT p_limit OFFSET p_offset;

    SELECT COUNT(*) AS myHistoryTotal FROM locker_applications
    WHERE user_id = p_user_id AND status IN ('Cancelled', 'Rejected', 'Revoked', 'Ended') AND payment IN ('Paid', 'Unpaid');

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
	ac.academic_year,
	ac.semester,
        ac.start_at,
        ac.end_at,
        DATE_FORMAT(la.created_at, '%M %d, %Y') AS created_at
    FROM locker_applications la

    INNER JOIN locker_slots ls ON la.slot_id = ls.id
    INNER JOIN locker_locations ll ON ls.location_id = ll.id
    INNER JOIN locker_sizes sz ON ls.size_id = sz.id
    LEFT JOIN academic_calendar ac ON ls.academic_year_id = ac.id

    WHERE la.user_id = p_user_id AND la.status = 'Pending'
    ORDER BY la.created_at DESC LIMIT p_limit OFFSET p_offset;

    SELECT COUNT(*) AS myPendingTotal FROM locker_applications
    WHERE user_id = p_user_id AND status = 'Pending';

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
        ac.academic_year,
        ac.semester,
        DATE_FORMAT(ac.start_at, '%M %d, %Y') AS start_at,
        DATE_FORMAT(ac.end_at, '%M %d, %Y') AS end_at,
        DATE_FORMAT(la.created_at, '%M %d, %Y') AS created_at
    FROM locker_applications la

    INNER JOIN users u ON la.user_id = u.id
    INNER JOIN locker_slots ls ON la.slot_id = ls.id
    INNER JOIN locker_locations ll ON ls.location_id = ll.id
    INNER JOIN locker_sizes lsz ON ls.size_id = lsz.id
    LEFT JOIN academic_calendar ac ON ls.academic_year_id = ac.id

    WHERE la.status = 'Pending'
    ORDER BY la.created_at DESC LIMIT p_limit OFFSET p_offset;

    SELECT COUNT(*) AS pendingTotal FROM locker_applications
    WHERE status = 'Pending';

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getSearchFilterAcademicCalendar` (IN `searchTerm` VARCHAR(255), IN `filterTerm` VARCHAR(255), IN `p_limit` INT, IN `p_offset` INT)   BEGIN
    SELECT 
        id,
        academic_year,
        semester,
        DATE_FORMAT(start_at, '%M %d, %Y') AS start_at,
        DATE_FORMAT(end_at, '%M %d, %Y') AS end_at,
        DATE_FORMAT(created_at, '%M %d, %Y %h:%i:%s %p') AS created_at,
        DATE_FORMAT(updated_at, '%M %d, %Y %h:%i:%s %p') AS updated_at
    FROM academic_calendar

    WHERE (
        searchTerm IS NULL
        OR searchTerm = ''
        OR academic_year LIKE CONCAT('%', searchTerm, '%')
        OR semester LIKE CONCAT('%', searchTerm, '%')
    )

    ORDER BY
        CASE WHEN filterTerm = 'a-z' THEN academic_year END ASC,
        CASE WHEN filterTerm = 'z-a' THEN academic_year END DESC,
        CASE WHEN filterTerm = 'oldest' THEN created_at END ASC,
        CASE WHEN filterTerm = 'newest' THEN created_at END DESC

    LIMIT p_limit OFFSET p_offset;
    
    SELECT COUNT(*) AS academicCalendarTotal
    FROM academic_calendar
    WHERE (
        searchTerm IS NULL
        OR searchTerm = ''
        OR academic_year LIKE CONCAT('%', searchTerm, '%')
        OR semester LIKE CONCAT('%', searchTerm, '%')
    );

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
        ac.academic_year,
        ac.semester,
        DATE_FORMAT(ac.start_at, '%M %d, %Y') AS start_at,
        DATE_FORMAT(ac.end_at, '%M %d, %Y') AS end_at,
        DATE_FORMAT(la.updated_at, '%M %d, %Y') AS updated_at
    FROM locker_applications la
 
    INNER JOIN users u ON la.user_id = u.id
    INNER JOIN locker_slots ls ON la.slot_id = ls.id
    INNER JOIN locker_locations ll ON ls.location_id = ll.id
    INNER JOIN locker_sizes lsz ON ls.size_id = lsz.id
    LEFT JOIN academic_calendar ac ON ls.academic_year_id = ac.id
 
    WHERE la.status = 'Accepted'
   AND (
        searchTerm IS NULL OR searchTerm = ''
        OR la.id LIKE CONCAT('%', searchTerm, '%')
        OR la.user_id LIKE CONCAT('%', searchTerm, '%')
        OR la.slot_id LIKE CONCAT('%', searchTerm, '%')
 
        OR u.firstname LIKE CONCAT('%', searchTerm, '%')
        OR u.middlename LIKE CONCAT('%', searchTerm, '%')
        OR u.lastname LIKE CONCAT('%', searchTerm, '%')
        OR u.username LIKE CONCAT('%', searchTerm, '%')
        OR u.email LIKE CONCAT('%', searchTerm, '%')
 
        OR ll.location LIKE CONCAT('%', searchTerm, '%')
        OR lsz.size LIKE CONCAT('%', searchTerm, '%')
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
    INNER JOIN locker_sizes lsz ON ls.size_id = lsz.id
    LEFT JOIN academic_calendar ac ON ls.academic_year_id = ac.id
 
    WHERE la.status = 'Accepted'
    AND (
        searchTerm IS NULL OR searchTerm = ''
        OR la.id LIKE CONCAT('%', searchTerm, '%')
        OR la.user_id LIKE CONCAT('%', searchTerm, '%')
        OR la.slot_id LIKE CONCAT('%', searchTerm, '%')
 
        OR u.firstname LIKE CONCAT('%', searchTerm, '%')
        OR u.middlename LIKE CONCAT('%', searchTerm, '%')
        OR u.lastname LIKE CONCAT('%', searchTerm, '%')
        OR u.username LIKE CONCAT('%', searchTerm, '%')
        OR u.email LIKE CONCAT('%', searchTerm, '%')
 
        OR ll.location LIKE CONCAT('%', searchTerm, '%')
        OR lsz.size LIKE CONCAT('%', searchTerm, '%')
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
       	ac.academic_year,
        ac.semester,
        DATE_FORMAT(ac.start_at, '%M %d, %Y') AS start_at,
        DATE_FORMAT(ac.end_at, '%M %d, %Y') AS end_at,
        DATE_FORMAT(la.updated_at, '%M %d, %Y') AS updated_at
    FROM locker_applications la
 
    INNER JOIN users u ON la.user_id = u.id
    INNER JOIN locker_slots ls ON la.slot_id = ls.id
    INNER JOIN locker_locations ll ON ls.location_id = ll.id
    INNER JOIN locker_sizes lsz ON ls.size_id = lsz.id
    LEFT JOIN academic_calendar ac ON ls.academic_year_id = ac.id
 
    WHERE la.status = 'Ended' AND la.payment = 'Unpaid'
    AND (
        searchTerm IS NULL OR searchTerm = ''
        OR la.id LIKE CONCAT('%', searchTerm, '%')
        OR la.user_id LIKE CONCAT('%', searchTerm, '%')
        OR la.slot_id LIKE CONCAT('%', searchTerm, '%')
 
        OR u.firstname LIKE CONCAT('%', searchTerm, '%')
        OR u.middlename LIKE CONCAT('%', searchTerm, '%')
        OR u.lastname LIKE CONCAT('%', searchTerm, '%')
        OR u.username LIKE CONCAT('%', searchTerm, '%')
        OR u.email LIKE CONCAT('%', searchTerm, '%')
 
        OR ll.location LIKE CONCAT('%', searchTerm, '%')
        OR lsz.size LIKE CONCAT('%', searchTerm, '%')
        OR ls.slot_number LIKE CONCAT('%', searchTerm, '%')
    )
    ORDER BY
        CASE WHEN filterTerm = 'newest' THEN la.updated_at END DESC,
        CASE WHEN filterTerm = 'oldest' THEN la.updated_at END ASC,
        la.updated_at DESC
    LIMIT p_limit OFFSET p_offset;

    SELECT COUNT(*) AS endedTotal
    FROM locker_applications la
 
    INNER JOIN users u ON la.user_id = u.id
    INNER JOIN locker_slots ls ON la.slot_id = ls.id
    INNER JOIN locker_locations ll ON ls.location_id = ll.id
    INNER JOIN locker_sizes lsz ON ls.size_id = lsz.id
    LEFT JOIN academic_calendar ac ON ls.academic_year_id = ac.id
 
    WHERE la.status = 'Ended' AND payment = 'Unpaid'
    AND (
        searchTerm IS NULL OR searchTerm = ''
        OR la.id LIKE CONCAT('%', searchTerm, '%')
        OR la.user_id LIKE CONCAT('%', searchTerm, '%')
        OR la.slot_id LIKE CONCAT('%', searchTerm, '%')
 
        OR u.firstname LIKE CONCAT('%', searchTerm, '%')
        OR u.middlename LIKE CONCAT('%', searchTerm, '%')
        OR u.lastname LIKE CONCAT('%', searchTerm, '%')
        OR u.username LIKE CONCAT('%', searchTerm, '%')
        OR u.email LIKE CONCAT('%', searchTerm, '%')
 
        OR ll.location LIKE CONCAT('%', searchTerm, '%')
        OR lsz.size LIKE CONCAT('%', searchTerm, '%')
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
        ac.academic_year,
        ac.semester,
        DATE_FORMAT(ac.start_at, '%M %d, %Y') AS start_at,
        DATE_FORMAT(ac.end_at, '%M %d, %Y') AS end_at,
        DATE_FORMAT(la.updated_at, '%M %d, %Y') AS updated_at
    FROM locker_applications la
 
    INNER JOIN users u ON la.user_id = u.id
    INNER JOIN locker_slots ls ON la.slot_id = ls.id
    INNER JOIN locker_locations ll ON ls.location_id = ll.id
    INNER JOIN locker_sizes lsz ON ls.size_id = lsz.id
    LEFT JOIN academic_calendar ac ON ls.academic_year_id = ac.id
 
    WHERE la.status IN ('Cancelled', 'Rejected', 'Revoked', 'Ended') AND la.payment IN ('Paid', 'Unpaid')
      AND (
        searchTerm IS NULL OR searchTerm = ''
        OR la.id LIKE CONCAT('%', searchTerm, '%')
        OR la.user_id LIKE CONCAT('%', searchTerm, '%')
        OR la.slot_id LIKE CONCAT('%', searchTerm, '%')
 
        OR u.firstname LIKE CONCAT('%', searchTerm, '%')
        OR u.middlename LIKE CONCAT('%', searchTerm, '%')
        OR u.lastname LIKE CONCAT('%', searchTerm, '%')
        OR u.username LIKE CONCAT('%', searchTerm, '%')
        OR u.email LIKE CONCAT('%', searchTerm, '%')
 
        OR ll.location LIKE CONCAT('%', searchTerm, '%')
        OR lsz.size LIKE CONCAT('%', searchTerm, '%')
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
    INNER JOIN locker_sizes lsz ON ls.size_id = lsz.id
    LEFT JOIN academic_calendar ac ON ls.academic_year_id = ac.id
 
    WHERE la.status IN ('Cancelled', 'Rejected', 'Revoked', 'Ended') AND la.payment IN ('Paid', 'Unpaid')
      AND (
        searchTerm IS NULL OR searchTerm = ''
        OR la.id LIKE CONCAT('%', searchTerm, '%')
        OR la.user_id LIKE CONCAT('%', searchTerm, '%')
        OR la.slot_id LIKE CONCAT('%', searchTerm, '%')
 
        OR u.firstname LIKE CONCAT('%', searchTerm, '%')
        OR u.middlename LIKE CONCAT('%', searchTerm, '%')
        OR u.lastname LIKE CONCAT('%', searchTerm, '%')
        OR u.username LIKE CONCAT('%', searchTerm, '%')
        OR u.email LIKE CONCAT('%', searchTerm, '%')
 
        OR ll.location LIKE CONCAT('%', searchTerm, '%')
        OR lsz.size LIKE CONCAT('%', searchTerm, '%')
        OR ls.slot_number LIKE CONCAT('%', searchTerm, '%')
    );

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getSearchFilterLockerLocation` (IN `searchTerm` VARCHAR(255), IN `filterTerm` VARCHAR(255), IN `p_limit` INT, IN `p_offset` INT)   BEGIN
    SELECT 
        id,
        location,
        DATE_FORMAT(created_at, '%M %d, %Y %h:%i:%s %p') AS created_at,
        DATE_FORMAT(updated_at, '%M %d, %Y %h:%i:%s %p') AS updated_at
    FROM locker_locations

    WHERE
    	searchTerm IS NULL
        OR searchTerm = ''
        OR location LIKE CONCAT('%', searchTerm, '%')
        
    ORDER BY
        CASE WHEN filterTerm = 'a-z' THEN location END ASC,
        CASE WHEN filterTerm = 'z-a' THEN location END DESC,
        CASE WHEN filterTerm = 'oldest' THEN created_at END ASC,
        CASE WHEN filterTerm = 'newest' THEN created_at END DESC
        
	LIMIT p_limit OFFSET p_offset;
    
    SELECT COUNT(*) AS lockerLocationsTotal
    FROM locker_locations
		WHERE
    	searchTerm IS NULL
        OR searchTerm = ''
        OR location LIKE CONCAT('%', searchTerm, '%');
        
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getSearchFilterLockerSizes` (IN `searchTerm` VARCHAR(255), IN `filterTerm` VARCHAR(255), IN `p_limit` INT, IN `p_offset` INT)   BEGIN
    SELECT
        id,
        size,
        price,
        DATE_FORMAT(created_at, '%M %d, %Y %h:%i:%s %p') AS created_at,
		DATE_FORMAT(updated_at, '%M %d, %Y %h:%i:%s %p') AS updated_at
    FROM locker_sizes

    WHERE (
        searchTerm IS NULL
        OR searchTerm = ''
        OR size LIKE CONCAT('%', searchTerm, '%')
        OR price LIKE CONCAT('%', searchTerm, '%')
    )

    ORDER BY
        CASE WHEN filterTerm = 'a-z' THEN size END ASC,
        CASE WHEN filterTerm = 'z-a' THEN size END DESC,
        CASE WHEN filterTerm = 'oldest' THEN created_at END ASC,
        CASE WHEN filterTerm = 'newest' THEN created_at END DESC,
        CASE WHEN filterTerm = 'cheaper' THEN price END ASC,
        CASE WHEN filterTerm = 'expensive' THEN price END DESC
        
	LIMIT p_limit OFFSET p_offset;
    
    SELECT COUNT(*) AS lockerSizesTotal
    FROM locker_sizes
		WHERE (
        searchTerm IS NULL
        OR searchTerm = ''
        OR size LIKE CONCAT('%', searchTerm, '%')
        OR price LIKE CONCAT('%', searchTerm, '%')
    );

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
        ac.academic_year,
        ac.semester,
        DATE_FORMAT(ac.start_at, '%M %d, %Y') AS start_at,
        DATE_FORMAT(ac.end_at, '%M %d, %Y') AS end_at,
        DATE_FORMAT(la.created_at, '%M %d, %Y') AS created_at
    FROM locker_applications la
 
    INNER JOIN users u ON la.user_id = u.id
    INNER JOIN locker_slots ls ON la.slot_id = ls.id
    INNER JOIN locker_locations ll ON ls.location_id = ll.id
    INNER JOIN locker_sizes lsz ON ls.size_id = lsz.id
    LEFT JOIN academic_calendar ac ON ls.academic_year_id = ac.id
 
    WHERE la.status = 'Pending'
    AND (
        searchTerm IS NULL OR searchTerm = ''
        OR la.id LIKE CONCAT('%', searchTerm, '%')
        OR la.user_id LIKE CONCAT('%', searchTerm, '%')
        OR la.slot_id LIKE CONCAT('%', searchTerm, '%')
 
        OR u.firstname LIKE CONCAT('%', searchTerm, '%')
        OR u.middlename LIKE CONCAT('%', searchTerm, '%')
        OR u.lastname LIKE CONCAT('%', searchTerm, '%')
        OR u.username LIKE CONCAT('%', searchTerm, '%')
        OR u.email LIKE CONCAT('%', searchTerm, '%')
 
        OR ll.location LIKE CONCAT('%', searchTerm, '%')
        OR lsz.size LIKE CONCAT('%', searchTerm, '%')
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
    INNER JOIN locker_sizes lsz ON ls.size_id = lsz.id
    LEFT JOIN academic_calendar ac ON ls.academic_year_id = ac.id
 
    WHERE la.status = 'Pending'
    AND (
        searchTerm IS NULL OR searchTerm = ''
        OR la.id LIKE CONCAT('%', searchTerm, '%')
        OR la.user_id LIKE CONCAT('%', searchTerm, '%')
        OR la.slot_id LIKE CONCAT('%', searchTerm, '%')
 
        OR u.firstname LIKE CONCAT('%', searchTerm, '%')
        OR u.middlename LIKE CONCAT('%', searchTerm, '%')
        OR u.lastname LIKE CONCAT('%', searchTerm, '%')
        OR u.username LIKE CONCAT('%', searchTerm, '%')
        OR u.email LIKE CONCAT('%', searchTerm, '%')
 
        OR ll.location LIKE CONCAT('%', searchTerm, '%')
        OR lsz.size LIKE CONCAT('%', searchTerm, '%')
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
        created_at
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `getUserById` (IN `p_value` VARCHAR(255))   BEGIN

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
    WHERE id = p_value
       OR username = p_value
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `get_paid_ended_locker_applications_amount` ()   BEGIN
    SELECT 
        COALESCE(SUM(lsz.price), 0) AS paid_total_amount
    FROM locker_applications la
    INNER JOIN locker_slots ls ON la.slot_id = ls.id
    INNER JOIN locker_sizes lsz ON ls.size_id = lsz.id
    WHERE la.payment = 'Paid'
    AND la.status = 'Ended';
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `get_paid_ended_locker_applications_count` ()   BEGIN
    SELECT 
        COUNT(*) AS paid_total_transactions
    FROM locker_applications
    WHERE payment = 'Paid'
    AND status = 'Ended';
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
        slot_number,
        location,
        size,
        price,
        status,
        payment,
        DATE_FORMAT(created_at, '%M %d, %Y') AS created_at
    FROM recent_locker_application;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `get_recent_user_account_activation` ()   BEGIN
    SELECT
        id,
        username,
        lastname,
	firstname,
	middlename,
        DATE_FORMAT(created_at, '%M %d, %Y') AS created_at
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `get_unpaid_ended_locker_applications_amount` ()   BEGIN
    SELECT 
        COALESCE(SUM(lsz.price), 0) AS unpaid_total_amount
    FROM locker_applications la
    INNER JOIN locker_slots ls ON la.slot_id = ls.id
    INNER JOIN locker_sizes lsz ON ls.size_id = lsz.id
    WHERE la.payment = 'Unpaid'
    AND la.status = 'Ended';
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `get_unpaid_ended_locker_applications_count` ()   BEGIN
    SELECT 
        COUNT(*) AS unpaid_total_transactions
    FROM locker_applications
    WHERE payment = 'Unpaid'
    AND status = 'Ended';
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `updateAcademicYear` (IN `p_id` INT, IN `p_academic_year` VARCHAR(255), IN `p_semester` VARCHAR(255), IN `p_start_at` DATE, IN `p_end_at` DATE)   BEGIN
    UPDATE academic_calendar
    SET academic_year = p_academic_year,
        semester = p_semester,
        start_at = p_start_at,
        end_at = p_end_at,
	updated_at = NOW()
    WHERE id = p_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `updateLocker` (IN `p_id` INT, IN `p_slot_number` INT, IN `p_size_id` INT, IN `p_academic_year_id` INT, IN `p_status` VARCHAR(255))   BEGIN
    DECLARE EXIT HANDLER FOR 1062
    BEGIN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Cannot update: Slot number already exists.';
    END;

    UPDATE locker_slots
    SET
        slot_number = p_slot_number,
        size_id = p_size_id,
        academic_year_id = p_academic_year_id,
        status = p_status,
        updated_at = NOW()
    WHERE id = p_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `updateLockerLocation` (IN `p_id` INT, IN `p_location` VARCHAR(255))   BEGIN
    UPDATE locker_locations
    SET 
        location = p_location,
        updated_at = NOW()
    WHERE id = p_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `updateLockerSize` (IN `p_id` INT, IN `p_size` VARCHAR(255), IN `p_price` DOUBLE)   BEGIN
    UPDATE locker_sizes
    SET size = p_size,
        price = p_price,
	updated_at = NOW()
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
-- Table structure for table `academic_calendar`
--

CREATE TABLE `academic_calendar` (
  `id` int(11) NOT NULL,
  `academic_year` varchar(255) NOT NULL,
  `semester` varchar(255) NOT NULL,
  `start_at` date NOT NULL,
  `end_at` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `academic_calendar`
--

INSERT INTO `academic_calendar` (`id`, `academic_year`, `semester`, `start_at`, `end_at`, `created_at`, `updated_at`) VALUES
(1, '2026-2027', '1st Semester', '2026-05-03', '2027-01-08', '2026-05-23 15:03:43', '2026-05-23 16:01:40'),
(2, '2026-2027', '2nd Semester', '2027-01-18', '2027-06-18', '2026-05-23 15:03:43', NULL);

--
-- Triggers `academic_calendar`
--
DELIMITER $$
CREATE TRIGGER `after_academic_calendar_deletion` AFTER DELETE ON `academic_calendar` FOR EACH ROW BEGIN
    DECLARE action VARCHAR(255) DEFAULT 'Delete';
    DECLARE description VARCHAR(255);

    SET description = CONCAT(
        'Academic year ',
        OLD.academic_year,
        ' (',
        OLD.semester,
        ') from ',
        DATE_FORMAT(OLD.start_at, '%M %d, %Y'),
        ' to ',
        DATE_FORMAT(OLD.end_at, '%M %d, %Y'),
        ' has been deleted.'
    );

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
CREATE TRIGGER `after_academic_calendar_insertion` AFTER INSERT ON `academic_calendar` FOR EACH ROW BEGIN
    DECLARE action VARCHAR(255) DEFAULT 'Add';
    DECLARE description VARCHAR(255);

    SET description = CONCAT(
        'Academic year ',
        NEW.academic_year,
        ' (',
        NEW.semester,
        ') has been added from ',
        DATE_FORMAT(NEW.start_at, '%M %d, %Y'),
        ' to ',
        DATE_FORMAT(NEW.end_at, '%M %d, %Y'),
        '.'
    );

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
CREATE TRIGGER `after_academic_calendar_updation` AFTER UPDATE ON `academic_calendar` FOR EACH ROW BEGIN
    DECLARE action VARCHAR(255) DEFAULT 'Update';
    DECLARE description VARCHAR(255);

    SET description = CONCAT(
        'Academic year ',
        OLD.academic_year,
        ' (',
        OLD.semester,
        ') has been updated to ',
        NEW.academic_year,
        ' (',
        NEW.semester,
        ') from ',
        DATE_FORMAT(NEW.start_at, '%M %d, %Y'),
        ' to ',
        DATE_FORMAT(NEW.end_at, '%M %d, %Y'),
        '.'
    );

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
(51, '2024-31214', 26, 'Cancelled', 'Unpaid', '2026-05-22 14:33:05', '2026-05-22 14:33:22'),
(52, '2024-31214', 32, 'Pending', 'Unpaid', '2026-05-23 05:10:49', NULL),
(53, '2024-31214', 26, 'Pending', 'Unpaid', '2026-05-23 05:10:52', NULL),
(54, '2024-31214', 36, 'Pending', 'Unpaid', '2026-05-23 05:11:13', NULL),
(55, '2024-31214', 35, 'Pending', 'Unpaid', '2026-05-23 05:11:16', NULL),
(56, '2024-11468', 38, 'Pending', 'Unpaid', '2026-05-23 05:11:49', NULL),
(57, '2024-11468', 37, 'Pending', 'Unpaid', '2026-05-23 05:11:51', NULL),
(58, '2024-11468', 40, 'Pending', 'Unpaid', '2026-05-23 05:11:54', NULL),
(59, '2024-11468', 39, 'Rejected', 'Unpaid', '2026-05-23 05:11:55', '2026-05-23 05:58:40'),
(60, '2024-11468', 32, 'Accepted', 'Unpaid', '2026-05-23 05:12:04', '2026-05-23 05:58:37'),
(61, '2024-11468', 26, 'Pending', 'Unpaid', '2026-05-23 05:12:06', NULL),
(62, '2024-11468', 36, 'Pending', 'Unpaid', '2026-05-23 05:12:13', NULL),
(63, '2024-11468', 35, 'Cancelled', 'Unpaid', '2026-05-23 05:12:15', '2026-05-23 09:41:44'),
(64, '2024-31214', 38, 'Accepted', 'Unpaid', '2026-05-23 05:12:57', '2026-05-23 05:58:20'),
(65, '2024-31214', 37, 'Rejected', 'Unpaid', '2026-05-23 05:12:59', '2026-05-23 05:58:18'),
(66, '2024-31214', 40, 'Rejected', 'Unpaid', '2026-05-23 05:13:01', '2026-05-23 05:58:15'),
(67, '2024-31214', 39, 'Accepted', 'Unpaid', '2026-05-23 05:13:03', '2026-05-23 05:58:12'),
(68, '2024-31214', 40, 'Pending', 'Unpaid', '2026-05-23 16:02:56', NULL);

--
-- Triggers `locker_applications`
--
DELIMITER $$
CREATE TRIGGER `after_locker_acception` AFTER UPDATE ON `locker_applications` FOR EACH ROW BEGIN
    DECLARE action VARCHAR(255) DEFAULT 'Accept';
    DECLARE v_slot_number INT;
    DECLARE v_location VARCHAR(255);
    DECLARE description TEXT;

    IF NEW.status = 'Accepted' AND OLD.status <> 'Accepted' THEN

        SELECT ls.slot_number, ll.location 
        INTO v_slot_number, v_location 
        FROM locker_slots ls
        INNER JOIN locker_locations ll ON ls.location_id = ll.id
        WHERE ls.id = NEW.slot_id
        LIMIT 1;

        SET description = CONCAT(
	    'User ',
            NEW.user_id,
            ' application on slot #',
            v_slot_number,
            ' at ',
            v_location,
            ' accepted.'
        );

        INSERT INTO locker_logs (action, description)
        VALUES (action, description);

    END IF;

END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_locker_application` AFTER INSERT ON `locker_applications` FOR EACH ROW BEGIN
    DECLARE action VARCHAR(255) DEFAULT 'Apply';
    DECLARE v_slot_number INT;
    DECLARE v_location VARCHAR(255);
    DECLARE description TEXT;

    SELECT ls.slot_number, ll.location INTO v_slot_number, v_location FROM locker_slots ls
    INNER JOIN locker_locations ll ON ls.location_id = ll.id
    WHERE ls.id = NEW.slot_id LIMIT 1;

    SET description = CONCAT(
	'User ',
        NEW.user_id,
        ' applied (Slot #',
        v_slot_number,
        ', ',
        v_location,
        ').'
    );

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
    DECLARE action VARCHAR(255) DEFAULT 'Cancel';
    DECLARE v_slot_number INT;
    DECLARE v_location VARCHAR(255);
    DECLARE description TEXT;

    IF NEW.status = 'Cancelled' AND OLD.status <> 'Cancelled' THEN

        SELECT ls.slot_number, ll.location INTO v_slot_number, v_location FROM locker_slots ls
        INNER JOIN locker_locations ll ON ls.location_id = ll.id
        WHERE ls.id = NEW.slot_id LIMIT 1;

        SET description = CONCAT(
            'User ',
            NEW.user_id,
            ' cancelled (Slot #',
            v_slot_number,
            ', ',
            v_location,
            ').'
        );

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
CREATE TRIGGER `after_locker_ending` AFTER UPDATE ON `locker_applications` FOR EACH ROW BEGIN
    DECLARE action VARCHAR(255) DEFAULT 'End';
    DECLARE v_slot_number INT;
    DECLARE v_location VARCHAR(255);
    DECLARE description TEXT;

    IF NEW.status = 'Ended' AND OLD.status <> 'Ended' THEN

        SELECT ls.slot_number, ll.location 
        INTO v_slot_number, v_location 
        FROM locker_slots ls
        INNER JOIN locker_locations ll ON ls.location_id = ll.id
        WHERE ls.id = NEW.slot_id
        LIMIT 1;

        SET description = CONCAT(
	    'User ',
            NEW.user_id,
            ' application on slot #',
            v_slot_number,
            ' at ',
            v_location,
            ' ended.'
        );

        INSERT INTO locker_logs (action, description)
        VALUES (action, description);

    END IF;

END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_locker_rejection` AFTER UPDATE ON `locker_applications` FOR EACH ROW BEGIN
    DECLARE action VARCHAR(255) DEFAULT 'Reject';
    DECLARE v_slot_number INT;
    DECLARE v_location VARCHAR(255);
    DECLARE description TEXT;

    IF NEW.status = 'Rejected' AND OLD.status <> 'Rejected' THEN

        SELECT ls.slot_number, ll.location 
        INTO v_slot_number, v_location 
        FROM locker_slots ls
        INNER JOIN locker_locations ll ON ls.location_id = ll.id
        WHERE ls.id = NEW.slot_id
        LIMIT 1;

        SET description = CONCAT(
	    'User ',
            NEW.user_id,
            ' application on slot #',
            v_slot_number,
            ' at ',
            v_location,
            ' rejected.'
        );

        INSERT INTO locker_logs (action, description)
        VALUES (action, description);

    END IF;

END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_locker_revoking` AFTER UPDATE ON `locker_applications` FOR EACH ROW BEGIN
    DECLARE action VARCHAR(255) DEFAULT 'Revoke';
    DECLARE v_slot_number INT;
    DECLARE v_location VARCHAR(255);
    DECLARE description TEXT;

    IF NEW.status = 'Revoked' AND OLD.status <> 'Revoked' THEN

        SELECT ls.slot_number, ll.location 
        INTO v_slot_number, v_location 
        FROM locker_slots ls
        INNER JOIN locker_locations ll ON ls.location_id = ll.id
        WHERE ls.id = NEW.slot_id
        LIMIT 1;

        SET description = CONCAT(
	    'User ',
            NEW.user_id,
            ' application on slot #',
            v_slot_number,
            ' at ',
            v_location,
            ' revoked.'
        );

        INSERT INTO locker_logs (action, description)
        VALUES (action, description);

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` varchar(255) NOT NULL DEFAULT 'No Changes'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `locker_locations`
--

INSERT INTO `locker_locations` (`id`, `location`, `created_at`, `updated_at`) VALUES
(6, 'AB 1st Floor', '2026-05-22 06:07:29', '2026-05-22 17:40:43'),
(7, 'AB 2nd Floor', '2026-05-22 06:07:32', 'No Changes'),
(8, 'AB 3rd Floor', '2026-05-22 06:07:36', '2026-05-22 18:29:13'),
(9, 'AB 4th Floor', '2026-05-22 06:07:38', '2026-05-23 16:56:27');

--
-- Triggers `locker_locations`
--
DELIMITER $$
CREATE TRIGGER `after_locker_location_deletion` AFTER DELETE ON `locker_locations` FOR EACH ROW BEGIN
    DECLARE action VARCHAR(255) DEFAULT 'Delete';
    DECLARE description VARCHAR(255);

    SET description = CONCAT(
        'Location ',
        OLD.location,
        ' has been deleted.'
    );

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
    DECLARE action VARCHAR(255) DEFAULT 'Add';
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
    DECLARE action VARCHAR(255) DEFAULT 'Update';
    DECLARE description VARCHAR(255);

    SET description = CONCAT(
        'Location ',
        OLD.location,
        ' has been updated to ',
        NEW.location,
        '.'
    );

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
(1, 'Update', 'Slot #1 (AB 4th Floor, Small, May 01, 2026 - May 31, 2026, Occupied) updated to Slot #1 (AB 4th Floor, Small, May 01, 2026 - May 22, 2026, Occupied).', '2026-05-22 11:15:51'),
(2, 'Update', 'Slot #1 (AB 4th Floor, Small, May 01, 2026 - May 22, 2026, Occupied) updated to Slot #1 (AB 4th Floor, Small, May 01, 2026 - May 22, 2026, Available).', '2026-05-22 11:15:55'),
(3, 'End', 'User 2024-11468 application on slot #1 at AB 4th Floor ended.', '2026-05-22 11:15:55'),
(4, 'Update', 'Slot #1 (AB 4th Floor, Small, May 01, 2026 - May 22, 2026, Available) updated to Slot #1 (AB 4th Floor, Small, May 01, 2026 - May 31, 2026, Available).', '2026-05-22 11:31:43'),
(5, 'Update', 'Slot #1 (AB 3rd Floor, Small, May 01, 2026 - May 31, 2026, Occupied) updated to Slot #1 (AB 3rd Floor, Small, May 01, 2026 - May 31, 2026, Available).', '2026-05-22 11:31:47'),
(6, 'Update', 'Slot #1 (AB 2nd Floor, Small, May 01, 2026 - May 31, 2026, Occupied) updated to Slot #1 (AB 2nd Floor, Small, May 01, 2026 - May 31, 2026, Available).', '2026-05-22 11:31:50'),
(7, 'Update', 'Slot #1 (AB 1st Floor, Small, May 01, 2026 - May 31, 2026, Occupied) updated to Slot #1 (AB 1st Floor, Small, May 01, 2026 - May 31, 2026, Available).', '2026-05-22 11:31:53'),
(8, 'Apply', 'User 2024-31214 applied (Slot #1, AB 4th Floor).', '2026-05-22 11:32:08'),
(9, 'Apply', 'User 2024-31214 applied (Slot #1, AB 3rd Floor).', '2026-05-22 11:32:10'),
(10, 'Apply', 'User 2024-31214 applied (Slot #1, AB 2nd Floor).', '2026-05-22 11:32:11'),
(11, 'Apply', 'User 2024-31214 applied (Slot #1, AB 1st Floor).', '2026-05-22 11:32:14'),
(12, 'Apply', 'User 2024-11468 applied (Slot #1, AB 1st Floor).', '2026-05-22 11:32:22'),
(13, 'Apply', 'User 2024-11468 applied (Slot #1, AB 2nd Floor).', '2026-05-22 11:32:24'),
(14, 'Apply', 'User 2024-11468 applied (Slot #1, AB 3rd Floor).', '2026-05-22 11:32:25'),
(15, 'Apply', 'User 2024-11468 applied (Slot #1, AB 4th Floor).', '2026-05-22 11:32:26'),
(16, 'Accept', 'User 2024-11468 application on slot #1 at AB 4th Floor accepted.', '2026-05-22 11:32:58'),
(17, 'Update', 'Slot #1 (AB 4th Floor, Small, May 01, 2026 - May 31, 2026, Available) updated to Slot #1 (AB 4th Floor, Small, May 01, 2026 - May 31, 2026, Occupied).', '2026-05-22 11:32:58'),
(18, 'Accept', 'User 2024-11468 application on slot #1 at AB 3rd Floor accepted.', '2026-05-22 11:33:00'),
(19, 'Update', 'Slot #1 (AB 3rd Floor, Small, May 01, 2026 - May 31, 2026, Available) updated to Slot #1 (AB 3rd Floor, Small, May 01, 2026 - May 31, 2026, Occupied).', '2026-05-22 11:33:00'),
(20, 'Accept', 'User 2024-31214 application on slot #1 at AB 1st Floor accepted.', '2026-05-22 11:33:02'),
(21, 'Update', 'Slot #1 (AB 1st Floor, Small, May 01, 2026 - May 31, 2026, Available) updated to Slot #1 (AB 1st Floor, Small, May 01, 2026 - May 31, 2026, Occupied).', '2026-05-22 11:33:02'),
(22, 'Accept', 'User 2024-31214 application on slot #1 at AB 2nd Floor accepted.', '2026-05-22 11:33:06'),
(23, 'Update', 'Slot #1 (AB 2nd Floor, Small, May 01, 2026 - May 31, 2026, Available) updated to Slot #1 (AB 2nd Floor, Small, May 01, 2026 - May 31, 2026, Occupied).', '2026-05-22 11:33:06'),
(24, 'Reject', 'User 2024-11468 application on slot #1 at AB 2nd Floor rejected.', '2026-05-22 12:03:35'),
(25, 'Reject', 'User 2024-11468 application on slot #1 at AB 1st Floor rejected.', '2026-05-22 12:03:37'),
(26, 'Reject', 'User 2024-31214 application on slot #1 at AB 3rd Floor rejected.', '2026-05-22 12:03:39'),
(27, 'Reject', 'User 2024-31214 application on slot #1 at AB 4th Floor rejected.', '2026-05-22 12:03:40'),
(28, 'Update', 'Slot #1 (AB 4th Floor, Small, May 01, 2026 - May 31, 2026, Occupied) updated to Slot #1 (AB 4th Floor, Small, May 01, 2026 - May 22, 2026, Occupied).', '2026-05-22 12:05:03'),
(29, 'Update', 'Slot #1 (AB 3rd Floor, Small, May 01, 2026 - May 31, 2026, Occupied) updated to Slot #1 (AB 3rd Floor, Small, May 01, 2026 - May 22, 2026, Occupied).', '2026-05-22 12:05:09'),
(30, 'Update', 'Slot #1 (AB 2nd Floor, Small, May 01, 2026 - May 31, 2026, Occupied) updated to Slot #1 (AB 2nd Floor, Small, May 01, 2026 - May 22, 2026, Occupied).', '2026-05-22 12:05:11'),
(31, 'Update', 'Slot #1 (AB 1st Floor, Small, May 01, 2026 - May 31, 2026, Occupied) updated to Slot #1 (AB 1st Floor, Small, May 01, 2026 - May 22, 2026, Occupied).', '2026-05-22 12:05:18'),
(32, 'Update', 'Slot #1 (AB 1st Floor, Small, May 01, 2026 - May 22, 2026, Occupied) updated to Slot #1 (AB 1st Floor, Small, May 01, 2026 - May 22, 2026, Available).', '2026-05-22 12:05:22'),
(33, 'Update', 'Slot #1 (AB 2nd Floor, Small, May 01, 2026 - May 22, 2026, Occupied) updated to Slot #1 (AB 2nd Floor, Small, May 01, 2026 - May 22, 2026, Available).', '2026-05-22 12:05:22'),
(34, 'Update', 'Slot #1 (AB 3rd Floor, Small, May 01, 2026 - May 22, 2026, Occupied) updated to Slot #1 (AB 3rd Floor, Small, May 01, 2026 - May 22, 2026, Available).', '2026-05-22 12:05:22'),
(35, 'Update', 'Slot #1 (AB 4th Floor, Small, May 01, 2026 - May 22, 2026, Occupied) updated to Slot #1 (AB 4th Floor, Small, May 01, 2026 - May 22, 2026, Available).', '2026-05-22 12:05:22'),
(36, 'End', 'User 2024-31214 application on slot #1 at AB 1st Floor ended.', '2026-05-22 12:05:22'),
(37, 'End', 'User 2024-31214 application on slot #1 at AB 2nd Floor ended.', '2026-05-22 12:05:22'),
(38, 'End', 'User 2024-11468 application on slot #1 at AB 3rd Floor ended.', '2026-05-22 12:05:22'),
(39, 'End', 'User 2024-11468 application on slot #1 at AB 4th Floor ended.', '2026-05-22 12:05:22'),
(40, 'Delete', 'Slot #1 (AB 1st Floor, Small, May 01, 2026 - May 22, 2026, Available) deleted.', '2026-05-22 13:21:14'),
(41, 'Delete', 'Slot #1 (AB 2nd Floor, Small, May 01, 2026 - May 22, 2026, Available) deleted.', '2026-05-22 13:21:14'),
(42, 'Delete', 'Slot #1 (AB 3rd Floor, Small, May 01, 2026 - May 22, 2026, Available) deleted.', '2026-05-22 13:21:14'),
(43, 'Delete', 'Slot #1 (AB 4th Floor, Small, May 01, 2026 - May 22, 2026, Available) deleted.', '2026-05-22 13:21:14'),
(44, 'Add', 'Slot #1 added (AB 1st Floor, Small, August 03, 2026 - January 08, 2027, Available).', '2026-05-22 13:34:27'),
(45, 'Delete', 'Slot #1 (AB 1st Floor, Small, August 03, 2026 - January 08, 2027, Available) deleted.', '2026-05-22 13:40:10'),
(46, 'Add', 'Slot #1 added (AB 1st Floor, Small, August 03, 2026 - January 08, 2027, Available).', '2026-05-22 14:06:38'),
(47, 'Apply', 'User 2024-31214 applied (Slot #1, AB 1st Floor).', '2026-05-22 14:33:05'),
(48, 'Cancel', 'User 2024-31214 cancelled (Slot #1, AB 1st Floor).', '2026-05-22 14:33:22'),
(49, 'Update', 'Slot #1 (AB 1st Floor, Small, May 01, 2026 - January 08, 2027, Available) updated to Slot #1 (AB 1st Floor, Large, January 18, 2027 - June 18, 2027, Available).', '2026-05-22 14:40:42'),
(50, 'Update', 'Slot #1 (AB 1st Floor, Large, January 18, 2027 - June 18, 2027, Available) updated to Slot #1 (AB 1st Floor, Small, May 01, 2026 - January 08, 2027, Available).', '2026-05-22 14:40:49'),
(51, 'Update', 'Slot #1 (AB 1st Floor, Small, May 01, 2026 - January 08, 2027, Available) updated to Slot #1 (AB 1st Floor, Small, January 18, 2027 - June 18, 2027, Available).', '2026-05-23 04:06:38'),
(52, 'Update', 'Slot #1 (AB 1st Floor, Small, January 18, 2027 - June 18, 2027, Available) updated to Slot #1 (AB 1st Floor, Small, May 01, 2026 - January 08, 2027, Available).', '2026-05-23 04:06:53'),
(53, 'Update', 'Slot #1 (AB 1st Floor, Small, May 01, 2026 - January 08, 2027, Available) updated to Slot #-6 (AB 1st Floor, Small, May 01, 2026 - January 08, 2027, Available).', '2026-05-23 04:10:12'),
(54, 'Update', 'Slot #-6 (AB 1st Floor, Small, May 01, 2026 - January 08, 2027, Available) updated to Slot #1 (AB 1st Floor, Small, May 01, 2026 - January 08, 2027, Available).', '2026-05-23 04:10:21'),
(55, 'Add', 'Slot #0 added (AB 1st Floor, Small, May 01, 2026 - January 08, 2027, Available).', '2026-05-23 04:11:13'),
(56, 'Delete', 'Slot #0 (AB 1st Floor, Small, Available) deleted.', '2026-05-23 04:33:04'),
(57, 'Add', 'Slot #2 (AB 1st Floor, Small, May 01, 2026 - January 08, 2027, Available) has been added.', '2026-05-23 04:49:41'),
(58, 'Delete', 'Slot #2 (AB 1st Floor, Small, May 01, 2026 - January 08, 2027, Available) has been deleted.', '2026-05-23 04:49:47'),
(59, 'Add', 'Slot #2 (AB 1st Floor, Small, May 01, 2026 - January 08, 2027, Available) has been added.', '2026-05-23 04:55:06'),
(60, 'Add', 'Slot #1 (AB 2nd Floor, Medium , May 01, 2026 - January 08, 2027, Available) has been added.', '2026-05-23 05:02:23'),
(61, 'Add', 'Slot #2 (AB 2nd Floor, Medium , May 01, 2026 - January 08, 2027, Available) has been added.', '2026-05-23 05:02:28'),
(62, 'Update', 'Slot #1 (AB 2nd Floor, Medium , May 01, 2026 - January 08, 2027, Available) updated to Slot #1 (AB 2nd Floor, Medium , January 18, 2027 - June 18, 2027, Available).', '2026-05-23 05:02:45'),
(63, 'Update', 'Slot #2 (AB 2nd Floor, Medium , May 01, 2026 - January 08, 2027, Available) updated to Slot #2 (AB 2nd Floor, Medium , January 18, 2027 - June 18, 2027, Available).', '2026-05-23 05:02:50'),
(64, 'Delete', 'Slot #1 (AB 2nd Floor, Medium , January 18, 2027 - June 18, 2027, Available) has been deleted.', '2026-05-23 05:03:19'),
(65, 'Delete', 'Slot #2 (AB 2nd Floor, Medium , January 18, 2027 - June 18, 2027, Available) has been deleted.', '2026-05-23 05:03:22'),
(66, 'Add', 'Slot #1 (AB 2nd Floor, Small, May 01, 2026 - January 08, 2027, Available) has been added.', '2026-05-23 05:05:03'),
(67, 'Add', 'Slot #2 (AB 2nd Floor, Small, May 01, 2026 - January 08, 2027, Available) has been added.', '2026-05-23 05:05:10'),
(68, 'Update', 'Slot #1 (AB 2nd Floor, Small, May 01, 2026 - January 08, 2027, Available) updated to Slot #1 (AB 2nd Floor, Medium , May 01, 2026 - January 08, 2027, Available).', '2026-05-23 05:05:17'),
(69, 'Update', 'Slot #2 (AB 2nd Floor, Small, May 01, 2026 - January 08, 2027, Available) updated to Slot #2 (AB 2nd Floor, Medium , May 01, 2026 - January 08, 2027, Available).', '2026-05-23 05:05:20'),
(70, 'Add', 'Slot #1 (AB 3rd Floor, Medium , May 01, 2026 - January 08, 2027, Available) has been added.', '2026-05-23 05:05:40'),
(71, 'Add', 'Slot #2 (AB 3rd Floor, Medium , May 01, 2026 - January 08, 2027, Available) has been added.', '2026-05-23 05:05:48'),
(72, 'Add', 'Slot #1 (AB 4th Floor, Large, May 01, 2026 - January 08, 2027, Available) has been added.', '2026-05-23 05:07:04'),
(73, 'Add', 'Slot #2 (AB 4th Floor, Large, May 01, 2026 - January 08, 2027, Available) has been added.', '2026-05-23 05:07:11'),
(74, 'Apply', 'User 2024-31214 applied (Slot #2, AB 1st Floor).', '2026-05-23 05:10:49'),
(75, 'Apply', 'User 2024-31214 applied (Slot #1, AB 1st Floor).', '2026-05-23 05:10:52'),
(76, 'Apply', 'User 2024-31214 applied (Slot #2, AB 2nd Floor).', '2026-05-23 05:11:13'),
(77, 'Apply', 'User 2024-31214 applied (Slot #1, AB 2nd Floor).', '2026-05-23 05:11:16'),
(78, 'Apply', 'User 2024-11468 applied (Slot #2, AB 3rd Floor).', '2026-05-23 05:11:49'),
(79, 'Apply', 'User 2024-11468 applied (Slot #1, AB 3rd Floor).', '2026-05-23 05:11:51'),
(80, 'Apply', 'User 2024-11468 applied (Slot #2, AB 4th Floor).', '2026-05-23 05:11:54'),
(81, 'Apply', 'User 2024-11468 applied (Slot #1, AB 4th Floor).', '2026-05-23 05:11:55'),
(82, 'Apply', 'User 2024-11468 applied (Slot #2, AB 1st Floor).', '2026-05-23 05:12:04'),
(83, 'Apply', 'User 2024-11468 applied (Slot #1, AB 1st Floor).', '2026-05-23 05:12:06'),
(84, 'Apply', 'User 2024-11468 applied (Slot #2, AB 2nd Floor).', '2026-05-23 05:12:13'),
(85, 'Apply', 'User 2024-11468 applied (Slot #1, AB 2nd Floor).', '2026-05-23 05:12:15'),
(86, 'Apply', 'User 2024-31214 applied (Slot #2, AB 3rd Floor).', '2026-05-23 05:12:57'),
(87, 'Apply', 'User 2024-31214 applied (Slot #1, AB 3rd Floor).', '2026-05-23 05:12:59'),
(88, 'Apply', 'User 2024-31214 applied (Slot #2, AB 4th Floor).', '2026-05-23 05:13:01'),
(89, 'Apply', 'User 2024-31214 applied (Slot #1, AB 4th Floor).', '2026-05-23 05:13:03'),
(90, 'Accept', 'User 2024-31214 application on slot #1 at AB 4th Floor accepted.', '2026-05-23 05:58:12'),
(91, 'Update', 'Slot #1 (AB 4th Floor, Large, May 01, 2026 - January 08, 2027, Available) updated to Slot #1 (AB 4th Floor, Large, May 01, 2026 - January 08, 2027, Occupied).', '2026-05-23 05:58:12'),
(92, 'Reject', 'User 2024-31214 application on slot #2 at AB 4th Floor rejected.', '2026-05-23 05:58:15'),
(93, 'Reject', 'User 2024-31214 application on slot #1 at AB 3rd Floor rejected.', '2026-05-23 05:58:18'),
(94, 'Accept', 'User 2024-31214 application on slot #2 at AB 3rd Floor accepted.', '2026-05-23 05:58:20'),
(95, 'Update', 'Slot #2 (AB 3rd Floor, Medium , May 01, 2026 - January 08, 2027, Available) updated to Slot #2 (AB 3rd Floor, Medium , May 01, 2026 - January 08, 2027, Occupied).', '2026-05-23 05:58:20'),
(96, 'Accept', 'User 2024-11468 application on slot #2 at AB 1st Floor accepted.', '2026-05-23 05:58:37'),
(97, 'Update', 'Slot #2 (AB 1st Floor, Small, May 01, 2026 - January 08, 2027, Available) updated to Slot #2 (AB 1st Floor, Small, May 01, 2026 - January 08, 2027, Occupied).', '2026-05-23 05:58:37'),
(98, 'Reject', 'User 2024-11468 application on slot #1 at AB 4th Floor rejected.', '2026-05-23 05:58:40'),
(99, 'Add', 'Slot #3 (AB 4th Floor, Large, May 01, 2026 - January 08, 2027, Available) has been added.', '2026-05-23 07:26:57'),
(100, 'Delete', 'Slot #3 (AB 4th Floor, Large, May 01, 2026 - January 08, 2027, Available) has been deleted.', '2026-05-23 07:28:56'),
(101, 'Add', 'Slot #3 (AB 4th Floor, Medium , May 01, 2026 - January 08, 2027, Available) has been added.', '2026-05-23 07:29:04'),
(102, 'Delete', 'Slot #3 (AB 4th Floor, Medium , May 01, 2026 - January 08, 2027, Available) has been deleted.', '2026-05-23 07:32:48'),
(103, 'Update', 'Location AB 4th Floor has been updated to AB 4th Flooring.', '2026-05-23 08:04:28'),
(104, 'Update', 'Location AB 4th Flooring has been updated to AB 4th Floor.', '2026-05-23 08:04:34'),
(105, 'Add', 'Location IC 1st Floor has been added.', '2026-05-23 08:04:44'),
(106, 'Delete', 'Location IC 1st Floor has been deleted.', '2026-05-23 08:04:48'),
(107, 'Add', 'Slot #3 (AB 4th Floor, Large, May 01, 2026 - January 08, 2027, Available) has been added.', '2026-05-23 08:46:22'),
(108, 'Delete', 'Slot #3 (AB 4th Floor, Large, May 01, 2026 - January 08, 2027, Available) has been deleted.', '2026-05-23 08:46:28'),
(109, 'Add', 'Slot #3 (AB 4th Floor, Large, January 18, 2027 - June 18, 2027, Available) has been added.', '2026-05-23 08:51:04'),
(110, 'Delete', 'Slot #3 (AB 4th Floor, Large, January 18, 2027 - June 18, 2027, Available) has been deleted.', '2026-05-23 08:51:11'),
(111, 'Add', 'Slot #3 (AB 4th Floor, Large, May 01, 2026 - January 08, 2027, Available) has been added.', '2026-05-23 08:51:17'),
(112, 'Add', 'Slot #3 (AB 3rd Floor, Medium , May 01, 2026 - January 08, 2027, Available) has been added.', '2026-05-23 08:51:24'),
(113, 'Update', 'Location AB 4th Floor has been updated to AB 4th Floorng.', '2026-05-23 08:56:19'),
(114, 'Update', 'Location AB 4th Floorng has been updated to AB 4th Floor.', '2026-05-23 08:56:27'),
(115, 'Update', 'Size Large with price ₱159.00 has been updated to Large with price ₱149.00.', '2026-05-23 08:56:43'),
(116, 'Cancel', 'User 2024-11468 cancelled (Slot #1, AB 2nd Floor).', '2026-05-23 09:41:44'),
(117, 'Add', 'Slot #4 (AB 4th Floor, Large, May 01, 2026 - January 08, 2027, Available) has been added.', '2026-05-23 09:46:40'),
(118, 'Add', 'Slot #5 (AB 4th Floor, Large, May 01, 2026 - January 08, 2027, Available) has been added.', '2026-05-23 09:56:30'),
(119, 'Delete', 'Slot #5 (AB 4th Floor, Large, May 01, 2026 - January 08, 2027, Available) has been deleted.', '2026-05-23 10:34:12'),
(120, 'Update', 'Slot #4 (AB 4th Floor, Large, May 01, 2026 - January 08, 2027, Available) updated to Slot #4 (AB 4th Floor, Large, January 18, 2027 - June 18, 2027, Available).', '2026-05-23 10:39:03'),
(121, 'Update', 'Slot #4 (AB 4th Floor, Large, January 18, 2027 - June 18, 2027, Available) updated to Slot #4 (AB 4th Floor, Large, May 01, 2026 - January 08, 2027, Available).', '2026-05-23 15:42:05'),
(122, 'Add', 'Academic year 2027-2028 (1st Semester) has been added from May 01, 2027 to June 02, 2028.', '2026-05-23 15:57:14'),
(123, 'Delete', 'Academic year 2027-2028 (1st Semester) from May 01, 2027 to June 02, 2028 has been deleted.', '2026-05-23 15:58:47'),
(124, 'Update', 'Academic year 2026-2027 (1st Semester) has been updated to 2026-2027 (1st Semester) from January 06, 2026 to January 08, 2027.', '2026-05-23 15:59:05'),
(125, 'Update', 'Academic year 2026-2027 (1st Semester) has been updated to 2026-2027 (1st Semester) from May 01, 2026 to January 08, 2027.', '2026-05-23 16:00:01'),
(126, 'Update', 'Academic year 2026-2027 (1st Semester) has been updated to 2026-2027 (1st Semester) from August 03, 2026 to January 08, 2027.', '2026-05-23 16:00:33'),
(127, 'Update', 'Academic year 2026-2027 (1st Semester) has been updated to 2026-2027 (1st Semester) from May 03, 2026 to January 08, 2027.', '2026-05-23 16:01:40'),
(128, 'Apply', 'User 2024-31214 applied (Slot #2, AB 4th Floor).', '2026-05-23 16:02:56');

-- --------------------------------------------------------

--
-- Table structure for table `locker_sizes`
--

CREATE TABLE `locker_sizes` (
  `id` int(11) NOT NULL,
  `size` varchar(255) NOT NULL,
  `price` double(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `locker_sizes`
--

INSERT INTO `locker_sizes` (`id`, `size`, `price`, `created_at`, `updated_at`) VALUES
(5, 'Small', 79.00, '2026-05-22 06:07:46', NULL),
(6, 'Medium ', 99.00, '2026-05-22 06:07:51', NULL),
(10, 'Large', 149.00, '2026-05-22 09:50:02', '2026-05-23 08:56:43');

--
-- Triggers `locker_sizes`
--
DELIMITER $$
CREATE TRIGGER `after_locker_sizes_deletion` AFTER DELETE ON `locker_sizes` FOR EACH ROW BEGIN
    DECLARE action VARCHAR(255) DEFAULT 'Delete';
    DECLARE description VARCHAR(255);

    SET description = CONCAT(
        'Size ',
        OLD.size,
        ' (₱',
        OLD.price,
        ') has been deleted.'
    );

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
    DECLARE action VARCHAR(255) DEFAULT 'Add';
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
    DECLARE action VARCHAR(255) DEFAULT 'Update';
    DECLARE description VARCHAR(255);

    SET description = CONCAT(
        'Size ',
        OLD.size,
        ' with price ₱',
        OLD.price,
        ' has been updated to ',
        NEW.size,
        ' with price ₱',
        NEW.price,
        '.'
    );

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
  `academic_year_id` int(11) NOT NULL,
  `slot_number` int(11) NOT NULL,
  `location_id` int(11) NOT NULL,
  `size_id` int(11) NOT NULL,
  `status` varchar(255) DEFAULT 'Available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `locker_slots`
--

INSERT INTO `locker_slots` (`id`, `academic_year_id`, `slot_number`, `location_id`, `size_id`, `status`, `created_at`, `updated_at`) VALUES
(26, 1, 1, 6, 5, 'Available', '2026-05-22 14:06:38', '2026-05-23 04:10:21'),
(32, 1, 2, 6, 5, 'Occupied', '2026-05-23 04:55:06', NULL),
(35, 1, 1, 7, 6, 'Available', '2026-05-23 05:05:03', '2026-05-23 05:05:17'),
(36, 1, 2, 7, 6, 'Available', '2026-05-23 05:05:10', '2026-05-23 05:05:20'),
(37, 1, 1, 8, 6, 'Available', '2026-05-23 05:05:40', NULL),
(38, 1, 2, 8, 6, 'Occupied', '2026-05-23 05:05:48', NULL),
(39, 1, 1, 9, 10, 'Occupied', '2026-05-23 05:07:04', NULL),
(40, 1, 2, 9, 10, 'Available', '2026-05-23 05:07:11', NULL),
(45, 1, 3, 9, 10, 'Available', '2026-05-23 08:51:17', NULL),
(46, 1, 3, 8, 6, 'Available', '2026-05-23 08:51:24', NULL),
(47, 1, 4, 9, 10, 'Available', '2026-05-23 09:46:40', '2026-05-23 15:42:05');

--
-- Triggers `locker_slots`
--
DELIMITER $$
CREATE TRIGGER `after_locker_slot_deletion` AFTER DELETE ON `locker_slots` FOR EACH ROW BEGIN
    DECLARE action VARCHAR(255) DEFAULT 'Delete';
    
    DECLARE v_location VARCHAR(255);
    DECLARE v_size VARCHAR(255);
    DECLARE v_start_at DATE;
    DECLARE v_end_at DATE;
    
    DECLARE description VARCHAR(255);

    SELECT location INTO v_location FROM locker_locations
    WHERE id = OLD.location_id LIMIT 1;

    SELECT size INTO v_size FROM locker_sizes
    WHERE id = OLD.size_id LIMIT 1;
    
    SELECT start_at, end_at INTO v_start_at, v_end_at FROM academic_calendar
    WHERE id = OLD.academic_year_id LIMIT 1;


    SET description = CONCAT(
        'Slot #',
        OLD.slot_number,
        ' (',
        v_location,
        ', ',
        v_size,
        ', ',
        DATE_FORMAT(v_start_at, '%M %d, %Y'),
        ' - ',
        DATE_FORMAT(v_end_at, '%M %d, %Y'),
        ', ',
        OLD.status,
        ') has been deleted.'
    );

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
    DECLARE action VARCHAR(255) DEFAULT 'Add';
    
    DECLARE v_location VARCHAR(255);
    DECLARE v_size VARCHAR(255);
    DECLARE v_start_at DATE;
    DECLARE v_end_at DATE;
    
    DECLARE description VARCHAR(255);

    SELECT location INTO v_location FROM locker_locations
    WHERE id = NEW.location_id LIMIT 1;

    SELECT size INTO v_size FROM locker_sizes
    WHERE id = NEW.size_id LIMIT 1;

    SELECT start_at, end_at INTO v_start_at, v_end_at FROM academic_calendar
    WHERE id = NEW.academic_year_id LIMIT 1;

    SET description = CONCAT(
        'Slot #', 
        NEW.slot_number,
        ' (',
        v_location, ', ',
        v_size, ', ',
        DATE_FORMAT(v_start_at, '%M %d, %Y'),
        ' - ',
        DATE_FORMAT(v_end_at, '%M %d, %Y'),
        ', ',
        NEW.status,
        ') has been added.'
    );

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

    DECLARE action VARCHAR(255) DEFAULT 'Update';

    DECLARE v_old_location VARCHAR(255);
    DECLARE v_new_location VARCHAR(255);
    DECLARE v_old_size VARCHAR(255);
    DECLARE v_new_size VARCHAR(255);
    DECLARE v_old_start_at DATE;
    DECLARE v_old_end_at DATE;
    DECLARE v_new_start_at DATE;
    DECLARE v_new_end_at DATE;

    DECLARE description TEXT;

    SELECT location INTO v_old_location FROM locker_locations
    WHERE id = OLD.location_id LIMIT 1;

    SELECT location INTO v_new_location FROM locker_locations
    WHERE id = NEW.location_id LIMIT 1;

    SELECT size INTO v_old_size FROM locker_sizes
    WHERE id = OLD.size_id LIMIT 1;

    SELECT size INTO v_new_size FROM locker_sizes
    WHERE id = NEW.size_id LIMIT 1;

    SELECT start_at, end_at INTO v_old_start_at, v_old_end_at FROM academic_calendar
    WHERE id = OLD.academic_year_id LIMIT 1;

    SELECT start_at, end_at INTO v_new_start_at, v_new_end_at FROM academic_calendar
    WHERE id = NEW.academic_year_id LIMIT 1;

    SET description = CONCAT(
        'Slot #', 
        OLD.slot_number,
        ' (', 
        v_old_location, 
        ', ', 
        v_old_size, 
        ', ',
        DATE_FORMAT(v_old_start_at, '%M %d, %Y'), 
        ' - ',
        DATE_FORMAT(v_old_end_at, '%M %d, %Y'), 
        ', ', 
        OLD.status,
        ') updated to Slot #', 
        NEW.slot_number,
        ' (', v_new_location, ', ', v_new_size, 
        ', ',
        DATE_FORMAT(v_new_start_at, '%M %d, %Y'), 
        ' - ',
        DATE_FORMAT(v_new_end_at, '%M %d, %Y'), 
        ', ', 
        NEW.status,
        ').'
    );

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
,`slot_number` int(11)
,`location` varchar(255)
,`size` varchar(255)
,`price` double(10,2)
,`status` varchar(255)
,`payment` varchar(255)
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
('2024-11468', 'Getalla', 'Joviet', 'Batang', 'Male', '2003-02-19', 'Institute of Computing', 'Bachelor of Science in Information System', 'jovietgetalla', 'getalla.joviet@dnscedu.onmicrosoft.com', '$2y$10$AAjH4kHWBsCZHcHm0wApAOFpQDheHyAz5FwBRyZoi5GX6S5ifpsj6', '2026-05-22 06:46:04'),
('2024-31214', 'Malbino', 'Feby Johnrel', 'Roferos', 'Male', '2006-02-11', 'Institute of Computing', 'Bachelor of Science in Information Technology', 'febyjohnrelmalbino', 'malbino.febyjohnrel@dnscedu.onmicrosoft.com', '$2y$10$4XRhk57zuTYEExfTo7KaguxsrrlwN4Min5eslKn2AiE8lKtk/zBrG', '2026-05-22 06:45:50');

--
-- Triggers `users`
--
DELIMITER $$
CREATE TRIGGER `after_user_activation` AFTER INSERT ON `users` FOR EACH ROW BEGIN
	DECLARE action VARCHAR(255) DEFAULT 'Activate';
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
	DECLARE action VARCHAR(255) DEFAULT 'Delete';
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
(2, '', '', 'Activated', 'jasonbulay-og (#2024-98026) account has been activated.', '2026-05-19 06:50:06'),
(3, '', '', 'Deleted', 'jovietgetalla (#2024-11468) account has been deleted.', '2026-05-22 06:45:42'),
(4, '', '', 'Deleted', 'febyjohnrelmalbino (#2024-31214) account has been deleted.', '2026-05-22 06:45:42'),
(5, '', '', 'Deleted', 'jasonbulay-og (#2024-98026) account has been deleted.', '2026-05-22 06:45:42'),
(6, '', '', 'Activated', 'febyjohnrelmalbino (#2024-31214) account has been activated.', '2026-05-22 06:45:50'),
(7, '', '', 'Activated', 'jovietgetalla (#2024-11468) account has been activated.', '2026-05-22 06:46:04');

-- --------------------------------------------------------

--
-- Structure for view `recent_locker_application`
--
DROP TABLE IF EXISTS `recent_locker_application`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `recent_locker_application`  AS SELECT `la`.`id` AS `id`, `la`.`user_id` AS `user_id`, `ls`.`slot_number` AS `slot_number`, `ll`.`location` AS `location`, `lsz`.`size` AS `size`, `lsz`.`price` AS `price`, `la`.`status` AS `status`, `la`.`payment` AS `payment`, `la`.`created_at` AS `created_at` FROM (((`locker_applications` `la` join `locker_slots` `ls` on(`la`.`slot_id` = `ls`.`id`)) join `locker_locations` `ll` on(`ls`.`location_id` = `ll`.`id`)) join `locker_sizes` `lsz` on(`ls`.`size_id` = `lsz`.`id`)) WHERE `la`.`status` = 'Pending' ORDER BY `la`.`created_at` DESC LIMIT 0, 1 ;

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
-- Indexes for table `academic_calendar`
--
ALTER TABLE `academic_calendar`
  ADD PRIMARY KEY (`id`);

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
  ADD KEY `slot_size` (`size_id`),
  ADD KEY `slot_academic_year` (`academic_year_id`);

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
-- AUTO_INCREMENT for table `academic_calendar`
--
ALTER TABLE `academic_calendar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `locker_applications`
--
ALTER TABLE `locker_applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT for table `locker_locations`
--
ALTER TABLE `locker_locations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `locker_logs`
--
ALTER TABLE `locker_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=129;

--
-- AUTO_INCREMENT for table `locker_sizes`
--
ALTER TABLE `locker_sizes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `locker_slots`
--
ALTER TABLE `locker_slots`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `user_account_logs`
--
ALTER TABLE `user_account_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

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
  ADD CONSTRAINT `slot_academic_year` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_calendar` (`id`) ON UPDATE CASCADE,
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
