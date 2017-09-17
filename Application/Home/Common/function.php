<?php

function curl_request($url, $params = array(), $method = 'GET', $timeout = 15, $extheaders = null, $extOptions = null){
    if(!function_exists('curl_init')) exit('需要开启curl');

    $method = strtoupper($method);
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $timeout);
    curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_HEADER, false);
    switch($method)
    {
        case 'POST':
            curl_setopt($curl, CURLOPT_POST, TRUE);
            if(!empty($params))
            {
                curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
            }
            break;

        case 'DELETE':
        case 'GET':
            if($method == 'DELETE')
            {
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
            }
            if(!empty($params))
            {
                $url = $url . (strpos($url, '?') ? '&' : '?') . (is_array($params) ? http_build_query($params) : $params);
            }
            break;
    }
    curl_setopt($curl, CURLINFO_HEADER_OUT, TRUE);
    curl_setopt($curl, CURLOPT_URL, $url);
    if(!empty($extheaders))
    {
        curl_setopt($curl, CURLOPT_HTTPHEADER, (array)$extheaders);
    }
    if(!empty($extOptions)) {
        foreach($extOptions as $key => $value) curl_setopt($curl, $key, $value);
    }
    $response = curl_exec($curl);
    curl_close($curl);

    return $response;
}

function get_user_info() {
    $user = session("user");
    if (empty($user)){
        $user = false;
    }
    return $user;
}