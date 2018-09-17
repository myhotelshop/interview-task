-- MySQL dump 10.16  Distrib 10.1.26-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: CTM
-- ------------------------------------------------------
-- Server version	10.1.26-MariaDB-0+deb9u1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;



--
-- Current Database: `CTM`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `CTM` /*!40100 DEFAULT CHARACTER SET utf8mb4 */;

USE `CTM`;

--
-- Current User: `mhs`
--

CREATE USER 'mhs'@'localhost' IDENTIFIED BY 'mhs4mhs@ctm.';

GRANT SELECT,INSERT,UPDATE ON CTM.* TO 'mhs'@'localhost';


--
-- Table structure for table `connection`
--

DROP TABLE IF EXISTS `connection`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `connection` (
  `id_connection` int(11) NOT NULL AUTO_INCREMENT,
  `id_conversion` int(11) DEFAULT NULL,
  `id_platform` int(11) DEFAULT NULL,
  `time` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_connection`),
  KEY `id_conversion` (`id_conversion`),
  KEY `id_platform` (`id_platform`),
  CONSTRAINT `connection_ibfk_1` FOREIGN KEY (`id_conversion`) REFERENCES `conversion` (`id_conversion`),
  CONSTRAINT `connection_ibfk_2` FOREIGN KEY (`id_platform`) REFERENCES `platform` (`id_platform`)
) ENGINE=InnoDB AUTO_INCREMENT=162975 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `conversion`
--

DROP TABLE IF EXISTS `conversion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `conversion` (
  `id_conversion` int(11) NOT NULL,
  `id_customer` int(11) DEFAULT NULL,
  `id_booking` int(11) DEFAULT NULL,
  `revenue` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_conversion`),
  KEY `id_customer` (`id_customer`),
  CONSTRAINT `conversion_ibfk_1` FOREIGN KEY (`id_customer`) REFERENCES `customer` (`id_customer`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `customer`
--

DROP TABLE IF EXISTS `customer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `customer` (
  `id_customer` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `pass_hash` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_customer`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;



--
-- Dumping data for table `customer`
--

LOCK TABLES `customer` WRITE;
/*!40000 ALTER TABLE `customer` DISABLE KEYS */;
INSERT INTO `customer` VALUES (123,'max','$2y$10$IF.5XmAbgN1LsZxPMu6x1O0dzJKNqH3rZomuRS7FGpGt/hl1Yu1sG');
/*!40000 ALTER TABLE `customer` ENABLE KEYS */;
UNLOCK TABLES;




--
-- Table structure for table `platform`
--

DROP TABLE IF EXISTS `platform`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `platform` (
  `id_platform` int(11) NOT NULL,
  `name` varchar(128) DEFAULT NULL,
  `score` int(11) DEFAULT NULL,
  `first` int(11) DEFAULT NULL,
  `last` int(11) DEFAULT NULL,
  `center` int(11) DEFAULT NULL,
  `sales` int(11) DEFAULT NULL,
  `conversions` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_platform`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2018-09-01 14:35:43
