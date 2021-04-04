<?php
/**
 * DeleteUserModel.php
 *
 * Author: Matthew
 * Date: 27/02/2021
 *
 * @author Matthew
 */


namespace Meeting;


class DeleteUserModel
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

    public function deleteProfilePic($app, $email)
    {
        $this->database_wrapper->setDatabaseConnectionSettings($this->database_connection_settings);
        $this->database_wrapper->makeDatabaseConnection();
        $query_string = $this->sql_queries->deleteProfilePic();
//        var_dump($query_string);
        $query_parameters = [
            ':user_email_address' => $email,

        ];
//        var_dump($query_parameters);
        $this->database_wrapper->safeQuery($query_string, $query_parameters);
    }

    public function deleteUser($app,$Uid)
    {
        $this->database_wrapper->setDatabaseConnectionSettings($this->database_connection_settings);
        $this->database_wrapper->makeDatabaseConnection();
        $query_string = $this->sql_queries->deleteUser();
        $query_parameters = [
            ':UiD' => $Uid,

        ];
//        var_dump($query_string);
//        var_dump($query_parameters);

        $this->database_wrapper->safeQuery($query_string, $query_parameters);
    }


}