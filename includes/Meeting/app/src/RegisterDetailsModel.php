<?php
/**
 * RegisterDetailsModel.php
 *
 * Author: Matthew
 * Date: 17/02/2021
 *
 * @author Matthew
 */

namespace Meeting;

class RegisterDetailsModel
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
        return "complete";
    }

    public function storeProfilePic($app, $name, $cleaned_parameters)
    {
        $this->database_wrapper->setDatabaseConnectionSettings($this->database_connection_settings);
        $this->database_wrapper->makeDatabaseConnection();
        $query_string = $this->sql_queries->storeProfilePic();
//        var_dump($query_string);
        $query_parameters = [
            ':user_email_address' => $cleaned_parameters,
            ':name' => $name
        ];
//        var_dump($query_parameters);
        $this->database_wrapper->safeQuery($query_string, $query_parameters);
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
        if ($this->database_wrapper->countRows() > 0) {
            $exists = true;
        }

        return $exists;
    }
}