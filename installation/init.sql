CREATE TABLE `cmw_core_options`
(
    `option_id`      int(11) NOT NULL,
    `option_value`   varchar(255) NOT NULL,
    `option_name`    varchar(255) NOT NULL,
    `option_updated` timestamp    NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

CREATE TABLE `cmw_roles`
(
    `role_id`          int(11) DEFAULT NULL,
    `role_name`        tinytext NOT NULL,
    `role_description` text DEFAULT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

CREATE TABLE `cmw_users`
(
    `user_id`        int(11) NOT NULL,
    `user_email`     varchar(255) NOT NULL,
    `user_pseudo`    varchar(255)          DEFAULT NULL,
    `user_firstname` varchar(255)          DEFAULT NULL,
    `user_lastname`  varchar(255)          DEFAULT NULL,
    `user_password`  varchar(255)          DEFAULT NULL,
    `user_state`     tinyint(1) NOT NULL DEFAULT '1',
    `role_id`        int(11) NOT NULL DEFAULT '1',
    `user_key`       varchar(255) NOT NULL,
    `user_created`   timestamp    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `user_updated`   timestamp    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `user_logged`    timestamp    NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

CREATE TABLE `cmw_permissions`
(
    `permission_id`          int(11) NOT NULL,
    `permission_code`        varchar(255) NOT NULL,
    `permission_libelle`     varchar(255) NOT NULL,
    `permission_description` varchar(255) NOT NULL,
    `permission_group_id`    int(11) DEFAULT NULL
) ENGINE=InnoDB
  DEFAULT CHARSET = utf8mb4;

CREATE TABLE `cmw_permissions_groups`
(
    `permission_group_id`          int(11) NOT NULL,
    `permission_group_libelle`     varchar(255) NOT NULL,
    `permission_group_description` varchar(255) NOT NULL,
    `permission_group_main_group`  varchar(255) NOT NULL,
    `permission_group_code`        varchar(255) NOT NULL
) ENGINE=InnoDB
  DEFAULT CHARSET = utf8mb4;

CREATE TABLE `cmw_permissions_groups_roles`
(
    `permission_id` int(11) NOT NULL,
    `role_id`       int(11) NOT NULL
) ENGINE=InnoDB
  DEFAULT CHARSET = utf8mb4;



ALTER TABLE `cmw_core_options`
    ADD PRIMARY KEY (`option_id`),
    ADD UNIQUE KEY `option_name` (`option_name`);

ALTER TABLE `cmw_roles`
    ADD UNIQUE KEY `role_id` (`role_id`);

ALTER TABLE `cmw_users`
    ADD PRIMARY KEY (`user_id`),
    ADD UNIQUE KEY `user_email` (`user_email`),
    ADD UNIQUE KEY `user_pseudo` (`user_pseudo`),
    ADD KEY `role_id` (`role_id`);

ALTER TABLE `cmw_core_options`
    MODIFY `option_id` int (11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `cmw_users`
    MODIFY `user_id` int (11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `cmw_users`
    ADD CONSTRAINT `fk_users_roles` FOREIGN KEY (`role_id`) REFERENCES `cmw_roles` (`role_id`);

ALTER TABLE `cmw_permissions`
    ADD PRIMARY KEY (`permission_id`);

ALTER TABLE `cmw_permissions_groups`
    ADD PRIMARY KEY (`permission_group_id`);

ALTER TABLE `cmw_permissions_groups_roles`
    ADD KEY `fk_permission` (`permission_id`),
    ADD KEY `fk_role` (`role_id`);

ALTER TABLE `cmw_permissions`
    MODIFY `permission_id` int (11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `cmw_permissions_groups`
    MODIFY `permission_group_id` int (11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `cmw_permissions_groups_roles`
    ADD CONSTRAINT `fk_permission` FOREIGN KEY (`permission_id`) REFERENCES `cmw_permissions` (`permission_id`),
    ADD CONSTRAINT `fk_role` FOREIGN KEY (`role_id`) REFERENCES `cmw_roles` (`role_id`);
COMMIT;


INSERT INTO `cmw_core_options` (`option_id`, `option_value`, `option_name`, `option_updated`)
VALUES (1, 'Sampler', 'theme', NOW());

INSERT INTO `cmw_roles` (`role_id`, `role_name`)
VALUES (0, 'Visiteur'),
       (1, 'Utilisateur'),
       (2, 'Editeur'),
       (3, 'Modérateur'),
       (10, 'Administrateur');