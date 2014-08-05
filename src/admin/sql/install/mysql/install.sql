CREATE TABLE IF NOT EXISTS `#__simplerenew_plans` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL,
  `code` varchar(45) NOT NULL,
  `name` varchar(45) NOT NULL,
  `length` int(11) NOT NULL,
  `unit` varchar(45) NOT NULL,
  `trial_length` int(11) NOT NULL,
  `trial_unit` varchar(45) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `setup_cost` decimal(10,2) NOT NULL,
  `published` tinyint(4) NOT NULL,
  `ordering`  int(11) NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(10) unsigned NOT NULL DEFAULT '0',
  `created_by_alias` varchar(255) NOT NULL DEFAULT '',
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(10) unsigned NOT NULL DEFAULT '0',
  `checked_out` int(10) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `#__simplerenew_push_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(255) NOT NULL DEFAULT '',
  `action` varchar(255) NOT NULL DEFAULT '',
  `handler` varchar(255) NOT NULL DEFAULT '',
  `response`  varchar(255) NOT NULL DEFAULT '',
  `package` text NOT NULL,
  `ipaddress` char(15) NOT NULL DEFAULT '',
  `logtime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `user_id` int(11) NOT NULL DEFAULT '0',
  `account_code` varchar(50) NOT NULL DEFAULT '',
  `subscription_id` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;
