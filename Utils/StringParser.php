<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 25.06.18
 * Time: 21:34
 */

namespace Utils;

class StringParser
{
    public static function sanitizeString($string) {
        $string = strip_tags($string);
        $string = htmlentities($string);
        $string = stripslashes($string);

        return $string;
    }
}