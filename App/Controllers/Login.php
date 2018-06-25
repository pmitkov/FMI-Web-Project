<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 25.06.18
 * Time: 17:46
 */

namespace App\Controllers;

use \Core\BaseView;
use \App\Models\User;
use Utils\StringParser;


class Login extends \Core\BaseController
{
    public function loginAction($args = []) {
        $error = "";
        $username = "";
        $password = "";

        if (isset($args["error"])) {
            $error = $args["error"];
        }

        if (isset($args["username"])) {
            $username = $args["username"];
        }

        if (isset($args["password"])) {
            $password = $args["password"];
        }

        ob_start(); // Turn on buffering

        BaseView::render("Login/login.php", [
            "error" => $error,
            "username" => $username,
            "password" => $password
        ]); // Get file contents

        $content = ob_get_contents(); // Get buffered contents

        ob_end_clean(); // Turn off buffering

        $view_styles = "App/Views/Login/Styles/login.css";

        BaseView::render("Header/header.php",
            [
                "user" => $this->user,
                "content" => $content,
                "view_styles" => $view_styles,
            ]);
    }

    public function checkAction() {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            if (isset($_POST["username"])) {
                $users = new User();

                $username = StringParser::sanitizeString($_POST["username"]);

                if ($users->checkIfUserExists($username)) {
                    echo json_encode(["exists" => true]);
                } else {
                    echo json_encode(["exists" => false]);
                }
            } else {
                throw new \Exception("Invalid post data");
            }
        } else {
            throw new \Exception("Invalid method type");
        }
    }

    public function finalizeAction()
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            if (isset($_POST["username"]) && isset($_POST["password"])) {
                $users = new User();

                $username = StringParser::sanitizeString($_POST["username"]);
                $password = StringParser::sanitizeString($_POST["password"]);

                if ($users->checkCredentials($username, $password)) {
                    $_SESSION["user"] = $username;

                    header("Location: http://localhost/home");
                } else {
                    $this->loginAction([
                        "error" => "Wrong username or password",
                        "username" => $username,
                        "password" => $password
                    ]);
                }

            } else {
                throw new \Exception("Invalid post data");
            }
        } else {
            throw new \Exception("Invalid method type");
        }
    }
}