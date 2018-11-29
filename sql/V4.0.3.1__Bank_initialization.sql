
CREATE TABLE `bank_backend` (
  `id` mediumint(9) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL DEFAULT '',
  `description` varchar(200) NOT NULL DEFAULT '',
  `symbol` varchar(50) NOT NULL DEFAULT '',
  `home` varchar(50) NOT NULL DEFAULT '',
  `redirect` varchar(50) NOT NULL DEFAULT '',
  `meta` varchar(3000) NOT NULL DEFAULT '',
  `engine` varchar(50) NOT NULL DEFAULT '',
  `creation_dtime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modif_dtime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `tenant` mediumint(9) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `tenant_foreignkey_idx` (`tenant`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `bank_receipt` (
  `id` mediumint(9) unsigned NOT NULL AUTO_INCREMENT,
  `secure_id` varchar(64) NOT NULL DEFAULT '',
  `amount` int(11) NOT NULL DEFAULT 0,
  `title` varchar(50) NOT NULL DEFAULT '',
  `description` varchar(200) NOT NULL DEFAULT '',
  `email` varchar(100) NOT NULL DEFAULT '',
  `phone` varchar(100) NOT NULL DEFAULT '',
  `callbackURL` varchar(200) NOT NULL DEFAULT '',
  `payRef` varchar(200) NOT NULL DEFAULT '',
  `callURL` varchar(200) NOT NULL DEFAULT '',
  `payMeta` longtext NOT NULL,
  `backend` mediumint(9) unsigned NOT NULL DEFAULT 0,
  `owner_id` int(11) NOT NULL DEFAULT 0,
  `owner_class` varchar(50) NOT NULL DEFAULT '',
  `creation_dtime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modif_dtime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `tenant` mediumint(9) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `backend_foreignkey_idx` (`backend`),
  KEY `tenant_foreignkey_idx` (`tenant`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
