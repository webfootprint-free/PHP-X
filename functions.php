<?php

    function x_show_errors() {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
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
