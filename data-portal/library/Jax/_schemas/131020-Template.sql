-- MySQL dump 10.13  Distrib 5.5.16, for Win32 (x86)
--
-- ------------------------------------------------------
-- Server version	5.5.12-log

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
-- Table structure for table `acl_modules`
--

DROP TABLE IF EXISTS `acl_modules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `acl_modules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module` varchar(45) NOT NULL,
  `description` tinytext,
  `parent` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `module_UNIQUE` (`module`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `acl_modules`
--

LOCK TABLES `acl_modules` WRITE;
/*!40000 ALTER TABLE `acl_modules` DISABLE KEYS */;
INSERT INTO `acl_modules` (`id`, `module`, `description`, `parent`) VALUES (1,'Controlpanel',NULL,NULL),(2,'Acladmin',NULL,'Controlpanel'),(3,'Moduleadmin',NULL,'Acladmin'),(4,'Roleadmin',NULL,'Acladmin'),(5,'Groupadmin',NULL,'Acladmin');
/*!40000 ALTER TABLE `acl_modules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `acl_modules_access`
--

DROP TABLE IF EXISTS `acl_modules_access`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `acl_modules_access` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `access` varchar(45) NOT NULL,
  `description` tinytext,
  PRIMARY KEY (`id`),
  UNIQUE KEY `access_UNIQUE` (`access`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `acl_modules_access`
--

LOCK TABLES `acl_modules_access` WRITE;
/*!40000 ALTER TABLE `acl_modules_access` DISABLE KEYS */;
INSERT INTO `acl_modules_access` (`id`, `access`, `description`) VALUES (1,'Read',NULL),(2,'Write',NULL),(3,'Update',NULL),(4,'Delete',NULL),(5,'Append',NULL),(6,'Service',NULL),(11,'Render',NULL);
/*!40000 ALTER TABLE `acl_modules_access` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `acl_role`
--

DROP TABLE IF EXISTS `acl_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `acl_role` (
  `role` varchar(45) NOT NULL,
  `description` text,
  `parent` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`role`),
  KEY `Index_2` (`parent`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `acl_role`
--

LOCK TABLES `acl_role` WRITE;
/*!40000 ALTER TABLE `acl_role` DISABLE KEYS */;
INSERT INTO `acl_role` (`role`, `description`, `parent`) VALUES ('Admin',NULL,NULL),('Developers',NULL,'Admin');
/*!40000 ALTER TABLE `acl_role` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE TRIGGER `acl_roles_insert`
AFTER INSERT ON `acl_role`
FOR EACH ROW
BEGIN
	CALL db_auditor('acl_role',NULL,'INSERT',CONCAT("Role '",NEW.role,"' created with Parent: ",NEW.parent));
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE TRIGGER `acl_roles_delete`
AFTER DELETE ON `acl_role`
FOR EACH ROW
BEGIN
	CALL db_auditor('acl_role',NULL,'DELETE',CONCAT("Role '",OLD.role,"' removed."));
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `acl_role_modules`
--

DROP TABLE IF EXISTS `acl_role_modules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `acl_role_modules` (
  `role` varchar(45) NOT NULL,
  `module` varchar(45) NOT NULL,
  `access` varchar(45) NOT NULL,
  `allow` int(1) DEFAULT '0',
  PRIMARY KEY (`role`,`module`,`access`),
  KEY `FK_acl_role_modules_2` (`module`),
  KEY `FK_acl_role_modules_3` (`access`),
  CONSTRAINT `FK_acl_role_modules_1` FOREIGN KEY (`role`) REFERENCES `acl_role` (`role`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_acl_role_modules_2` FOREIGN KEY (`module`) REFERENCES `acl_modules` (`module`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_acl_role_modules_3` FOREIGN KEY (`access`) REFERENCES `acl_modules_access` (`access`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `acl_role_modules`
--

LOCK TABLES `acl_role_modules` WRITE;
/*!40000 ALTER TABLE `acl_role_modules` DISABLE KEYS */;
INSERT INTO `acl_role_modules` (`role`, `module`, `access`, `allow`) VALUES ('Admin','Acladmin','Render',1),('Admin','Controlpanel','Append',1),('Admin','Controlpanel','Read',1),('Admin','Controlpanel','Render',1),('Admin','Controlpanel','Update',1),('Admin','Controlpanel','Write',1),('Admin','Customers','Append',1),('Admin','Customers','Delete',1),('Admin','Customers','Read',1),('Admin','Customers','Render',1),('Admin','Customers','Update',1),('Admin','Customers','Write',1),('Admin','Filemanager','Render',0),('Admin','Groupadmin','Render',0),('Admin','Manifests','Append',1),('Admin','Manifests','Delete',1),('Admin','Manifests','Read',1),('Admin','Manifests','Render',1),('Admin','Manifests','Update',1),('Admin','Manifests','Write',1),('Admin','Moduleadmin','Delete',0),('Admin','Moduleadmin','Render',0),('Admin','Moduleadmin','Update',0),('Admin','Moduleadmin','Write',0),('Admin','Utilities','Append',1),('Admin','Utilities','Delete',1),('Admin','Utilities','Read',1),('Admin','Utilities','Update',1),('Admin','Utilities','Write',1),('Developers','Controlpanel','Delete',1),('Developers','Controlpanel','Render',1),('Developers','Groupadmin','Render',1),('Developers','Moduleadmin','Append',1),('Developers','Moduleadmin','Delete',1),('Developers','Moduleadmin','Read',1),('Developers','Moduleadmin','Render',1),('Developers','Moduleadmin','Update',1),('Developers','Moduleadmin','Write',1);
/*!40000 ALTER TABLE `acl_role_modules` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE TRIGGER `acl_rolemod_insert`
AFTER INSERT ON `acl_role_modules`
FOR EACH ROW
BEGIN
	DECLARE access VARCHAR(20);

	IF (NEW.allow = 0) THEN SET access = " denied ";
	ELSEIF(NEW.allow = 1) THEN SET access = " granted ";
	END IF;
	
	CALL db_auditor('acl_role_modules',null,'INSERT',CONCAT(NEW.role,access,NEW.access," access to ",NEW.module));
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE TRIGGER `acl_rolemod_update`
BEFORE UPDATE ON `acl_role_modules`
FOR EACH ROW
BEGIN
	DECLARE access VARCHAR(20);
	
	IF(NEW.allow NOT IN(0,1)) THEN SET NEW.allow = OLD.allow;
	END IF;

	IF (NEW.allow = 0) THEN SET access = " denied ";
	ELSEIF(NEW.allow = 1) THEN SET access = " granted ";
	END IF;
	
	CALL db_auditor('acl_role_modules',null,'UPDATE',CONCAT(NEW.role,access,NEW.access," access to ",NEW.module));
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `acl_user_modules`
--

DROP TABLE IF EXISTS `acl_user_modules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `acl_user_modules` (
  `role` varchar(45) NOT NULL COMMENT 'Actually usernames',
  `module` varchar(45) NOT NULL,
  `access` varchar(45) NOT NULL,
  `allow` int(1) DEFAULT '0',
  PRIMARY KEY (`role`,`module`,`access`),
  KEY `FK_acl_user_modules_1` (`module`),
  KEY `FK_acl_user_modules_2` (`access`),
  CONSTRAINT `FK_acl_user_modules_1` FOREIGN KEY (`module`) REFERENCES `acl_modules` (`module`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_acl_user_modules_2` FOREIGN KEY (`access`) REFERENCES `acl_modules_access` (`access`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `acl_user_modules`
--

LOCK TABLES `acl_user_modules` WRITE;
/*!40000 ALTER TABLE `acl_user_modules` DISABLE KEYS */;
/*!40000 ALTER TABLE `acl_user_modules` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE TRIGGER `acl_usermod_insert`
AFTER INSERT ON `acl_user_modules`
FOR EACH ROW
BEGIN
	DECLARE access VARCHAR(20);

	IF (NEW.allow = 0) THEN SET access = " denied ";
	ELSEIF(NEW.allow = 1) THEN SET access = " granted ";
	END IF;
	
	CALL db_auditor('acl_user_modules',null,'INSERT',CONCAT(NEW.role,access,NEW.access," access to ",NEW.module));
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE TRIGGER `acl_usermod_update`
BEFORE UPDATE ON `acl_user_modules`
FOR EACH ROW
BEGIN
	DECLARE access VARCHAR(20);
	
	IF(NEW.allow NOT IN(0,1)) THEN SET NEW.allow = OLD.allow;
	END IF;

	IF (NEW.allow = 0) THEN SET access = " denied ";
	ELSEIF(NEW.allow = 1) THEN SET access = " granted ";
	END IF;
	
	CALL db_auditor('acl_role_modules',null,'UPDATE',CONCAT(NEW.role,access,NEW.access," access to ",NEW.module));
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `acl_user_roles`
--

DROP TABLE IF EXISTS `acl_user_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `acl_user_roles` (
  `role` varchar(45) NOT NULL,
  `parent` varchar(45) NOT NULL,
  `description` tinytext,
  PRIMARY KEY (`role`,`parent`),
  KEY `FK_acl_user_roles_1` (`parent`),
  CONSTRAINT `FK_acl_user_roles_1` FOREIGN KEY (`parent`) REFERENCES `acl_role` (`role`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `acl_user_roles`
--

LOCK TABLES `acl_user_roles` WRITE;
/*!40000 ALTER TABLE `acl_user_roles` DISABLE KEYS */;
INSERT INTO `acl_user_roles` (`role`, `parent`, `description`) VALUES ('dev','Developers',NULL),('root','Admin',NULL);
/*!40000 ALTER TABLE `acl_user_roles` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE TRIGGER `acl_user_roles_insert`
AFTER INSERT ON `acl_user_roles`
FOR EACH ROW
BEGIN
	CALL db_auditor('acl_user_roles',null,'INSERT',CONCAT(NEW.role,' added to ',NEW.parent));
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE TRIGGER `acl_user_roles_delete`
AFTER DELETE ON `acl_user_roles`
FOR EACH ROW
BEGIN
	CALL db_auditor('acl_user_roles',null,'DELETE',CONCAT(OLD.role,' removed from ',OLD.parent));
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `auth_users`
--

DROP TABLE IF EXISTS `auth_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `auth_users` (
  `username` varchar(45) NOT NULL,
  `password` varchar(45) DEFAULT NULL,
  `Email` varchar(45) NOT NULL,
  `Firstname` varchar(45) NOT NULL,
  `Lastname` varchar(45) NOT NULL,
  `Sex` varchar(1) NOT NULL,
  `Phone` varchar(45) DEFAULT NULL,
  `sys_disabled` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `auth_users`
--

LOCK TABLES `auth_users` WRITE;
/*!40000 ALTER TABLE `auth_users` DISABLE KEYS */;
INSERT INTO `auth_users` (`username`, `password`, `Email`, `Firstname`, `Lastname`, `Sex`, `Phone`, `sys_disabled`) VALUES ('dev','c4ca4238a0b923820dcc509a6f75849b','dev@email.com','Default','Developer','M','000-0000',0),('admin','c4ca4238a0b923820dcc509a6f75849b','admin@email.com','Default','Admin','M',NULL,0);
/*!40000 ALTER TABLE `auth_users` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE TRIGGER `auth_users_insert`
AFTER INSERT ON `auth_users`
FOR EACH ROW
BEGIN
	CALL db_auditor('auth_users',null,'INSERT',CONCAT('New user added: ',NEW.username));
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE TRIGGER `auth_users_update`
AFTER UPDATE ON `auth_users`
FOR EACH ROW
BEGIN
	IF (OLD.username != NEW.username)
	THEN
		CALL db_auditor('auth_users',null,'UPDATE',CONCAT('Username change from ',OLD.username,' to ',NEW.username));
	END IF;

	IF (OLD.`password` != NEW.`password`)
	THEN
		CALL db_auditor('auth_users',null,'UPDATE',CONCAT('Password changed for ',NEW.`username`));
	END IF;

	IF (OLD.`Email` != NEW.`Email`)
	THEN
		CALL db_auditor('auth_users',null,'UPDATE',CONCAT('Email updated for ',NEW.username,' from ',OLD.Email,' to ',NEW.Email));
	END IF;

	IF (OLD.Phone != NEW.Phone)
	THEN
		CALL db_auditor('auth_users',null,'UPDATE',CONCAT('Phone updated for ',NEW.username,' from ',OLD.Phone,' to ',NEW.Phone));
	END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE TRIGGER `auth_users_delete`
AFTER DELETE ON `auth_users`
FOR EACH ROW
BEGIN
	CALL db_auditor('auth_users',null,'DELETE',CONCAT('User deleted: ',OLD.username));
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `sys_dbaudit`
--

DROP TABLE IF EXISTS `sys_dbaudit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sys_dbaudit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `table` varchar(45) DEFAULT NULL,
  `record_id` int(11) DEFAULT NULL,
  `operation` varchar(45) DEFAULT NULL,
  `details` text,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE TRIGGER `sys_dbauditor_no_del`
BEFORE DELETE ON `sys_dbaudit`
FOR EACH ROW
BEGIN
	INSERT INTO `sys_dbaudit` (`table`,`record_id`,`operation`,`details`) 
	VALUES('sys_dbaudit',OLD.id,'DELETE',OLD.details);
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `sys_logs`
--

DROP TABLE IF EXISTS `sys_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sys_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user` varchar(45) DEFAULT NULL,
  `role` varchar(45) DEFAULT NULL,
  `category` varchar(45) DEFAULT NULL,
  `acl_mod` varchar(45) DEFAULT NULL,
  `details` text,
  `ip` varchar(45) DEFAULT NULL,
  `access` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE TRIGGER `sys_log_auditor`
AFTER INSERT ON `sys_logs`
FOR EACH ROW
BEGIN
	CALL db_auditor('sys_logs',NEW.id,'INSERT',NEW.details);
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE TRIGGER `sys_log_update_audit`
BEFORE UPDATE ON `sys_logs`
FOR EACH ROW
BEGIN
	IF (OLD.id != NEW.id)
	THEN
		SET NEW.id = OLD.id;
		CALL db_auditor('sys_logs',OLD.id,'UPDATE','Attempted id change');
	END IF;

	IF (OLD.user != NEW.user)
	THEN
		SET NEW.user = OLD.user;
		CALL db_auditor('sys_logs',OLD.id,'UPDATE','Attempted username change');
	END IF;

	IF (OLD.`timestamp` != NEW.`timestamp`)
	THEN
		SET NEW.`timestamp` = OLD.`timestamp`;
		CALL db_auditor('sys_logs',OLD.id,'UPDATE','Attempted timestamp change');
	END IF;

	IF (OLD.`details` != NEW.`details`)
	THEN
		SET NEW.details = OLD.details;
		CALL db_auditor('sys_logs',OLD.id,'UPDATE','Attempted details change');
	END IF;

	IF (OLD.ip != NEW.ip)
	THEN
		SET NEW.ip = OLD.ip;
		CALL db_auditor('sys_logs',OLD.id,'UPDATE','Attempted IP change');
	END IF;

	IF (OLD.access != NEW.access)
	THEN
		SET NEW.access = OLD.access;
		CALL db_auditor('sys_logs',OLD.id,'UPDATE','Attempted access change');
	END IF;

	IF (OLD.acl_mod != NEW.acl_mod)
	THEN
		SET NEW.acl_mod = OLD.acl_mod;
		CALL db_auditor('sys_logs',OLD.id,'UPDATE','Attempted acl_mod change');
	END IF;

	IF (OLD.category != NEW.category)
	THEN
		SET NEW.category = OLD.category;
		CALL db_auditor('sys_logs',OLD.id,'UPDATE','Attempted category change');
	END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE TRIGGER `sys_log_auditor_delete`
BEFORE DELETE ON `sys_logs`
FOR EACH ROW
BEGIN
	INSERT INTO `sys_logs` (SELECT * FROM `sys_logs` WHERE `id`=OLD.id);
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `sys_options`
--

DROP TABLE IF EXISTS `sys_options`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sys_options` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `option` varchar(45) NOT NULL,
  `value` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `Index_2` (`option`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sys_options`
--

LOCK TABLES `sys_options` WRITE;
/*!40000 ALTER TABLE `sys_options` DISABLE KEYS */;
/*!40000 ALTER TABLE `sys_options` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'seaboard'
--

--
-- Dumping routines for database 'seaboard'
--
/*!50003 DROP PROCEDURE IF EXISTS `db_auditor` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE PROCEDURE `db_auditor`(IN tableName VARCHAR(45),IN recordId INT,IN operation varchar(45),IN details TEXT)
BEGIN
	INSERT INTO `sys_dbaudit` (`table`,`record_id`,`operation`,`details`)
	VALUES(tableName,recordId,operation,details);
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
