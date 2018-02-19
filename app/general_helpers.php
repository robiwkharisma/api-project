<?php
use Carbon\Carbon;

if (! function_exists('user_info')) {
    /**
     * Get logged user info.
     *
     * @param  string $column
     * @return mixed
     */
    function user_info($column = null)
    {
        if ($user = JWTAuth::toUser(Request::input('token'))) {
            if (is_null($column)) {
                return $user;
            }

            if ('role' == $column) {
                $role = user_info()->roles->toArray();

                foreach ($role as $value) {
                    $slug[] = $value['slug'];
                }

                return $slug;
            }

            return $user->{$column};
        } else {

        }

        return null;
    }
}

if (! function_exists('createdirYmd')) {
    function createdirYmd($path="")
    {
            if(!file_exists($path."/".date("Y"))) {
                mkdir($path."/".date("Y"), 0777,true);
                chmod($path."/".date("Y"), 0777);
            }
            if(!file_exists($path."/".date("Y")."/".date("m"))) {
                mkdir($path."/".date("Y")."/".date("m"), 0777,true);
                chmod($path."/".date("Y")."/".date("m"), 0777);
            }
            if(!file_exists($path."/".date("Y")."/".date("m")."/".date("d"))) {
                mkdir($path."/".date("Y")."/".date("m")."/".date("d"), 0777,TRUE);
                chmod($path."/".date("Y")."/".date("m")."/".date("d"), 0777);
            }
    }
}

function rmdirr($dirname)
{
    // Sanity check
    if (!file_exists($dirname)) {
        return false;
    }

    // Simple delete for a file
    if (is_file($dirname) || is_link($dirname)) {
        return unlink($dirname);
    }

    // Create and iterate stack
    $stack = array($dirname);
    while ($entry = array_pop($stack)) {
        // Watch for symlinks
        if (is_link($entry)) {
            unlink($entry);
            continue;
        }

        // Attempt to remove the directory
        if (@rmdir($entry)) {
            continue;
        }

        // Otherwise add it to the stack
        $stack[] = $entry;
        $dh = opendir($entry);
        while (false !== $child = readdir($dh)) {
            // Ignore pointers
            if ($child === '.' || $child === '..') {
                continue;
            }

            // Unlink files and add directories to stack
            $child = $entry . DIRECTORY_SEPARATOR . $child;
            if (is_dir($child) && !is_link($child)) {
                $stack[] = $child;
            } else {
                unlink($child);
            }
        }
        closedir($dh);
        // print_r($stack);
    }

    return true;
}

if (!function_exists('generate_password'))
{
    function generate_password($password = null, $length = 8, $addSpecialChars = false)
    {
        $pass = $password;

        if (empty($pass))
        {
            $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
            $specialChars = "@*%&!-_";
            $pass = array();
            $alphaLength = strlen($alphabet) - 1;
            $specialCharsLength = strlen($specialChars) - 1;
            for ($i = 0; $i < $length; $i++) {
                $n = rand(0, $alphaLength);
                $pass[] = $alphabet[$n];
            }

            if ($addSpecialChars) {
                $n = rand(0, $specialCharsLength);
                $pass[] = $specialChars[$n];
            }
            $pass = implode($pass);
        }
        else
        {
            $pass = $password;
        }

        return $pass; //turn the array into a string
    }
}

define('quotes',"'");

if (!function_exists('swift_validate'))
{
    function swift_validate($swift)
    {

        if(!preg_match("/^[a-z]{6}[0-9a-z]{2}([0-9a-z]{3})?\z/i", $swift))
        {
            return false;
        }
        else
        {
            return true;
        }
    }
}

if(!function_exists('siren_validate'))
{
    function siren_validate($siren)
    {
        $siren = str_replace(' ', '', $siren);
        $reg   = "/^(\d)(\d)(\d)(\d)(\d)(\d)(\d)(\d)(\d)$/";
        if (!preg_match($reg, $siren, $match)) {
            return false;
        }
        $match[2] *= 2;
        $match[4] *= 2;
        $match[6] *= 2;
        $match[8] *= 2;
        $sum       = 0;
        for ($i = 1; $i < count($match); $i++) {
            if ($match[$i] > 9) {
                $a         = (int) substr($match[$i], 0, 1);
                $b         = (int) substr($match[$i], 1, 1);
                $match[$i] = $a + $b;
            }
            $sum += $match[$i];
        }
        return (($sum % 10) == 0);
    }
}

if (! function_exists('eform_date')) {
    /**
     * Generate new datetime from configured format datetime.
     *
     * @param  string $datetime
     * @return string
     */
    function eform_date($datetime, $code = NULL)
    {
        switch ($code) {
            case 'date':
                $date = date(env('APP_DATE_FORMAT', 'd'), strtotime($datetime));
                break;
            case 'month':
                $date = date(env('APP_DATE_FORMAT', 'M'), strtotime($datetime));
                break;
            case 'year':
                $date = date(env('APP_DATE_FORMAT', 'Y'), strtotime($datetime));
                break;

            default:
                $date = date(env('APP_DATE_FORMAT', 'd M'), strtotime($datetime));
                break;
        }

        return $date;
    }
}

if (! function_exists('random_color')) {
    function random_color($id=0, $code=0) { 
        $first_part = substr(str_pad( md5( $id ), 3, '0', STR_PAD_LEFT), 0, 3);
        $last_part = substr(str_pad( md5( $code ), 3, '0', STR_PAD_LEFT), 0, 3);
        
        return '#'.$first_part.$last_part; 
    } 
}

if (! function_exists('has_duplicate')) {
    function has_duplicate($array){
        return count($array) != count(array_unique($array));
    }
}
