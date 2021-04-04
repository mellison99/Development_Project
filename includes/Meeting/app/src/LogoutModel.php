<?php
/**
 * LogoutModel.php
 *
 * Author: Matthew
 * Date: 17/01/2021
 *
 * @author Matthew
 */

namespace Meeting;

class LogoutModel
{
    private $database_wrapper;
    private $database_connection_settings;
    private $sql_queries;

    public function __construct(){
        $this->database_wrapper = null;
        $this->database_connection_settings = null;
    }
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

    public function unsetSession($app, $session_id)
    {
        $this->database_wrapper->setSqlQueries($this->sql_queries);
        $this->database_wrapper->setDatabaseConnectionSettings($this->database_connection_settings);// setDatabaseConnectionSettings($this->database_connection_settings);
        $this->database_wrapper->makeDatabaseConnection();

        $query_string = $this->sql_queries->unsetSessionVar();

        $query_parameters = [
            "user_session_id" => $session_id
        ];

        if ($session_id !== false)
        {
            $store_result = true;
            $this->database_wrapper->safeQuery($query_string, $query_parameters);
        }
        return $store_result;
    }

}