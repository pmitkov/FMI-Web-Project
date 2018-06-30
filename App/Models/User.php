<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 25.06.18
 * Time: 19:03
 */

namespace App\Models;

use Core\BaseModel;

class User extends BaseModel
{
    public function __construct() {
        parent::__construct();
        $this->table_name = "users";
    }

    public function checkIfUserExists($username) {
        $query = "SELECT * 
                  FROM users 
                  WHERE username='$username'";
        $result = $this->conn->query($query);

        if (!$result) {
            throw new \Exception($this->conn->error);
        }

        if ($result->num_rows == 0) {
            return false;
        } else {
            return true;
        }
    }

    public function checkIfEmailExists($email) {
        $query = "SELECT * 
                  FROM users
                  WHERE email='$email'";
        $result = $this->conn->query($query);

        if (!$result) {
            throw new \Exception($this->conn->error);
        }

        if ($result->num_rows == 0) {
            return false;
        } else {
            return true;
        }
    }

    public function createAccount($username, $firstname, $lastname, $password, $email, $phone) {
        $query = "INSERT INTO users (username, firstname, lastname, password, email, phone) 
                  VALUES ('$username', '$firstname', '$lastname', '$password', '$email', '$phone');";
        $result = $this->conn->query($query);

        if (!$result) {
            throw new \Exception($this->conn->error);
        }
    }

    public function checkCredentials($username, $password) {
        $query = "SELECT * 
                  FROM users 
                  WHERE username='$username'";
        $result = $this->conn->query($query);

        if (!$result) {
            throw new \Exception($this->conn->error);
        }

        if ($result->num_rows == 0) {
            return false;
        } else if ($password != $result->fetch_array(MYSQLI_ASSOC)["password"]) {
            return false;
        } else {
            return true;
        }
    }
}