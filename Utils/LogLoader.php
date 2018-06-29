<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 25.06.18
 * Time: 22:01
 */

namespace Utils;

use \phpseclib\Net\SCP;
use \phpseclib\Net\SSH2;

class LogLoader
{
    public static function loadLogs($ssh, $server, $files) {
        $scp = new SCP($ssh);

        foreach ($files as $file) {
            $dir = dirname(dirname(__FILE__)) . "/logs/$server/";

            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }

            $base = basename($file);

           if (!$scp->get($file, "$dir/$base")) {
                throw new \Exception("Problem copying file");
           }
        }
    }

    public static function getNetworkUtilization($ssh, $adapter) {
        $input_traffic = $ssh->exec("ifstat -i $adapter -q 1 1 | sed -n '3p' | awk '{print $1}'");
        $output_traffic = $ssh->exec("ifstat -i $adapter -q 1 1 | sed -n '3p' | awk '{print $2}'");

        if ($input_traffic == "" || $output_traffic == "") {
            throw new \Exception("Error getting network utilization");
        }

        return [
            "input_traffic" => $input_traffic,
            "output_traffic" => $output_traffic
        ];
    }

    public static function getIOUtilization($ssh, $disk) {
        $read_IO = $ssh->exec("iostat -xtc | fgrep $disk | awk '{print $6}'");
        $write_IO = $ssh->exec("iostat -xtc | fgrep $disk | awk '{print $7}'");

        if ($read_IO == "" || $write_IO == "") {
            throw new \Exception("Error getting disk IO utilization");
        }

        return [
            "read_IO" => $read_IO,
            "write_IO" => $write_IO
        ];
    }

    public static function getCPUUtilization($ssh) {
        $cpu_usage = $ssh->exec("mpstat | sed -n '4p' | awk '{print $3}'");

        if ($cpu_usage == "") {
            throw new \Exception("Error getting CPU utilization");
        }

        return [
            "cpu_usage" => $cpu_usage
        ];
    }

    public static function getMemoryUtilization($ssh) {
        $memory_total = $ssh->exec("free -m | sed -n '2p' | awk '{print $2}'");
        $memory_used = $ssh->exec("free -m | sed -n '2p' | awk '{print $3}'");

        if ($memory_total == "" || $memory_used == "") {
            throw new \Exception("Error getting memory utilization");
        }

        return [
            "memory_total" => $memory_total,
            "memory_used" => $memory_used
        ];
    }

    public static function getTotalUtilization($server, $user, $password, $adapter, $disk) {
        $ssh = new SSH2($server);

        if (!$ssh->login($user, $password)) {
            throw new \Exception("Error connecting to server");
        }

        $network_utilization = self::getNetworkUtilization($ssh, $adapter);
        $disk_utilization = self::getIOUtilization($ssh, $disk);
        $cpu_utilization = self::getCPUUtilization($ssh);
        $memory_utilization = self::getMemoryUtilization($ssh);

        return [
            "network_utilization" => $network_utilization,
            "disk_utilization" => $disk_utilization,
            "cpu_utilization" => $cpu_utilization,
            "memory_utilization" => $memory_utilization
        ];
    }

    public static function readAccessLog($file_name) {
        $handle = fopen($file_name, "r");

        if (!$handle) {
            throw new \Exception("Failed to open access log file");
        }

        $result = [];

        while (($line = fgets($handle)) !== false) {
            $result[] = self::readAccessLogLine($line);
        }

        fclose($handle);

        return $result;
    }

    public static function readAccessLogLine($line) {
        $remote_addr = "";

        $index = 0;

        while ($line[$index] != " ") {
            $remote_addr = $remote_addr . $line[$index];
            $index++;
        }

        $remote_user = "";

        $index = $index + 3;

        while ($line[$index] != " ") {
            $remote_user = $remote_user . $line[$index];
            $index++;
        }

        $time_local = "";

        $index = $index + 2;

        while ($line[$index] != "]") {
            $time_local = $time_local . $line[$index];
            $index++;
        }

        $request = "";

        $index = $index + 3;

        while ($line[$index] != "\"") {
            $request = $request . $line[$index];
            $index++;
        }

        $status = "";

        $index = $index + 2;

        while ($line[$index] != " ") {
            $status = $status . $line[$index];
            $index++;

        }

        $body_bytes_send = "";

        $index = $index + 1;

        while ($line[$index] != " ") {
            $body_bytes_send = $body_bytes_send . $line[$index];
            $index++;
        }

        $http_referer = "";

        $index = $index + 2;

        while ($line[$index] != "\"") {
            $http_referer = $http_referer . $line[$index];
            $index++;
        }

        $http_user_agent = "";

        $index = $index + 3;

        while ($line[$index] != "\"") {
            $http_user_agent = $http_user_agent . $line[$index];
            $index++;
        }


        return [
            "remoteAddr" => $remote_addr,
            "remoteUser" => $remote_user,
            "timeLocal" => $time_local,
            "request" => $request,
            "status" => $status,
            "bodyBytesSend" => $body_bytes_send,
            "httpReferer" => $http_referer,
            "httpUserAgent" => $http_user_agent
        ];
    }

    public static function readDateTime($datetime) {
        $day = "";

        $index = 0;

        while ($datetime[$index] != "/") {
            $day = $day . $datetime[$index];
            $index++;
        }

        $month = "";

        $index = $index + 1;

        while ($datetime[$index] != "/") {
            $month = $month . $datetime[$index];
            $index++;
        }

        $year = "";

        $index = $index + 1;

        while ($datetime[$index] != ":") {
            $year = $year . $datetime[$index];
            $index++;
        }

        $time = "";

        $index = $index + 1;

        while ($datetime[$index] != " ") {
            $time = $time . $datetime[$index];
            $index++;
        }

        $offset = "";

        $index = $index + 1;

        for (; $index < strlen($datetime); $index++) {
            $offset = $offset . $datetime[$index];
        }

        return [
            "year" => $year,
            "month" => $month,
            "day" => $day,
            "time" => $time,
            "offset" => $offset,
        ];
    }

    public static function convertMonthToNumber($month) {
        $table = [
            "Jan" => "01",
            "Feb" => "02",
            "Mar" => "03",
            "Apr" => "04",
            "May" => "05",
            "Jun" => "06",
            "Jul" => "07",
            "Aug" => "08",
            "Sep" => "09",
            "Oct" => "10",
            "Nov" => "11",
            "Dec" => "12"
        ];

        return $table[$month];
    }
}