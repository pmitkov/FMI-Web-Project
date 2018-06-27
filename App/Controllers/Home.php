<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 25.06.18
 * Time: 22:17
 */

namespace App\Controllers;

use \Core\BaseView;

use \Utils\LogLoader;

class Home extends \Core\BaseController
{
    protected $login_required = true;

    public function homeAction() {
        $server = "40.89.132.106";
        $user = "u61936";
        $pass = "38564538onE$";

        $files = ["/var/log/nginx/access.log", "/var/log/nginx/error.log"];

        LogLoader::loadLogs($server, $user, $pass, $files);

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