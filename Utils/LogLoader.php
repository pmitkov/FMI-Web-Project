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
    public static function loadLogs($server, $user, $pass, $files) {
        $ssh = new SSH2($server);

        if (!$ssh->login($user, $pass)) {
            throw new \Exception("Failed to connect to server.");
        }

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
}