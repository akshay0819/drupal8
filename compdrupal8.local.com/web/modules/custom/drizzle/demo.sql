CREATE DEFINER=`root`@`localhost` TRIGGER `drupal869comp`.`xtragbatch_BEFORE_INSERT` BEFORE INSERT ON `xtragbatch` FOR EACH ROW
BEGIN
set new.tenant = SUBSTRING_INDEX(USER(),'@',1);
END


CREATE 
    ALGORITHM = UNDEFINED 
    DEFINER = `root`@`localhost` 
    SQL SECURITY DEFINER
VIEW `tragbatch` AS
    SELECT 
        `xtragbatch`.`batchpk` AS `batchpk`,
        `xtragbatch`.`batchno` AS `batchno`,
        `xtragbatch`.`batchdate` AS `batchdate`,
        `xtragbatch`.`packdate` AS `packdate`,
        `xtragbatch`.`usebydate` AS `usebydate`,
        `xtragbatch`.`netweight` AS `netweight`,
        `xtragbatch`.`packedweight` AS `packedweight`,
        `xtragbatch`.`batchtext` AS `batchtext`,
        `xtragbatch`.`accountpk` AS `accountpk`,
        `xtragbatch`.`productpk` AS `productpk`,
        `xtragbatch`.`productdesc` AS `productdesc`,
        `xtragbatch`.`doctype` AS `doctype`,
        `xtragbatch`.`docno` AS `docno`,
        `xtragbatch`.`docdate` AS `docdate`,
        `xtragbatch`.`refdoc` AS `refdoc`,
        `xtragbatch`.`refdocpk` AS `refdocpk`,
        `xtragbatch`.`customtemplatepk` AS `customtemplatepk`,
        `xtragbatch`.`approvedby` AS `approvedby`,
        `xtragbatch`.`updatedby` AS `updatedby`,
        `xtragbatch`.`updatedtime` AS `updatedtime`,
        `xtragbatch`.`tenant` AS `tenant`
    FROM
        `xtragbatch`
    WHERE
        (`xtragbatch`.`tenant` = CONVERT( SUBSTRING_INDEX(USER(), '@', 1) USING LATIN1))