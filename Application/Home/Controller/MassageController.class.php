<?php
// 本类由系统自动生成，仅供测试用途
namespace Home\Controller;

class MassageController extends CommonController {
    public function index(){
        $this -> display();
    }
    public function massage(){
        session('index',4);
        $user = get_user_info();
        if(!$user){
            session('back_url','Massage/massage');
            $this -> assign('error','请先登录！');
        }
//        $type = I('type')?I('type'):1;
//        $where['type'] = $type;
        $where['user_id'] = $user['user_id'];
        $info = M('infos') -> where($where) -> order('time desc') ->select();
        $where['status'] = 1;
        M('infos') -> where($where) ->save(['status' => 2]);
        $this -> assign('info',$info);
        $this -> display();
    }
}