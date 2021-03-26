<?php

/**
 * calendar.php
 *
 * Author: Matthew
 * Date: 17/01/2021
 *
 * @author Matthew
 */

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->get('/alluserslist', function (Request $request, Response $response) use ($app) {
    if (!isset($_SESSION['username'])) {
        $error = "please login";
        $_SESSION['error'] = $error;
        $html_output = $this->view->render($response,
            'homepageform.html.twig');
        return $html_output->withHeader('Location', LANDING_PAGE);
    }
    $email = $_SESSION['username'];
    $permissions = checkUserRole($app,$email);


    if ($permissions[0] != "admin") {
        $error = "invalid permissions";
        $_SESSION['error'] = $error;
        var_dump($error);
        $html_output = $this->view->render($response,
            'calendar.html.twig');
        return $html_output->withHeader('Location', LANDING_PAGE . "/calendar");
    }

$users = getAllUsers($app,$email);
    //var_dump($users);




    $html_output = $this->view->render($response,
        'alluserslist.html.twig',
        [
            'css_path' => CSS_PATH,
            'landing_page' => LANDING_PAGE . '/loginuser',
            'meeting_requests' => LANDING_PAGE . '/downloadedmessageselect',
            'upcoming_meetings'=>LANDING_PAGE . '/upcomingmeetings',
            'hosted_meetings'=>LANDING_PAGE . '/meetingshosted',
            'method' => 'post',
            'method2' => 'post',
            'action' => 'eventspost',
            'initial_input_box_value' => null,
            'page_title' => APP_NAME,
            'page_heading_1' => APP_NAME,
            'userslist'=> $users
        ]);


    $processed_output = processOutput($app, $html_output);
    return $processed_output;

})->setName('listofallusers');
function checkUserRole($app,$email)
{
    $database_wrapper = $app->getContainer()->get('databaseWrapper');
    $sql_queries = $app->getContainer()->get('SQLQueries');
    $DetailsModel = $app->getContainer()->get('RegisterDetailsModel');

    $settings = $app->getContainer()->get('settings');
    $database_connection_settings = $settings['pdo_settings'];

    $DetailsModel->setSqlQueries($sql_queries);
    $DetailsModel->setDatabaseConnectionSettings($database_connection_settings);
    $DetailsModel->setDatabaseWrapper($database_wrapper);

    $result = $DetailsModel->checkUserRole($app, $email);
//var_dump($result);
    return $result;
}

function getAllUsers($app,$email){
    $userDetails = [];
    $database_wrapper = $app->getContainer()->get('databaseWrapper');
    $sql_queries = $app->getContainer()->get('SQLQueries');
    $DetailsModel = $app->getContainer()->get('RegisterDetailsModel');

    $settings = $app->getContainer()->get('settings');
    $database_connection_settings = $settings['pdo_settings'];

    $DetailsModel->setSqlQueries($sql_queries);
    $DetailsModel->setDatabaseConnectionSettings($database_connection_settings);
    $DetailsModel->setDatabaseWrapper($database_wrapper);

    $value = $DetailsModel->getAllUsersCount($app,$email);
//var_dump($value);
    for($i =0; $i<=$value-1 ; $i++){
        $idstring = $DetailsModel->getAllUsers($app,$i,$email);
        //var_dump($idstring);
//        var_dump($idstring);
        array_push($userDetails,$idstring);

    }

    return $userDetails;
}
