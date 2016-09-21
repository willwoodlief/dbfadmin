-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 21, 2016 at 06:26 AM
-- Server version: 5.5.52-0ubuntu0.14.04.1
-- PHP Version: 5.5.9-1ubuntu4.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `dbfadmin`
--

-- --------------------------------------------------------

--
-- Table structure for table `app_email`
--

CREATE TABLE IF NOT EXISTS `app_email` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `website_name` varchar(100) NOT NULL,
  `smtp_server` varchar(100) NOT NULL,
  `smtp_port` int(10) NOT NULL,
  `email_login` varchar(150) NOT NULL,
  `email_pass` varchar(100) NOT NULL,
  `from_name` varchar(100) NOT NULL,
  `from_email` varchar(150) NOT NULL,
  `transport` varchar(255) NOT NULL,
  `verify_url` varchar(255) NOT NULL,
  `email_act` int(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `app_email`
--

INSERT INTO `app_email` (`id`, `website_name`, `smtp_server`, `smtp_port`, `email_login`, `email_pass`, `from_name`, `from_email`, `transport`, `verify_url`, `email_act`) VALUES
  (1, 'emailsrvr.com', 'smtp.emailsrvr.com', 587, 'info@gokabam.com', 'apple-daily-8', 'DBF App', 'info@gokabam.com', 'tls', 'http://localhost/dbfadmin/', 0);

-- --------------------------------------------------------

--
-- Table structure for table `app_keys`
--

CREATE TABLE IF NOT EXISTS `app_keys` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `stripe_ts` varchar(255) NOT NULL,
  `stripe_tp` varchar(255) NOT NULL,
  `stripe_ls` varchar(255) NOT NULL,
  `stripe_lp` varchar(255) NOT NULL,
  `recap_pub` varchar(100) NOT NULL,
  `recap_pri` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `app_pages`
--

CREATE TABLE IF NOT EXISTS `app_pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page` varchar(100) NOT NULL,
  `private` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=39 ;

--
-- Dumping data for table `app_pages`
--

INSERT INTO `app_pages` (`id`, `page`, `private`) VALUES
  (1, 'index.php', 0),
  (2, 'z_us_root.php', 0),
  (3, 'users/account.php', 1),
  (4, 'users/admin.php', 1),
  (5, 'users/admin_page.php', 1),
  (6, 'users/admin_pages.php', 1),
  (7, 'users/admin_permission.php', 1),
  (8, 'users/admin_permissions.php', 1),
  (9, 'users/admin_user.php', 1),
  (10, 'users/admin_users.php', 1),
  (11, 'users/edit_profile.php', 1),
  (12, 'users/email_settings.php', 1),
  (13, 'users/email_test.php', 1),
  (14, 'users/forgot_password.php', 0),
  (15, 'users/forgot_password_reset.php', 0),
  (16, 'users/index.php', 0),
  (17, 'users/init.php', 0),
  (18, 'users/join.php', 0),
  (19, 'users/joinThankYou.php', 0),
  (20, 'users/login.php', 0),
  (21, 'users/logout.php', 0),
  (22, 'users/profile.php', 1),
  (23, 'users/times.php', 0),
  (24, 'users/user_settings.php', 1),
  (25, 'users/verify.php', 0),
  (26, 'users/verify_resend.php', 0),
  (27, 'users/view_all_users.php', 1),
  (28, 'usersc/empty.php', 0),
  (29, 'info.php', 0),
  (30, 'users/private_init.example.php', 0),
  (31, 'users/private_init.php', 0),
  (32, 'pages/completed_grid.php', 0),
  (33, 'pages/index.php', 0),
  (34, 'pages/status.php', 1),
  (35, 'pages/mappings.php', 1),
  (36, 'pages/rollbacks.php', 1),
  (37, 'pages/upload.php', 1),
  (38, 'pages/validate.php', 1);

-- --------------------------------------------------------

--
-- Table structure for table `app_permissions`
--

CREATE TABLE IF NOT EXISTS `app_permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `app_permissions`
--

INSERT INTO `app_permissions` (`id`, `name`) VALUES
  (1, 'User'),
  (2, 'Administrator');

-- --------------------------------------------------------

--
-- Table structure for table `app_permission_page_matches`
--

CREATE TABLE IF NOT EXISTS `app_permission_page_matches` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `permission_id` int(15) NOT NULL,
  `page_id` int(15) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=24 ;

--
-- Dumping data for table `app_permission_page_matches`
--

INSERT INTO `app_permission_page_matches` (`id`, `permission_id`, `page_id`) VALUES
  (2, 2, 27),
  (3, 1, 24),
  (4, 1, 22),
  (5, 2, 13),
  (6, 2, 12),
  (7, 1, 11),
  (8, 2, 10),
  (9, 2, 9),
  (10, 2, 8),
  (11, 2, 7),
  (12, 2, 6),
  (13, 2, 5),
  (14, 2, 4),
  (15, 1, 3),
  (16, 1, 34),
  (17, 2, 34),
  (18, 2, 35),
  (19, 2, 36),
  (20, 1, 37),
  (21, 2, 37),
  (22, 1, 38),
  (23, 2, 38);

-- --------------------------------------------------------

--
-- Table structure for table `app_profiles`
--

CREATE TABLE IF NOT EXISTS `app_profiles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `bio` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=19 ;

--
-- Dumping data for table `app_profiles`
--

INSERT INTO `app_profiles` (`id`, `user_id`, `bio`) VALUES
  (1, 1, '<h1>This is the Admin''s bio.</h1>'),
  (2, 2, 'This is your bio'),
  (18, 18, 'This is your bio');

-- --------------------------------------------------------

--
-- Table structure for table `app_settings`
--

CREATE TABLE IF NOT EXISTS `app_settings` (
  `id` int(50) NOT NULL AUTO_INCREMENT,
  `recaptcha` int(1) NOT NULL DEFAULT '0',
  `force_ssl` int(1) NOT NULL,
  `login_type` varchar(20) NOT NULL,
  `css_sample` int(1) NOT NULL,
  `us_css1` varchar(255) NOT NULL,
  `us_css2` varchar(255) NOT NULL,
  `us_css3` varchar(255) NOT NULL,
  `css1` varchar(255) NOT NULL,
  `css2` varchar(255) NOT NULL,
  `css3` varchar(255) NOT NULL,
  `site_name` varchar(100) NOT NULL,
  `language` varchar(255) NOT NULL,
  `track_guest` int(1) NOT NULL,
  `site_offline` int(1) NOT NULL,
  `force_pr` int(1) NOT NULL,
  `reserved1` varchar(100) NOT NULL,
  `reserverd2` varchar(100) NOT NULL,
  `custom1` varchar(100) NOT NULL,
  `custom2` varchar(100) NOT NULL,
  `custom3` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `app_settings`
--

INSERT INTO `app_settings` (`id`, `recaptcha`, `force_ssl`, `login_type`, `css_sample`, `us_css1`, `us_css2`, `us_css3`, `css1`, `css2`, `css3`, `site_name`, `language`, `track_guest`, `site_offline`, `force_pr`, `reserved1`, `reserverd2`, `custom1`, `custom2`, `custom3`) VALUES
  (1, 1, 0, '', 1, '../users/css/color_schemes/standard.css', '../users/css/sb-admin.css', '../users/css/custom.css', '', '', '', 'UserSpice', 'en', 0, 0, 0, '', '', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `app_users`
--

CREATE TABLE IF NOT EXISTS `app_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(155) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fname` varchar(255) NOT NULL,
  `lname` varchar(255) NOT NULL,
  `permissions` int(11) NOT NULL,
  `logins` int(100) NOT NULL,
  `account_owner` tinyint(4) NOT NULL DEFAULT '0',
  `account_id` int(11) NOT NULL DEFAULT '0',
  `company` varchar(255) NOT NULL,
  `stripe_cust_id` varchar(255) NOT NULL,
  `billing_phone` varchar(20) NOT NULL,
  `billing_srt1` varchar(255) NOT NULL,
  `billing_srt2` varchar(255) NOT NULL,
  `billing_city` varchar(255) NOT NULL,
  `billing_state` varchar(255) NOT NULL,
  `billing_zip_code` varchar(255) NOT NULL,
  `join_date` datetime NOT NULL,
  `last_login` datetime NOT NULL,
  `email_verified` tinyint(4) NOT NULL DEFAULT '0',
  `vericode` varchar(15) NOT NULL,
  `title` varchar(100) NOT NULL,
  `active` int(1) NOT NULL,
  `custom1` varchar(255) NOT NULL,
  `custom2` varchar(255) NOT NULL,
  `custom3` varchar(255) NOT NULL,
  `custom4` varchar(255) NOT NULL,
  `custom5` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `EMAIL` (`email`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=19 ;

--
-- Dumping data for table `app_users`
--

INSERT INTO `app_users` (`id`, `email`, `username`, `password`, `fname`, `lname`, `permissions`, `logins`, `account_owner`, `account_id`, `company`, `stripe_cust_id`, `billing_phone`, `billing_srt1`, `billing_srt2`, `billing_city`, `billing_state`, `billing_zip_code`, `join_date`, `last_login`, `email_verified`, `vericode`, `title`, `active`, `custom1`, `custom2`, `custom3`, `custom4`, `custom5`) VALUES
  (1, 'userspicephp@gmail.com', 'admin', '$2y$12$1v06jm2KMOXuuo3qP7erTuTIJFOnzhpds1Moa8BadnUUeX0RV3ex.', 'Admin', 'User', 1, 3, 1, 0, 'UserSpice', '', '', '', '', '', '', '', '2016-01-01 00:00:00', '2016-09-21 06:04:55', 1, '322418', '', 0, '', '', '', '', ''),
  (2, 'noreply@userspice.com', 'user', '$2y$12$HZa0/d7evKvuHO8I3U8Ff.pOjJqsGTZqlX8qURratzP./EvWetbkK', 'user', 'user', 1, 0, 1, 0, 'none', '', '', '', '', '', '', '', '2016-01-02 00:00:00', '2016-01-02 00:00:00', 1, '970748', '', 1, '', '', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `app_users_online`
--

CREATE TABLE IF NOT EXISTS `app_users_online` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `ip` varchar(15) NOT NULL,
  `timestamp` varchar(15) NOT NULL,
  `user_id` int(10) NOT NULL,
  `session` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=28 ;

-- --------------------------------------------------------

--
-- Table structure for table `app_users_session`
--

CREATE TABLE IF NOT EXISTS `app_users_session` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `hash` varchar(255) NOT NULL,
  `uagent` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=40 ;

--
-- Dumping data for table `app_users_session`
--

INSERT INTO `app_users_session` (`id`, `user_id`, `hash`, `uagent`) VALUES
  (38, 1, 'e5c284103ce354831561e3b447d1758976c799922d7e20743e9f8c290dfe078f', 'Mozilla (Windows NT 6.1; Win64; x64) AppleWebKit (KHTML, like Gecko) Chrome Safari'),
  (39, 1, '6523a6faed7f03a1a7e96173a332d64da4cd477dd8997c7e36967eb81d2ed253', 'Mozilla (X11; Ubuntu; Linux x86_64; rv:48.0) Gecko Firefox');

-- --------------------------------------------------------

--
-- Table structure for table `app_user_permission_matches`
--

CREATE TABLE IF NOT EXISTS `app_user_permission_matches` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=104 ;

--
-- Dumping data for table `app_user_permission_matches`
--

INSERT INTO `app_user_permission_matches` (`id`, `user_id`, `permission_id`) VALUES
  (100, 1, 1),
  (101, 1, 2),
  (102, 2, 1);
