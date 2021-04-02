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

$app->get('/meetingshosted', function (Request $request, Response $response) use ($app) {
    if (!isset($_SESSION['username'])) {
        $error = "please login";
        $_SESSION['error'] = $error;
        $html_output = $this->view->render($response,
            'homepageform.html.twig');
        return $html_output->withHeader('Location', LANDING_PAGE);
    }
    $date = getdate();
    $start = $date[0];
    //var_dump($_SESSION['error']);
    $email = $_SESSION['username'];
    $upcomingMeetings = getUpcomingMeeting($app,$email,$start);
    $hostedRepeatMeetings = getHostedMeetings($app,$email);
    $disabledHostedRepeatMeetings = getDisabledHostedMeetings($app,$email);
   // var_dump($upcomingMeetings);
    $hostedRepeatMeetings = formatMeetingInfo($hostedRepeatMeetings);
    $disabledHostedRepeatMeetings = formatMeetingInfo($disabledHostedRepeatMeetings);

    //var_dump($upcomingMeetings);

    $html_output = $this->view->render($response,
        'meetingshosted.html.twig',
        [
            'css_path' => CSS_PATH,
            'landing_page' => LANDING_PAGE . '/calendar',
            'meeting_requests' => LANDING_PAGE . '/downloadedmessageselect',
            'upcoming_meetings'=>LANDING_PAGE . '/upcomingmeetings',
            'save_event'=>LANDING_PAGE . '/eventspost',
            'Send' => LANDING_PAGE . '/sendmessage',
            'action' => 'eventspost',
            'error' => $_SESSION['error'],
            'method' => 'post',
            'method2' => 'post',
            'initial_input_box_value' => null,
            'page_title' => APP_NAME,
            'page_heading_1' => APP_NAME,
            'page_heading_2' => "Manage hosted meetings",
            'currentDate' =>date('Y-m-d'),
            'edit_profile'=>LANDING_PAGE . '/profilemanagement',
            'HostedMeetings'=>$hostedRepeatMeetings,
            'disabledHostedMeetings'=> $disabledHostedRepeatMeetings,
            'OneOffHostedMeetings'=>$upcomingMeetings,

        ]);
    $_SESSION['error'] = "";

    $processed_output = processOutput($app, $html_output);
    return $processed_output;

})->setName('homepage');


function getUpcomingMeeting($app,$email,$date)
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
    $value = $DetailsModel->getUpcomingMeetingsByUserCount($app, $email,$date);


    $downloadMessages = [];
    if($value<0){
        return "no  meetings";
    }else{
        for($i =0; $i<=$value ; $i++){
            $idstring = $DetailsModel->getUpcomingMeetingsByUser($app, $email, $date, $i);

            $downloadMessages[$idstring[6]] = $idstring;
        }
        array_pop($downloadMessages);

        return $downloadMessages;
    }
}

function getHostedMeetings($app,$email)
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
    $value = $DetailsModel->getRecurringMeetingsHost($app, $email);


    $recurringMeeting = [];
    if($value<0){
        return "no  meetings";
    }else{
        for($i =0; $i<=$value ; $i++){
            $idstring = $DetailsModel->getRecurringMeetingsHostDetails($app, $email, $i);
            array_push($recurringMeeting,$idstring);

        }
        array_pop($recurringMeeting);
        return $recurringMeeting;
    }
}
function getDisabledHostedMeetings($app,$email)
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
    $value = $DetailsModel->getRecurringMeetingsHostDisabled($app, $email);


    $recurringMeeting = [];
    if($value<0){
        return "no  meetings";
    }else{
        for($i =0; $i<=$value ; $i++){
            $idstring = $DetailsModel->getRecurringMeetingsHostDetailsDisabled($app, $email, $i);
            array_push($recurringMeeting,$idstring);

        }
        array_pop($recurringMeeting);
        return $recurringMeeting;
    }
}


function formatMeetingInfo($repeatMeetings){
    $count = sizeof($repeatMeetings);

    for($i =0; $i<=$count-1 ; $i++){
        $repeatInfo = getdate($repeatMeetings[$i][1]);
        if($repeatMeetings[$i][3] == "weekly") {
            $repeatMeetings[$i][1] = $repeatInfo['weekday'];
        }
        if($repeatMeetings[$i][3] == "monthly") {
            $repeatMeetings[$i][1] = $repeatInfo['mday'];
        }
        if($repeatMeetings[$i][3] == "annually") {
            $repeatMeetings[$i][1] = $repeatInfo['mday'] . "/".$repeatInfo['mon'];
        }
        if($repeatMeetings[$i][3] == "never") {
            unset($repeatMeetings[$i]);

        }
    }
    return $repeatMeetings;
}


