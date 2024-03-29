<?php

/**
 * calendar.php
 *
 * Author: Matthew
 * Date: 17/01/2021
 *
 * @author Matthew
 */

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->get('/events', function (Request $request, Response $response) use ($app) {
    if (!isset($_SESSION['username'])) {
        $error = "please login";
        $_SESSION['error'] = $error;
        $html_output = $this->view->render($response,
            'homepageform.html.twig');
        return $html_output->withHeader('Location', LANDING_PAGE);
    }
    //var_dump($_SESSION['error']);

    $listOfWeekDays = ['Non','Monday','Tuesday','Wednesday', 'Thursday', 'Friday','Saturday', 'Sunday'];
    $listOfMonths = ['Non','January','February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

    $html_output = $this->view->render($response,
        'events.html.twig',
        [
            'css_path' => CSS_PATH,
            'landing_page' => LANDING_PAGE . '/loginuser',
            'meeting_requests' => LANDING_PAGE . '/meetingack',
            'hosted_meetings'=> LANDING_PAGE . '/meetingshosted',
            'upcoming_meetings'=>LANDING_PAGE . '/upcomingmeetings',
            'save_event'=>LANDING_PAGE . '/eventspost',
            'edit_profile'=> LANDING_PAGE . '/profilemanagement',
            'create_event'=> LANDING_PAGE . '/events',
            'view_event'=> LANDING_PAGE . '/eventView',
            'Send' => LANDING_PAGE . '/createmeeting',
            'action' => 'eventspost',
            'error' => $_SESSION['error'],
            'method' => 'post',
            'method2' => 'post',
            'initial_input_box_value' => null,
            'page_title' => APP_NAME,
            'page_heading_1' => APP_NAME,
            'page_heading_2' => "Create reccurring events",
            'currentDate' =>date('Y-m-d'),
            'weekdays'=>$listOfWeekDays,
            'months'=>$listOfMonths,
        ]);
    $_SESSION['error'] = "";

    $processed_output = processOutput($app, $html_output);
    return $processed_output;

})->setName('homepage');





