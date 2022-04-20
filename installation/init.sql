CREATE TABLE `cmw_core_options`
(
    `option_id`      int(11) NOT NULL,
    `option_value`   varchar(255) NOT NULL,
    `option_name`    varchar(255) NOT NULL,
    `option_updated` timestamp    NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `cmw_menus`
(
    `menu_id`        int(11) NOT NULL,
    `menu_name`      varchar(255) NOT NULL,
    `menu_url`       varchar(255) NOT NULL,
    `menu_level`     int(1) NOT NULL,
    `menu_parent_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `cmw_pages`
(
    `page_id`      int(11) NOT NULL,
    `user_id`      int(11) NOT NULL,
    `page_title`   varchar(255) NOT NULL,
    `page_content` longtext     NOT NULL,
    `page_state`   int(1) NOT NULL,
    `page_created` timestamp    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `page_updated` timestamp    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `page_slug`    varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `cmw_permissions`
(
    `permission_id`          int(11) NOT NULL,
    `permission_code`        varchar(255) NOT NULL,
    `permission_description` varchar(255) NOT NULL,
    `role_id`                int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `cmw_roles`
(
    `role_id`          int(11) DEFAULT NULL,
    `role_name`        tinytext NOT NULL,
    `role_description` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `cmw_roles` (`role_id`, `role_name`, `role_description`)
VALUES (0, 'Visiteur', NULL),
       (1, 'Utilisateur', NULL),
       (2, 'Editeur', NULL),
       (3, 'Modérateur', NULL),
       (10, 'Administrateur', NULL);


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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


ALTER TABLE `cmw_core_options`
    ADD PRIMARY KEY (`option_id`),
  ADD UNIQUE KEY `option_name` (`option_name`);

ALTER TABLE `cmw_menus`
    ADD PRIMARY KEY (`menu_id`);

ALTER TABLE `cmw_pages`
    ADD PRIMARY KEY (`page_id`),
  ADD UNIQUE KEY `page_slug` (`page_slug`),
  ADD KEY `fk_pages_users` (`user_id`);

ALTER TABLE `cmw_permissions`
    ADD PRIMARY KEY (`permission_id`),
  ADD KEY `permission_role_id` (`role_id`);

ALTER TABLE `cmw_roles`
    ADD UNIQUE KEY `role_id` (`role_id`);

ALTER TABLE `cmw_users`
    ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `user_email` (`user_email`),
  ADD UNIQUE KEY `user_pseudo` (`user_pseudo`),
  ADD KEY `role_id` (`role_id`);

ALTER TABLE `cmw_menus`
    MODIFY `menu_id` int (11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `cmw_pages`
    MODIFY `page_id` int (11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `cmw_pages`
    ADD CONSTRAINT `fk_pages_users` FOREIGN KEY (`user_id`) REFERENCES `cmw_users` (`user_id`);

ALTER TABLE `cmw_permissions`
    ADD CONSTRAINT `cmw_permissions_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `cmw_roles` (`role_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `cmw_users`
    ADD CONSTRAINT `fk_users_roles` FOREIGN KEY (`role_id`) REFERENCES `cmw_roles` (`role_id`);
COMMIT;
