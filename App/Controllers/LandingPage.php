<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 25.06.18
 * Time: 11:15
 */

namespace App\Controllers;

use \Core\BaseView;

class LandingPage extends \Core\BaseController
{
    public function indexAction() {
        BaseView::render("Header/header.php", ["user" => $this->user]);
    }
}