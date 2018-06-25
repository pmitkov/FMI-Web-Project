<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 24.06.18
 * Time: 23:07
 */

namespace Core;

use \App\Config;

use mysqli;

class DBObject
{
    public $conn = null;

    private static $instance = null;

    private function __construct()
    {
        $dbhost = Config::DB_HOST;
        $dbname = Config::DB_NAME;
        $dbuser = Config::DB_USER;
        $dbpass = Config::DB_PASSWORD;

        $this->conn = new mysqli($dbhost, $dbuser, $dbpass, $dbname);

        if ($this->conn->connect_error) {
            throw new \Exception("Failed to connect to database");
        }
    }

    static function get_instance() {
        if (!self::$instance) {
            self::$instance = new DBObject();
        }

        return self::$instance;
    }
}