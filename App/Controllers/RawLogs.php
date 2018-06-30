<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 28.06.18
 * Time: 23:25
 */

namespace App\Controllers;

use App\Config;
use App\Models\AccessLog;
use \App\Models\Host;
use \Core\BaseView;
use phpseclib\Net\SSH1;
use phpseclib\Net\SSH2;
use Utils\LogLoader;

class RawLogs extends \Core\BaseController
{
    protected $login_required = true;
    const LOG_PAGE_SIZE = 10;

    public function rawLogsAction() {
        if ($_SERVER["REQUEST_METHOD"] != "GET") {
            throw new \Exception("Invalid method type");
        }

        $hosts_model = new Host();
        $access_logs_model = new AccessLog();

        if (isset($_GET["server"])) {
            $initial_host = $_GET["server"];

            if (!$hosts_model->checkIfUserOwnsHost($initial_host, $this->user)) {
                throw new \Exception("You have no permission to access logs for this host");
            }
        }

        $hosts = $hosts_model->getHostsForUser($this->user);

        if (empty($hosts)) {
            $content = "Sorry you don't have any hosts listed to your account";
        } else {
            if (!isset($initial_host)) {
                $initial_host = $hosts[0]["server"];
            }

            $number_of_logs = $access_logs_model->getNumberOfLogsForHost($initial_host);
            $number_of_pages = ceil($number_of_logs / self::LOG_PAGE_SIZE);

            $page = [];

            if ($number_of_logs > 0) {
                $page = $access_logs_model->getLogsForPage($initial_host, 1, self::LOG_PAGE_SIZE);
            }

            ob_start(); // Turn on buffering

            BaseView::render("RawLogs/rawLogs.php", [
                "number_of_pages" => $number_of_pages,
                "number_of_logs" => $number_of_logs,
                "page_size" => self::LOG_PAGE_SIZE,
                "initial_host" => $initial_host,
                "hosts" => $hosts,
                "page" => $page
            ]); // Get file contents

            $content = ob_get_contents(); // Get buffered contents

            ob_end_clean(); // Turn off buffering
        }

        $view_styles = "App/Views/RawLogs/Styles/rawLogs.css";

        BaseView::render("Header/header.php",
            [
                "user" => $this->user,
                "content" => $content,
                "view_styles" => $view_styles
        ]);
    }

    public function rawLogsPageAction() {
        if ($_SERVER["REQUEST_METHOD"] != "GET") {
            throw new \Exception("Invalid method type");
        }

        $server = $this->params["server"];
        $page = $this->params["page"];

        $hosts_model = new Host();
        $access_logs_model = new AccessLog();

        if(!$hosts_model->checkIfUserOwnsHost($server, $this->user)) {
            throw new \Exception("You have no permission to access logs for this host");
        }

        $number_of_logs = $access_logs_model->getNumberOfLogsForHost($server);
        $number_of_pages = ceil($number_of_logs / self::LOG_PAGE_SIZE);

        if ($page > $number_of_pages) {
            throw new \Exception("There is no such page");
        }

        echo json_encode($access_logs_model->getLogsForPage($server, $page, self::LOG_PAGE_SIZE));
    }

    public function rawLogsRefreshAction() {
        if ($_SERVER["REQUEST_METHOD"] != "GET") {
            throw new \Exception("Invalid method type");
        }

        $server = $this->params["server"];

        $hosts_model = new Host();
        $access_logs_model = new AccessLog();

        if (!$hosts_model->checkIfUserOwnsHost($server, $this->user)) {
            throw new \Exception("You have no permission to access logs for this host");
        }

        $access_log_path = CONFIG::ACCESS_LOGS_PATH;
        $error_log_path = CONFIG::ERROR_LOGS_PATH;

        /* open ssh connection to server */
        $hosts = new Host();
        $host_info = $hosts->getHostByName($server);

        $ssh = new SSH2($server);

        if (!$ssh->login($host_info["user"], $host_info["password"])) {
            throw new \Exception("Error connecting to server");
        }

        LogLoader::loadLogs($ssh, $server, [$access_log_path, $error_log_path]);

        $access_logs = LogLoader::readAccessLog(Config::SERVER_LOGS_PATH . "/$server/access.log");

        foreach($access_logs as $access_log) {
            $access_logs_model->addNewLogIfAfterLastDate($access_log, $server);
        }

        header("Location: http://localhost/raw?server=$server");
    }
}