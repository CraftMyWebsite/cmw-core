CREATE TABLE IF NOT EXISTS `cmw_core_options`
(
    `option_name`    varchar(255) NOT NULL,
    `option_value`   varchar(255) NOT NULL,
    `option_updated` timestamp    NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE IF NOT EXISTS `cmw_permissions`
(
    `permission_id`          int(11) NOT NULL,
    `permission_code`        varchar(255) NOT NULL,
    `permission_description` varchar(255) NOT NULL,
    `role_id`                int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `cmw_roles`
(
    `role_id`          int(11) DEFAULT NULL,
    `role_name`        tinytext NOT NULL,
    `role_description` text,
    `role_weight`           int  DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE IF NOT EXISTS `cmw_users`
(
    `user_id`        int(11) NOT NULL,
    `user_email`     varchar(255) NOT NULL,
    `user_pseudo`    varchar(255)          DEFAULT NULL,
    `user_firstname` varchar(255)          DEFAULT NULL,
    `user_lastname`  varchar(255)          DEFAULT NULL,
    `user_password`  varchar(255)          DEFAULT NULL,
    `user_state`     tinyint(1) NOT NULL DEFAULT '1',
    `user_key`       varchar(255) NOT NULL,
    `user_created`   timestamp    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `user_updated`   timestamp    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `user_logged`    timestamp    NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `cmw_users_roles`
(
    `id` int(11) NOT NULL,
    `user_id` int(11) NOT NULL,
    `role_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


ALTER TABLE `cmw_core_options`
    ADD UNIQUE KEY `option_name` (`option_name`);

ALTER TABLE `cmw_permissions`
    ADD PRIMARY KEY (`permission_id`),
    ADD KEY `permission_role_id` (`role_id`);

ALTER TABLE `cmw_roles`
    ADD PRIMARY KEY (`role_id`);

ALTER TABLE `cmw_roles`
    MODIFY `role_id` INT (11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `cmw_permissions`
    MODIFY `permission_id` INT (11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `cmw_users`
    ADD PRIMARY KEY (`user_id`),
    ADD UNIQUE KEY `user_email` (`user_email`),
    ADD UNIQUE KEY `user_pseudo` (`user_pseudo`);

ALTER TABLE `cmw_users`
    MODIFY `user_id` INT (11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `cmw_permissions`
    ADD CONSTRAINT `cmw_permissions_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `cmw_roles` (`role_id`) ON DELETE CASCADE ON UPDATE CASCADE;

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


INSERT INTO `cmw_core_options` (`option_name`, `option_value`, `option_updated`)
VALUES ('theme', 'Sampler', NOW());


INSERT INTO `cmw_roles` (`role_name`, `role_description`, `role_weight`)
VALUES  ('Visiteur', 'Rôle pour les visiteurs', 0),
        ('Utilisateur', 'Rôle pour les utilisateurs', 1),
        ('Editeur', 'Rôle pour les éditeurs', 5),
        ('Modérateur', 'Rôle pour les modérateurs', 10),
        ('Administrateur', 'Rôle pour les administrateurs', 100);

INSERT INTO `cmw_permissions` (`permission_code`, `permission_description`, `role_id`)
VALUES  ('*', 'Obtiens toutes les permissions', 5),
        ('pages.show', 'Afficher la liste des pages', 3),
        ('pages.add', 'Ajouts d''une page', 3),
        ('pages.edit', 'Modification d''une page', 3),
        ('pages.delete', 'Suppression d''une page', 3),
        ('core.dashboard', 'Accès au dashboard admin', 3),
        ('core.dashboard', 'Accès au dashboard admin', 4),
        ('users.show', 'Afficher la liste des utilisateurs', 4),
        ('users.add', 'Ajouts d''un utilisateur', 4),
        ('users.edit', 'Modification d''un utilisateur', 4),
        ('users.delete', 'Suppression d''un utilisateur', 4),
        ('users.roles', 'Gestion des rôles', 4);

