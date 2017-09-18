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

function getProjectStatus($type) {
    $list = array(
        '1' => '待审核' ,
        '2' => '进行中' ,
        '3' => '审核不通过' ,
        '4' => '未完成' ,
        '5' => '众筹成功' ,
        '6' => '众筹完成' ,
        '7' => '项目结束' ,
    );
    return $list[$type];
}
function send_sms($code,$phone){

    Vendor('sendapi.TopSdk');
    $c = new \TopClient;
    $c->appkey = '23617189';
    $c->secretKey = '8ecdfa52b01ac953faf54f4c6c9d427c';
    $req = new AlibabaAliqinFcSmsNumSendRequest;
    $req->setExtend("123456");
    $req->setSmsType("normal");
    $req->setSmsFreeSignName("身份验证");
    $req->setSmsParam("{\"code\":\"$code\",\"product\":\"手机\"}");
    $req->setRecNum($phone);
    $req->setSmsTemplateCode("SMS_44435271");
    $resp = $c->execute($req);
    if($resp->result->success){
        return true;
    }else{
        return false;
    }


}
function get_string() {
    Vendor("Wifisoft.Strings");
    return new Strings;
}