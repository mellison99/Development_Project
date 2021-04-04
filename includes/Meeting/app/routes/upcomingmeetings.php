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

$app->get('/upcomingmeetings', function (Request $request, Response $response) use ($app) {
    if (!isset($_SESSION['username'])) {
        $error = "please login";
        $_SESSION['error'] = $error;
        $html_output = $this->view->render($response,
            'homepageform.html.twig');
        return $html_output->withHeader('Location', LANDING_PAGE);
    }
    $email = $_SESSION['username'];
    $date = getdate();
    $start = $date[0];
    //var_dump($start);
    $upcomingMeetings = getUpcomingMeetings($app,$email,$start);
    $pastMeetings = getPastMeetings($app,$email,$start);
    $repeatMeetings = getRecurringMeetingHostD($app,$email);
    $searchableId = getRecurringMeetingParticipantD($app, $email);
    $repeatMeetings2 = getRecurringMeetingParticipantDetailsD($app, $searchableId)[0];
    $formattedRepeatMeetings = formatRecurringMeetingInfo($repeatMeetings);
    $formattedRepeatMeetings2 = formatRecurringMeetingInfo($repeatMeetings2);
    $count = sizeof($repeatMeetings2);

    for($i =0; $i<=$count-1 ; $i++){
        $repeatInfo = getdate($repeatMeetings2[$i][1]);
        if($repeatMeetings2[$i][3] == "weekly") {
            $repeatMeetings2[$i][1] = $repeatInfo['weekday'];
        }
        if($repeatMeetings2[$i][3] == "monthly") {
            $repeatMeetings2[$i][1] = $repeatInfo['mday'];
        }
        if($repeatMeetings2[$i][3] == "annually") {
            $repeatMeetings2[$i][1] = $repeatInfo['mday'] . "/".$repeatInfo['mon'];




        }
    }
    //var_dump($repeatMeetings[0]);

    //var_dump($repeatMeetings2);

   //var_dump($upcomingMeetings);
   // var_dump($pastMeetings);

    $html_output = $this->view->render($response,
        'upcomingmeetings.html.twig',
        [
            'css_path' => CSS_PATH,
            'landing_page' => LANDING_PAGE . '/calendar',
            'meeting_requests' => LANDING_PAGE . '/meetingack',
            'upcoming_meetings'=>LANDING_PAGE . '/upcomingmeetings',
            'edit_profile'=> LANDING_PAGE . '/profilemanagement',
            'create_event'=> LANDING_PAGE . '/events',
            'view_event'=> LANDING_PAGE . '/eventView',
            'hosted_meetings'=>LANDING_PAGE . '/meetingshosted',
            'method' => 'post',
            'method2' => 'post',
            'initial_input_box_value' => null,
            'page_title' => APP_NAME,
            'page_heading_1' => APP_NAME,
            'currentDate' =>date('Y-m-d'),
            'upcomingMeetings' =>$upcomingMeetings,
            'pastMeetings' =>$pastMeetings,
            'repeatMeetings1' => $formattedRepeatMeetings,
            'repeatMeetings2' => $formattedRepeatMeetings2,
        ]);


    $processed_output = processOutput($app, $html_output);
    return $processed_output;

})->setName('homepage');

function getUpcomingMeetings($app,$email,$date)
{
    $store_data_result = null;

    $database_wrapper = $app->getContainer()->get('databaseWrapper');
    $sql_queries = $app->getContainer()->get('SQLQueries');
    $DetailsModel = $app->getContainer()->get('RetrieveMeetingModel');

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
//            var_dump($idstring);
            $downloadMessages[$idstring[6]] = $idstring;
        }
        array_pop($downloadMessages);

        return $downloadMessages;
    }
}

function getPastMeetings($app,$email,$date)
{
    $store_data_result = null;

    $database_wrapper = $app->getContainer()->get('databaseWrapper');
    $sql_queries = $app->getContainer()->get('SQLQueries');
    $DetailsModel = $app->getContainer()->get('RetrieveMeetingModel');

    $settings = $app->getContainer()->get('settings');
    $database_connection_settings = $settings['pdo_settings'];

    $DetailsModel->setSqlQueries($sql_queries);
    $DetailsModel->setDatabaseConnectionSettings($database_connection_settings);
    $DetailsModel->setDatabaseWrapper($database_wrapper);
    $value = $DetailsModel->getPastMeetingsByUserCount($app, $email,$date);


    $downloadMessages = [];
    if($value<0){
        array_push($downloadMessages,"no  meetings");
    }else{
        for($i =0; $i<=$value ; $i++){
            $idstring = $DetailsModel->getPastMeetingsByUser($app, $email, $date, $i);

            $downloadMessages[$idstring[6]] = $idstring;
        }
        array_pop($downloadMessages);

        return $downloadMessages;
    }
}

function getRecurringMeetingHostD($app,$email)
{
    $store_data_result = null;

    $database_wrapper = $app->getContainer()->get('databaseWrapper');
    $sql_queries = $app->getContainer()->get('SQLQueries');
    $DetailsModel = $app->getContainer()->get('RetrieveMeetingModel');

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

function getRecurringMeetingParticipantD($app, $email)
{
    $store_data_result = null;

    $database_wrapper = $app->getContainer()->get('databaseWrapper');
    $sql_queries = $app->getContainer()->get('SQLQueries');
    $DetailsModel = $app->getContainer()->get('RetrieveMeetingModel');

    $settings = $app->getContainer()->get('settings');
    $database_connection_settings = $settings['pdo_settings'];

    $DetailsModel->setSqlQueries($sql_queries);
    $DetailsModel->setDatabaseConnectionSettings($database_connection_settings);
    $DetailsModel->setDatabaseWrapper($database_wrapper);
    $value = $DetailsModel->getIdForRecurringMeeting($app, $email);
    //var_dump($value);

    $recurringMeeting = [];
    $recurringMeetingId = [];
    if($value<0){
        return "no  meetings";
    }
    else{
        for($i =0; $i<=$value ; $i++){
            $idstring = $DetailsModel->getIdForRecurringMeetingDetails($app, $email, $i);
            array_push($recurringMeetingId,$idstring[0]);

        }
        array_pop($recurringMeetingId);
        return $recurringMeetingId;
    }
}
function getRecurringMeetingParticipantDetailsD($app, $meetingIDtoSearch){
    $database_wrapper = $app->getContainer()->get('databaseWrapper');
    $sql_queries = $app->getContainer()->get('SQLQueries');
    $DetailsModel = $app->getContainer()->get('RetrieveMeetingModel');

    $settings = $app->getContainer()->get('settings');
    $database_connection_settings = $settings['pdo_settings'];

    $DetailsModel->setSqlQueries($sql_queries);
    $DetailsModel->setDatabaseConnectionSettings($database_connection_settings);
    $DetailsModel->setDatabaseWrapper($database_wrapper);
    $value = $DetailsModel->getStartDurationById($app, $meetingIDtoSearch );
    //var_dump($meetingIDtoSearch);

    $recurringMeeting = [];

    if($value<0){
        return "no  meetings";
    }
    else{
        for($i =0; $i<=$value ; $i++){
            $detailstring = $DetailsModel->getStartDurationByIdDetails($app, $meetingIDtoSearch[$i], $i);

            array_push($recurringMeeting,getRecurringMeetingHostD($app,$detailstring[0]));
//            var_dump($recurringMeeting);


        }
        array_pop($recurringMeeting);
        return $recurringMeeting;


    }
}
function formatRecurringMeetingInfo($repeatMeetings){
    $count = sizeof($repeatMeetings);

    for($i =0; $i<=$count ; $i++){
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



