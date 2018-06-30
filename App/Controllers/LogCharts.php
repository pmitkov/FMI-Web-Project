<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 30.06.18
 * Time: 12:46
 */

namespace App\Controllers;

use \Core\BaseView;
use \App\Models\Host;
use \App\Models\AccessLog;


class LogCharts extends \Core\BaseController
{
    protected $login_required = true;

    public function chartsAction() {
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
            $status_summary = $access_logs_model->getStatusSummaryForHost($initial_host);
            $day_of_week_summary = $access_logs_model->getDayOfWeekSummaryForHost($initial_host);
            $hour_summary = $access_logs_model->getHourSummaryForHost($initial_host);
            $month_summary = $access_logs_model->getMonthSummaryForHost($initial_host);
            $served_files_summary = $access_logs_model->getServedFilesSummaryForHost($initial_host);
            $request_verb_summary = $access_logs_model->getRequestVerbSummaryForHost($initial_host);

            ob_start(); // Turn on buffering

            BaseView::render("LogCharts/logCharts.php", [
                "number_of_logs" => $number_of_logs,
                "initial_host" => $initial_host,
                "hosts" => $hosts,
                "status_summary" => json_encode($status_summary),
                "day_of_week_summary" => json_encode($day_of_week_summary),
                "hour_summary" => json_encode($hour_summary),
                "month_summary" => json_encode($month_summary),
                "served_files_summary" => json_encode($served_files_summary),
                "request_verb_summary" => json_encode($request_verb_summary)
            ]); // Get file contents

            $content = ob_get_contents(); // Get buffered contents

            ob_end_clean(); // Turn off buffering
        }

        $view_styles = "App/Views/LogCharts/Styles/logCharts.css";

        BaseView::render("Header/header.php",
            [
                "user" => $this->user,
                "content" => $content,
                "view_styles" => $view_styles
       ]);
    }
}