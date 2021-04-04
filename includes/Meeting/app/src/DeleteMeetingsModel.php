<?php
/**
 * DeleteMeetingsModel.php
 *
 * Author: Matthew
 * Date: 22/03/2021
 *
 * @author Matthew
 */

namespace Meeting;


class DeleteMeetingsModel
{
    private $database_wrapper;
    private $database_connection_settings;
    private $sql_queries;


    public function __construct()
    {
    }
    public function __destruct()
    {
    }

    public function setDatabaseWrapper($database_wrapper)
    {
        $this->database_wrapper = $database_wrapper;
    }
    public function setDatabaseConnectionSettings($database_connection_settings)
    {
        $this->database_connection_settings = $database_connection_settings;
    }

    public function setSqlQueries($sql_queries)
    {
        $this->sql_queries = $sql_queries;
    }

    public function deleteUserMeetings($app,$Uid)
    {
        $this->database_wrapper->setDatabaseConnectionSettings($this->database_connection_settings);
        $this->database_wrapper->makeDatabaseConnection();
        $query_string = $this->sql_queries->deleteUserMeetings();
        $query_parameters = [
            ':UiD' => $Uid,

        ];


        $this->database_wrapper->safeQuery($query_string, $query_parameters);
    }

    public function deleteUserMeetingsPart($app,$Uid)
    {
        $this->database_wrapper->setDatabaseConnectionSettings($this->database_connection_settings);
        $this->database_wrapper->makeDatabaseConnection();
        $query_string = $this->sql_queries->deleteUserMeetingsPart();
        $query_parameters = [
            ':UiD' => $Uid,

        ];


        $this->database_wrapper->safeQuery($query_string, $query_parameters);
    }

    public function deleteMeetingUser($app,$meetingID,$user)
    {
        $this->database_wrapper->setDatabaseConnectionSettings($this->database_connection_settings);
        $this->database_wrapper->makeDatabaseConnection();
        $query_string = $this->sql_queries->deleteMeetingAck();
        $query_parameters = [
            ':user_email' => $user,
            ':MiD' => $meetingID

        ];

        $this->database_wrapper->safeQuery($query_string, $query_parameters);
    }
    public function deleteAllMeetingUsers($app,$meetingID)
    {
        $this->database_wrapper->setDatabaseConnectionSettings($this->database_connection_settings);
        $this->database_wrapper->makeDatabaseConnection();
        $query_string = $this->sql_queries->deleteAllMeetingUser();
        $query_parameters = [
            ':MiD' => $meetingID

        ];
        $this->database_wrapper->safeQuery($query_string, $query_parameters);
    }
    public function deleteMeetingData($app,$meetingID, $user)
    {
        $this->database_wrapper->setDatabaseConnectionSettings($this->database_connection_settings);
        $this->database_wrapper->makeDatabaseConnection();
        $query_string = $this->sql_queries->deleteMeetingData();
        $query_parameters = [
            ':MiD' => $meetingID,
            ':user_email' => $user,

        ];
        $this->database_wrapper->safeQuery($query_string, $query_parameters);
    }

    public function deleteEventData($app,$meetingID,$user)
    {
        $this->database_wrapper->setDatabaseConnectionSettings($this->database_connection_settings);
        $this->database_wrapper->makeDatabaseConnection();
        $query_string = $this->sql_queries->deleteEvent();
        $query_parameters = [
            ':user_email' => $user,
            ':MiD' => $meetingID

        ];
        var_dump($query_string);
        var_dump($query_parameters);

        $this->database_wrapper->safeQuery($query_string, $query_parameters);
    }

}