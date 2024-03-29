<?php
/**
 * createmeeting.php
 *
 * Author: Matthew
 * Date: 17/01/2021
 *
 * @author Matthew
 */

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->post(
    '/createmeeting',
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
        $error = "";
        $tainted_parameters = $request->getParsedBody();
       // var_dump($tainted_parameters);




        $cleaned_parameters = cleanupParameters1($app, $tainted_parameters);
//        var_dump($cleaned_parameters);
        $StartVal=getdate($cleaned_parameters['sanitised_start'])['hours'].getdate($cleaned_parameters['sanitised_start'])['minutes'];
        $EndVal=getdate($cleaned_parameters['sanitised_end'])['hours'].getdate($cleaned_parameters['sanitised_end'])['minutes'];
//        var_dump($StartVal);
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
        $arrayOfHostedRepeatMeetings = getRecurringMeetingHost2($app,$_SESSION['username']);
        $meetingIDtoSearch = getRecurringMeetingParticipant2($app,$_SESSION['username']);
        $recurringmeetingDetails = getRecurringMeetingParticipantDetails2($app, $meetingIDtoSearch);
        $recurringmeetingDetails = $recurringmeetingDetails[0];
//        var_dump($StartVal);
        if($eventCountInTimeslot != NULL ){
            $html_output =  $this->view->render($response,
                'created_meeting.html.twig');
            $date = $_SESSION['date'];
            return $html_output->withHeader('Location', LANDING_PAGE . "/createmeetingform?date=".$date);
        }
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
        for ($i =0; $i<sizeOf($usersToCheck); $i++){
            $eventsToCheck = getEventDetails($app, $usersToCheck[$i]);
            //var_dump($eventsToCheck);
            $eventCountInTimeslot = checkEventTimeslots($eventsToCheck,$newMeetingArray );
            //var_dump($eventCountInTimeslot);
            if($eventCountInTimeslot != NULL ){
                $html_output =  $this->view->render($response,
                    'created_meeting.html.twig');
                $date = $_SESSION['date'];
                $_SESSION['error'] = "Event organised at this time";
                return $html_output->withHeader('Location', LANDING_PAGE . "/createmeetingform?date=".$date);
            }

            if(checkRecurringTimeslots($arrayOfHostedRepeatMeetings,$newMeetingArray )>0
                || checkRecurringTimeslots($recurringmeetingDetails,$newMeetingArray )>0){
                $html_output =  $this->view->render($response,
                    'created_meeting.html.twig');
                $date = $_SESSION['date'];
                return $html_output->withHeader('Location', LANDING_PAGE . "/createmeetingform?date=".$date);
            }
        }




        if(checkRecurringTimeslots($arrayOfHostedRepeatMeetings,$newMeetingArray )>0
            || checkRecurringTimeslots($recurringmeetingDetails,$newMeetingArray )>0){
            $html_output =  $this->view->render($response,
                            'created_meeting.html.twig');
                        $date = $_SESSION['date'];
                        return $html_output->withHeader('Location', LANDING_PAGE . "/createmeetingform?date=".$date);
        }
        for ($i =0; $i<sizeOf($usersToCheck)-1; $i++){
            $arrayOfHostedRepeatMeetings = getRecurringMeetingHost2($app,$usersToCheck[$i]);
            $meetingIDtoSearch = getRecurringMeetingParticipant2($app,$usersToCheck[$i]);
            $recurringmeetingDetails = getRecurringMeetingParticipantDetails2($app, $meetingIDtoSearch);
            $recurringmeetingDetails = $recurringmeetingDetails[0];

            if(checkRecurringTimeslots($arrayOfHostedRepeatMeetings,$newMeetingArray )>0
                || checkRecurringTimeslots($recurringmeetingDetails,$newMeetingArray )>0){
                $html_output =  $this->view->render($response,
                    'created_meeting.html.twig');
                $date = $_SESSION['date'];
                return $html_output->withHeader('Location', LANDING_PAGE . "/createmeetingform?date=".$date);
            }
        }

        if($_POST['time'] == '' || $_POST['duration'] == '' || $_POST['name'][0] == '' )
        {

            $error = 'please fill in all details to register';
            $_SESSION['error'] = $error;
            $html_output =  $this->view->render($response,
                'created_meeting.html.twig');
            $date = $_SESSION['date'];
            return $html_output->withHeader('Location', LANDING_PAGE . "/createmeetingform?date=".$date);

        }





        $message_content = makeM2MString($cleaned_parameters,$email);
        $numbersToMessage = getNumberbyUser($app,$cleaned_parameters);
        if(checkEmail($app, $cleaned_parameters)==false){
            $error = 'one or more emails entered do not exist';
            $_SESSION['error'] = $error;
            $html_output =  $this->view->render($response,
                'created_meeting.html.twig');
            $date = $_SESSION['date'];
            return $html_output->withHeader('Location', LANDING_PAGE . "/createmeetingform?date=".$date);
        }
        $meetingsToCheck = checkTimeslot($app,$cleaned_parameters);
       // var_dump($meetingsToCheck);
        for($i =0; $i<$meetingsToCheck; $i++){
            checkTimeslotUsers($app,$cleaned_parameters,$meetingsToCheck[$i]);
            $usersAtTimeslotArray = checkTimeslotUsers($app,$cleaned_parameters,$meetingsToCheck[$i]);
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
           if(checkTimeslot($app,$cleaned_parameters)=="0" and $meetingsattending <= 0) {



            $meetingID = 2;
            storeMeetingDetails($app, $cleaned_parameters, $email);
            storeMeetingUserDetails($app,$cleaned_parameters, $meetingID);
            storeMeetingRecursion($app,$cleaned_parameters);
            $success = true;
            $amountOfNumbers = count($numbersToMessage);
            //var_dump($amountOfNumbers);
            //var_dump($numbersToMessage[0]);
            for($i =0; $i<$amountOfNumbers; $i++){
                // var_dump($numbersToMessage[$i]);
                sendM2MMessage($app, $message_content, $numbersToMessage[$i]);
            }
    }
        else{
            $error = 'Timeslot already booked ' .$meetingsToCheck;
            $_SESSION['error'] = $error;
            $html_output =  $this->view->render($response,
                'created_meeting.html.twig');
            $date = $_SESSION['date'];
            return $html_output->withHeader('Location', LANDING_PAGE . "/createmeetingform?date=".$date);
        }

        if($success == true){


                $name = $cleaned_parameters['sanitised_Id'] .$_FILES['file']['name'];
                $target_dir = "meetingdocs/";
                $target_file = $target_dir . basename($_FILES["file"]["name"]);

                // Select file type
                $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

                // Valid file extensions
                $extensions_arr = array("txt", "docx", "doc", "pdf");

                // Check extension
                if (in_array($imageFileType, $extensions_arr)) {
                    // Insert record
                    storeMeetingDoc($app, $name, $cleaned_parameters);
//

                    // Upload file
                    move_uploaded_file($_FILES['file']['tmp_name'], $target_dir . $name);

                    $html_output =  $this->view->render($response,
                        'upcomingmeetings.html.twig');
                    $date = $_SESSION['date'];
                    return $html_output->withHeader('Location', LANDING_PAGE . "/upcomingmeetings");
            }

        }

        //getSimbyEmail($app, $email);
        $html_output =  $this->view->render($response,
            'created_meeting.html.twig',
            [
                'landing_page' => LANDING_PAGE . '/calendar',
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
    })->setName('createmeeting');

$_SESSION['error'] ="";
function cleanupParameters1($app, $tainted_parameters)
{
    $yearInString = (substr($_SESSION['date'],0,4));
    $monthInString=(substr($_SESSION['date'],5,2));
    $dayInString = (substr($_SESSION['date'],8,2));
    $monthInInt = (int)$monthInString;
    $dayInInt = (int)$dayInString;
    $cleaned_parameters = [];
    $validator = $app->getContainer()->get('validator');
    $tainted_time = $tainted_parameters['time'];
    $tainted_duration = $tainted_parameters['duration'];
    $tainted_notes = $tainted_parameters['notes'];
    $tainted_subject = $tainted_parameters['subject'];
    $tainted_repeat = $tainted_parameters['repeat'];
    $tainted_users = $tainted_parameters['name'];
    $cleaned_parameters['sanitised_time'] = $validator->sanitiseString($tainted_time);
    $cleaned_parameters['sanitised_duration'] = $validator->sanitiseString($tainted_duration);
    $cleaned_parameters['sanitised_Id'] = time();
    $cleaned_parameters['sanitised_notes'] = $validator->sanitiseString($tainted_notes);
    $cleaned_parameters['sanitised_subject'] = $validator->sanitiseString($tainted_subject);
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




function makeM2MString(array $cleaned_parameters,$email){
    $M2MString = '';
    $M2MString .= '';
    $M2MString .= 'start date:';
    $M2MString .= $_SESSION['date'];
    $M2MString .= '  |';
    $M2MString .= 'start time:';
    $M2MString .= $cleaned_parameters['sanitised_time'];
    $M2MString .= '  |';
    $M2MString .= 'duration:';
    $M2MString .= $cleaned_parameters['sanitised_duration'];
    $M2MString .= '  |';
    $M2MString .= 'Subject: ';
    $M2MString .= $cleaned_parameters['sanitised_subject'];
    $M2MString .= '  |';

    $M2MString .= 'host email: ';
    $M2MString .= $email;
    $M2MString .= ' ';

    $M2MString .= '';
    return $M2MString;
}
function sendM2MMessage($app, $message_content, $numberToSend){
    //var_dump("+".$numberToSend[0]);
    $SoapWrapper = $app->getContainer()->get('SoapWrapper');
    $soap_client_handle = $SoapWrapper->createSoapClient();
    $soap_client_handle->sendMessage('20_2414628', 'PublicPassword12',"+".$numberToSend[0],$message_content,false,"SMS");
}
function checkTimeslot($app, array $cleaned_parameters)
{


    $database_wrapper = $app->getContainer()->get('databaseWrapper');
    $sql_queries = $app->getContainer()->get('SQLQueries');
    $DetailsModel = $app->getContainer()->get('CheckTimesModel');

    $settings = $app->getContainer()->get('settings');
    $database_connection_settings = $settings['pdo_settings'];

    $DetailsModel->setSqlQueries($sql_queries);
    $DetailsModel->setDatabaseConnectionSettings($database_connection_settings);
    $DetailsModel->setDatabaseWrapper($database_wrapper);

    $result = $DetailsModel->checkTimeslot($app, $cleaned_parameters);
    //var_dump($result);
    return $result;

}

function checkTimeslotUsers($app, array $cleaned_parameters, $meetingsToCheck)
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
       // var_dump($DetailsModel->checkTimeslotDetails($app, $cleaned_parameters,$meeting));
        $result = $DetailsModel->checkTimeslotDetails($app, $cleaned_parameters,$meetingsToCheck);
    }
    //var_dump($result);
        return $result;
       // array_push($meetingList,$result." ");
}
function storeMeetingDetails($app, array $cleaned_parameters, string $email)
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
function storeMeetingDoc($app, $name, $cleaned_parameters)
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

    $DetailsModel->storeMeetingDoc($app, $name, $cleaned_parameters);



}

function storeMeetingRecursion($app,$cleaned_parameters)
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



function storeMeetingUserDetails($app, array $cleaned_parameters, $meetingID)
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
        $DetailsModel->setMeetingUserDetails($app, $cleaned_parameters, $meetingID, $user);
        $headers = 'From: MeetingOrganiser@email.com' . "\r\n" .
            'Reply-To: MeetingOrganiser@email.com' . "\r\n" .
            'X-Mailer: PHP/' . phpversion();
        mail ( $user , $cleaned_parameters['sanitised_notes'] , "New meeting at: ".
            $cleaned_parameters['sanitised_time']. " for: ". $cleaned_parameters['sanitised_duration'] . "on: ".$_SESSION['date'] .
            "host: ".$_SESSION['username'], $headers);
    }

}
function checkEmail($app, $cleaned_parameters)

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


function getNumberbyUser($app,$cleaned_parameters)
{
    $store_data_result = null;

    $database_wrapper = $app->getContainer()->get('databaseWrapper');
    $sql_queries = $app->getContainer()->get('SQLQueries');
    $DetailsModel = $app->getContainer()->get('RetrieveUserModel');

    $settings = $app->getContainer()->get('settings');
    $database_connection_settings = $settings['pdo_settings'];

    $DetailsModel->setSqlQueries($sql_queries);
    $DetailsModel->setDatabaseConnectionSettings($database_connection_settings);
    $DetailsModel->setDatabaseWrapper($database_wrapper);
    $value = count($cleaned_parameters['sanitised_user']);
    $email = $cleaned_parameters['sanitised_user'];
    //var_dump($value);
    $numbersToMessage = [];
    if($value<0){
        return "no numbers to message";
    }else{
        for($i =0; $i<=$value ; $i++){
            $idstring = $DetailsModel->getNumberbyUser($app, $email, $i);
            //var_dump($idstring);
            array_push($numbersToMessage,$idstring);
        }
        array_pop($numbersToMessage);
        //var_dump( $numbersToMessage);
    }
    return $numbersToMessage;
}
function getEventDetails($app,$email)
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

function getRecurringMeetingHost2($app,$email)
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
function getRecurringMeetingParticipant2($app, $email)
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
function getRecurringMeetingParticipantDetails2($app, $meetingIDtoSearch){
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

function checkRecurringTimeslots($arrayOfHostedRepeatMeetings,$newMeetingArray ){
    $meetingsAtTime = 0;
    $StartVal =$newMeetingArray[0];
    $EndVal = $newMeetingArray[1];
    $weekdayVal = $newMeetingArray[2];
    $yearVal = $newMeetingArray[3];
    $dateVal = $newMeetingArray[4];
    $monthVal = $newMeetingArray[5];
    for($i =0; $i<sizeof($arrayOfHostedRepeatMeetings); $i++){

        if ($arrayOfHostedRepeatMeetings[$i][3] != "never"){
            $duration = (int) $arrayOfHostedRepeatMeetings[$i][2]*60;
            $dateInfo = getdate($arrayOfHostedRepeatMeetings[$i][1]);
            $dateInfo2 = getdate($arrayOfHostedRepeatMeetings[$i][1]+$duration);
            $storedStart = str_pad($dateInfo['hours']. $dateInfo['minutes'],4,"0",STR_PAD_RIGHT);
            $storedEnd = str_pad($dateInfo2['hours']. $dateInfo2['minutes'],4,"0",STR_PAD_RIGHT);
//                var_dump((int)$storedStart);
//                var_dump((int)$storedEnd);

            if ($arrayOfHostedRepeatMeetings[$i][3] == "weekly" && $dateInfo['weekday'] == $weekdayVal && $StartVal >=$storedStart && $EndVal <=$storedEnd)
            {
                $meetingsAtTime=$meetingsAtTime+1;
                return $meetingsAtTime;
            }

            if ($arrayOfHostedRepeatMeetings[$i][3] == "monthly" && $dateInfo['mday'] == $dateVal && $StartVal >=$storedStart && $EndVal <=$storedEnd)
            {
                $meetingsAtTime=$meetingsAtTime+1;
                return $meetingsAtTime;
            }
            if ($arrayOfHostedRepeatMeetings[$i][3] == "annually" && $dateInfo['yday'] == $yearVal && $StartVal >=$storedStart && $EndVal <=$storedEnd)
            {
                $meetingsAtTime=$meetingsAtTime+1;
                return $meetingsAtTime;
            }

        }



    }
}

function checkEventTimeslots($eventsToCheck,$newMeetingArray ){
    $eventsAtTime = 0;
    $StartVal =(int)$newMeetingArray[0];
    $EndVal = (int)$newMeetingArray[1];
    $weekdayNumVal = $newMeetingArray[7];
    $weekdayNumVal = (int) $weekdayNumVal;
    $dateVal = $newMeetingArray[4];
    $monthNumVal = $newMeetingArray[6];
    $timesToLoop = sizeof($eventsToCheck);

    for($i =0; $i<sizeof($eventsToCheck); $i++){
        $duration = (int) $eventsToCheck[$i][1];
        if($duration >=60){
            $duration =(100 * (($duration/60))) + ($duration % 60);
        }
        $storedStart =  $eventsToCheck[$i][0][0].$eventsToCheck[$i][0][1].$eventsToCheck[$i][0][3].$eventsToCheck[$i][0][4];
        $storedStart = (int) $storedStart;
        $storedEnd = $storedStart+$duration;

            if ($eventsToCheck[$i][3] == $dateVal && $StartVal >=$storedStart  && $EndVal <=$storedEnd)
            {
                $eventsAtTime=$eventsAtTime+1;
                return $eventsAtTime;
            }

            if ($eventsToCheck[$i][4] ==  $weekdayNumVal && $StartVal >=$storedStart && $EndVal <=$storedEnd)
            {
                $eventsAtTime=$eventsAtTime+1;
                return $eventsAtTime;
            }

            if ((int)$eventsToCheck[$i][5] ==  $monthNumVal-1 && $StartVal >=$storedStart && $EndVal <=$storedEnd)
            {
                $eventsAtTime=$eventsAtTime+1;
                return $eventsAtTime;
            }

    }

}


