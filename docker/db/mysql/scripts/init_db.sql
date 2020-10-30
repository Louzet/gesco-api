DROP TABLE IF EXISTS `user`;

CREATE TABLE `user` (
    `id` INT unsigned NOT NULL AUTO_INCREMENT,
    `email` VARCHAR(180) NOT NULL,
    `roles` JSON NOT NULL,
    `created_at` DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
    `discr` VARCHAR(255) NOT NULL,
    `password` VARCHAR(255) DEFAULT NULL,
    UNIQUE INDEX UNIQ_8D93D649E7927C74 (`email`)
    PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `user` (email, roles, created_at, discr, password) 
VALUES ('user1@yopmail.fr', "[\'ROLE_USER\']", '2020-10-30 11:27:00', 'agent', 'demo')
;

