<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 24.06.18
 * Time: 21:15
 */

namespace Core;


class BaseController
{
    protected $login_required = false;
    protected $logout_required = false;
    protected $params = [];
    protected $user = "";

    public function __construct($params)
    {
        $this->params = $params;
    }

    public function __call($name, $args) {
        $method = $name . "Action";

        if (method_exists($this, $method)) {
            if ($this->before() !== false) {
                call_user_func_array([$this, $method], $args);
                $this->after();
            } else {
                header("Location: http://$_SERVER[HTTP_HOST]/");
            }
        } else {
            throw new \Exception("Method $method not found in controller " . get_class($this));
        }
    }

    protected function before() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION["user"])) {
            $_SESSION["user"] = "";
        }

        $this->user = $_SESSION["user"];


        if ($_SESSION["user"] == "" && $this->login_required) {
            return false;
        }

        if ($_SESSION["user"] != "" && $this->logout_required) {
            return false;
        }

        return true;
    }

    protected function after() {

    }
}