-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: May 14, 2025 at 03:58 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pharmacy_portal2`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `AddOrUpdateUser` (IN `p_userId` INT, IN `p_userName` VARCHAR(45), IN `p_contactInfo` VARCHAR(200), IN `p_userType` ENUM('pharmacist','patient'))   BEGIN
    IF p_userId IS NOT NULL THEN
        UPDATE Users
        SET userName = p_userName, contactInfo = p_contactInfo, userType = p_userType
        WHERE userId = p_userId;
    ELSE
        INSERT INTO Users (userName, contactInfo, userType)
        VALUES (p_userName, p_contactInfo, p_userType);
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ProcessSale` (IN `p_prescriptionId` INT, IN `p_quantitySold` INT)   BEGIN
    DECLARE medId INT;
    DECLARE saleAmount DECIMAL(10,2);

    SELECT medicationId INTO medId FROM Prescriptions WHERE prescriptionId = p_prescriptionId;
    UPDATE Inventory
    SET quantityAvailable = quantityAvailable - p_quantitySold, lastUpdated = NOW()
    WHERE medicationId = medId;

    SET saleAmount = (SELECT price FROM Medications WHERE medicationId = medId) * p_quantitySold;

    INSERT INTO Sales (prescriptionId, saleDate, quantitySold, saleAmount)
    VALUES (p_prescriptionId, NOW(), p_quantitySold, saleAmount);
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `Inventory`
--

CREATE TABLE `Inventory` (
  `inventoryId` int(11) NOT NULL,
  `medicationId` int(11) NOT NULL,
  `quantityAvailable` int(11) NOT NULL,
  `lastUpdated` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Inventory`
--

INSERT INTO `Inventory` (`inventoryId`, `medicationId`, `quantityAvailable`, `lastUpdated`) VALUES
(1, 1, 100, '2025-05-14 01:37:17'),
(2, 2, 50, '2025-05-14 01:37:17'),
(3, 3, 69, '2025-05-14 09:10:55');

-- --------------------------------------------------------

--
-- Stand-in structure for view `medicationinventoryview`
-- (See below for the actual view)
--
CREATE TABLE `medicationinventoryview` (
`medicationName` varchar(45)
,`dosage` varchar(45)
,`manufacturer` varchar(100)
,`quantityAvailable` int(11)
);

-- --------------------------------------------------------

--
-- Table structure for table `Medications`
--

CREATE TABLE `Medications` (
  `medicationId` int(11) NOT NULL,
  `medicationName` varchar(45) NOT NULL,
  `dosage` varchar(45) NOT NULL,
  `manufacturer` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Medications`
--

INSERT INTO `Medications` (`medicationId`, `medicationName`, `dosage`, `manufacturer`) VALUES
(1, 'Amoxicillin', '500mg', 'Pfizer'),
(2, 'Ibuprofen', '200mg', 'Bayer'),
(3, 'Ozempic', '0.25mg', 'Novo Nordisk'),
(4, 'Metformin', '300mg', 'Eli Lily Co');

-- --------------------------------------------------------

--
-- Table structure for table `Prescriptions`
--

CREATE TABLE `Prescriptions` (
  `prescriptionId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `medicationId` int(11) NOT NULL,
  `prescribedDate` datetime NOT NULL,
  `dosageInstructions` varchar(200) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `refillCount` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Prescriptions`
--

INSERT INTO `Prescriptions` (`prescriptionId`, `userId`, `medicationId`, `prescribedDate`, `dosageInstructions`, `quantity`, `refillCount`) VALUES
(4, 3, 1, '2025-05-13 17:29:50', 'Take one capsule every 8 hours', 30, 2),
(5, 4, 2, '2025-05-13 17:29:50', 'Take two tablets after meals', 20, 1),
(6, 3, 3, '2025-05-13 17:29:50', 'One tablet daily at bedtime', 15, 0),
(7, 9, 1, '2025-05-13 17:38:13', 'twice daily', 1, 3),
(8, 9, 3, '2025-05-14 09:10:55', '0.25 MG ONCE A WEEK', 6, 3);

--
-- Triggers `Prescriptions`
--
DELIMITER $$
CREATE TRIGGER `AfterPrescriptionInsert` AFTER INSERT ON `Prescriptions` FOR EACH ROW BEGIN
    UPDATE Inventory
    SET quantityAvailable = quantityAvailable - NEW.quantity,
        lastUpdated = NOW()
    WHERE medicationId = NEW.medicationId;

    -- Optional: Send a warning if stock is low (pseudo code, implementation in PHP)
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `Sales`
--

CREATE TABLE `Sales` (
  `saleId` int(11) NOT NULL,
  `prescriptionId` int(11) NOT NULL,
  `saleDate` datetime NOT NULL,
  `quantitySold` int(11) NOT NULL,
  `saleAmount` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Users`
--

CREATE TABLE `Users` (
  `userId` int(11) NOT NULL,
  `userName` varchar(45) NOT NULL,
  `contactInfo` varchar(200) DEFAULT NULL,
  `userType` enum('pharmacist','patient') NOT NULL,
  `password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Users`
--

INSERT INTO `Users` (`userId`, `userName`, `contactInfo`, `userType`, `password`) VALUES
(3, 'john_pharm', 'john@pharmacy.com', 'pharmacist', '$2y$10$WzF2vwFYXbc4CJZr3qSMneJ4Yw/S7NnaHj8gUHlUsqW0.9L4hV9J6'),
(4, 'anna_patient', 'anna@gmail.com', 'patient', '$2y$10$WzF2vwFYXbc4CJZr3qSMneJ4Yw/S7NnaHj8gUHlUsqW0.9L4hV9J6'),
(5, 'maria_pharm', 'maria@pharma.net', 'pharmacist', '$2y$10$WzF2vwFYXbc4CJZr3qSMneJ4Yw/S7NnaHj8gUHlUsqW0.9L4hV9J6'),
(6, 'daniel_patient', 'daniel@gmail.com', 'patient', '$2y$10$WzF2vwFYXbc4CJZr3qSMneJ4Yw/S7NnaHj8gUHlUsqW0.9L4hV9J6'),
(7, 'nina_pharm', 'nina@pharma.com', 'pharmacist', '123456'),
(8, 'testpharma', 'testemail', 'pharmacist', '$2y$10$J5jPCNBVKqGXFpNbtSawPuGmLWMlooDll4cjmrNWRi2JjgHc/9V1i'),
(9, 'testpatient', 'patientemail', 'patient', '$2y$10$qtbGkWn4iwW2mj6s6vnSjO8CQsLmirG3/F4lMEyKcNfdU3g1LF0Hm');

-- --------------------------------------------------------

--
-- Structure for view `medicationinventoryview`
--
DROP TABLE IF EXISTS `medicationinventoryview`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `medicationinventoryview`  AS SELECT `m`.`medicationName` AS `medicationName`, `m`.`dosage` AS `dosage`, `m`.`manufacturer` AS `manufacturer`, `i`.`quantityAvailable` AS `quantityAvailable` FROM (`medications` `m` join `inventory` `i` on(`m`.`medicationId` = `i`.`medicationId`)) ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Inventory`
--
ALTER TABLE `Inventory`
  ADD PRIMARY KEY (`inventoryId`),
  ADD UNIQUE KEY `inventoryId` (`inventoryId`),
  ADD KEY `medicationId` (`medicationId`);

--
-- Indexes for table `Medications`
--
ALTER TABLE `Medications`
  ADD PRIMARY KEY (`medicationId`),
  ADD UNIQUE KEY `medicationId` (`medicationId`);

--
-- Indexes for table `Prescriptions`
--
ALTER TABLE `Prescriptions`
  ADD PRIMARY KEY (`prescriptionId`),
  ADD UNIQUE KEY `prescriptionId` (`prescriptionId`),
  ADD KEY `userId` (`userId`),
  ADD KEY `medicationId` (`medicationId`);

--
-- Indexes for table `Sales`
--
ALTER TABLE `Sales`
  ADD PRIMARY KEY (`saleId`),
  ADD UNIQUE KEY `saleId` (`saleId`),
  ADD KEY `prescriptionId` (`prescriptionId`);

--
-- Indexes for table `Users`
--
ALTER TABLE `Users`
  ADD PRIMARY KEY (`userId`),
  ADD UNIQUE KEY `userId` (`userId`),
  ADD UNIQUE KEY `userName` (`userName`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Inventory`
--
ALTER TABLE `Inventory`
  MODIFY `inventoryId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `Medications`
--
ALTER TABLE `Medications`
  MODIFY `medicationId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `Prescriptions`
--
ALTER TABLE `Prescriptions`
  MODIFY `prescriptionId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `Sales`
--
ALTER TABLE `Sales`
  MODIFY `saleId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Users`
--
ALTER TABLE `Users`
  MODIFY `userId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `Inventory`
--
ALTER TABLE `Inventory`
  ADD CONSTRAINT `inventory_ibfk_1` FOREIGN KEY (`medicationId`) REFERENCES `Medications` (`medicationId`);

--
-- Constraints for table `Prescriptions`
--
ALTER TABLE `Prescriptions`
  ADD CONSTRAINT `prescriptions_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `Users` (`userId`),
  ADD CONSTRAINT `prescriptions_ibfk_2` FOREIGN KEY (`medicationId`) REFERENCES `Medications` (`medicationId`);

--
-- Constraints for table `Sales`
--
ALTER TABLE `Sales`
  ADD CONSTRAINT `sales_ibfk_1` FOREIGN KEY (`prescriptionId`) REFERENCES `Prescriptions` (`prescriptionId`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
