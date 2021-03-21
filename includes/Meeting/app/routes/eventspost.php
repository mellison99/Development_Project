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
        var_dump($tainted_parameters['day']);

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
storeEventDetails($app, $cleaned_parameters);

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
    $DetailsModel = $app->getContainer()->get('RegisterDetailsModel');

    $settings = $app->getContainer()->get('settings');
    $database_connection_settings = $settings['pdo_settings'];

    $DetailsModel->setSqlQueries($sql_queries);
    $DetailsModel->setDatabaseConnectionSettings($database_connection_settings);
    $DetailsModel->setDatabaseWrapper($database_wrapper);

    $DetailsModel->setEventDetails($app, $cleaned_parameters);
}