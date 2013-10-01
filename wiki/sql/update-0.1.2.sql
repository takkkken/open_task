CREATE TABLE `blocked` (
  `ip_address` varchar(24) collate utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE  `node` DROP `node_type`;

ALTER TABLE  `user` ADD `subscribe` tinyint(1) NOT NULL;
