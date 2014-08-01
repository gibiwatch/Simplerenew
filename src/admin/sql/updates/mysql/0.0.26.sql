CREATE TABLE IF NOT EXISTS `#__simplerenew_push_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(255) NOT NULL DEFAULT '',
  `action` varchar(255) NOT NULL DEFAULT '',
  `handler` varchar(255) NOT NULL DEFAULT '',
  `package` text NOT NULL,
  `ipaddress` char(15) NOT NULL DEFAULT '',
  `logtime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `user_id` int(11) NOT NULL DEFAULT '0',
  `account_code` varchar(50) NOT NULL DEFAULT '',
  `subscription_id` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;
