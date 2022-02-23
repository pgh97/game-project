-- MySQL dump 10.13  Distrib 8.0.27, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: fishgame
-- ------------------------------------------------------
-- Server version	5.7.36

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `account_info`
--

DROP TABLE IF EXISTS `account_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `account_info` (
  `account_code` int(11) NOT NULL,
  `account_type` int(11) NOT NULL,
  `hive_code` int(11) DEFAULT NULL,
  `country_code` int(11) NOT NULL,
  `language_code` int(11) NOT NULL,
  `last_login_date` timestamp NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`account_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `account_info`
--

LOCK TABLES `account_info` WRITE;
/*!40000 ALTER TABLE `account_info` DISABLE KEYS */;
/*!40000 ALTER TABLE `account_info` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `auction_info_data`
--

DROP TABLE IF EXISTS `auction_info_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `auction_info_data` (
  `auction_code` int(11) NOT NULL AUTO_INCREMENT,
  `fish_code` int(11) NOT NULL,
  `money_code` int(11) NOT NULL,
  `auction_price` int(11) NOT NULL,
  `change_time` int(11) NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`auction_code`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `auction_info_data`
--

LOCK TABLES `auction_info_data` WRITE;
/*!40000 ALTER TABLE `auction_info_data` DISABLE KEYS */;
INSERT INTO `auction_info_data` VALUES (1,1,1,2000,6,'2022-02-07 00:54:09'),(2,2,1,3000,6,'2022-02-07 00:54:09'),(3,3,1,4000,6,'2022-02-07 00:54:09'),(4,4,1,5000,6,'2022-02-07 00:54:09'),(5,5,1,6000,6,'2022-02-07 00:54:09'),(6,6,1,7000,6,'2022-02-07 00:54:09'),(7,7,1,8000,6,'2022-02-07 00:54:09'),(8,8,1,9000,6,'2022-02-07 00:54:09'),(9,9,1,10000,6,'2022-02-07 00:54:09'),(10,10,1,5000,6,'2022-02-07 00:54:09'),(11,11,1,2000,6,'2022-02-07 00:54:09'),(12,12,1,3000,6,'2022-02-07 00:54:09'),(13,13,1,6000,6,'2022-02-07 00:54:09'),(14,14,1,7000,6,'2022-02-07 00:54:09'),(15,15,1,8000,6,'2022-02-07 00:54:09');
/*!40000 ALTER TABLE `auction_info_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `auction_ranking`
--

DROP TABLE IF EXISTS `auction_ranking`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `auction_ranking` (
  `week_date` int(11) NOT NULL,
  `user_code` int(11) NOT NULL,
  `money_code` int(11) NOT NULL,
  `price_sum` int(11) NOT NULL,
  `auction_rank` int(11) NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`week_date`,`user_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `auction_ranking`
--

LOCK TABLES `auction_ranking` WRITE;
/*!40000 ALTER TABLE `auction_ranking` DISABLE KEYS */;
/*!40000 ALTER TABLE `auction_ranking` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `compensation_info_data`
--

DROP TABLE IF EXISTS `compensation_info_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `compensation_info_data` (
  `compensation_code` int(11) NOT NULL AUTO_INCREMENT,
  `item_code` int(11) NOT NULL,
  `item_type` int(11) NOT NULL,
  `compensation_value` int(11) NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`compensation_code`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `compensation_info_data`
--

LOCK TABLES `compensation_info_data` WRITE;
/*!40000 ALTER TABLE `compensation_info_data` DISABLE KEYS */;
INSERT INTO `compensation_info_data` VALUES (1,1,1,1000,'2022-02-04 09:57:02'),(2,1,1,2000,'2022-02-04 09:57:02'),(3,1,1,3000,'2022-02-04 09:57:02'),(4,1,1,4000,'2022-02-04 09:57:02'),(5,2,1,100,'2022-02-04 09:57:02'),(6,2,1,200,'2022-02-04 09:57:02'),(7,2,1,300,'2022-02-04 09:57:02'),(8,2,1,400,'2022-02-04 09:57:02'),(9,1,2,5,'2022-02-04 09:57:02'),(10,2,2,5,'2022-02-04 09:57:02'),(11,3,2,5,'2022-02-04 09:57:02'),(12,4,2,5,'2022-02-04 09:57:02'),(13,5,2,5,'2022-02-04 09:57:02'),(14,6,2,5,'2022-02-04 09:57:02'),(15,7,2,5,'2022-02-04 09:57:02'),(16,8,2,5,'2022-02-04 09:57:02'),(17,9,2,5,'2022-02-04 09:57:02'),(18,1,3,5,'2022-02-04 09:57:02'),(19,2,3,5,'2022-02-04 09:57:02'),(20,3,3,5,'2022-02-04 09:57:02'),(21,4,3,5,'2022-02-04 09:57:02'),(22,5,3,5,'2022-02-04 09:57:02'),(23,6,3,5,'2022-02-04 09:57:02'),(24,7,3,5,'2022-02-04 09:57:02'),(25,8,3,5,'2022-02-04 09:57:02'),(26,9,3,5,'2022-02-04 09:57:02');
/*!40000 ALTER TABLE `compensation_info_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `country_info_data`
--

DROP TABLE IF EXISTS `country_info_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `country_info_data` (
  `country_code` int(11) NOT NULL AUTO_INCREMENT,
  `country_name` varchar(100) NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`country_code`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `country_info_data`
--

LOCK TABLES `country_info_data` WRITE;
/*!40000 ALTER TABLE `country_info_data` DISABLE KEYS */;
INSERT INTO `country_info_data` VALUES (1,'한국','2022-02-04 08:39:46');
/*!40000 ALTER TABLE `country_info_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fish_grade_data`
--

DROP TABLE IF EXISTS `fish_grade_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fish_grade_data` (
  `fish_grade_code` int(11) NOT NULL AUTO_INCREMENT,
  `fish_code` int(11) NOT NULL,
  `grade_code` int(11) NOT NULL,
  `min_value` int(11) NOT NULL,
  `max_value` int(11) NOT NULL,
  `add_experience` int(11) NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`fish_grade_code`)
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fish_grade_data`
--

LOCK TABLES `fish_grade_data` WRITE;
/*!40000 ALTER TABLE `fish_grade_data` DISABLE KEYS */;
INSERT INTO `fish_grade_data` VALUES (1,1,1,36,40,100,'2022-02-04 08:35:00'),(2,1,2,31,35,200,'2022-02-04 08:35:00'),(3,1,3,25,30,300,'2022-02-04 08:35:00'),(4,1,4,20,24,400,'2022-02-04 08:35:00'),(5,2,1,36,40,100,'2022-02-04 08:35:00'),(6,2,2,31,35,200,'2022-02-04 08:35:00'),(7,2,3,25,30,300,'2022-02-04 08:35:00'),(8,2,4,16,24,400,'2022-02-04 08:35:00'),(9,3,1,31,35,100,'2022-02-04 08:35:00'),(10,3,2,26,30,200,'2022-02-04 08:35:00'),(11,3,3,20,25,300,'2022-02-04 08:35:00'),(12,3,4,14,19,400,'2022-02-04 08:35:00'),(13,4,1,31,35,100,'2022-02-04 08:35:00'),(14,4,2,26,30,200,'2022-02-04 08:35:00'),(15,4,3,20,25,300,'2022-02-04 08:35:00'),(16,4,4,14,19,400,'2022-02-04 08:35:00'),(17,5,1,46,50,100,'2022-02-04 08:35:00'),(18,5,2,41,45,200,'2022-02-04 08:35:00'),(19,5,3,31,40,300,'2022-02-04 08:35:00'),(20,5,4,20,30,400,'2022-02-04 08:35:00'),(21,6,1,32,30,100,'2022-02-04 08:35:00'),(22,6,2,29,31,200,'2022-02-04 08:35:00'),(23,6,3,24,28,300,'2022-02-04 08:35:00'),(24,6,4,18,23,400,'2022-02-04 08:35:00'),(25,7,1,32,30,100,'2022-02-04 08:35:00'),(26,7,2,28,31,200,'2022-02-04 08:35:00'),(27,7,3,22,27,300,'2022-02-04 08:35:00'),(28,7,4,15,21,400,'2022-02-04 08:35:00'),(29,8,1,35,36,100,'2022-02-04 08:35:00'),(30,8,2,31,34,200,'2022-02-04 08:35:00'),(31,8,3,26,30,300,'2022-02-04 08:35:00'),(32,8,4,18,25,400,'2022-02-04 08:35:00'),(33,9,1,46,48,100,'2022-02-04 08:35:00'),(34,9,2,41,45,200,'2022-02-04 08:35:00'),(35,9,3,34,40,300,'2022-02-04 08:35:00'),(36,9,4,24,33,400,'2022-02-04 08:35:00'),(37,10,1,35,36,100,'2022-02-04 08:35:00'),(38,10,2,31,34,200,'2022-02-04 08:35:00'),(39,10,3,26,30,300,'2022-02-04 08:35:00'),(40,10,4,18,25,400,'2022-02-04 08:35:00'),(41,11,1,32,30,100,'2022-02-04 08:35:00'),(42,11,2,28,31,200,'2022-02-04 08:35:00'),(43,11,3,22,27,300,'2022-02-04 08:35:00'),(44,11,4,15,21,400,'2022-02-04 08:35:00'),(45,12,1,40,42,100,'2022-02-04 08:35:00'),(46,12,2,37,39,200,'2022-02-04 08:35:00'),(47,12,3,33,36,300,'2022-02-04 08:35:00'),(48,12,4,18,32,400,'2022-02-04 08:35:00'),(49,13,1,29,32,100,'2022-02-04 08:35:00'),(50,13,2,24,28,200,'2022-02-04 08:35:00'),(51,13,3,18,23,300,'2022-02-04 08:35:00'),(52,13,4,12,17,400,'2022-02-04 08:35:00'),(53,14,1,20,21,100,'2022-02-04 08:35:00'),(54,14,2,18,19,200,'2022-02-04 08:35:00'),(55,14,3,14,17,300,'2022-02-04 08:35:00'),(56,14,4,9,13,400,'2022-02-04 08:35:00'),(57,15,1,40,42,100,'2022-02-04 08:35:00'),(58,15,2,37,39,200,'2022-02-04 08:35:00'),(59,15,3,33,36,300,'2022-02-04 08:35:00'),(60,15,4,18,32,400,'2022-02-04 08:35:00');
/*!40000 ALTER TABLE `fish_grade_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fish_info_data`
--

DROP TABLE IF EXISTS `fish_info_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fish_info_data` (
  `fish_code` int(11) NOT NULL AUTO_INCREMENT,
  `fish_name` varchar(100) NOT NULL,
  `min_depth` int(11) NOT NULL,
  `max_depth` int(11) NOT NULL,
  `min_size` int(11) NOT NULL,
  `max_size` int(11) NOT NULL,
  `money_code` int(11) NOT NULL,
  `min_price` int(11) NOT NULL,
  `max_price` int(11) NOT NULL,
  `fish_probability` int(11) NOT NULL,
  `fish_durability` int(11) NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`fish_code`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fish_info_data`
--

LOCK TABLES `fish_info_data` WRITE;
/*!40000 ALTER TABLE `fish_info_data` DISABLE KEYS */;
INSERT INTO `fish_info_data` VALUES (1,'물고기1',20,40,2,4,1,2000,10000,10,3,'2022-02-04 08:19:04'),(2,'물고기2',50,100,2,5,1,2000,10000,8,3,'2022-02-04 08:19:04'),(3,'물고기3',40,80,2,5,1,2000,10000,7,3,'2022-02-04 08:19:04'),(4,'물고기4',60,100,2,5,1,2000,10000,7,3,'2022-02-04 08:19:04'),(5,'물고기5',80,150,2,5,1,2000,10000,10,3,'2022-02-04 08:19:04'),(6,'물고기6',100,300,3,6,1,2000,10000,5,3,'2022-02-04 08:19:04'),(7,'물고기7',120,200,3,6,1,2000,10000,5,3,'2022-02-04 08:19:04'),(8,'물고기8',130,350,3,6,1,2000,10000,6,3,'2022-02-04 08:19:04'),(9,'물고기9',200,650,3,6,1,2000,10000,8,3,'2022-02-04 08:19:04'),(10,'물고기10',350,500,3,6,1,2000,10000,6,3,'2022-02-04 08:19:04'),(11,'물고기11',300,500,3,6,1,2000,10000,5,3,'2022-02-04 08:19:04'),(12,'물고기12',700,1000,3,7,1,2000,10000,6,3,'2022-02-04 08:19:04'),(13,'물고기13',500,900,3,7,1,2000,10000,4,3,'2022-02-04 08:19:04'),(14,'물고기14',600,800,3,7,1,2000,10000,3,3,'2022-02-04 08:19:04'),(15,'물고기15',400,700,3,7,1,2000,10000,6,3,'2022-02-04 08:19:04');
/*!40000 ALTER TABLE `fish_info_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fishing_bait_grade_data`
--

DROP TABLE IF EXISTS `fishing_bait_grade_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fishing_bait_grade_data` (
  `item_grade_code` int(11) NOT NULL AUTO_INCREMENT,
  `item_code` int(11) NOT NULL,
  `grade_code` int(11) NOT NULL,
  `fish_probability` int(11) NOT NULL,
  `money_code` int(11) NOT NULL,
  `item_price` int(11) NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`item_grade_code`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fishing_bait_grade_data`
--

LOCK TABLES `fishing_bait_grade_data` WRITE;
/*!40000 ALTER TABLE `fishing_bait_grade_data` DISABLE KEYS */;
INSERT INTO `fishing_bait_grade_data` VALUES (1,10,5,5,1,100,'2022-02-04 08:52:34'),(2,10,6,10,1,200,'2022-02-04 08:52:34'),(3,10,7,15,2,30,'2022-02-04 08:52:34'),(4,11,5,2,1,100,'2022-02-04 08:52:34'),(5,11,6,5,1,2000,'2022-02-04 08:52:34'),(6,11,7,8,1,10000,'2022-02-04 08:52:34'),(7,12,5,8,2,10,'2022-02-04 08:52:34'),(8,12,6,10,2,20,'2022-02-04 08:52:34'),(9,12,7,13,2,30,'2022-02-04 08:52:34');
/*!40000 ALTER TABLE `fishing_bait_grade_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fishing_item_function_data`
--

DROP TABLE IF EXISTS `fishing_item_function_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fishing_item_function_data` (
  `item_grade_code` int(11) NOT NULL,
  `function_code` int(11) NOT NULL,
  PRIMARY KEY (`item_grade_code`,`function_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fishing_item_function_data`
--

LOCK TABLES `fishing_item_function_data` WRITE;
/*!40000 ALTER TABLE `fishing_item_function_data` DISABLE KEYS */;
/*!40000 ALTER TABLE `fishing_item_function_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fishing_item_info_data`
--

DROP TABLE IF EXISTS `fishing_item_info_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fishing_item_info_data` (
  `item_code` int(11) NOT NULL AUTO_INCREMENT,
  `item_name` varchar(100) NOT NULL,
  `weight` int(11) DEFAULT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`item_code`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fishing_item_info_data`
--

LOCK TABLES `fishing_item_info_data` WRITE;
/*!40000 ALTER TABLE `fishing_item_info_data` DISABLE KEYS */;
INSERT INTO `fishing_item_info_data` VALUES (1,'낚시대1',NULL,'2022-02-04 08:22:28'),(2,'낚시대2',NULL,'2022-02-04 08:22:28'),(3,'낚시대3',NULL,'2022-02-04 08:22:28'),(4,'낚시줄1',NULL,'2022-02-04 08:22:28'),(5,'낚시줄2',NULL,'2022-02-04 08:22:28'),(6,'낚시줄3',NULL,'2022-02-04 08:22:28'),(7,'바늘1',NULL,'2022-02-04 08:22:28'),(8,'바늘2',NULL,'2022-02-04 08:22:28'),(9,'바늘3',NULL,'2022-02-04 08:22:28'),(10,'낚시 미끼1',NULL,'2022-02-04 08:22:28'),(11,'낚시 미끼2',NULL,'2022-02-04 08:22:28'),(12,'낚시 미끼3',NULL,'2022-02-04 08:22:28'),(13,'낚시 릴1',NULL,'2022-02-04 08:22:28'),(14,'낚시 릴2',NULL,'2022-02-04 08:22:28'),(15,'낚시 릴3',NULL,'2022-02-04 08:22:28');
/*!40000 ALTER TABLE `fishing_item_info_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fishing_item_upgrade_data`
--

DROP TABLE IF EXISTS `fishing_item_upgrade_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fishing_item_upgrade_data` (
  `upgrade_code` int(11) NOT NULL AUTO_INCREMENT,
  `item_grade_code` int(11) NOT NULL,
  `item_type` int(11) NOT NULL,
  `upgrade_level` int(11) NOT NULL,
  `upgrade_item_code` int(11) NOT NULL,
  `upgrade_item_count` int(11) NOT NULL,
  `money_code` int(11) NOT NULL,
  `upgrade_price` int(11) NOT NULL,
  `add_probability` int(11) NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`upgrade_code`)
) ENGINE=InnoDB AUTO_INCREMENT=154 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fishing_item_upgrade_data`
--

LOCK TABLES `fishing_item_upgrade_data` WRITE;
/*!40000 ALTER TABLE `fishing_item_upgrade_data` DISABLE KEYS */;
INSERT INTO `fishing_item_upgrade_data` VALUES (1,1,1,1,1,5,1,50,3,'2022-02-04 09:03:00'),(2,1,1,2,1,10,1,100,3,'2022-02-04 09:03:00'),(3,1,1,3,1,15,1,150,3,'2022-02-04 09:03:00'),(4,2,1,1,2,5,1,100,4,'2022-02-04 09:03:00'),(5,2,1,2,2,10,1,150,4,'2022-02-04 09:03:00'),(6,2,1,3,2,15,1,200,4,'2022-02-04 09:03:00'),(7,2,1,4,3,5,1,250,4,'2022-02-04 09:03:00'),(8,2,1,5,3,10,1,300,4,'2022-02-04 09:03:00'),(9,3,1,1,3,15,2,10,5,'2022-02-04 09:03:00'),(10,3,1,2,1,5,2,20,5,'2022-02-04 09:03:00'),(11,3,1,3,1,10,2,30,5,'2022-02-04 09:03:00'),(12,3,1,4,1,15,2,40,5,'2022-02-04 09:03:00'),(13,3,1,5,2,5,2,50,5,'2022-02-04 09:03:00'),(14,3,1,6,2,10,2,60,5,'2022-02-04 09:03:00'),(15,3,1,7,2,15,2,70,5,'2022-02-04 09:03:00'),(16,3,1,8,3,5,2,80,5,'2022-02-04 09:03:00'),(17,3,1,9,3,10,2,100,5,'2022-02-04 09:03:00'),(18,4,1,1,3,15,1,50,3,'2022-02-04 09:03:00'),(19,4,1,2,1,5,1,100,3,'2022-02-04 09:03:00'),(20,4,1,3,1,10,1,150,3,'2022-02-04 09:03:00'),(21,5,1,1,1,15,1,100,4,'2022-02-04 09:03:00'),(22,5,1,2,2,5,1,150,4,'2022-02-04 09:03:00'),(23,5,1,3,2,10,1,200,4,'2022-02-04 09:03:00'),(24,5,1,4,2,15,1,250,4,'2022-02-04 09:03:01'),(25,5,1,5,3,5,1,300,4,'2022-02-04 09:03:01'),(26,6,1,1,3,10,1,150,5,'2022-02-04 09:03:01'),(27,6,1,2,3,15,1,200,5,'2022-02-04 09:03:01'),(28,6,1,3,1,5,1,250,5,'2022-02-04 09:03:01'),(29,6,1,4,1,10,1,300,5,'2022-02-04 09:03:01'),(30,6,1,5,1,15,1,350,5,'2022-02-04 09:03:01'),(31,6,1,6,2,5,1,400,5,'2022-02-04 09:03:01'),(32,6,1,7,2,10,1,450,5,'2022-02-04 09:03:01'),(33,6,1,8,2,15,1,500,5,'2022-02-04 09:03:01'),(34,6,1,9,3,5,1,600,5,'2022-02-04 09:03:01'),(35,7,1,1,3,10,2,5,3,'2022-02-04 09:03:01'),(36,7,1,2,3,15,2,10,3,'2022-02-04 09:03:01'),(37,7,1,3,1,5,2,15,3,'2022-02-04 09:03:01'),(38,8,1,1,1,10,2,10,4,'2022-02-04 09:03:01'),(39,8,1,2,1,15,2,20,4,'2022-02-04 09:03:01'),(40,8,1,3,2,5,2,30,4,'2022-02-04 09:03:01'),(41,8,1,4,2,10,2,40,4,'2022-02-04 09:03:01'),(42,8,1,5,2,15,2,50,4,'2022-02-04 09:03:01'),(43,9,1,1,3,5,2,15,5,'2022-02-04 09:03:01'),(44,9,1,2,3,10,2,30,5,'2022-02-04 09:03:01'),(45,9,1,3,3,15,2,45,5,'2022-02-04 09:03:01'),(46,9,1,4,1,5,2,60,5,'2022-02-04 09:03:01'),(47,9,1,5,1,10,2,75,5,'2022-02-04 09:03:01'),(48,9,1,6,1,15,2,90,5,'2022-02-04 09:03:01'),(49,9,1,7,2,5,2,105,5,'2022-02-04 09:03:01'),(50,9,1,8,2,10,2,120,5,'2022-02-04 09:03:01'),(51,9,1,9,2,15,2,140,5,'2022-02-04 09:03:01'),(52,1,2,1,3,5,1,50,3,'2022-02-04 09:03:01'),(53,1,2,2,3,10,1,100,3,'2022-02-04 09:03:01'),(54,1,2,3,3,15,1,150,3,'2022-02-04 09:03:01'),(55,2,2,1,1,5,1,100,4,'2022-02-04 09:03:01'),(56,2,2,2,1,10,1,150,4,'2022-02-04 09:03:01'),(57,2,2,3,1,15,1,200,4,'2022-02-04 09:03:01'),(58,2,2,4,2,5,1,250,4,'2022-02-04 09:03:01'),(59,2,2,5,2,10,1,300,4,'2022-02-04 09:03:01'),(60,3,2,1,2,15,2,10,5,'2022-02-04 09:03:01'),(61,3,2,2,3,5,2,20,5,'2022-02-04 09:03:01'),(62,3,2,3,3,10,2,30,5,'2022-02-04 09:03:01'),(63,3,2,4,3,15,2,40,5,'2022-02-04 09:03:01'),(64,3,2,5,1,5,2,50,5,'2022-02-04 09:03:01'),(65,3,2,6,1,10,2,60,5,'2022-02-04 09:03:01'),(66,3,2,7,1,15,2,70,5,'2022-02-04 09:03:01'),(67,3,2,8,2,5,2,80,5,'2022-02-04 09:03:01'),(68,3,2,9,2,10,2,100,5,'2022-02-04 09:03:01'),(69,4,2,1,2,15,1,50,3,'2022-02-04 09:03:01'),(70,4,2,2,3,5,1,100,3,'2022-02-04 09:03:01'),(71,4,2,3,3,10,1,150,3,'2022-02-04 09:03:01'),(72,5,2,1,3,15,1,100,4,'2022-02-04 09:03:01'),(73,5,2,2,1,5,1,150,4,'2022-02-04 09:03:01'),(74,5,2,3,1,10,1,200,4,'2022-02-04 09:03:01'),(75,5,2,4,1,15,1,250,4,'2022-02-04 09:03:01'),(76,5,2,5,2,5,1,300,4,'2022-02-04 09:03:01'),(77,6,2,1,2,10,1,150,5,'2022-02-04 09:03:01'),(78,6,2,2,2,15,1,200,5,'2022-02-04 09:03:01'),(79,6,2,3,3,5,1,250,5,'2022-02-04 09:03:01'),(80,6,2,4,3,10,1,300,5,'2022-02-04 09:03:01'),(81,6,2,5,3,15,1,350,5,'2022-02-04 09:03:01'),(82,6,2,6,1,5,1,400,5,'2022-02-04 09:03:01'),(83,6,2,7,1,10,1,450,5,'2022-02-04 09:03:01'),(84,6,2,8,1,15,1,500,5,'2022-02-04 09:03:01'),(85,6,2,9,2,5,1,600,5,'2022-02-04 09:03:01'),(86,7,2,1,2,10,2,5,3,'2022-02-04 09:03:01'),(87,7,2,2,2,15,2,10,3,'2022-02-04 09:03:01'),(88,7,2,3,3,5,2,15,3,'2022-02-04 09:03:01'),(89,8,2,1,3,10,2,10,4,'2022-02-04 09:03:01'),(90,8,2,2,3,15,2,20,4,'2022-02-04 09:03:01'),(91,8,2,3,1,5,2,30,4,'2022-02-04 09:03:01'),(92,8,2,4,1,10,2,40,4,'2022-02-04 09:03:01'),(93,8,2,5,1,15,2,50,4,'2022-02-04 09:03:01'),(94,9,2,1,2,5,2,15,5,'2022-02-04 09:03:01'),(95,9,2,2,2,10,2,30,5,'2022-02-04 09:03:01'),(96,9,2,3,2,15,2,45,5,'2022-02-04 09:03:01'),(97,9,2,4,3,5,2,60,5,'2022-02-04 09:03:01'),(98,9,2,5,3,10,2,75,5,'2022-02-04 09:03:01'),(99,9,2,6,3,15,2,90,5,'2022-02-04 09:03:01'),(100,9,2,7,1,5,2,105,5,'2022-02-04 09:03:01'),(101,9,2,8,1,10,2,120,5,'2022-02-04 09:03:01'),(102,9,2,9,1,15,2,140,5,'2022-02-04 09:03:01'),(103,1,3,1,2,5,1,50,3,'2022-02-04 09:03:01'),(104,1,3,2,2,10,1,100,3,'2022-02-04 09:03:01'),(105,1,3,3,2,15,1,150,3,'2022-02-04 09:03:01'),(106,2,3,1,3,5,1,100,4,'2022-02-04 09:03:01'),(107,2,3,2,3,10,1,150,4,'2022-02-04 09:03:01'),(108,2,3,3,3,15,1,200,4,'2022-02-04 09:03:01'),(109,2,3,4,1,5,1,250,4,'2022-02-04 09:03:01'),(110,2,3,5,1,10,1,300,4,'2022-02-04 09:03:01'),(111,3,3,1,1,15,2,10,5,'2022-02-04 09:03:01'),(112,3,3,2,2,5,2,20,5,'2022-02-04 09:03:01'),(113,3,3,3,2,10,2,30,5,'2022-02-04 09:03:01'),(114,3,3,4,2,15,2,40,5,'2022-02-04 09:03:01'),(115,3,3,5,3,5,2,50,5,'2022-02-04 09:03:01'),(116,3,3,6,3,10,2,60,5,'2022-02-04 09:03:01'),(117,3,3,7,3,15,2,70,5,'2022-02-04 09:03:01'),(118,3,3,8,1,5,2,80,5,'2022-02-04 09:03:01'),(119,3,3,9,1,10,2,100,5,'2022-02-04 09:03:01'),(120,4,3,1,1,15,1,50,3,'2022-02-04 09:03:01'),(121,4,3,2,2,5,1,100,3,'2022-02-04 09:03:01'),(122,4,3,3,2,10,1,150,3,'2022-02-04 09:03:01'),(123,5,3,1,2,15,1,100,4,'2022-02-04 09:03:01'),(124,5,3,2,3,5,1,150,4,'2022-02-04 09:03:01'),(125,5,3,3,3,10,1,200,4,'2022-02-04 09:03:01'),(126,5,3,4,3,15,1,250,4,'2022-02-04 09:03:01'),(127,5,3,5,1,5,1,300,4,'2022-02-04 09:03:01'),(128,6,3,1,1,10,1,150,5,'2022-02-04 09:03:01'),(129,6,3,2,1,15,1,200,5,'2022-02-04 09:03:01'),(130,6,3,3,2,5,1,250,5,'2022-02-04 09:03:01'),(131,6,3,4,2,10,1,300,5,'2022-02-04 09:03:01'),(132,6,3,5,2,15,1,350,5,'2022-02-04 09:03:01'),(133,6,3,6,3,5,1,400,5,'2022-02-04 09:03:01'),(134,6,3,7,3,10,1,450,5,'2022-02-04 09:03:01'),(135,6,3,8,3,15,1,500,5,'2022-02-04 09:03:01'),(136,6,3,9,1,5,1,600,5,'2022-02-04 09:03:01'),(137,7,3,1,1,10,2,5,3,'2022-02-04 09:03:01'),(138,7,3,2,1,15,2,10,3,'2022-02-04 09:03:01'),(139,7,3,3,2,5,2,15,3,'2022-02-04 09:03:01'),(140,8,3,1,2,10,2,10,4,'2022-02-04 09:03:01'),(141,8,3,2,2,15,2,20,4,'2022-02-04 09:03:01'),(142,8,3,3,3,5,2,30,4,'2022-02-04 09:03:01'),(143,8,3,4,3,10,2,40,4,'2022-02-04 09:03:01'),(144,8,3,5,3,15,2,50,4,'2022-02-04 09:03:01'),(145,9,3,1,1,5,2,15,5,'2022-02-04 09:03:01'),(146,9,3,2,1,10,2,30,5,'2022-02-04 09:03:01'),(147,9,3,3,1,15,2,45,5,'2022-02-04 09:03:01'),(148,9,3,4,2,5,2,60,5,'2022-02-04 09:03:01'),(149,9,3,5,2,10,2,75,5,'2022-02-04 09:03:01'),(150,9,3,6,2,15,2,90,5,'2022-02-04 09:03:01'),(151,9,3,7,3,5,2,105,5,'2022-02-04 09:03:01'),(152,9,3,8,3,10,2,120,5,'2022-02-04 09:03:01'),(153,9,3,9,3,15,2,140,5,'2022-02-04 09:03:01');
/*!40000 ALTER TABLE `fishing_item_upgrade_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fishing_line_grade_data`
--

DROP TABLE IF EXISTS `fishing_line_grade_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fishing_line_grade_data` (
  `item_grade_code` int(11) NOT NULL AUTO_INCREMENT,
  `item_code` int(11) NOT NULL,
  `grade_code` int(11) NOT NULL,
  `durability` int(11) NOT NULL,
  `suppress_probability` int(11) NOT NULL,
  `hooking_probability` int(11) NOT NULL,
  `max_weight` int(11) DEFAULT NULL,
  `min_weight` int(11) DEFAULT NULL,
  `money_code` int(11) NOT NULL,
  `item_price` int(11) NOT NULL,
  `max_upgrade` int(11) NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`item_grade_code`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fishing_line_grade_data`
--

LOCK TABLES `fishing_line_grade_data` WRITE;
/*!40000 ALTER TABLE `fishing_line_grade_data` DISABLE KEYS */;
INSERT INTO `fishing_line_grade_data` VALUES (1,4,8,15,5,5,NULL,NULL,1,100,3,'2022-02-04 08:49:57'),(2,4,9,30,10,10,NULL,NULL,1,200,5,'2022-02-04 08:49:57'),(3,4,10,60,15,15,NULL,NULL,2,30,9,'2022-02-04 08:49:57'),(4,5,8,10,2,2,NULL,NULL,1,100,3,'2022-02-04 08:49:57'),(5,5,9,20,5,5,NULL,NULL,1,2000,5,'2022-02-04 08:49:57'),(6,5,10,30,8,8,NULL,NULL,1,10000,9,'2022-02-04 08:49:57'),(7,6,8,12,8,8,NULL,NULL,2,10,3,'2022-02-04 08:49:57'),(8,6,9,24,10,10,NULL,NULL,2,20,5,'2022-02-04 08:49:57'),(9,6,10,48,13,13,NULL,NULL,2,30,9,'2022-02-04 08:49:57');
/*!40000 ALTER TABLE `fishing_line_grade_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fishing_needle_grade_data`
--

DROP TABLE IF EXISTS `fishing_needle_grade_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fishing_needle_grade_data` (
  `item_grade_code` int(11) NOT NULL AUTO_INCREMENT,
  `item_code` int(11) NOT NULL,
  `grade_code` int(11) NOT NULL,
  `suppress_probability` int(11) NOT NULL,
  `hooking_probability` int(11) NOT NULL,
  `money_code` int(11) NOT NULL,
  `item_price` int(11) NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`item_grade_code`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fishing_needle_grade_data`
--

LOCK TABLES `fishing_needle_grade_data` WRITE;
/*!40000 ALTER TABLE `fishing_needle_grade_data` DISABLE KEYS */;
INSERT INTO `fishing_needle_grade_data` VALUES (1,7,8,5,5,1,100,'2022-02-04 08:51:25'),(2,7,9,10,10,1,200,'2022-02-04 08:51:25'),(3,7,10,15,15,2,30,'2022-02-04 08:51:25'),(4,8,8,2,2,1,100,'2022-02-04 08:51:25'),(5,8,9,5,5,1,2000,'2022-02-04 08:51:25'),(6,8,10,8,8,1,10000,'2022-02-04 08:51:25'),(7,9,8,8,8,2,10,'2022-02-04 08:51:25'),(8,9,9,10,10,2,20,'2022-02-04 08:51:25'),(9,9,10,13,13,2,30,'2022-02-04 08:51:25');
/*!40000 ALTER TABLE `fishing_needle_grade_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fishing_reel_grade_data`
--

DROP TABLE IF EXISTS `fishing_reel_grade_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fishing_reel_grade_data` (
  `item_grade_code` int(11) NOT NULL AUTO_INCREMENT,
  `item_code` int(11) NOT NULL,
  `grade_code` int(11) NOT NULL,
  `durability` int(11) NOT NULL,
  `reel_number` int(11) DEFAULT NULL,
  `reel_winding_amount` int(11) DEFAULT NULL,
  `money_code` int(11) NOT NULL,
  `item_price` int(11) NOT NULL,
  `max_upgrade` int(11) NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`item_grade_code`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fishing_reel_grade_data`
--

LOCK TABLES `fishing_reel_grade_data` WRITE;
/*!40000 ALTER TABLE `fishing_reel_grade_data` DISABLE KEYS */;
INSERT INTO `fishing_reel_grade_data` VALUES (1,10,5,15,NULL,NULL,1,1000,3,'2022-02-04 09:00:11'),(2,10,6,30,NULL,NULL,1,2000,5,'2022-02-04 09:00:11'),(3,10,7,60,NULL,NULL,2,300,9,'2022-02-04 09:00:11'),(4,11,5,10,NULL,NULL,1,1000,3,'2022-02-04 09:00:11'),(5,11,6,20,NULL,NULL,1,20000,5,'2022-02-04 09:00:11'),(6,11,7,30,NULL,NULL,1,1000000,9,'2022-02-04 09:00:11'),(7,12,5,12,NULL,NULL,2,100,3,'2022-02-04 09:00:11'),(8,12,6,24,NULL,NULL,2,200,5,'2022-02-04 09:00:11'),(9,12,7,48,NULL,NULL,2,300,9,'2022-02-04 09:00:11');
/*!40000 ALTER TABLE `fishing_reel_grade_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fishing_rod_grade_data`
--

DROP TABLE IF EXISTS `fishing_rod_grade_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fishing_rod_grade_data` (
  `item_grade_code` int(11) NOT NULL AUTO_INCREMENT,
  `item_code` int(11) NOT NULL,
  `item_type` int(11) NOT NULL,
  `grade_code` int(11) NOT NULL,
  `durability` int(11) NOT NULL,
  `suppress_probability` int(11) NOT NULL,
  `hooking_probability` int(11) NOT NULL,
  `max_weight` int(11) DEFAULT NULL,
  `min_weight` int(11) DEFAULT NULL,
  `money_code` int(11) NOT NULL,
  `item_price` int(11) NOT NULL,
  `max_upgrade` int(11) NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`item_grade_code`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fishing_rod_grade_data`
--

LOCK TABLES `fishing_rod_grade_data` WRITE;
/*!40000 ALTER TABLE `fishing_rod_grade_data` DISABLE KEYS */;
INSERT INTO `fishing_rod_grade_data` VALUES (1,1,1,5,15,5,5,NULL,NULL,1,1000,3,'2022-02-04 08:48:43'),(2,1,1,6,30,10,10,NULL,NULL,1,2000,5,'2022-02-04 08:48:43'),(3,1,1,7,60,15,15,NULL,NULL,2,300,9,'2022-02-04 08:48:43'),(4,2,2,5,10,2,2,NULL,NULL,1,1000,3,'2022-02-04 08:48:43'),(5,2,2,6,20,5,5,NULL,NULL,1,20000,5,'2022-02-04 08:48:43'),(6,2,2,7,30,8,8,NULL,NULL,1,1000000,9,'2022-02-04 08:48:43'),(7,3,1,5,12,8,8,NULL,NULL,2,100,3,'2022-02-04 08:48:43'),(8,3,1,6,24,10,10,NULL,NULL,2,200,5,'2022-02-04 08:48:43'),(9,3,1,7,48,13,13,NULL,NULL,2,300,9,'2022-02-04 08:48:43');
/*!40000 ALTER TABLE `fishing_rod_grade_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `function_info_data`
--

DROP TABLE IF EXISTS `function_info_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `function_info_data` (
  `function_code` int(11) NOT NULL AUTO_INCREMENT,
  `function_name` varchar(200) NOT NULL,
  `function_probability` int(11) DEFAULT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`function_code`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `function_info_data`
--

LOCK TABLES `function_info_data` WRITE;
/*!40000 ALTER TABLE `function_info_data` DISABLE KEYS */;
INSERT INTO `function_info_data` VALUES (1,'훅킹 확률 증가',3,'2022-02-04 09:01:22'),(2,'제압 확률 증가',5,'2022-02-04 09:01:22'),(3,'물고기 등장 확률 증가',2,'2022-02-04 09:01:22'),(4,'내구도 소모 감소',2,'2022-02-04 09:01:22');
/*!40000 ALTER TABLE `function_info_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `grade_info_data`
--

DROP TABLE IF EXISTS `grade_info_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `grade_info_data` (
  `grade_code` int(11) NOT NULL AUTO_INCREMENT,
  `grade_name` varchar(100) NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`grade_code`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `grade_info_data`
--

LOCK TABLES `grade_info_data` WRITE;
/*!40000 ALTER TABLE `grade_info_data` DISABLE KEYS */;
INSERT INTO `grade_info_data` VALUES (1,'A','2022-02-04 08:20:31'),(2,'B','2022-02-04 08:20:31'),(3,'C','2022-02-04 08:20:31'),(4,'D','2022-02-04 08:20:31'),(5,'일반','2022-02-04 08:20:31'),(6,'희귀','2022-02-04 08:20:31'),(7,'전설','2022-02-04 08:20:31'),(8,'3호','2022-02-04 08:20:31'),(9,'2호','2022-02-04 08:20:31'),(10,'1호','2022-02-04 08:20:31');
/*!40000 ALTER TABLE `grade_info_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `item_repair_info_data`
--

DROP TABLE IF EXISTS `item_repair_info_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `item_repair_info_data` (
  `repair_code` int(11) NOT NULL AUTO_INCREMENT,
  `item_code` int(11) NOT NULL,
  `item_type` int(11) NOT NULL,
  `money_code` int(11) NOT NULL,
  `repair_price` int(11) NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`repair_code`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `item_repair_info_data`
--

LOCK TABLES `item_repair_info_data` WRITE;
/*!40000 ALTER TABLE `item_repair_info_data` DISABLE KEYS */;
INSERT INTO `item_repair_info_data` VALUES (1,1,1,1,50,'2022-02-04 09:05:33'),(2,2,1,1,100,'2022-02-04 09:05:33'),(3,3,1,1,150,'2022-02-04 09:05:33'),(4,4,1,1,200,'2022-02-04 09:05:33'),(5,5,1,1,250,'2022-02-04 09:05:33'),(6,6,1,1,300,'2022-02-04 09:05:33'),(7,7,1,1,350,'2022-02-04 09:05:33'),(8,8,1,1,400,'2022-02-04 09:05:33'),(9,9,1,1,450,'2022-02-04 09:05:33'),(10,1,2,1,50,'2022-02-04 09:05:33'),(11,2,2,1,100,'2022-02-04 09:05:33'),(12,3,2,1,150,'2022-02-04 09:05:33'),(13,4,2,1,200,'2022-02-04 09:05:33'),(14,5,2,1,250,'2022-02-04 09:05:33'),(15,6,2,1,300,'2022-02-04 09:05:33'),(16,7,2,1,350,'2022-02-04 09:05:33'),(17,8,2,1,400,'2022-02-04 09:05:33'),(18,9,2,1,450,'2022-02-04 09:05:33'),(19,1,3,1,50,'2022-02-04 09:05:33'),(20,2,3,1,100,'2022-02-04 09:05:33'),(21,3,3,1,150,'2022-02-04 09:05:33'),(22,4,3,1,200,'2022-02-04 09:05:33'),(23,5,3,1,250,'2022-02-04 09:05:33'),(24,6,3,1,300,'2022-02-04 09:05:33'),(25,7,3,1,350,'2022-02-04 09:05:33'),(26,8,3,1,400,'2022-02-04 09:05:33'),(27,9,3,1,450,'2022-02-04 09:05:33'),(28,1,4,1,200,'2022-02-04 09:05:33');
/*!40000 ALTER TABLE `item_repair_info_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `language_info_data`
--

DROP TABLE IF EXISTS `language_info_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `language_info_data` (
  `language_code` int(11) NOT NULL AUTO_INCREMENT,
  `language_name` varchar(100) NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`language_code`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `language_info_data`
--

LOCK TABLES `language_info_data` WRITE;
/*!40000 ALTER TABLE `language_info_data` DISABLE KEYS */;
INSERT INTO `language_info_data` VALUES (1,'한국어','2022-02-04 08:40:37'),(2,'영어','2022-02-04 08:40:37');
/*!40000 ALTER TABLE `language_info_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `map_fish_data`
--

DROP TABLE IF EXISTS `map_fish_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `map_fish_data` (
  `map_fish_code` int(11) NOT NULL AUTO_INCREMENT,
  `map_code` int(11) NOT NULL,
  `fish_code` int(11) NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`map_fish_code`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `map_fish_data`
--

LOCK TABLES `map_fish_data` WRITE;
/*!40000 ALTER TABLE `map_fish_data` DISABLE KEYS */;
INSERT INTO `map_fish_data` VALUES (1,1,1,'2022-02-04 08:33:14'),(2,1,2,'2022-02-04 08:33:14'),(3,1,3,'2022-02-04 08:33:14'),(4,1,4,'2022-02-04 08:33:14'),(5,1,5,'2022-02-04 08:33:14'),(6,2,5,'2022-02-04 08:33:14'),(7,2,6,'2022-02-04 08:33:14'),(8,2,7,'2022-02-04 08:33:14'),(9,2,8,'2022-02-04 08:33:14'),(10,2,9,'2022-02-04 08:33:14'),(11,3,6,'2022-02-04 08:33:14'),(12,3,8,'2022-02-04 08:33:14'),(13,3,9,'2022-02-04 08:33:14'),(14,3,10,'2022-02-04 08:33:14'),(15,3,11,'2022-02-04 08:33:14'),(16,4,12,'2022-02-04 08:33:14'),(17,4,13,'2022-02-04 08:33:14'),(18,4,14,'2022-02-04 08:33:14'),(19,4,15,'2022-02-04 08:33:14'),(20,4,9,'2022-02-04 08:33:14');
/*!40000 ALTER TABLE `map_fish_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `map_info_data`
--

DROP TABLE IF EXISTS `map_info_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `map_info_data` (
  `map_code` int(11) NOT NULL AUTO_INCREMENT,
  `map_name` varchar(100) NOT NULL,
  `max_depth` int(11) NOT NULL,
  `min_level` int(11) NOT NULL,
  `distance` int(11) NOT NULL,
  `money_code` int(11) NOT NULL,
  `departure_price` int(11) NOT NULL,
  `departure_time` int(11) NOT NULL,
  `per_fiatigue` int(11) NOT NULL,
  `map_fish_count` int(11) NOT NULL,
  `fish_size_probability` int(11) NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`map_code`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `map_info_data`
--

LOCK TABLES `map_info_data` WRITE;
/*!40000 ALTER TABLE `map_info_data` DISABLE KEYS */;
INSERT INTO `map_info_data` VALUES (1,'얕은연안1',100,5,100,1,100,5,5,5,5,'2022-02-04 08:12:48'),(2,'깊은연안1',300,10,200,2,200,10,10,5,5,'2022-02-04 08:12:48'),(3,'근거리바다1',500,20,300,1,10,15,15,5,15,'2022-02-04 08:12:48'),(4,'먼바다1',1000,30,400,2,30,20,20,5,20,'2022-02-04 08:12:48');
/*!40000 ALTER TABLE `map_info_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `map_item_data`
--

DROP TABLE IF EXISTS `map_item_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `map_item_data` (
  `map_item_code` int(11) NOT NULL AUTO_INCREMENT,
  `map_code` int(11) NOT NULL,
  `item_code` int(11) NOT NULL,
  `item_type` int(11) NOT NULL,
  `item_probability` int(11) NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`map_item_code`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `map_item_data`
--

LOCK TABLES `map_item_data` WRITE;
/*!40000 ALTER TABLE `map_item_data` DISABLE KEYS */;
INSERT INTO `map_item_data` VALUES (1,1,1,1,5,'2022-02-04 08:44:11'),(2,1,1,2,4,'2022-02-04 08:44:11'),(3,1,1,3,3,'2022-02-04 08:44:11'),(4,1,1,4,2,'2022-02-04 08:44:11'),(5,1,1,5,1,'2022-02-04 08:44:11'),(6,2,1,1,4,'2022-02-04 08:44:11'),(7,2,1,2,5,'2022-02-04 08:44:11'),(8,2,1,3,3,'2022-02-04 08:44:11'),(9,2,1,4,10,'2022-02-04 08:44:11'),(10,2,1,5,7,'2022-02-04 08:44:11'),(11,3,1,1,5,'2022-02-04 08:44:11'),(12,3,1,2,4,'2022-02-04 08:44:11'),(13,3,1,3,3,'2022-02-04 08:44:11'),(14,3,1,4,2,'2022-02-04 08:44:11'),(15,3,1,5,1,'2022-02-04 08:44:11'),(16,4,1,1,4,'2022-02-04 08:44:11'),(17,4,1,2,5,'2022-02-04 08:44:11'),(18,4,1,3,3,'2022-02-04 08:44:11'),(19,4,1,4,10,'2022-02-04 08:44:11'),(20,4,1,5,7,'2022-02-04 08:44:11');
/*!40000 ALTER TABLE `map_item_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `map_tide_data`
--

DROP TABLE IF EXISTS `map_tide_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `map_tide_data` (
  `map_tide_code` int(11) NOT NULL,
  `map_code` int(11) NOT NULL,
  `tide_code` int(11) NOT NULL,
  `tide_sort` int(11) NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`map_tide_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `map_tide_data`
--

LOCK TABLES `map_tide_data` WRITE;
/*!40000 ALTER TABLE `map_tide_data` DISABLE KEYS */;
INSERT INTO `map_tide_data` VALUES (1,1,1,1,'2022-02-04 08:14:27'),(2,1,2,2,'2022-02-04 08:14:27'),(3,1,3,3,'2022-02-04 08:14:27'),(4,1,4,4,'2022-02-04 08:14:27'),(5,1,5,5,'2022-02-04 08:14:27'),(6,1,6,6,'2022-02-04 08:14:27'),(7,1,7,7,'2022-02-04 08:14:27'),(8,2,5,1,'2022-02-04 08:14:27'),(9,2,6,2,'2022-02-04 08:14:27'),(10,2,7,3,'2022-02-04 08:14:27'),(11,2,1,4,'2022-02-04 08:14:27'),(12,2,2,5,'2022-02-04 08:14:27'),(13,2,3,6,'2022-02-04 08:14:27'),(14,2,4,7,'2022-02-04 08:14:27'),(15,3,4,1,'2022-02-04 08:14:27'),(16,3,5,2,'2022-02-04 08:14:27'),(17,3,6,3,'2022-02-04 08:14:27'),(18,3,7,4,'2022-02-04 08:14:27'),(19,3,1,5,'2022-02-04 08:14:27'),(20,3,2,6,'2022-02-04 08:14:27'),(21,3,3,7,'2022-02-04 08:14:27'),(22,4,6,1,'2022-02-04 08:14:27'),(23,4,7,2,'2022-02-04 08:14:27'),(24,4,1,3,'2022-02-04 08:14:27'),(25,4,2,4,'2022-02-04 08:14:27'),(26,4,3,5,'2022-02-04 08:14:27'),(27,4,4,6,'2022-02-04 08:14:27'),(28,4,5,7,'2022-02-04 08:14:27');
/*!40000 ALTER TABLE `map_tide_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `money_info_data`
--

DROP TABLE IF EXISTS `money_info_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `money_info_data` (
  `money_code` int(11) NOT NULL AUTO_INCREMENT,
  `money_name` varchar(100) NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`money_code`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `money_info_data`
--

LOCK TABLES `money_info_data` WRITE;
/*!40000 ALTER TABLE `money_info_data` DISABLE KEYS */;
INSERT INTO `money_info_data` VALUES (1,'골드','2022-02-04 08:37:43'),(2,'진주','2022-02-04 08:37:43');
/*!40000 ALTER TABLE `money_info_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `quest_compensation_data`
--

DROP TABLE IF EXISTS `quest_compensation_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `quest_compensation_data` (
  `quest_code` int(11) NOT NULL,
  `compensation_code` int(11) NOT NULL,
  PRIMARY KEY (`quest_code`,`compensation_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `quest_compensation_data`
--

LOCK TABLES `quest_compensation_data` WRITE;
/*!40000 ALTER TABLE `quest_compensation_data` DISABLE KEYS */;
INSERT INTO `quest_compensation_data` VALUES (1,1),(1,9),(1,18),(2,2),(2,10),(2,19),(3,3),(3,11),(3,20),(4,4),(4,12),(4,21),(5,5),(5,13),(5,22),(6,6),(6,14),(6,23),(7,7),(7,15),(7,24),(8,1),(8,9),(8,18),(9,2),(9,10),(9,19),(10,3),(10,11),(10,20),(11,4),(11,12),(11,21);
/*!40000 ALTER TABLE `quest_compensation_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `quest_info_data`
--

DROP TABLE IF EXISTS `quest_info_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `quest_info_data` (
  `quest_code` int(11) NOT NULL AUTO_INCREMENT,
  `quest_type` int(11) NOT NULL,
  `quest_goal` int(11) NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`quest_code`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `quest_info_data`
--

LOCK TABLES `quest_info_data` WRITE;
/*!40000 ALTER TABLE `quest_info_data` DISABLE KEYS */;
INSERT INTO `quest_info_data` VALUES (1,1,1,'2022-02-04 09:58:01'),(2,1,2,'2022-02-04 09:58:01'),(3,1,3,'2022-02-04 09:58:01'),(4,1,4,'2022-02-04 09:58:01'),(5,1,5,'2022-02-04 09:58:01'),(6,1,6,'2022-02-04 09:58:01'),(7,1,7,'2022-02-04 09:58:01'),(8,2,1,'2022-02-04 09:58:01'),(9,2,2,'2022-02-04 09:58:01'),(10,2,3,'2022-02-04 09:58:01'),(11,2,4,'2022-02-04 09:58:01');
/*!40000 ALTER TABLE `quest_info_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ship_info_data`
--

DROP TABLE IF EXISTS `ship_info_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ship_info_data` (
  `ship_code` int(11) NOT NULL AUTO_INCREMENT,
  `ship_name` varchar(100) NOT NULL,
  `durability` int(11) NOT NULL,
  `fuel` int(11) NOT NULL,
  `max_upgrade` int(11) NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ship_code`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ship_info_data`
--

LOCK TABLES `ship_info_data` WRITE;
/*!40000 ALTER TABLE `ship_info_data` DISABLE KEYS */;
INSERT INTO `ship_info_data` VALUES (1,'보로롱24호',1000,500,10,'2022-02-04 08:47:35');
/*!40000 ALTER TABLE `ship_info_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ship_item_upgrade_data`
--

DROP TABLE IF EXISTS `ship_item_upgrade_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ship_item_upgrade_data` (
  `upgrade_code` int(11) NOT NULL AUTO_INCREMENT,
  `ship_code` int(11) NOT NULL,
  `upgrade_level` int(11) NOT NULL,
  `money_code` int(11) NOT NULL,
  `upgrade_price` int(11) NOT NULL,
  `add_fuel` int(11) NOT NULL,
  `add_probability` int(11) NOT NULL,
  `upgrad_probability` int(11) NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`upgrade_code`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ship_item_upgrade_data`
--

LOCK TABLES `ship_item_upgrade_data` WRITE;
/*!40000 ALTER TABLE `ship_item_upgrade_data` DISABLE KEYS */;
INSERT INTO `ship_item_upgrade_data` VALUES (1,1,1,1,1000,1,1,10,'2022-02-04 09:04:23'),(2,1,2,1,2000,2,2,9,'2022-02-04 09:04:23'),(3,1,3,1,3000,3,3,8,'2022-02-04 09:04:23'),(4,1,4,1,4000,4,4,7,'2022-02-04 09:04:23'),(5,1,5,1,5000,5,5,6,'2022-02-04 09:04:23'),(6,1,6,1,6000,6,6,5,'2022-02-04 09:04:23'),(7,1,7,1,7000,7,7,4,'2022-02-04 09:04:23'),(8,1,8,1,8000,8,8,3,'2022-02-04 09:04:23'),(9,1,9,1,9000,9,9,2,'2022-02-04 09:04:23'),(10,1,10,1,10000,10,10,1,'2022-02-04 09:04:23');
/*!40000 ALTER TABLE `ship_item_upgrade_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shop_info_data`
--

DROP TABLE IF EXISTS `shop_info_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `shop_info_data` (
  `shop_code` int(11) NOT NULL AUTO_INCREMENT,
  `item_code` int(11) NOT NULL,
  `item_type` int(11) NOT NULL,
  `sale_percent` int(11) DEFAULT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`shop_code`)
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop_info_data`
--

LOCK TABLES `shop_info_data` WRITE;
/*!40000 ALTER TABLE `shop_info_data` DISABLE KEYS */;
INSERT INTO `shop_info_data` VALUES (1,1,1,NULL,'2022-02-04 10:02:30'),(2,2,1,NULL,'2022-02-04 10:02:30'),(3,3,1,NULL,'2022-02-04 10:02:30'),(4,4,1,NULL,'2022-02-04 10:02:30'),(5,5,1,NULL,'2022-02-04 10:02:30'),(6,6,1,NULL,'2022-02-04 10:02:30'),(7,7,1,NULL,'2022-02-04 10:02:30'),(8,8,1,NULL,'2022-02-04 10:02:30'),(9,9,1,NULL,'2022-02-04 10:02:30'),(10,1,2,NULL,'2022-02-04 10:02:30'),(11,2,2,NULL,'2022-02-04 10:02:30'),(12,3,2,NULL,'2022-02-04 10:02:30'),(13,4,2,NULL,'2022-02-04 10:02:30'),(14,5,2,NULL,'2022-02-04 10:02:30'),(15,6,2,NULL,'2022-02-04 10:02:30'),(16,7,2,NULL,'2022-02-04 10:02:30'),(17,8,2,NULL,'2022-02-04 10:02:30'),(18,9,2,NULL,'2022-02-04 10:02:30'),(19,1,3,NULL,'2022-02-04 10:02:30'),(20,2,3,NULL,'2022-02-04 10:02:30'),(21,3,3,NULL,'2022-02-04 10:02:30'),(22,4,3,NULL,'2022-02-04 10:02:30'),(23,5,3,NULL,'2022-02-04 10:02:30'),(24,6,3,NULL,'2022-02-04 10:02:30'),(25,7,3,NULL,'2022-02-04 10:02:30'),(26,8,3,NULL,'2022-02-04 10:02:30'),(27,9,3,NULL,'2022-02-04 10:02:30'),(28,1,4,NULL,'2022-02-04 10:02:30'),(29,2,4,NULL,'2022-02-04 10:02:30'),(30,3,4,NULL,'2022-02-04 10:02:30'),(31,4,4,NULL,'2022-02-04 10:02:30'),(32,5,4,NULL,'2022-02-04 10:02:30'),(33,6,4,NULL,'2022-02-04 10:02:30'),(34,7,4,NULL,'2022-02-04 10:02:30'),(35,8,4,NULL,'2022-02-04 10:02:30'),(36,9,4,NULL,'2022-02-04 10:02:30'),(37,1,5,NULL,'2022-02-04 10:02:30'),(38,2,5,NULL,'2022-02-04 10:02:30'),(39,3,5,NULL,'2022-02-04 10:02:30'),(40,4,5,NULL,'2022-02-04 10:02:30'),(41,5,5,NULL,'2022-02-04 10:02:30'),(42,6,5,NULL,'2022-02-04 10:02:30'),(43,7,5,NULL,'2022-02-04 10:02:30'),(44,8,5,NULL,'2022-02-04 10:02:30'),(45,9,5,NULL,'2022-02-04 10:02:30'),(46,1,6,NULL,'2022-02-04 10:02:30'),(47,2,6,NULL,'2022-02-04 10:02:30'),(48,3,6,NULL,'2022-02-04 10:02:30');
/*!40000 ALTER TABLE `shop_info_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `temperature_info_data`
--

DROP TABLE IF EXISTS `temperature_info_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `temperature_info_data` (
  `temperature_code` int(11) NOT NULL AUTO_INCREMENT,
  `min_temperature` int(11) NOT NULL,
  `max_temperature` int(11) NOT NULL,
  `change_time` int(11) NOT NULL,
  `change_value` int(11) NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`temperature_code`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `temperature_info_data`
--

LOCK TABLES `temperature_info_data` WRITE;
/*!40000 ALTER TABLE `temperature_info_data` DISABLE KEYS */;
INSERT INTO `temperature_info_data` VALUES (1,5,15,2,5,'2022-02-04 07:41:49'),(2,8,16,1,5,'2022-02-04 07:41:49'),(3,16,26,3,5,'2022-02-04 07:41:49'),(4,18,25,2,5,'2022-02-04 07:41:49');
/*!40000 ALTER TABLE `temperature_info_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tide_info_data`
--

DROP TABLE IF EXISTS `tide_info_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tide_info_data` (
  `tide_code` int(11) NOT NULL AUTO_INCREMENT,
  `high_tide_time1` time DEFAULT NULL,
  `low_tide_time1` time DEFAULT NULL,
  `high_tide_time2` time DEFAULT NULL,
  `low_tide_time2` time DEFAULT NULL,
  `water_splash_time` int(11) NOT NULL,
  `appear_probability` int(11) NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`tide_code`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tide_info_data`
--

LOCK TABLES `tide_info_data` WRITE;
/*!40000 ALTER TABLE `tide_info_data` DISABLE KEYS */;
INSERT INTO `tide_info_data` VALUES (1,'00:20:00','05:30:00','11:40:00','18:10:00',2,10,'2022-02-04 08:11:00'),(2,'01:20:00','06:30:00','12:40:00','19:10:00',2,20,'2022-02-04 08:11:00'),(3,'02:20:00','07:30:00','13:40:00','20:10:00',2,10,'2022-02-04 08:11:00'),(4,'03:20:00','08:30:00','14:40:00','21:10:00',2,30,'2022-02-04 08:11:00'),(5,'04:20:00','09:30:00','15:40:00','22:10:00',2,10,'2022-02-04 08:11:00'),(6,'05:20:00','10:30:00','16:40:00','23:10:00',2,20,'2022-02-04 08:11:00'),(7,'06:20:00','11:30:00','17:40:00','00:10:00',2,10,'2022-02-04 08:11:00');
/*!40000 ALTER TABLE `tide_info_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `upgrade_item_data`
--

DROP TABLE IF EXISTS `upgrade_item_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `upgrade_item_data` (
  `upgrade_item_code` int(11) NOT NULL AUTO_INCREMENT,
  `upgrade_item_name` varchar(100) NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`upgrade_item_code`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `upgrade_item_data`
--

LOCK TABLES `upgrade_item_data` WRITE;
/*!40000 ALTER TABLE `upgrade_item_data` DISABLE KEYS */;
INSERT INTO `upgrade_item_data` VALUES (1,'부품1','2022-02-04 08:42:01'),(2,'부품2','2022-02-04 08:42:01'),(3,'부품3','2022-02-04 08:42:01');
/*!40000 ALTER TABLE `upgrade_item_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_choice_item_info`
--

DROP TABLE IF EXISTS `user_choice_item_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_choice_item_info` (
  `choice_code` int(11) NOT NULL AUTO_INCREMENT,
  `user_code` int(11) NOT NULL,
  `fishing_rod_code` int(11) NOT NULL,
  `fishing_line_code` int(11) NOT NULL,
  `fishing_needle_code` int(11) NOT NULL,
  `fishing_bait_code` int(11) NOT NULL,
  `fishing_reel_code` int(11) NOT NULL,
  `fishing_item_code1` int(11) DEFAULT NULL,
  `fishing_item_code2` int(11) DEFAULT NULL,
  `fishing_item_code3` int(11) DEFAULT NULL,
  `fishing_item_code4` int(11) DEFAULT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`choice_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_choice_item_info`
--

LOCK TABLES `user_choice_item_info` WRITE;
/*!40000 ALTER TABLE `user_choice_item_info` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_choice_item_info` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_fish_dictionary`
--

DROP TABLE IF EXISTS `user_fish_dictionary`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_fish_dictionary` (
  `map_fish_code` int(11) NOT NULL,
  `user_code` int(11) NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`map_fish_code`,`user_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_fish_dictionary`
--

LOCK TABLES `user_fish_dictionary` WRITE;
/*!40000 ALTER TABLE `user_fish_dictionary` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_fish_dictionary` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_gitf_box_info`
--

DROP TABLE IF EXISTS `user_gitf_box_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_gitf_box_info` (
  `box_code` int(11) NOT NULL AUTO_INCREMENT,
  `item_code` int(11) NOT NULL,
  `item_type` int(11) NOT NULL,
  `item_count` int(11) NOT NULL,
  `read_status` int(11) NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`box_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_gitf_box_info`
--

LOCK TABLES `user_gitf_box_info` WRITE;
/*!40000 ALTER TABLE `user_gitf_box_info` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_gitf_box_info` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_info`
--

DROP TABLE IF EXISTS `user_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_info` (
  `user_code` int(11) NOT NULL AUTO_INCREMENT,
  `account_code` int(11) NOT NULL,
  `user_nicknm` varchar(100) NOT NULL,
  `level_code` int(11) NOT NULL,
  `user_experience` int(11) NOT NULL,
  `money_gold` int(11) NOT NULL,
  `money_pearl` int(11) NOT NULL,
  `fatigue` int(11) NOT NULL,
  `use_inventory_count` int(11) NOT NULL,
  `use_save_item_count` int(11) NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_info`
--

LOCK TABLES `user_info` WRITE;
/*!40000 ALTER TABLE `user_info` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_info` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_inventory_info`
--

DROP TABLE IF EXISTS `user_inventory_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_inventory_info` (
  `inventory_code` int(11) NOT NULL AUTO_INCREMENT,
  `user_code` int(11) NOT NULL,
  `item_code` int(11) NOT NULL,
  `item_type` int(11) NOT NULL,
  `upgrade_code` int(11) DEFAULT NULL,
  `upgrade_level` int(11) DEFAULT NULL,
  `item_count` int(11) NOT NULL,
  `item_durability` int(11) NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`inventory_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_inventory_info`
--

LOCK TABLES `user_inventory_info` WRITE;
/*!40000 ALTER TABLE `user_inventory_info` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_inventory_info` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_level_info_data`
--

DROP TABLE IF EXISTS `user_level_info_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_level_info_data` (
  `level_code` int(11) NOT NULL AUTO_INCREMENT,
  `level_experience` int(11) NOT NULL,
  `max_fatigue` int(11) NOT NULL,
  `auction_profit` int(11) NOT NULL,
  `inventory_count` int(11) NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`level_code`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_level_info_data`
--

LOCK TABLES `user_level_info_data` WRITE;
/*!40000 ALTER TABLE `user_level_info_data` DISABLE KEYS */;
INSERT INTO `user_level_info_data` VALUES (1,1000,100,3,20,'2022-02-04 10:00:50'),(2,10000,200,4,50,'2022-02-04 10:00:50'),(3,100000,300,5,80,'2022-02-04 10:00:50'),(4,1000000,400,6,110,'2022-02-04 10:00:50'),(5,10000000,500,7,140,'2022-02-04 10:00:50'),(6,10000000,600,8,170,'2022-02-04 10:00:50'),(7,10000000,700,9,200,'2022-02-04 10:00:50');
/*!40000 ALTER TABLE `user_level_info_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_ship_info`
--

DROP TABLE IF EXISTS `user_ship_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_ship_info` (
  `user_code` int(11) NOT NULL,
  `ship_code` int(11) NOT NULL,
  `durability` int(11) NOT NULL,
  `fuel` int(11) NOT NULL,
  `upgrade_code` int(11) NOT NULL,
  `upgrade_level` int(11) NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_code`,`ship_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_ship_info`
--

LOCK TABLES `user_ship_info` WRITE;
/*!40000 ALTER TABLE `user_ship_info` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_ship_info` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_weather_history`
--

DROP TABLE IF EXISTS `user_weather_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_weather_history` (
  `weather_history_code` int(11) NOT NULL AUTO_INCREMENT,
  `user_code` int(11) NOT NULL,
  `weather_code` int(11) NOT NULL,
  `map_wave_code` int(11) NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`weather_history_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_weather_history`
--

LOCK TABLES `user_weather_history` WRITE;
/*!40000 ALTER TABLE `user_weather_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_weather_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `weather_info_data`
--

DROP TABLE IF EXISTS `weather_info_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `weather_info_data` (
  `weather_code` int(11) NOT NULL AUTO_INCREMENT,
  `temperature_code` int(11) NOT NULL,
  `wind_code` int(11) NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`weather_code`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `weather_info_data`
--

LOCK TABLES `weather_info_data` WRITE;
/*!40000 ALTER TABLE `weather_info_data` DISABLE KEYS */;
INSERT INTO `weather_info_data` VALUES (1,1,1,'2022-02-04 07:38:44'),(2,1,2,'2022-02-04 07:38:44'),(3,2,1,'2022-02-04 07:38:44'),(4,2,2,'2022-02-04 07:38:44'),(5,3,1,'2022-02-04 07:38:44'),(6,3,2,'2022-02-04 07:38:44'),(7,4,1,'2022-02-04 07:38:44'),(8,4,2,'2022-02-04 07:38:44');
/*!40000 ALTER TABLE `weather_info_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wind_info_data`
--

DROP TABLE IF EXISTS `wind_info_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wind_info_data` (
  `wind_code` int(11) NOT NULL AUTO_INCREMENT,
  `min_wind` int(11) NOT NULL,
  `max_wind` int(11) NOT NULL,
  `change_time` int(11) NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`wind_code`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wind_info_data`
--

LOCK TABLES `wind_info_data` WRITE;
/*!40000 ALTER TABLE `wind_info_data` DISABLE KEYS */;
INSERT INTO `wind_info_data` VALUES (1,5,11,2,'2022-02-04 08:02:57'),(2,7,13,2,'2022-02-04 08:02:57');
/*!40000 ALTER TABLE `wind_info_data` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2022-02-07 10:38:52
