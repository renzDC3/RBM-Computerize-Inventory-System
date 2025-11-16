-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: inventory_and_sales_system_database
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

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
-- Table structure for table `add_product_history`
--

DROP TABLE IF EXISTS `add_product_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `add_product_history` (
  `add_product_history_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `barcode` varchar(255) NOT NULL,
  `category` varchar(255) NOT NULL,
  `model` varchar(255) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(15,2) NOT NULL,
  `date_time` datetime NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `Id` int(11) NOT NULL,
  PRIMARY KEY (`add_product_history_id`),
  KEY `product_id` (`product_id`,`Id`),
  KEY `add_product_history_id` (`add_product_history_id`),
  KEY `Id` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `add_product_history`
--

LOCK TABLES `add_product_history` WRITE;
/*!40000 ALTER TABLE `add_product_history` DISABLE KEYS */;
INSERT INTO `add_product_history` VALUES (1,1,'Break pad','123456789','','',15,1100.00,'2024-10-25 12:12:48',0,1),(2,2,'Seat Cover','234567891','','',15,700.00,'2024-10-25 12:16:13',0,1),(3,3,'Oil Seal','345678912','','',15,110.00,'2024-10-25 12:17:30',0,1),(4,4,'Gear Oil','456789123','','',15,35.00,'2024-10-25 12:18:29',0,1),(5,5,'Fork Oil','567891234','','',15,102.00,'2024-10-25 12:19:07',0,1),(6,6,'Tire','678912345','','',15,2500.00,'2024-10-25 12:19:44',0,1),(7,7,'CDI','789123456','','',15,150.00,'2024-10-25 12:20:26',0,1),(8,8,'Mini Driving Light','891234567','','',15,620.00,'2024-10-25 12:24:25',0,1),(9,9,'Piaa Horn','912345678','','',15,1300.00,'2024-10-25 12:25:34',0,1),(10,10,'CVT Cleaning','1123456789','','',15,300.00,'2024-10-25 12:26:37',0,1),(11,11,'Relay','2234567891','','',15,50.00,'2024-10-25 12:27:36',0,1),(12,12,'Fuse','3345678912','','',15,200.00,'2024-10-25 12:28:26',0,1),(13,13,'Tail Light','4456789123','','',15,1900.00,'2024-10-25 12:29:16',0,1),(14,14,'Gasket, Cylinder','1','','',15,200.00,'2024-10-26 10:15:47',0,1),(15,15,'Prembo Califer','1122','','',100,40000.00,'2024-10-26 11:53:09',0,1),(26,27,'Very Spoiled Milk','98792345124','all','Milky',5,15.00,'2025-07-18 18:30:35',0,1),(27,30,'Super Rotten to the core Milk','98792345124','all','Milky',5,15.00,'2025-07-18 18:55:19',0,1),(28,32,'asdfdff','125421342134','all','asdfsadf',5,15.00,'2025-07-18 18:58:51',0,1),(29,33,'asdfasgdasdg','1.231231251254123e15','all','asdfsdafasdf',5,15.00,'2025-07-18 19:01:22',0,1),(30,34,'Some cretaceous mammal','8251239875918','all','Milky',15,100.00,'2025-09-12 11:38:00',0,1),(31,35,'Milk from the Permian Period','61238746218703','1','Milky',15,15.00,'2025-09-26 11:41:17',0,29),(32,37,'Milk from Jurassic Period','162098369','all','0',15,15.00,'2025-09-26 11:52:32',0,1),(33,38,'Milk from Cretaceous Period','253463483','all','0',15,15.00,'2025-09-26 11:57:09',0,1),(37,44,'Soy Milk','109284691283','all','Milky',15,15.00,'2025-09-26 12:39:03',0,1),(38,45,'Dairy Milk','7512809517','all','Milky',15,15.00,'2025-09-26 12:45:55',1,1),(39,46,'Oil','236345245','Engine Component','Oil',15,15.00,'2025-10-16 20:21:28',0,33),(40,47,'Oily oil','73472135','Engine Component','Oil',15,15.00,'2025-10-16 20:22:07',11,33),(41,48,'Very Oily Oil','1920347219307','Engine Component','Oil',15,19.00,'2025-10-16 20:28:30',11,33);
/*!40000 ALTER TABLE `add_product_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `backups`
--

DROP TABLE IF EXISTS `backups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `backups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `file_name` varchar(255) NOT NULL,
  `file_id` varchar(255) NOT NULL,
  `uploaded_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `backups`
--

LOCK TABLES `backups` WRITE;
/*!40000 ALTER TABLE `backups` DISABLE KEYS */;
INSERT INTO `backups` VALUES (1,'db_backup_2025-10-16_23-40-15.sql','id:ec_8x0O3YpEAAAAAAAAACQ','2025-10-17 05:40:20'),(2,'db_backup_2025-10-16_23-47-23.sql','id:ec_8x0O3YpEAAAAAAAAACg','2025-10-17 05:47:27'),(3,'db_backup_2025-10-17_00-20-48.sql','id:ec_8x0O3YpEAAAAAAAAACw','2025-10-17 06:20:52'),(4,'db_backup_2025-10-17_00-21-16.sql','id:ec_8x0O3YpEAAAAAAAAADA','2025-10-17 06:21:20');
/*!40000 ALTER TABLE `backups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `delete_employee_history`
--

DROP TABLE IF EXISTS `delete_employee_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `delete_employee_history` (
  `delete_employee_history_id` int(11) NOT NULL AUTO_INCREMENT,
  `Id` int(11) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `date_joined` date NOT NULL,
  `Username` varchar(200) DEFAULT NULL,
  `Password` varchar(200) DEFAULT NULL,
  `two_factor_secret` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`delete_employee_history_id`),
  KEY `Test31` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `delete_employee_history`
--

LOCK TABLES `delete_employee_history` WRITE;
/*!40000 ALTER TABLE `delete_employee_history` DISABLE KEYS */;
INSERT INTO `delete_employee_history` VALUES (1,33,'Worker','Employee III','2025-09-10','Worker11','$2y$10$PVBiV4hdJUemykNa0eXAI.GmG2VP2QPKa3WvfzETuy1shiGIrP1ty',NULL);
/*!40000 ALTER TABLE `delete_employee_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `delete_product_history`
--

DROP TABLE IF EXISTS `delete_product_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `delete_product_history` (
  `delete_product_history_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `barcode` varchar(255) NOT NULL,
  `category` varchar(255) NOT NULL,
  `model` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(15,2) NOT NULL,
  `date_time` datetime NOT NULL,
  `Id` int(11) NOT NULL,
  PRIMARY KEY (`delete_product_history_id`),
  KEY `product_id` (`product_id`,`Id`),
  KEY `delete_product_history_id` (`delete_product_history_id`),
  KEY `Id` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `delete_product_history`
--

LOCK TABLES `delete_product_history` WRITE;
/*!40000 ALTER TABLE `delete_product_history` DISABLE KEYS */;
INSERT INTO `delete_product_history` VALUES (7,0,'asdfasgdasdg','1231231251254123','all','0',5,15.00,'2025-08-08 01:34:17',1),(8,0,'Worker','Employee III','','0',0,2025.00,'2025-09-10 16:32:44',29);
/*!40000 ALTER TABLE `delete_product_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `edit_product_history`
--

DROP TABLE IF EXISTS `edit_product_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `edit_product_history` (
  `edit_product_history_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `barcode` varchar(255) DEFAULT NULL,
  `category` varchar(255) DEFAULT NULL,
  `model` varchar(255) NOT NULL,
  `reason` varchar(255) NOT NULL,
  `date_time` datetime NOT NULL,
  `Id` int(11) NOT NULL,
  PRIMARY KEY (`edit_product_history_id`),
  KEY `edit_product_history_id` (`edit_product_history_id`,`Id`),
  KEY `product_id` (`product_id`),
  KEY `Id` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `edit_product_history`
--

LOCK TABLES `edit_product_history` WRITE;
/*!40000 ALTER TABLE `edit_product_history` DISABLE KEYS */;
INSERT INTO `edit_product_history` VALUES (1,1,'','','option1','','Wrong input','2024-10-25 12:14:26',1),(2,1,'','','Braking System','','Correction','2024-10-25 15:49:54',1),(3,2,'','','Interior Accessories','','Correction','2024-10-25 15:50:19',1),(4,3,'','','Engine Component','','Correction','2024-10-25 15:50:32',1),(5,4,'','','Engine Component','','Correction','2024-10-25 15:50:46',1),(6,5,'','','Engine Component','','Correction','2024-10-25 15:51:02',1),(7,6,'','','Tires and Wheels','','Correction','2024-10-25 15:51:25',1),(8,7,'','','Engine Component','','Correction','2024-10-25 15:51:41',1),(9,8,'','','Lighting','','Correction','2024-10-25 15:51:57',1),(10,9,'','','Lighting','','Correction','2024-10-25 15:52:11',1),(11,11,'','','Electrical Component','','Correction','2024-10-25 15:52:34',1),(12,10,'','','Engine Component','','Correction','2024-10-25 15:52:47',1),(13,12,'','','Electrical Component','','Correction','2024-10-25 15:53:01',1),(14,13,'','','Lighting','','Correction','2024-10-25 15:53:24',1),(15,5,'','4806527201175',NULL,'','Adjust','2024-10-26 09:38:08',1),(16,14,'','','Engine Component','','correction','2024-10-26 10:17:05',1),(17,3,'','931023080200',NULL,'','correction','2024-10-26 10:20:31',1),(18,14,'','12211324',NULL,'','correction','2024-10-26 10:38:08',1),(19,1,'','4902430222891',NULL,'','correction','2024-10-26 10:38:38',1),(20,14,'','2PHE13510000',NULL,'','correction','2024-10-26 10:43:14',1),(21,3,'','931023080200',NULL,'','correction','2024-10-26 10:44:45',1),(22,3,'','931023080200',NULL,'','correction','2024-10-26 10:44:47',1),(23,15,'','1122-DCD',NULL,'','correction','2024-10-26 11:53:54',1),(24,17,'On the very verge of expiring milk','',NULL,'','name correction','2024-11-14 22:25:16',1),(26,32,'Brake pad','',NULL,'','Wrong name','2025-10-10 10:10:41',33),(27,15,'Brembo Califers','',NULL,'','Wrong name','2025-10-10 10:11:35',33);
/*!40000 ALTER TABLE `edit_product_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `handle_defective_product_history`
--

DROP TABLE IF EXISTS `handle_defective_product_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `handle_defective_product_history` (
  `handle_defective_product_history_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `barcode` varchar(255) NOT NULL,
  `category` varchar(255) NOT NULL,
  `model` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(15,2) NOT NULL,
  `date_time` datetime NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `Id` int(11) NOT NULL,
  PRIMARY KEY (`handle_defective_product_history_id`),
  KEY `handle_defective_product_history` (`handle_defective_product_history_id`,`product_id`,`Id`),
  KEY `product_id` (`product_id`),
  KEY `Id` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `handle_defective_product_history`
--

LOCK TABLES `handle_defective_product_history` WRITE;
/*!40000 ALTER TABLE `handle_defective_product_history` DISABLE KEYS */;
INSERT INTO `handle_defective_product_history` VALUES (1,1,'Break pad','4902430222891','Braking System','Honda XR150',1,1100.00,'2025-09-10 19:36:30',0,1),(2,1,'Break pad','4902430222891','Braking System','Honda XR150',1,1100.00,'2025-09-26 12:35:19',0,1),(3,5,'Fork Oil 500ml','4806527201175','Engine Component','',1,102.00,'2025-09-26 12:35:49',0,1),(4,1,'Break pad','4902430222891','Braking System','Honda XR150',1,1100.00,'2025-09-26 12:40:10',1,1);
/*!40000 ALTER TABLE `handle_defective_product_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_detail`
--

DROP TABLE IF EXISTS `order_detail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `order_detail` (
  `order_detail_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(8,2) NOT NULL,
  `subtotal` decimal(8,2) NOT NULL,
  PRIMARY KEY (`order_detail_id`),
  KEY `order_detail_id` (`order_detail_id`,`order_id`,`product_id`),
  KEY `Test7` (`order_id`),
  KEY `Test8` (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_detail`
--

LOCK TABLES `order_detail` WRITE;
/*!40000 ALTER TABLE `order_detail` DISABLE KEYS */;
INSERT INTO `order_detail` VALUES (1,1,1,'Break pad',1,1100.00,1100.00),(2,1,2,'Seat Cover',1,700.00,700.00),(3,1,3,'Oil Seal',1,110.00,110.00),(4,2,10,'CVT Cleaning',1,300.00,300.00),(5,2,11,'Relay',1,50.00,50.00),(6,2,12,'Fuse',1,200.00,200.00),(7,3,8,'Mini Driving Light',1,620.00,620.00),(8,3,9,'Piaa Horn',1,1300.00,1300.00),(9,4,1,'Break pad',8,1100.00,8800.00),(10,4,2,'Seat Cover',10,700.00,7000.00),(11,4,4,'Gear Oil',3,35.00,105.00),(12,5,1,'Break pad',1,1100.00,1100.00),(13,6,2,'Seat Cover',1,700.00,700.00),(14,7,2,'Seat Cover',1,700.00,700.00),(15,8,2,'Seat Cover',2,700.00,1400.00),(16,9,13,'Tail Light',3,1900.00,5700.00),(17,10,15,'Prembo Califer',5,40000.00,200000.00),(18,11,11,'Relay',1,50.00,50.00),(19,12,3,'Oil Seal',3,110.00,330.00),(20,12,1,'Break pad',1,1100.00,1100.00),(21,12,2,'Seat Cover',1,700.00,700.00),(22,12,4,'Gear Oil',1,35.00,35.00),(23,12,5,'Fork Oil',1,102.00,102.00),(24,13,1,'Break pad',1,1100.00,1100.00),(25,15,1,'Break pad',3,1100.00,3300.00),(26,16,3,'Oil Seal',5,110.00,550.00),(27,17,1,'Break pad',5,1100.00,5500.00),(28,17,2,'Seat Cover',1,700.00,700.00),(29,17,3,'Oil Seal',2,110.00,220.00),(30,36,2,'Seat Cover',1,700.00,700.00),(31,37,1,'Break pad',10,1100.00,11000.00),(32,38,4,'Gear Oil 500ml',10,35.00,350.00),(33,39,1,'Break pad',7,1100.00,7700.00),(34,40,2,'Seat Cover',3,700.00,2100.00),(35,41,3,'Oil Seal',3,110.00,330.00),(36,42,5,'Fork Oil 500ml',7,102.00,714.00),(37,43,6,'Tire',15,2500.00,37500.00),(38,44,7,'CDI',14,150.00,2100.00),(39,45,8,'Mini Driving Light',3,620.00,1860.00),(40,46,9,'Piaa Horn',7,1300.00,9100.00),(41,47,10,'CVT Cleaning',5,300.00,1500.00),(42,48,12,'Fuse',5,200.00,1000.00),(43,49,13,'Tail Light',6,1900.00,11400.00),(44,50,14,'Gasket, Cylinder',5,200.00,1000.00),(45,51,1,'Break pad',1,1100.00,1100.00);
/*!40000 ALTER TABLE `order_detail` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL AUTO_INCREMENT,
  `orders_total` decimal(8,2) NOT NULL,
  `orders_cash` decimal(8,2) NOT NULL,
  `orders_change` decimal(8,2) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Id` int(11) NOT NULL,
  PRIMARY KEY (`order_id`),
  KEY `order_id_2` (`order_id`),
  KEY `Id` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (1,1910.00,2000.00,90.00,'2024-11-14 14:53:40',1),(2,550.00,1000.00,450.00,'2024-11-14 14:53:40',1),(3,1920.00,2000.00,80.00,'2024-11-14 14:53:40',1),(4,15905.00,16000.00,95.00,'2024-11-14 14:53:40',1),(5,1100.00,2000.00,900.00,'2024-11-14 14:53:40',1),(6,700.00,1000.00,300.00,'2024-11-14 14:53:40',1),(7,700.00,1000.00,300.00,'2024-11-14 14:53:40',1),(8,1400.00,2000.00,600.00,'2024-11-14 14:53:40',1),(9,5700.00,6000.00,300.00,'2024-11-14 14:53:40',1),(10,200000.00,250000.00,50000.00,'2024-11-14 14:53:40',1),(11,50.00,100.00,50.00,'2024-11-14 14:53:40',1),(12,2267.00,2300.00,33.00,'2024-11-14 14:53:40',1),(13,1100.00,2000.00,900.00,'2024-11-14 23:03:21',1),(14,5500.00,6000.00,500.00,'2025-03-20 19:39:19',1),(15,3300.00,4000.00,700.00,'2025-03-20 19:39:50',1),(16,550.00,1000.00,450.00,'2025-04-08 11:43:04',1),(17,6420.00,7000.00,580.00,'2025-05-17 15:24:12',1),(18,6420.00,7000.00,580.00,'2025-05-17 15:24:12',1),(19,6420.00,7000.00,580.00,'2025-05-17 15:24:13',1),(20,6420.00,7000.00,580.00,'2025-05-17 15:24:13',1),(21,6420.00,7000.00,580.00,'2025-05-17 15:24:13',1),(22,6420.00,7000.00,580.00,'2025-05-17 15:24:13',1),(23,6420.00,7000.00,580.00,'2025-05-17 15:24:13',1),(24,6420.00,7000.00,580.00,'2025-05-17 15:24:13',1),(25,6420.00,7000.00,580.00,'2025-05-17 15:24:14',1),(26,6420.00,7000.00,580.00,'2025-05-17 15:24:14',1),(27,6420.00,7000.00,580.00,'2025-05-17 15:24:14',1),(28,6420.00,7000.00,580.00,'2025-05-17 15:24:14',1),(29,6420.00,7000.00,580.00,'2025-05-17 15:24:14',1),(30,6420.00,7000.00,580.00,'2025-05-17 15:24:14',1),(31,6420.00,7000.00,580.00,'2025-05-17 15:24:15',1),(32,6420.00,7000.00,580.00,'2025-05-17 15:24:15',1),(33,6420.00,7000.00,580.00,'2025-05-17 15:24:15',1),(34,6420.00,7000.00,580.00,'2025-05-17 15:24:15',1),(35,6420.00,7000.00,580.00,'2025-05-17 15:24:15',1),(36,700.00,1000.00,300.00,'2025-05-24 14:01:05',1),(37,11000.00,11000.00,0.00,'2025-08-07 17:35:05',1),(38,350.00,400.00,50.00,'2025-09-12 01:00:12',29),(39,7700.00,8000.00,300.00,'2025-09-12 01:00:38',29),(40,2100.00,3000.00,900.00,'2025-09-12 01:00:56',29),(41,330.00,500.00,170.00,'2025-09-12 01:01:11',29),(42,714.00,800.00,86.00,'2025-09-12 01:01:47',29),(43,37500.00,38000.00,500.00,'2025-09-12 01:02:13',29),(44,2100.00,3000.00,900.00,'2025-09-12 01:02:38',29),(45,1860.00,2000.00,140.00,'2025-09-12 01:02:51',29),(46,9100.00,10000.00,900.00,'2025-09-12 01:03:23',29),(47,1500.00,2000.00,500.00,'2025-09-12 01:03:35',29),(48,1000.00,1000.00,0.00,'2025-09-12 01:03:55',29),(49,11400.00,12000.00,600.00,'2025-09-12 01:04:10',29),(50,1000.00,1000.00,0.00,'2025-09-12 01:04:24',29),(51,1100.00,1500.00,400.00,'2025-10-09 15:34:04',33);
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `products` (
  `product_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `barcode` varchar(255) NOT NULL,
  `category` varchar(255) NOT NULL,
  `model` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(15,2) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  PRIMARY KEY (`product_id`),
  KEY `supplier_id` (`supplier_id`),
  KEY `product_id` (`product_id`,`supplier_id`)
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (1,'Break pads','4902430222891','Braking System','Honda XR150',9,1100.00,0),(2,'Seat Cover','6922014510112','Interior Accessories','Universal',5,700.00,0),(3,'Oil Seal','931023080200','Engine Component','Yamaha Aero',0,110.00,0),(4,'Gear Oil 500ml','4806035204828','Engine Component','',1,35.00,0),(5,'Fork Oil 500ml','4806527201175','Engine Component','',7,102.00,0),(6,'Tire','678912345','Tires and Wheels','Suzuki Raid',0,2500.00,0),(7,'CDI','789123456','Engine Component','Universal',1,150.00,0),(8,'Mini Driving Light','891234567','Lighting','Yamaha NMAX',11,620.00,0),(9,'Piaa Horn','912345678','Lighting','Suzuki Burg',7,1300.00,0),(10,'CVT Cleaning','1123456789','Engine Component','Hondal Clic',9,300.00,0),(11,'Relay','2234567891','Electrical Component','Universal',0,50.00,0),(12,'Fuse','3345678912','Electrical Component','Universal',9,200.00,0),(13,'Tail Light','4456789123','Lighting','Honda ADV16',6,1900.00,0),(14,'Gasket, Cylinder','2PHE13510000','Engine Component','Universal',10,200.00,0),(15,'Brembo Califers','1122-DCD','Braking System','Universal',95,40000.00,0),(16,'Almost Expired Milk','412765457','Braking System','',15,15.00,0),(17,'On the immediate verge of expiring milk','63456134','Braking System','',15,15.00,0),(19,'Spoiled Milk','902141234','Braking System','Milky',15,15.00,0),(27,'Very Spoiled Milk','098792345124','all','Milky',5,15.00,0),(28,'Super Rotten Milk','098792345124','all','Milky',5,15.00,0),(29,'Rotten to the core Milk','098792345124','all','Milky',5,15.00,0),(30,'Super Rotten to the core Milk','098792345124','all','Milky',5,15.00,0),(31,'jdfgh','125421342134','all','asdfsadf',8,15.00,0),(32,'Brake pad','125421342134','all','asdfsadf',5,15.00,0),(34,'Some cretaceous mammal','8251239875918','all','Milky',15,100.00,0),(35,'Milk from the Permian Period','61238746218703','1','Milky',15,15.00,0),(36,'Milk from Triassic Period','162098369','all','Milky',15,15.00,1),(37,'Milk from Jurassic Period','162098369','all','Milky',15,15.00,1),(38,'Milk from Cretaceous Period','253463483','all','Milky',15,15.00,1),(39,'Milk from Eocene Epoch','12349801724','all','Milky',15,15.00,1),(40,'Milk from Pliocene Epoch','12349801724','all','Milky',15,15.00,1),(44,'Soy Milk','109284691283','all','Milky',15,15.00,1),(45,'Dairy Milk','7512809517','all','Milky',15,15.00,1),(46,'Oil','236345245','Engine Component','Oil',15,15.00,0),(47,'Oily oil','73472135','Engine Component','Oil',15,15.00,11),(48,'Very Oily Oil','1920347219307','Engine Component','Oil',15,19.00,11);
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sales`
--

DROP TABLE IF EXISTS `sales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sales` (
  `sales_id` int(11) NOT NULL AUTO_INCREMENT,
  `sales_total` decimal(8,2) NOT NULL,
  `sales_cash` decimal(8,2) NOT NULL,
  `sales_change` decimal(8,2) NOT NULL,
  `sales_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Id` int(11) NOT NULL,
  PRIMARY KEY (`sales_id`),
  KEY `sales_id` (`sales_id`,`Id`),
  KEY `Id` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sales`
--

LOCK TABLES `sales` WRITE;
/*!40000 ALTER TABLE `sales` DISABLE KEYS */;
/*!40000 ALTER TABLE `sales` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `services`
--

DROP TABLE IF EXISTS `services`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `services` (
  `services_id` int(11) NOT NULL AUTO_INCREMENT,
  `services_description` varchar(255) NOT NULL,
  `services_price` decimal(8,2) NOT NULL,
  `services_customer_cash` decimal(8,2) NOT NULL,
  `services_customer_change` decimal(8,2) NOT NULL,
  `services_date` datetime NOT NULL,
  `Id` int(11) NOT NULL,
  PRIMARY KEY (`services_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `services`
--

LOCK TABLES `services` WRITE;
/*!40000 ALTER TABLE `services` DISABLE KEYS */;
INSERT INTO `services` VALUES (1,'Fixing wheels',800.00,1000.00,200.00,'2025-09-26 10:06:31',10),(2,'Fix chains',800.00,1000.00,200.00,'2025-10-09 23:33:42',10),(3,'Fix motor',800.00,1000.00,200.00,'2025-10-09 23:37:48',10),(4,'Fix spokes',800.00,1000.00,200.00,'2025-10-10 10:39:28',33);
/*!40000 ALTER TABLE `services` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stock_adjustment`
--

DROP TABLE IF EXISTS `stock_adjustment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stock_adjustment` (
  `adjustment_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `qty` int(11) NOT NULL,
  `date_time` datetime NOT NULL,
  `reason` text NOT NULL,
  `Id` int(11) NOT NULL,
  PRIMARY KEY (`adjustment_id`),
  KEY `adjustment_id` (`adjustment_id`,`product_id`,`Id`),
  KEY `product_id` (`product_id`),
  KEY `Id` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stock_adjustment`
--

LOCK TABLES `stock_adjustment` WRITE;
/*!40000 ALTER TABLE `stock_adjustment` DISABLE KEYS */;
INSERT INTO `stock_adjustment` VALUES (1,11,1,'2024-10-26 10:26:17','1',1),(2,3,10,'2024-11-14 22:09:44','Wrong count',1),(3,1,5,'2025-08-07 05:44:17','Wrong count',1),(4,1,10,'2025-08-07 05:45:10','Wrong count',1),(5,31,3,'2025-08-08 01:33:55','Wrong count',1);
/*!40000 ALTER TABLE `stock_adjustment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stock_in`
--

DROP TABLE IF EXISTS `stock_in`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stock_in` (
  `stock_in_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `qty` int(11) NOT NULL,
  `date_time` datetime NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `delivery_id` varchar(255) DEFAULT NULL,
  `Id` int(11) NOT NULL,
  PRIMARY KEY (`stock_in_id`),
  KEY `stock_in_id` (`stock_in_id`,`product_id`),
  KEY `Test12` (`product_id`),
  KEY `Id` (`Id`),
  KEY `supplier_id` (`supplier_id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stock_in`
--

LOCK TABLES `stock_in` WRITE;
/*!40000 ALTER TABLE `stock_in` DISABLE KEYS */;
INSERT INTO `stock_in` VALUES (1,2,5,'2024-10-26 10:39:28',0,'1',1),(2,2,5,'2024-10-26 10:39:29',0,'1',1),(3,1,1,'2024-11-14 21:52:10',0,'0',1),(4,2,1,'2024-11-14 21:52:37',0,'0',1),(5,5,1,'2024-11-14 22:00:55',0,'A14',1),(6,1,5,'2025-05-17 15:03:39',0,'74398',1),(7,1,2,'2025-05-17 15:06:11',0,'74398',1),(8,1,5,'2025-08-07 06:09:57',0,'A12',1),(9,1,5,'2025-08-07 06:23:29',0,'',1),(10,1,5,'2025-08-07 06:23:33',0,'',1),(11,1,5,'2025-08-07 06:25:46',0,'',1),(12,31,5,'2025-08-08 01:34:02',0,'',1),(13,41,1,'2025-09-26 12:17:18',1,'',1),(14,48,15,'2025-10-16 20:28:30',11,NULL,33);
/*!40000 ALTER TABLE `stock_in` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `suppliers`
--

DROP TABLE IF EXISTS `suppliers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `suppliers` (
  `supplier_id` int(11) NOT NULL,
  `supplier_name` varchar(255) NOT NULL,
  `supplier__business_address` varchar(255) NOT NULL,
  `supplier_contact_no` varchar(255) NOT NULL,
  `supplier_email` varchar(255) NOT NULL,
  `supplier_date_added` datetime DEFAULT NULL,
  PRIMARY KEY (`supplier_id`),
  KEY `supplier_id` (`supplier_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `suppliers`
--

LOCK TABLES `suppliers` WRITE;
/*!40000 ALTER TABLE `suppliers` DISABLE KEYS */;
INSERT INTO `suppliers` VALUES (0,'Wheel Maker','8 Wheel St','67210895712','wheelmaker@email.com','2025-10-03 01:48:23'),(10,'Supplier Supplier','15 Supplier St.','7834673465','supplier89@email.com','2025-10-03 02:38:09'),(11,'John Supplier','8 Supplier Street, Barangay Manufacturer, Maker City','91-23912-308409','johnsupplier@email.com','2025-10-03 03:58:10');
/*!40000 ALTER TABLE `suppliers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `date_joined` date NOT NULL,
  `Username` varchar(200) DEFAULT NULL,
  `Password` varchar(200) DEFAULT NULL,
  `two_factor_secret` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `Id` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'','','0000-00-00','Manager','$2y$10$.2dsCdGiUSuhGmwf07cuRO3g89HMlZh35TqW1Rq3pMpHoVHAymKG6','FF2HZ7KJX2NHZSOH'),(10,'Worker','Employee','2025-09-04','Worker9','$2y$10$QZun9keATF2y2gz29ZMf2ediAwoffm7r.OXxeeUNhKvXiVwft3m5K','FF2HZ7KJX2NHZSOH'),(32,'Worker','Employee II','2025-09-04','Worker10','$2y$10$4y2kp926ECcfEJYM7XnIc.2PB7C1wTSGx6dzzfgdOtcrpuBSF4AXq','FF2HZ7KJX2NHZSOH'),(33,'','','0000-00-00','Admin','$2y$10$LOtLALQ6AAsb69EDfzcVx.6YEj5l6Ka5m3TzzcR5uIq0rlOQ3JnRa','FF2HZ7KJX2NHZSOH'),(35,'Worker','Worker','2025-10-09','Worker1','$2y$10$dUKZncskCKUr5/jS/nbEeO/gyLVaES48Hu40Nf6yPb3b6MpwYf6Iq','FF2HZ7KJX2NHZSOH');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-10-17 10:54:58
