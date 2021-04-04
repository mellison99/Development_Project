<?php
/**
 * logoutuser.php
 *
 * Author: Matthew
 * Date: 23/01/2021
 *
 * @author Matthew
 */

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->post(
    '/logoutuser',
    function(Request $request, Response $response) use ($app)
    {
            logoutAccount($app, $_SESSION['id']);

            $_SESSION['loggedout'] = true;
            unset($_SESSION['loggein']);
            unset($_SESSION['username']);

        $_SESSION['info'] = 'logged out';
        $html_output = $this->view->render($response,
            'homepageform.html.twig',
            []);
        processOutput($app, $html_output);

        return $html_output->withHeader('Location', LANDING_PAGE);

    })->setName('logout');

function logoutAccount($app, $session_id)
{
    $store_data_result = null;

    $database_wrapper = $app->getContainer()->get('databaseWrapper');
    $sql_queries = $app->getContainer()->get('SQLQueries');
    $LogoutModel = $app->getContainer()->get('LogoutModel');

    $settings = $app->getContainer()->get('settings');
    $database_connection_settings = $settings['pdo_settings'];

    $LogoutModel->setSqlQueries($sql_queries);
    $LogoutModel->setDatabaseConnectionSettings($database_connection_settings);
    $LogoutModel->setDatabaseWrapper($database_wrapper);

    $store_data_result = $LogoutModel->unsetSession($app, $session_id);

    return $store_data_result;
}
