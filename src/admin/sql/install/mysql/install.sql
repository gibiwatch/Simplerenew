CREATE TABLE `#__simplerenew_plans` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL,
  `code` varchar(45) NOT NULL,
  `name` varchar(45) NOT NULL,
  `slug` varchar(45) NOT NULL,
  `description` text NOT NULL,
  `interval_length` int(11) NOT NULL,
  `interval_unit` varchar(45) NOT NULL,
  `trial_length` int(11) NOT NULL,
  `trial_unit` varchar(45) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `setup` decimal(10,2) NOT NULL,
  `published` tinyint(4) DEFAULT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(10) unsigned NOT NULL DEFAULT '0',
  `created_by_alias` varchar(255) NOT NULL DEFAULT '',
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(10) unsigned NOT NULL DEFAULT '0',
  `checked_out` int(10) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;
