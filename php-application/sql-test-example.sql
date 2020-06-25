-- MySQL dump 10.13  Distrib 5.6.47, for Linux (x86_64)
--
-- Host: localhost    Database: scholarship
-- ------------------------------------------------------
-- Server version	5.6.47-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `Academy_info`
--

DROP TABLE IF EXISTS `Academy_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Academy_info` (
  `Academy_id` int(11) NOT NULL,
  `Academy_Name` varchar(20) NOT NULL,
  PRIMARY KEY (`Academy_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Academy_info`
--

LOCK TABLES `Academy_info` WRITE;
/*!40000 ALTER TABLE `Academy_info` DISABLE KEYS */;
INSERT INTO `Academy_info` VALUES (193,'计算机学院');
/*!40000 ALTER TABLE `Academy_info` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Announce_info`
--

DROP TABLE IF EXISTS `Announce_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Announce_info` (
  `Announce_id` int(11) NOT NULL AUTO_INCREMENT,
  `Announce_head` varchar(20) NOT NULL,
  `Announce_data` varchar(1000) NOT NULL,
  PRIMARY KEY (`Announce_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Announce_info`
--

LOCK TABLES `Announce_info` WRITE;
/*!40000 ALTER TABLE `Announce_info` DISABLE KEYS */;
INSERT INTO `Announce_info` VALUES (1,'新公告','测试测试测试');
/*!40000 ALTER TABLE `Announce_info` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Apply_date`
--

DROP TABLE IF EXISTS `Apply_date`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Apply_date` (
  `Apply_id` int(11) NOT NULL,
  `Apply_type` varchar(20) NOT NULL,
  `Apply_reason` varchar(1000) NOT NULL,
  `Stu_id` int(11) NOT NULL,
  `Audit_id` int(11) NOT NULL,
  PRIMARY KEY (`Apply_id`),
  KEY `Stu_id` (`Stu_id`,`Audit_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Apply_date`
--

LOCK TABLES `Apply_date` WRITE;
/*!40000 ALTER TABLE `Apply_date` DISABLE KEYS */;
INSERT INTO `Apply_date` VALUES (0,'1','测试',1,0);
/*!40000 ALTER TABLE `Apply_date` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Audit_date`
--

DROP TABLE IF EXISTS `Audit_date`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Audit_date` (
  `Audit_id` int(11) NOT NULL,
  `Apply_id` int(11) DEFAULT NULL,
  `First_result` int(11) DEFAULT NULL,
  `First_reason` varchar(100) DEFAULT NULL,
  `Second_result` int(11) DEFAULT NULL,
  `Second_reason` varchar(100) DEFAULT NULL,
  `Third_result` int(11) DEFAULT NULL,
  `Third_reason` varchar(100) DEFAULT NULL,
  `Next_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Audit_id`,`Next_id`),
  KEY `Apply_id` (`Apply_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Audit_date`
--

LOCK TABLES `Audit_date` WRITE;
/*!40000 ALTER TABLE `Audit_date` DISABLE KEYS */;
/*!40000 ALTER TABLE `Audit_date` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Auditor_info`
--

DROP TABLE IF EXISTS `Auditor_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Auditor_info` (
  `Auditor_id` int(11) NOT NULL,
  `Auditor_name` varchar(20) NOT NULL,
  `User_id` int(11) NOT NULL,
  `Belonging_type` int(11) NOT NULL,
  `Belonging_info` int(11) DEFAULT NULL,
  PRIMARY KEY (`Auditor_id`),
  KEY `User_id` (`User_id`),
  KEY `User_id_2` (`User_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Auditor_info`
--

LOCK TABLES `Auditor_info` WRITE;
/*!40000 ALTER TABLE `Auditor_info` DISABLE KEYS */;
/*!40000 ALTER TABLE `Auditor_info` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Stu_info`
--

DROP TABLE IF EXISTS `Stu_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Stu_info` (
  `Stu_id` int(11) NOT NULL DEFAULT '0',
  `Stu_Name` varchar(20) NOT NULL,
  `User_id` int(11) NOT NULL,
  `Class_id` int(11) DEFAULT NULL,
  `Academy_id` int(11) DEFAULT NULL,
  `Stu_score` int(11) DEFAULT NULL,
  `Sex` int(11) DEFAULT NULL,
  `Bank_account` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`Stu_id`),
  KEY `Stu_info_ibfk_1` (`User_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Stu_info`
--

LOCK TABLES `Stu_info` WRITE;
/*!40000 ALTER TABLE `Stu_info` DISABLE KEYS */;
/*!40000 ALTER TABLE `Stu_info` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `User_info`
--

DROP TABLE IF EXISTS `User_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `User_info` (
  `User_id` int(11) NOT NULL AUTO_INCREMENT,
  `User_name` varchar(20) NOT NULL,
  `User_password` varchar(20) NOT NULL,
  `User_type` int(11) NOT NULL,
  `User_personal_identify_id` varchar(20) NOT NULL,
  PRIMARY KEY (`User_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2147483647 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `User_info`
--

LOCK TABLES `User_info` WRITE;
/*!40000 ALTER TABLE `User_info` DISABLE KEYS */;
INSERT INTO `User_info` VALUES (1,'管理员','123456',0,'123123197011111234');
/*!40000 ALTER TABLE `User_info` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `class_info`
--

DROP TABLE IF EXISTS `class_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `class_info` (
  `Class_id` int(11) NOT NULL,
  `Academy_id` int(11) NOT NULL,
  PRIMARY KEY (`Class_id`),
  KEY `Academy_id` (`Academy_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `class_info`
--

LOCK TABLES `class_info` WRITE;
/*!40000 ALTER TABLE `class_info` DISABLE KEYS */;
INSERT INTO `class_info` VALUES (193181,193),(193182,193);
/*!40000 ALTER TABLE `class_info` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2020-06-10 10:16:52
