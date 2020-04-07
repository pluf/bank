
ALTER TABLE `bank_backend` CHANGE `title` `title` varchar(50) DEFAULT '';
ALTER TABLE `bank_backend` CHANGE `description` `description` varchar(200) DEFAULT '';
ALTER TABLE `bank_backend` CHANGE `symbol` `symbol` varchar(50) DEFAULT '';
ALTER TABLE `bank_backend` CHANGE `home` `home` varchar(50) DEFAULT '';
ALTER TABLE `bank_backend` CHANGE `redirect` `redirect` varchar(50) DEFAULT '';

ALTER TABLE `bank_receipt` CHANGE `title` `title` varchar(50) DEFAULT '';
ALTER TABLE `bank_receipt` CHANGE `description` `description` varchar(200) DEFAULT '';
ALTER TABLE `bank_receipt` CHANGE `email` `email` varchar(100) DEFAULT '';
ALTER TABLE `bank_receipt` CHANGE `phone` `phone` varchar(100) DEFAULT '';
ALTER TABLE `bank_receipt` CHANGE `callbackURL` `callbackURL` varchar(200) DEFAULT '';
ALTER TABLE `bank_receipt` CHANGE `payRef` `payRef` varchar(200) DEFAULT '';
ALTER TABLE `bank_receipt` CHANGE `callURL` `callURL` varchar(200) DEFAULT '';
ALTER TABLE `bank_receipt` CHANGE `payMeta` `payMeta` longtext;
