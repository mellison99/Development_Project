<?php


namespace Meeting;


class UpdateMeetingsModel
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

    public function updateMeetingAck($app,$meetingID,$user)
    {
        $this->database_wrapper->setDatabaseConnectionSettings($this->database_connection_settings);
        $this->database_wrapper->makeDatabaseConnection();
        $query_string = $this->sql_queries->updateMeetingAck();
        $query_parameters = [
            ':user_email' => $user,
            ':MiD' => $meetingID

        ];

        $this->database_wrapper->safeQuery($query_string, $query_parameters);
    }

    public function updateEventStatus($app,$meetingID,$user,$state)
    {
        $this->database_wrapper->setDatabaseConnectionSettings($this->database_connection_settings);
        $this->database_wrapper->makeDatabaseConnection();
        $query_string = $this->sql_queries->updateEventStatus();
        $query_parameters = [
            ':user_email' => $user,
            ':MiD' => $meetingID,
            ':State'=> $state,

        ];
        var_dump($query_string);
        var_dump($query_parameters);

        $this->database_wrapper->safeQuery($query_string, $query_parameters);
    }

    public function updateMeetingRecursion($app,$meetingID,$state)
    {
        $this->database_wrapper->setDatabaseConnectionSettings($this->database_connection_settings);
        $this->database_wrapper->makeDatabaseConnection();
        $query_string = $this->sql_queries->updateMeetingRecursion();
        $query_parameters = [
            ':MiD' => $meetingID,
            ':State'=> $state,

        ];
        var_dump($query_string);
        var_dump($query_parameters);

        $this->database_wrapper->safeQuery($query_string, $query_parameters);
    }

}