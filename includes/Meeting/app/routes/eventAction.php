<?php

/**
 * eventAction.php
 *
 * Author: Matthew
 * Date: 20/03/2021
 *
 * @author Matthew
 */

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->get('/eventAction', function (Request $request, Response $response) use ($app) {
    if (!isset($_SESSION['username'])) {
        $error = "please login";
        $_SESSION['error'] = $error;
        $html_output = $this->view->render($response,
            'homepageform.html.twig');
        return $html_output->withHeader('Location', LANDING_PAGE);
    }
    $email = $_SESSION['username'];
    $tainted_MiD = $_GET["MiD"];
    $cleaned_MiD = cleanupParameters($app, $tainted_MiD);
    if($cleaned_MiD[0] == "D"){
        var_dump(deleteEventFromDatabase($app, $cleaned_MiD, $email));
        $error = "Event Deleted";
        $_SESSION['error'] = $error;
        $html_output =  $this->view->render($response,
            'eventView.html.twig');
        return $html_output->withHeader('Location', LANDING_PAGE . '/eventView');
    }
    else{
        toggleEventState($app, $cleaned_MiD, $email);
        if($cleaned_MiD[0] == "1"){
            $error = "Event set to active";
            $_SESSION['error'] = $error;
            $html_output =  $this->view->render($response,
                'eventView.html.twig');
            return $html_output->withHeader('Location', LANDING_PAGE . '/eventView');
        }
        if($cleaned_MiD[0] == "0"){
            $error = "Event disabled";
            $_SESSION['error'] = $error;
            $html_output =  $this->view->render($response,
                'eventView.html.twig');
            return $html_output->withHeader('Location', LANDING_PAGE . '/eventView');
        }
    }

    $html_output = $this->view->render($response,
        'eventAction.html.twig',
        [
            'css_path' => CSS_PATH,
            'landing_page' => LANDING_PAGE . '/loginuser',
            'meeting_requests' => LANDING_PAGE . '/downloadedmessageselect',
            'upcoming_meetings'=>LANDING_PAGE . '/upcomingmeetings',
            'save_event'=>LANDING_PAGE . '/eventspost',
            'Send' => LANDING_PAGE . '/sendmessage',
            'action' => 'eventEdit',
            'error' => $_SESSION['error'],
            'method' => 'post',
            'method2' => 'post',
            'initial_input_box_value' => null,
            'page_title' => APP_NAME,
            'page_heading_1' => APP_NAME,
            'page_heading_2' => "View events",
            'currentDate' =>date('Y-m-d'),


        ]);
    $_SESSION['error'] = "";

    $processed_output = processOutput($app, $html_output);
    return $processed_output;

})->setName('homepage');

function deleteEventFromDatabase($app, $MiD, $email)
{
    $database_wrapper = $app->getContainer()->get('databaseWrapper');
    $sql_queries = $app->getContainer()->get('SQLQueries');
    $DetailsModel = $app->getContainer()->get('RegisterDetailsModel');

    $settings = $app->getContainer()->get('settings');
    $database_connection_settings = $settings['pdo_settings'];
    $MiD = substr($MiD, 1);
    $DetailsModel->setSqlQueries($sql_queries);
    $DetailsModel->setDatabaseConnectionSettings($database_connection_settings);
    $DetailsModel->setDatabaseWrapper($database_wrapper);
    $DetailsModel->deleteEventData($app,$MiD, $email);

}
function toggleEventState($app, $MiD, $email)
{
    $database_wrapper = $app->getContainer()->get('databaseWrapper');
    $sql_queries = $app->getContainer()->get('SQLQueries');
    $DetailsModel = $app->getContainer()->get('RegisterDetailsModel');

    $settings = $app->getContainer()->get('settings');
    $database_connection_settings = $settings['pdo_settings'];
    $state = $MiD[0];
    $MiD = substr($MiD, 1);
    $DetailsModel->setSqlQueries($sql_queries);
    $DetailsModel->setDatabaseConnectionSettings($database_connection_settings);
    $DetailsModel->setDatabaseWrapper($database_wrapper);
    $DetailsModel->updateEventStatus($app,$MiD,$email,$state);

}

