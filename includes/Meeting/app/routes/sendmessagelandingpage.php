<?php
/**
 * sendmessagelandingpage.php
 *
 * Author: Matthew
 * Date: 17/01/2021
 *
 * @author Matthew
 */

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;


$app->get('/sendmessagelandingpage', function(Request $request, Response $response) use ($app)
    {
    if(!isset($_SESSION['username']))
    {
        $error = "please login";
        $_SESSION['error'] = $error;
        $html_output =  $this->view->render($response,
                'homepageform.html.twig');
            return $html_output->withHeader('Location', LANDING_PAGE);
    }

    //var_dump($_GET["date"]);
    $_SESSION['date'] = $_GET["date"];
    $test = getMeetingbyDateUser($app,$_SESSION['username'],$_SESSION['date']);
    //var_dump($_SESSION['username']);
        //var_dump($test);

        $html_output =  $this->view->render($response,
            'sendmessage.html.twig',
            [
                'css_path' => CSS_PATH,
                'landing_page' => LANDING_PAGE . '/loginuser',
                'page_title' => APP_NAME,
                'method' => 'post',
                'action' => 'sendmessage',
                'initial_input_box_value' => null,
                'page_heading_1' => 'Create a meeting',
                'page_heading_2' => 'Meeting details',
                'date' => $_GET["date"],
                'Add'=> LANDING_PAGE .'/sendmessagelandingpage'."?date=".$date,
                'Send' => LANDING_PAGE . '/sendmessage',
                'Calendar' => LANDING_PAGE . '/calendar',
                'meetingsOnDate'=>$test,
            ]);
        processOutput($app, $html_output);
        return $html_output;
    })->setName('sendmessage');




function getMeetingbyDateUser($app,$email,$date)
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
    $value = $DetailsModel->getMeetingbyDateUser($app, $email,$date);
    //var_dump($value);
    $downloadMessages = [];
    if($value<0){
        return "no  meetings";
    }else{
        for($i =0; $i<=$value ; $i++){
            $idstring = $DetailsModel->getMeetingbyDateUserDetails($app, $email, $date, $i);
            array_push($downloadMessages,$idstring);
        }
        array_pop($downloadMessages);
        return $downloadMessages;
    }
}


