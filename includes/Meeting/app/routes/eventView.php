<?php

/**
 * eventView.php
 *
 * Author: Matthew
 * Date: 20/03/2021
 *
 * @author Matthew
 */

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->get('/eventView', function (Request $request, Response $response) use ($app) {
    if (!isset($_SESSION['username'])) {
        $error = "please login";
        $_SESSION['error'] = $error;
        $html_output = $this->view->render($response,
            'homepageform.html.twig');
        return $html_output->withHeader('Location', LANDING_PAGE);
    }

    $eventList = (getEventList($app,$_SESSION['username']));
    $disabledEvents = getDisabledEventList($app,$_SESSION['username']);
    $formattedEventList = formatEventList($eventList);
    $formattedDisabledEventList = formatEventList($disabledEvents);
    var_dump($_SESSION['error']);
    $html_output = $this->view->render($response,
        'eventView.html.twig',
        [
            'css_path' => CSS_PATH,
            'landing_page' => LANDING_PAGE . '/loginuser',
            'meeting_requests' => LANDING_PAGE . '/downloadedmessageselect',
            'upcoming_meetings'=>LANDING_PAGE . '/upcomingmeetings',
            'save_event'=>LANDING_PAGE . '/eventspost',
            'Send' => LANDING_PAGE . '/sendmessage',
            'action' => 'eventEdit',
            'error' => $_SESSION['error'],
            'method' => 'post',
            'method2' => 'post',
            'initial_input_box_value' => null,
            'page_title' => APP_NAME,
            'page_heading_1' => APP_NAME,
            'page_heading_2' => "View events",
            'currentDate' =>date('Y-m-d'),
            'events' => $formattedEventList,
            'disabled_events' => $formattedDisabledEventList,

        ]);


    $processed_output = processOutput($app, $html_output);
    return $processed_output;

})->setName('homepage');

function getEventList($app,$email)
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
    $value = $DetailsModel->checkAllEventsByUser($app, $email);


    $event = [];
    if($value<0){
        return "no  meetings";
    }else{
        for($i =0; $i<=$value ; $i++){
            $idstring = $DetailsModel->getAllEventDetails($app, $email, $i);
            array_push($event,$idstring);

        }
        array_pop($event);
        return $event;
    }
}

function getDisabledEventList($app,$email)
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
    $value = $DetailsModel->checkAllNonActiveEvents($app, $email);


    $event = [];
    if($value<0){
        return "no  meetings";
    }else{
        for($i =0; $i<=$value ; $i++){
            $idstring = $DetailsModel->getDisabledEventDetails($app, $email, $i);
            array_push($event,$idstring);

        }
        array_pop($event);
        return $event;
    }
}


function formatEventList($eventList){
    $value = sizeof($eventList);
    $months = ['January','February','March','April','May','June','July','August', 'September','October','November', 'December'];
    $days = ['Sunday', 'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
    for($i =0; $i<=$value ; $i++){

        if ($eventList[$i][3] != NULL){
            if ($eventList[$i][3] ==1 || $eventList[$i][3] ==21 || $eventList[$i][3] ==31 ){
                $eventList[$i][3] = $eventList[$i][3]."st";
            }
            if ($eventList[$i][3] ==2 || $eventList[$i][3] ==22 ){
                $eventList[$i][3] = $eventList[$i][3]."nd";
            }
            if ($eventList[$i][3] ==3 || $eventList[$i][3] ==23 ){
                $eventList[$i][3] = $eventList[$i][3]."rd";
            }
            if ($eventList[$i][3] !=3 || $eventList[$i][3] !=23 || $eventList[$i][3] !=2 || $eventList[$i][3] !=22 ||
                $eventList[$i][3] !=1 || $eventList[$i][3] !=21 || $eventList[$i][3] !=31) {
                $eventList[$i][3] = "on the ".$eventList[$i][3]."th" ." of every month";
            }
        }
        if($eventList[$i][4] <10 && $eventList[$i][4] != NULL){
            $eventList[$i][3] = "every ".$days[$eventList[$i][4]];
        }
        if($eventList[$i][5] <100 && $eventList[$i][5] != NULL){
            $eventList[$i][3] = "throughout " .$months[$eventList[$i][5]];
        }


    }
    return $eventList;
}

