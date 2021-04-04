<?php
/**
 * meetingack.php
 *
 * Author: Matthew
 * Date: 17/01/2021
 *
 * @author Matthew
 */

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->get('/meetingack', function(Request $request, Response $response) use ($app)
{
    if(!isset($_SESSION['username']))
    {
        $error = "please login";
        $_SESSION['error'] = $error;
        $html_output =  $this->view->render($response,
            'homepageform.html.twig');
        return $html_output->withHeader('Location', LANDING_PAGE);
    }

    $email = $_SESSION['username'];
    $tester = RetrieveNumUnacknowledgedmeetings($app, $email);




    $html_output = $this->view->render($response,
    'meetingack.html.twig',
    [
      'css_path' => CSS_PATH,
      'landing_page' => LANDING_PAGE . '/calendar',
      'method' => 'post',
      'method2' => 'post',
      'meeting_requests' => LANDING_PAGE . '/meetingack',
      'upcoming_meetings'=>LANDING_PAGE . '/upcomingmeetings',
      'hosted_meetings'=>LANDING_PAGE . '/meetingshosted',
      'edit_profile'=> LANDING_PAGE . '/profilemanagement',
      'create_event'=> LANDING_PAGE . '/events',
      'view_event'=> LANDING_PAGE . '/eventView',
      'action' => 'meetingackpost',
      'initial_input_box_value' => null,
      'page_title' => APP_NAME,
      'page_heading_1' => APP_NAME,
      'page_heading_2' => 'Choose a message to download/ View in detail',
      'country_names' => $tester,
      'country_names2'=>"placeholder",
      'page_text' => 'You have '.count($tester).' unacknowledged meeting(s)',
      'error'=> $_SESSION['error'],
    ]);

    $processed_output = processOutput($app, $html_output);

    return $processed_output;

})->setName('homepage');

function RetrieveNumUnacknowledgedmeetings($app, $email)
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
    $time = time();

    $value = $DetailsModel->getUnacceptedMeeting($app, $email);
    //var_dump($value);
    $downloadMessages = [];
    if($value<0){
        return "no  messages";
    }else{
        for($i =1; $i<=$value ; $i++){
            $idstring = $DetailsModel->getUnacceptedMeetingDetails($app, $email ,$i);;
            //var_dump($idstring);
            array_push($downloadMessages,$idstring);
        }
        //array_pop($downloadMessages);
       // var_dump($downloadMessages);
        return $downloadMessages;}
}




