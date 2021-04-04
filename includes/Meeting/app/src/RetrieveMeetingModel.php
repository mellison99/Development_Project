<?php


namespace Meeting;


class RetrieveMeetingModel
{
    private $database_wrapper;
    private $database_connection_settings;
    private $sql_queries;

    public function __construct(){}

    public function __destruct(){}

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

    public function getAllEventDetails($app, $email, $pos): array
    {
        $value = [];
        $this->database_wrapper->setDatabaseConnectionSettings($this->database_connection_settings);
        $this->database_wrapper->makeDatabaseConnection();
        $query_string = $this->sql_queries->getAllEventDetails();
        $query_parameters = [
            ':user_email' => $email,
            'event_active' => 1
        ];
        $this->database_wrapper->safeQuery($query_string, $query_parameters);
        if ($this->database_wrapper->countRows() >= 0)
        {
            $x = $this->database_wrapper->countRows();
            for($i=$pos+1; $i<=$x; $i++)
                $value = $this->database_wrapper->safeFetchRow();
        }
        return $value;
    }
    public function getDisabledEventDetails($app, $email, $pos): array
    {
        $value = [];
        $this->database_wrapper->setDatabaseConnectionSettings($this->database_connection_settings);
        $this->database_wrapper->makeDatabaseConnection();
        $query_string = $this->sql_queries->getAllEventDetails();
        $query_parameters = [
            ':user_email' => $email,
            'event_active' => 0
        ];
        $this->database_wrapper->safeQuery($query_string, $query_parameters);
        if ($this->database_wrapper->countRows() >= 0)
        {
            $x = $this->database_wrapper->countRows();
            for($i=$pos+1; $i<=$x; $i++)
                $value = $this->database_wrapper->safeFetchRow();
        }
        return $value;
    }

    public function getRecurringMeetingsHost($app, $email)
    {
        $value = [];
        $this->database_wrapper->setDatabaseConnectionSettings($this->database_connection_settings);
        $this->database_wrapper->makeDatabaseConnection();
        $query_string = $this->sql_queries->getRecurringMeetingsHost();
        $query_parameters = [
            ':meeting_host' => $email,
            ':active' => 1
        ];
//         var_dump($query_parameters);
//        var_dump($query_string);
        $this->database_wrapper->safeQuery($query_string, $query_parameters);
        return $this->database_wrapper->countRows();
    }
    public function getRecurringMeetingsHostEx($app, $email,$MiD)
    {
        $value = [];
        $this->database_wrapper->setDatabaseConnectionSettings($this->database_connection_settings);
        $this->database_wrapper->makeDatabaseConnection();
        $query_string = $this->sql_queries->getRecurringMeetingsHostEx();
        $query_parameters = [
            ':meeting_host' => $email,
            ':active' => 1,
            ':meetingId'=> $MiD
        ];
//         var_dump($query_parameters);
//        var_dump($query_string);
        $this->database_wrapper->safeQuery($query_string, $query_parameters);
        return $this->database_wrapper->countRows();
    }

    public function getRecurringMeetingsHostDetails($app, $email, $pos): array
    {
        $value = [];
        $this->database_wrapper->setDatabaseConnectionSettings($this->database_connection_settings);
        $this->database_wrapper->makeDatabaseConnection();
        $query_string = $this->sql_queries->getRecurringMeetingsHost();
        $query_parameters = [
            ':meeting_host' => $email,
            ':active' => 1
        ];
//        var_dump($query_parameters);
//        var_dump($query_string);
        $this->database_wrapper->safeQuery($query_string, $query_parameters);
        if ($this->database_wrapper->countRows() >= 0)
        {
            $x = $this->database_wrapper->countRows();
            for($i=$pos+1; $i<=$x; $i++)
                $value = $this->database_wrapper->safeFetchRow();
        }
        return $value;
    }
    public function getRecurringMeetingsHostDetailsEx($app, $email, $pos,$MiD): array
    {
        $value = [];
        $this->database_wrapper->setDatabaseConnectionSettings($this->database_connection_settings);
        $this->database_wrapper->makeDatabaseConnection();
        $query_string = $this->sql_queries->getRecurringMeetingsHostEx();
        $query_parameters = [
            ':meeting_host' => $email,
            ':active' => 1,
            ':meetingId'=> $MiD
        ];
//        var_dump($query_parameters);
//        var_dump($query_string);
        $this->database_wrapper->safeQuery($query_string, $query_parameters);
        if ($this->database_wrapper->countRows() >= 0)
        {
            $x = $this->database_wrapper->countRows();
            for($i=$pos+1; $i<=$x; $i++)
                $value = $this->database_wrapper->safeFetchRow();
        }
        return $value;
    }


    public function getRecurringMeetingsHostDisabled($app, $email)
    {
        $value = [];
        $this->database_wrapper->setDatabaseConnectionSettings($this->database_connection_settings);
        $this->database_wrapper->makeDatabaseConnection();
        $query_string = $this->sql_queries->getRecurringMeetingsHost();
        $query_parameters = [
            ':meeting_host' => $email,
            ':active' => 0
        ];
//         var_dump($query_parameters);
//        var_dump($query_string);
        $this->database_wrapper->safeQuery($query_string, $query_parameters);
        return $this->database_wrapper->countRows();
    }

    public function getRecurringMeetingsHostDetailsDisabled($app, $email, $pos): array
    {
        $value = [];
        $this->database_wrapper->setDatabaseConnectionSettings($this->database_connection_settings);
        $this->database_wrapper->makeDatabaseConnection();
        $query_string = $this->sql_queries->getRecurringMeetingsHost();
        $query_parameters = [
            ':meeting_host' => $email,
            ':active' => 0
        ];
//        var_dump($query_parameters);
//        var_dump($query_string);
        $this->database_wrapper->safeQuery($query_string, $query_parameters);
        if ($this->database_wrapper->countRows() >= 0)
        {
            $x = $this->database_wrapper->countRows();
            for($i=$pos+1; $i<=$x; $i++)
                $value = $this->database_wrapper->safeFetchRow();
        }
        return $value;
    }
    public function getMeetingbyDateUser($app, $email,$date)
    {
        $value = [];
        $this->database_wrapper->setDatabaseConnectionSettings($this->database_connection_settings);
        $this->database_wrapper->makeDatabaseConnection();
        $query_string = $this->sql_queries->getMeetingByDateUser();
        //var_dump($query_string);
        $query_parameters = [
            ':user_email' => $email,
            ':meeting_date' => $date,
            ':meeting_ack' => 1,
            ':user_email_2' => $email,
            ':meeting_date_2' => $date
        ];
        //var_dump($query_parameters);
        $this->database_wrapper->safeQuery($query_string, $query_parameters);
        $value = $this->database_wrapper->countRows();
        //var_dump($value);
        return $value;
    }

    public function getMeetingbyDateUserDetails($app, $email, $date, $pos): array
    {
        $value = [];
        $this->database_wrapper->setDatabaseConnectionSettings($this->database_connection_settings);
        $this->database_wrapper->makeDatabaseConnection();
        $query_string = $this->sql_queries->getMeetingByDateUser();
        $query_parameters = [
            ':user_email' => $email,
            ':meeting_date' => $date,
            ':meeting_ack' => 1,
            ':user_email_2' => $email,
            ':meeting_date_2' => $date
        ];
        $this->database_wrapper->safeQuery($query_string, $query_parameters);
        if ($this->database_wrapper->countRows() >= 0)
        {
            $x = $this->database_wrapper->countRows();
            for($i=$pos+1; $i<=$x; $i++)
                $value = $this->database_wrapper->safeFetchRow();
        }
        return $value;
    }

    public function getIdForRecurringMeeting($app, $email)
    {
        $value = [];
        $this->database_wrapper->setDatabaseConnectionSettings($this->database_connection_settings);
        $this->database_wrapper->makeDatabaseConnection();
        $query_string = $this->sql_queries->getIdForRecurringMeeting();
        //var_dump($query_string);
        $query_parameters = [
            ':user_email' => $email,
            ':acknowledged' => 1,
            ':active' => 1

        ];
        // var_dump($query_parameters);
        $this->database_wrapper->safeQuery($query_string, $query_parameters);
        $value = $this->database_wrapper->countRows();
        //var_dump($value);
        return $value;
    }

    public function getIdForRecurringMeetingEx($app, $email, $MiD)
    {
        $value = [];
        $this->database_wrapper->setDatabaseConnectionSettings($this->database_connection_settings);
        $this->database_wrapper->makeDatabaseConnection();
        $query_string = $this->sql_queries->getIdForRecurringMeetingEx();
        //var_dump($query_string);
        $query_parameters = [
            ':user_email' => $email,
            ':acknowledged' => 1,
            ':active' => 1,
            ':meetingId'=>$MiD

        ];
        // var_dump($query_parameters);
        $this->database_wrapper->safeQuery($query_string, $query_parameters);
        $value = $this->database_wrapper->countRows();
        //var_dump($value);
        return $value;
    }

    public function getIdForRecurringMeetingDetails($app, $email,$pos)
    {
        $value = [];
        $this->database_wrapper->setDatabaseConnectionSettings($this->database_connection_settings);
        $this->database_wrapper->makeDatabaseConnection();
        $query_string = $this->sql_queries->getIdForRecurringMeeting();
        //var_dump($query_string);
        $query_parameters = [
            ':user_email' => $email,
            ':acknowledged' => 1,
            ':active' => 1

        ];
        // var_dump($query_parameters);
        $this->database_wrapper->safeQuery($query_string, $query_parameters);
        $this->database_wrapper->safeQuery($query_string, $query_parameters);
        if ($this->database_wrapper->countRows() >= 0)
        {
            $x = $this->database_wrapper->countRows();
            for($i=$pos+1; $i<=$x; $i++)
                $value = $this->database_wrapper->safeFetchRow();
        }
        return $value;
    }
    public function getIdForRecurringMeetingDetailsEx($app, $email,$pos, $MiD)
    {
        $value = [];
        $this->database_wrapper->setDatabaseConnectionSettings($this->database_connection_settings);
        $this->database_wrapper->makeDatabaseConnection();
        $query_string = $this->sql_queries->getIdForRecurringMeetingEx();
        //var_dump($query_string);
        $query_parameters = [
            ':user_email' => $email,
            ':acknowledged' => 1,
            ':active' => 1,
            ':meetingId' => $MiD,

        ];
        // var_dump($query_parameters);
        $this->database_wrapper->safeQuery($query_string, $query_parameters);
        $this->database_wrapper->safeQuery($query_string, $query_parameters);
        if ($this->database_wrapper->countRows() >= 0)
        {
            $x = $this->database_wrapper->countRows();
            for($i=$pos+1; $i<=$x; $i++)
                $value = $this->database_wrapper->safeFetchRow();
        }
        return $value;
    }


    public function getStartDurationById($app,$Id)
    {
        $value = [];
        $this->database_wrapper->setDatabaseConnectionSettings($this->database_connection_settings);
        $this->database_wrapper->makeDatabaseConnection();
        $query_string = $this->sql_queries->getStartDurationById();
        $query_parameters = [
            ':MId' => $Id
        ];

        $value = $this->database_wrapper->countRows();

        return $value;

    }
    public function getStartDurationByIdDetails($app,$Id,$pos)
    {
        $value = [];
        $this->database_wrapper->setDatabaseConnectionSettings($this->database_connection_settings);
        $this->database_wrapper->makeDatabaseConnection();
        $query_string = $this->sql_queries->getStartDurationById();
        $query_parameters = [
            ':MId' => $Id
        ];

        $this->database_wrapper->safeQuery($query_string, $query_parameters);
        $this->database_wrapper->safeQuery($query_string, $query_parameters);
        if ($this->database_wrapper->countRows() >= 0)
        {
            $x = $this->database_wrapper->countRows();
            for($i=$pos+1; $i<=$x; $i++)
                $value = $this->database_wrapper->safeFetchRow();

            array_push($value,$this->database_wrapper->safeFetchRow());
        }
        return $value;

    }
    public function getMeetingDetailsByUserMeetingID($app, $email, $MiD){
        $this->database_wrapper->setDatabaseConnectionSettings($this->database_connection_settings);
        $this->database_wrapper->makeDatabaseConnection();
        $query_string = $this->sql_queries->getMeetingByEmailMeetingId();
        $query_parameters = [
            ':user_email' => $email,
            ':MiD'=>$MiD
        ];

        $this->database_wrapper->safeQuery($query_string, $query_parameters);
        $results = $this->database_wrapper->safeFetchRow();
        return $results;
    }
    public function getMeetingNotes($app ,$MiD){
        $this->database_wrapper->setDatabaseConnectionSettings($this->database_connection_settings);
        $this->database_wrapper->makeDatabaseConnection();
        $query_string = $this->sql_queries->getMeetingNotes();
        $query_parameters = [
            ':MiD'=>$MiD
        ];

        $this->database_wrapper->safeQuery($query_string, $query_parameters);
        $results = $this->database_wrapper->safeFetchRow();
        return $results;
    }
    public function getMeetingRecursionByID($app, $MiD){
        $this->database_wrapper->setDatabaseConnectionSettings($this->database_connection_settings);
        $this->database_wrapper->makeDatabaseConnection();
        $query_string = $this->sql_queries->getMeetingRecursionByID();
        $query_parameters = [
            ':MiD'=>$MiD
        ];

        $this->database_wrapper->safeQuery($query_string, $query_parameters);
        $results = $this->database_wrapper->safeFetchRow();
        return $results;
    }

    public function getUpcomingMeetingsByUserCount($app, $email, $start)
    {
        $this->database_wrapper->setDatabaseConnectionSettings($this->database_connection_settings);
        $this->database_wrapper->makeDatabaseConnection();
        $query_string = $this->sql_queries->getUpcomingMeetingByUser();
        $query_parameters = [
            ':user_email' => $email,
            ':meeting_start' => $start,
            ':meeting_ack' => 1,
            ':user_email_2' => $email,
            ':meeting_start_2' => $start
        ];

        $this->database_wrapper->safeQuery($query_string, $query_parameters);
        $results = $this->database_wrapper->countRows();
        return $results;
    }

    public function getUpcomingMeetingsByUser($app, $email, $start,$pos)
    {
        $value = [];
        $this->database_wrapper->setDatabaseConnectionSettings($this->database_connection_settings);
        $this->database_wrapper->makeDatabaseConnection();
        $query_string = $this->sql_queries->getUpcomingMeetingByUser();
        $query_parameters = [
            ':user_email' => $email,
            ':meeting_start' => $start,
            ':meeting_ack' => 1,
            ':user_email_2' => $email,
            ':meeting_start_2' => $start
        ];
//        var_dump($query_string);
//        var_dump($query_parameters);
        $this->database_wrapper->safeQuery($query_string, $query_parameters);
        //var_dump($this->database_wrapper->safeFetchRow());
        if ($this->database_wrapper->countRows() >= 0)
        {

            $x = $this->database_wrapper->countRows();
            for($i=$pos+1; $i<=$x; $i++)
                $value = $this->database_wrapper->safeFetchRow();
            //var_dump($value);
        }
        return $value;
    }

    public function getPastMeetingsByUserCount($app, $email, $start)
    {
        $this->database_wrapper->setDatabaseConnectionSettings($this->database_connection_settings);
        $this->database_wrapper->makeDatabaseConnection();
        $query_string = $this->sql_queries->getPastMeetingByUser();
        $query_parameters = [
            ':user_email' => $email,
            ':meeting_start' => $start,
            ':meeting_ack' => 1,
            ':user_email_2' => $email,
            ':meeting_start_2' => $start
        ];
//var_dump($query_parameters);
//var_dump($query_string);
        $this->database_wrapper->safeQuery($query_string, $query_parameters);
        $results = $this->database_wrapper->countRows();
        return $results;
    }

    public function getPastMeetingsByUser($app, $email, $start,$pos)
    {
        $value = [];
        $this->database_wrapper->setDatabaseConnectionSettings($this->database_connection_settings);
        $this->database_wrapper->makeDatabaseConnection();
        $query_string = $this->sql_queries->getPastMeetingByUser();
        $query_parameters = [
            ':user_email' => $email,
            ':meeting_start' => $start,
            ':meeting_ack' => 1,
            ':user_email_2' => $email,
            ':meeting_start_2' => $start
        ];
        $this->database_wrapper->safeQuery($query_string, $query_parameters);
        //var_dump($this->database_wrapper->safeFetchRow());
        if ($this->database_wrapper->countRows() >= 0)
        {

            $x = $this->database_wrapper->countRows();
            for($i=$pos+1; $i<=$x; $i++)
                $value = $this->database_wrapper->safeFetchRow();
            //var_dump($value);
        }
        return $value;
    }

    public function getUnacceptedMeeting($app, $email)
    {
        $value = [];
        $this->database_wrapper->setDatabaseConnectionSettings($this->database_connection_settings);
        $this->database_wrapper->makeDatabaseConnection();
        $query_string = $this->sql_queries->getUnacceptedMeetings();
        //var_dump($query_string);
        $query_parameters = [
            ':user_email' => $email,
            ':meeting_ack' => 0,
        ];
        //var_dump($query_parameters);
        $this->database_wrapper->safeQuery($query_string, $query_parameters);
        $value = $this->database_wrapper->countRows();
        //var_dump($value);
        return $value;
    }
    public function getUnacceptedMeetingDetails($app, $email, $value)
    {
        $meetingDetails = [];
        $this->database_wrapper->setDatabaseConnectionSettings($this->database_connection_settings);
        $this->database_wrapper->makeDatabaseConnection();
        $query_string = $this->sql_queries->getUnacceptedMeetings();
        //var_dump($query_string);
        $query_parameters = [
            ':user_email' => $email,
            ':meeting_ack' => 0,
        ];
        //var_dump($query_parameters);
        $this->database_wrapper->safeQuery($query_string, $query_parameters);
        $x = $this->database_wrapper->countRows();
        for($i=$value+1; $i<=$x; $i++)
            $meetingDetails = $this->database_wrapper->safeFetchRow();

        return $this->database_wrapper->safeFetchRow();
    }

    public function getMeetingById($app, $email,$Mid)
    {
        $value = [];
        $this->database_wrapper->setDatabaseConnectionSettings($this->database_connection_settings);
        $this->database_wrapper->makeDatabaseConnection();
        $query_string = $this->sql_queries->getMeetingByEmailId();
        $query_parameters = [
            ':MiD' => $Mid

        ];

        $this->database_wrapper->safeQuery($query_string, $query_parameters);
        //var_dump($this->database_wrapper->safeFetchRow());
        $value = $this->database_wrapper->safeFetchRow();
        return $value;
    }
    public function fetchMeetingDoc($app,$Id)
    {

        $this->database_wrapper->setDatabaseConnectionSettings($this->database_connection_settings);
        $this->database_wrapper->makeDatabaseConnection();
        $query_string = $this->sql_queries->fetchMeetingDoc();
        $query_parameters = [
            ':id' => $Id
        ];
//        var_dump($query_parameters);
//        var_dump($query_string);
        $this->database_wrapper->safeQuery($query_string, $query_parameters);
        return $value = $this->database_wrapper->safeFetchRow();



    }



}