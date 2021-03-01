<?php
/**
 * registeruser.php
 *
 * Author: Hasan
 * Date: 17/01/2021
 *
 * @author Hasan
 */

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->post(
    '/registeruser',
    function(Request $request, Response $response) use ($app)
    {
        $error = "";
        $tainted_parameters = $request->getParsedBody();

        $cleaned_parameters = cleanupParameterss1($app, $tainted_parameters);
        $hashed_password = hash_passwords($app, $tainted_parameters['password']);

        $_SESSION['error'] = $cleaned_parameters;
        if($_POST['firstname'] == '' || $_POST['lastname'] == '' || $_POST['email'] == '' || $_POST['role'] == '' || $_POST['sim'] == '' || $_POST['password'] == '')
        {
            $error = 'please fill in all details to register';
            $_SESSION['error'] = $error;
            $html_output =  $this->view->render($response,
                'register.html.twig');
            return $html_output->withHeader('Location', LANDING_PAGE . '/registeruserlandingpage');

        } else
            {
                if (ctype_alpha($cleaned_parameters['sanitised_firstname']) == false || ctype_alpha($cleaned_parameters['sanitised_lastname']) == false )
                {
                    $error = 'please only user alphabetic characters for your name';
                    $_SESSION['error'] = $error;
                    $html_output =  $this->view->render($response,
                        'register.html.twig');
                    return $html_output->withHeader('Location', LANDING_PAGE . '/registeruserlandingpage');
                }

                if (preg_match('/'.preg_quote('@', '/').'/', $cleaned_parameters['sanitised_email']) == false || substr($cleaned_parameters['sanitised_email'], -6) != '.co.uk')
                {
                    $error = 'please enter a valid email address';
                    $_SESSION['error'] = $error;
                    $html_output =  $this->view->render($response,
                        'register.html.twig');
                    return $html_output->withHeader('Location', LANDING_PAGE . '/registeruserlandingpage');
                }

                if (preg_match('#[^0-9]#',$cleaned_parameters['sanitised_sim']))
                {
                    $error = 'please enter numbers only for your mobile number';
                    $_SESSION['error'] = $error;
                    $html_output =  $this->view->render($response,
                        'register.html.twig');
                    return $html_output->withHeader('Location', LANDING_PAGE . '/registeruserlandingpage');
                }

                if (strlen($cleaned_parameters['sanitised_sim']) != 11 )
                {
                    $error = 'please enter a valid UK mobile number of 11 digits    ';
                    $_SESSION['error'] = $error;
                    $html_output =  $this->view->render($response,
                        'register.html.twig');
                    return $html_output->withHeader('Location', LANDING_PAGE . '/registeruserlandingpage');
                }

//                if(checkNumber($app, $cleaned_parameters) == true)
//                {
//                    $error = "That number is already registered, please login ";
//                    $_SESSION['error'] = $error;
//                    $html_output =  $this->view->render($response,
//                        'register.html.twig');
//                    return $html_output->withHeader('Location', LANDING_PAGE . '/registeruserlandingpage');
//                }
            }


        if($error == '')
        {storeUserDetails($app, $cleaned_parameters, $hashed_password);
            try {

                       try {
                           $stored = true;
                           //StoreMetaData($app, $cleaned_parameters);
                           if($stored == true)
                           {
                               $e = "Thank you for registering you can now login ";
                               $_SESSION['error'] = $e;
                               $html_output =  $this->view->render($response,
                                   'homepageform.html.twig');
                               return $html_output->withHeader('Location', LANDING_PAGE );
                           }
                       }catch (exception $e) {
                           $e = "there was an issue registering ";
                           $_SESSION['error'] = $e;
                           $html_output =  $this->view->render($response,
                               'register.html.twig');
                           return $html_output->withHeader('Location', LANDING_PAGE . '/registeruserlandingpage');
                       }
            } catch (exception $e){
                $e = "Email already in use";
                $_SESSION['error'] = $e;
                $html_output =  $this->view->render($response,
                    'register.html.twig');
                return $html_output->withHeader('Location', LANDING_PAGE . '/registeruserlandingpage');
            };
        }

        $html_output =  $this->view->render($response,
            'register_user.html.twig',
            [
                'landing_page' => LANDING_PAGE,
                'css_path' => CSS_PATH,
                'page_title' => APP_NAME,
                'method' => 'post',
                'action' => 'registeruser',
                'page_heading_1' => 'Registration',
                'page_heading_2' => 'Details',
                'error' => $error,
            ]);

        processOutput($app, $html_output);

        return $html_output;
    });

function cleanupParameterss1($app, $tainted_parameters)
{
    $cleaned_parameters = [];
    $validator = $app->getContainer()->get('validator');

    $tainted_email = $tainted_parameters['email'];
    $tainted_lastname = $tainted_parameters['lastname'];
    $tainted_firstname = $tainted_parameters['firstname'];
    $tainted_sim = $tainted_parameters['sim'];
    $tainted_role = $tainted_parameters['role'];

    $cleaned_parameters['sanitised_email'] = $validator->sanitiseString($tainted_email);
    $cleaned_parameters['sanitised_firstname'] = $validator->sanitiseString($tainted_firstname);
    $cleaned_parameters['sanitised_lastname'] = $validator->sanitiseString($tainted_lastname);
    $cleaned_parameters['sanitised_role'] = $validator->sanitiseString($tainted_role);
    $cleaned_parameters['sanitised_sim'] = $validator->sanitiseString($tainted_sim);

    return $cleaned_parameters;

}

function storeUserDetails($app, array $cleaned_parameters, string $hashed_password)
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

    $DetailsModel->setRegisterDetails($app, $cleaned_parameters, $hashed_password);
}


function StoreMetaData($app, array $cleaned_parameters)
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
    $DetailsModel->setRegisterMetaDetails($app, $cleaned_parameters);
}


function checkNumber($app, $cleaned_parameters)

{
    $result = false;

    $database_wrapper = $app->getContainer()->get('databaseWrapper');
    $sql_queries = $app->getContainer()->get('SQLQueries');
    $DetailsModel = $app->getContainer()->get('RegisterDetailsModel');

    $settings = $app->getContainer()->get('settings');
    $database_connection_settings = $settings['pdo_settings'];

    $DetailsModel->setSqlQueries($sql_queries);
    $DetailsModel->setDatabaseConnectionSettings($database_connection_settings);
    $DetailsModel->setDatabaseWrapper($database_wrapper);
    $result = $DetailsModel->checkNumber($app, $cleaned_parameters);

    return $result;
}
