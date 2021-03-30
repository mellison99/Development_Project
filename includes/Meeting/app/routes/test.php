<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->get(
    '/test',
    function(Request $request, Response $response) use ($app)
    {
        $arrayVals = [["John","Doe", "john@example.com"],["Mary","Moe","mary@mail.com"],["July", "Dooley","july@greatstuff.com"],["Anja", "Ravendale", "a_r@test.com"]];


        $html_output =  $this->view->render($response,
            'test2.html.twig',
            [
                'landing_page' => LANDING_PAGE . '/loginuser',
                'css_path' => CSS_PATH,
                'page_title' => APP_NAME,
                'method' => 'post',
                'action' => 'images',
                'page_heading_1' => 'Send a Message',
                'page_heading_2' => 'Message details',
                'error' => $_SESSION['error'],
                'test'=> $arrayVals,

            ]);

        processOutput($app, $html_output);

        return $html_output;
    })->setName('test');
