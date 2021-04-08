<?php
/**
 * CreateMeetingsModel.php
 *
 * Author: Matthew
 * Date: 21/02/2021
 *
 * @author Matthew
 */
namespace Meeting;


class CreateMeetingsModel
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

    public function storeMeetingDoc($app, $name, $cleaned_parameters)
    {
        $this->database_wrapper->setDatabaseConnectionSettings($this->database_connection_settings);
        $this->database_wrapper->makeDatabaseConnection();
        $query_string = $this->sql_queries->storeMeetingDoc();
//        var_dump($query_string);
        $query_parameters = [
            ':meetingId' => $cleaned_parameters['sanitised_Id'],
            ':name' => $name
        ];
//        var_dump($query_parameters);
        $this->database_wrapper->safeQuery($query_string, $query_parameters);
    }

    public function setMeetingDetails($app, $cleaned_parameters, $email)
    {
        $this->database_wrapper->setDatabaseConnectionSettings($this->database_connection_settings);
        $this->database_wrapper->makeDatabaseConnection();
        $query_string = $this->sql_queries->createNewMeeting();
        $query_parameters = [
            ':meetingId' => $cleaned_parameters['sanitised_Id'],
            ':meeting_date' => $cleaned_parameters['sanitised_date'],
            ':meeting_time' => $cleaned_parameters['sanitised_time'],
            ':meeting_duration' => $cleaned_parameters['sanitised_duration'],
            ':meeting_start' => $cleaned_parameters['sanitised_start'],
            ':meeting_end' => $cleaned_parameters['sanitised_end'],
            ':meeting_host' => $email,
            ':meeting_subject'=>$cleaned_parameters['sanitised_subject'],
            ':notes' => $cleaned_parameters['sanitised_notes']
        ];
        $this->database_wrapper->safeQuery($query_string, $query_parameters);

    }

    public function setMeetingUserDetails($app, $cleaned_parameters,$meetingID,$user)
    {
        $this->database_wrapper->setDatabaseConnectionSettings($this->database_connection_settings);
        $this->database_wrapper->makeDatabaseConnection();
        $query_string = $this->sql_queries->createNewMeetingUser();
        $query_parameters = [
            ':meeting_user' => $user,
            ':meeting_ack' => 0,
            ':meetingId' => $cleaned_parameters['sanitised_Id']

        ];
        //var_dump($query_string);
        //var_dump($query_parameters);
        $this->database_wrapper->safeQuery($query_string, $query_parameters);
    }

    public function setMeetingRecursionDetails($app, $cleaned_parameters)
    {
        $this->database_wrapper->setDatabaseConnectionSettings($this->database_connection_settings);
        $this->database_wrapper->makeDatabaseConnection();
        $query_string = $this->sql_queries->createNewMeetingRecursion();
        $query_parameters = [
            ':recursion_type' => $cleaned_parameters['sanitised_repeat'],
            ':recursion_active' => 1,
            ':meetingId' => $cleaned_parameters['sanitised_Id']

        ];
        //var_dump($query_string);
        //var_dump($query_parameters);
        $this->database_wrapper->safeQuery($query_string, $query_parameters);
    }

    public function setEventDetails($app, $cleaned_parameters)
    {

        $this->database_wrapper->setDatabaseConnectionSettings($this->database_connection_settings);
        $this->database_wrapper->makeDatabaseConnection();
        $query_string = $this->sql_queries->createNewEvent();
        $query_parameters = [
            ':event_start_time' => $cleaned_parameters['sanitised_time'],
            ':event_duration' => $cleaned_parameters['sanitised_duration'],
            ':event_description' => $cleaned_parameters['sanitised_description'],
            ':event_date' => $cleaned_parameters['sanitised_date'],
            ':event_day' => $cleaned_parameters['sanitised_dayOfWeek'],
            ':event_month' => $cleaned_parameters['sanitised_monthInYear'],
            ':event_active' => 1,
            ':user_email' => $cleaned_parameters['sanitised_email']

        ];
        //var_dump($query_string);
        //var_dump($query_parameters);
        $this->database_wrapper->safeQuery($query_string, $query_parameters);
    }


}