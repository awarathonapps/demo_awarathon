<?php

//limit for IP to 100 requests per 5 min
function limitRequests($ip, $max_requests = 2, $sec = 60){
    $CI =& get_instance();

    $CI->load->driver('cache', array('adapter'=>'file'));

    $cache_key = $ip . "_key";
    $cache_remain_time = $ip . "_tmp";

    $current_time = date("Y-m-d H:i:s");

    //if first request
    if ($CI->cache->file->get($cache_key) === false){
        $current_time_plus = date("Y-m-d H:i:s", strtotime("+".$sec." seconds"));

        $CI->cache->file->save($cache_key, 1, $sec);
        $CI->cache->file->save($cache_remain_time, $current_time_plus, $sec * 2);
    }
    else{
        $requests = $CI->cache->file->get($cache_key);

        $time_lost = $CI->cache->file->get($cache_remain_time);

        if($current_time > $time_lost){
            //as first time request
            $current_time_plus = date("Y-m-d H:i:s", strtotime("+".$sec." seconds"));
            $CI->cache->file->save($cache_key, 1, $sec);
            $CI->cache->file->save($cache_remain_time, $current_time_plus, $sec * 2);
        }
        else{
            $CI->cache->file->save($cache_key, $requests + 1, $sec);
        }

        $requests = $CI->cache->file->get($cache_key);
        if($requests > $max_requests){
            header("HTTP/1.0 429 Too Many Requests");
            // exit;
            return "HTTP 429";
        }

    }

}

?>