<?php
/**
 * sendmessage.php
 *
 * Author: Matthew
 * Date: 17/01/2021
 *
 * @author Matthew
 */

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->post(
    '/sendmessage',
    function(Request $request, Response $response) use ($app)
    {
        if(!isset($_SESSION['username']))
        {
            $error = "please login";
            $_SESSION['error'] = $error;
            $html_output =  $this->view->render($response,
                'homepageform.html.twig');
            return $html_output->withHeader('Location', LANDING_PAGE);
        }
        $email = ($_SESSION['username']);
        $error = "";
        $tainted_parameters = $request->getParsedBody();

        try
        {
            //$taintedDigit = (int)$tainted_parameters['lastNumber'];
            //$taintedTemp = (int)$tainted_parameters['curTemp'];
        }
        catch(exception $e)
        {
            $e = 'error';
            return $e;
        }

       // var_dump($_SESSION['date']);
        $cleaned_parameters = cleanupParameters1($app, $tainted_parameters);
        //var_dump($cleaned_parameters);
        /*$numberToSend = '+'.$cleaned_parameters['sanitised_num'];
        if(strlen($numberToSend)!= strlen('+447817814149'))
        {
            $error .= "number set to default value +447817814149 ";
            $numberToSend = '+447817814149';
        }*/
        $message_content = makeM2MString($cleaned_parameters,$email);
        $numberToSend = '+447908940083';
        //getSimbyEmail($app, $email);
        sendM2MMessage($app, $message_content, $numberToSend);
        storeMeetingDetails($app, $cleaned_parameters, $email);
        $meetingID = 2;
        storeMeetingUserDetails($app,$cleaned_parameters, $meetingID);

        $html_output =  $this->view->render($response,
            'sent_message.html.twig',
            [
                'landing_page' => LANDING_PAGE . '/loginuser',
                'css_path' => CSS_PATH,
                'page_title' => APP_NAME,
                'method' => 'post',
                'action' => 'sendmessages',
                'page_heading_1' => 'Create a Meeting',
                'page_heading_2' => 'Meeting details',
                'error' => $error,
               'user'=> $cleaned_parameters['sanitised_user'],
                'time'=> $cleaned_parameters['sanitised_time'],
                'host'=> $email
            ]);

        processOutput($app, $html_output);

        return $html_output;
    })->setName('sendmessage');
function cleanupParameters1($app, $tainted_parameters)
{
    $cleaned_parameters = [];
    $validator = $app->getContainer()->get('validator');
    $tainted_time = $tainted_parameters['time'];
    $tainted_duration = $tainted_parameters['duration'];
    $tainted_notes = $tainted_parameters['notes'];
    $tainted_user = $tainted_parameters['user'];
    $cleaned_parameters['sanitised_time'] = $validator->sanitiseString($tainted_time);
    $cleaned_parameters['sanitised_duration'] = $validator->sanitiseString($tainted_duration);
    $cleaned_parameters['sanitised_Id'] = time();
    $cleaned_parameters['sanitised_notes'] = $validator->sanitiseString($tainted_notes);
    $cleaned_parameters['sanitised_user'] = $validator->sanitiseString($tainted_user);
    $cleaned_parameters['sanitised_date'] = $_SESSION['date'];

    return $cleaned_parameters;
}

function makeM2MString(array $cleaned_parameters,$email){
    $M2MString = '';
    $M2MString .= '';
    $M2MString .= 'start date:';
    $M2MString .= $_SESSION['date'];
    $M2MString .= '  |';
    $M2MString .= 'start time:';
    $M2MString .= $cleaned_parameters['sanitised_time'];
    $M2MString .= '  |';
    $M2MString .= 'duration:';
    $M2MString .= $cleaned_parameters['sanitised_duration'];
    $M2MString .= '  |';
    $M2MString .= 'notes: ';
    $M2MString .= $cleaned_parameters['sanitised_notes'];
    $M2MString .= '  |';

    $M2MString .= 'host email: ';
    $M2MString .= $email;
    $M2MString .= ' ';

    $M2MString .= '';
    return $M2MString;
}
function sendM2MMessage($app, $message_content, $numberToSend){
    $SoapWrapper = $app->getContainer()->get('SoapWrapper');
    $soap_client_handle = $SoapWrapper->createSoapClient();
    $soap_client_handle->sendMessage('20_2414628', 'PublicPassword12', $numberToSend,$message_content,false,"SMS");
}
function storeMeetingDetails($app, array $cleaned_parameters, string $email)
{
    $store_data_result = null;

    $database_wrapper = $app->getContainer()->get('databaseWrapper');
    $sql_queries = $app->getContainer()->get('SQLQueries');
    $DetailsModel = $app->getContainer()->get('RegisterDetailsModel');

    $settings = $app->getContainer()->get('settings');
    $database_connection_settings = $settings['pdo_settings'];

    $DetailsModel->setSqlQueries($sql_queries);
    $DetailsModel->setDatabaseConnectionSettings($database_connection_settings);
    $DetailsModel->setDatabaseWrapper($database_wrapper);

    $DetailsModel->setMeetingDetails($app, $cleaned_parameters, $email);
}

function storeMeetingUserDetails($app, array $cleaned_parameters, $meetingID)
{
    $store_data_result = null;

    $database_wrapper = $app->getContainer()->get('databaseWrapper');
    $sql_queries = $app->getContainer()->get('SQLQueries');
    $DetailsModel = $app->getContainer()->get('RegisterDetailsModel');

    $settings = $app->getContainer()->get('settings');
    $database_connection_settings = $settings['pdo_settings'];

    $DetailsModel->setSqlQueries($sql_queries);
    $DetailsModel->setDatabaseConnectionSettings($database_connection_settings);
    $DetailsModel->setDatabaseWrapper($database_wrapper);

    $DetailsModel->setMeetingUserDetails($app, $cleaned_parameters, $meetingID);
}

function getSimbyEmail($app, $email)
{
    $store_data_result = null;

    $database_wrapper = $app->getContainer()->get('databaseWrapper');
    $sql_queries = $app->getContainer()->get('SQLQueries');
    $DetailsModel = $app->getContainer()->get('RegisterDetailsModel');

    $settings = $app->getContainer()->get('settings');
    $database_connection_settings = $settings['pdo_settings'];

    $DetailsModel->setSqlQueries($sql_queries);
    $DetailsModel->setDatabaseConnectionSettings($database_connection_settings);
    $DetailsModel->setDatabaseWrapper($database_wrapper);

    $DetailsModel->setMeetingUserDetails($app, $email);
    $sim = $DetailsModel->setMeetingUserDetails($app, $email);
    var_dump($sim);
    return $sim;
}


