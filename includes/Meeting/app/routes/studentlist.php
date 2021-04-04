<?php

/**
 *studentlist.php
 *
 * Author: Matthew
 * Date: 17/01/2021
 *
 * @author Matthew
 */

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->get('/studentlist', function (Request $request, Response $response) use ($app) {
    if (!isset($_SESSION['username'])) {
        $error = "please login";
        $_SESSION['error'] = $error;
        $html_output = $this->view->render($response,
            'homepageform.html.twig');
        return $html_output->withHeader('Location', LANDING_PAGE);
    }
    $email = $_SESSION['username'];
    $permissions = checkIfTeacher($app,$email);


    if ($permissions[0] != "Teacher") {
        if ($permissions[0] != "admin" ) {
        var_dump($permissions[0]);
        $error = "Invalid permissions";
        $_SESSION['error'] = $error;
        var_dump($error);
        $html_output = $this->view->render($response,
            'calendar.html.twig');
        return $html_output->withHeader('Location', LANDING_PAGE . "/calendar");
        }
    }


$users = getAllStudents($app);





    $html_output = $this->view->render($response,
        'studentlist.html.twig',
        [
            'css_path' => CSS_PATH,
            'landing_page' => LANDING_PAGE . '/loginuser',
            'meeting_requests' => LANDING_PAGE . '/meetingack',
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

})->setName('listofallstudents');

function checkIfTeacher($app,$email)
{
    $database_wrapper = $app->getContainer()->get('databaseWrapper');
    $sql_queries = $app->getContainer()->get('SQLQueries');
    $DetailsModel = $app->getContainer()->get('RetrieveUserModel');

    $settings = $app->getContainer()->get('settings');
    $database_connection_settings = $settings['pdo_settings'];

    $DetailsModel->setSqlQueries($sql_queries);
    $DetailsModel->setDatabaseConnectionSettings($database_connection_settings);
    $DetailsModel->setDatabaseWrapper($database_wrapper);

    $result = $DetailsModel->checkUserRole($app, $email);
//var_dump($result);
    return $result;
}

function getAllStudents($app){
    $userDetails = [];
    $database_wrapper = $app->getContainer()->get('databaseWrapper');
    $sql_queries = $app->getContainer()->get('SQLQueries');
    $DetailsModel = $app->getContainer()->get('RetrieveUserModel');

    $settings = $app->getContainer()->get('settings');
    $database_connection_settings = $settings['pdo_settings'];

    $DetailsModel->setSqlQueries($sql_queries);
    $DetailsModel->setDatabaseConnectionSettings($database_connection_settings);
    $DetailsModel->setDatabaseWrapper($database_wrapper);

    $value = $DetailsModel->getStudentsCount($app);
//var_dump($value);
    for($i =0; $i<=$value-1 ; $i++){
        $idstring = $DetailsModel->getStudents($app,$i);

        array_push($userDetails,$idstring);

    }

    return $userDetails;
}
