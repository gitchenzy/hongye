<?php
// 本类由系统自动生成，仅供测试用途
namespace Hy\Controller;
use Think\Controller;
class CommonController extends Controller {

    protected function _initialize(){

        $user = get_user_info();
        if($user){
            //登录的时候可以查查几条唯独记录
            $where['id'] = $user['user_id'];
            $login =  M('users') -> where($where) -> find();
           $this -> assign('login',$login);
        }

    }



}