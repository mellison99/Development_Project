<?php

$container['view'] = function ($container) {
  $view = new \Slim\Views\Twig(
    $container['settings']['view']['template_path'],
    $container['settings']['view']['twig'],
    [
      'debug' => false
    ]
  );

  $basePath = rtrim(str_ireplace('index.php', '', $container['request']->getUri()->getBasePath()), '/');
  $view->addExtension(new Slim\Views\TwigExtension($container['router'], $basePath));

  return $view;
};

$container['bcryptWrapper'] = function ($container) {
  $bcryptWrapper = new \Meeting\BcryptWrapper();
  return $bcryptWrapper;
};

$container['LogoutModel'] = function ($container) {
    $LogoutModel = new \Meeting\LogoutModel();
    return $LogoutModel;
};

$container['LoginDetailsModel'] = function ($container) {
    $loginDetailsModel = new \Meeting\LoginDetailsModel();
    return $loginDetailsModel;
};

$container['RegisterDetailsModel'] = function ($container) {
    $registerDetailsModel = new \Meeting\RegisterDetailsModel();
    return $registerDetailsModel;
};

$container['databaseWrapper'] = function ($container) {
    $database_wrapper = new \Meeting\DatabaseWrapper();
    return $database_wrapper;
};

$container['SQLQueries'] = function ($container) {
    $sql_queries = new \Meeting\SQLQueries();
    return $sql_queries;
};

$container['RetrieveDataModel'] = function ($container) {
    $RetrieveDataModel = new \Meeting\RetrieveDataModel();
    return $RetrieveDataModel;
};

$container['processOutput'] = function ($container) {
    $processOutput = new \Meeting\ProcessOutput();
    return $processOutput;
};

$container['DetailsModel'] = function ($container) {
    $DetailsModel = new \Meeting\MessageModel();
    return $DetailsModel;
};

$container['validator'] = function ($container)
{
    $validator = new \Meeting\Validator();
    return $validator;
};

$container['XmlParser'] = function ($container)
{
    $xml_parser = new \Meeting\XmlParser();
    return $xml_parser;
};

$container['SoapWrapper'] = function ($container) {
    $retrieve_stock_data_model = new \Meeting\SoapWrapper();
    return $retrieve_stock_data_model;
};
