SET FOREIGN_KEY_CHECKS=0;

CREATE DATABASE IF NOT EXISTS meetingPlanner_db COLLATE utf8_unicode_ci;


--
-- Create the user account
--
CREATE USER IF NOT EXISTS `login_user`@localhost IDENTIFIED BY 'login_user_pass';
GRANT SELECT, INSERT ON meetingPlanner_db.* TO login_user@localhost;

USE meetingPlanner_db;


CREATE TABLE `user_data`(
`user_email_address` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci,
`user_password`varchar(256) CHARACTER SET utf8 COLLATE utf8_unicode_ci,
`user_sim` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci,
`user_type`varchar(30)CHARACTER SET utf8 COLLATE utf8_unicode_ci,
user_session_id varchar(40) CHARACTER SET utf8 COLLATE utf8_unicode_ci,
PRIMARY KEY (`user_email_address`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


CREATE TABLE `meeting_data`(
`meetingId` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci,
`meeting_time` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci,
`meeting_host` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci,
`notes`varchar(240) CHARACTER SET utf8 COLLATE utf8_unicode_ci,
PRIMARY KEY (`meetingId`),
CONSTRAINT `fk_host_email_address`
FOREIGN KEY (meeting_host) REFERENCES user_data(user_email_address)
ON DELETE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE `meeting_user`(
`user_meeting_id` int(4) NOT NULL AUTO_INCREMENT,
`meetingId` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci,
`user_email` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci,
PRIMARY KEY (`user_meeting_id`),
CONSTRAINT `fk_email_address`
FOREIGN KEY (user_email) REFERENCES user_data(user_email_address),
CONSTRAINT `fk_meetingId`
FOREIGN KEY (meetingId) REFERENCES meeting_data(meetingId)

ON DELETE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;



-- ----------------------------
-- Records of company_name
-- ----------------------------
