<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;


$app->post('/updateuser', function(Request $request, Response $response) use ($app)
{
    if($_SESSION['error'] != '')
    {
        $error = $_SESSION['error2'];
    }
    else
    {
        $error = '';
    }
    if(!isset($_SESSION['username']))
        {
        $error = "please login";
        $_SESSION['error2'] = $error;
        $html_output =  $this->view->render($response,
        'homepageform.html.twig');
        return $html_output->withHeader('Location', LANDING_PAGE);
        }
    $fileArray = $_FILES;
    $tainted_parameters = $request->getParsedBody();

    $cleaned_parameters = cleanupEditParameters($app, $tainted_parameters);
    if (ctype_alpha($cleaned_parameters['sanitised_firstname']) == false || ctype_alpha($cleaned_parameters['sanitised_lastname']) == false )
    {
        $error = 'please only user alphabetic characters for your name';
        $_SESSION['error2'] = $error;
        $html_output =  $this->view->render($response,
            'profilemanagement.html.twig');
        return $html_output->withHeader('Location', LANDING_PAGE . '/profilemanagement');
    }
    if (preg_match('#[^0-9]#',$cleaned_parameters['sanitised_sim']))
    {
        $error = 'please enter numbers only for your mobile number';
        $_SESSION['error2'] = $error;
        $html_output =  $this->view->render($response,
            'profilemanagement.html.twig');
        return $html_output->withHeader('Location', LANDING_PAGE . '/profilemanagement');
    }

    if (strlen($cleaned_parameters['sanitised_sim']) != 12 )
    {
        $error = 'please enter a valid UK mobile number of 12 digits (44 code included)    ';
        $_SESSION['error2'] = $error;
        $html_output =  $this->view->render($response,
            'profilemanagement.html.twig');
        return $html_output->withHeader('Location', LANDING_PAGE . '/profilemanagement');
    }


    updateUser($app, $_SESSION['username'], $cleaned_parameters);
    if(isset($fileArray['profile_image']['name'])){
    $name = $fileArray['profile_image']['name'];

        $target_dir = "profileimages/";
        $target_file = $target_dir . basename($_FILES["profile_image"]["name"]);
               echo($target_file);
        // Select file type
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Valid file extensions
        $extensions_arr = array("jpg", "jpeg", "png", "gif");

        // Check extension
        if (in_array($imageFileType, $extensions_arr)) {
            // Insert record

            // Upload file
           move_uploaded_file($_FILES['file']['tmp_name'], $target_dir . $name);

            updateUserProfileImage($app,$name, $_SESSION['username']);

        }}
    $error = "Success";
    $_SESSION['error2'] = $error;
    $html_output =  $this->view->render($response,
        'profilemanagement.html.twig');
    return $html_output->withHeader('Location', LANDING_PAGE . '/profilemanagement');


$html_output = $this->view->render($response,
'profilemanagement.html.twig',
[
'css_path' => CSS_PATH,
'landing_page' => LANDING_PAGE,
'method' => 'post',
'action' => 'registeruser',
'initial_input_box_value' => null,
'page_title' => APP_NAME,
'page_heading_1' => APP_NAME,
'page_heading_2' => 'Register',
'update' => LANDING_PAGE . '/updateuser',
'error' => $error,
]);

processOutput($app, $html_output);

return $html_output;

})->setName('registeruser');

function cleanupEditParameters($app, $tainted_parameters)
{
    $cleaned_parameters = [];
    $validator = $app->getContainer()->get('validator');

    $tainted_firstname = $tainted_parameters['firstname'];
    $tainted_lastname = $tainted_parameters['lastname'];
    $tainted_sim = $tainted_parameters['sim'];


    $cleaned_parameters['sanitised_firstname'] = $validator->sanitiseString($tainted_firstname);
    $cleaned_parameters['sanitised_lastname'] = $validator->sanitiseString($tainted_lastname);
    $cleaned_parameters['sanitised_sim'] = $validator->sanitiseString($tainted_sim);

    return $cleaned_parameters;
}

function updateUser($app, $email, $cleanedparameters){
$database_wrapper = $app->getContainer()->get('databaseWrapper');
$sql_queries = $app->getContainer()->get('SQLQueries');
$DetailsModel = $app->getContainer()->get('UpdateUserModel');

$settings = $app->getContainer()->get('settings');
$database_connection_settings = $settings['pdo_settings'];

$DetailsModel->setSqlQueries($sql_queries);
$DetailsModel->setDatabaseConnectionSettings($database_connection_settings);
$DetailsModel->setDatabaseWrapper($database_wrapper);
$DetailsModel->updateUser($app, $email, $cleanedparameters);


}
function updateUserProfileImage($app, $email, $name)
{
    $database_wrapper = $app->getContainer()->get('databaseWrapper');
    $sql_queries = $app->getContainer()->get('SQLQueries');
    $DetailsModel = $app->getContainer()->get('RegisterDetailsModel');
    $DetailsModel2 = $app->getContainer()->get('DeleteUserModel');

    $settings = $app->getContainer()->get('settings');
    $database_connection_settings = $settings['pdo_settings'];

    $DetailsModel->setSqlQueries($sql_queries);
    $DetailsModel->setDatabaseConnectionSettings($database_connection_settings);
    $DetailsModel->setDatabaseWrapper($database_wrapper);
    $DetailsModel2->setSqlQueries($sql_queries);
    $DetailsModel2->setDatabaseConnectionSettings($database_connection_settings);
    $DetailsModel2->setDatabaseWrapper($database_wrapper);
    $DetailsModel2->deleteProfilePic($app, $name);
    $DetailsModel->storeProfilePic($app, $email, $name);

}


