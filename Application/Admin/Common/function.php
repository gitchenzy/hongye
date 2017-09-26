<?php
/**
 * Created by PhpStorm.
 * User: Huge
 * Date: 2016/1/23 0023
 * Time: 9:59
 */

/**
 *获取当前登录的用户
 * @return int
 */
function get_user () {
    $user = session("current_user");
    if (empty($user)){
        $user = 0;
    }
    return $user;
}
/**
 *获取当前登录的用户类型
 * @return int
 */
function get_user_pic(){
    $user = session("CurrentUser");
    $pic = M("employee")->where(array("EmployeeID"=>$user["EmployeeID"]))->getField("Pic");
    if(!empty($pic)){
        $pic = thumbImage($pic,64,64,100,3);
    }else{
        $pic = '/Public/no_photo.png';
    }
    return $pic;
}
function get_user_type () {
    $user = session("CurrentUser");
    if (empty($user)){
        $user = 1;
    }
    return $user['LoginType'];
}
//返回登陆者的名字
function get_user_trueName () {
    $user = session("CurrentUser");
    if (empty($user)){
        $user = 1;
    }
    return $user['Name'];
}
//返回登陆者的名字
function get_user_typeName () {
    $type = get_user_type ();
    if ($type==1){
        return '内部员工';
    }else if($type==2){
        return  '代理商';
    }else if($type==3){
        return '客户';
    }
}
//判断是否是超级管理员admin
function get_user_name(){
    $user = session("CurrentUser");
    $type = get_user_type();
    if($type == 1){
        if($user['EmployeeNum'] == 'admin'){
            return true;
        }
    }
}
function get_user_id(){
    $usr = session("current_user");
    if(empty($usr["employeeID"])){
        $user = 0;
    }else{
        $user = $usr["employeeID"];
    }
    return $user;
}

function get_string() {
    Vendor("Wifisoft.Strings");
    return new Strings;
}
//获取该用户的所以权限组
function get_group(){
    $uid = get_user_id();
    $type = get_user_type();
    static $groups = array();
    if (isset($groups[$uid]))
        return $groups[$uid];
    $user_groups = M()
        ->table('employee_duty a')
        ->where("a.MemberID='$uid' and g.Status='1' and g.Type='$type'")
        ->join("duty g on a.DutyID=g.ID and g.Type = a.Type")
        ->field('MemberID,DutyID,DutyName,Rules')->select();
    $groups[$uid]=$user_groups?:array();
    return $groups[$uid];
}
//获取该用户下权限的菜单
function get_Menu(){
    $groups = get_group();
    $ids = array();//保存用户所属用户组设置的所有权限菜单id
    foreach ($groups as $g) {
        $ids = array_merge($ids, explode(',', trim($g['Rules'], ',')));
    }
    $id_sring = implode(',',$ids); //获取到用户组赋予的所有权限
    $map=array(
        'ID'=>array('in',$id_sring),
        'ShowType'=>0,
        'status'=>1,
    );
    $menu = M('menu')->where($map)-> order('Sort') ->select();
    return Employee($menu ,0);
}
/**
 *递归权限菜单的遍历
 */
function Employee($data, $id=0){
    $list = array();
    foreach($data as $v) {
        if($v['PID'] == $id) {
            $v['son'] = Employee($data, $v['ID']);
            if(empty($v['son'])) {
                unset($v['son']);
            }
            array_push($list, $v);
        }
    }
    return $list;
}
//用来判断是否有增删改的权限
function display_menu($sting){
    if(get_user_name()){
        return true;
    }
    $groups = get_group();
    $ids = array();//保存用户所属用户组设置的所有权限菜单id
    foreach ($groups as $g) {
        $ids = array_merge($ids, explode(',', trim($g['Rules'], ',')));
    }
    $id_sring = implode(',',$ids); //获取到用户组赋予的所有权限

    $map=array(
        'ID'=>array('in',$id_sring),
        'ShowType'=>2,
        'status'=>1,
    );
    $menu = M('menu')->where($map)->select();
    $list_array= array();
    foreach($menu as $v){
        $list_array[] = trimTring($v['Url']);
    }
   if(in_array($sting,$list_array)){
        return true;
   }else{
       return false;
    }
}
//删除空格
function trimTring($str){
    $qian=array(" ","　","\t","\n","\r");
    $hou=array("","","","","");
    return str_replace($qian,$hou,$str);
}


/**
 * 时间戳格式化
 * @param int $time
 * @return string 完整的时间显示
 * @author huajie <banhuajie@163.com>
 */
function time_format($time = NULL,$format='Y-m-d H:i:s'){
    $time = $time === NULL ? NOW_TIME : intval($time);
    return date($format, $time);
}
/**
 * @param null $time
 * @param string $format
 * @return bool|string
 */
function time_formats($time = NULL,$format='Y/m/d H:i'){
    if(empty($time)){
        return "";
    }else{
        $time = $time === NULL ? NOW_TIME : intval($time);
        return date($format, $time);
    }
}
function strToDate($str){
    if($str) return date("Y-m-d",strtotime($str));
}
/** 传入密码和一个随机数， 返回加过密的密码
 * @param $pwd
 * @param $rad
 */
function get_guoyuanPWD($pwd,$rad){
    return md5(md5($pwd).$rad);
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

/**
 * @param null $time
 * @param string $format
 * @return bool|string
 */
function date_formats_custom($time = NULL,$format='Y/m/d'){
    return time_formats($time, $format);
}
function get_excel_reader() {
    Vendor("phpexcel.PHPExcel.IOFactory");
    return PHPExcel_IOFactory::createReader('Excel5');
}
function get_excel_read(){
    Vendor("phpexcel.PHPExcel.IOFactory");
    return PHPExcel_IOFactory::createReader('Excel2007');
}
function get_excel_IOFactory(){
    Vendor("phpexcel.PHPExcel.IOFactory");
    return new PHPExcel_IOFactory();
}
function get_excel_readers() {
    Vendor("phpexcel.PHPExcel");
    return new PHPExcel();
}
function get_excel_dateFormat(){
    Vendor("phpexcel.PHPExcel");
    return new PHPExcel_Shared_Date;
}

/** html页面的下拉框
 * @param $val
 * @param string $function 要传进去的数据，传方法或数组. 1 可传一维数组.也可传二维数组.二维数组时只能两个字段,键在前面,值在后面.
 * 示例,也就是thinkphp方法里M()->field('字段1,字段2')->select()出来的数据. Array ( [0] => Array ( [id] => 1 [name] => php ) [1] => Array ( [id] => 2 [name] => java ))
 * @param string $input_name 元素name
 * @param string $input_id   元素的id
 * @param string $cssClass   元素的class
 * @param string $empty      没选择时，默认显示的空值
 * @return string  返回一个html select元素。
 */
function parse_dropdownlist($val, $function = '', $input_name="", $input_id = "" , $cssClass="",$empty="") {
    if (is_null($function)) {
        return "";
    }
    if (is_array($function)) {
        $list = $function;
    }
    else {
        $list = $function(0, true);
    }
    $html = "<select ";
    if (empty($input_id)) {
        $input_id = $input_name;
    }
    if ($input_id) {
        $html .= "id='$input_id' ";
    }
    if ($input_name) {
        $html .= "name='$input_name' ";
    }
    if ($cssClass) {
        $html .= "class='$cssClass' ";
    }
    $html .= ">";
    if($empty!=""){
        $html .="<option value=''>{$empty}</option>";
    }
    if(arrayLevel($list)==1&&is_array($list)){
        foreach($list as $key=>$tmp) {
            if ("$key" === "$val") {
                $html .= "<option selected='selected' value='$key'>";
            }
            else {
                $html .= "<option value='$key'>";
            }
            $html .= $tmp. "</option>";
        }
    }
    else if(arrayLevel($list)==2&&is_array($list)){
        foreach($list as $key=>$tmp) {
            $one=reset($tmp);
            $two=end($tmp);
            if ("$one" === "$val") {
                $html .= "<option selected='selected' value='$one'>";
            }
            else {
                $html .= "<option value='$one'>";
            }
            $html .= $two. "</option>";
        }
    }
    $html .= "</select>";
    return $html;
}
/**判断是几维的数组
 * @param $arr
 * @return int
 */
function arrayLevel($arr){
    $list=$arr;
    if(!is_array($list)){
        return 0;
    }
    else{
        $dimension = 0;
        foreach($list as $item1) {
            $t1=arrayLevel($item1);
            if($t1>$dimension){$dimension = $t1;}
        }
        return $dimension+1;
    }
}
/**
 *
 */
function get_new_array($arr,$key,$value){
    $new_array = array();
    foreach($arr as $val){
        $new_array[$val[$key]] = $val[$value];
    }
    return $new_array;
}

function parsePageImage($html) {
    $html_string = htmlspecialchars_decode($html);
    preg_match_all('#<img.*?src="([^"]*)"[^>]*>#i', $html_string , $match);
    if (!empty($match) && !empty($match[0])) {
        for($i = 0; $i < count($match[0]) ; $i++) {
            $old_url = $match[1][$i];
            $old_html = $match[0][$i];
            $width = getimagesize('http://'. $_SERVER['SERVER_NAME']. $old_url);
            //preg_match('#<img.*?width="([^"]*)"[^>]*>#i', $old_html, $match_width);
            //print_r($match_width) ."<br/>";

            if (intval($width[0]) > 930) {
                if (stripos($old_url, 'http') === 0) {
                    //echo 1;
                    $html_string = str_replace( $old_html, "<img src='$old_url' width='100%' />" , $html_string);
                }
                else {
                    //echo $old_url;
                    //$new_path = htmlImage($old_url , 640 , 3000 , 100 , 1);
                    //echo "<br/>H:" . $new_path ."H<br/>";
                    //echo $_SERVER['DOCUMENT_ROOT'] . $old_url ."<br/>";
                    //echo ROOT_PATH .$old_url."<br/>";
                    if(!file_exists(dirname(ROOT_PATH.$old_url))){
                        makeDir(dirname(ROOT_PATH.$old_url));
                    }
                    copy($_SERVER['DOCUMENT_ROOT'] . $old_url,ROOT_PATH . $old_url);
                    $new_path = thumbImage($old_url,930,3000,100,1);

                    $new_path = $new_path;
                    $html_string = str_replace( $old_html, "<img src='".__ROOT__ ."$new_path' width='100%' />" , $html_string);
                }
            }
        }
    }
    return $html_string;
}
//自动获取客户编号
function getCustomerIdentifier(){
    $res = M('customer') -> max('Identifier');
    return $res?++$res:350500000001;
}
//自动获取代理商编号
function getAgentIdentifier(){
    $str = date('Y');
    $where['Identifier'] = array('like',"%{$str}%");
    $res = M('agent') -> where($where) ->max('Identifier');
    //return $res;
    return $res?$res+1:date('Y').'00000001';
}

//获取缩略图
function thumbImage($filename, $width=100, $height=100, $quality = 100, $cut_type = 2,$c1=255,$c2=255,$c3=255,$hov=0 ) {
    /*
    echo "filename:".ROOT_PATH .'/'. $filename."<br/>";
    if(file_exists(ROOT_PATH .'/'. $filename) && is_file(ROOT_PATH .'/'. $filename)){
        echo "真<br/>";
    }else{
        echo "假<br/>";
    }
    if (!file_exists(ROOT_PATH .'/'. $filename) || !is_file(ROOT_PATH .'/'. $filename)) {
        return ;
    }
    echo "传进Image.<br/>";
    if (!file_exists(ROOT_PATH .'/'. $filename) || !is_file(ROOT_PATH .'/'. $filename)) {
        return ;
    }*/
    $info = pathinfo($filename);
    $extension = $info['extension'];
    $name = explode(".",$info['basename']);

    $old_image = '/'.$filename;
    if($hov){
        $new_image = '/uploads/temp/'.$name[0] . '-' . $width . 'x' . $height. '-2.' .$extension;
    }else{
        $new_image = '/uploads/temp/'.$name[0] . '-' . $width . 'x' . $height. '.' .$extension;
    }

    if (!file_exists(ROOT_PATH . $new_image) || (filemtime(ROOT_PATH . $old_image) > filemtime(ROOT_PATH . $new_image))) {
        $path = '';

        $directories = explode('/', dirname(str_replace('../', '', $new_image)));

        foreach ($directories as $directory) {
            $path = $path . '/' . $directory;

            if (!file_exists(ROOT_PATH . $path)) {
                @mkdir(ROOT_PATH . $path, 0777);
            }
        }
        /*Vendor('Wifisoft.image');
        $image = new Image(ROOT_PATH . $old_image);
        $image->resize($width, $height);
        $image->save(ROOT_PATH . $new_image, $quality);*/
        if (!file_exists(ROOT_PATH . $old_image)) {
            return $old_image;
        }
        $image = new \Think\Image();
        $image->open(ROOT_PATH . $old_image);
        $image->thumb($width, $height, $cut_type, $c1,$c2,$c3)->save(ROOT_PATH . $new_image);
    }
    return $new_image;
}
//获取缩略图
function htmlImage($filename, $width=100, $height=100, $quality = 100, $cut_type = 2) {
    $info = pathinfo($filename);
    $extension = $info['extension'];
    $name = explode(".",$info['basename']);
    $old_image = $filename;
    $new_image = '/uploads/tempImage/'.$name[0] . '-' . $width . 'x' . $height. '.' .$extension;
    if (!file_exists($new_image) || (filemtime($old_image) > filemtime($new_image))) {
        $path = '';
        $directories = explode('/', dirname(str_replace('../', '', $new_image)));
        foreach ($directories as $directory) {
            if($directory){
                $path = $path . '/' . $directory;
                if (!file_exists($path)) {
                    @mkdir( $path, 0777);
                }else{
                    //echo "<br/>========<br/>";
                }
            }
        }die();
        /*Vendor('Wifisoft.image');
        $image = new Image(ROOT_PATH . $old_image);
        $image->resize($width, $height);
        $image->save(ROOT_PATH . $new_image, $quality);*/
        if (!file_exists($old_image)) {
            return $old_image;
        }
        $image = new \Think\Image();
        $image->open($old_image);
        $image->thumb($width, $height, $cut_type)->save($new_image);
    }
    return $new_image;
}

function msubstr($str, $start=0, $length, $charset="utf-8", $suffix=true) {
    if(function_exists("mb_substr"))
        $slice = mb_substr($str, $start, $length, $charset);
    elseif(function_exists('iconv_substr')) {
        $slice = iconv_substr($str,$start,$length,$charset);
        if(false === $slice) {
            $slice = '';
        }
    }else{
        $re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        preg_match_all($re[$charset], $str, $match);
        $slice = join("",array_slice($match[0], $start, $length));
    }
    return $suffix ? $slice.'...' : $slice;
}

//发邮件
function sendEmail($to_email,$title,$content){
    if(!preg_match("/^[A-Z0-9a-zd]+([-_.][A-Z0-9a-zd]+)*@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-])+/",$to_email)){
        return false;
    }
    if(empty($title)||empty($content)){
        return false;
    }
    Vendor('PHPMailer.class#phpmailer');
    $mail = new PHPMailer;
    $mail->CharSet    = 'UTF-8'; //设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
    $mail->IsSMTP();  // 设定使用SMTP服务
    $mail->SMTPSecure = 'ssl';                 // 使用安全协议
    $mail->SMTPDebug  = 0;// 关闭SMTP调试功能
    $mail->SMTPAuth   = true;                  // 启用 SMTP 验证功能
    $mail->SMTPSecure = 'none';                 // 使用安全协议
    $mail->Host       = 'smtp.sina.com';  // SMTP 服务器
    $mail->Port       = 25;  // SMTP服务器的端口号
    $mail->Username   = 'guoyuan162@sina.com';// SMTP服务器用户名
    $mail->Password   = 'guoyuan162';  // SMTP服务器密码
    $mail->SetFrom('guoyuan162@sina.com', '果园CRM系统');
    $replyEmail       = 'guoyuan162@sina.com' ;
    $replyName        = '果园CRM系统';
    $mail->AddReplyTo($replyEmail, $replyName);
    $mail->Subject    = $title;
    $mail->MsgHTML($content);
    $mail->AddAddress($to_email);

    $res = $mail->Send();
   // dump($mail->ErrorInfo);
    if($res){
        return true;
    }else{
        return false;
    }
}


function kicsort($arr){
    $b = array_keys($arr);
    foreach($b as $z=>$y){
        $c[$y]=$y;
    }
    $c=array_change_key_case($c,CASE_LOWER);
    ksort($c);
    foreach($c as $x =>$y){
        $d[$y] = $arr[$y];
    }
    return $d;
}

//项目状态
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
//项目状态
function getProjectPayStatus($type) {
    $list = array(
        '1' => '未付' ,
        '2' => '未完成' ,
        '3' => '完成',
    );
    return $list[$type];
}

//订单状态
function getOrderStatus($type) {
    $list = array(
        '1' => '未付款' ,
        '2' => '已付款' ,
        '3' => '已发货',
        '4' => '已完成',
    );
    return $list[$type];
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










