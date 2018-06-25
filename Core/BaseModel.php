<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 24.06.18
 * Time: 21:17
 */

namespace Core;


class BaseModel
{
    protected $table_name;
    public $conn;

    function __construct() {
        $this->conn = DBObject::get_instance()->conn;
    }

    public function getAll() {
        $query = "SELECT * FROM {$this->table_name}";
        $result = $this->conn->query($query);

        if (!$result) {
            throw new \Exception($this->conn->error);
        }

        $result = array();

        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $result[] = $row;
        }

        return $result;
    }

    public function get($id) {
        $query = "SELECT * FROM {$this->table_name} where id = $id";
        $result = $this->conn->query($query);

        if (!$result) {
            throw new \Exception($this->conn->error);
        }

        return $result->fetch_array(MYSQLI_ASSOC);
    }
}