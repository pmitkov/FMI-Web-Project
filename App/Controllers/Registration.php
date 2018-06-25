<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 25.06.18
 * Time: 11:22
 */

namespace App\Controllers;


class Registration extends \Core\BaseController
{
    public function registerAction() {
        ob_start(); // Turn on buffering

        BaseView::render("Register/register.php"); // Get file contents

        $template = ob_get_contents(); // Get buffered contents

        ob_end_clean(); // Turn off buffering

        $view_styles = "App/Views/Register/register.css";

        BaseView::render("Header/header.php");
    }
}