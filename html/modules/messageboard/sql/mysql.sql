CREATE TABLE `{prefix}_{dirname}_board` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `client_key` varchar(255) NOT NULL DEFAULT '',
  `created` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `client_key` (`client_key`)
) ENGINE=InnoDB;

CREATE TABLE `{prefix}_{dirname}_comment` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `board_id` int(11) unsigned NOT NULL,
  `user_id` mediumint(8) unsigned NOT NULL,
  `body` text NOT NULL,
  `created` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `board_id` (`board_id`)
) ENGINE=InnoDB;

CREATE TABLE `{prefix}_{dirname}_attachment` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `board_id` int(11) unsigned NOT NULL,
  `comment_id` int(11) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `size` int(11) unsigned NOT NULL,
  `created` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `board_id` (`board_id`),
  KEY `comment_id` (`comment_id`)
) ENGINE=InnoDB;

CREATE TABLE `{prefix}_{dirname}_member` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` mediumint(8) unsigned NOT NULL,
  `board_id` int(11) unsigned NOT NULL,
  `created` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`,`board_id`),
  KEY `board_id` (`board_id`)
) ENGINE=InnoDB;