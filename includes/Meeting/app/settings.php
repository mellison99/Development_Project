<?php
/**
 * Created by PhpStorm.
 * User: slim
 * Date: 13/10/17
 * Time: 12:33
 */

ini_set('display_errors', 'Off');
ini_set('html_errors', 'Off');
ini_set('xdebug.trace_output_name', 'telemetry.%t');

define('DIRSEP', DIRECTORY_SEPARATOR);

$url_root = $css_path = $_SERVER['SCRIPT_NAME'];
$url_root = implode('/', explode('/', $url_root, -1));
$css_path = $url_root . '/css/standard.css';
define('CSS_PATH', $css_path);
define('APP_NAME', 'Meeting');
define('LANDING_PAGE', $_SERVER['SCRIPT_NAME']);

define ('BCRYPT_ALGO', PASSWORD_DEFAULT);
define ('BCRYPT_COST', 12);

$wsdl = 'https://m2mconnect.ee.co.uk/orange-soap/services/MessageServiceByCountry?wsdl';
define('WSDL', $wsdl);

$settings = [
    "settings" => [
        'displayErrorDetails' => true,
        'addContentLengthHeader' => false,
        'mode' => 'development',
        'debug' => true,
        'view' => [
            'template_path' => __DIR__ . '/templates/',
            'twig' => [
                'cache' => false,
                'auto_reload' => true,
            ]],
    'pdo_settings' => [
        'rdbms' => 'mysql',
        'host' => 'localhost',
        'db_name' => 'meetingPlanner_db',
        'port' => '3306',
        'user_name' => 'login_user',
        'user_password' => 'login_user_pass',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'options' => [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => true,
        ],
    ]
]
];

return $settings;
