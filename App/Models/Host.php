<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 27.06.18
 * Time: 14:12
 */

namespace App\Models;

use Core\BaseModel;

class Host extends BaseModel
{
    public function __construct() {
        parent::__construct();
        $this->table_name = "hosts";
    }

    public function getHostsForUser($owner) {
        $query = "SELECT * FROM hosts 
                  WHERE owner='$owner'";
        $result = $this->conn->query($query);

        if (!$result) {
            throw new \Exception($this->conn->error);
        }

        $hosts = [];

        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $hosts[] = $row;
        }

        return $hosts;
    }

    public function getHostByName($serverName) {
        $query = "SELECT * FROM hosts 
                  WHERE server='$serverName'";
        $result = $this->conn->query($query);

        if (!$result) {
            throw new \Exception($this->conn->error);
        }

        $host = $result->fetch_array(MYSQLI_ASSOC);

        return $host;
    }

    public function checkIfUserOwnsHost($server, $user) {
        $host = $this->getHostByName($server);

        if (!$host) {
            throw new \Exception("No such host");
        }

        return $host["owner"] == $user;
    }
}