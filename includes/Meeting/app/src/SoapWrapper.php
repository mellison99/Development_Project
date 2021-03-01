<?php
/**
 * SoapWrapper.php
 *
 * Author: Matthew
 * Date: 17/01/2021
 *
 * @author Matthew
 */

namespace Meeting;

class SoapWrapper
{
    public function __construct(){}
    public function __destruct(){}

    public function createSoapClient()
    {
        $soap_client_handle = false;
        $soap_client_parameters = array();
        $exception = '';
        $wsdl = WSDL;
        $soap_client_parameters = ['trace' => true, 'exceptions' => true];

        try
        {
            $soap_client_handle = new \SoapClient($wsdl, $soap_client_parameters);
        }
        catch (\SoapFault $exception)
        {
            $soap_client_handle = 'Ooops - something went wrong when connecting to the data supplier.  Please try again later';
        }
        return $soap_client_handle;
    }

    public function performSoapCall($soap_client, $webservice_function, $webservice_call_parameters, $webservice_value)
    {
        $soap_call_result = null;
        $raw_xml = '';
        if ($soap_client)
        {
            try
            {
                $webservice_call_result = $soap_client->{$webservice_function};
                $soap_call_result = $webservice_call_result->{$webservice_value};
            }
            catch (\SoapFault $exception)
            {
                $soap_call_result = $exception;
            }
        }
        return $soap_call_result;
    }
}