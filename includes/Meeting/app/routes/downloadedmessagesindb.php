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

$app->post('/downloadedmessagesindb', function(Request $request, Response $response) use ($app)
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
    $error = '';
    $input = $request->getParsedBody()['messagedb'];
      $inputint = (int)$input;

    if(strLen(strLen($input)<1)){
        $pos = $pos;
    }
    else{
        $pos = $inputint;
    }

    $values =  RetrieveMessageData($app, $email,$pos);

    $html_output = $this->view->render($response,
        'downloadedmessagesindb.html.twig',
        [
            'css_path' => CSS_PATH,
            'landing_page' => LANDING_PAGE . '/loginuser',
            'initial_input_box_value' => null,
            'page_title' => APP_NAME,
            'page_heading_1' => APP_NAME,
            'page_heading_2' => 'Download',
            'switch1' => 'Switch 1 state: '.$values[0],
            'switch2' => 'Switch 2 state: '.$values[1],
            'switch3' => 'Switch 3 state: '.$values[2],
            'switch4' => 'Switch 4 state: '.$values[3],
            'fan' => 'Fan state: '.$values[4],
            'temperature' => 'Temperature: '.$values[5],
            'lastdigit' => 'Last digit entered: '.$values[6],
            'datetime' => ' Date/Time received: '.$values[7],
            'sendernum' => 'Number of sender: '.$values[8],
            'email' => ' Email of sender: '.$email,
            'error'=> $error,
            'Send' => LANDING_PAGE . '/downloadedmessageselect',
        ]);

    processOutput($app, $html_output);

    return $html_output;

})->setName('downloadedmessages');

function RetrieveMessageData($app, $email,$pos)
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
    $value = $DetailsModel->getEmailBySender($app, $email,$pos);
    return $value;
}

