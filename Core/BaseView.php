<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 24.06.18
 * Time: 23:00
 */

namespace Core;

class BaseView
{
    public static function render($view, $params = []) {
        $file = dirname(__DIR__) . "/App/Views/$view";

        if (is_readable($file)) {
            require $file;
        } else {
            throw new \Exception("$file not found");
        }
    }
}