CREATE TABLE `appattachment` (
  `appattachmentpk` int(11) NOT NULL AUTO_INCREMENT COMMENT 'appattachmentpk',
  `fid` int(11) NOT NULL COMMENT 'fid',
  `uid` int(11) NOT NULL COMMENT 'slno',
  `filename` varchar(255) NOT NULL COMMENT 'filename',
  `uri` varchar(255) COMMENT 'uri',
  `filemime` varchar(255) COMMENT 'filemime',
  `module` varchar(64) COMMENT 'module',
  `type` varchar(64) COMMENT 'type',
  `createdtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `tenant` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`appattachmentpk`),
  UNIQUE KEY `fid` (`fid`, `appattachmentpk`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `appmilestone` (
  `appmilestonepk` int(11) NOT NULL AUTO_INCREMENT COMMENT 'appmilestonepk',
  `datetime` datetime,
  `milestonedesc` varchar(255) NOT NULL COMMENT 'milestonedesc',
  `filename` varchar(255) COMMENT 'filename',
  `uri` varchar(255) COMMENT 'uri',
  `filemime` varchar(255) COMMENT 'filemime',
  `bopk` int(11) COMMENT 'bopk',
  `botype` varchar(64) COMMENT 'botype',
  `createdtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `tenant` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`appmilestonepk`),
  UNIQUE KEY `milestonedesc` (`milestonedesc`, `appmilestonepk`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
