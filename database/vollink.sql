
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

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `vollink_db` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;

USE `vollink_db`;
DROP TABLE IF EXISTS `application`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `application` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `shift_id` int(11) NOT NULL,
  `submitted_at` datetime NOT NULL,
  `status` varchar(15) NOT NULL DEFAULT 'PENDING',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq-application-user-shift` (`user_id`,`shift_id`),
  KEY `fk-application-event_id` (`event_id`),
  KEY `fk-application-shift_id` (`shift_id`),
  CONSTRAINT `fk-application-event_id` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk-application-shift_id` FOREIGN KEY (`shift_id`) REFERENCES `shift` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk-application-user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `attendance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `attendance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `application_id` int(11) NOT NULL,
  `shift_id` int(11) NOT NULL,
  `hours_worked` decimal(5,2) NOT NULL DEFAULT 0.00,
  `confirmed` tinyint(1) NOT NULL DEFAULT 0,
  `confirmed_by_id` int(11) DEFAULT NULL,
  `confirmed_at` datetime DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq-attendance-application-shift` (`application_id`,`shift_id`),
  KEY `fk-attendance-shift_id` (`shift_id`),
  KEY `fk-attendance-confirmed_by_id` (`confirmed_by_id`),
  CONSTRAINT `fk-attendance-application_id` FOREIGN KEY (`application_id`) REFERENCES `application` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk-attendance-confirmed_by_id` FOREIGN KEY (`confirmed_by_id`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk-attendance-shift_id` FOREIGN KEY (`shift_id`) REFERENCES `shift` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `auth_assignment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `auth_assignment` (
  `item_name` varchar(64) NOT NULL,
  `user_id` varchar(64) NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`item_name`,`user_id`),
  KEY `idx-auth_assignment-user_id` (`user_id`),
  CONSTRAINT `auth_assignment_ibfk_1` FOREIGN KEY (`item_name`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `auth_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `auth_item` (
  `name` varchar(64) NOT NULL,
  `type` smallint(6) NOT NULL,
  `description` text DEFAULT NULL,
  `rule_name` varchar(64) DEFAULT NULL,
  `data` blob DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`name`),
  KEY `rule_name` (`rule_name`),
  KEY `idx-auth_item-type` (`type`),
  CONSTRAINT `auth_item_ibfk_1` FOREIGN KEY (`rule_name`) REFERENCES `auth_rule` (`name`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `auth_item_child`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `auth_item_child` (
  `parent` varchar(64) NOT NULL,
  `child` varchar(64) NOT NULL,
  PRIMARY KEY (`parent`,`child`),
  KEY `child` (`child`),
  CONSTRAINT `auth_item_child_ibfk_1` FOREIGN KEY (`parent`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `auth_item_child_ibfk_2` FOREIGN KEY (`child`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `auth_rule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `auth_rule` (
  `name` varchar(64) NOT NULL,
  `data` blob DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `event`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `event` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `creator_id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `description` text NOT NULL,
  `category` varchar(50) NOT NULL,
  `location` varchar(150) NOT NULL,
  `required_skills` varchar(255) DEFAULT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `application_deadline` datetime NOT NULL,
  `capacity` int(11) NOT NULL,
  `status` varchar(15) NOT NULL DEFAULT 'DRAFT',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk-event-creator_id` (`creator_id`),
  CONSTRAINT `fk-event-creator_id` FOREIGN KEY (`creator_id`) REFERENCES `user` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `migration`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migration` (
  `version` varchar(180) NOT NULL,
  `apply_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `notification`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notification` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `event_id` int(11) DEFAULT NULL,
  `type` varchar(40) NOT NULL,
  `message` varchar(500) NOT NULL,
  `scheduled_at` datetime NOT NULL,
  `sent_at` datetime DEFAULT NULL,
  `read_at` datetime DEFAULT NULL,
  `status` varchar(15) NOT NULL DEFAULT 'PENDING',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk-notification-user_id` (`user_id`),
  KEY `fk-notification-event_id` (`event_id`),
  CONSTRAINT `fk-notification-event_id` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk-notification-user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `shift`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shift` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` int(11) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `capacity` int(11) NOT NULL DEFAULT 1,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk-shift-event_id` (`event_id`),
  CONSTRAINT `fk-shift-event_id` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `auth_key` varchar(32) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `password_reset_token` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `status` smallint(6) NOT NULL DEFAULT 10,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `deleted_at` int(11) DEFAULT NULL,
  `verification_token` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `password_reset_token` (`password_reset_token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `user_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `account_type` varchar(15) NOT NULL DEFAULT 'VOLUNTEER',
  `birth_date` date DEFAULT NULL,
  `skills` varchar(255) DEFAULT NULL,
  `description` varchar(500) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `location` varchar(150) DEFAULT NULL,
  `email_verified` tinyint(1) NOT NULL DEFAULT 0,
  `account_status` varchar(15) NOT NULL DEFAULT 'PENDING',
  `rejection_reason` varchar(255) DEFAULT NULL,
  `suspended_until` datetime DEFAULT NULL,
  `suspension_reason` varchar(255) DEFAULT NULL,
  `last_login_at` datetime DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  CONSTRAINT `fk-user_details-user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

LOCK TABLES `auth_item` WRITE;
/*!40000 ALTER TABLE `auth_item` DISABLE KEYS */;
INSERT INTO `auth_item` VALUES ('accessBackoffice',2,'Aceder a area de gestao (back-office)',NULL,NULL,1792783176,1792783176),('accessFrontoffice',2,'Aceder ao front-office autenticado',NULL,NULL,1792783176,1792783176),('accessMobile',2,'Aceder a API da aplicacao mobile',NULL,NULL,1792783176,1792783176),('applicationDecide',2,'Aprovar ou rejeitar candidaturas dos eventos que criei','eventOwner',NULL,1792783176,1792783176),('applicationSubmit',2,'Candidatar-se a um evento',NULL,NULL,1792783176,1792783176),('contentModerate',2,'Moderar conteudos e publicacoes',NULL,NULL,1792783176,1792783176),('empresa',1,NULL,NULL,NULL,1792783176,1792783176),('eventCreate',2,'Criar um voluntariado/evento (nasce em DRAFT)',NULL,NULL,1792783176,1792783176),('eventManage',2,'Gerir um evento que criei: editar, gerir os seus turnos, publicar, cancelar, concluir ou apagar','eventOwner',NULL,1792783176,1792783176),('moderateEvent',2,'Back-office: gerir qualquer evento - publicar, cancelar, turnos e decidir candidaturas',NULL,NULL,1792783176,1792783176),('presenceConfirm',2,'Registar horas e confirmar presencas nos turnos dos eventos que criei','eventOwner',NULL,1792783176,1792783176),('sysAdmin',1,NULL,NULL,NULL,1792783176,1792783176),('systemManage',2,'Gerir as configuracoes do sistema',NULL,NULL,1792783176,1792783176),('userModerate',2,'Back-office: gerir contas (ativar, suspender, rejeitar com motivo) e validar empresas',NULL,NULL,1792783176,1792783176),('voluntario',1,NULL,NULL,NULL,1792783176,1792783176);
/*!40000 ALTER TABLE `auth_item` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `auth_item_child` WRITE;
/*!40000 ALTER TABLE `auth_item_child` DISABLE KEYS */;
INSERT INTO `auth_item_child` VALUES ('empresa','accessFrontoffice'),('empresa','applicationDecide'),('empresa','eventCreate'),('empresa','eventManage'),('empresa','presenceConfirm'),('sysAdmin','accessBackoffice'),('sysAdmin','accessFrontoffice'),('sysAdmin','moderateEvent'),('sysAdmin','userModerate'),('voluntario','accessFrontoffice'),('voluntario','accessMobile'),('voluntario','applicationSubmit');
/*!40000 ALTER TABLE `auth_item_child` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `auth_rule` WRITE;
/*!40000 ALTER TABLE `auth_rule` DISABLE KEYS */;
INSERT INTO `auth_rule` VALUES ('eventOwner','O:26:\"common\\rbac\\EventOwnerRule\":3:{s:4:\"name\";s:10:\"eventOwner\";s:9:\"createdAt\";i:1792783176;s:9:\"updatedAt\";i:1792783176;}',1792783176,1792783176);
/*!40000 ALTER TABLE `auth_rule` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `migration` WRITE;
/*!40000 ALTER TABLE `migration` DISABLE KEYS */;
INSERT INTO `migration` VALUES ('m000000_000000_base',1792695398),('m130524_201442_init',1792695401),('m140506_102106_rbac_init',1792696149),('m170907_052038_rbac_add_index_on_auth_assignment_user_id',1792696149),('m180523_151638_rbac_updates_indexes_without_prefix',1792696149),('m190124_110200_add_verification_token_column_to_user_table',1792695401),('m200409_110543_rbac_update_mssql_trigger',1792696149),('m261022_203230_create_user_details_table',1792701278),('m261022_203630_create_event_table',1792701451),('m261022_203912_create_shift_table',1792701780),('m261022_203915_create_application_table',1792701780),('m261022_203917_create_attendance_table',1792701781),('m261022_203918_create_notification_table',1792701781),('m261024_130000_add_core_columns',1792846459),('m261024_150000_application_shift_level',1792872646),('m261024_160000_add_ui_columns',1792873294),('m261115_120000_add_deleted_at_to_user',1794775071);
/*!40000 ALTER TABLE `migration` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;


-- Contas de exemplo (credenciais no README)
INSERT INTO `user` (id, username, auth_key, password_hash, email, status, created_at, updated_at) VALUES
(1, 'admin', 'kQ3vN8xT1mZ5rL0cYb7dWe2HfUa9PjXs', '$2y$13$y629NYtmBxHCWFR4/ZkRXOmHOyXz2DHVUvV/uqeIUmFtHXYGGXJXO', 'admin@vollink.pt', 10, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(2, 'voluntario', 'Zt4yB6nM9qRw1KcVxE3aJd8LgHu5PsOf', '$2y$13$BUzl5wyAy6s/7Qpqvm8EWuw..kudLJM8O4TkpHGCfCKrqOn5dcpBW', 'voluntario@vollink.pt', 10, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(3, 'empresa', 'Pl7wC2sD5vFg8NhJkA1zXq4RtYu6BeIo', '$2y$13$FEYrqL3tZSqcDXcb7q8ZIeZOJxT0ZORs6GYdPKp6R7OYVaMm7CWjm', 'empresa@vollink.pt', 10, UNIX_TIMESTAMP(), UNIX_TIMESTAMP());

INSERT INTO user_details (user_id, name, account_type, account_status, email_verified, created_at, updated_at) VALUES
(1, 'Administrador', 'ADMIN', 'APPROVED', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(2, 'Ana Silva', 'VOLUNTEER', 'APPROVED', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(3, 'Associação Horizonte', 'COMPANY', 'APPROVED', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP());

INSERT INTO auth_assignment (item_name, user_id, created_at) VALUES
('sysAdmin', '1', UNIX_TIMESTAMP()),
('voluntario', '2', UNIX_TIMESTAMP()),
('empresa', '3', UNIX_TIMESTAMP());
