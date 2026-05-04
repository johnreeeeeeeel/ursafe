-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 04, 2026 at 06:35 PM
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
-- Table structure for table `institutes`
--

CREATE TABLE `institutes` (
  `id` int(11) NOT NULL,
  `code` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `institutes`
--

INSERT INTO `institutes` (`id`, `code`, `description`) VALUES
(1, 'IC', 'Institute of Computing'),
(2, 'IAAS', 'Institute of Aquatic and Applied Sciences'),
(3, 'ILEGG', 'Institute of Leadership, Entrepreneurship, and Good Governance'),
(4, 'ITED', 'Institute of Teacher Education');

-- --------------------------------------------------------

--
-- Table structure for table `programs`
--

CREATE TABLE `programs` (
  `id` int(11) NOT NULL,
  `code` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `institute_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `programs`
--

INSERT INTO `programs` (`id`, `code`, `description`, `institute_id`) VALUES
(1, 'BSIS', 'Bachelor of Science in Information Systems', 1),
(2, 'BSIT', 'Bachelor of Science in Information Technology', 1),
(3, 'MIT', 'Master in Information Technology', 1),
(4, 'BSAF', 'Bachelor of Science in Agro-Forestry', 2),
(5, 'BSFAS', 'Bachelor of Science in Fisheries and Aquatic Sciences', 2),
(6, 'BSFT', 'Bachelor of Science in Food Technology', 2),
(7, 'BSMB', 'Bachelor of Science in Marine Biology', 2),
(8, 'MFM-AQUA', 'Master in Fisheries Management - Aquaculture Technology', 2),
(9, 'MFM-FP', 'Master in Fisheries Management - Fish Processing', 2),
(10, 'MSMB', 'Master of Science in Marine Biodiversity', 2),
(11, 'BPA', 'Bachelor of Public Administration', 3),
(12, 'BSDRM', 'Bachelor of Science in Disaster and Resilience Management', 3),
(13, 'BSENTREP', 'Bachelor of Science in Entrepreneurship', 3),
(14, 'BSSW', 'Bachelor of Science in Social Work', 3),
(15, 'BSTM', 'Bachelor of Science in Tourism Management', 3),
(16, 'MAEM', 'Master of Arts in Educational Management', 3),
(17, 'BACOMM', 'Bachelor of Arts in Communication', 4),
(18, 'BSED-ENG', 'Bachelor of Secondary Education - English', 4),
(19, 'BSED-MATH', 'Bachelor of Secondary Education - Mathematics', 4),
(20, 'BSED-SCI', 'Bachelor of Secondary Education - Science', 4),
(21, 'BTLED', 'Bachelor of Technology and Livelihood Education', 4),
(22, 'BPE', 'Bachelor of Physical Education', 4),
(23, 'PhD-EM', 'Doctor of Philosophy in Educational Management', 4),
(24, 'MAED-ELST', 'Master of Arts in Education - English Language Studies and Teaching', 4),
(25, 'MST-MATH', 'Master of Science Teaching - Mathematics', 4),
(26, 'MST-GENSCI', 'Master of Science Teaching - General Science', 4);

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `student_id` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `middlename` varchar(255) NOT NULL,
  `sex` varchar(6) NOT NULL,
  `dob` date NOT NULL,
  `institute` int(11) NOT NULL,
  `program` int(11) NOT NULL,
  `email` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`student_id`, `lastname`, `firstname`, `middlename`, `sex`, `dob`, `institute`, `program`, `email`) VALUES
('2024-01100', 'Malbino', 'Feby Johnrel', 'Roferos', 'Male', '2006-12-11', 1, 1, 'malbino.febyjohnrel@dnscedu.onmicrosoft.com'),
('2024-23119', 'Getalla', 'Joviet', '', 'Male', '2003-02-19', 1, 3, 'getalla.joviet@dnscedu.onmicrosoft.com'),
('2024-45423', 'Bulay-og', 'Jason', '', 'Male', '2002-12-20', 1, 2, 'bulay-og.jason@dnscedu.onmicrosoft.com');

--
-- Triggers `students`
--
DELIMITER $$
CREATE TRIGGER `sync_students_update` AFTER UPDATE ON `students` FOR EACH ROW BEGIN
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
    WHERE id = NEW.student_id;
END
$$
DELIMITER ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `institutes`
--
ALTER TABLE `institutes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniqueCode` (`code`);

--
-- Indexes for table `programs`
--
ALTER TABLE `programs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniqueCode` (`code`),
  ADD KEY `programInstitute` (`institute_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD UNIQUE KEY `uniqueStudentID` (`student_id`),
  ADD UNIQUE KEY `uniqueEmail` (`email`),
  ADD KEY `userInstitute` (`institute`),
  ADD KEY `userProgram` (`program`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `institutes`
--
ALTER TABLE `institutes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `programs`
--
ALTER TABLE `programs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `programs`
--
ALTER TABLE `programs`
  ADD CONSTRAINT `programInstitute` FOREIGN KEY (`institute_id`) REFERENCES `institutes` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `userInstitute` FOREIGN KEY (`institute`) REFERENCES `institutes` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `userProgram` FOREIGN KEY (`program`) REFERENCES `programs` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
