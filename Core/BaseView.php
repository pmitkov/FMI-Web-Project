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
    function render($view, $params) {
        ob_start(); // Turn on buffering

        require("App/Views/$view.php"); // Get file contents

        $template = ob_get_contents(); // Get buffered contents

        ob_end_clean(); // Turn off buffering

        $view_styles = "App/Views/$view.css";

        require("App/Views/Header.php");
    }
}