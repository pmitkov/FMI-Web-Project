<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 25.06.18
 * Time: 22:17
 */

namespace App\Controllers;

use \Core\BaseView;

class Home extends \Core\BaseController
{
    protected $login_required = true;

    public function homeAction() {
        ob_start(); // Turn on buffering

        BaseView::render("Home/home.php"); // Get file contents

        $content = ob_get_contents(); // Get buffered contents

        ob_end_clean(); // Turn off buffering

        $view_styles = "App/Views/Home/Styles/home.css";

        BaseView::render("Header/header.php",
            [
                "user" => $this->user,
                "content" => $content,
                "view_styles" => $view_styles,
        ]);
    }
}