<?php
/**
 * createmeetingform.php
 *
 * Author: Matthew
 * Date: 17/01/2021
 *
 * @author Matthew
 */

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;


$app->get('/createmeetingform', function(Request $request, Response $response) use ($app)
    {
    if(!isset($_SESSION['username']))
    {
        $error = "please login";
        $_SESSION['error'] = $error;
        $html_output =  $this->view->render($response,
                'homepageform.html.twig');
            return $html_output->withHeader('Location', LANDING_PAGE);
    }
$error = $_SESSION['error'];
    //var_dump($_GET["date"]);
    $_SESSION['date'] = $_GET["date"];
    $test = getMeetingbyDateUser($app,$_SESSION['username'],$_SESSION['date']);
        $yearInString = (substr($_SESSION['date'],0,4));
        $monthInString=(substr($_SESSION['date'],5,2));
        $dayInString = (substr($_SESSION['date'],8,2));
        $monthInInt = (int)$monthInString;
        $dayInInt = (int)$dayInString;


        $starttime = mktime(0,0,0,$monthInInt,$dayInInt,$yearInString);
        $endtime = getdate($starttime)[0]+(60*30);
//        var_dump(getdate($starttime));
        $weekdayVal = getdate($starttime)['wday']+1;
        $dateVal = getdate($starttime)['mday'];
        $monthVal = getdate($starttime)['mon'];
        $arrayOfHostedRepeatMeetings = getRecurringMeetingHost($app,$_SESSION['username']);
        $meetingIDtoSearch = getRecurringMeetingParticipant($app,$_SESSION['username']);
        $recurringmeetingDetails = getRecurringMeetingParticipantDetails($app, $meetingIDtoSearch);
        $meetingIDtoSearch = getRecurringMeetingParticipant($app,$_SESSION['username']);
        $recurringmeetingDetails2 = getRecurringMeetingParticipantDetails($app, $meetingIDtoSearch)[0];

        $recurringWhereHost = sortRecurringMeetings($arrayOfHostedRepeatMeetings,$starttime);

        $recurringWhereParticipant = sortRecurringMeetings($recurringmeetingDetails2,$starttime);


//        var_dump($weekdayVal);
        $eventsOnDay = getEventbyDayUser($app,$_SESSION['username'],$weekdayVal-1);
//        var_dump($weekdayVal-1);
//        var_dump($eventsOnDay);
        $eventsInMonth =getEventbyMonthUser($app,$_SESSION['username'],$monthVal);
        $eventsOnDate =getEventbyDateUser($app,$_SESSION['username'],$dateVal);
        $html_output =  $this->view->render($response,
            'createmeeting.html.twig',
            [
                'css_path' => CSS_PATH,
                'landing_page' => LANDING_PAGE . '/calendar',
                'page_title' => APP_NAME,
                'method' => 'post',
                'action' => 'createmeeting',
                'initial_input_box_value' => null,
                'page_heading_1' => 'Create a meeting',
                'page_heading_2' => 'Meeting details',
                'date' => $_GET["date"],
                'Add'=> LANDING_PAGE .'/createmeetingform'."?date=".$date,
                'Send' => LANDING_PAGE . '/createmeeting',
                'Calendar' => LANDING_PAGE . '/calendar',
                'meetingsOnDate'=>$test,
                'RepeatingmeetingsOnDate'=>$recurringWhereHost,
                'RepeatingmeetingsOnDate2' =>$recurringWhereParticipant,
                'eventsOnDateByMonth'=>$eventsInMonth,
                'eventsOnDateByDate'=>$eventsOnDate,
                'eventsOnDateByDay'=>$eventsOnDay,
                'error' => $error,
            ]);
        processOutput($app, $html_output);
        return $html_output;
    })->setName('createmeeting');




function getMeetingbyDateUser($app,$email,$date)
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
    $value = $DetailsModel->getMeetingbyDateUser($app, $email,$date);


    $downloadMessages = [];
    if($value<0){
        return "no  meetings";
    }else{
        for($i =0; $i<=$value ; $i++){
            $idstring = $DetailsModel->getMeetingbyDateUserDetails($app, $email, $date, $i);

            $downloadMessages[$idstring[5]] = $idstring;
        }
        array_pop($downloadMessages);
        return $downloadMessages;
    }
}

function getEventbyDayUser($app,$email,$day)
{
    $store_data_result = null;
//    var_dump($day);
    $database_wrapper = $app->getContainer()->get('databaseWrapper');
    $sql_queries = $app->getContainer()->get('SQLQueries');
    $DetailsModel = $app->getContainer()->get('CheckTimesModel');

    $settings = $app->getContainer()->get('settings');
    $database_connection_settings = $settings['pdo_settings'];

    $DetailsModel->setSqlQueries($sql_queries);
    $DetailsModel->setDatabaseConnectionSettings($database_connection_settings);
    $DetailsModel->setDatabaseWrapper($database_wrapper);

    $value = $DetailsModel->checkEventByDayUser($app, $email,$day);


    $weekdayEvent = [];
    if($value<0){
        return "no  meetings";
    }else{
        for($i =0; $i<=$value ; $i++){
            $idstring = $DetailsModel->checkEventByDayUserDetails($app, $email, $day, $i);
            array_push($weekdayEvent,$idstring);

        }
        array_pop($weekdayEvent);
        return $weekdayEvent;
    }
}
function getEventbyDateUser($app,$email,$date)
{
    $store_data_result = null;

    $database_wrapper = $app->getContainer()->get('databaseWrapper');
    $sql_queries = $app->getContainer()->get('SQLQueries');
    $DetailsModel = $app->getContainer()->get('CheckTimesModel');

    $settings = $app->getContainer()->get('settings');
    $database_connection_settings = $settings['pdo_settings'];

    $DetailsModel->setSqlQueries($sql_queries);
    $DetailsModel->setDatabaseConnectionSettings($database_connection_settings);
    $DetailsModel->setDatabaseWrapper($database_wrapper);
    $value = $DetailsModel->checkEventByDateUser($app, $email,$date);


    $eventsByDate = [];
    if($value<0){
        return "no  meetings";
    }else{
        for($i =0; $i<=$value-1 ; $i++){
            $idstring = $DetailsModel->checkEventByDateUserDetails($app, $email, $date, $i);
            array_push($eventsByDate,$idstring);
        }
        return $eventsByDate;
    }
}
function getEventbyMonthUser($app,$email,$month)
{
    $store_data_result = null;

    $database_wrapper = $app->getContainer()->get('databaseWrapper');
    $sql_queries = $app->getContainer()->get('SQLQueries');
    $DetailsModel = $app->getContainer()->get('CheckTimesModel');

    $settings = $app->getContainer()->get('settings');
    $database_connection_settings = $settings['pdo_settings'];

    $DetailsModel->setSqlQueries($sql_queries);
    $DetailsModel->setDatabaseConnectionSettings($database_connection_settings);
    $DetailsModel->setDatabaseWrapper($database_wrapper);
    $value = $DetailsModel->checkEventByMonthUser($app, $email,$month-1);


    $monthEvent = [];
    if($value<0){
        return "no  meetings";
    }else{
        for($i =0; $i<=$value ; $i++){
            $idstring = $DetailsModel->checkEventByMonthUserDetails($app, $email, $month-1, $i);

            array_push($monthEvent,$idstring);

        }
         array_pop($monthEvent);

        return $monthEvent;
    }
}

function getRecurringMeetingHost($app,$email)
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
function getRecurringMeetingParticipant($app, $email)
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
function getRecurringMeetingParticipantDetails($app, $meetingIDtoSearch){
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

            array_push($recurringMeeting,getRecurringMeetingHost($app,$detailstring[0]));



        }
        //array_pop($recurringMeeting);
        return $recurringMeeting;


    }
}
function sortRecurringMeetings($arrayOfHostedRepeatMeetings,$starttime ){
    $repeatingMeetings = [];
    $numOfRepeatHostedMeetings = sizeof($arrayOfHostedRepeatMeetings);
    for($i =0; $i<=$numOfRepeatHostedMeetings-1 ; $i++){
        $repeatInfo = getdate($arrayOfHostedRepeatMeetings[$i][1]);
//        var_dump($repeatInfo);
        if($arrayOfHostedRepeatMeetings[$i][3] == "weekly") {

            if($repeatInfo['wday'] == getdate($starttime)['wday'] ){
                //var_dump("add weekly");
                $mins = str_pad ( $repeatInfo['minutes'] , 2 , "0" ,  STR_PAD_LEFT);
                $infoString = $repeatInfo['hours'] .":". $mins. " for " . $arrayOfHostedRepeatMeetings[$i][2]." minutes ". "weekly";
                array_push($repeatingMeetings,$infoString);

            }
        }
        if($arrayOfHostedRepeatMeetings[$i][3] == "monthly") {
            if($repeatInfo['mday'] == getdate($starttime)['mday'] ){

                $mins = str_pad ( $repeatInfo['minutes'] , 2 , "0" ,  STR_PAD_LEFT);
                $infoString = $repeatInfo['hours'] .":". $mins. " for " . $arrayOfHostedRepeatMeetings[$i][2]." minutes " . "monthly";
                array_push($repeatingMeetings,$infoString);

            }
        }
        if($arrayOfHostedRepeatMeetings[$i][3] == "annually") {

            if($repeatInfo['yday'] == getdate($starttime)['yday'] ){
                $mins = str_pad ( $repeatInfo['minutes'] , 2 , "0" ,  STR_PAD_LEFT);
                $infoString = $repeatInfo['hours'] .":". $mins. " for " . $arrayOfHostedRepeatMeetings[$i][2] . " minutes ". "annually";
                array_push($repeatingMeetings,$infoString);

            }
        }
    }

    return $repeatingMeetings;

}

