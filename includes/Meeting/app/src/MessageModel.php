<?php
/**
 * MessageModel.php
 *
 * Author: Matthew
 * Date: 17/01/2021
 *
 * @author Matthew
 */

namespace Meeting;


class MessageModel
{
    private $detail;
    private $result;
    private $xml_parser;
    private $soap_wrapper;

    public function __construct()
    {
        $this->soap_wrapper = null;
        $this->xml_parser = null;
        $this->detail = '';
        $this->result = [];
    }

    public function setSoapWrapper($soap_wrapper)
    {
        $this->soap_wrapper = $soap_wrapper;
    }
    public function performDetailRetrieval()
    {
        $message_details = [];

        $soap_client_handle = $this->soap_wrapper->createSoapClient();
        if ($soap_client_handle !== false)
        {
            $webservice_call_parameters = ['20_2414628', 'PublicPassword12', 1000, ""];
            $soapcall_result = $soap_client_handle ->peekMessages($webservice_call_parameters[0], $webservice_call_parameters[1], $webservice_call_parameters[2], "");

            $this->result = $soapcall_result;
        }
    }
    public function setSqlQueries($sql_queries)
    {
        $this->sql_queries = $sql_queries;
    }
    public function performDetailSend($phoneNumber, $message)
    {
        $soap_client_handle = $this->soap_wrapper->createSoapClient();
        if ($soap_client_handle !== false)
        {
            $soap_client_handle ->sendMessage('20_2414628', 'PublicPassword12', $phoneNumber, $message,false,"SMS");
        }
    }
    public function getResult()
    {
        return $this->result;
    }

    public function checkExistingDate($app, $cleaned_parameters): string
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

    public function selectUserSim($app, $email)
    {
        $password = '';
        $this->database_wrapper->setDatabaseConnectionSettings($this->database_connection_settings);
        $this->database_wrapper->makeDatabaseConnection();
        $query_string = $this->sql_queries->getSimbyEmail();

        $query_parameters = [
            ':user_email_address' => $email
        ];

        $this->database_wrapper->safeQuery($query_string, $query_parameters);

        if ($this->database_wrapper->countRows() > 0)
        {
            $sim = $this->database_wrapper->safeFetchRow();
        }
        return $sim[0];
    }
}