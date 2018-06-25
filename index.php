<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 24.06.18
 * Time: 20:54
 */

require "vendor/autoload.php";

/*
 *  Error and Exception handling
 **/
error_reporting(E_ALL);
set_error_handler("Core\Error::errorHandler");
set_exception_handler("Core\Error::exceptionHandler");

$router = new Core\Router();

$router->add("", ["controller" => "LandingPage", "action" => "index"]);
$router->add("{controller}/{action}");

$router->dispatch($_SERVER["QUERY_STRING"]);

