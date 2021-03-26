<?php
/**
 * profilemanagement.php
 *
 * Author: Matthew
 * Date: 26/03/2021
 *
 * @author Matthew
 */

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;


$app->get('/profilemanagement', function(Request $request, Response $response) use ($app)
{
    if(!isset($_SESSION['username']))
    {
        $error = "please login";
        $_SESSION['error'] = $error;
        $html_output =  $this->view->render($response,
            'homepageform.html.twig');
        return $html_output->withHeader('Location', LANDING_PAGE);
    }
$userDetails = getUserInfo($app, $_SESSION['username']);

$html_output = $this->view->render($response,
'profilemanagement.html.twig',
[
'css_path' => CSS_PATH,
'landing_page' => LANDING_PAGE . '/calendar',
'meeting_requests' => LANDING_PAGE . '/downloadedmessageselect',
'upcoming_meetings'=>LANDING_PAGE . '/upcomingmeetings',
'hosted_meetings'=>LANDING_PAGE . '/meetingshosted',
'edit_profile'=> LANDING_PAGE . '/profilemanagement',
'create_event'=> LANDING_PAGE . '/events',
'view_event'=> LANDING_PAGE . '/eventView',
'method' => 'post',
'action' => 'updateuser',
'initial_input_box_value' => null,
'page_title' => APP_NAME,
'page_heading_1' => APP_NAME,
'page_heading_2' => 'update',
'update' => LANDING_PAGE . '/updateuser',
'error' => $_SESSION['error2'],
'userdetails'=>$userDetails,
]);

processOutput($app, $html_output);

return $html_output;

})->setName('registeruser');

function getUserInfo($app, $email){
    $userDetails = [];
    $database_wrapper = $app->getContainer()->get('databaseWrapper');
    $sql_queries = $app->getContainer()->get('SQLQueries');
    $DetailsModel = $app->getContainer()->get('RegisterDetailsModel');

    $settings = $app->getContainer()->get('settings');
    $database_connection_settings = $settings['pdo_settings'];

    $DetailsModel->setSqlQueries($sql_queries);
    $DetailsModel->setDatabaseConnectionSettings($database_connection_settings);
    $DetailsModel->setDatabaseWrapper($database_wrapper);

    $userDetails = $DetailsModel->getUserInfo($app, $email);
    return $userDetails;
}

function updateUserDetails($app, array $cleaned_parameters)
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

    $DetailsModel->updateRegisterDetails($app, $cleaned_parameters);
}
