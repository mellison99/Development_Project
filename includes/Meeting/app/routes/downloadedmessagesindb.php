<?php
/**
 * downloadedmessagesindb.php
 *
 * Author: Matthew
 * Date: 17/01/2021
 *
 * @author Matthew
 */

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->get('/downloadedmessagesindb', function(Request $request, Response $response) use ($app)
{
    if(!isset($_SESSION['username']))
    {
        $error = "please login";
        $_SESSION['error'] = $error;
        $html_output =  $this->view->render($response,
            'homepageform.html.twig');
        return $html_output->withHeader('Location', LANDING_PAGE);
    }

    $email = $_SESSION['username'];
    $tainted_MiD = $_GET["MiD"];
    $cleaned_MiD = cleanupParameters($app, $tainted_MiD);
    $values = RetrieveMeetingData($app,$email,$cleaned_MiD);
    //var_dump($cleaned_MiD);
    if($cleaned_MiD[0] == "0"){
        $message_content = makeM2MUpdate($email, $cleaned_MiD, $values);
        $cleaned_MiD = substr($cleaned_MiD,1);
        $numberToMessage = RetrieveHostNumber($app,$values);
        deleteMeetingData($app, $email,$cleaned_MiD);

        //var_dump($message_content);
        var_dump($numberToMessage);
        sendM2MUpdate($app, $message_content, $numberToMessage);
        $error = "Meeting Invite declined";
        $_SESSION['error'] = $error;
        $html_output =  $this->view->render($response,
            'downloadmessageselect.html.twig');
        return $html_output->withHeader('Location', LANDING_PAGE . '/downloadedmessageselect');
    }

    else{
//        var_dump($cleaned_MiD);
//        var_dump($email);
        UpdateMeetingData($app, $email,$cleaned_MiD);
        $numberToMessage = RetrieveHostNumber($app,$values);
        $message_content = makeM2MUpdate($email, $cleaned_MiD, $values);
       // var_dump($message_content);
       // var_dump($numberToMessage);
        sendM2MUpdate($app, $message_content, $numberToMessage);
        $error = "";
        $_SESSION['error'] = $error;
        $html_output =  $this->view->render($response,
            'downloadmessageselect.html.twig');
        return $html_output->withHeader('Location', LANDING_PAGE . '/downloadedmessageselect');
    }

    $error = '';
    $input = $request->getParsedBody()['messagedb'];
      $inputint = (int)$input;

    if(strLen(strLen($input)<1)){
        $pos = $pos;
    }
    else{
        $pos = $inputint;
    }

    $values =  [];//RetrieveMessageData($app, $email,$pos);
    $values = RetrieveMeetingData($app,$email,$cleaned_MiD);
    $html_output = $this->view->render($response,
        'downloadedmessagesindb.html.twig',
        [
            'css_path' => CSS_PATH,
            'landing_page' => LANDING_PAGE . '/loginuser',
            'initial_input_box_value' => null,
            'page_title' => APP_NAME,
            'page_heading_1' => APP_NAME,
            'page_heading_2' => 'Meeting Details',
            'switch1' => 'Meeting Id: '. $cleaned_MiD,
            'switch2' => 'Meeting date: '.$values[1],
            'switch3' => 'Start time: '.$values[2],
            'switch4' => 'Duration: '.$values[3],

            'email' => ' Host: '.$values[6],
            'error'=> $error,
            'Send' => LANDING_PAGE . '/downloadedmessageselect',
        ]);

    processOutput($app, $html_output);

    return $html_output;

})->setName('downloadedmessages');

function cleanupParameters($app, $tainted_MiD)
{
    $cleaned_MiD = [];
    $validator = $app->getContainer()->get('validator');

    $cleaned_MiD = $validator->sanitiseString($tainted_MiD);

    return $cleaned_MiD;
}
function makeM2MUpdate($email,$cleaned_MiD, $values){
    if ($cleaned_MiD[0] == "0"){
    $M2MString = 'User: '.$email . ' has declined your meeting invite';
    }
    else{
        $M2MString = 'User: '.$email . ' has accepted your meeting invite for '.$values[1] . ' at '.$values[2];
    }
    return $M2MString;
}

function sendM2MUpdate($app, $message_content, $numberToSend){
    var_dump("+".$numberToSend[0]);
    $SoapWrapper = $app->getContainer()->get('SoapWrapper');
    $soap_client_handle = $SoapWrapper->createSoapClient();
    $soap_client_handle->sendMessage('20_2414628', 'PublicPassword12',"+".$numberToSend[0],$message_content,false,"SMS");
}

function RetrieveMeetingData($app, $email,$MiD)
{
    $value = [];
    $store_data_result = null;

    $database_wrapper = $app->getContainer()->get('databaseWrapper');
    $sql_queries = $app->getContainer()->get('SQLQueries');
    $DetailsModel = $app->getContainer()->get('RegisterDetailsModel');

    $settings = $app->getContainer()->get('settings');
    $database_connection_settings = $settings['pdo_settings'];

    $DetailsModel->setSqlQueries($sql_queries);
    $DetailsModel->setDatabaseConnectionSettings($database_connection_settings);
    $DetailsModel->setDatabaseWrapper($database_wrapper);
    $value = $DetailsModel->getMeetingById($app, $email,$MiD);
    return $value;
}
function RetrieveHostNumber($app, $values)
{
    $value = [];
    $store_data_result = null;

    $database_wrapper = $app->getContainer()->get('databaseWrapper');
    $sql_queries = $app->getContainer()->get('SQLQueries');
    $DetailsModel = $app->getContainer()->get('RegisterDetailsModel');

    $settings = $app->getContainer()->get('settings');
    $database_connection_settings = $settings['pdo_settings'];

    $DetailsModel->setSqlQueries($sql_queries);
    $DetailsModel->setDatabaseConnectionSettings($database_connection_settings);
    $DetailsModel->setDatabaseWrapper($database_wrapper);
    var_dump($values[6]);
    $value = $DetailsModel->getHostNumber($app, $values[6]);

    return $value;
}

function UpdateMeetingData($app, $MiD, $email)
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
    $DetailsModel->updateMeetingAck($app, $email,$MiD);

}

function deleteMeetingData($app, $MiD, $email)
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
    $DetailsModel->deleteMeetingUser($app, $email,$MiD);

}

