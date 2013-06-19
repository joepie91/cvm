CREATE TABLE IF NOT EXISTS `settings` (
  `Id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `Key` varchar(120) NOT NULL,
  `Value` text NOT NULL,
  `LastChanged` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Key` (`Key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
