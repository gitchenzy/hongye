<?php
// 本类由系统自动生成，仅供测试用途
namespace Home\Controller;
use Think\Controller;
class CenterController extends Controller {
    private $user_id;
    protected function _initialize(){
        $user = get_user_info();

        if (!$user) {
            $this->redirect("Login/index");
        }

        $this -> user_id = $user['user_id'];
    }

    public function index(){

        session('index',5);

        $this -> display();
    }
    //我的订单
    public function orders(){
        $where['status'] = ['GT',1];
        $where['user_id'] = $this -> user_id;
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
    //加载更多的订单
    public function loadOrders(){

        $where['status'] = ['GT',1];
        $where['user_id'] = $this -> user_id;
        $count = I('time');
        $info = M('orders') -> where($where) -> order('pay_time desc') ->limit(5*$count,5*($count+1)) -> select();

        if($info){
            foreach($info as &$v){
                $v['pay_time'] = date('Y-m-d',$v['pay_time']);

                $data = M('project') -> where(['id'=>$v['project_id']]) -> find();
                $v['status_name'] = getProjectStatus($data['status']);
                $v['project_name'] = $data['title'];
                $v['start_time'] = date('Y-m-d',$data['start_time']);
            }
            $this -> assign('info',$info);
            $this->success($this->fetch(),"",true);
        }else{
            $this -> error('没有更多数据！');
        }

    }
    //我的钱包
    public function cash(){

        //查询余额
        $where['id'] = $this -> user_id;

       //send_sms('2356','15060880529');

        $balance = M('users') -> where($where) -> getfield('balance');
        $this -> assign('balance',$balance);
        $this -> display();
    }
    //交易明细
    public function notes(){
        $type = I('type')?I('type'):2;
      //  echo $type;
        $where['pay_type'] = $type;
        $where['user_id'] = $this -> user_id;
        $info = M('user_pay') -> where($where)-> order('pay_time desc') ->limit(0,5) -> select();
        $this -> assign('info',$info);
        $this -> assign('type',$type);
        $this -> display();
    }
    public function loadNotes(){

        $type = I('type')?I('type'):2;
        $where['pay_type'] = $type;
        $where['user_id'] = $this -> user_id;
        $count = I('time');
        $info = M('user_pay') -> where($where) -> order('pay_time desc') ->limit(5*$count,5*($count+1)) -> select();

        if($info){
            $this -> assign('info',$info);
            $this->success($this->fetch(),"",true);
        }else{
            $this -> error('没有更多数据！');
        }

    }

}