<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 25.06.18
 * Time: 11:22
 */

namespace App\Controllers;

use \Core\BaseView;
use \App\Models\User;
use Utils\StringParser;

class Register extends \Core\BaseController
{
    public function registerAction($args = []) {
        $errors = [];
        $username = "";
        $firstname = "";
        $lastname = "";
        $password = "";
        $repeat_password = "";
        $email = "";
        $phone = "";

        if (isset($args["errors"])) {
            $errors = $args["errors"];
        }

        if (isset($args["username"])) {
            $username = $args["username"];
        }

        if (isset($args["firstname"])) {
            $firstname = $args["firstname"];
        }

        if (isset($args["lastname"])) {
            $lastname = $args["lastname"];
        }

        if (isset($args["password"])) {
            $password = $args["password"];
        }

        if (isset($args["repeat_password"])) {
            $repeat_password = $args["repeat_password"];
        }

        if (isset($args["email"])) {
            $email = $args["email"];
        }

        if (isset($args["phone"])) {
            $phone = $args["phone"];
        }

        ob_start(); // Turn on buffering

        BaseView::render("Register/register.php", [
            "errors" => $errors,
            "username" => $username,
            "firstname" => $firstname,
            "lastname" => $lastname,
            "password" => $password,
            "repeat_password" => $repeat_password,
            "email" => $email,
            "phone" => $phone
        ]); // Get file contents

        $content = ob_get_contents(); // Get buffered contents

        ob_end_clean(); // Turn off buffering

        $view_styles = "App/Views/Register/Styles/register.css";

        BaseView::render("Header/header.php", [
            "user" => $this->user,
            "content" => $content,
            "view_styles" => $view_styles
        ]);
    }

    public function finalizeAction() {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            if (isset($_POST["username"]) &&
                isset($_POST["firstname"]) &&
                isset($_POST["lastname"]) &&
                isset($_POST["password"]) &&
                isset($_POST["repeat_password"]) &&
                isset($_POST["email"]) &&
                isset($_POST["phone"])) {

                $users = new User();

                $errors = [];

                $username = StringParser::sanitizeString($_POST["username"]);
                $firstname = StringParser::sanitizeString($_POST["firstname"]);
                $lastname = StringParser::sanitizeString($_POST["lastname"]);
                $password = StringParser::sanitizeString($_POST["password"]);
                $repeat_password = StringParser::sanitizeString($_POST["repeat_password"]);
                $email = StringParser::sanitizeString($_POST["email"]);
                $phone = StringParser::sanitizeString($_POST["phone"]);

                if ($username == "") {
                    $errors[] = "Username is required";
                } else if ($users->checkIfUserExists($username)) {
                    $errors[] = "Username already exists";
                }

                if ($email == "") {
                    $errors[] = "Email is required";
                } else if ($users->checkIfEmailExists($email)) {
                    $errors[] = "Email already exists";
                }

                if ($password == "") {
                    $errors[] = "Password is required";
                }

                if ($repeat_password == "") {
                    $errors[] = "Repeated password is required";
                }

                if ($password != $repeat_password) {
                    $errors[] = "Passwords don't match";
                }

                if ($firstname == "") {
                    $errors[] = "First name is required";
                }

                if ($lastname == "") {
                    $errors[] = "Last name is required";
                }

                if ($phone == "") {
                    $errors[] = "Phone is required";
                }

                if (empty($errors)) {
                    $users->createAccount($username, $firstname, $lastname, $password, $email, $phone);

                    $_SESSION["user"] = $username;

                    header("Location: http://localhost/home");
                } else {
                    $this->registerAction([
                        "errors" => $errors,
                        "username" => $username,
                        "firstname" => $firstname,
                        "lastname" => $lastname,
                        "password" => $password,
                        "repeat_password" => $repeat_password,
                        "email" => $email,
                        "phone" => $phone
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