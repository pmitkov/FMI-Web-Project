<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 28.06.18
 * Time: 18:57
 */

namespace App\Models;

use Core\BaseModel;
use Utils\LogLoader;
use \DateTime;

class AccessLog extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
        $this->table_name = "accesslogs";
    }

    public function getAccessLogsForHost($host) {
        $query = "SELECT * FROM accesslogs WHERE host='$host'";
        $result = $this->conn->query($query);

        if (!$result) {
            throw new \Exception($this->conn->error);
        }

        $logs = [];

        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $logs[] = $row;
        }

        return $logs;
    }

    public function addNewLog($log, $host) {
        $time_local = $log["timeLocal"];
        $time_parsed = LogLoader::readDateTime($time_local);
        $datetime = new DateTime($time_parsed["year"] . "-" .
                                 LogLoader::convertMonthToNumber($time_parsed["month"]) . "-" .
                                 $time_parsed["day"] . "T" . $time_parsed["time"] . $time_parsed["offset"]);

        $datetimeSQL = $datetime->format("Y-m-d H:i:s");

        $query = "INSERT INTO accesslogs(remoteAddr, remoteUser, timeLocal, request, status, bodyBytesSend, httpReferer, httpUserAgent, host)
                  VALUES ('{$log['remoteAddr']}', '{$log['remoteUser']}', '$datetimeSQL', '{$log['request']}', '{$log['status']}', {$log['bodyBytesSend']}, '{$log['httpReferer']}', '{$log['httpUserAgent']}', '$host')";

        $result = $this->conn->query($query);

        if (!$result) {
            throw new \Exception("Failed to add log {$this->conn->error}");

        }
    }


    public function getLastDate($host) {
        $query = "SELECT max(timeLocal) as timeLocal FROM accesslogs WHERE host='$host'";

        $result = $this->conn->query($query);

        if (!$result) {
            throw new \Exception($this->conn->error);
        }

        $datetime = $result->fetch_array(MYSQLI_ASSOC)["timeLocal"];

        if ($datetime == "") {
            return (new DateTime())->setTimestamp(0);
        } else {
            return new DateTime($datetime);
        }
    }

    public function addNewLogIfAfterLastDate($log, $host) {
        $last_date = $this->getLastDate($host);

        $time_local = $log["timeLocal"];
        $time_parsed = LogLoader::readDateTime($time_local);
        $datetime = new DateTime($time_parsed["year"] . "-" .
            LogLoader::convertMonthToNumber($time_parsed["month"]) . "-" .
            $time_parsed["day"] . "T" . $time_parsed["time"]);

        if ($datetime > $last_date) {
            echo "datetime: " . $datetime->format("Y-m-d H:i:s") . "\n";
            echo "last_date: " . $last_date->format("Y-m-d H:i:s") . "\n\n\n\n\n\n";
            $datetimeSQL = $datetime->format("Y-m-d H:i:s");

            $query = "INSERT INTO accesslogs(remoteAddr, remoteUser, timeLocal, request, status, bodyBytesSend, httpReferer, httpUserAgent, host)
                  VALUES ('{$log['remoteAddr']}', '{$log['remoteUser']}', '$datetimeSQL', '{$log['request']}', '{$log['status']}', {$log['bodyBytesSend']}, '{$log['httpReferer']}', '{$log['httpUserAgent']}', '$host')";

            $result = $this->conn->query($query);

            if (!$result) {
                throw new \Exception("Failed to add log {$this->conn->error}");

            }
        }
    }

    public function getNumberOfLogsForHost($host) {
        $query = "SELECT count(host) as count FROM accesslogs WHERE host='$host'";
        $result = $this->conn->query($query);

        if (!$result) {
            throw new \Exception($this->conn->error);
        }

        return $result->fetch_array(MYSQLI_ASSOC)["count"];
    }

    public function getLogsForPage($host, $page_number, $page_size) {
        $logs = $this->getAccessLogsForHost($host);

        $start_index = $page_size * ($page_number - 1);
        $end_index = $start_index + $page_size - 1;

        if(count($logs) < $start_index + 1) {
            throw new \Exception("Page does not exist");
        }

        $result = [];

        while ($start_index < count($logs) && $start_index <= $end_index) {
            $result[] = $logs[$start_index];
            $start_index++;
        }

        return $result;
    }
}