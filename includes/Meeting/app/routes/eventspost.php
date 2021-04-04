<?php
/**
 * eventsPost.php
 *
 * Author: Matthew
 * Date: 17/01/2021
 *
 * @author Matthew
 */
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
$app->post(
'/eventspost',
    function(Request $request, Response $response) use ($app) {
        if (!isset($_SESSION['username'])) {
            $error = "please login";
            $_SESSION['error'] = $error;
            $html_output = $this->view->render($response,
                'homepageform.html.twig');
            return $html_output->withHeader('Location', LANDING_PAGE);
        }
        $tainted_parameters = $request->getParsedBody();
        if($_POST['dayOfWeek'] == ''&& $_POST['monthInYear'] == '' && $_POST['date'] == '') {
       // var_dump($tainted_parameters['day']);

                    $error = 'please select when to repeat';
                    $_SESSION['error'] = $error;
                    $html_output = $this->view->render($response,
                        'eventspost.html.twig');

                    return $html_output->withHeader('Location', LANDING_PAGE . "/events");
        }

        if($_POST['time'] == '' || $_POST['duration'] == '' || $_POST['description'][0] == '' )
        {
            $error = 'please fill in all relevant details';
            $_SESSION['error'] = $error;
            $html_output =  $this->view->render($response,
                'eventspost.html.twig');
            $date = $_SESSION['date'];
            return $html_output->withHeader('Location', LANDING_PAGE . "/events");

        }
$email = ($_SESSION['username']);
$error = "";







$cleaned_parameters = cleanupParameter($app, $tainted_parameters);
$eventList = getEventUserDetails($app,$_SESSION['username']);
for($i =0; $i<sizeof($eventList); $i++) {
    $eventsClash = checkEventTimeslot($eventList[$i], $cleaned_parameters);
}

if($eventsClash >0){
    $error = 'Event already exists for this time slot';
    $_SESSION['error'] = $error;
    $html_output =  $this->view->render($response,
        'eventspost.html.twig');
    $date = $_SESSION['date'];
    return $html_output->withHeader('Location', LANDING_PAGE . "/events");
}
//var_dump(getRecurringMeetingParticipants($app, $email));
        $arrayOfHostedRepeatMeetings = getRecurringMeetingHost2($app,$_SESSION['username']);
        //var_dump($arrayOfHostedRepeatMeetings);
        $meetingIDtoSearch = getRecurringMeetingParticipants($app,$_SESSION['username']);
        $recurringmeetingDetails = getRecurringMeetingParticipantDetail($app, $meetingIDtoSearch);
        $recurringmeetingDetails = $recurringmeetingDetails[0];

if(checkRecurringTimeslot($arrayOfHostedRepeatMeetings,$cleaned_parameters )!= NULL
    || checkRecurringTimeslot($recurringmeetingDetails,$cleaned_parameters )!= NULL){
    $error = 'Meeting already exists for this time slot';
    $_SESSION['error'] = $error;
    $html_output =  $this->view->render($response,
        'eventspost.html.twig');
    $date = $_SESSION['date'];
    return $html_output->withHeader('Location', LANDING_PAGE . "/events");
}
if($eventsClash == NULL) {
    $error = 'Successfully added event';
    $_SESSION['error'] = $error;
    storeEventDetails($app, $cleaned_parameters);
    $html_output =  $this->view->render($response,
        'eventspost.html.twig');
    $date = $_SESSION['date'];
    return $html_output->withHeader('Location', LANDING_PAGE . "/events");
}
//
$html_output =  $this->view->render($response,
'eventspost.html.twig',
[
'landing_page' => LANDING_PAGE . '/loginuser',
'css_path' => CSS_PATH,
'page_title' => APP_NAME,
'method' => 'post',
'tester' => $tainted_parameters,
'page_heading_1' => 'Create a Meeting',
'page_heading_2' => 'Meeting details'
]);
processOutput($app, $html_output);
return $html_output;
})->setName('eventspost');
function cleanupParameter($app, $tainted_parameters)
{
    $cleaned_parameters = [];
    $validator = $app->getContainer()->get('validator');

    $tainted_email = $_SESSION['username'];
    $tainted_time = $tainted_parameters['time'];
    $tainted_duration = $tainted_parameters['duration'];
    $tainted_description = $tainted_parameters['description'];
    $tainted_date = $tainted_parameters['date'];
    $tainted_day = (int)$tainted_parameters['dayOfWeek'];
    $tainted_month = (int)$tainted_parameters['monthInYear'];

    $cleaned_parameters['sanitised_email'] = $validator->sanitiseString($tainted_email);
    $cleaned_parameters['sanitised_time'] = $validator->sanitiseString($tainted_time);
    $cleaned_parameters['sanitised_duration'] = $validator->sanitiseString($tainted_duration);
    $cleaned_parameters['sanitised_description'] = $validator->sanitiseString($tainted_description);
    $cleaned_parameters['sanitised_date'] = $validator->sanitiseString($tainted_date);
    $cleaned_parameters['sanitised_dayOfWeek'] = $tainted_day;
    if($cleaned_parameters['sanitised_dayOfWeek'] == 0){
        $cleaned_parameters['sanitised_dayOfWeek'] = 100;
    }
    $cleaned_parameters['sanitised_monthInYear'] = $tainted_month;
    if($cleaned_parameters['sanitised_monthInYear'] == 0){
        $cleaned_parameters['sanitised_monthInYear'] = 100;
    }

    return $cleaned_parameters;

}
function storeEventDetails($app, array $cleaned_parameters)
{
    $store_data_result = null;

    $database_wrapper = $app->getContainer()->get('databaseWrapper');
    $sql_queries = $app->getContainer()->get('SQLQueries');
    $DetailsModel = $app->getContainer()->get('CreateMeetingsModel');

    $settings = $app->getContainer()->get('settings');
    $database_connection_settings = $settings['pdo_settings'];

    $DetailsModel->setSqlQueries($sql_queries);
    $DetailsModel->setDatabaseConnectionSettings($database_connection_settings);
    $DetailsModel->setDatabaseWrapper($database_wrapper);

    $DetailsModel->setEventDetails($app, $cleaned_parameters);
}

function getEventUserDetails($app,$email)
{
//    var_dump($email);
//    var_dump($day);
    $store_data_result = null;

    $database_wrapper = $app->getContainer()->get('databaseWrapper');
    $sql_queries = $app->getContainer()->get('SQLQueries');
    $DetailsModel = $app->getContainer()->get('CheckTimesModel');
    $DetailsModel2 = $app->getContainer()->get('RetrieveMeetingModel');

    $settings = $app->getContainer()->get('settings');
    $database_connection_settings = $settings['pdo_settings'];

    $DetailsModel->setSqlQueries($sql_queries);
    $DetailsModel->setDatabaseConnectionSettings($database_connection_settings);
    $DetailsModel->setDatabaseWrapper($database_wrapper);
    $DetailsModel2->setSqlQueries($sql_queries);
    $DetailsModel2->setDatabaseConnectionSettings($database_connection_settings);
    $DetailsModel2->setDatabaseWrapper($database_wrapper);
    $value = $DetailsModel->checkAllEventsByUser($app, $email);


    $event = [];
    if($value<0){
        return "no  meetings";
    }else{
        for($i =0; $i<=$value ; $i++){
            $idstring = $DetailsModel2->getAllEventDetails($app, $email, $i);
            array_push($event,$idstring);

        }
        array_pop($event);
        return $event;
    }
}
function checkEventTimeslot($eventsToCheck,$newMeetingArray ){
    //var_dump($eventsToCheck);
    $eventsAtTime = 0;
    $newDuration = $newMeetingArray['sanitised_duration'];
    if($newDuration >=60){
        $newDuration =(100 * (($newDuration/60))) + ($newDuration % 60);
    }
    $startVal =  $newMeetingArray['sanitised_time'][0].$newMeetingArray['sanitised_time'][1].$newMeetingArray['sanitised_time'][3].$newMeetingArray['sanitised_time'][4];
    $startVal = (int) $startVal;
    $endVal = $startVal+$newDuration;
    $startTime = $newMeetingArray[0];
    $timesToLoop = sizeof($eventsToCheck);
//    var_dump($eventsToCheck);
//    var_dump($newMeetingArray);
//    for($i =0; $i<sizeof($eventsToCheck); $i++){

        $duration = (int) $eventsToCheck[1];
        if($duration >=60){
            $duration =(100 * (($duration/60))) + ($duration % 60);
        }
         $storedStart =  $eventsToCheck[0][0].$eventsToCheck[0][1].$eventsToCheck[0][3].$eventsToCheck[0][4];
        $storedStart = (int) $storedStart;
       $storedEnd = $storedStart+$duration;


        if ($eventsToCheck[3] == $newMeetingArray['sanitised_date'] && $startVal >=$storedStart && $endVal <=$storedEnd)
        {
            $eventsAtTime=$eventsAtTime+1;
            return $eventsAtTime;
        }

        if ($eventsToCheck[4] ==  $newMeetingArray['sanitised_dayOfWeek'] && $startVal >=$storedStart && $endVal <=$storedEnd)
        {
            $eventsAtTime=$eventsAtTime+1;
            return $eventsAtTime;
        }

        if ((int)$eventsToCheck[5] ==  $newMeetingArray['sanitised_monthInYear'] && $startVal >=$storedStart && $endVal <=$storedEnd)
        {
            $eventsAtTime=$eventsAtTime+1;
            return $eventsAtTime;
        }



}

function getRecurringMeetingParticipants($app, $email)
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
    // var_dump($value);

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
function getRecurringMeetingParticipantDetail($app, $meetingIDtoSearch){
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

            array_push($recurringMeeting,getRecurringMeetingHost2($app,$detailstring[0]));



        }
        array_pop($recurringMeeting);
        return $recurringMeeting;


    }
}

function checkRecurringTimeslot($arrayOfHostedRepeatMeetings,$newMeetingArray ){
    $meetingsAtTime = 0;
    $newDuration = $newMeetingArray['sanitised_duration'];
    if($newDuration >=60){
        $newDuration =(100 * (($newDuration/60))) + ($newDuration % 60);
    }


    $startVal =  $newMeetingArray['sanitised_time'][0].$newMeetingArray['sanitised_time'][1].$newMeetingArray['sanitised_time'][3].$newMeetingArray['sanitised_time'][4];
    $startVal = (int) $startVal;
    $endVal = $startVal+$newDuration;
    for($i =0; $i<sizeof($arrayOfHostedRepeatMeetings); $i++){

        if ($arrayOfHostedRepeatMeetings[$i][3] != "never"){
            $duration = (int) $arrayOfHostedRepeatMeetings[$i][2]*60;
            $dateInfo = getdate($arrayOfHostedRepeatMeetings[$i][1]);
            $dateInfo2 = getdate($arrayOfHostedRepeatMeetings[$i][1]+$duration);
            $storedStart = str_pad($dateInfo['hours']. $dateInfo['minutes'],4,"0",STR_PAD_RIGHT);
            $storedEnd = str_pad($dateInfo2['hours']. $dateInfo2['minutes'],4,"0",STR_PAD_RIGHT);
           if ($arrayOfHostedRepeatMeetings[$i][3] == "weekly" && $dateInfo['wday'] == $newMeetingArray['sanitised_dayOfWeek'] && $startVal >=$storedStart && $endVal <=$storedEnd)
            {
                $meetingsAtTime=$meetingsAtTime+1;
                return $meetingsAtTime;

            }

            if ($dateInfo['month'] == $newMeetingArray['sanitised_monthInYear'] && $startVal >=$storedStart && $endVal <=$storedEnd)
            {
                $meetingsAtTime=$meetingsAtTime+1;
                return $meetingsAtTime;
            }
            if ($arrayOfHostedRepeatMeetings[$i][3] == "monthly" && $dateInfo['month'] == $newMeetingArray['sanitised_date'] && $startVal >=$storedStart && $endVal <=$storedEnd)
            {
                $meetingsAtTime=$meetingsAtTime+1;
                return $meetingsAtTime;
            }

        }



    }
}