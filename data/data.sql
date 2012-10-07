
delimiter $$

CREATE TABLE `acl_roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `parent` int(11) DEFAULT NULL,
  `sort` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `FK_role_parent` (`parent`),
  CONSTRAINT `FK_role_parent` FOREIGN KEY (`parent`) REFERENCES `acl_roles` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1$$

delimiter $$

CREATE TABLE `acl_resources` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `parent` int(11) DEFAULT NULL,
  `sort` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `FK_resource_parent` (`parent`),
  CONSTRAINT `FK_resource_parent` FOREIGN KEY (`parent`) REFERENCES `acl_resources` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1$$

delimiter $$

CREATE TABLE `acl_privileges` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(5) NOT NULL,
  `privilege` varchar(100) DEFAULT NULL,
  `role` int(11) DEFAULT NULL,
  `resource` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `acl_pr_role` (`role`),
  KEY `acl_pr_resource` (`resource`),
  KEY `FK_privilege_role` (`role`),
  KEY `FK_privilege_resource` (`resource`),
  CONSTRAINT `FK_privilege_role` FOREIGN KEY (`role`) REFERENCES `acl_roles` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `FK_privilege_resource` FOREIGN KEY (`resource`) REFERENCES `acl_resources` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1$$

delimiter $$

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `role` int(11) DEFAULT '1',
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `firstname` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lastname` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `active` tinyint(1) DEFAULT '0',
  `signature` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `display_name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email_UNIQUE` (`email`),
  UNIQUE KEY `username_UNIQUE` (`username`),
  KEY `fk_user_role` (`role`),
  CONSTRAINT `fk_user_role` FOREIGN KEY (`role`) REFERENCES `acl_roles` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci$$


-- Vulling
delimiter $$

INSERT INTO `acl_roles` (`name`,`parent`,`sort`) VALUES ('guest',NULL,0)$$
INSERT INTO `acl_roles` (`name`,`parent`,`sort`) VALUES ('registered',1,1)$$
INSERT INTO `acl_roles` (`name`,`parent`,`sort`) VALUES ('member',2,2)$$
INSERT INTO `acl_roles` (`name`,`parent`,`sort`) VALUES ('supermember',3,3)$$
INSERT INTO `acl_roles` (`name`,`parent`,`sort`) VALUES ('moderator',4,4)$$
INSERT INTO `acl_roles` (`name`,`parent`,`sort`) VALUES ('admin',5,5)$$
INSERT INTO `acl_roles` (`name`,`parent`,`sort`) VALUES ('superadmin',NULL,6)$$

