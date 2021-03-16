<?php
/**
 * sendmessage.php
 *
 * Author: Matthew
 * Date: 17/01/2021
 *
 * @author Matthew
 */

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->post(
    '/sendmessage',
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
        try
        {
            //$taintedDigit = (int)$tainted_parameters['lastNumber'];
            //$taintedTemp = (int)$tainted_parameters['curTemp'];
        }
        catch(exception $e)
        {
            $e = 'error';
            return $e;
        }
      //  var_dump($tainted_parameters['name']);
        $nameArray = $tainted_parameters['name'];
        //var_dump(count($nameArray));
       // var_dump($_SESSION['date']);
        $yearInString = (substr($_SESSION['date'],0,4));
        $monthInString=(substr($_SESSION['date'],5,2));
        $dayInString = (substr($_SESSION['date'],8,2));
        $monthInInt = (int)$monthInString;
        $dayInInt = (int)$dayInString;
        $cleaned_parameters = cleanupParameters1($app, $tainted_parameters);
        if($_POST['time'] == '' || $_POST['duration'] == '' || $_POST['name'][0] == '' )
        {
            $error = 'please fill in all details to register';
            $_SESSION['error'] = $error;
            $html_output =  $this->view->render($response,
                'sent_message.html.twig');
            $date = $_SESSION['date'];
            return $html_output->withHeader('Location', LANDING_PAGE . "/sendmessagelandingpage?date=".$date);

        }




        $message_content = makeM2MString($cleaned_parameters,$email);
        $numbersToMessage = getNumberbyUser($app,$cleaned_parameters);
        if(checkEmail($app, $cleaned_parameters)==false){
            $error = 'one or more emails entered do not exist';
            $_SESSION['error'] = $error;
            $html_output =  $this->view->render($response,
                'sent_message.html.twig');
            $date = $_SESSION['date'];
            return $html_output->withHeader('Location', LANDING_PAGE . "/sendmessagelandingpage?date=".$date);
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
                //var_dump(sizeof($cleaned_parameters['sanitised_user']));
               // var_dump($usersAtTimeslotArray[$i]);
                $arrayToSearch = $usersAtTimeslotArray[$i];
                //var_dump($arrayToSearch);
                //var_dump($cleaned_parameters['sanitised_user'][$j]);
                $meetingsattending = $meetingsattending + array_search( $cleaned_parameters['sanitised_user'][$j],$arrayToSearch);
                $meetingsattending = $meetingsattending + array_search($email, $usersAtTimeslotArray[$i]);
            }

        }
        var_dump($meetingsattending);
           if(checkTimeslot($app,$cleaned_parameters)=="0" and $meetingsattending <= 0) {



            $meetingID = 2;
            storeMeetingDetails($app, $cleaned_parameters, $email);
            storeMeetingUserDetails($app,$cleaned_parameters, $meetingID);
            storeMeetingRecursion($app,$cleaned_parameters);
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
                'sent_message.html.twig');
            $date = $_SESSION['date'];
            return $html_output->withHeader('Location', LANDING_PAGE . "/sendmessagelandingpage?date=".$date);
        }

        //getSimbyEmail($app, $email);
        $html_output =  $this->view->render($response,
            'sent_message.html.twig',
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
    })->setName('sendmessage');
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
    $tainted_repeat = $tainted_parameters['repeat'];
    $tainted_users = $tainted_parameters['name'];
    $cleaned_parameters['sanitised_time'] = $validator->sanitiseString($tainted_time);
    $cleaned_parameters['sanitised_duration'] = $validator->sanitiseString($tainted_duration);
    $cleaned_parameters['sanitised_Id'] = time();
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
    $M2MString .= 'notes: ';
    $M2MString .= $cleaned_parameters['sanitised_notes'];
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
    $DetailsModel = $app->getContainer()->get('RegisterDetailsModel');

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
    $DetailsModel = $app->getContainer()->get('RegisterDetailsModel');

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
function checkTimeslotList($app, array $listofUserMeetings){

}


function storeMeetingDetails($app, array $cleaned_parameters, string $email)
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

    $DetailsModel->setMeetingDetails($app, $cleaned_parameters, $email);



}
function storeMeetingRecursion($app,$cleaned_parameters)
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

    $DetailsModel->setMeetingRecursionDetails($app, $cleaned_parameters);



}



function storeMeetingUserDetails($app, array $cleaned_parameters, $meetingID)
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
    $userList = $cleaned_parameters['sanitised_user'];
    $value = count($userList)-1;
    for($i =0; $i<=$value; $i++){
        $user = $userList[$i];
        $DetailsModel->setMeetingUserDetails($app, $cleaned_parameters, $meetingID, $user);
        //var_dump(getNumberbyUser($app,$user));
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
    $DetailsModel = $app->getContainer()->get('RegisterDetailsModel');

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


