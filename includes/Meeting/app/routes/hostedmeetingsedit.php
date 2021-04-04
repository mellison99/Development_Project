<?php
/**
 * hostedmeetingsedit.php
 *
 * Author: Matthew
 * Date: 23/03/2021
 *
 * @author Matthew
 */

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->post(
    '/hostedmeetingsedit',
    function(Request $request, Response $response) use ($app)
    {
        if(!isset($_SESSION['username']))
        {
            $error = "please login";
            $_SESSION['error'] = $error;
            $html_output =  $this->view->render($response,
                'homepageform.html.twig');
            return $html_output->withHeader('Location', LANDING_PAGE);
        }

        $email = ($_SESSION['username']);

        $tainted_parameters = $request->getParsedBody();
        $cleaned_MiD = $_SESSION['MiD'];
        $cleaned_parameters = cleanupParametersForUpdate($app, $tainted_parameters,$cleaned_MiD);

        if(checkEmailEdit($app, $cleaned_parameters)==false){
            $error = 'one or more emails entered do not exist';
            $_SESSION['error'] = $error;
            $html_output =  $this->view->render($response,
                'created_meeting.html.twig');
            $date = $_SESSION['date'];
            return $html_output->withHeader('Location', LANDING_PAGE . "/hostedmeetingsaction?MiD=E".$_SESSION['MiD']);
        }


//var_dump($cleaned_parameters['sanitised_start']);

        $StartVal=getdate($cleaned_parameters['sanitised_start'])['hours'].getdate($cleaned_parameters['sanitised_start'])['minutes'];
        $EndVal=getdate($cleaned_parameters['sanitised_end'])['hours'].getdate($cleaned_parameters['sanitised_end'])['minutes'];
        $StartVal = str_pad($StartVal,4,"0",STR_PAD_RIGHT);
        $EndVal = str_pad($EndVal,4,"0",STR_PAD_RIGHT);
        $weekdayVal = (getdate($cleaned_parameters['sanitised_start'])['weekday']);
        $weekdayIntVal = (getdate($cleaned_parameters['sanitised_start'])['wday']);
        $yearVal = (getdate($cleaned_parameters['sanitised_start'])['yday']);
        $dateVal=(getdate($cleaned_parameters['sanitised_start'])['mday']);
        $monthVal=(getdate($cleaned_parameters['sanitised_start'])['month']);
        $monthNumVal=(getdate($cleaned_parameters['sanitised_start'])['mon']);
        $newMeetingArray =[$StartVal,$EndVal,$weekdayVal,$yearVal,$dateVal,$monthVal,$monthNumVal,$weekdayIntVal];
        $usersToCheck = $cleaned_parameters['sanitised_user'];
        $eventsToCheck = getEventDetails($app, $_SESSION['username']);
        $eventCountInTimeslot = checkEventTimeslots($eventsToCheck,$newMeetingArray );
        $arrayOfHostedRepeatMeetings = getRecurringMeetingHostEx($app,$_SESSION['username'],$cleaned_MiD);
        $meetingIDtoSearch = getRecurringMeetingParticipantEx($app,$_SESSION['username'],$cleaned_MiD);
        $recurringmeetingDetails = getRecurringMeetingParticipantDetails2($app, $meetingIDtoSearch);
        $recurringmeetingDetails = $recurringmeetingDetails[0];

        if($cleaned_parameters['sanitised_start'] < $cleaned_parameters['sanitised_Id']){
            $html_output =  $this->view->render($response,
                'created_meeting.html.twig');
            $date = $_SESSION['date'];
            $error="cannot create a meeting where the specified start time has passed";
            $_SESSION['error'] = $error;
            return $html_output->withHeader('Location', LANDING_PAGE . "/createmeetingform?date=".$date);
        }
        $startTime = ($cleaned_parameters['sanitised_time'][0].$cleaned_parameters['sanitised_time'][1].$cleaned_parameters['sanitised_time'][3].$cleaned_parameters['sanitised_time'][4]);
        $startTime =  (int)$startTime;


        $openingTime = 830;
        $closingTime = 1730;
        if($startTime < $openingTime || $startTime >= $closingTime){
            $html_output =  $this->view->render($response,
                'created_meeting.html.twig');
            $date = $_SESSION['date'];
            $error="This is outside of office hours";
            $_SESSION['error'] = $error;
            return $html_output->withHeader('Location', LANDING_PAGE . "/createmeetingform?date=".$date);
        }

        if($eventCountInTimeslot != NULL ){
            $_SESSION['error'] = "Event organised at this time";
            var_dump($_SESSION['error']);
//            $html_output =  $this->view->render($response,
//                'created_meeting.html.twig');
//            return $html_output->withHeader('Location', LANDING_PAGE . "/hostedmeetingsaction?MiD=E".$_SESSION['MiD']);
        }

        for ($i =0; $i<sizeOf($usersToCheck); $i++){
            $eventsToCheck = getEventDetails($app, $usersToCheck[$i]);
            //var_dump($eventsToCheck);
            $eventCountInTimeslot = checkEventTimeslots($eventsToCheck,$newMeetingArray );
            //var_dump($eventCountInTimeslot);
            if($eventCountInTimeslot != NULL ){
                $_SESSION['error'] = "Event organised at this time";
                $html_output =  $this->view->render($response,
                    'created_meeting.html.twig');
                return $html_output->withHeader('Location', LANDING_PAGE . "/hostedmeetingsaction?MiD=E".$_SESSION['MiD']);
            }


           // deleteMeetingToUpdate($app, $cleaned_MiD, $email);
            if(checkRecurringTimeslots($arrayOfHostedRepeatMeetings,$newMeetingArray )>1
                || checkRecurringTimeslots($recurringmeetingDetails,$newMeetingArray )>1){
                var_dump("recurringTimeslot");
//                $html_output =  $this->view->render($response,
//                    'created_meeting.html.twig');
//                return $html_output->withHeader('Location', LANDING_PAGE . "/hostedmeetingsaction?MiD=E".$_SESSION['MiD']);
            }
        }




        if(checkRecurringTimeslots($arrayOfHostedRepeatMeetings,$newMeetingArray )>1
            || checkRecurringTimeslots($recurringmeetingDetails,$newMeetingArray )>1){
            $html_output =  $this->view->render($response,
                            'created_meeting.html.twig');
                        return $html_output->withHeader('Location', LANDING_PAGE . "/hostedmeetingsaction?MiD=E".$_SESSION['MiD']);
        }
        for ($i =0; $i<sizeOf($usersToCheck)-1; $i++){
            $arrayOfHostedRepeatMeetings = getRecurringMeetingHostEx($app,$usersToCheck[$i], $cleaned_MiD);
            $meetingIDtoSearch = getRecurringMeetingParticipantEx($app, $usersToCheck[$i], $cleaned_MiD);
            $recurringmeetingDetails = getRecurringMeetingParticipantDetails2($app, $meetingIDtoSearch);

            $recurringmeetingDetails = $recurringmeetingDetails[0];

            if(checkRecurringTimeslots($arrayOfHostedRepeatMeetings,$newMeetingArray )>1
                || checkRecurringTimeslots($recurringmeetingDetails,$newMeetingArray )>1){
                $html_output =  $this->view->render($response,
                    'created_meeting.html.twig');
                return $html_output->withHeader('Location', LANDING_PAGE . "/hostedmeetingsaction?MiD=E".$_SESSION['MiD']);
            }
        }

        if($_POST['time'] == '' || $_POST['duration'] == '' || $_POST['name'][0] == '' )
        {
            $error = 'please fill in all details to register';
            $_SESSION['error'] = $error;
            $html_output =  $this->view->render($response,
                'created_meeting.html.twig');
            $date = $_SESSION['date'];
            return $html_output->withHeader('Location', LANDING_PAGE . "/hostedmeetingsaction?MiD=E".$_SESSION['MiD']);

        }





        $message_content = makeEditM2MString($cleaned_parameters,$email);
        $numbersToMessage = getNumberbyUser($app,$cleaned_parameters);

        $meetingsToCheck = checkTimeslotEx($app,$cleaned_parameters,$cleaned_MiD);
       // var_dump($meetingsToCheck);
        for($i =0; $i<$meetingsToCheck; $i++){
            checkTimeslotUsersEx($app,$cleaned_parameters,$meetingsToCheck[$i],$cleaned_MiD);
            $usersAtTimeslotArray = checkTimeslotUsersEx($app,$cleaned_parameters,$meetingsToCheck[$i],$cleaned_MiD);
        }
        //var_dump(sizeof($usersAtTimeslotArray));
        $meetingsattending = 0;
        for($i =0; $i<sizeof($usersAtTimeslotArray)-1; $i++){

            for($j =0; $j<sizeof($cleaned_parameters['sanitised_user']); $j++){
                $arrayToSearch = $usersAtTimeslotArray[$i];
                $meetingsattending = $meetingsattending + array_search( $cleaned_parameters['sanitised_user'][$j],$arrayToSearch);
                $meetingsattending = $meetingsattending + array_search($email, $usersAtTimeslotArray[$i]);
            }

        }
        //var_dump($meetingsattending);
           if(checkTimeslotEx($app,$cleaned_parameters,$cleaned_MiD)=="0" and $meetingsattending <= 0) {



            $meetingID = 2;
            deleteMeetingToUpdate($app, $cleaned_MiD, $email);
            storeMeetingDetails($app, $cleaned_parameters, $email);
            storeMeetingUserDetails($app,$cleaned_parameters, $meetingID);
            storeMeetingRecursion($app,$cleaned_parameters);
            $success = true;
            $amountOfNumbers = count($numbersToMessage);

            for($i =0; $i<$amountOfNumbers; $i++){

                sendM2MMessage($app, $message_content, $numbersToMessage[$i]);
            }
    }
        else{
            $error = 'Timeslot already booked ' .$meetingsToCheck;
            $_SESSION['error'] = $error;
            $html_output =  $this->view->render($response,
                'created_meeting.html.twig');
            return $html_output->withHeader('Location', LANDING_PAGE . "/hostedmeetingsaction?MiD=E".$_SESSION['MiD']);
        }
        if($success == true){
            $name = $cleaned_MiD .$_FILES['file']['name'];
            $target_dir = "meetingdocs/";
            $target_file = $target_dir . basename($_FILES["file"]["name"]);
            // Select file type
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            // Valid file extensions
            $extensions_arr = array("txt", "docx", "doc", "pdf");

            // Check extension
            if (in_array($imageFileType, $extensions_arr)) {

                $cleaned_parameters = ['sanitised_id'=> $cleaned_MiD];
                storeMeetingDoc($app, $name, $cleaned_MiD);
                // Upload file
                move_uploaded_file($_FILES['file']['tmp_name'], $target_dir . $name);
                $information = move_uploaded_file($_FILES['file']['tmp_name'], $target_dir . $name);
            }
        }
        $html_output =  $this->view->render($response,
            'meetingshosted.html.twig');
        return $html_output->withHeader('Location', LANDING_PAGE . "/meetingshosted");
        //getSimbyEmail($app, $email);
        //$_SESSION['error'] = $error;
        $html_output =  $this->view->render($response,
            'created_meeting.html.twig',
            [
                'landing_page' => LANDING_PAGE . '/loginuser',
                'css_path' => CSS_PATH,
                'page_title' => APP_NAME,
                'method' => 'post',
                'action' => 'sendmessages',
                'page_heading_1' => 'Create a Meeting',
                'page_heading_2' => 'Meeting details',
                'error' => $error,
                'user'=> $cleaned_parameters['sanitised_user'],
                'time'=> $cleaned_parameters['sanitised_time'],
                'host'=> $email
            ]);

        processOutput($app, $html_output);

        return $html_output;
    })->setName('hostedmeetingsedit');


function cleanupParametersForUpdate($app, $tainted_parameters, $meetingId)
{
//    var_dump($tainted_parameters['date']);
    $yearInString = (substr($tainted_parameters['date'],0,4));
    $monthInString=(substr($tainted_parameters['date'],5,2));
    $dayInString = (substr($tainted_parameters['date'],8,2));
    $monthInInt = (int)$monthInString;
    $dayInInt = (int)$dayInString;
    $cleaned_parameters = [];
    $validator = $app->getContainer()->get('validator');
    $tainted_time = $tainted_parameters['time'];
    $tainted_duration = $tainted_parameters['duration'];
    $tainted_subject = $tainted_parameters['subject'];
    $tainted_notes = $tainted_parameters['notes'];
    $tainted_repeat = $tainted_parameters['repeat'];
    $tainted_users = $tainted_parameters['name'];
    $cleaned_parameters['sanitised_time'] = $validator->sanitiseString($tainted_time);
    $cleaned_parameters['sanitised_duration'] = $validator->sanitiseString($tainted_duration);
    $cleaned_parameters['sanitised_Id'] = $meetingId;
    $cleaned_parameters['sanitised_subject'] = $validator->sanitiseString($tainted_subject);
    $cleaned_parameters['sanitised_notes'] = $validator->sanitiseString($tainted_notes);
    $cleaned_parameters['sanitised_repeat'] = $validator->sanitiseString($tainted_repeat);
    $sanitised_users = [];
    //var_dump($tainted_users[0]);
    $value = sizeOf($tainted_users)-1;
    //var_dump($value);
    for($i =0; $i<=$value; $i++){
        $sanitised_user = $validator->sanitiseString($tainted_users[$i]);
        array_push($sanitised_users,$sanitised_user);
    }
    //var_dump($sanitised_users);
    $hourString = substr($cleaned_parameters['sanitised_time'],0,2);
    $minString = substr($cleaned_parameters['sanitised_time'],3,2);
   // var_dump((int)$hourString);
    //var_dump((int)$minString);
    $startTime = mktime((int)$hourString,(int)$minString,0,$monthInInt,$dayInInt,$yearInString);

    $endTime = getdate($startTime)[0]+(60*(int)$cleaned_parameters['sanitised_duration']);
    $cleaned_parameters['sanitised_start'] = $startTime;
    $cleaned_parameters['sanitised_end'] = $endTime;
    $cleaned_parameters['sanitised_user'] = $sanitised_users;
    $cleaned_parameters['sanitised_date'] = $_SESSION['date'];

    return $cleaned_parameters;
}

function deleteMeetingToUpdate($app, $MiD, $email)
{
    $database_wrapper = $app->getContainer()->get('databaseWrapper');
    $sql_queries = $app->getContainer()->get('SQLQueries');
    $DetailsModel = $app->getContainer()->get('DeleteMeetingsModel');

    $settings = $app->getContainer()->get('settings');
    $database_connection_settings = $settings['pdo_settings'];
    $DetailsModel->setSqlQueries($sql_queries);
    $DetailsModel->setDatabaseConnectionSettings($database_connection_settings);
    $DetailsModel->setDatabaseWrapper($database_wrapper);
    $DetailsModel->deleteAllMeetingUsers($app,$MiD);
    $DetailsModel->deleteMeetingData($app,$MiD, $email);

}
//
//
//
//
function makeEditM2MString(array $cleaned_parameters,$email){
    $M2MString = '';
    $M2MString .= 'Changes have been made to an accepted meeting, please accept again  |';
    $M2MString .= 'start date:';
    $M2MString .= $_SESSION['date'];
    $M2MString .= '  |';
    $M2MString .= 'start time:';
    $M2MString .= $cleaned_parameters['sanitised_time'];
    $M2MString .= '  |';
    $M2MString .= 'duration:';
    $M2MString .= $cleaned_parameters['sanitised_duration'];
    $M2MString .= '  |';
    $M2MString .= 'notes: ';
    $M2MString .= $cleaned_parameters['sanitised_notes'];
    $M2MString .= '  |';

    $M2MString .= 'host email: ';
    $M2MString .= $email;
    $M2MString .= ' ';

    $M2MString .= '';
    return $M2MString;
}

function checkTimeslotEx($app, array $cleaned_parameters, $MiD)
{


    $database_wrapper = $app->getContainer()->get('databaseWrapper');
    $sql_queries = $app->getContainer()->get('SQLQueries');
    $DetailsModel = $app->getContainer()->get('CheckTimesModel');

    $settings = $app->getContainer()->get('settings');
    $database_connection_settings = $settings['pdo_settings'];

    $DetailsModel->setSqlQueries($sql_queries);
    $DetailsModel->setDatabaseConnectionSettings($database_connection_settings);
    $DetailsModel->setDatabaseWrapper($database_wrapper);

    $result = $DetailsModel->checkTimeslotEx($app, $cleaned_parameters, $MiD);
    //var_dump($result);
    return $result;

}

function checkTimeslotUsersEx($app, array $cleaned_parameters, $meetingsToCheck, $MiD)
{
    $database_wrapper = $app->getContainer()->get('databaseWrapper');
    $sql_queries = $app->getContainer()->get('SQLQueries');
    $DetailsModel = $app->getContainer()->get('CheckTimesModel');

    $settings = $app->getContainer()->get('settings');
    $database_connection_settings = $settings['pdo_settings'];

    $DetailsModel->setSqlQueries($sql_queries);
    $DetailsModel->setDatabaseConnectionSettings($database_connection_settings);
    $DetailsModel->setDatabaseWrapper($database_wrapper);
    $meetingList = [];
    $value = $meetingsToCheck;
    //var_dump($meetingsToCheck);
    //var_dump($value);
    for($i =0; $i<=$value; $i++){
        //$meeting = $meetingsToCheck[$i];
        $result = $DetailsModel->checkTimeslotDetailsEx($app, $cleaned_parameters,$meetingsToCheck, $MiD);
    }
    //var_dump($result);
        return $result;
       // array_push($meetingList,$result." ");
}
function updateMeetingDetails($app, array $cleaned_parameters, string $email)
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

    $DetailsModel->setMeetingDetails($app, $cleaned_parameters, $email);



}
function updateMeetingRecursion($app,$cleaned_parameters)
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

    $DetailsModel->setMeetingRecursionDetails($app, $cleaned_parameters);



}



function updateMeetingUserDetails($app, array $cleaned_parameters, $meetingID)
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
    $userList = $cleaned_parameters['sanitised_user'];
    $value = count($userList)-1;
    for($i =0; $i<=$value; $i++){
        $user = $userList[$i];
        $DetailsModel->updateMeetingUserDetails($app, $cleaned_parameters, $meetingID, $user);
        $headers = 'From: MeetingOrganiser@email.com' . "\r\n" .
            'Reply-To: MeetingOrganiser@email.com' . "\r\n" .
            'X-Mailer: PHP/' . phpversion();
        mail ( $user , $cleaned_parameters['sanitised_notes'] , "New meeting at: ".
            $cleaned_parameters['sanitised_time']. " for: ". $cleaned_parameters['sanitised_duration'] . "on: ".$_SESSION['date'] .
            "host: ".$_SESSION['username'], $headers);
    }

}
function checkEmailEdit($app, $cleaned_parameters)

{

    $database_wrapper = $app->getContainer()->get('databaseWrapper');
    $sql_queries = $app->getContainer()->get('SQLQueries');
    $DetailsModel = $app->getContainer()->get('RegisterDetailsModel');

    $settings = $app->getContainer()->get('settings');
    $database_connection_settings = $settings['pdo_settings'];

    $DetailsModel->setSqlQueries($sql_queries);
    $DetailsModel->setDatabaseConnectionSettings($database_connection_settings);
    $DetailsModel->setDatabaseWrapper($database_wrapper);
    $userList = $cleaned_parameters['sanitised_user'];
    $emailChecks = [];
    $value = count($userList)-1;
    for($i =0; $i<=$value; $i++){
        $user = $userList[$i];
        //var_dump($user);
        $result = $DetailsModel->checkEmail($app, $user);
        //var_dump($result);
        array_push($emailChecks,$result." ");
        //var_dump(getNumberbyUser($app,$user));
    }
    //var_dump($emailChecks);
    if(in_array(" ",$emailChecks)){
        $result = false;
    }
    else{
        $result = true;
    }
    return $result;

}


function getRecurringMeetingHostEx($app,$email, $MiD)
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
    $value = $DetailsModel->getRecurringMeetingsHostEx($app, $email, $MiD);


    $recurringMeeting = [];
    if($value<0){
        return "no  meetings";
    }else{
        for($i =0; $i<=$value ; $i++){
            $idstring = $DetailsModel->getRecurringMeetingsHostDetailsEx($app, $email, $i, $MiD);
            array_push($recurringMeeting,$idstring);

        }
        array_pop($recurringMeeting);
        return $recurringMeeting;
    }
}
function getRecurringMeetingParticipantEx($app, $email, $MiD)
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
    $value = $DetailsModel->getIdForRecurringMeetingEx($app, $email, $MiD);
   // var_dump($value);

    $recurringMeeting = [];
    $recurringMeetingId = [];
    if($value<0){
        return "no  meetings";
    }
    else{
        for($i =0; $i<=$value ; $i++){
            $idstring = $DetailsModel->getIdForRecurringMeetingDetailsEx($app, $email, $i, $MiD);
            array_push($recurringMeetingId,$idstring[0]);

        }
        array_pop($recurringMeetingId);
        return $recurringMeetingId;
    }
}



