<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 27.06.18
 * Time: 12:48
 */

namespace App\Controllers;

use \Utils\LogLoader;
use \App\Models\Host;
use \Core\BaseView;

class Monitoring extends \Core\BaseController
{
    protected $login_required = true;

    public function monitorAction() {
        $hosts_model = new Host();

        $hosts = $hosts_model->getHostsForUser($this->user);

        ob_start(); // Turn on buffering

        BaseView::render("Monitoring/monitoring.php", [
            "hosts" => $hosts
        ]); // Get file contents

        $content = ob_get_contents(); // Get buffered contents

        ob_end_clean(); // Turn off buffering

        $view_styles = "App/Views/Monitoring/Styles/monitoring.css";

        BaseView::render("Header/header.php",
            [
                "user" => $this->user,
                "content" => $content,
                "view_styles" => $view_styles
        ]);
    }

    public function updateMonitoringAction() {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            if (isset($this->params["server"])) {
                $host_model = new Host();
                $serverName = $this->params["server"];

                $hostData = $host_model->getHostByName($serverName);

                $monitoringData = LogLoader::getTotalUtilization($hostData["server"],
                    $hostData["user"],
                    $hostData["password"],
                    $hostData["adapter"],
                    $hostData["disk"]);

                echo json_encode($monitoringData);
            } else {
                throw new \Exception("Invalid url data");
            }
        } else {
            throw new \Exception("Invalid method type");
        }
    }
}