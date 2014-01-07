# ************************************************************
# Sequel Pro SQL dump
# Version 4096
#
# http://www.sequelpro.com/
# http://code.google.com/p/sequel-pro/
#
# Host: 127.0.0.1 (MySQL 5.5.29)
# Database: blogmarks
# Generation Time: 2014-01-07 22:23:43 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table bm_links
# ------------------------------------------------------------

CREATE TABLE `bm_links` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `href` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `href` (`href`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table bm_marks
# ------------------------------------------------------------

CREATE TABLE `bm_marks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `published` datetime DEFAULT '0000-00-00 00:00:00',
  `updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `related` int(11) DEFAULT NULL,
  `via` int(11) DEFAULT NULL,
  `image` int(11) DEFAULT NULL,
  `author` int(11) DEFAULT NULL,
  `content` text,
  `contentType` varchar(4) NOT NULL DEFAULT 'text',
  `visibility` tinyint(1) NOT NULL DEFAULT '0',
  `display` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `author` (`author`),
  KEY `related` (`related`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table bm_marks_has_bm_tags
# ------------------------------------------------------------

CREATE TABLE `bm_marks_has_bm_tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mark_id` int(11) NOT NULL DEFAULT '0',
  `tag_id` int(11) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL,
  `link_id` int(11) NOT NULL,
  `label` varchar(100) NOT NULL,
  `isHidden` tinyint(1) NOT NULL,
  `visibility` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mark_id` (`mark_id`),
  KEY `tag_id` (`tag_id`),
  KEY `user_id` (`user_id`),
  KEY `link_id` (`link_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table bm_screenshots
# ------------------------------------------------------------

CREATE TABLE `bm_screenshots` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `link` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `generated` datetime NOT NULL,
  `url` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `tentatives` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `status_link` (`link`,`status`),
  KEY `status` (`status`),
  KEY `created` (`created`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table bm_tags
# ------------------------------------------------------------

CREATE TABLE `bm_tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(255) NOT NULL DEFAULT '',
  `author` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `index_author` (`author`),
  KEY `label_author` (`label`,`author`),
  KEY `label` (`label`),
  FULLTEXT KEY `text_label` (`label`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table bm_users
# ------------------------------------------------------------

CREATE TABLE `bm_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `url` varchar(255) NOT NULL DEFAULT '',
  `login` varchar(250) NOT NULL DEFAULT '',
  `pass` varchar(255) DEFAULT NULL,
  `permlevel` tinyint(1) NOT NULL DEFAULT '0',
  `avatar` varchar(255) NOT NULL DEFAULT '',
  `timezone` varchar(4) DEFAULT '1',
  `lang` tinyint(2) NOT NULL,
  `ip` varchar(255) NOT NULL,
  `updated` datetime NOT NULL,
  `code` varchar(255) NOT NULL,
  `activationkey` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_login` (`login`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
