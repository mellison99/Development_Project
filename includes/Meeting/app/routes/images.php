<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->post('/images', function(Request $request, Response $response) use ($app)
{
    $_SESSION['date'] = $_GET["date"];
    if (isset($_POST['name'])=== true && empty($_POST['name'])=== false) {

        $meetings = RetrieveUserData($app, $_POST['name'],1617031805);
        var_dump($_POST['time']);
        $futureMeetings = upcomingMeetings ($app,$_POST['name'],1617031805);
        var_dump($futureMeetings);
        foreach ($meetings as $meeting){
            echo  $meeting[0]." ". $meeting[1];
            echo '                ';
        }

        foreach ($futureMeetings as $meeting){
            echo "meeting at ";
            echo  $meeting[0]." ". $meeting[1];
            echo '                ';
        }
        echo $_POST['name'] . " is here";
    }
});
function RetrieveUserData($app, $email,$date)
{
    var_dump($date);
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










