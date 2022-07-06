CREATE TABLE IF NOT EXISTS `cmw_core_options`
(
    `option_name`    varchar(255) NOT NULL,
    `option_value`   varchar(255) NOT NULL,
    `option_updated` timestamp    NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;


CREATE TABLE IF NOT EXISTS `cmw_roles_permissions`
(
    `role_permission_id`      int(11)      NOT NULL,
    `role_permission_code`    varchar(255) NOT NULL,
    `role_permission_role_id` int(11) DEFAULT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

CREATE TABLE IF NOT EXISTS `cmw_permissions_parent`
(
    `permission_parent_code`     varchar(255) NOT NULL,
    `permission_parent_package`  varchar(255) NOT NULL,
    `permission_parent_editable` smallint(6)  NOT NULL DEFAULT '0'
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

CREATE TABLE IF NOT EXISTS `cmw_permissions_child`
(
    `permission_child_code`     varchar(255) NOT NULL,
    `permission_child_parent`   varchar(255) NOT NULL,
    `permission_child_editable` smallint(6)  NOT NULL DEFAULT '0'
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

CREATE TABLE IF NOT EXISTS `cmw_permissions_desc`
(
    `permission_desc_id`          int(11)      NOT NULL,
    `permission_desc_code_parent` varchar(255) DEFAULT NULL,
    `permission_desc_code_child`  varchar(255) DEFAULT NULL,
    `permission_desc_lang`        varchar(20)  NOT NULL,
    `permission_desc_value`       varchar(255) NOT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

# MY PROPOSITION !

create table cmw_permissions2
(
    permission_id        int auto_increment
        primary key,
    permission_parent_id int                         null,
    permission_code      varchar(50) charset utf8mb4 not null,
    permission_editable  smallint                    not null,
    constraint FK_PERMISSION_PARENT_ID
        foreign key (permission_parent_id) references cmw_permissions2 (permission_id)
            on update cascade on delete cascade
);

INSERT INTO `cmw_permissions2` (`permission_id`, `permission_parent_id`, `permission_code`, `permission_editable`)
VALUES (1, NULL, 'users', 0),
       (2, 1, 'edit', 0),
       (3, 1, 'add', 0),
       (4, NULL, 'core', 0),
       (5, 4, 'dashboard', 0);

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


ALTER TABLE `cmw_core_options`
    ADD UNIQUE KEY `option_name` (`option_name`);

ALTER TABLE `cmw_roles_permissions`
    ADD PRIMARY KEY (`role_permission_id`),
    ADD KEY `permission_role_id` (`role_permission_role_id`);

ALTER TABLE `cmw_roles`
    ADD PRIMARY KEY (`role_id`);

ALTER TABLE `cmw_roles`
    MODIFY `role_id` INT(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `cmw_roles_permissions`
    MODIFY `role_permission_id` INT(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `cmw_users`
    ADD PRIMARY KEY (`user_id`),
    ADD UNIQUE KEY `user_email` (`user_email`),
    ADD UNIQUE KEY `user_pseudo` (`user_pseudo`);

ALTER TABLE `cmw_users`
    MODIFY `user_id` INT(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `cmw_roles_permissions`
    ADD CONSTRAINT `cmw_roles_permissions_ibfk_1` FOREIGN KEY (`role_permission_role_id`) REFERENCES `cmw_roles` (`role_id`) ON DELETE CASCADE ON UPDATE CASCADE;

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

ALTER TABLE `cmw_permissions_child`
    ADD UNIQUE KEY `code` (`permission_child_code`),
    ADD KEY `parent` (`permission_child_parent`);

ALTER TABLE `cmw_permissions_desc`
    ADD PRIMARY KEY (`permission_desc_id`),
    ADD KEY `permission_code_parent` (`permission_desc_code_parent`),
    ADD KEY `permission_code_child` (`permission_desc_code_child`);

ALTER TABLE `cmw_permissions_parent`
    ADD UNIQUE KEY `code` (`permission_parent_code`);

ALTER TABLE `cmw_permissions_desc`
    MODIFY `permission_desc_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `cmw_permissions_child`
    ADD CONSTRAINT `cmw_permissions_child_ibfk_1` FOREIGN KEY (`permission_child_parent`) REFERENCES `cmw_permissions_parent` (`permission_parent_code`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `cmw_permissions_desc`
    ADD CONSTRAINT `cmw_permissions_desc_ibfk_1` FOREIGN KEY (`permission_desc_code_parent`) REFERENCES `cmw_permissions_parent` (`permission_parent_code`) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `cmw_permissions_desc_ibfk_2` FOREIGN KEY (`permission_desc_code_child`) REFERENCES `cmw_permissions_child` (`permission_child_code`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;


INSERT INTO `cmw_core_options` (`option_name`, `option_value`, `option_updated`)
VALUES ('theme', 'Sampler', NOW());


INSERT INTO `cmw_roles` (`role_name`, `role_description`, `role_weight`)
VALUES ('Visiteur', 'Rôle pour les visiteurs', 0),
       ('Utilisateur', 'Rôle pour les utilisateurs', 1),
       ('Editeur', 'Rôle pour les éditeurs', 5),
       ('Modérateur', 'Rôle pour les modérateurs', 10),
       ('Administrateur', 'Rôle pour les administrateurs', 100);

INSERT INTO `cmw_roles_permissions` (`role_permission_code`, `role_permission_role_id`)
VALUES ('*', 5),
       ('pages.show', 3),
       ('pages.add', 3),
       ('pages.edit', 3),
       ('pages.delete', 3),
       ('core.dashboard', 3),
       ('core.dashboard', 4),
       ('users.show', 4),
       ('users.add', 4),
       ('users.edit', 4),
       ('users.delete', 4),
       ('users.roles', 4);