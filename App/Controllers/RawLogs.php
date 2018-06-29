<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 28.06.18
 * Time: 23:25
 */

namespace App\Controllers;

use App\Models\AccessLog;
use \App\Models\Host;
use \Core\BaseView;

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
            $number_of_pages = intdiv($number_of_logs, self::LOG_PAGE_SIZE);

            $page = [];

            if ($number_of_logs > 0) {
                $page = $access_logs_model->getLogsForPage($initial_host, 1, self::LOG_PAGE_SIZE);
            }

            ob_start(); // Turn on buffering

            BaseView::render("RawLogs/rawLogs.php", [
                "number_of_pages" => $number_of_pages,
                "number_of_logs" => $number_of_logs,
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
}