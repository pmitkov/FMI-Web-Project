<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 24.06.18
 * Time: 20:54
 */

use Core\Router;

$router = new Router();

$router->add("", ["controller" => "Home", "action" => "index"]);
$router->add("{controller}/{action}");

$router->dispatch($_SERVER["QUERY_STRING"]);

