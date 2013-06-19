SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE TABLE IF NOT EXISTS `api_keys` (
  `Id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `KeyType` tinyint(4) NOT NULL,
  `UserId` bigint(20) unsigned NOT NULL,
  `PublicToken` varchar(32) NOT NULL,
  `PrivateToken` varchar(43) NOT NULL,
  `Salt` varchar(10) NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `containers` (
  `Id` bigint(20) NOT NULL AUTO_INCREMENT,
  `VirtualizationType` smallint(6) NOT NULL,
  `InternalId` varchar(60) NOT NULL,
  `NodeId` bigint(20) NOT NULL,
  `Hostname` varchar(200) NOT NULL,
  `DiskSpace` int(11) NOT NULL,
  `GuaranteedRam` int(11) NOT NULL,
  `BurstableRam` int(11) NOT NULL,
  `TemplateId` bigint(20) NOT NULL,
  `CpuCount` smallint(6) NOT NULL,
  `RootPassword` varchar(50) NOT NULL,
  `Status` tinyint(4) NOT NULL,
  `IncomingTrafficUsed` bigint(20) NOT NULL,
  `IncomingTrafficLast` bigint(20) NOT NULL,
  `OutgoingTrafficUsed` bigint(20) NOT NULL,
  `OutgoingTrafficLast` bigint(20) NOT NULL,
  `OutgoingTrafficLimit` bigint(20) NOT NULL,
  `IncomingTrafficLimit` bigint(20) NOT NULL,
  `TotalTrafficLimit` bigint(20) NOT NULL,
  `UserId` bigint(20) NOT NULL,
  `TerminationDate` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ip_assignments` (
  `Id` bigint(20) NOT NULL AUTO_INCREMENT,
  `ContainerId` bigint(20) NOT NULL,
  `IpType` tinyint(4) NOT NULL,
  `IpRange` varchar(46) NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `nodes` (
  `Id` bigint(20) NOT NULL AUTO_INCREMENT,
  `Name` varchar(100) NOT NULL,
  `Hostname` varchar(200) NOT NULL,
  `Port` mediumint(9) NOT NULL,
  `User` varchar(80) NOT NULL,
  `HasCustomKey` tinyint(1) NOT NULL,
  `CustomPrivateKey` varchar(200) NOT NULL,
  `CustomPublicKey` varchar(200) NOT NULL,
  `PhysicalLocation` varchar(150) NOT NULL,
  `TunnelPort` smallint(5) unsigned NOT NULL,
  `TunnelKey` varchar(16) NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `settings` (
  `Id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `Key` varchar(120) NOT NULL,
  `Value` text NOT NULL,
  `LastChanged` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Key` (`Key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `templates` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(200) NOT NULL,
  `Description` mediumtext NOT NULL,
  `TemplateName` varchar(200) NOT NULL,
  `Supported` tinyint(1) NOT NULL,
  `Available` tinyint(1) NOT NULL,
  `Outdated` tinyint(1) NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `users` (
  `Id` bigint(20) NOT NULL AUTO_INCREMENT,
  `Username` varchar(100) NOT NULL,
  `EmailAddress` varchar(350) NOT NULL,
  `Hash` varchar(200) NOT NULL,
  `Salt` varchar(30) NOT NULL,
  `AccessLevel` tinyint(4) NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
