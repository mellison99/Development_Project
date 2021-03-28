<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->post(
    '/images',
    function(Request $request, Response $response) use ($app)
    {
        $arrayVals = [["John","Doe", "john@example.com"],["Mary","Moe","mary@mail.com"],["July", "Dooley","july@greatstuff.com"],["Anja", "Ravendale", "a_r@test.com"]];
        if(isset($_POST['but_upload'])) {
            var_dump($_FILES);
            $name = $_FILES['file']['name'];
            $target_dir = "profileimages/";
            $target_file = $target_dir . basename($_FILES["file"]["name"]);
            var_dump($target_file);
            // Select file type
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            // Valid file extensions
            $extensions_arr = array("jpg", "jpeg", "png", "gif");

            // Check extension
            if (in_array($imageFileType, $extensions_arr)) {

                // Insert record
                storeFile($app,$name);
//                $query = "insert into images(name) values('" . $name . "')";
//                mysqli_query($con, $query);

                // Upload file
                move_uploaded_file($_FILES['file']['tmp_name'], $target_dir . $name);
                $information = move_uploaded_file($_FILES['file']['tmp_name'], $target_dir . $name);

            }
        }
$id = $request->getParsedBody();
        var_dump($id);
$id  =  $id['name'];
//$result = fetchFile($app,$id);


//$image = $result[0];
//$image_src = "../upload/".$image;


            $html_output =  $this->view->render($response,
            'images.html.twig',
            [
                'landing_page' => LANDING_PAGE . '/loginuser',
                'css_path' => CSS_PATH,
                'page_title' => APP_NAME,
                'method' => 'post',
                'action' => '',
                'page_heading_1' => 'Send a Message',
                'page_heading_2' => 'Message details',
                'error' => $_SESSION['error'],
                'test'=> $information,
                'image'=>  $image_src

            ]);

        processOutput($app, $html_output);

        return $html_output;
    })->setName('image');

        function storeFile($app,$name)
        {
            $database_wrapper = $app->getContainer()->get('databaseWrapper');
            $sql_queries = $app->getContainer()->get('SQLQueries');
            $DetailsModel = $app->getContainer()->get('RegisterDetailsModel');

            $settings = $app->getContainer()->get('settings');
            $database_connection_settings = $settings['pdo_settings'];

            $DetailsModel->setSqlQueries($sql_queries);
            $DetailsModel->setDatabaseConnectionSettings($database_connection_settings);
            $DetailsModel->setDatabaseWrapper($database_wrapper);
            $value = $DetailsModel->storeProfilePic($app, $name, $_SESSION['username']);
            return $value;
        }

        function fetchFile($app,$id){
            $database_wrapper = $app->getContainer()->get('databaseWrapper');
            $sql_queries = $app->getContainer()->get('SQLQueries');
            $DetailsModel = $app->getContainer()->get('RegisterDetailsModel');

            $settings = $app->getContainer()->get('settings');
            $database_connection_settings = $settings['pdo_settings'];

            $DetailsModel->setSqlQueries($sql_queries);
            $DetailsModel->setDatabaseConnectionSettings($database_connection_settings);
            $DetailsModel->setDatabaseWrapper($database_wrapper);
            $value = $DetailsModel->fetchFile($app,$id);
            return $value;
        }
