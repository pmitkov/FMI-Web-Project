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
    protected $params = [];

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
            }
        } else {
            throw new \Exception("Method $method not found in controller " . get_class($this));
        }
    }

    protected function before() {

    }

    protected function after() {

    }
}