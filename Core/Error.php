<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 25.06.18
 * Time: 10:26
 */

namespace Core;


class Error
{
    public static function errorHandler($level, $message, $file, $line) {
        if (error_reporting() !== 0) {
            throw new \ErrorException($message, 0, $level, $file, $line);
        }
    }

    public static function exceptionHandler($exception) {
        $code = $exception->getCode();

        if ($code != 404) {
            $code = 500;
        }

        http_response_code($code);

        // If SHOW_ERRORS is on(dev mode), show all errors
        if (\App\Config::SHOW_ERRORS) {
            echo "<h1>Fatal Error</h1>";
            echo "<p>Uncaught exception: '" . get_class($exception) . "'</p>";
            echo "<p>Message: '" . $exception->getMessage() . "'</p>";
            echo "<p>Stack trace:<pre>" . $exception->getTraceAsString() . "</pre></p>";
            echo "<p>Thrown in '" . $exception->getFile() . "' on line " . $exception->getLine() . "</p>";
        // If SHOW_ERRORS is off(prod mode), show 404 or 500 page and log errors in log
        } else {
            $log = dirname(__DIR__) . "/logs/" . date("Y-m-d") . ".txt";
            ini_set("error_log", $log);

            $message = "Uncaught exception: '" . get_class($exception) . "'";
            $message .= " with message '" . $exception->getMessage() . "'";
            $message .= "\nStack trace: " . $exception->getTraceAsString();
            $message .= "\nThrown in '" . $exception->getFile() . "' on line " . $exception->getLine();
            error_log($message);

            BaseView::render("$code.html");
        }
    }
}