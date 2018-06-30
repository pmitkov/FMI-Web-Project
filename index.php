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
$router->add("login", ["controller" => "Login", "action" => "login"]);
$router->add("logout", ["controller" => "Logout", "action" => "logout"]);
$router->add("home", ["controller" => "Home", "action" => "home"]);
$router->add("register", ["controller" => "Register", "action" => "register"]);
$router->add("monitoring", ["controller" => "Monitoring", "action" => "monitor"]);
$router->add("monitoring/{server:.+}", ["controller" => "Monitoring", "action" => "updateMonitoring"]);
$router->add("raw", ["controller" => "RawLogs", "action" => "rawLogs"]);
$router->add("raw/{server:.+}/{page:\d+}", ["controller" => "RawLogs", "action" => "rawLogsPage"]);
$router->add("raw/refresh/{server:.+}", ["controller" => "RawLogs", "action" => "rawLogsRefresh"]);
$router->add("charts", ["controller" => "LogCharts", "action" => "charts"]);
$router->add("{controller}/{action}");

$router->dispatch($_SERVER["QUERY_STRING"]);
