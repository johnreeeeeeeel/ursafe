-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 24, 2026 at 10:22 AM
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
-- Database: `campus_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `middlename` varchar(255) DEFAULT NULL,
  `sex` varchar(6) NOT NULL,
  `dob` date NOT NULL,
  `institute` varchar(255) NOT NULL,
  `program` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `lastname`, `firstname`, `middlename`, `sex`, `dob`, `institute`, `program`, `email`) VALUES
('2024-11231', 'Bulay-og', 'Jason', 'Dy', 'Male', '2004-11-21', 'Institute of Computing', 'Bachelor of Science in Information System', 'bulay-og.jason@dnscedu.onmicrosoft.com'),
('2024-11468', 'Getalla', 'Joviet', 'Batang', 'Male', '2003-02-19', 'Institute of Computing', 'Bachelor of Science in Information System', 'getalla.joviet@dnscedu.onmicrosoft.com'),
('2024-98798', 'Malbino', 'Feby Johnrel', 'Roferos', 'Male', '2006-02-11', 'Institute of Computing', 'Bachelor of Science in Information Technology', 'malbino.febyjohnrel@dnscedu.onmicrosoft.com');

--
-- Triggers `students`
--
DELIMITER $$
CREATE TRIGGER `after_student_delete` AFTER DELETE ON `students` FOR EACH ROW BEGIN
    IF EXISTS (
        SELECT 1 FROM ursafe_db.users WHERE id = OLD.id
    ) THEN

        DELETE FROM ursafe_db.users
        WHERE id = OLD.id;

    END IF;

    INSERT INTO ursafe_db.user_account_logs (
        action,
        description
    )
    VALUES (
        'Delete',
        CONCAT(
            'Deleted user: ',
            OLD.lastname, ', ', OLD.firstname, ' ', IFNULL(OLD.middlename, ''),

            ' | ', OLD.id,
            ' | ', OLD.sex,
            ' | ', OLD.dob,
            ' | ', OLD.institute,
            ' | ', OLD.program,
            ' | ', OLD.email
        )
    );

END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_student_insert` AFTER INSERT ON `students` FOR EACH ROW BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM ursafe_db.users WHERE id = NEW.id
    ) THEN

        INSERT INTO ursafe_db.users (
            id, 
            lastname, 
            firstname, 
            middlename,
            sex, 
            dob, 
            institute, 
            program, 
            email
        )
        VALUES (
            NEW.id, 
            NEW.lastname, 
            NEW.firstname, 
            NEW.middlename,
            NEW.sex, 
            NEW.dob, 
            NEW.institute, 
            NEW.program, 
            NEW.email
        );

    END IF;

    INSERT INTO ursafe_db.user_account_logs (
        action,
        description
    )
    VALUES (
        'Add',
        CONCAT(
            'Added user: ',
            NEW.lastname, ', ', NEW.firstname, ' ', IFNULL(NEW.middlename, ''),

            ' | ', NEW.id,
            ' | ', NEW.sex,
            ' | ', NEW.dob,
            ' | ', NEW.institute,
            ' | ', NEW.program,
            ' | ', NEW.email
        )
    );

END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_student_update` AFTER UPDATE ON `students` FOR EACH ROW BEGIN
    IF EXISTS (
        SELECT 1 FROM ursafe_db.users WHERE id = NEW.id
    ) THEN

        UPDATE ursafe_db.users
        SET
            lastname = NEW.lastname,
            firstname = NEW.firstname,
            middlename = NEW.middlename,
            sex = NEW.sex,
            dob = NEW.dob,
            institute = NEW.institute,
            program = NEW.program,
            email = NEW.email
        WHERE id = NEW.id;

    END IF;

    INSERT INTO ursafe_db.user_account_logs (
        action,
        description
    )
    VALUES (
        'Update',
        CONCAT(
            'Updated user: ',
            NEW.lastname, ', ', NEW.firstname, ' ', IFNULL(NEW.middlename, ''),

            ' | ', NEW.id,
            ' | ', NEW.sex,
            ' | ', NEW.dob,
            ' | ', NEW.institute,
            ' | ', NEW.program,
            ' | ', NEW.email
        )
    );

END
$$
DELIMITER ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD UNIQUE KEY `unique_student_id` (`id`),
  ADD UNIQUE KEY `unique_email` (`email`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
