<?php
/**
 * loginuser.php
 *
 * Author: Matthew
 * Date: 23/01/2021
 *
 * @author Matthew
 */

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->any(
    '/loginuser',
    function(Request $request, Response $response) use ($app)
    {
        $error = '';
        $landing_page = LANDING_PAGE . '/loginuser';
        $action = 'logoutuser';

        if(!isset($_SESSION['loggein']))
        {
            if(isset($_SESSION['loggedout']))
            {
                $html_output =  $this->view->render($response,
                    'homepageform.html.twig');
                unset($_SESSION['loggedout']);
                return $html_output->withHeader('Location', LANDING_PAGE);
            }
            $tainted_parameters = [$_POST['username'], $_POST['password']];
            $cleaned_parameters = cleanupParameterss($app, $tainted_parameters);
            $_SESSION['username'] = $cleaned_parameters['sanitised_email'];
            $_SESSION['password'] = $cleaned_parameters['user_password'];

        $_SESSION['id'] = session_id();
        $_SESSION['error'] = '';

            if (!isset($_POST['username']) || !isset($_POST['password']) && $_POST['username'] == '' || $_POST['password'] == '' )
            {
                $error = 'Please fill both the username and password fields!';
                $_SESSION['error'] = $error;
                $html_output =  $this->view->render($response,
                    'homepageform.html.twig');
                return $html_output->withHeader('Location', LANDING_PAGE);
            }else
            {
                if (checkExistingAccount($app, $cleaned_parameters) == true)
                {
                    if(isset($_POST['submit']))
                    {
                        if (authenticatePassword($app, $_SESSION['password'], selectHashedPassword($app, $cleaned_parameters)) == true)
                        {
                            if(loginExistingAccount($app, $cleaned_parameters) == true) {
                                $_SESSION['loggein'] = true;
                                $info = "Welcome " . $_SESSION['username'];
                                $_SESSION['id'] = session_id();
                                $landing_page = LANDING_PAGE . '/loginuser';
                            }
                        } else
                        {
                            $error = "your password was wrong please try again";
                            $_SESSION['error'] = $error;
                            $html_output =  $this->view->render($response,
                                'register.html.twig');
                            return $html_output->withHeader('Location', LANDING_PAGE );
                        }
                    } else
                    {
                        $html_output =  $this->view->render($response,
                            'homepageform.html.twig');
                        return $html_output->withHeader('Location', LANDING_PAGE);
                    }
                } else
                {
                    $error = "you dont have an account please register";
                    $_SESSION['error'] = $error;
                    $html_output =  $this->view->render($response,
                        'register.html.twig');
                    return $html_output->withHeader('Location', LANDING_PAGE . '/registeruserlandingpage');
                }
            }
        }
        $result = getUserPic($app, $_SESSION['username']);
        $image = $result[0];
        if($image == null){
            $image = "pic.png";
        }
        $image_src = "../profileimages/".$image;

        $info = "Welcome ". $_SESSION['username'];

        $html_output =  $this->view->render($response,
            'login_user.html.twig',
            [
                'landing_page' => $landing_page,
                'css_path' => CSS_PATH,
                'page_title' => APP_NAME,
                'method' => 'post',
                'action' => $action,
                'page_heading_1' => 'UserLoggedIn',
                'page_heading_2' => $_SESSION['username'],
                'send_message' => LANDING_PAGE . '/calendar',
                'view_message' => LANDING_PAGE. '/meetingack',
                'view_upcoming' => LANDING_PAGE. '/upcomingmeetings',
                'settings' => LANDING_PAGE. '/profilemanagement',
                'events' => LANDING_PAGE. '/eventView',
                'error' => $error,
                'info' => $info,
                'profile_image'=>$image_src,
                'logout' => LANDING_PAGE . '/',
            ]);

        processOutput($app, $html_output);

        return $html_output;
    })->setName('login');

function cleanupParameterss($app, $tainted_parameters)
{
    $cleaned_parameters = [];
    $validator = $app->getContainer()->get('validator');

    $tainted_username = $tainted_parameters[0];
    $tainted_password = $tainted_parameters[1];

    $cleaned_parameters['user_password'] = $tainted_password;
    $cleaned_parameters['sanitised_email'] = $validator->sanitiseString($tainted_username);

    return $cleaned_parameters;
}

function hash_passwords($app, $password_to_hash): string
{
    $bcrypt_wrapper = $app->getContainer()->get('bcryptWrapper');
    $hashed_password = $bcrypt_wrapper->createHashedPassword($password_to_hash);
    return $hashed_password;
}


function authenticatePassword($app, $string_to_check, $stored_user_password_hash): bool
{
    $bcrypt_wrapper = $app->getContainer()->get('bcryptWrapper');
    $authentication = $bcrypt_wrapper->authenticatePassword($string_to_check, $stored_user_password_hash);
    return $authentication;
}

function checkExistingAccount($app, array $cleaned_parameters): bool
{
    $store_data_result = null;

    $database_wrapper = $app->getContainer()->get('databaseWrapper');
    $sql_queries = $app->getContainer()->get('SQLQueries');
    $DetailsModel = $app->getContainer()->get('LoginDetailsModel');

    $settings = $app->getContainer()->get('settings');
    $database_connection_settings = $settings['pdo_settings'];

    $DetailsModel->setSqlQueries($sql_queries);
    $DetailsModel->setDatabaseConnectionSettings($database_connection_settings);
    $DetailsModel->setDatabaseWrapper($database_wrapper);

    $store_data_result = $DetailsModel->checkExistingAccount($app, $cleaned_parameters);

    if($store_data_result == true){
         $store_data_result = true;
    } else
        {
            $store_data_result = false;
        }
    return $store_data_result;
}

function checkSessionID($app,  $cleaned_parameters)
{
    $store_data_result = null;

    $database_wrapper = $app->getContainer()->get('databaseWrapper');
    $sql_queries = $app->getContainer()->get('SQLQueries');
    $DetailsModel = $app->getContainer()->get('LoginDetailsModel');

    $settings = $app->getContainer()->get('settings');
    $database_connection_settings = $settings['pdo_settings'];

    $DetailsModel->setSqlQueries($sql_queries);
    $DetailsModel->setDatabaseConnectionSettings($database_connection_settings);
    $DetailsModel->setDatabaseWrapper($database_wrapper);

    $store_data_result = $DetailsModel->checkSessionID($app, $cleaned_parameters);

    return $store_data_result;
}

function loginExistingAccount($app, array $cleaned_parameters)
{
    $store_data_result = null;

    $database_wrapper = $app->getContainer()->get('databaseWrapper');
    $sql_queries = $app->getContainer()->get('SQLQueries');
    $LoginModel = $app->getContainer()->get('LoginDetailsModel');

    $settings = $app->getContainer()->get('settings');
    $database_connection_settings = $settings['pdo_settings'];

    $LoginModel->setSqlQueries($sql_queries);
    $LoginModel->setDatabaseConnectionSettings($database_connection_settings);
    $LoginModel->setDatabaseWrapper($database_wrapper);

    $cleaned_parameters['session_id'] = session_id();

    $store_data_result  = $LoginModel->storeDataInSessionDatabase($app, $cleaned_parameters);

    return $store_data_result;
}

function selectHashedPassword($app, array $cleaned_parameters)
{
    $store_data_result = null;

    $database_wrapper = $app->getContainer()->get('databaseWrapper');
    $sql_queries = $app->getContainer()->get('SQLQueries');
    $DetailsModel = $app->getContainer()->get('LoginDetailsModel');

    $settings = $app->getContainer()->get('settings');
    $database_connection_settings = $settings['pdo_settings'];

    $DetailsModel->setSqlQueries($sql_queries);
    $DetailsModel->setDatabaseConnectionSettings($database_connection_settings);
    $DetailsModel->setDatabaseWrapper($database_wrapper);

    $result = $DetailsModel->selectHashedPassword($app, $cleaned_parameters);

    return $result;
}


