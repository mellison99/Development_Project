<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;


$app->get('/meetingnotes', function(Request $request, Response $response) use ($app)
{
    if($_SESSION['error'] != '')
    {
        $error = $_SESSION['error2'];
    }
    else
    {
        $error = '';
    }
    if(!isset($_SESSION['username']))
        {
        $error = "please login";
        $_SESSION['error2'] = $error;
        $html_output =  $this->view->render($response,
        'homepageform.html.twig');
        return $html_output->withHeader('Location', LANDING_PAGE);
        }
    $tainted_MiD = $_GET["MiD"];

    $cleaned_MiD = cleanupParameters($app, $tainted_MiD);
   $notes = getMeetingNotes($app, $cleaned_MiD);
   $fileLink = getMeetingFiles($app, $cleaned_MiD);

   $fileLink = "../meetingdocs/".$fileLink[0];
$html_output = $this->view->render($response,
'meetingnotes.html.twig',
[
'css_path' => CSS_PATH,
'landing_page' => LANDING_PAGE . '/calendar',
'meeting_requests' => LANDING_PAGE . '/meetingack',
'upcoming_meetings'=>LANDING_PAGE . '/upcomingmeetings',
'hosted_meetings'=>LANDING_PAGE . '/meetingshosted',
'edit_profile'=> LANDING_PAGE . '/profilemanagement',
'create_event'=> LANDING_PAGE . '/events',
'view_event'=> LANDING_PAGE . '/eventView',
'method' => 'post',
'action' => 'registeruser',
'initial_input_box_value' => null,
'page_title' => APP_NAME,
'page_heading_1' => APP_NAME,
'page_heading_2' => 'Notes',
'update' => LANDING_PAGE . '/updateuser',
'error' => $error,
'notes' => $notes,
'meetingDocs' => $fileLink,
]);

processOutput($app, $html_output);

return $html_output;

})->setName('meetingnotes');


function getMeetingNotes($app, $MiD){
$database_wrapper = $app->getContainer()->get('databaseWrapper');
$sql_queries = $app->getContainer()->get('SQLQueries');
$DetailsModel = $app->getContainer()->get('RetrieveMeetingModel');

$settings = $app->getContainer()->get('settings');
$database_connection_settings = $settings['pdo_settings'];

$DetailsModel->setSqlQueries($sql_queries);
$DetailsModel->setDatabaseConnectionSettings($database_connection_settings);
$DetailsModel->setDatabaseWrapper($database_wrapper);
return $DetailsModel->getMeetingNotes($app, $MiD);

}

function getMeetingFiles($app, $MiD){
    $database_wrapper = $app->getContainer()->get('databaseWrapper');
    $sql_queries = $app->getContainer()->get('SQLQueries');
    $DetailsModel = $app->getContainer()->get('RetrieveMeetingModel');

    $settings = $app->getContainer()->get('settings');
    $database_connection_settings = $settings['pdo_settings'];

    $DetailsModel->setSqlQueries($sql_queries);
    $DetailsModel->setDatabaseConnectionSettings($database_connection_settings);
    $DetailsModel->setDatabaseWrapper($database_wrapper);
    return $DetailsModel->fetchMeetingDoc($app, $MiD);

}


