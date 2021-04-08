<?php
/**
 * UpdateUserModel.php
 *
 * Author: Matthew
 * Date: 26/02/2021
 *
 * @author Matthew
 */

namespace Meeting;


class UpdateUserModel
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

    public function updateUser($app,$email,$cleaned_parameters)
    {
        $this->database_wrapper->setDatabaseConnectionSettings($this->database_connection_settings);
        $this->database_wrapper->makeDatabaseConnection();
        $query_string = $this->sql_queries->updateUser();
        $query_parameters = [
            ':user_email' => $email,
            ':sim' => $cleaned_parameters['sanitised_sim'],
            ':firstname'=> $cleaned_parameters['sanitised_firstname'],
            ':lastname' => $cleaned_parameters['sanitised_lastname']

        ];

        $this->database_wrapper->safeQuery($query_string, $query_parameters);
    }
    public function setUserRoleStudent($app,$Uid)
    {
        $this->database_wrapper->setDatabaseConnectionSettings($this->database_connection_settings);
        $this->database_wrapper->makeDatabaseConnection();
        $query_string = $this->sql_queries->updateRoleStudent();
        $query_parameters = [
            ':UiD' => $Uid,

        ];
//        var_dump($query_string);
//        var_dump($query_parameters);

        $this->database_wrapper->safeQuery($query_string, $query_parameters);
    }
    public function setUserRoleTeacher($app,$Uid)
    {
        $this->database_wrapper->setDatabaseConnectionSettings($this->database_connection_settings);
        $this->database_wrapper->makeDatabaseConnection();
        $query_string = $this->sql_queries->updateRoleTeacher();
        $query_parameters = [
            ':UiD' => $Uid,

        ];
//        var_dump($query_string);
//        var_dump($query_parameters);

        $this->database_wrapper->safeQuery($query_string, $query_parameters);
    }

}