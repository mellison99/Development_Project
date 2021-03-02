<?php
/**
 * SQLQueries.php
 *
 * Authors: Hasan, Matthew
 * Date: 17/01/2021
 *
 * @author Hasan
 * @author Matthew
 */

namespace Meeting;

class SQLQueries
{
    public function __construct(){ }
    public function __destruct(){ }

    public function createUser()
    {

        $query_string = "INSERT INTO user_data ";
        $query_string .= "SET ";
        $query_string .= "user_email_address = :user_email_address, ";
        $query_string .= "user_sim = :user_sim, ";
        $query_string .= "user_firstname = :user_firstname, ";
        $query_string .= "user_lastname = :user_lastname, ";
        $query_string .= "user_type = :user_role, ";
        $query_string .= "user_hashed_password = :user_hashed_password;";

        return $query_string;
    }

    public function checkUserPresent()
    {
        $query_string  = "SELECT user_email_address ";
        $query_string .= "FROM user_data ";
        $query_string .= "WHERE user_email_address = :user_email_address;";
        return $query_string;
    }
    public function  createNewMeeting()
    {
        $query_string = "INSERT INTO meeting_data ";
        $query_string .= "SET ";
        $query_string .= "meetingId = :meetingId,";
        $query_string .= "meeting_date = :meeting_date,";
        $query_string .= "meeting_time = :meeting_time,";
        $query_string .= "meeting_duration = :meeting_duration,";
        $query_string .= "meeting_host = :meeting_host,";
        $query_string .= "notes = :notes;";
        return $query_string;
    }

    public function  createNewMeetingUser()
    {
        $query_string = "INSERT INTO meeting_user ";
        $query_string .= "SET ";
        $query_string .= "user_email = :meeting_user,";
        $query_string .= "meetingId = :meetingId;";
        return $query_string;
    }


    public function checkTimePresent()
    {
        $query_string  = "SELECT date_time_received ";
        $query_string .= "FROM message_test_data ";
        $query_string .= "WHERE date_time_received = :date_time_received;";
        return $query_string;
    }

    public function getUserEmail()
    {
        $query_string  = "SELECT user_email_address ";
        $query_string .= "FROM user_data;";
        return $query_string;
    }

    public function  createNewMessage()
    {
        $query_string = "INSERT INTO message_test_data ";
        $query_string .= "SET ";
        $query_string .= "switch_1 = :switch_1,";
        $query_string .= "switch_2 = :switch_2,";
        $query_string .= "switch_3 = :switch_3,";
        $query_string .= "switch_4 = :switch_4,";
        $query_string .= "fan = :fan,";
        $query_string .= "temperature = :temperature,";
        $query_string .= "last_digit = :last_digit,";
        $query_string .= "message_receiver = :message_receiver,";
        $query_string .= "date_time_received = :date_time_received,";
        $query_string .= "sender_email_address = :sender_email_address;";
        return $query_string;
    }
    public static function getMeetingByDateUser()
    {
        $query_string  = "SELECT meeting_data.meeting_time,meeting_data.meeting_date, meeting_data.meeting_host, meeting_user.user_email " ;
        $query_string .= "FROM meeting_data JOIN meeting_user ON meeting_data.meetingId = meeting_user.meetingId ";
        $query_string .= "WHERE meeting_user.user_email = :user_email ";
        $query_string .= "AND meeting_data.meeting_date = :meeting_date ";
        $query_string .= " OR meeting_data.meeting_host = :user_email_2 ";
        $query_string .= "AND meeting_data.meeting_date = :meeting_date_2 ";

        return $query_string;
    }

    public static function getMeetingByUser()
    {
        $query_string  = "SELECT meeting_data.meeting_time,meeting_data.meeting_date, meeting_data.meeting_host, meeting_user.user_email " ;
        $query_string .= "FROM meeting_data JOIN meeting_user ON meeting_data.meetingId = meeting_user.meetingId ";
        $query_string .= "WHERE meeting_user.user_email = :user_email ";
        $query_string .= " OR meeting_data.meeting_host = :user_email_2 ;";
        return $query_string;
    }

    public static function selectHashedPassword()
    {
        $query_string = "SELECT user_hashed_password ";
        $query_string .= "FROM user_data ";
        $query_string .= "WHERE user_email_address = :user_email_address";
        return $query_string;
    }

    public function getMessageBySimID()
    {
        $query_string  = "SELECT switch_1, switch_2, switch_3, switch_4, fan, temperature, last_digit, date_time_received, message_receiver, user_sim ";
        $query_string .= "FROM message_data  ";
        $query_string .= "WHERE message_data.user_sim = :user_sim;";
        return $query_string;
    }

    public function getMessageBySenderEmail()
    {
        $query_string  = "SELECT switch_1, switch_2, switch_3, switch_4, fan, temperature, last_digit, date_time_received, message_receiver ";
        $query_string .= "FROM message_test_data  ";
        $query_string .= "WHERE sender_email_address = :sender_email_address;";
        return $query_string;
    }

    public static function getEmail()
    {
        $query_string  = "SELECT user_email_address " ;
        $query_string .= "FROM user_data ";
        $query_string .= "WHERE user_session_id = :user_session_id ";
        return $query_string;
    }

    public function getSimId()
    {
        $query_string  = "SELECT user_sim ";
        $query_string .= "FROM meta_data  ";
        $query_string .= "WHERE user_email = :user_email;";
        return $query_string;
    }

    public function getSimbyEmail()
    {
        $query_string  = "SELECT user_sim ";
        $query_string .= "FROM user_data  ";
        $query_string .= "WHERE user_email = :sender_email;";
        return $query_string;
    }
    public function addMetaData()
    {
        $query_string = "INSERT INTO meta_data ";
        $query_string .= "SET ";
        $query_string .= "user_sim_id = :user_sim_id, ";
        $query_string .= "user_email = :user_email_address, ";
        $query_string .= "user_name = :user_name;";
        return $query_string;
    }
    public static function checkSessionID()
    {
        $query_string  = "SELECT user_session_id ";
        $query_string .= "FROM user_data ";
        $query_string .= "WHERE user_email_address = :user_email_address ";
        $query_string .= "LIMIT 1";
        return $query_string;
    }

    public static function createSessionVar()
    {
        $query_string  = "UPDATE user_data ";
        $query_string .= "SET user_session_id = :user_session_id ";
        $query_string .= "WHERE user_password = :user_password ";
        $query_string .= "AND user_email_address = :user_email_address";
        return $query_string;
    }

    public static function setSessionVar()
    {
        $query_string  = "UPDATE user_data ";
        $query_string .= "SET user_session_id = :user_session_id ";
        $query_string .= "WHERE user_email_address = :user_email_address";
        return $query_string;
    }

    public static function unsetSessionVar()
    {
        $query_string  = "UPDATE user_data ";
        $query_string .= "SET user_session_id = NULL ";
        $query_string .= "WHERE user_session_id = :user_session_id";
        return $query_string;
    }

    public function checkNumberPresent()
    {
        $query_string  = "SELECT user_sim_id ";
        $query_string .= "FROM meta_data ";
        $query_string .= "WHERE user_sim_id = :user_sim_id;";
        return $query_string;
    }
}