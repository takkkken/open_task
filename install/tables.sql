CREATE DATABASE opentask DEFAULT CHARACTER SET UTF8;

-- ========================================================================================
-- opentask tables
-- ========================================================================================

CREATE TABLE topic (
  topic_id int(11) NOT NULL auto_increment,
  user_name varchar(255) NOT NULL,
  topic_to varchar(255) NULL,
  topic_cc varchar(255) NULL,
  topic_title varchar(255) NULL,
  topic_contents text NOT NULL,
  topic_type varchar(255),
  topic_project varchar(255),
  topic_due_datetime datetime NULL,
  topic_res_count int(10) NOT NULL default '0',
  topic_status varchar(255) NOT NULL,
  topic_priority int(10) NOT NULL default '0',
  topic_cost int(10) NOT NULL default '0',
  file_name text NULL,
  modified_user varchar(255) NULL,
  is_admin int(1) NOT NULL default '0',
  is_deleted int(1) NOT NULL default '0',
  modified_time datetime NOT NULL default '0000-00-00 00:00:00',
  registered_time datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (topic_id)
);

-- ALTER TABLE topic ADD topic_priority int(10) AFTER topic_status;
-- ALTER TABLE topic ADD topic_cc varchar(255) NULL AFTER topic_to;
-- ALTER TABLE topic ADD topic_cost int(10) AFTER topic_priority;

CREATE TABLE res (
  res_id int(11) NOT NULL auto_increment,
  topic_id int(11) NOT NULL ,
  topic_status varchar(255) NOT NULL,
  user_name varchar(255) NOT NULL,
  res_title varchar(255) NULL,
  res_contents text NOT NULL,
  authority varchar(255),
  file_name text NULL,
  modified_user varchar(255) NULL,
  is_deleted int(1) NOT NULL default '0',
  modified_time datetime NOT NULL default '0000-00-00 00:00:00',
  registered_time datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (res_id)
);

CREATE TABLE modified_user_count (
  user_name varchar(255) NOT NULL unique,
  modified_count int(11) NOT NULL default '1'
);

CREATE TABLE perf (
  perf_id int(11) NOT NULL auto_increment,
  topic_id int(11) NOT NULL ,
  user_name varchar(255) NOT NULL,
  work_start datetime ,
  work_end   datetime ,
  PRIMARY KEY  (perf_id),
  INDEX idx_topic_id (topic_id),
  INDEX idx_user_name (user_name)
);






























-- ========================================================================================
-- wiki tables
-- ========================================================================================



-- phpMyAdmin SQL Dump
-- version 3.1.3.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 30, 2010 at 05:24 PM
-- Server version: 5.1.33
-- PHP Version: 5.2.9

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `test`
--

-- --------------------------------------------------------

--
-- Table structure for table `blocked`
--

CREATE TABLE IF NOT EXISTS `blocked` (
  `ip_address` varchar(24) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `blocked`
--


-- --------------------------------------------------------

--
-- Table structure for table `node`
--

CREATE TABLE IF NOT EXISTS `node` (
  `node_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) unsigned NOT NULL,
  `label` varchar(48) COLLATE utf8_unicode_ci NOT NULL,
  `node_position` int(10) unsigned NOT NULL,
  `locked` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`node_id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='node can be folder or page.  root is for language reference.' AUTO_INCREMENT=3 ;

--
-- Dumping data for table `node`
--

INSERT INTO `node` (`node_id`, `parent_id`, `label`, `node_position`, `locked`) VALUES
(1, 0, 'root', 0, 1),
(2, 1, 'Welcome', 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `node_revision`
--

CREATE TABLE IF NOT EXISTS `node_revision` (
  `revision_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `node_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `user_ip` varchar(48) COLLATE utf8_unicode_ci NOT NULL,
  `language` varchar(8) COLLATE utf8_unicode_ci NOT NULL,
  `action` enum('add','remove','rename','paste') COLLATE utf8_unicode_ci NOT NULL,
  `comment` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `revision_time` datetime NOT NULL,
  `label` varchar(48) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`revision_id`),
  KEY `item_id` (`node_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='This is the history of changes to nodes(tree)' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `page`
--

CREATE TABLE IF NOT EXISTS `page` (
  `node_id` int(11) NOT NULL,
  `language` varchar(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `label` varchar(96) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `page_text` text COLLATE utf8_unicode_ci NOT NULL,
  `locked` tinyint(1) NOT NULL DEFAULT '0',
  KEY `folder_id` (`node_id`),
  KEY `language` (`language`),
  FULLTEXT KEY `page_text` (`page_text`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='non root nodes have a page wether folder or leaf';

INSERT INTO `page` (`node_id`, `language`, `label`, `page_text`, `locked`) VALUES
(2, 'en', 'Welcome', '=Welcome=\n\nThank you for choosing Wiki Web Help.\n\nThe wiki has become the clear choice for use among projects for their help documentation. This project takes the wiki concept and tailors it for use specifically for help documentation. It combines the best of both worlds with the operation similar to a chm viewer and the web technologies that enable community involvement.\n\nYou can begin adding pages to the *Contents* section by right clicking on the *Welcome* node.  You can edit pages by selecting the *Edit* menu above.\n\nFor additional help see the project documentation on our [http://wikiwebhelp.org,website].\n', 0);

-- --------------------------------------------------------

--
-- Table structure for table `redirect`
--

CREATE TABLE IF NOT EXISTS `redirect` (
  `redirect_path` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `language` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `redirect_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Table structure for table `revision`
--

CREATE TABLE IF NOT EXISTS `revision` (
  `revision_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `node_id` int(10) unsigned NOT NULL,
  `language` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `user_ip` varchar(48) COLLATE utf8_unicode_ci NOT NULL,
  `type` enum('page','tag') COLLATE utf8_unicode_ci NOT NULL,
  `page_text` text COLLATE utf8_unicode_ci,
  `comment` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `revision_time` datetime NOT NULL,
  PRIMARY KEY (`revision_id`),
  KEY `item_id` (`node_id`),
  KEY `item_id_2` (`node_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='item_id will be id of either page or folder that was changed' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `subscription`
--

CREATE TABLE IF NOT EXISTS `subscription` (
  `subscription_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `page_id` int(10) unsigned NOT NULL,
  `language` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`subscription_id`),
  KEY `page_id` (`page_id`,`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Table structure for table `tag`
--

CREATE TABLE IF NOT EXISTS `tag` (
  `tag_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tag` varchar(48) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`tag_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=134 ;

--
-- Table structure for table `tagxref`
--

CREATE TABLE IF NOT EXISTS `tagxref` (
  `tagxref_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tag_id` int(10) unsigned NOT NULL,
  `node_id` int(10) unsigned NOT NULL,
  `language` varchar(2) NOT NULL,
  PRIMARY KEY (`tagxref_id`),
  KEY `tag_id` (`tag_id`,`node_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=211 ;

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_name` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `level` enum('admin','user','blocked') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'user',
  `subscribe` tinyint(1) NOT NULL,
  `user_disp_name` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

-- ALTER TABLE user ADD `user_disp_name` varchar(32) AFTER subscribe;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `user_name`, `password`, `email`, `level`, `subscribe`) VALUES
(1, 'admin', '21232f297a57a5a743894a0e4a801fc3', 'admin@admin.com', 'admin', 1);
