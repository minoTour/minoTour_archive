-- MySQL dump 10.13  Distrib 5.6.19, for osx10.7 (x86_64)
--
-- Host: localhost    Database: Gru
-- ------------------------------------------------------
-- Server version	5.6.19

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
-- Table structure for table `Comments_orig`
--

DROP TABLE IF EXISTS `Comments_orig`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Comments_orig` (
  `index` int(11) NOT NULL AUTO_INCREMENT,
  `runname` text NOT NULL,
  `user` text NOT NULL,
  `date` datetime NOT NULL,
  `comment` text NOT NULL,
  `name` text,
  PRIMARY KEY (`index`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `comments`
--

DROP TABLE IF EXISTS `comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `comments` (
  `comment_id` int(11) NOT NULL AUTO_INCREMENT,
  `runindex` int(11) DEFAULT NULL,
  `runname` text NOT NULL,
  `user_name` text NOT NULL,
  `date` datetime NOT NULL,
  `comment` text NOT NULL,
  `name` text NOT NULL,
  PRIMARY KEY (`comment_id`)
) ENGINE=InnoDB AUTO_INCREMENT=30079 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `json_store`
--

DROP TABLE IF EXISTS `json_store`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `json_store` (
  `runkey` int(11) NOT NULL AUTO_INCREMENT,
  `user_name` text,
  `date_time` text,
  `json_string` longtext,
  `minion` text,
  PRIMARY KEY (`runkey`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `minIONruns`
--

DROP TABLE IF EXISTS `minIONruns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `minIONruns` (
  `runindex` int(11) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `user_name` varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `flowcellid` text NOT NULL,
  `runname` text NOT NULL,
  `activeflag` int(11) NOT NULL,
  `comment` text NOT NULL,
  `FlowCellOwner` tinytext NOT NULL,
  `RunNumber` int(11) NOT NULL,
  `reference` text,
  `reflength` int(11) DEFAULT NULL,
  `basecalleralg` text,
  `version` text,
  `minup_version` text,
  `process` text,
  `mt_ctrl_flag` int(1) DEFAULT '0',
  `watch_dir` text,
  `host_ip` tinytext,
  `updatecheck` int(1) DEFAULT '0',
  PRIMARY KEY (`runindex`),
  KEY `user` (`user_name`(5)),
  KEY `runname` (`runname`(5))
) ENGINE=MyISAM AUTO_INCREMENT=7127 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `userrun`
--

DROP TABLE IF EXISTS `userrun`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `userrun` (
  `user_id` int(11) NOT NULL,
  `runindex` int(11) NOT NULL,
  `userrunindex` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`userrunindex`)
) ENGINE=MyISAM AUTO_INCREMENT=6398 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'auto incrementing user_id of each user, unique index',
  `user_name` varchar(64) COLLATE utf8_unicode_ci NOT NULL COMMENT 'user''s name, unique',
  `user_password_hash` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'user''s password in salted and hashed format',
  `user_email` varchar(64) COLLATE utf8_unicode_ci NOT NULL COMMENT 'user''s email, unique',
  `admin` int(11) NOT NULL DEFAULT '0',
  `twitter` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `twitnote` int(11) DEFAULT NULL,
  `emailnote` int(11) DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_name` (`user_name`),
  UNIQUE KEY `user_email` (`user_email`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='user data';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-11-08  9:32:23
