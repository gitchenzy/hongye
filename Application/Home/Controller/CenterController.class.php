<?php
// 本类由系统自动生成，仅供测试用途
namespace Home\Controller;
use Think\Controller;
class CenterController extends Controller {

    protected function _initialize(){
        $user = get_user_info();

        if (!$user) {
            $this->redirect("Login/index");
        }


    }

    public function index(){

        session('index',5);

        $this -> display();
    }


}