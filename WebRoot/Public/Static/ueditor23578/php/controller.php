<?php
//header('Access-Control-Allow-Origin: http://www.baidu.com'); //设置http://www.baidu.com允许跨域访问
//header('Access-Control-Allow-Headers: X-Requested-With,X_Requested_With'); //设置允许的跨域header
date_default_timezone_set("Asia/Chongqing");
error_reporting(E_ERROR);
header("Content-Type: text/html; charset=utf-8");

//系统需要在登录情况下使用文件上传功能
session_start();


$action = $_GET['action'];
//读取用户id
$userInfo = $_SESSION["GYCrm_"]["CurrentUser"];
$customerID = $userInfo['EmployeeID'];
$CONFIG = array();
if (empty($customerID)) {
    echo json_encode(array(
        'state'=> 'login to upload picture'
    ));
    exit;
}
else {
    $config_string = preg_replace("/\/\*[\s\S]+?\*\//", "", file_get_contents("config.json"));
    $config_string = str_replace('{user_id}', $customerID , $config_string);
    $CONFIG = json_decode($config_string, true);
}

switch ($action) {
    case 'config':
        $result =  json_encode($CONFIG);
        break;

    /* 上传图片 */
    case 'uploadimage':
    /* 上传涂鸦 */
    case 'uploadscrawl':
    /* 上传视频 */
    case 'uploadvideo':
    /* 上传文件 */
    case 'uploadfile':
        $result = include("action_upload.php");
        break;

    /* 列出图片 */
    case 'listimage':
        $result = include("action_list.php");
        break;
    /* 列出文件 */
    case 'listfile':
        $result = include("action_list.php");
        break;

    /* 抓取远程文件 */
    case 'catchimage':
        $result = include("action_crawler.php");
        break;

    default:
        $result = json_encode(array(
            'state'=> '请求地址出错'
        ));
        break;
}

/* 输出结果 */
if (isset($_GET["callback"])) {
    if (preg_match("/^[\w_]+$/", $_GET["callback"])) {
        echo htmlspecialchars($_GET["callback"]) . '(' . $result . ')';
    } else {
        echo json_encode(array(
            'state'=> 'callback参数不合法'
        ));
    }
} else {
    echo $result;
}