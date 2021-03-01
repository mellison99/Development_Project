<?php
/**
 * sendmessagelandingpage.php
 *
 * Author: Matthew
 * Date: 17/01/2021
 *
 * @author Matthew
 */

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;


$app->get('/sendmessagelandingpage', function(Request $request, Response $response) use ($app)
    {
    if(!isset($_SESSION['username']))
    {
        $error = "please login";
        $_SESSION['error'] = $error;
        $html_output =  $this->view->render($response,
                'homepageform.html.twig');
            return $html_output->withHeader('Location', LANDING_PAGE);
    }

        $html_output =  $this->view->render($response,
            'sendmessage.html.twig',
            [
                'css_path' => CSS_PATH,
                'landing_page' => LANDING_PAGE . '/loginuser',
                'page_title' => APP_NAME,
                'method' => 'post',
                'action' => 'sendmessage',
                'initial_input_box_value' => null,
                'page_heading_1' => 'Create a meeting',
                'page_heading_2' => 'Meeting details',
                'Send' => LANDING_PAGE . '/sendmessage',
            ]);
        processOutput($app, $html_output);
        return $html_output;
    })->setName('sendmessage');
