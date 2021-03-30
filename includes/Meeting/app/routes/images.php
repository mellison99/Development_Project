<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->post('/images', function(Request $request, Response $response) use ($app)
{
//var_dump($_SESSION['date']);

    if (isset($_POST['name'])=== true && empty($_POST['name'])=== false) {

        $meetings = getMeetingbyDateUser($app,$_POST['name'],$_SESSION['date']);
        $yearInString = (substr($_SESSION['date'],0,4));
        $monthInString=(substr($_SESSION['date'],5,2));
        $dayInString = (substr($_SESSION['date'],8,2));
        $monthInInt = (int)$monthInString;
        $dayInInt = (int)$dayInString;
        $starttime = mktime(0,0,0,$monthInInt,$dayInInt,$yearInString);
        $endtime = getdate($starttime)[0]+(60*30);
        //var_dump(getdate($starttime));
        $weekdayVal = getdate($starttime)['wday']+1;
        $dateVal = getdate($starttime)['mday'];
        $monthVal = getdate($starttime)['mon'];
        $arrayOfHostedRepeatMeetings = getRecurringMeetingHost($app,$_POST['name']);
        $meetingIDtoSearch = getRecurringMeetingParticipant($app,$_POST['name']);
        $recurringmeetingDetails = getRecurringMeetingParticipantDetails($app, $meetingIDtoSearch);
        $meetingIDtoSearch = getRecurringMeetingParticipant($app,$_POST['name']);
        $recurringmeetingDetails2 = getRecurringMeetingParticipantDetails($app, $meetingIDtoSearch)[0];
        $recurringWhereHost = sortRecurringMeetings($arrayOfHostedRepeatMeetings,$starttime);
        $recurringWhereParticipant = sortRecurringMeetings($recurringmeetingDetails2,$starttime);
        $eventsOnDay = getEventbyDayUser($app,$_POST['name'],$weekdayVal-1);
        $eventsInMonth =getEventbyMonthUser($app,$_POST['name'],$monthVal);
        $eventsOnDate =getEventbyDateUser($app,$_POST['name'],$dateVal);

        foreach ($meetings as $meeting){
            echo " meeting at " .$meeting[0]." for ". $meeting[2];
        }
        foreach ($recurringWhereHost as $meeting){
            echo " meeting at " .$meeting[0]." for ". $meeting[1];
        }
        foreach ($recurringWhereParticipant as $meeting){
            echo " meeting at " .$meeting[0]." for ". $meeting[1];
        }

        foreach ($eventsOnDay as $meeting){
            echo " event at ". $meeting[0]." for ". $meeting[1] ;
        }

        foreach ($eventsInMonth as $meeting){
            echo " event at ". $meeting[0]." for ". $meeting[1] ;
        }
        foreach ($eventsOnDate as $meeting){
            echo " event at ". $meeting[0]." for ". $meeting[1]  ;

        }

       // echo $_POST['name'] . " is here";
    }
});
function RetrieveUserData($app, $email,$date)
{
//    var_dump($date);
    $store_data_result = null;

    $database_wrapper = $app->getContainer()->get('databaseWrapper');
    $sql_queries = $app->getContainer()->get('SQLQueries');
    $DetailsModel = $app->getContainer()->get('RegisterDetailsModel');

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

            array_push($downloadMessages,$idstring);
        }
        array_pop($downloadMessages);

        return $downloadMessages;
    }
}

function upcomingMeetings ($app,$email,$date){

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

            array_push($downloadMessages,$idstring);
        }
        array_pop($downloadMessages);

        return $downloadMessages;
    }
}










