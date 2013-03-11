-- MySQL dump 10.13  Distrib 5.1.66, for redhat-linux-gnu (x86_64)
--
-- Host: localhost    Database: beehub
-- ------------------------------------------------------
-- Server version	5.1.66

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
-- Table structure for table `beehub_groups`
--

DROP TABLE IF EXISTS `beehub_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `beehub_groups` (
  `group_name` varchar(255) CHARACTER SET ascii COLLATE ascii_bin NOT NULL COMMENT 'urldecoded group name',
  `displayname` text NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`group_name`),
  KEY `displayname` (`displayname`(255))
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `beehub_group_members`
--

DROP TABLE IF EXISTS `beehub_group_members`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `beehub_group_members` (
  `is_admin` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Is this user also a group admin?',
  `is_invited` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Is the user invited/accepted by a group admin?',
  `is_requested` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Is the membership requested/accepted by the user?',
  `group_name` varchar(255) CHARACTER SET ascii COLLATE ascii_bin NOT NULL COMMENT 'urldecoded group name',
  `user_name` varchar(255) CHARACTER SET ascii COLLATE ascii_bin NOT NULL COMMENT 'urldecoded username',
  PRIMARY KEY (`group_name`,`user_name`),
  KEY `user_name` (`user_name`),
  KEY `is_admin` (`is_admin`),
  KEY `is_requested` (`is_requested`),
  KEY `is_invited` (`is_invited`,`is_requested`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `beehub_sponsors`
--

DROP TABLE IF EXISTS `beehub_sponsors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `beehub_sponsors` (
  `sponsor_name` varchar(255) CHARACTER SET ascii COLLATE ascii_bin NOT NULL COMMENT 'urldecoded sponsor name',
  `displayname` text NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`sponsor_name`),
  KEY `displayname` (`displayname`(255))
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `beehub_sponsor_members`
--

DROP TABLE IF EXISTS `beehub_sponsor_members`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `beehub_sponsor_members` (
  `sponsor_name` varchar(255) CHARACTER SET ascii COLLATE ascii_bin NOT NULL COMMENT 'urldecoded sponsor name',
  `user_name` varchar(255) CHARACTER SET ascii COLLATE ascii_bin NOT NULL COMMENT 'urldecoded username',
  `is_admin` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Whether this user is administrator of the sponsor',
  `is_accepted` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Has the sponsor accepted to sponsor this user?',
  UNIQUE KEY `membership` (`sponsor_name`,`user_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `beehub_users`
--

DROP TABLE IF EXISTS `beehub_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `beehub_users` (
  `user_name` varchar(255) CHARACTER SET ascii COLLATE ascii_bin NOT NULL COMMENT 'urldecoded username',
  `displayname` text NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `unverified_email` varchar(255) DEFAULT NULL COMMENT 'If a user enters a new e-mail address, he/she has to verify it first. Until then, it is stored here.',
  `password` varchar(255) DEFAULT NULL,
  `surfconext_id` varchar(256) DEFAULT NULL,
  `surfconext_description` text,
  `x509` text CHARACTER SET ascii,
  `sponsor_name` varchar(255) DEFAULT NULL COMMENT 'The default sponsor for files of this user',
  `verification_code` varchar(32) DEFAULT NULL COMMENT 'If the user still has to verify his e-mail address, he/she is sent an e-mail with this code',
  `verification_expiration` timestamp NULL DEFAULT NULL COMMENT 'The moment when the verification_code should expire',
  PRIMARY KEY (`user_name`),
  UNIQUE KEY `surfconext_id` (`surfconext_id`),
  KEY `sponsor_name` (`sponsor_name`),
  KEY `displayname` (`displayname`(255))
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2013-03-06 18:07:57
