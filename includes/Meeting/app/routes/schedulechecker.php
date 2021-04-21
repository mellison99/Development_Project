<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->post('/schedulechecker', function(Request $request, Response $response) use ($app)
{

    if (isset($_POST['name'])=== true && empty($_POST['name'])=== false) {
//        var_dump($_POST['name']);
        $meetings = getMeetingbyDateUser($app,$_POST['name'],$_SESSION['date']);
        $yearInString = (substr($_SESSION['date'],0,4));
        $monthInString=(substr($_SESSION['date'],5,2));
        $dayInString = (substr($_SESSION['date'],8,2));
        $monthInInt = (int)$monthInString;
        $dayInInt = (int)$dayInString;
        $starttime = mktime(0,0,0,$monthInInt,$dayInInt,$yearInString);
//        var_dump($starttime);
        $endtime = getdate($starttime)[0]+(60*30);
        //var_dump(getdate($starttime));
        $weekdayVal = getdate($starttime)['wday']+1;
        $dateVal = getdate($starttime)['mday'];
        $monthVal = getdate($starttime)['mon'];
//        var_dump($monthVal);
        $arrayOfHostedRepeatMeetings = getRecurringMeetingHost($app,$_POST['name']);
//        var_dump($arrayOfHostedRepeatMeetings);
        $meetingIDtoSearch = getRecurringMeetingParticipant($app,$_POST['name']);
//        var_dump($meetingIDtoSearch);
        $recurringmeetingDetails = getRecurringMeetingParticipantDetails($app, $meetingIDtoSearch);
//        var_dump($recurringmeetingDetails);
        $recurringmeetingDetails2 = getRecurringMeetingParticipantDetails($app, $meetingIDtoSearch)[0];
        $recurringWhereHost = sortRecurringMeetings($arrayOfHostedRepeatMeetings,$starttime);
//        var_dump($arrayOfHostedRepeatMeetings);
        $recurringWhereParticipant = sortRecurringMeetings($recurringmeetingDetails2,$starttime);
        $eventsOnDay = getEventbyDayUser($app,$_POST['name'],$weekdayVal-1);
        $eventsInMonth =getEventbyMonthUser($app,$_POST['name'],$monthVal);
        $eventsOnDate =getEventbyDateUser($app,$_POST['name'],$dateVal);
        $meetingString ='';
        foreach ($meetings as $meeting){
            $meetingString = " meeting at " .$meeting[0]." for ". $meeting[2];
            echo $meetingString;

        }
        foreach ($recurringWhereHost as $meeting){
            $meetingString = " meeting at " .$meeting;
            echo $meetingString;

        }
        foreach ($recurringWhereParticipant as $meeting){
            $meetingString = " meeting at " .$meeting;
            echo $meetingString;

        }

        foreach ($eventsOnDay as $meeting){
            $meetingString = " event at ". $meeting[0]." for ". $meeting[1];
            echo $meetingString;

        }

        foreach ($eventsInMonth as $meeting){
            $meetingString = " event at ". $meeting[0]." for ". $meeting[1];
            echo $meetingString;

        }
        foreach ($eventsOnDate as $meeting){
            $meetingString = " event at ". $meeting[0]." for ". $meeting[1];
            echo $meetingString;


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

            array_push($downloadMessages,$idstring);
        }
        array_pop($downloadMessages);

        return $downloadMessages;
    }
}










