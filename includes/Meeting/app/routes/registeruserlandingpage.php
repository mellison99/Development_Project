<?php
/**
 * registeruserlandingpage.php
 *
 * Author: Matthew
 * Date: 23/01/2021
 *
 * @author Matthew
 */

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->get('/registeruserlandingpage', function(Request $request, Response $response) use ($app)
{
    if($_SESSION['error'] != '')
    {
        $error = $_SESSION['error'];
    } else
    {
        $error = '';
    }
    $_SESSION['error'] = '';

    $html_output = $this->view->render($response,
        'register.html.twig',
        [
            'css_path' => CSS_PATH,
            'landing_page' => LANDING_PAGE,
            'method' => 'post',
            'action' => 'registeruser',
            'initial_input_box_value' => null,
            'page_title' => APP_NAME,
            'page_heading_1' => APP_NAME,
            'page_heading_2' => 'Register',
            'register' => LANDING_PAGE . '/registeruser',
            'error' => $error,
        ]);

    processOutput($app, $html_output);

    return $html_output;

})->setName('registeruser');

