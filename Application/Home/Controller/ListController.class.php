<?php
// 本类由系统自动生成，仅供测试用途
namespace Home\Controller;
use Think\Controller;
class ListController extends Controller {
    public function index(){

        session('index',2);
        $where['del'] = 0;
        $where['status'] = ['GT',1];
        $type = I('type')?I('type'):'start';
        switch ($type){
            case 'start':
                $order = 'start_time desc';
            break;
            case 'hot':
                $where['is_hot'] = 1;
            break;
            case 'end':
                $order = 'end_time desc';
            break;
        }

        $info = M('project') -> where($where)-> order($order)-> limit(0,5) -> select();

        $this -> assign('info',$info);
        $this -> assign('type',$type);
        $this -> display();
    }
    //加载更多项目
    public function loadProject(){
        $type = I('type');
        $where['del'] = 0;
        $where['status'] = ['GT',1];
        switch ($type){
            case 'start':
                $order = 'start_time desc';
                break;
            case 'hot':
                $where['is_hot'] = 1;
                break;
            case 'end':
                $order = 'end_time desc';
                break;
        }
        $count = I('time');
        $project = M('project') -> where($where) -> order($order) ->limit(5*$count,5*($count+1)) -> select();

        if($project){

            $this -> assign('info',$project);
            $this->success($this->fetch(),"",true);
        }else{
            $this -> error('没有更多数据！');
        }
    }
    //项目详情

    public function content(){

        $id = I('id');
        $where['id'] = $id;
        $where['del'] = 0;

        $project = M('project') -> where($where) -> find();

        $info = M('users') -> where(['id'=>$project['user_id']]) -> find();
        $project['name'] = $info['nick_name'];
        $project['head_pic'] = $info['pic'];


        $this -> assign('info',$project);
        $this -> display();
    }
    //添加评价
    public function comments(){
        $user = get_user_info();
        $data = I('post.');
        if(!$user){
            session('back_url','List/content?id='.$data['project_id']);
            $this -> error('1');
        }
        $data['time'] = time();
        $data['user_id'] = $user['user_id'];
        $rews = M('comments') -> add($data);
        if($rews){
            $this -> success('评价成功！');
        }else{
            $this -> error('评价失败！');
        }
    }

    //筹款动态
    public function active(){

        $project_id = I('id');
        $where['project_id'] = $project_id;
        $where['pid'] = 0;

        $info = M('comments') -> where($where) -> order('time desc') -> limit(0,10) -> select();
        //查出地下都有多少回复
        foreach ($info as &$v){
            $v['son'] = comments_info($v['id']);
            $v['pay_amount'] = M('orders') -> where(['user_id'=>$v['user_id'],'project_id'=>$v['project_id']]) -> getfield('pay_amount');
            $user_info = M('users') -> where(['id'=>$v['user_id']])-> field('nick_name,pic') -> find();
            $v['name'] = $user_info['nick_name'];
            $v['head_pic'] = $user_info['pic'];
        }
        $this -> assign('info',$info);
        $this -> display();
    }



}