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
        $user = get_user_info();
        //是否有关注
        if($user){
            $atten['user_id'] = $user['user_id'];
            $atten['project_id'] = $id;
            $res = M('user_attention')-> where($atten) -> find();
            if($res){
                $this -> assign('key',1);
            }
        }

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

        if($data['pid'] > 0){
            //获取pid的fid
            $fid = M('comments')->where(['id'=>$data['pid']]) -> getfield('fid');
            if($fid > 0){
                $data['fid'] = $fid;
            }else{
                $data['fid'] = $data['pid'];
            }
        }else{
            $data['fid'] = 0;
        }
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
    //关注取消项目
    public function atten(){
        $user = get_user_info();
        $data = I('post.');
        if(!$user){
            session('back_url','List/content?id='.$data['project_id']);
            $this -> error('1');
        }
        //等一1关注
        if($data['key'] ==1){
            $info['time'] = time();
            $info['user_id'] = $user['user_id'];
            $info['project_id'] = $data['project_id'];
            $pro_info = M('project') -> where(['id'=>$data['project_id']])-> field('title,des') -> find();
            $info['project_title'] = $pro_info['title'];
            $info['project_des'] = $pro_info['des'];
            $res = M('user_attention')->add($info);
            if($res){
                $this -> success('关注成功！');
            }else{
                $this -> error('关注失败！');
            }
        }else{
            $where['user_id'] = $user['user_id'];
            $where['project_id'] = $data['project_id'];
            $res = M('user_attention')-> where($where) -> delete();
            if($res){
                $this -> success('取消关注成功！');
            }else{
                $this -> error('取消关注失败！');
            }
        }
    }
    //添加新的项目

    public function news(){
        //没有登录 要先去登录
        $user = get_user_info();
        if(!$user){
            $this->redirect("Login/index");
        }
        $auto = M('users')->where(['id'=>$user['user_id']])->getfield('is_auto');
        if($auto != 1){
            $this->redirect("Center/verify");
        }
        //类别
        $info_type = M('project_type')->where(['pid'=>['GT',0]]) -> select();

        $this -> assign('info_type',$info_type);
        $this -> display();
    }
    //添加项目
    public function addProject(){
        $user = get_user_info();
        $data = I('post.');
        $data['user_id'] = $user['user_id'];
        $info = M('project_type') -> where(['id'=>$data['type_id']])-> field('pid,is_return') -> find();
        $data['father_type_id'] = $info['pid'];
        $data['is_return'] = $info['is_return'];
        $res = M('project') -> add($data);
        if($res){
            $url = U('List/payreturn',array('project_id'=>$res));
            $this -> success($url);
        }else{
            $this -> error('添加失败！');
        }
    }
    //项目回报
    public function payreturn(){

        $project_id = I('project_id');

        $info = M('project_return') -> where(['project_id'=>$project_id]) -> select();
        
        foreach($info as &$v){
            $v['rule'] = trim($v['rule'],';');
            $v['rule'] = explode(';',$v['rule']);
        }
        $this -> assign('info',$info);
        $this -> display();
    }
    //添加项目汇报
    public function addReturn(){
        $data = I('post.');
        $data['time'] = time();
        $user = get_user_info();
        $info = M('project_return') -> add($data);
        if($info){
            $this -> success('添加成功！');
        }else{
            $this -> error('添加失败！');
        }
    }
    //我要去支持
    public function getmonney(){
        $project_id = I('id');
        $info = M('project_return') -> where(['project_id'=>$project_id]) -> select();

        foreach($info as &$v){
            $v['rule'] = trim($v['rule'],';');
            $v['rule'] = explode(';',$v['rule']);
        }
        $this -> assign('info',$info);
        $this -> display();

    }

    public function pay(){
        $id = I('id');
        $user = get_user_info();
        if(!$user){
            session('back_url','List/pay?id='.$id);
            $this->redirect("Login/index");
        }
        //
        //查询出回报信息
        $info = M('project_return')-> field('title,amount') -> where(['id'=>$id]) -> find();
        $this -> assign('return',$info);
        $this -> display();
    }
    public function printf_info($data)
    {
        foreach($data as $key=>$value){
            echo "<font color='#00ff55;'>$key</font> : $value <br/>";
        }
    }
    public function notify(){
        echo 'chenggong';
    }
    public function orders(){
        //
        $return_id = I('return_id');
        $address_id = I('address_id');
        $address = M('address') -> where(['id'=>$address_id]) -> find();
        $return = M('project_return') -> where(['id'=>$return_id]) -> find();
        $orders['name'] = $address['name'];
        $orders['phone'] = $address['phone'];
        $orders['province'] = $address['province'];
        $orders['city'] = $address['city'];
        $orders['area'] = $address['area'];
        $orders['address'] = $address['address'];
        $orders['time'] = time();
        $orders['pay_amount'] = $return['amount'];
        $orders['order_no'] = get_pay_no();
        $user = get_user_info();
        $orders['user_id'] = $user['user_id'];
        $orders['project_id'] = $return['project_id'];
        $orders['return_id'] = $return['id'];
        $res = M('orders') -> add($orders);
        if($res){
            $this -> success($res,$orders['order_no']);
        }else{
            $this -> error('订单添加失败！');
        }
    }


}