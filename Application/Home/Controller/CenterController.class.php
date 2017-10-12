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
        //查出个人信息
        $where['id'] = $this -> user_id;

        $info = M('users') -> where($where) -> find();
        //获取会员的等级
        $grade['grade'] = M('user_grade') -> where(['id'=>$info['grade_id']]) -> getfield('level');
        $grade['ungrade'] = 5 - $grade['grade'];
        $this -> assign('info',$info);
        $this -> assign('grade',$grade);
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
        $info = M('orders') -> where($where) -> order('pay_time desc') ->limit(5*$count,5) -> select();

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
        $info = M('user_pay') -> where($where) -> order('pay_time desc') ->limit(5*$count,5) -> select();

        if($info){
            $this -> assign('info',$info);
            $this->success($this->fetch(),"",true);
        }else{
            $this -> error('没有更多数据！');
        }
    }
    //绑定手机
    public function bind(){

        $this -> display();
    }
    //发送短信
    public function send_sms(){
        $phone = I('phone');
        $type = I('type');
        $string = get_string();
        $code= $string->rand_string(4,1);
        $res = send_sms($code,$phone);
        if($res){
            $info['code'] = $type.$code.$phone;
            $info['code_time'] = time();
            session('sms',$info);
            $this -> success('发送成功！');
        }else{
            $this -> error('验证码发送失败');
        }
    }
    //发送绑定手机
    public function sub_bind(){
        $phone = I('phone');
        $type = I('type');
        $code = I('code');
        $info = session('sms');
        if($type.$code.$phone != $info['code']){
            $this -> error('验证码错误！');
        }
        if(time() > $info['code_time']*5*60){
            $this -> error('验证码过期，请重新发送！');
        }
        //验证完了就开始修改手机号码 //线查询手机是否绑定
        $result = M('users') -> where(['phone'=>$phone,'del'=>0]) -> find();
        if($result){
            $this -> error('该手机已经绑定过！');
        }
        $where['id'] = $this -> user_id;
        $res = M('users') -> where($where) -> save(['phone'=>$phone]);
        if($res){
            $this -> success('手机绑定成功！');
        }else{
            $this -> error('手机绑定失败！');
        }
    }
    //我的关注
    public function likes(){
        $where['user_id'] = $this -> user_id;
        $res = M('user_attention') -> where($where) -> order('time desc') -> limit(0,5) ->select();
        foreach($res as &$v){
            $v['pic'] = M('project') -> where(['id'=>$v['project_id']]) -> getfield('pic');
            $v['time'] = date('Y-m-d',$v['time']);
        }
        $this -> assign('title','我的关注');
        $this -> assign('info',$res);
        $this -> display();
    }
    public function verify(){

        $active  = I('active')?I('active'):1;
        $this -> assign('active',$active);
        $this -> display();
    }
    //添加认证
    public function addverify(){

        $data = I('post.');

        $user = $this -> user_id;
        $where['user_id'] = $user;
        if($data['v'] == 1){
            //个人
            //先查询是否认证过了
            $sms = session('sms');
            if('verify'.$data['code'].$data['phone'] != $sms['code']){
                $this -> error('验证码错误！');
            }
            if(time() > $sms['code_time']*5*60){
                $this -> error('验证码过期，请重新发送！');
            }

            $where['types'] = $data['v'];
            $res = M('user_author')->where($where)->find();
            if($res){
                $this -> error('您已经提交过，请等待管理员审核！');
            }
            $info['add_time'] = time();
            $info['types'] = $data['v'];
            $info['name'] = $data['name'];
            $info['phone'] = $data['phone'];
            $info['pic'] = $data['pic'];
            $info['user_id'] = $user;

            $result = M('user_author') -> add($info);
            if($result){
                $this -> success('提交成功');
            }else{
                $this -> error('资料提交失败，请重新提交！');
            }
        }else{
            //机构
            $where['types'] = $data['v'];
            $res = M('user_author')->where($where)->find();
            if($res){
                $this -> error('您已经提交过，请等待管理员审核！');
            }
            $info['add_time'] = time();
            $info['types'] = $data['v'];
            $info['name'] = $data['names'];
            $info['phone'] = $data['phones'];
            $info['pic'] = $data['pics'];
            $info['address'] = $data['address'];
            $info['user_id'] = $user;
            $result = M('user_author') -> add($info);
            if($result){
                $this -> success('提交成功');
            }else{
                $this -> error('资料提交失败，请重新提交！');
            }
        }
    }
    //我的活动
    public function myactive(){

        $where['del'] = 0;
        $where['user_id'] = $this -> user_id;

        $info = M('project') -> field('status,title,time,pic') -> where($where) -> select();
        $this -> assign('info',$info);
        $this -> display();

    }
    //充值
    public function payin(){

        $this -> assign('user_id',$this->user_id);
        $this -> display();
    }


}