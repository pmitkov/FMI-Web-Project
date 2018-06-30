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
use Utils\StringParser;
use \DateTime;

class AccessLog extends BaseModel
{
    public function __construct() {
        parent::__construct();
        $this->table_name = "accesslogs";
    }

    public function getAccessLogsForHost($host) {
        $query = "SELECT * FROM accesslogs 
                  WHERE host='$host'";
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
        $query = "SELECT max(timeLocal) AS timeLocal 
                  FROM accesslogs 
                  WHERE host='$host'";

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
            $datetimeSQL = $datetime->format("Y-m-d H:i:s");

            $query = "INSERT INTO accesslogs(remoteAddr, remoteUser, timeLocal, request, status, bodyBytesSend, httpReferer, httpUserAgent, host)
                      VALUES ('{$log['remoteAddr']}', '{$log['remoteUser']}', '$datetimeSQL', \"{$log['request']}\", '{$log['status']}', {$log['bodyBytesSend']}, '{$log['httpReferer']}', '{$log['httpUserAgent']}', '$host')";

            $result = $this->conn->query($query);

            if (!$result) {
                throw new \Exception("Failed to add log {$this->conn->error}");

            }
        }
    }

    public function getNumberOfLogsForHost($host) {
        $query = "SELECT COUNT(host) AS count 
                  FROM accesslogs 
                  WHERE host='$host'";
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

        if (count($logs) < $start_index + 1) {
            throw new \Exception("Page does not exist");
        }

        $result = [];

        while ($start_index < count($logs) && $start_index <= $end_index) {
            $result[] = $logs[$start_index];
            $start_index++;
        }

        return $result;
    }

    public function getStatusSummaryForHost($host) {
        $query = "SELECT COUNT(*) AS count, status 
                  FROM accesslogs 
                  WHERE host='$host' 
                  GROUP BY status";
        $result = $this->conn->query($query);

        if (!$result) {
            throw new \Exception($this->conn->error);
        }

        $summary = [];

        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $summary[] = $row;
        }

        return $summary;
    }

    public function getDayOfWeekSummaryForHost($host) {
        $query = "SELECT COUNT(*) AS count, DAYNAME(timeLocal) AS dayname 
                  FROM accesslogs WHERE host='$host' 
                  GROUP BY DAYNAME(timeLocal)";
        $result = $this->conn->query($query);

        if (!$result) {
            throw new \Exception($this->conn->error);
        }

        $summary = [];

        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $summary[] = $row;
        }

        return $summary;
    }

    public function getHourSummaryForHost($host) {
        $query = "SELECT COUNT(*) AS count, HOUR(timeLocal) AS hour
                  FROM accesslogs
                  WHERE host='$host'
                  GROUP BY HOUR(timeLocal)";
        $result = $this->conn->query($query);

        if (!$result) {
            throw new \Exception($this->conn->error);
        }

        $summary = [];

        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $summary[] = $row;
        }

        return $summary;
    }

    public function getMonthSummaryForHost($host) {
        $query = "SELECT COUNT(*) AS count, MONTHNAME(timeLocal) AS month
                  FROM accesslogs
                  WHERE host='$host'
                  GROUP BY MONTHNAME(timeLocal)";
        $result = $this->conn->query($query);

        if (!$result) {
            throw new \Exception($this->conn->error);
        }

        $summary = [];

        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $summary[] = $row;
        }

        return $summary;
    }

    public function getServedFilesSummaryForHost($host) {
        $query = "SELECT COUNT(*) AS count, SUBSTRING_INDEX(SUBSTRING_INDEX(request, ' ', 2), ' ', -1) AS file
                  FROM accesslogs
                  WHERE host='$host' AND (LENGTH(request) - LENGTH(REPLACE(request, ' ', ''))) > 1
                  GROUP BY SUBSTRING_INDEX(SUBSTRING_INDEX(request, ' ', 2), ' ', -1)";
        $result = $this->conn->query($query);

        if (!$result) {
            throw new \Exception($this->conn->error);
        }

        $summary = [];

        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $summary[] = $row;
        }

        return $summary;
    }

    public function getRequestVerbSummaryForHost($host) {
        $query = "SELECT COUNT(*) AS count, SUBSTRING_INDEX(request, ' ', 1) AS verb 
                  FROM accesslogs 
                  WHERE SUBSTRING_INDEX(request, ' ', 1) IN ('GET', 'HEAD', 'POST', 'PUT', 'DELETE', 'CONNECT', 'OPTIONS', 'TRACE', 'PATCH') AND host='$host'
                  GROUP BY SUBSTRING_INDEX(request, ' ', 1)";
        $result = $this->conn->query($query);

        if (!$result) {
            throw new \Exception($this->conn->error);
        }

        $summary = [];

        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $summary[] = $row;
        }

        return $summary;
    }
}