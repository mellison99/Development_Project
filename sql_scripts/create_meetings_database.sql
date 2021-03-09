SET FOREIGN_KEY_CHECKS=0;

CREATE DATABASE IF NOT EXISTS meetingPlanner_db COLLATE utf8_unicode_ci;
DROP TABLE user_data, meeting_data, meeting_user, office_hours;

--
-- Create the user account
--
CREATE USER IF NOT EXISTS `login_user`@localhost IDENTIFIED BY 'login_user_pass';
GRANT SELECT, INSERT, UPDATE ON meetingPlanner_db.* TO login_user@localhost;

USE meetingPlanner_db;


CREATE TABLE `user_data`(
`user_email_address` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci,
`user_hashed_password`varchar(256) CHARACTER SET utf8 COLLATE utf8_unicode_ci,
`user_firstname` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci,
`user_lastname` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci,
`user_sim` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci,
`user_type`varchar(30)CHARACTER SET utf8 COLLATE utf8_unicode_ci,
user_session_id varchar(40) CHARACTER SET utf8 COLLATE utf8_unicode_ci,
PRIMARY KEY (`user_email_address`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


CREATE TABLE `meeting_data`(
`meetingId` int(11) NOT NULL,
`meeting_date` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci,
`meeting_time` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci,
`meeting_duration` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci,
`meeting_start` int(12),
`meeting_end` int(12),
`meeting_host` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci,
`notes`varchar(240) CHARACTER SET utf8 COLLATE utf8_unicode_ci,
PRIMARY KEY (`meetingId`),
CONSTRAINT `fk_host_email_address`
FOREIGN KEY (meeting_host) REFERENCES user_data(user_email_address)
ON DELETE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE `meeting_user`(
`user_meeting_id` int(11) NOT NULL AUTO_INCREMENT ,
`meetingId` int(11) NOT NULL,
`user_email` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci,
`meeting_ack` tinyint(1) NOT NULL,
PRIMARY KEY (`user_meeting_id`),
CONSTRAINT `fk_meetingId`
FOREIGN KEY (meetingId) REFERENCES meeting_data(meetingID)


) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE `office_hours`(
`user_office_hours` int(4) NOT NULL AUTO_INCREMENT,
`user_start_time` TIME,
`user_end_time` TIME,
`user_email` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci,
PRIMARY KEY (`user_office_hours`),
CONSTRAINT `fk_teacher_email`
FOREIGN KEY (user_email) REFERENCES user_data(user_email_address)
ON DELETE CASCADE

) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


-- ----------------------------
-- Records of company_name
-- ----------------------------
