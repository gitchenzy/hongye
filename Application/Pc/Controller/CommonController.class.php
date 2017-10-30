<?php
// 本类由系统自动生成，仅供测试用途
namespace Pc\Controller;
use Think\Controller;
class CommonController extends Controller {

    protected function _initialize(){

        $user = get_user_info();
        if($user){
            //登录的时候可以查查几条唯独记录
            $where['user_id'] = $user['user_id'];
            $where['status'] = 1;
            $info_num = M('infos') -> where($where) -> count();
            $this -> assign('infos_num',$info_num);
        }

    }


}