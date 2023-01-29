CREATE TABLE IF NOT EXISTS `cmw_core_options`
(
    `option_name`    VARCHAR(255) NOT NULL,
    `option_value`   VARCHAR(255) NOT NULL,
    `option_updated` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
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

CREATE TABLE IF NOT EXISTS cmw_permissions
(
    permission_id        INT AUTO_INCREMENT
        PRIMARY KEY,
    permission_parent_id INT                         NULL,
    permission_code      VARCHAR(50) CHARSET utf8mb4 NOT NULL,
    CONSTRAINT FK_PERMISSION_PARENT_ID
        FOREIGN KEY (permission_parent_id) REFERENCES cmw_permissions (permission_id)
            ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE = InnoDB
  CHARSET = utf8mb4;


# END

CREATE TABLE IF NOT EXISTS `cmw_roles`
(
    `role_id`          INT(11) DEFAULT NULL,
    `role_name`        TINYTEXT NOT NULL,
    `role_description` TEXT,
    `role_weight`      INT     DEFAULT 0
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

CREATE TABLE IF NOT EXISTS `cmw_users`
(
    `user_id`        INT(11)      NOT NULL,
    `user_email`     VARCHAR(255) NOT NULL,
    `user_pseudo`    VARCHAR(255)          DEFAULT NULL,
    `user_firstname` VARCHAR(255)          DEFAULT NULL,
    `user_lastname`  VARCHAR(255)          DEFAULT NULL,
    `user_password`  VARCHAR(255)          DEFAULT NULL,
    `user_state`     TINYINT(1)   NOT NULL DEFAULT '1',
    `user_key`       VARCHAR(255) NOT NULL,
    `user_created`   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `user_updated`   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `user_logged`    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

CREATE TABLE IF NOT EXISTS `cmw_users_roles`
(
    `id`      INT(11) NOT NULL,
    `user_id` INT(11) NOT NULL,
    `role_id` INT(11) NOT NULL
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
    `users_settings_name`    VARCHAR(255) NOT NULL,
    `users_settings_value`   VARCHAR(255) NOT NULL,
    `users_settings_updated` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY (`users_settings_name`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

CREATE TABLE IF NOT EXISTS `cmw_menus`
(
    `menu_id`        INT(11)      NOT NULL,
    `menu_name`      VARCHAR(255) NOT NULL,
    `menu_url`       VARCHAR(255) NOT NULL,
    `menu_level`     INT(1)       NOT NULL,
    `menu_parent_id` INT(11)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

# Condition general !
CREATE TABLE IF NOT EXISTS `cmw_condition`
(
    `condition_id`      INT(11)    NOT NULL,
    `condition_content` LONGTEXT   NOT NULL,
    `condition_state`   TINYINT(1) NOT NULL DEFAULT '1',
    `condition_updated` TIMESTAMP  NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `condition_author`  INT(11)    NOT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

ALTER TABLE `cmw_condition`
    ADD PRIMARY KEY (`condition_id`);

ALTER TABLE `cmw_condition`
    MODIFY `condition_id` INT(11) NOT NULL AUTO_INCREMENT;

INSERT INTO `cmw_condition` (`condition_content`, `condition_author`)
VALUES ('Veuillez écrire votre CGV !', '1'),
       ('Veuillez écrire votre CGU !', '1');
# FIN : Condition general !

ALTER TABLE `cmw_menus`
    ADD PRIMARY KEY (`menu_id`);

ALTER TABLE `cmw_menus`
    MODIFY `menu_id` INT(11) NOT NULL AUTO_INCREMENT;

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
    MODIFY `id` INT(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `cmw_users_roles`
    ADD CONSTRAINT `cmw_users_roles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `cmw_users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `cmw_users_roles_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `cmw_roles` (`role_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

ALTER TABLE `cmw_users_pictures`
    ADD CONSTRAINT `cmw_users_pictures_ibfk_1` FOREIGN KEY (`users_pictures_user_id`) REFERENCES `cmw_users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

CREATE TABLE IF NOT EXISTS cmw_roles_permissions
(
    permission_id INT NOT NULL,
    role_id       INT NOT NULL,
    PRIMARY KEY (role_id, permission_id),
    CONSTRAINT FK_ROLE_PERMISSION_PERMISSION_ID
        FOREIGN KEY (permission_id) REFERENCES cmw_permissions (permission_id),
    CONSTRAINT FK_ROLE_PERMISSION_ROLE_ID
        FOREIGN KEY (role_id) REFERENCES cmw_roles (role_id)
) ENGINE = InnoDB
  CHARSET = utf8mb4;

CREATE TABLE IF NOT EXISTS `cmw_theme_config`
(
    `theme_config_id`    INT          NOT NULL AUTO_INCREMENT,
    `theme_config_name`  VARCHAR(255) NOT NULL,
    `theme_config_value` MEDIUMTEXT   NULL,
    `theme_config_theme` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`theme_config_id`)
) ENGINE = InnoDB
  CHARSET = utf8mb4;

CREATE TABLE IF NOT EXISTS `cmw_core_routes`
(
    `core_routes_id`            INT                                   NOT NULL AUTO_INCREMENT,
    `core_routes_slug`          VARCHAR(300)                          NOT NULL,
    `core_routes_package`       VARCHAR(50)                           NOT NULL DEFAULT 'core',
    `core_routes_title`         VARCHAR(75)                           NOT NULL,
    `core_routes_method`        VARCHAR(10)                           NOT NULL DEFAULT 'GET',
    `core_routes_is_admin`      TINYINT(1)                            NOT NULL DEFAULT '0',
    `core_routes_is_dynamic`    TINYINT(1)                            NOT NULL DEFAULT '0',
    `core_routes_weight`        INT                                   NOT NULL DEFAULT '1',
    `core_routes_last_edit`     TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `core_routes_date_creation` TIMESTAMP                             NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`core_routes_id`)
) ENGINE = InnoDB
  CHARSET = utf8mb4;

CREATE TABLE IF NOT EXISTS `cmw_visits`
(
    `visits_id`       INT(11)      NOT NULL AUTO_INCREMENT,
    `visits_ip`       VARCHAR(39)  NOT NULL,
    `visits_date`     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `visits_path`     VARCHAR(255) NOT NULL,
    `visits_package`  VARCHAR(255)          DEFAULT NULL,
    `visits_code`     INT(4)       NOT NULL,
    `visits_is_admin` TINYINT(4)   NOT NULL DEFAULT '0',
    PRIMARY KEY (`visits_id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

CREATE INDEX role_id
    ON cmw_roles_permissions (role_id);

INSERT INTO `cmw_core_options` (`option_name`, `option_value`, `option_updated`)
VALUES ('theme', 'Sampler', NOW()),
       ('captcha', 'none', NOW()),
       ('dateFormat', 'd-m-Y H:i:s', NOW());


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
VALUES ('defaultImage', 'defaultImage.jpg'),
       ('resetPasswordMethod', '0')
