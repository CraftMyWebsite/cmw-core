CREATE TABLE `cmw_pages` (
    `page_id` int(11) NOT NULL,
    `user_id` int(11) NOT NULL,
    `page_title` varchar(255) NOT NULL,
    `page_content` longtext NOT NULL,
    `page_state` int(1) NOT NULL,
    `page_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `page_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `page_slug` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `cmw_pages`
    ADD PRIMARY KEY (`page_id`),
    ADD UNIQUE KEY `page_slug` (`page_slug`),
    ADD KEY `fk_pages_users` (`user_id`);


ALTER TABLE `cmw_pages`
    MODIFY `page_id` int(11) NOT NULL AUTO_INCREMENT;


ALTER TABLE `cmw_pages`
    ADD CONSTRAINT `fk_pages_users` FOREIGN KEY (`user_id`) REFERENCES `cmw_users` (`user_id`);
