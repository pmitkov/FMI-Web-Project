<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 25.06.18
 * Time: 23:48
 */

namespace App\Controllers;

use \Core\BaseView;

class Logout extends \Core\BaseController
{
    protected $login_required = true;

    public function logoutAction() {
        $_SESSION = array();

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(),
                "",
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]);
        }

        session_destroy();

        header("Location: http://localhost");
    }
}