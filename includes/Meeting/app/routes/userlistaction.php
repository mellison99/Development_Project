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

$app->get('/userlistaction', function (Request $request, Response $response) use ($app) {
    if (!isset($_SESSION['username'])) {
        $error = "please login";
        $_SESSION['error'] = $error;
        $html_output = $this->view->render($response,
            'homepageform.html.twig');
        return $html_output->withHeader('Location', LANDING_PAGE);
    }
    $tainted_UiD = $_GET["UiD"];
    $cleaned_UiD = cleanupParameters($app, $tainted_UiD);
    if($cleaned_UiD[0]=="D"){
        $uid = substr($cleaned_UiD,1);
        deleteUser($app,$uid);
        $html_output = $this->view->render($response,
            'alluserslist.html.twig');
        return $html_output->withHeader('Location', LANDING_PAGE . "/alluserslist");
    }
    var_dump($cleaned_UiD[0]);
    if($cleaned_UiD[0]=="T"){
        $uid = substr($cleaned_UiD,7);
        setRole($app,$uid,"Student");
        $html_output = $this->view->render($response,
            'alluserslist.html.twig');
        return $html_output->withHeader('Location', LANDING_PAGE . "/alluserslist");
    }
    if($cleaned_UiD[0]=="S"){
        $uid = substr($cleaned_UiD,7);
        setRole($app,$uid,"Teacher");
        $html_output = $this->view->render($response,
            'alluserslist.html.twig');
        return $html_output->withHeader('Location', LANDING_PAGE . "/alluserslist");
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







    $html_output = $this->view->render($response,
        'userlistaction.html.twig',
        [
            'css_path' => CSS_PATH,
            'landing_page' => LANDING_PAGE . '/loginuser',
            'meeting_requests' => LANDING_PAGE . '/downloadedmessageselect',
            'upcoming_meetings'=>LANDING_PAGE . '/upcomingmeetings',
            'hosted_meetings'=>LANDING_PAGE . '/meetingshosted',
            'method' => 'post',
            'method2' => 'post',
            'initial_input_box_value' => null,
            'page_title' => APP_NAME,
            'page_heading_1' => APP_NAME,
        ]);


    $processed_output = processOutput($app, $html_output);
    return $processed_output;

})->setName('usersactions');
function setRole($app, $Uid,$role){
    var_dump($role);
    var_dump($Uid);
    $userDetails = [];
    $database_wrapper = $app->getContainer()->get('databaseWrapper');
    $sql_queries = $app->getContainer()->get('SQLQueries');
    $DetailsModel = $app->getContainer()->get('RegisterDetailsModel');

    $settings = $app->getContainer()->get('settings');
    $database_connection_settings = $settings['pdo_settings'];

    $DetailsModel->setSqlQueries($sql_queries);
    $DetailsModel->setDatabaseConnectionSettings($database_connection_settings);
    $DetailsModel->setDatabaseWrapper($database_wrapper);
    if($role == "Teacher"){
        $DetailsModel->setUserRoleTeacher($app,$Uid);
    }
    if($role == "Student"){
        $DetailsModel->setUserRoleStudent($app,$Uid);
    }

}


function deleteUser($app,$UiD){
    var_dump($UiD);
    $userDetails = [];
    $database_wrapper = $app->getContainer()->get('databaseWrapper');
    $sql_queries = $app->getContainer()->get('SQLQueries');
    $DetailsModel = $app->getContainer()->get('RegisterDetailsModel');

    $settings = $app->getContainer()->get('settings');
    $database_connection_settings = $settings['pdo_settings'];

    $DetailsModel->setSqlQueries($sql_queries);
    $DetailsModel->setDatabaseConnectionSettings($database_connection_settings);
    $DetailsModel->setDatabaseWrapper($database_wrapper);
    $DetailsModel->deleteUserMeetings($app,$UiD);
    $DetailsModel->deleteUserMeetingsPart($app,$UiD);
    $DetailsModel->deleteUser($app,$UiD);
}
