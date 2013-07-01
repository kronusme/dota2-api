-- phpMyAdmin SQL Dump
-- version 3.4.11.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 01, 2013 at 02:25 PM
-- Server version: 5.5.32
-- PHP Version: 5.2.17

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `account_id` bigint(20) NOT NULL DEFAULT '0',
  `personaname` varchar(50) NOT NULL DEFAULT '',
  `steamid` varchar(64) NOT NULL DEFAULT '',
  `communityvisibilitystate` bigint(20) DEFAULT NULL,
  `profilestate` bigint(20) DEFAULT NULL,
  `lastlogoff` varchar(256) DEFAULT NULL,
  `commentpermission` bigint(20) DEFAULT NULL,
  `profileurl` varchar(256) DEFAULT NULL,
  `avatar` varchar(256) DEFAULT NULL,
  `avatarmedium` varchar(256) DEFAULT NULL,
  `avatarfull` varchar(256) DEFAULT NULL,
  `personastate` bigint(20) DEFAULT NULL,
  `realname` varchar(256) DEFAULT NULL,
  `primaryclanid` varchar(256) DEFAULT NULL,
  `timecreated` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
