<?php

/**
 * eventAction.php
 *
 * Author: Matthew
 * Date: 20/03/2021
 *
 * @author Matthew
 */

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->get('/hostedmeetingsaction', function (Request $request, Response $response) use ($app) {
    if (!isset($_SESSION['username'])) {
        $error = "please login";
        $_SESSION['error'] = $error;
        $html_output = $this->view->render($response,
            'homepageform.html.twig');
        return $html_output->withHeader('Location', LANDING_PAGE);
    }
    $email = $_SESSION['username'];
    $tainted_MiD = $_GET["MiD"];
    $cleaned_MiD = cleanupParameters($app, $tainted_MiD);
    if($cleaned_MiD[0] == "D"){
        deleteMeetingFromDatabase($app, $cleaned_MiD, $email);
        $error = "Meeting Deleted";
        $_SESSION['error'] = $error;
        $html_output =  $this->view->render($response,
            'meetingshosted.html.twig');
        return $html_output->withHeader('Location', LANDING_PAGE . '/meetingshosted');
    }
    if($cleaned_MiD[0] == "1"|| $cleaned_MiD[0] == "0"){
        toggleRecurringState($app, $cleaned_MiD, $email);
        if($cleaned_MiD[0] == "1"){
            $error = "Meeting set to repeat";
            $_SESSION['error'] = $error;
            $html_output =  $this->view->render($response,
                'meetingshosted.html.twig');
            return $html_output->withHeader('Location', LANDING_PAGE . '/meetingshosted');
        }
        if($cleaned_MiD[0] == "0"){
            $error = "Meeting disabled";
            $_SESSION['error'] = $error;
            $html_output =  $this->view->render($response,
                'meetingshosted.html.twig');
            return $html_output->withHeader('Location', LANDING_PAGE . '/meetingshosted');
        }
    }
    if($cleaned_MiD[0] == "E"){
        $_SESSION['MiD'] = substr($cleaned_MiD, 1);;
    }
    $meetingDetails = getMeetingDetailsById($app, $cleaned_MiD, $email);
    $userList = getUserDetailsById($app, $cleaned_MiD);
    $recursionValue = getMeetingRecursionTypeById($app, $cleaned_MiD)[0];
    var_dump($userList);
    var_dump($meetingDetails);
    var_dump($recursionValue);

    $html_output = $this->view->render($response,
        'hostedmeetingsaction.html.twig',
        [
            'css_path' => CSS_PATH,
            'landing_page' => LANDING_PAGE . '/loginuser',
            'meeting_requests' => LANDING_PAGE . '/downloadedmessageselect',
            'upcoming_meetings'=>LANDING_PAGE . '/upcomingmeetings',
            'save_event'=>LANDING_PAGE . '/eventspost',
            'Send' => LANDING_PAGE . '/hostedmeetingsedit',
            'action' => 'hostedmeetingsedit',
            'error' => $_SESSION['error'],
            'method' => 'post',
            'method2' => 'post',
            'initial_input_box_value' => null,
            'page_title' => APP_NAME,
            'page_heading_1' => APP_NAME,
            'page_heading_2' => "View events",
            'currentDate' =>date('Y-m-d'),
            'meetingData'=>$meetingDetails,
            'userData' => $userList,
            'neverChecked' => $neverChecked,
            'weeklyChecked' => $weeklyChecked,
            'monthlyChecked' => $monthlyChecked,
            'annuallyChecked' => $annuallyChecked,
        ]);
    $_SESSION['error'] = "";

    $processed_output = processOutput($app, $html_output);
    return $processed_output;

})->setName('homepage');

function recursionHtmlPrep(){
    $radioButtons .=" <br>";
    $radioButtons .="   <p>Set to repeat:" ;
    $radioButtons .="    <input type='radio' id='never' name='repeat' value='never' checked='checked'>";
    $radioButtons .=" <label for='never'>Never</label>" ;
    $radioButtons .="<input type='radio' id='weekly' name='repeat' value='weekly' >";
    $radioButtons .=" <label for='weekly'>Weekly</label>";
    $radioButtons .="<input type='radio' id='monthly' name='repeat' value='monthly'>";
    $radioButtons .="<label for='monthly'>Monthly</label>";
    $radioButtons .="<input type='radio' id='annually' name='repeat' value='annually'>";
    $radioButtons .="<label for='annually'>Annually</label>";
    $radioButtons .="<br>";
    return $radioButtons;
}

function getMeetingDetailsById($app, $MiD, $email){
    $database_wrapper = $app->getContainer()->get('databaseWrapper');
    $sql_queries = $app->getContainer()->get('SQLQueries');
    $DetailsModel = $app->getContainer()->get('RegisterDetailsModel');

    $settings = $app->getContainer()->get('settings');
    $database_connection_settings = $settings['pdo_settings'];
    $MiD = substr($MiD, 1);
    $DetailsModel->setSqlQueries($sql_queries);
    $DetailsModel->setDatabaseConnectionSettings($database_connection_settings);
    $DetailsModel->setDatabaseWrapper($database_wrapper);

    return $DetailsModel->getMeetingDetailsByUserMeetingID($app, $email, $MiD);

}

function getMeetingRecursionTypeById($app, $MiD){
    $database_wrapper = $app->getContainer()->get('databaseWrapper');
    $sql_queries = $app->getContainer()->get('SQLQueries');
    $DetailsModel = $app->getContainer()->get('RegisterDetailsModel');

    $settings = $app->getContainer()->get('settings');
    $database_connection_settings = $settings['pdo_settings'];
    $MiD = substr($MiD, 1);
    $DetailsModel->setSqlQueries($sql_queries);
    $DetailsModel->setDatabaseConnectionSettings($database_connection_settings);
    $DetailsModel->setDatabaseWrapper($database_wrapper);

    return $DetailsModel->getMeetingRecursionByID($app, $MiD);

}

function getUserDetailsById($app, $MiD){
    $database_wrapper = $app->getContainer()->get('databaseWrapper');
    $sql_queries = $app->getContainer()->get('SQLQueries');
    $DetailsModel = $app->getContainer()->get('RegisterDetailsModel');

    $settings = $app->getContainer()->get('settings');
    $database_connection_settings = $settings['pdo_settings'];
    $MiD = substr($MiD, 1);
    $DetailsModel->setSqlQueries($sql_queries);
    $DetailsModel->setDatabaseConnectionSettings($database_connection_settings);
    $DetailsModel->setDatabaseWrapper($database_wrapper);
    $value = $DetailsModel->getUsersByEmailMeetingIdCount($app,$MiD);
    $downloadMessages = [];
    if($value<0){
        return "no  meetings";
    }else{
        for($i =0; $i<=$value ; $i++){
            $idstring = $DetailsModel->getUsersByEmailMeetingId($app, $MiD, $i);

            array_push($downloadMessages,$idstring);
        }
        array_pop($downloadMessages);

        return $downloadMessages;
    }

}

function deleteMeetingFromDatabase($app, $MiD, $email)
{
    $database_wrapper = $app->getContainer()->get('databaseWrapper');
    $sql_queries = $app->getContainer()->get('SQLQueries');
    $DetailsModel = $app->getContainer()->get('RegisterDetailsModel');

    $settings = $app->getContainer()->get('settings');
    $database_connection_settings = $settings['pdo_settings'];
    $MiD = substr($MiD, 1);
    $DetailsModel->setSqlQueries($sql_queries);
    $DetailsModel->setDatabaseConnectionSettings($database_connection_settings);
    $DetailsModel->setDatabaseWrapper($database_wrapper);
    $DetailsModel->deleteAllMeetingUsers($app,$MiD);
    $DetailsModel->deleteMeetingData($app,$MiD, $email);

}
function toggleRecurringState($app, $MiD, $email)
{
    $database_wrapper = $app->getContainer()->get('databaseWrapper');
    $sql_queries = $app->getContainer()->get('SQLQueries');
    $DetailsModel = $app->getContainer()->get('RegisterDetailsModel');

    $settings = $app->getContainer()->get('settings');
    $database_connection_settings = $settings['pdo_settings'];
    $state = $MiD[0];
    $MiD = substr($MiD, 1);
    $DetailsModel->setSqlQueries($sql_queries);
    $DetailsModel->setDatabaseConnectionSettings($database_connection_settings);
    $DetailsModel->setDatabaseWrapper($database_wrapper);
    $DetailsModel->updateMeetingRecursion($app,$MiD,$state);

}

