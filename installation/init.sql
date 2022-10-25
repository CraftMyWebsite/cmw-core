CREATE TABLE IF NOT EXISTS `cmw_core_options`
(
    `option_name`    varchar(255) NOT NULL,
    `option_value`   varchar(255) NOT NULL,
    `option_updated` timestamp    NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

CREATE TABLE IF NOT EXISTS `cmw_mail_config_smtp`
(
    `mail_config_mail`         VARCHAR(255) NOT NULL,
    `mail_config_mail_reply`   VARCHAR(255) NOT NULL,
    `mail_config_address_smtp` VARCHAR(255) NOT NULL,
    `mail_config_user`         VARCHAR(255) NOT NULL,
    `mail_config_port`         INT(5)       NOT NULL,
    `mail_config_protocol`     VARCHAR(50)  NOT NULL,
    `mail_config_footer`       MEDIUMTEXT   NULL,
    `mail_config_enable`       TINYINT(1)   NOT NULL DEFAULT 1
) ENGINE = InnoDB
  CHARSET = utf8mb4;

# MY PROPOSITION !

create table IF NOT EXISTS cmw_permissions
(
    permission_id        int auto_increment
        primary key,
    permission_parent_id int                         null,
    permission_code      varchar(50) charset utf8mb4 not null,
    constraint FK_PERMISSION_PARENT_ID
        foreign key (permission_parent_id) references cmw_permissions (permission_id)
            on update cascade on delete cascade
) ENGINE = InnoDB
  charset = utf8mb4;


# END

CREATE TABLE IF NOT EXISTS `cmw_roles`
(
    `role_id`          int(11) DEFAULT NULL,
    `role_name`        tinytext NOT NULL,
    `role_description` text,
    `role_weight`      int     DEFAULT 0
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

CREATE TABLE IF NOT EXISTS `cmw_users`
(
    `user_id`        int(11)      NOT NULL,
    `user_email`     varchar(255) NOT NULL,
    `user_pseudo`    varchar(255)          DEFAULT NULL,
    `user_firstname` varchar(255)          DEFAULT NULL,
    `user_lastname`  varchar(255)          DEFAULT NULL,
    `user_password`  varchar(255)          DEFAULT NULL,
    `user_state`     tinyint(1)   NOT NULL DEFAULT '1',
    `user_key`       varchar(255) NOT NULL,
    `user_created`   timestamp    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `user_updated`   timestamp    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `user_logged`    timestamp    NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

CREATE TABLE IF NOT EXISTS `cmw_users_roles`
(
    `id`      int(11) NOT NULL,
    `user_id` int(11) NOT NULL,
    `role_id` int(11) NOT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;


CREATE TABLE IF NOT EXISTS `cmw_users_pictures`
(
    `users_pictures_user_id`     INT          NOT NULL,
    `users_pictures_image_name`  VARCHAR(255) NOT NULL,
    `users_pictures_last_update` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (`users_pictures_user_id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

CREATE TABLE IF NOT EXISTS `cmw_users_settings`
(
    `users_settings_name`    varchar(255) NOT NULL,
    `users_settings_value`   varchar(255) NOT NULL,
    `users_settings_updated` timestamp    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY (`users_settings_name`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

CREATE TABLE IF NOT EXISTS `cmw_menus`
(
    `menu_id`        int(11)      NOT NULL,
    `menu_name`      varchar(255) NOT NULL,
    `menu_url`       varchar(255) NOT NULL,
    `menu_level`     int(1)       NOT NULL,
    `menu_parent_id` int(11)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

ALTER TABLE `cmw_menus`
    ADD PRIMARY KEY (`menu_id`);

ALTER TABLE `cmw_menus`
    MODIFY `menu_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `cmw_core_options`
    ADD UNIQUE KEY `option_name` (`option_name`);

ALTER TABLE `cmw_roles`
    ADD PRIMARY KEY (`role_id`);

ALTER TABLE `cmw_roles`
    MODIFY `role_id` INT(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `cmw_mail_config_smtp`
    ADD `mail_config_id` INT NOT NULL AUTO_INCREMENT FIRST,
    ADD PRIMARY KEY (`mail_config_id`);

ALTER TABLE `cmw_users`
    ADD PRIMARY KEY (`user_id`),
    ADD UNIQUE KEY `user_email` (`user_email`),
    ADD UNIQUE KEY `user_pseudo` (`user_pseudo`);

ALTER TABLE `cmw_users`
    MODIFY `user_id` INT(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `cmw_users_roles`
    ADD PRIMARY KEY (`id`),
    ADD KEY `user_id` (`user_id`),
    ADD KEY `role_id` (`role_id`);

ALTER TABLE `cmw_users_roles`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `cmw_users_roles`
    ADD CONSTRAINT `cmw_users_roles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `cmw_users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `cmw_users_roles_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `cmw_roles` (`role_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

ALTER TABLE `cmw_users_pictures`
    ADD CONSTRAINT `cmw_users_pictures_ibfk_1` FOREIGN KEY (`users_pictures_user_id`) REFERENCES `cmw_users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

CREATE TABLE IF NOT EXISTS cmw_roles_permissions
(
    permission_id int not null,
    role_id       int not null,
    primary key (role_id, permission_id),
    constraint FK_ROLE_PERMISSION_PERMISSION_ID
        foreign key (permission_id) references cmw_permissions (permission_id),
    constraint FK_ROLE_PERMISSION_ROLE_ID
        foreign key (role_id) references cmw_roles (role_id)
) ENGINE = InnoDB
  charset = utf8mb4;

CREATE TABLE IF NOT EXISTS `cmw_theme_config`
(
    `theme_config_id`    INT          NOT NULL AUTO_INCREMENT,
    `theme_config_name`  VARCHAR(255) NOT NULL,
    `theme_config_value` MEDIUMTEXT   NULL,
    `theme_config_theme` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`theme_config_id`)
) ENGINE = InnoDB
  CHARSET = utf8mb4;

CREATE INDEX role_id
    ON cmw_roles_permissions (role_id);

INSERT INTO `cmw_core_options` (`option_name`, `option_value`, `option_updated`)
VALUES ('theme', 'Sampler', NOW()),
       ('captcha', 'none', NOW());


INSERT INTO `cmw_roles` (`role_name`, `role_description`, `role_weight`)
VALUES ('Visiteur', 'Rôle pour les visiteurs', 0),
       ('Utilisateur', 'Rôle pour les utilisateurs', 1),
       ('Editeur', 'Rôle pour les éditeurs', 5),
       ('Modérateur', 'Rôle pour les modérateurs', 10),
       ('Administrateur', 'Rôle pour les administrateurs', 100);


INSERT INTO `cmw_permissions` (`permission_id`, `permission_parent_id`, `permission_code`)
VALUES (1, NULL, 'operator'),
       (2, NULL, 'pages'),
       (3, NULL, 'core'),
       (4, NULL, 'users'),
       (5, 2, 'show'),
       (6, 2, 'add'),
       (7, 2, 'edit'),
       (8, 3, 'dashboard'),
       (9, 4, 'show'),
       (10, 4, 'add'),
       (11, 4, 'edit'),
       (12, 4, 'delete'),
       (13, 4, 'roles');

#Insert operator permission for the admin role
INSERT INTO `cmw_roles_permissions` (`permission_id`, `role_id`)
VALUES ('1', '5');

#Insert the default profile picture image
INSERT INTO `cmw_users_settings` (users_settings_name, users_settings_value)
VALUES ('defaultImage', 'defaultImage.jpg')
