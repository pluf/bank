ALTER TABLE `bank_backend` 
  ADD COLUMN `currency` varchar(50) NOT NULL DEFAULT 'IRR' AFTER `engine`,
  ADD COLUMN `deleted` tinyint(1) NOT NULL DEFAULT 0 AFTER `currency`;

CREATE TABLE `bank_wallets` (
  `id` mediumint(9) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(256) DEFAULT '',
  `currency` varchar(50) NOT NULL DEFAULT '',
  `total_deposit` decimal(32,8) NOT NULL DEFAULT 0.00000000,
  `total_withdraw` decimal(32,8) NOT NULL DEFAULT 0.00000000,
  `description` varchar(1024) DEFAULT '',
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  `creation_dtime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modif_dtime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `owner_id` mediumint(9) unsigned NOT NULL DEFAULT 0,
  `tenant` mediumint(9) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `wallet_owner_idx` (`tenant`,`owner_id`),
  KEY `owner_id_foreignkey_idx` (`owner_id`),
  KEY `tenant_foreignkey_idx` (`tenant`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

CREATE TABLE `bank_transfers` (
  `id` mediumint(9) unsigned NOT NULL AUTO_INCREMENT,
  `amount` decimal(32,8) NOT NULL DEFAULT 0.00000000,
  `description` varchar(1024) DEFAULT '',
  `creation_dtime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `acting_id` mediumint(9) unsigned NOT NULL DEFAULT 0,
  `from_wallet_id` mediumint(9) unsigned DEFAULT 0,
  `to_wallet_id` mediumint(9) unsigned NOT NULL DEFAULT 0,
  `receipt_id` mediumint(9) unsigned DEFAULT 0,
  `tenant` mediumint(9) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `transfer_acting_idx` (`tenant`,`acting_id`),
  KEY `transfer_from_wallet_idx` (`tenant`,`from_wallet_id`),
  KEY `transfer_to_wallet_idx` (`tenant`,`to_wallet_id`),
  KEY `acting_id_foreignkey_idx` (`acting_id`),
  KEY `from_wallet_id_foreignkey_idx` (`from_wallet_id`),
  KEY `to_wallet_id_foreignkey_idx` (`to_wallet_id`),
  KEY `receipt_id_foreignkey_idx` (`receipt_id`),
  KEY `tenant_foreignkey_idx` (`tenant`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

