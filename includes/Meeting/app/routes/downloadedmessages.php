<?php
/**
 * downloadedmessages.php
 *
 * Author: Matthew
 * Date: 17/01/2021
 *
 * @author Matthew
 */

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->post('/downloadedmessages', function(Request $request, Response $response) use ($app)
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
    $selectedmessage = sizeof(getDetails($app, $email))-1;

    $input = $request->getParsedBody()['message'];
    $arraypos = 0;
    if(strLen(strLen($input)<1)){
        $arraypos = $arraypos;
    }
    else{
    $arraypos = $request->getParsedBody();
        }
    $valarraypos= $arraypos['message'];
    $valarraypos = (int)$valarraypos;

    $selectedmessage = $selectedmessage - $valarraypos;

    $parsedmessage = parseMessageData($app, getDetails($app,$email),$selectedmessage);
    $error = '';

    if(checkExistingTime($app, $parsedmessage) == false){
        StoreMessageData($app, $parsedmessage);
    }
    else{
        $error .= 'Already in database';
    }

    $html_output = $this->view->render($response,
        'downloadedmessages.html.twig',
        [
            'css_path' => CSS_PATH,
            'landing_page' => LANDING_PAGE . '/loginuser',
            'initial_input_box_value' => null,
            'page_title' => APP_NAME,
            'page_heading_1' => APP_NAME,
            'page_heading_2' => 'Download',
            'switch1' => 'Switch 1 state: '.$parsedmessage['S1'],
            'switch2' => 'Switch 2 state: '.$parsedmessage['S2'],
            'switch3' => 'Switch 3 state: '.$parsedmessage['S3'],
            'switch4' => 'Switch 4 state: '.$parsedmessage['S4'],
            'fan' => 'Fan state: '.$parsedmessage['FAN'],
            'temperature' => 'Temperature: '.$parsedmessage['TEMP'],
            'lastdigit' => 'Last digit entered: '.$parsedmessage['LSTDIG'],
            'sendernum' => 'Number of sender: '.$parsedmessage['SOURCEMSISDN'],
            'recievernum' => 'Number of receiver: '.$parsedmessage['DESTINATIONMSISDN'],
            'datetime' => ' Date/Time received: '.$parsedmessage['RECEIVEDTIME'],
            'email' => ' Email of sender: '.$parsedmessage['EMAIL'],
            'error'=> $error,
            'Send' => LANDING_PAGE . '/downloadedmessageselect',
        ]);

    processOutput($app, $html_output);

    return $html_output;

})->setName('downloadedmessages');

function getDetails($app, $email)
{
    $soap_wrapper = $app->getContainer()->get('SoapWrapper');
    $details_model = $app->getContainer()->get('DetailsModel');
    $details_model->setSoapWrapper($soap_wrapper);
    $details_model->performDetailRetrieval();
    $detail_result = $details_model->getResult();
    $filtered_result = [];

    if(sizeof($detail_result)<1){
    return "no  messages";
    }else{
        foreach ($detail_result as $message)
        {
            if(str_contains($message, 'idtag') && str_contains($message, $email) ){
                array_push($filtered_result,$message);
            }
        }
        $lastsent = sizeof($filtered_result)-1;
        if($lastsent<0){
            return 'No messages';
        }
        else{
    return $filtered_result;}}
}

function checkExistingTime($app, array $cleaned_parameters): bool
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

    $store_data_result = $DetailsModel->checkTimePresent($app, $cleaned_parameters);

    return $store_data_result;
}

function parseMessageData($app, array $messages,$selectedmessage)
{
    $xml_parser = $app->getContainer()->get('XmlParser');
    $element_to_parse = $xml_parser->setXmlStringToParse($messages[$selectedmessage]);
    $test_var = $xml_parser->parseTheXmlString();
    $message_data = $xml_parser->getParsedData();
    return $message_data;
}

function StoreMessageData($app, $parsedmessage)
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
    $DetailsModel->setMessageDetails($app, $parsedmessage);
}
