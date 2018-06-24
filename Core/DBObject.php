<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 24.06.18
 * Time: 23:07
 */

namespace Core;


class DBObject
{
    public $conn = null;

    private static $instance = null;

    function __construct()
    {
        $config = include("config.php");

        $this->conn = new PDO(
            "mysql:host={$config['DB']['HOST']};dbname={$config['DB']['DATABASE']};charset=UTF8",
            $config['DB']['USER'],
            $config['DB']['PASS']
        );
    }

    static function get_instance() {
        if (!self::$instance) {
            self::$instance = new DBObject();
        }

        return self::$instance;
    }
}