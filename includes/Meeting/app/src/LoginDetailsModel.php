<?php
/**
 * LoginDetailsModel.php
 *
 * Author: Matthew
 * Date: 23/01/2021
 *
 * @author Matthew
 */

namespace Meeting;

class LoginDetailsModel
{
    private $database_wrapper;
    private $username;
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

    public function getUserName()
    {
        return $this->username;
    }

    public function checkExistingAccount($app, $cleaned_parameters): string
    {
        $exists = false;
        $this->database_wrapper->setDatabaseConnectionSettings($this->database_connection_settings);
        $this->database_wrapper->makeDatabaseConnection();
        $query_string = $this->sql_queries->checkUserPresent();

        $query_parameters = [
            ':user_email_address' => $cleaned_parameters['sanitised_email']
        ];
        $this->database_wrapper->safeQuery($query_string, $query_parameters);
        if ($this->database_wrapper->countRows() > 0)
        {
            $exists = true;
        }
        return $exists;
    }

    public function selectHashedPassword($app, $cleaned_parameters)
    {
        $password = '';
        $this->database_wrapper->setDatabaseConnectionSettings($this->database_connection_settings);
        $this->database_wrapper->makeDatabaseConnection();
        $query_string = $this->sql_queries->selectHashedPassword();

        $query_parameters = [
            ':user_email_address' => $cleaned_parameters['sanitised_email']
        ];

        $this->database_wrapper->safeQuery($query_string, $query_parameters);

        if ($this->database_wrapper->countRows() > 0)
        {
            $password = $this->database_wrapper->safeFetchRow();
        }
        return $password[0];
    }




    public function checkSessionID($app, $email)

    {
        $result = '';
        $this->database_wrapper->setDatabaseConnectionSettings($this->database_connection_settings);
        $this->database_wrapper->makeDatabaseConnection();
        $query_string = $this->sql_queries->checkSessionID();

        $query_parameters = [
            ':user_email_address' => $email
        ];
        $this->database_wrapper->safeQuery($query_string, $query_parameters);

        if ($this->database_wrapper->countRows() > 0)
        {
            $result = $this->database_wrapper->safeFetchRow();
        }
        return $result;
    }

    public function storeDataInSessionDatabase($app, $cleaned_parameters)
    {
        $store_result = false;
        $this->database_wrapper->setSqlQueries($this->sql_queries);
        $this->database_wrapper->setDatabaseConnectionSettings($this->database_connection_settings);
        $this->database_wrapper->makeDatabaseConnection();
        $query_string = $this->sql_queries->setSessionVar();

        $store_result_username = $cleaned_parameters['sanitised_email'];
        $store_result_sessionID = $cleaned_parameters['session_id'];

        $query_parameters = [
            ':user_session_id' => $cleaned_parameters['session_id'],
            ':user_email_address' => $cleaned_parameters['sanitised_email'],
        ];

        $this->database_wrapper->safeQuery($query_string, $query_parameters);

        if ($store_result_username !== false && $store_result_sessionID !== false) {
            $store_result = true;
        }
        return $store_result;
    }
}