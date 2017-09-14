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

            $typeName = 'employee';
            $typeID = 'employeeID';
            $name = 'employee_num';
            $data['employee_num']=I('LoginName');
            if(empty($data['employee_num'])){
                $this->error('账号输入错误!');
            }


            $password=I('Password');

            if(empty($password)){
                $this->error('密码输入错误!');
            }
            $data['del']=0;
            $user= M($typeName)->where($data)->find();

            $current_user['name'] = $user['name'];



            if($user){
                if($user['sstatus'] == 1){
                    $this->error('用户已被锁定，暂时无法登录!');
                }else{
                    if($user['password']!=get_guoyuanPWD($password,$user['random'])){
                        $this->error('密码错误!');
                    }else{
                        $current_user['employeen_num'] = $user[$name];
                        $current_user['name'] = $user['name'];
                        $current_user['position'] = $user['position'];
                        $current_user['pic'] = $user['pic'];
                        $current_user['employeeID'] = $user[$typeID];
                        session("current_user" , $current_user);//写入Session 登录成功
                        $this->success('登录成功', '/index.php/admin/');
                    }
                }
            }
            else{
                $this->error('无此用户!');
            }
        }
        else{
            if($this->dal->where(array("status"=>0,"del"=>0))->count() <= 0){
                $add["employee_num"] = "admin";
                $string = get_string();
                $add['random']= $string->rand_string(6,1);
                $add["password"] = md5(md5("123456").$add['Random']);
                $add['register_time'] = time_format();
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