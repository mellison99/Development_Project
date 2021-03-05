<?php
/**
 * downloadedmessageselect.php
 *
 * Author: Matthew
 * Date: 17/01/2021
 *
 * @author Matthew
 */

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->get('/downloadedmessageselect', function(Request $request, Response $response) use ($app)
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
   // $message_details = getMessages($app,$email);

    //$parsed_messages = parseAllMessageData($app,$message_details);
   // $downloaded_messages = RetrieveNumMessageData($app, $email);

    $html_output = $this->view->render($response,
    'downloadmessageselect.html.twig',
    [
      'css_path' => CSS_PATH,
      'landing_page' => LANDING_PAGE . '/loginuser',
      'method' => 'post',
      'method2' => 'post',
      'action' => 'downloadedmessages',
      'action2' => 'downloadedmessagesindb',
      'initial_input_box_value' => null,
      'page_title' => APP_NAME,
      'page_heading_1' => APP_NAME,
      'page_heading_2' => 'Choose a message to download/ View in detail',
      'country_names' => "placeholder",
      'country_names2'=>"placeholder",
      'page_text' => 'Select a message',
    ]);

    $processed_output = processOutput($app, $html_output);

    return $processed_output;

})->setName('homepage');

function getMessages($app, $email)
{
    $soap_wrapper = $app->getContainer()->get('SoapWrapper');
    $details_model = $app->getContainer()->get('DetailsModel');

    $details_model->setSoapWrapper($soap_wrapper);
    $details_model->performDetailRetrieval();

    $detail_result = $details_model->getResult();
    $filtered_result = [];

    if(sizeof($detail_result)<0){
        return "no  messages";
    }else{
        foreach ($detail_result as $message)
        {
            if(str_contains($message, 'idtag') && str_contains($message, $email)){
                array_push($filtered_result,$message);
            }
        }
        $lastsent = sizeof($filtered_result)-1;
        if($lastsent<0){
            return $filtered_result;
        }
        else{
            return $filtered_result;}}
}
function parseAllMessageData($app, array $messages)
{
    $parsedMessages =[];
    $xml_parser = $app->getContainer()->get('XmlParser');
    if(sizeof($messages)<0){
        return "no  messages";
    }else{
        $x = 0;
        foreach ($messages as $message)
        {
           $messagetoparse = $messages[$x];
            $xml_parser->setXmlStringToParse($messagetoparse);
            $x++;
            $xml_parser->parseTheXmlString();
            $idstring = $x;
            array_push($parsedMessages,$idstring);
        }
            return $parsedMessages;}
}

function RetrieveNumMessageData($app, $email)
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
    $value = $DetailsModel->getNumEmailBySender($app, $email);
    $downloadMessages = [];
    if($value<0){
        return "no  messages";
    }else{
        for($i =1; $i<=$value+1 ; $i++){
            $idstring = $i;
            array_push($downloadMessages,$idstring);
        }
        array_pop($downloadMessages);
        return $downloadMessages;}
}

function getNumEmailBySender($app,$email){
    $store_data_result = null;

    $database_wrapper = $app->getContainer()->get('databaseWrapper');
    $sql_queries = $app->getContainer()->get('SQLQueries');
    $DetailsModel = $app->getContainer()->get('RegisterDetailsModel');

    $settings = $app->getContainer()->get('settings');
    $database_connection_settings = $settings['pdo_settings'];

    $DetailsModel->setSqlQueries($sql_queries);
    $DetailsModel->setDatabaseConnectionSettings($database_connection_settings);
    $DetailsModel->setDatabaseWrapper($database_wrapper);
    $value = $DetailsModel->getNumEmailBySender($app, $email);
    return $value;
}


