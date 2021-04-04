<?php
/**
 * homepage.php
 *
 * Author: Matthew
 * Date: 17/01/2021
 *
 * @author Matthew
 */

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->get('/', function(Request $request, Response $response) use ($app)
{
    if(!isset($_SESSION['loggedout']))
    {
        logoutAccount($app, $_SESSION['id']);
        unset($_SESSION['loggedout']);
    }

   if($_SESSION['error'] != '')
   {
       $error = $_SESSION['error'];
   } else
       {
           $error = '';
       }

    if($_SESSION['info'] != '')
    {
        $info = $_SESSION['info'];
    } else
        {
            $info = '';
        }

   $_SESSION['error'] = '';
   $_SESSION['info'] = '';
   $_SESSION['id'] = session_id();
   session_regenerate_id();

    $html_output = $this->view->render($response,
        'homepageform.html.twig',
        [
            'css_path' => CSS_PATH,
            'landing_page' => LANDING_PAGE,
            'method' => 'post',
            'action' => 'loginuser',
            'initial_input_box_value' => null,
            'page_title' => APP_NAME,
            'page_heading_1' => APP_NAME,
            'page_heading_2' => 'Login',
            'error' => $error,
            'info' => $info,
            'register' => LANDING_PAGE . '/registeruserlandingpage',
            'login' => LANDING_PAGE . '/loginuser',
        ]);

    processOutput($app, $html_output);

    return $html_output;

})->setName('homepage');

function processOutput($app, $html_output)
{
    $process_output = $app->getContainer()->get('processOutput');
    $html_output = $process_output->processOutput($html_output);
    return $html_output;
}

