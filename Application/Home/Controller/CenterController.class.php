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
    //我的订单
    public function orders(){
        $user = get_user_info();
        $where['status'] = ['GT',1];
        $where['user_id'] = $user['user_id'];
        $info = M('orders') -> where($where) -> order('pay_time desc') ->limit(0,5) -> select();

        foreach($info as &$v){
            $v['pay_time'] = date('Y-m-d',$v['pay_time']);

            $data = M('project') -> where(['id'=>$v['project_id']]) -> find();
            $v['status_name'] = getProjectStatus($data['status']);
            $v['project_name'] = $data['title'];
            $v['start_time'] = date('Y-m-d',$data['start_time']);
        }

        $this -> assign('info',$info);
        $this -> display();
    }

}