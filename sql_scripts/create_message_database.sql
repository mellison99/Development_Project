SET FOREIGN_KEY_CHECKS=0;

CREATE DATABASE IF NOT EXISTS user_db COLLATE utf8_unicode_ci;

CREATE USER IF NOT EXISTS `login_user`@localhost IDENTIFIED BY 'login_user_pass';
GRANT SELECT, INSERT, UPDATE, DELETE ON user_db.* TO login_user@localhost;

USE user_db;


CREATE TABLE `user_data`(
`user_email_address` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci,
`user_password`varchar(256) CHARACTER SET utf8 COLLATE utf8_unicode_ci,
user_session_id varchar(40) CHARACTER SET utf8 COLLATE utf8_unicode_ci,
PRIMARY KEY (`user_email_address`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


CREATE TABLE `meta_data`(
`user_email` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci,
`user_sim_id` varchar(12) CHARACTER SET utf8 COLLATE utf8_unicode_ci,
`user_name`varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci,
PRIMARY KEY (`user_sim_id`),
CONSTRAINT `fk_user_email_address`
FOREIGN KEY (user_email) REFERENCES user_data(user_email_address)
ON DELETE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE `message_data`(
`switch_1` varchar(4) CHARACTER SET utf8 COLLATE utf8_unicode_ci,
`switch_2` varchar(4) CHARACTER SET utf8 COLLATE utf8_unicode_ci,
`switch_3` varchar(4) CHARACTER SET utf8 COLLATE utf8_unicode_ci,
`switch_4` varchar(4) CHARACTER SET utf8 COLLATE utf8_unicode_ci,
`fan` varchar(8) CHARACTER SET utf8 COLLATE utf8_unicode_ci,
`temperature` varchar(4) CHARACTER SET utf8 COLLATE utf8_unicode_ci,
`last_digit` varchar(4) CHARACTER SET utf8 COLLATE utf8_unicode_ci,
`message_receiver` varchar(12) CHARACTER SET utf8 COLLATE utf8_unicode_ci,
`date_time_received`  varchar(40) CHARACTER SET utf8 COLLATE utf8_unicode_ci,
`sender_email_address`  varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci,
`user_sim` varchar(12) CHARACTER SET utf8 COLLATE utf8_unicode_ci,
PRIMARY KEY (`date_time_received`),
CONSTRAINT `fk_sim_id`
FOREIGN KEY (user_sim) REFERENCES meta_data(user_sim_id)
ON DELETE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE `message_test_data`(
                               `switch_1` varchar(4) CHARACTER SET utf8 COLLATE utf8_unicode_ci,
                               `switch_2` varchar(4) CHARACTER SET utf8 COLLATE utf8_unicode_ci,
                               `switch_3` varchar(4) CHARACTER SET utf8 COLLATE utf8_unicode_ci,
                               `switch_4` varchar(4) CHARACTER SET utf8 COLLATE utf8_unicode_ci,
                               `fan` varchar(8) CHARACTER SET utf8 COLLATE utf8_unicode_ci,
                               `temperature` varchar(4) CHARACTER SET utf8 COLLATE utf8_unicode_ci,
                               `last_digit` varchar(4) CHARACTER SET utf8 COLLATE utf8_unicode_ci,
                               `message_receiver` varchar(12) CHARACTER SET utf8 COLLATE utf8_unicode_ci,
                               `date_time_received`  varchar(40) CHARACTER SET utf8 COLLATE utf8_unicode_ci,
                               `sender_email_address`  varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci,

                               PRIMARY KEY (`date_time_received`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


-- ----------------------------
-- Records of company_name
-- ----------------------------
