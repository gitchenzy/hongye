<?php
/**
 * Created by PhpStorm.
 * User: Huge
 * Date: 2016/1/23 0023
 * Time: 10:42
 */
namespace Admin\Controller;

class AccountController extends AdminController {

    protected function _initialize(){
        $this->admin_authoried = false;
        $this->dal = M("employee");
        Vendor("Wifisoft/String");
    }

    public function index(){

    }

    //异步登录接口
    public function LoginAsyn() {
        $token = i('get.token');
        $token_model = M('accesstoken')->where(array('AccessToken' => $token , 'ExpiredTime' => array("gt" , time_format())))->find();
        if (empty($token_model)) {
            $this->error("钥匙无效", '/admin/account/login');
        }
        $customer = M('customer')->where(array('CustomerID' => $token_model['UserID'] , 'DelFlag' => 0 , 'Status' => 0))->find();
        if (empty($customer)) {
            $this->error("用户不存在或者已锁定", '/admin/account/login');
        }
        $current_user['Name'] = $customer['ShortName'];
        $current_user['EmployeeNum'] = $customer['LoginName'];
        $current_user['EmployeeID'] = $customer['CustomerID'];
        $current_user['LoginType'] = 3;
        session("CurrentUser" , $current_user);//写入Session 登录成功
        $this->success('登录成功', '/admin/');
    }

    public function Login(){
        if(IS_POST){
            $VerifyCode=I('VerifyCode');
            if($this->VerifyCodeIsRight($VerifyCode)==false){
                $this->error('验证码输入错误!');
            }
            $type = i('type');
            switch($type){
                case 1:
                    $typeName = 'employee';
                    $typeID = 'EmployeeID';
                    $name = 'EmployeeNum';
                    $data['EmployeeNum']=I('LoginName');
                    if(empty($data['EmployeeNum'])){
                        $this->error('账号输入错误!');
                    }
                break;
                //2为代理
                case 2:
                    $typeName = 'agent';
                    $data['LoginName']=I('LoginName');
                    $name = 'LoginName';
                    $typeID = 'AgentID';
                    if(empty($data['LoginName'])){
                        $this->error('账号输入错误!');
                    }
                break;
                case 3:
                    $typeName = 'customer';
                    $data['LoginName']=I('LoginName');
                    $name = 'LoginName';
                    $typeID = 'CustomerID';
                    if(empty($data['LoginName'])){
                        $this->error('账号输入错误!');
                    }
                break;
            }

            $Password=I('Password');
            if(empty($Password)){
                $this->error('密码输入错误!');
            }
            $data['FlagDel']=0;
            $user= M($typeName)->where($data)->find();
            if($type==1){
                $current_user['Name'] = $user['Name'];

            }else if($type==2){
                $current_user['Name'] = $user['CompanyName'];

            }elseif($type==3){
                $current_user['Name'] = $user['ShortName'];
            }

            if($user){
                if($user['Status'] == 1){
                    $this->error('用户已被锁定，暂时无法登录!');
                }else{
                    if($user['Password']!=get_guoyuanPWD($Password,$user['Random'])){
                        $this->error('密码错误!');
                    }else{
                        $current_user['EmployeeNum'] = $user[$name];
                        $current_user['EmployeeID'] = $user[$typeID];
                        $current_user['LoginType'] = $type;
                        session("CurrentUser" , $current_user);//写入Session 登录成功
                        $this->success('登录成功', '/admin/');
                    }
                }
            }
            else{
                $this->error('无此用户!');
            }
        }
        else{
            if($this->dal->where(array("Status"=>0,"DelFlag"=>0))->count() <= 0){
                $add["EmployeeNum"] = "admin";
                $string = get_string();
                $add['Random']= $string->rand_string(6,1);
                $add["Password"] = md5(md5("888888").$add['Random']);
                $add['RegisterTime'] = time_format();
                $add['Sort'] = 0;
                $add['OperatorID'] = "System Generation" ;
                $this->dal->add($add);
            }
            $this->display();
        }
    }

    public function logout() {
        session_destroy();
        redirect("/admin/account/Login");
    }

    /** 验证码是否正确.
     * @param $VerifyCode
     * @return bool
     */
    function VerifyCodeIsRight($VerifyCode){
        if($_SESSION['VerifyCode']==$VerifyCode){
            return true;
        }
        else
        {
            return false;
        }
    }
}