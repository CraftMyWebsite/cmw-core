CREATE TABLE IF NOT EXISTS `cmw_pages`
(
    `page_id`      INT(11)      NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_id`      INT(11)      NULL,
    `page_title`   VARCHAR(255) NOT NULL,
    `page_content` LONGTEXT     NOT NULL,
    `page_state`   INT(1)       NOT NULL,
    `page_created` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `page_updated` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `page_slug`    VARCHAR(255) NOT NULL,
    CONSTRAINT fk_pages_users FOREIGN KEY (user_id) REFERENCES cmw_users (user_id) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;
