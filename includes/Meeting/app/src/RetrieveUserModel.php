<?php
/**
 * DeleteMeetingsModel.php
 *
 * Author: Matthew
 * Date: 20/02/2021
 *
 * @author Matthew
 */

namespace Meeting;


class RetrieveUserModel
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
    public function fetchProfilePic($app,$email)
    {

        $this->database_wrapper->setDatabaseConnectionSettings($this->database_connection_settings);
        $this->database_wrapper->makeDatabaseConnection();
        $query_string = $this->sql_queries->fetchProfilePic();
        $query_parameters = [
            ':id' => $email
        ];
//        var_dump($query_parameters);
//        var_dump($query_string);
        $this->database_wrapper->safeQuery($query_string, $query_parameters);
        return $value = $this->database_wrapper->safeFetchRow();

    }
    public function checkUserRole($app, $email)
    {
        $this->database_wrapper->setDatabaseConnectionSettings($this->database_connection_settings);
        $this->database_wrapper->makeDatabaseConnection();
        $query_string = $this->sql_queries->checkUserRole();
        $query_parameters = [
            ':user_email'=> $email
        ];
        $this->database_wrapper->safeQuery($query_string, $query_parameters);
        $value = $this->database_wrapper->safeFetchRow();

        return $value;
    }

    public function getUsersByEmailMeetingIdCount($app, $MiD){
        $this->database_wrapper->setDatabaseConnectionSettings($this->database_connection_settings);
        $this->database_wrapper->makeDatabaseConnection();
        $query_string = $this->sql_queries->getUsersByEmailMeetingId();
        $query_parameters = [
            ':MiD'=>$MiD
        ];

        $this->database_wrapper->safeQuery($query_string, $query_parameters);
        $results = $this->database_wrapper->countRows();
        return $results;
    }

    public function getUsersByEmailMeetingId($app, $MiD, $pos){
        $this->database_wrapper->setDatabaseConnectionSettings($this->database_connection_settings);
        $this->database_wrapper->makeDatabaseConnection();
        $query_string = $this->sql_queries->getUsersByEmailMeetingId();
        $query_parameters = [
            ':MiD'=>$MiD
        ];

        $this->database_wrapper->safeQuery($query_string, $query_parameters);
        if ($this->database_wrapper->countRows() >= 0)
        {

            $x = $this->database_wrapper->countRows();
            for($i=$pos+1; $i<=$x; $i++)
                $value = $this->database_wrapper->safeFetchRow();
            //var_dump($value);
        }
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

    public function getUserInfo($app,$email)
    {
        $this->database_wrapper->setDatabaseConnectionSettings($this->database_connection_settings);
        $this->database_wrapper->makeDatabaseConnection();
        $query_string = $this->sql_queries->getUserInfo();
        $query_parameters = [
            ':email' => $email
        ];
        $this->database_wrapper->safeQuery($query_string, $query_parameters);
        $value = $this->database_wrapper->safeFetchRow();

        return $value;
    }
    public function getStudentsCount($app)
    {
        $this->database_wrapper->setDatabaseConnectionSettings($this->database_connection_settings);
        $this->database_wrapper->makeDatabaseConnection();
        $query_string = $this->sql_queries->getStudents();
        $query_parameters = [
            ':Student' => "Student"
        ];

        $this->database_wrapper->safeQuery($query_string, $query_parameters);
        $value = $this->database_wrapper->countRows();

        return $value;
    }
    public function getStudents($app,$pos): array
    {
        $value = [];
        $this->database_wrapper->setDatabaseConnectionSettings($this->database_connection_settings);
        $this->database_wrapper->makeDatabaseConnection();
        $query_string = $this->sql_queries->getStudents();
        $query_parameters = [
            ':Student' => "Student"
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

    public function getAllUsersCount($app,$email)
    {
        $this->database_wrapper->setDatabaseConnectionSettings($this->database_connection_settings);
        $this->database_wrapper->makeDatabaseConnection();
        $query_string = $this->sql_queries->getAllUsers();
        $query_parameters = [
            ':email' => $email
        ];
        $this->database_wrapper->safeQuery($query_string, $query_parameters);
        $value = $this->database_wrapper->countRows();

        return $value;
    }

    public function getAllUsers($app,$pos,$email): array
    {
        $value = [];
        $this->database_wrapper->setDatabaseConnectionSettings($this->database_connection_settings);
        $this->database_wrapper->makeDatabaseConnection();
        $query_string = $this->sql_queries->getAllUsers();
        $query_parameters = [
            ':email' => $email
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
            //var_dump($value);
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

}