CREATE TABLE IF NOT EXISTS `cmw_users_uuid`
(
    `user_id`    int(11)     NOT NULL,
    `user_uuid`  varchar(32) NOT NULL,
    `user_uuidf` varchar(36) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `cmw_users_uuid`
    ADD PRIMARY KEY (`user_id`),
    ADD UNIQUE KEY `user_id` (`user_id`);

ALTER TABLE `cmw_users_uuid`
    ADD CONSTRAINT `fk_uuid_users` FOREIGN KEY (`user_id`) REFERENCES `cmw_users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;




