<?php
/**
 * SQLQueries.php
 *
 * Authors:Matthew
 * Date: 17/01/2021
 *
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

    public function checkUserRole()
    {
        $query_string  = "SELECT user_type ";
        $query_string .= "FROM user_data ";
        $query_string .= "WHERE user_email_address = :user_email;";
        return $query_string;
    }

    public function searchUser()
    {
        $query_string  = "SELECT user_email_address ";
        $query_string .= "FROM user_data ";
        $query_string .= "WHERE user_email_address LIKE '% :searchInput %'";
        $query_string .= "OR user_firstname LIKE '% :searchInput %'";
        $query_string .= "ORDER BY user_email_address LIMIT 5;";
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
        $query_string .= "meeting_start = :meeting_start,";
        $query_string .= "meeting_end = :meeting_end,";
        $query_string .= "meeting_host = :meeting_host,";
        $query_string .= "subject = :meeting_subject,";
        $query_string .= "notes = :notes;";
        return $query_string;
    }
    public function  checkTimeslot()
    {

        $query_string  = "SELECT meeting_data.meeting_host, meeting_user.user_email ";
        $query_string .= "FROM meeting_data JOIN meeting_user ON meeting_data.meetingId = meeting_user.meetingId ";
        $query_string .= "WHERE meeting_start BETWEEN :meeting_start  ";
        $query_string .= "AND :meeting_end ";
        $query_string .= "OR meeting_end BETWEEN :meeting_start  ";
        $query_string .="AND :meeting_end; ";
        return $query_string;
    }
    public function  checkTimeslotEx()
    {

        $query_string  = "SELECT meeting_data.meeting_host, meeting_user.user_email ";
        $query_string .= "FROM meeting_data JOIN meeting_user ON meeting_data.meetingId = meeting_user.meetingId ";
        $query_string .= "WHERE meeting_start BETWEEN :meeting_start  ";
        $query_string .= "AND :meeting_end ";
        $query_string .= "AND meeting_data.meetingId != :meetingId ";
        $query_string .= "OR meeting_end BETWEEN :meeting_start  ";
        $query_string .="AND :meeting_end ";
        $query_string .= "AND meeting_data.meetingId != :meetingId; ";
        return $query_string;
    }

//stop when the start time of a new meeting is between the start and end of an existing meeting or when the end time is between the start and end of an existing meeting
    public function  createNewMeetingUser()
    {
        $query_string = "INSERT INTO meeting_user ";
        $query_string .= "SET ";
        $query_string .= "user_email = :meeting_user,";
        $query_string .= "meeting_ack = :meeting_ack,";
        $query_string .= "meetingId = :meetingId;";
        return $query_string;
    }

    public function  createNewMeetingRecursion()
    {
        $query_string = "INSERT INTO meeting_recursion ";
        $query_string .= "SET ";
        $query_string .= "recursion_type = :recursion_type,";
        $query_string .= "recursion_active = :recursion_active,";
        $query_string .= "meetingId = :meetingId;";
        return $query_string;
    }

    public function  createNewEvent()
    {
        $query_string = "INSERT INTO event_data ";
        $query_string .= "SET ";
        $query_string .= "event_start_time = :event_start_time,";
        $query_string .= "event_duration = :event_duration,";
        $query_string .= "event_description = :event_description,";
        $query_string .= "event_date = :event_date,";
        $query_string .= "event_day = :event_day,";
        $query_string .= "event_month = :event_month,";
        $query_string .= "event_active = :event_active,";
        $query_string .= "user_email = :user_email;";
        return $query_string;
    }

    public function  checkEventByDayUser()
    {
        $query_string = "SELECT event_start_time, event_duration, event_description ";
        $query_string .= "FROM event_data ";
        $query_string .= "WHERE event_day = :event_day ";
        $query_string .= "AND user_email = :user_email ";
        $query_string .= "AND event_active = :event_active;";
        return $query_string;
    }
    public function  getAllEventDetails()
    {
        $query_string = "SELECT event_start_time, event_duration, event_description, event_date, event_day, event_month, eventId ";
        $query_string .= "FROM event_data ";
        $query_string .= "WHERE user_email = :user_email ";
        $query_string .= "AND event_active = :event_active;";
        return $query_string;
    }

    public function  checkEventByDateUser()
    {
        $query_string = "SELECT event_start_time, event_duration, event_description ";
        $query_string .= "FROM event_data ";
        $query_string .= "WHERE event_date = :event_date ";
        $query_string .= "AND user_email = :user_email ";
        $query_string .= "AND event_active = :event_active;";
        return $query_string;
    }
    public function  checkEventByMonthUser()
    {
        $query_string = "SELECT event_start_time, event_duration, event_description ";
        $query_string .= "FROM event_data ";
        $query_string .= "WHERE event_month = :event_month ";
        $query_string .= "AND user_email = :user_email ";
        $query_string .= "AND event_active = :event_active;";
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

    public function getUnacceptedMeetings()
    {
        $query_string  = "SELECT meeting_user.meetingId, meeting_data.meeting_date, meeting_data.meeting_time, meeting_data.meeting_duration, meeting_data.meeting_host, meeting_user.meeting_ack ";
        $query_string .= "FROM meeting_user JOIN meeting_data ON meeting_user.meetingId = meeting_data.meetingId ";
        $query_string .= "WHERE meeting_ack = :meeting_ack ";
        $query_string .= "AND user_email = :user_email ;";
        return $query_string;
    }

    public function getMeetingByEmailId()
    {
        $query_string  = "SELECT * ";
        $query_string .= "FROM meeting_data ";
        $query_string .= "WHERE meetingId = :MiD ;";

        return $query_string;
    }

    public function getMeetingByEmailMeetingId()
    {
        $query_string  = "SELECT * ";
        $query_string .= "FROM meeting_data ";
        $query_string .= "WHERE meetingId = :MiD ";
        $query_string .= "AND meeting_host = :user_email ;";

        return $query_string;
    }
    public function getMeetingRecursionByID()
    {
        $query_string  = "SELECT recursion_type ";
        $query_string .= "FROM meeting_recursion ";
        $query_string .= "WHERE meetingId = :MiD; ";


        return $query_string;
    }

    public function getUsersByEmailMeetingId()
    {
        $query_string  = "SELECT * ";
        $query_string .= "FROM meeting_user ";
        $query_string .= "WHERE meetingId = :MiD; ";

        return $query_string;
    }

    public function getAllUsers()
    {
        $query_string  = "SELECT user_email_address, user_firstname, user_lastname, user_sim, user_type ";
        $query_string .= "FROM user_data ";
        $query_string .= "WHERE user_email_address != :email; ";


        return $query_string;
    }

    public function updateMeetingAck()
    {
        $query_string  = "UPDATE meeting_user ";
        $query_string .=" SET meeting_ack = 1 " ;
        $query_string .= "WHERE meetingId = :MiD ";
        $query_string .= "AND user_email = :user_email;" ;
        return $query_string;
    }

    public function updateRoleStudent()
    {
        $query_string  = "UPDATE user_data ";
        $query_string .=" SET user_type = 'Student' " ;
        $query_string .= "WHERE user_email_address = :UiD;" ;
        return $query_string;
    }

    public function updateRoleTeacher()
    {
        $query_string  = "UPDATE user_data ";
        $query_string .=" SET user_type = 'Teacher' " ;
        $query_string .= "WHERE user_email_address = :UiD;" ;
        return $query_string;
    }

    public function getStudents()
    {
        $query_string  = "Select user_email_address, user_firstname, user_lastname, user_sim";
        $query_string .=" FROM user_data " ;
        $query_string .= "WHERE user_type = :Student ;" ;
        return $query_string;
    }

    public function updateUser()
    {
        $query_string  = "UPDATE user_data ";
        $query_string .=" SET user_firstname = :firstname , " ;
        $query_string .= "user_lastname = :lastname , ";
        $query_string .= "user_sim = :sim  ";
        $query_string .= "WHERE user_email_address = :user_email;" ;
        return $query_string;
    }

    public function getUserInfo()
    {
        $query_string  = "SELECT user_email_address, user_firstname, user_lastname, user_sim ";
        $query_string .= "FROM user_data ";
        $query_string .= "WHERE user_email_address = :email; ";
        return $query_string;
    }

    public function deleteUser()
    {
        $query_string  = "DELETE FROM user_data ";
        $query_string .= "WHERE user_email_address = :UiD;" ;
        return $query_string;
    }

    public function deleteUserMeetings()
    {
        $query_string  = "DELETE FROM meeting_data ";
        $query_string .= "WHERE meeting_host = :UiD;" ;
        return $query_string;
    }
    public function deleteUserMeetingsPart()
    {
        $query_string  = "DELETE FROM meeting_user ";
        $query_string .= "WHERE user_email = :UiD;" ;
        return $query_string;
    }
    public function deleteMeetingAck()
    {
        $query_string  = "DELETE ";
        $query_string .="FROM meeting_user ";
        $query_string .= "WHERE meetingId = :MiD ";
        $query_string .= "AND user_email = :user_email;" ;
        return $query_string;
    }
    public function deleteEvent()
    {
        $query_string  = "DELETE ";
        $query_string .="FROM event_data ";
        $query_string .= "WHERE eventId = :MiD ";
        $query_string .= "AND user_email = :user_email;" ;
        return $query_string;
    }
    public function deleteAllMeetingUser()
    {
        $query_string  = "DELETE ";
        $query_string .="FROM meeting_user ";
        $query_string .= "WHERE meetingId = :MiD; ";
        return $query_string;
    }

    public function deleteMeetingData()
    {
        $query_string  = "DELETE ";
        $query_string .="FROM meeting_data ";
        $query_string .= "WHERE meetingId = :MiD ";
        $query_string .= "AND meeting_host = :user_email;" ;
        return $query_string;
    }
    public function updateEventStatus()
    {
        $query_string  = "UPDATE event_data ";
        $query_string .=" SET event_active = :State " ;
        $query_string .= "WHERE eventId = :MiD ";
        $query_string .= "AND user_email = :user_email;" ;
        return $query_string;
    }

    public function updateMeetingRecursion()
    {
        $query_string  = "UPDATE meeting_recursion ";
        $query_string .=" SET recursion_active = :State " ;
        $query_string .= "WHERE meetingId = :MiD ";
        return $query_string;
    }


    public static function getMeetingByDateUser()
    {
        $query_string  = "SELECT DISTINCT meeting_data.meeting_time,meeting_data.meeting_date, meeting_data.meeting_duration, meeting_data.meeting_host, meeting_user.user_email, meeting_data.meetingId " ;
        $query_string .= "FROM meeting_data JOIN meeting_user ON meeting_data.meetingId = meeting_user.meetingId ";
        $query_string .= "WHERE meeting_user.user_email = :user_email ";
        $query_string .= "AND meeting_data.meeting_date = :meeting_date ";
        $query_string .= "AND meeting_user.meeting_ack = :meeting_ack ";
        $query_string .= " OR meeting_data.meeting_host = :user_email_2 ";
        $query_string .= "AND meeting_data.meeting_date = :meeting_date_2 ";

        return $query_string;
    }
    public static function getRecurringMeetingsHost()
    {
        $query_string  = "SELECT meeting_data.meetingId, meeting_data.meeting_start, meeting_data.meeting_duration, meeting_recursion.recursion_type, meeting_data.subject, meeting_data.meeting_time " ;
        $query_string .= "FROM meeting_data  ";
        $query_string .= "JOIN meeting_recursion on meeting_data.meetingId = meeting_recursion.meetingId ";
        $query_string .= "WHERE meeting_data.meeting_host = :meeting_host  ";
        $query_string .= "AND meeting_recursion.recursion_active = :active;";


        return $query_string;
    }

    public static function getRecurringMeetingsHostEx()
    {
        $query_string  = "SELECT meeting_data.meetingId, meeting_data.meeting_start, meeting_data.meeting_duration, meeting_recursion.recursion_type, meeting_data.subject, meeting_data.meeting_time " ;
        $query_string .= "FROM meeting_data  ";
        $query_string .= "JOIN meeting_recursion on meeting_data.meetingId = meeting_recursion.meetingId ";
        $query_string .= "WHERE meeting_data.meeting_host = :meeting_host  ";
        $query_string .= "AND meeting_data.meetingId != :meetingId  ";
        $query_string .= "AND meeting_recursion.recursion_active = :active;";


        return $query_string;
    }

    public static function getIdForRecurringMeeting()
    {
        $query_string  = "SELECT meeting_user.meetingId " ;
        $query_string .= "FROM meeting_user  ";
        $query_string .= "JOIN meeting_recursion on meeting_user.meetingId = meeting_recursion.meetingId ";
        $query_string .= "WHERE meeting_user.user_email = :user_email  ";
        $query_string .= "AND meeting_recursion.recursion_active = :active ";
        $query_string .= "AND meeting_user.meeting_ack = :acknowledged;";


        return $query_string;
    }
    public static function getIdForRecurringMeetingEx()
    {
        $query_string  = "SELECT meeting_user.meetingId " ;
        $query_string .= "FROM meeting_user  ";
        $query_string .= "JOIN meeting_recursion on meeting_user.meetingId = meeting_recursion.meetingId ";
        $query_string .= "WHERE meeting_user.user_email = :user_email  ";
        $query_string .= "AND meeting_recursion.recursion_active = :active ";
        $query_string .= "AND meeting_user.meetingId != :meetingId ";
        $query_string .= "AND meeting_user.meeting_ack = :acknowledged;";


        return $query_string;
    }
    public static function getStartDurationById()
    {
        $query_string  = "SELECT meeting_host " ;
        $query_string .= "FROM meeting_data  ";
        $query_string .= "WHERE meetingId = :MId ;";

        return $query_string;
    }



    public static function getUpcomingMeetingByUser()
    {
        $query_string  = "SELECT meeting_data.meeting_time,meeting_data.meeting_date, 
        meeting_data.meeting_duration, meeting_data.meeting_host, meeting_user.user_email, meeting_data.subject, meeting_data.meetingId  " ;
        $query_string .= "FROM meeting_data JOIN meeting_user ON meeting_data.meetingId = meeting_user.meetingId ";
        $query_string .= "WHERE meeting_user.user_email = :user_email ";
        $query_string .= "AND meeting_data.meeting_host != :user_email ";
        $query_string .= "AND meeting_data.meeting_start > :meeting_start ";
        $query_string .= "AND meeting_user.meeting_ack = :meeting_ack ";
        $query_string .= " OR meeting_data.meeting_host = :user_email_2 ";
        $query_string .= "AND meeting_data.meeting_start > :meeting_start_2 ";
        $query_string .= "ORDER BY meeting_data.meeting_start DESC;";

        return $query_string;
    }
    public static function getPastMeetingByUser()
    {
        $query_string  = "SELECT meeting_data.meeting_time,meeting_data.meeting_date, 
        meeting_data.meeting_duration, meeting_data.meeting_host, meeting_user.user_email, meeting_data.subject, meeting_data.meetingId  " ;
        $query_string .= "FROM meeting_data JOIN meeting_user ON meeting_data.meetingId = meeting_user.meetingId ";
        $query_string .= "WHERE meeting_user.user_email = :user_email ";
        $query_string .= "AND meeting_data.meeting_host != :user_email ";
        $query_string .= "AND meeting_data.meeting_start < :meeting_start ";
        $query_string .= "AND meeting_user.meeting_ack = :meeting_ack ";
        $query_string .= " OR meeting_data.meeting_host = :user_email_2 ";
        $query_string .= "AND meeting_data.meeting_start < :meeting_start_2 ";
        $query_string .= "ORDER BY meeting_data.meeting_start DESC;";

        return $query_string;
    }
public static function getMeetingNotes()
{
    $query_string  = "SELECT meeting_data.notes " ;
    $query_string .= "FROM meeting_data  ";
    $query_string .= "WHERE meetingId = :MiD;";
    return $query_string;

}

    public function storeProfilePic()
    {
        $query_string  = "INSERT INTO user_image ";
        $query_string .= "SET ";
        $query_string .= "user_email = :user_email_address , ";
        $query_string .= "image_name = :name ;";
        return $query_string;
    }
    public function deleteProfilePic()
    {
        $query_string  = "DELETE FROM user_image ";
        $query_string .= "WHERE ";
        $query_string .= "user_email = :user_email_address;";
        return $query_string;
    }

//    public function updateProfilePic()
//    {
//        $query_string  = "UPDATE user_image ";
//        $query_string .= "SET image_name = :name  ";
//        $query_string .= "WHERE user_email = :user_email_address ;";
//        return $query_string;
//    }
    public function fetchProfilePic()
    {
        $query_string  ="SELECT image_name ";
        $query_string .= "FROM user_image ";
        $query_string .="WHERE user_email = :id;";
        return $query_string;
    }

    public function storeMeetingDoc()
    {
        $query_string  = "INSERT INTO meeting_docs ";
        $query_string .= "SET ";
        $query_string .= "doc_name = :name , ";
        $query_string .= "meetingId = :meetingId ;";
        return $query_string;
    }

    public function fetchMeetingDoc()
    {
        $query_string  ="SELECT doc_name ";
        $query_string .= "FROM meeting_docs ";
        $query_string .="WHERE meetingId = :id;";
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

    public static function getNumberByUser()
    {
        $query_string  = "SELECT user_sim " ;
        $query_string .= "FROM user_data ";
        $query_string .= "WHERE user_email_address = :user_email ;";
        return $query_string;
    }

    public static function selectHashedPassword()
    {
        $query_string = "SELECT user_hashed_password ";
        $query_string .= "FROM user_data ";
        $query_string .= "WHERE user_email_address = :user_email_address";
        return $query_string;
    }


    public static function getEmail()
    {
        $query_string  = "SELECT user_email_address " ;
        $query_string .= "FROM user_data ";
        $query_string .= "WHERE user_session_id = :user_session_id ";
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

    public function checkEmailPresent()
    {
        $query_string  = "SELECT user_email_address ";
        $query_string .= "FROM user_data ";
        $query_string .= "WHERE user_email_address = :user_email_address;";
        return $query_string;
    }

}