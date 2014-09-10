CREATE TABLE IF NOT EXISTS `#__simplerenew_countries` (
  `code` char(2) NOT NULL,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`code`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `#__simplerenew_regions` (
  `code` char(2) NOT NULL,
  `country_code` char(2) NOT NULL,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`code`,`country_code`)
) ENGINE=InnoDB;
