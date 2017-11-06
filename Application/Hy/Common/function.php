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
        '0' => '草稿中' ,
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
    $req->setSmsFreeSignName("厦门市红叶帮帮忙科技公司");
    $req->setSmsParam("{\"code\":\"$code\"}");
    $req->setRecNum($phone);
    $req->setSmsTemplateCode("SMS_105590022");
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
//评论儿子
function comments_info($pid=0){
    $where['fid'] = $pid;
    $info = M('comments') -> where($where)-> order('time asc') -> select();
    foreach ($info as &$in){
        $p_user = M('comments') -> where(['id'=>$in['pid']])  -> getfield('user_id');
        $in['fname'] = M('users') -> where(['id'=>$p_user]) -> getfield('nick_name');
        $in['name'] = M('users') -> where(['id'=>$in['user_id']]) -> getfield('nick_name');
        $in['head_pic'] = M('users') -> where(['id'=>$in['user_id']]) -> getfield('pic');
    }
    return $info;
}

/**
 * 连续创建目录
 *
 * @param string $dir 目录字符串
 * @param int $mode 权限数字
 * @return boolean
 */
function makeDir($dir, $mode = "0777") {
    if (!$dir) return false;
    if(!file_exists($dir)) {
        return mkdir($dir,$mode,true);
    } else {
        return true;
    }

}

function get_pay_no(){

    $time = strtotime(date('Y-m-d'));
    $res = M('user_account') -> where(['pay_time'=>['GT',$time]]) -> order('id desc') -> find();
    $order = M('orders') -> where(['time'=>['GT',$time]]) -> order('id desc') -> find();

    if($res || $order){
        if($res && !$order){
            $no = $res['pay_no'] + 1;
        }else if(!$res && $order){
            $no = $order['order_no'] + 1;
        }else{
            if($res['pay_no'] < $order['order_no']){
                $no = $order['order_no'] + 1;
            }else{
                $no = $res['pay_no'] + 1;
            }
        }
    }else{
        $no = date('Ymd').'00001';
    }
    return $no;
}