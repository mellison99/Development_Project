<?php
/**
 * CheckTimesModel.php
 *
 * Author: Matthew
 * Date: 14/02/2021
 *
 * @author Matthew
 */

namespace Meeting;


class CheckTimesModel
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
    public function checkTimeslot($app, $cleaned_parameters): int
    {

        $this->database_wrapper->setDatabaseConnectionSettings($this->database_connection_settings);
        $this->database_wrapper->makeDatabaseConnection();
        $query_string = $this->sql_queries->checkTimeslot();
        $sanitisedStart = $cleaned_parameters['sanitised_start'];
        $sanitisedEnd = $cleaned_parameters['sanitised_end'];
        $query_parameters = [
            ':meeting_start' => $sanitisedStart+60,
            ':meeting_end' => $sanitisedEnd-60
        ];
        // var_dump($query_parameters);
        //var_dump($query_string);
        $this->database_wrapper->safeQuery($query_string, $query_parameters);
        return $this->database_wrapper->countRows();
    }
    public function checkTimeslotEx($app, $cleaned_parameters,$MiD): int
    {

        $this->database_wrapper->setDatabaseConnectionSettings($this->database_connection_settings);
        $this->database_wrapper->makeDatabaseConnection();
        $query_string = $this->sql_queries->checkTimeslotEx();
        $sanitisedStart = $cleaned_parameters['sanitised_start'];
        $sanitisedEnd = $cleaned_parameters['sanitised_end'];
        $query_parameters = [
            ':meetingId' => $MiD,
            ':meeting_start' => $sanitisedStart+60,
            ':meeting_end' => $sanitisedEnd-60
        ];
        // var_dump($query_parameters);
        //var_dump($query_string);
        $this->database_wrapper->safeQuery($query_string, $query_parameters);
        return $this->database_wrapper->countRows();
    }

    public function checkEventByDayUser($app, $email, $day)
    {
        $value = [];
        $this->database_wrapper->setDatabaseConnectionSettings($this->database_connection_settings);
        $this->database_wrapper->makeDatabaseConnection();
        $query_string = $this->sql_queries->checkEventByDayUser();
        $query_parameters = [
            ':event_day' => $day,
            ':user_email' => $email,
            'event_active' => 1
        ];
//         var_dump($query_parameters);
//        var_dump($query_string);
        $this->database_wrapper->safeQuery($query_string, $query_parameters);
        return $this->database_wrapper->countRows();
    }
    public function checkAllEventsByUser($app, $email)
    {
        $value = [];
        $this->database_wrapper->setDatabaseConnectionSettings($this->database_connection_settings);
        $this->database_wrapper->makeDatabaseConnection();
        $query_string = $this->sql_queries->getAllEventDetails();
        $query_parameters = [
            ':user_email' => $email,
            'event_active' => 1
        ];
        //var_dump($query_parameters);
        //var_dump($query_string);
        $this->database_wrapper->safeQuery($query_string, $query_parameters);
        return $this->database_wrapper->countRows();
    }

    public function checkAllNonActiveEvents($app, $email)
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
        return $this->database_wrapper->countRows();
    }





    public function checkEventByDayUserDetails($app, $email, $day, $pos): array
    {
        $value = [];
        $this->database_wrapper->setDatabaseConnectionSettings($this->database_connection_settings);
        $this->database_wrapper->makeDatabaseConnection();
        $query_string = $this->sql_queries->checkEventByDayUser();
        $query_parameters = [
            ':event_day' => $day,
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

    public function checkEventByMonthUser($app, $email, $month)
    {
        $value = [];
        $this->database_wrapper->setDatabaseConnectionSettings($this->database_connection_settings);
        $this->database_wrapper->makeDatabaseConnection();
        $query_string = $this->sql_queries->checkEventByMonthUser();
        $query_parameters = [
            ':event_month' => $month,
            ':user_email' => $email,
            'event_active' => 1
        ];
//         var_dump($query_parameters);
//        var_dump($query_string);
        $this->database_wrapper->safeQuery($query_string, $query_parameters);
        return $this->database_wrapper->countRows();
    }
    public function checkEventByMonthUserDetails($app, $email, $month, $pos): array
    {
        $value = [];
        $this->database_wrapper->setDatabaseConnectionSettings($this->database_connection_settings);
        $this->database_wrapper->makeDatabaseConnection();
        $query_string = $this->sql_queries->checkEventByMonthUser();
        $query_parameters = [
            ':event_month' => $month,
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
    public function checkEventByDateUser($app, $email, $date)
    {

        $this->database_wrapper->setDatabaseConnectionSettings($this->database_connection_settings);
        $this->database_wrapper->makeDatabaseConnection();
        $query_string = $this->sql_queries->checkEventByDateUser();
        $query_parameters = [
            ':event_date' => $date,
            ':user_email' => $email,
            'event_active' => 1
        ];
//        var_dump($query_parameters);
//        var_dump($query_string);
        $this->database_wrapper->safeQuery($query_string, $query_parameters);
        return $this->database_wrapper->countRows();

    }
    public function checkEventByDateUserDetails($app, $email, $date, $pos): array
    {
        $value = [];
        $this->database_wrapper->setDatabaseConnectionSettings($this->database_connection_settings);
        $this->database_wrapper->makeDatabaseConnection();
        $query_string = $this->sql_queries->checkEventByDateUser();
        $query_parameters = [
            ':event_date' => $date,
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

    public function checkTimeslotDetails($app, $cleaned_parameters, $pos)
    {
        $value = [];
        $this->database_wrapper->setDatabaseConnectionSettings($this->database_connection_settings);
        $this->database_wrapper->makeDatabaseConnection();
        $query_string = $this->sql_queries->checkTimeslot();
        $sanitisedStart = $cleaned_parameters['sanitised_start'];
        $sanitisedEnd = $cleaned_parameters['sanitised_end'];
        $query_parameters = [
            ':meeting_start' => $sanitisedStart,
            ':meeting_end' => $sanitisedEnd
        ];
        //var_dump($query_parameters);
        //var_dump($query_string);
        // var_dump($this->database_wrapper->safeFetchRow());
        //var_dump($this->database_wrapper->countRows() . " without assignment");
        $x = $this->database_wrapper->countRows();
        $this->database_wrapper->safeQuery($query_string, $query_parameters);

        //var_dump($x . " with assignment");
        if ($x >= 0)
        {

            //var_dump($x);
            for($i=$pos; $i<=$x; $i++)
//var_dump($value,$this->database_wrapper->safeFetchRow());
                //var_dump($value,$this->database_wrapper->safeFetchRow()[1]);
                array_push($value,$this->database_wrapper->safeFetchRow());
            //array_push($value,$this->database_wrapper->safeFetchRow());
            //var_dump($value);

        }


        return $value;
    }

    public function checkTimeslotDetailsEx($app, $cleaned_parameters, $pos, $MiD)
    {
        $value = [];
        $this->database_wrapper->setDatabaseConnectionSettings($this->database_connection_settings);
        $this->database_wrapper->makeDatabaseConnection();
        $query_string = $this->sql_queries->checkTimeslotEx();
        $sanitisedStart = $cleaned_parameters['sanitised_start'];
        $sanitisedEnd = $cleaned_parameters['sanitised_end'];
        $query_parameters = [
            ':meetingId'=>$MiD,
            ':meeting_start' => $sanitisedStart,
            ':meeting_end' => $sanitisedEnd
        ];
        //var_dump($query_parameters);
        //var_dump($query_string);
        // var_dump($this->database_wrapper->safeFetchRow());
        //var_dump($this->database_wrapper->countRows() . " without assignment");
        $x = $this->database_wrapper->countRows();
        $this->database_wrapper->safeQuery($query_string, $query_parameters);

        //var_dump($x . " with assignment");
        if ($x >= 0)
        {

            //var_dump($x);
            for($i=$pos; $i<=$x; $i++)
//var_dump($value,$this->database_wrapper->safeFetchRow());
                //var_dump($value,$this->database_wrapper->safeFetchRow()[1]);
                array_push($value,$this->database_wrapper->safeFetchRow());
            //array_push($value,$this->database_wrapper->safeFetchRow());
            //var_dump($value);

        }


        return $value;
    }

    public function checkTimePresent($app, $cleaned_parameters): string
    {
        $exists = false;
        $this->database_wrapper->setDatabaseConnectionSettings($this->database_connection_settings);
        $this->database_wrapper->makeDatabaseConnection();
        $query_string = $this->sql_queries->checkTimePresent();

        $query_parameters = [
            ':date_time_received' => $cleaned_parameters['RECEIVEDTIME']
        ];

        $this->database_wrapper->safeQuery($query_string, $query_parameters);
        if ($this->database_wrapper->countRows() > 0)
        {
            $exists = true;
        }
        return $exists;
    }


}