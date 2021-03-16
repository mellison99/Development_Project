<?php
/**
 * RegisterDetailsModel.php
 *
 * Author: Hasan
 * Date: 17/01/2021
 *
 * @author Hasan
 */

namespace Meeting;

class RegisterDetailsModel
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

    public function setRegisterDetails($app, $cleaned_parameters, $hashed_password)
    {
        $this->database_wrapper->setDatabaseConnectionSettings($this->database_connection_settings);
        $this->database_wrapper->makeDatabaseConnection();
        $query_string = $this->sql_queries->createUser();

        $query_parameters = [
            ':user_email_address' => $cleaned_parameters['sanitised_email'],
            ':user_sim' => $cleaned_parameters['sanitised_sim'],
            ':user_lastname' => $cleaned_parameters['sanitised_lastname'],
            'user_firstname' => $cleaned_parameters['sanitised_firstname'],
            'user_role' => $cleaned_parameters['sanitised_role'],
            ':user_hashed_password' => $hashed_password
        ];
        $this->database_wrapper->safeQuery($query_string, $query_parameters);
        //var_dump($this);
    }

    public function getUserEmailFromInput(){
        $this->database_wrapper->setDatabaseConnectionSettings($this->database_connection_settings);
        $this->database_wrapper->makeDatabaseConnection();
        $query_string = $this->sql_queries->searchUser();
        $query_parameters = [
            ':searchInput' => $cleaned_parameters['sanitised_email']
            ];
        $this->database_wrapper->safeQuery($query_string, $query_parameters);
    }

    public function setRegisterMetaDetails($app, $cleaned_parameters)
    {
        $this->database_wrapper->setDatabaseConnectionSettings($this->database_connection_settings);
        $this->database_wrapper->makeDatabaseConnection();
        $query_string = $this->sql_queries->addMetaData();
        $query_parameters = [
            ':user_sim_id' => $cleaned_parameters['sanitised_sim'],
            ':user_email_address' => $cleaned_parameters['sanitised_email'],
            ':user_name' => $cleaned_parameters['sanitised_name']
        ];
        $this->database_wrapper->safeQuery($query_string, $query_parameters);
    }
/* Matthews work from this point on*/
    public function setMessageDetails($app, $parsedmessage)
    {
        $this->database_wrapper->setDatabaseConnectionSettings($this->database_connection_settings);
        $this->database_wrapper->makeDatabaseConnection();
        $query_string = $this->sql_queries->createNewMessage();
        $query_parameters = [
            ':switch_1' => $parsedmessage['S1'],
            ':switch_2' => $parsedmessage['S2'],
            ':switch_3' => $parsedmessage['S3'],
            ':switch_4' => $parsedmessage['S4'],
            ':fan' => $parsedmessage['FAN'],
            ':temperature' => $parsedmessage['TEMP'],
            ':last_digit' => $parsedmessage['LSTDIG'],
            ':message_receiver'=> $parsedmessage['DESTINATIONMSISDN'],
            ':date_time_received'=>$parsedmessage['RECEIVEDTIME'],
            ':sender_email_address'=>$parsedmessage['EMAIL']
        ];
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
            ':Id' => 1,
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
            ':eventId' => 1,
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

    public function checkTimeslot($app, $cleaned_parameters): int
    {

        $this->database_wrapper->setDatabaseConnectionSettings($this->database_connection_settings);
        $this->database_wrapper->makeDatabaseConnection();
        $query_string = $this->sql_queries->checkTimeslot();
        $sanitisedStart = $cleaned_parameters['sanitised_start'];
        $sanitisedEnd = $cleaned_parameters['sanitised_end'];
        $query_parameters = [
            ':meeting_start' => $sanitisedStart,
            ':meeting_end' => $sanitisedEnd
        ];
       // var_dump($query_parameters);
        //var_dump($query_string);
        $this->database_wrapper->safeQuery($query_string, $query_parameters);
        return $this->database_wrapper->countRows();
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

    public function getUnacceptedMeeting($app, $email, $time)
    {
        $value = [];
        $this->database_wrapper->setDatabaseConnectionSettings($this->database_connection_settings);
        $this->database_wrapper->makeDatabaseConnection();
        $query_string = $this->sql_queries->getUnacceptedMeetings();
        //var_dump($query_string);
        $query_parameters = [
            ':user_email' => $email,
            ':meeting_ack' => 0,
            ':current_time' => $time,
        ];
        //var_dump($query_parameters);
        $this->database_wrapper->safeQuery($query_string, $query_parameters);
        $value = $this->database_wrapper->countRows();
        //var_dump($value);
        return $value;
    }
    public function getUnacceptedMeetingDetails($app, $email, $time, $value)
    {
        $meetingDetails = [];
        $this->database_wrapper->setDatabaseConnectionSettings($this->database_connection_settings);
        $this->database_wrapper->makeDatabaseConnection();
        $query_string = $this->sql_queries->getUnacceptedMeetings();
        //var_dump($query_string);
        $query_parameters = [
            ':user_email' => $email,
            ':meeting_ack' => 0,
            ':current_time' => $time,
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


    public function getEmailBySender($app, $cleaned_parameters,$pos): array
    {
        $value = [];
        $this->database_wrapper->setDatabaseConnectionSettings($this->database_connection_settings);
        $this->database_wrapper->makeDatabaseConnection();
        $query_string = $this->sql_queries->getMessageBySenderEmail();
        $query_parameters = [
            ':sender_email_address' => $cleaned_parameters
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

    public function getNumEmailBySender($app, $email)
    {
        $value = [];
        $this->database_wrapper->setDatabaseConnectionSettings($this->database_connection_settings);
        $this->database_wrapper->makeDatabaseConnection();
        $query_string = $this->sql_queries->getMessageBySenderEmail();
        $query_parameters = [
            ':sender_email' => $email
        ];
        $this->database_wrapper->safeQuery($query_string, $query_parameters);
        $value = $this->database_wrapper->countRows();

        return $value;
    }

    public function getNumberByUser($app, $cleaned_parameters,$pos)
    {
        $value = [];
        $exists = false;
        $this->database_wrapper->setDatabaseConnectionSettings($this->database_connection_settings);
        $this->database_wrapper->makeDatabaseConnection();
        $query_string = $this->sql_queries->getNumberByUser();
        $query_parameters = [
            ':user_email' => $cleaned_parameters[$pos]
        ];
        $this->database_wrapper->safeQuery($query_string, $query_parameters);
        if ($this->database_wrapper->countRows() >= 0)
        {
            $value = $this->database_wrapper->safeFetchRow();
            var_dump($value);
        }

        return $value;

    }
    public function getHostNumber($app, $host_email)
    {
        $this->database_wrapper->setDatabaseConnectionSettings($this->database_connection_settings);
        $this->database_wrapper->makeDatabaseConnection();
        $query_string = $this->sql_queries->getNumberByUser();
        $query_parameters = [
            ':user_email' => $host_email
        ];
        $this->database_wrapper->safeQuery($query_string, $query_parameters);



        return $this->database_wrapper->safeFetchRow();

    }

    public function checkNumber($app, $cleaned_parameters)
       {
        $exists = false;
        $this->database_wrapper->setDatabaseConnectionSettings($this->database_connection_settings);
        $this->database_wrapper->makeDatabaseConnection();
        $query_string = $this->sql_queries->checkNumberPresent();
        $make_trace = false;
        $query_parameters = [
            ':user_sim_id' => $cleaned_parameters['sanitised_sim']
           ];
        $this->database_wrapper->safeQuery($query_string, $query_parameters);
        if ($this->database_wrapper->countRows() > 0)
        {
            $exists = true;
        }

        return $exists;
    }

    public function checkEmail($app, $cleaned_parameters)
    {
        $exists = false;
        $this->database_wrapper->setDatabaseConnectionSettings($this->database_connection_settings);
        $this->database_wrapper->makeDatabaseConnection();
        $query_string = $this->sql_queries->checkEmailPresent();
        $query_parameters = [
            ':user_email_address' => $cleaned_parameters
        ];
        $this->database_wrapper->safeQuery($query_string, $query_parameters);
        //var_dump($this->database_wrapper->countRows());
        if ($this->database_wrapper->countRows() > 0)
        {
            $exists = true;
        }

        return $exists;
    }
  

        




}