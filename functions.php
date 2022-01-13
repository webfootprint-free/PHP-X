<?php

    function x_errors() {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
    }

    function x_die($var) {
        echo'<pre>';
        print_r($var);
        echo'</pre>';
        die();
    }

    function x_print($var) {
        echo'<pre>';
        print_r($var);
        echo'</pre>';
    }

    function x_log($var) {
        $var = json_encode($var);
        print "
            <script>
                // var debug_var = JSON.parse('".$var."');
                var debug_var = ".$var.";
                console.log (debug_var);
            </script>
        ";
    }

    function x_email($to, $var) {
        $var = json_encode($var);
        $subject = 'Debug Email';
        $message = $var;
        $headers = 'From: website@'.$_SERVER['HTTP_HOST'] . "\r\n" .
            'X-Mailer: PHP/' . phpversion();

        mail($to, $subject, $message, $headers);
    }


    function x_set(&$var) {
        if (!empty($var)) {
            return $var;
        }
        return '';
    }

    function x_truncate($string, $length, $dots = "...") {
	    return (strlen($string) > $length) ? substr($string, 0, $length - strlen($dots)) . $dots : $string;
	}

    function x_time_elapsed($datetime, $timestamp = true)
    {
        if (empty($timestamp)) {
            $ptime = $datetime;
        } else {
            $ptime = strtotime($datetime);
        }
        $etime = time() - $ptime;

        if ($etime < 1)
        {
            $timeago = '0 seconds';
        } else {
            $a = array( 12 * 30 * 24 * 60 * 60  =>  'year',
                        30 * 24 * 60 * 60       =>  'month',
                        24 * 60 * 60            =>  'day',
                        60 * 60                 =>  'hour',
                        60                      =>  'minute',
                        1                       =>  'second'
                        );

            foreach ($a as $secs => $str)
            {
                $d = $etime / $secs;
                if ($d >= 1)
                {
                    $r = round($d);
                    $timeago = $r . ' ' . $str . ($r > 1 ? 's' : '') . ' ago';
                    break;
                }
            }
        }

        $human_readable = human_date ($datetime);
        $output = '<a title="'.$human_readable.'">'.$timeago.'</a>';
        return $output;
    }

    function x_curl($url){
	    if (!function_exists('curl_init')){
	        die('Sorry cURL is not installed!');
	    }
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_HEADER, 0);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_FAILONERROR, false);
		curl_setopt($ch, CURLOPT_AUTOREFERER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
	    $output = curl_exec($ch);
	    curl_close($ch);
	    return $output;
	}

    function x_curl_cache($actual_url, $expires = null){
        global $x_override_curl_cache;

        if (empty($expires)) {
            $expires = 3600;
        }

        $hash = md5($cache_url);
        $filepath = dirname(__FILE__).'/assets/cached/';
        if (!is_dir($filepath)) {
            mkdir($filepath, 0777, true);
        }
        $filename = $filepath . $hash . '.cache';
        if(file_exists($filename)) {
          $changed = filemtime($filename);
        } else {
          $changed = 0;
        }
        $now = time();
        $diff = $now - $changed;
        if ( !$changed || ($diff > $expires) || !empty($x_override_curl_cache) || !empty($_GET['nocache'])) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $actual_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 60);
            $rawData = curl_exec($ch);
            curl_close($ch);
            if(!$rawData){
                if($changed){
                    $cache = unserialize(file_get_contents($filename));
                    return $cache;
                }else{
                    return false;
                }
            }
            $cache = fopen($filename, 'wb');
            $write = fwrite($cache, serialize($rawData));
            fclose($cache);
            chmod($filename, 0777);
            return $rawData;
        }
        $cache = unserialize(file_get_contents($filename));
        return $cache;
    }

 ?>
