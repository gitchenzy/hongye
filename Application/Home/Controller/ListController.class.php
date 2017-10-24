<?php
// 本类由系统自动生成，仅供测试用途
namespace Home\Controller;

class ListController extends CommonController {
    protected function _initialize(){
        parent::_initialize();
    }
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
        $project = M('project') -> where($where) -> order($order) ->limit(5*$count,5) -> select();
     //   dump($project);

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
        $project['num'] = $project['reach_amount']/$project['target_amount']*100;
        $project['num_1'] = 100-$project['reach_amount']/$project['target_amount']*100;
        $project['num_2'] = $project['reach_amount']/$project['target_amount']*100-10;
        $project['num_3'] = floor($project['reach_amount']/$project['target_amount']*100);
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
        //计算出这个项目还有多久
        $daojishi = $project['end_time'] - time();
        if($daojishi > 0){
            $d = 60*60*24;
            $h = 60*60;
            $s = 60;
            $day = floor($daojishi /$d);
            if($day > 0){
                $shijian['d'] = $day;
                $daojishi -= $day*60*60*24;

                $hour = floor($daojishi/$h);
                if($hour > 0){
                    $shijian['h'] = $hour;
                    $daojishi -= $hour*60*60;
                    $shijian['s'] = ceil($daojishi/$s);
                    if($daojishi < 1){
                        //项目结束
                        if($project['reach_amount']>=$project['target_amount']){
                            $status = 5;
                        }else{
                            $status = 4;
                        }
                        M('project')-> where($where)->save(['status'=>$status]);
                        $shijian['s'] = 0;
                    }
                }else{
                    $shijian['h'] = 0;
                }
            }else{
                $shijian['d'] = 0;
            }
        }else{
            $shijian['d'] = 0;
            $shijian['h'] = 0;
            $shijian['s'] = 0;
        }

        $project['count'] =  M('user_attention')-> where(['project_id'=>$id]) -> count();
        $project['comment_count'] =  M('comments')-> where(['project_id'=>$id]) -> count();
        $fenx = M('project') -> where(['id'=>$id]) -> find();
        $fenx['name'] = $info['nick_name'];
        $fenx['pic'] = 'http://'.$_SERVER['SERVER_NAME'].$fenx['pic'];
        Vendor("Wifisoft.Jssdk");
        $appid = C('WX_APP_ID');
        $APPSECRET = C('WX_APP_SECRET');
        //echo U('List/content',array('id'=>$fenx['id']));
        $jssdk = new \Jssdk($appid, $APPSECRET);
        $data = $jssdk->GetSignPackage();
      //  dump($data);
        $this -> assign('data',$data);
        $this -> assign('fenx',$fenx);
        $this -> assign('info',$project);
        $this -> assign('shijian',$shijian);
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
           // dump($v['son']);
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
        $auto = M('user_author')->where(['user_id'=>$user['user_id']])->find();
        if(!$auto){
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
        $data['time'] = time();
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
            //
            if($v['amount'] > 0){
                $v['count'] = M('orders') -> where(['return_id'=>$v['id'],'status'=>['GT','1']]) -> count();
            }else{
                $v['count'] = M('orders') -> where(['return_id'=>$v['id']]) -> count();
            }

        }

        $count = M('orders') -> where(['return_id'=>0,'project_id'=>$project_id]) -> count();

        //查询分享内容
        $fenx = M('project') -> where(['id'=>$project_id]) -> find();
        $fenx['pic'] = 'http://'.$_SERVER['SERVER_NAME'].$fenx['pic'];
        Vendor("Wifisoft.Jssdk");
        $appid = C('WX_APP_ID');
        $APPSECRET = C('WX_APP_SECRET');
        $jssdk = new \Jssdk($appid, $APPSECRET);
        $data = $jssdk->GetSignPackage();
        $this -> assign('info',$info);
        $this -> assign('count',$count);
        $this -> assign('data',$data);
        $this -> assign('fenx',$fenx);
        $this -> display();

    }
    //地址列表
    public function addr_before(){
        $user = get_user_info();
        $address = M('address') -> where(['user_id'=>$user['user_id']]) -> select();
        $this -> assign('address',$address);
        $this -> display();
    }
    //添加地址列表
    public function insert_address(){
        $this -> display('addr');

    }
    public function addaddr(){
        $data = I('post.');
        $user = get_user_info();
        $data['user_id'] = $user['user_id'];
        if($data['is_default'] == 2){
            $info = M('address') -> where(['user_id'=>$user['user_id'],'is_default'=>2]) -> find();
            M('address') -> where(['id'=>$info['id']]) -> save(['is_default'=>1]);
        }
        $aid = $data['aid'];
        unset($data['aid']);
        if($aid > 0){
            $address = M('address') -> where(['id'=>$aid]) ->save($data);
        }else{
            $count = M('address') -> where(['user_id'=>$user['user_id']]) -> count();
            if($count > 20){
                $this -> error('每个人最多添加20条地址！');
            }
            $address = M('address') -> add($data);
        }

        if($address){
            $this -> success($address);
        }else{
            $this -> error('添加失败！');
        }
    }
    public function editaddr(){
        $aid = I('aid');
        $address = M('address') -> where(['id'=>$aid]) -> find();
        $address['pca'] = $address['province']." ".$address['city'].' '.$address['area'];
        $this -> assign('address',$address);
        $this -> assign('aid',$aid);
        $this -> display('addr');
    }
    public function deladdr(){
        $aid = I('aid');
        $address = M('address') -> where(['id'=>$aid]) -> delete();
        if($address){
            $this -> success('删除成功！');
        }else{
            $this -> error('删除失败！');
        }
    }
    public function is_default(){
        $user = get_user_info();
        $aid = I('aid');
        $info = M('address') -> where(['user_id'=>$user['user_id'],'is_default'=>2]) -> find();
        if($info['id'] != $aid){
            M('address') -> where(['id'=>$info['id']]) -> save(['is_default'=>1]);
            $res = M('address') -> where(['id'=>$aid]) -> save(['is_default'=>2]);
            if($res){
                $this -> success('设置成功！');
            }else{
                $this -> error('设置失败！');
            }
        }else{
            echo 3;
        }


    }
    public function pay(){
        $id = I('id');
        $aid = I('aid');
        $user = get_user_info();
        if(!$user){
            session('back_url','List/pay?id='.$id);
            $this->redirect("Login/index");
        }

        //查询出回报信息
        $info = M('project_return')-> field('title,amount') -> where(['id'=>$id]) -> find();
        //查出这个会员的默认地址
        if($aid){
            $address = M('address') -> where(['id'=>$aid]) -> find();
        }else{
            $address = M('address') -> where(['user_id'=>$user['user_id'],'is_default'=>2]) -> find();
            $aid = $address['id'];
        }
        if($address){
           // $addressinfo = $address['province'].$address['city'].$address['area'].' '.$address['address'].' '.$address['name'].' '.$address['phone'];
            $addressinfo = $address['province'].$address['city'].$address['area'].' '.$address['address'];
        }else{
            $addressinfo = null;
        }
        //得到余额
        $amount = M('users') -> where(['id'=>$user['user_id']]) -> getfield('balance');
        $this -> assign('return',$info);
        $this -> assign('aid',$aid);
        $this -> assign('amount',$amount);
        $this -> assign('addressinfo',$addressinfo);
        $this -> display();
    }
    public function orders(){
        //
        $return_id = I('return_id');
        $address_id = I('address_id');
        $node = I('node');
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
        $orders['node'] = $node;
        $res = M('orders') -> add($orders);
        if($res){
            $this -> success($res,$orders['order_no']);
        }else{
            $this -> error('订单添加失败！');
        }
    }
    //无尝支持
    public function love_orders(){
        $return_id = I('return_id');
        $project_id = I('project_id');
        $user = get_user_info();
        if(!$user){
            session('back_url','List/getmonney?id='.$project_id);
            $this->error(1);
        }
        $orders['user_id'] = $user['user_id'];
        $orders['time'] = time();
        $orders['pay_amount'] = I('amount');
        $orders['order_no'] = get_pay_no();
        $orders['project_id'] = $project_id;
        $orders['return_id'] = 0;
        $res = M('orders') -> add($orders);
        if($res){
            $this -> success($res,$orders['order_no']);
        }else{
            $this -> error('订单添加失败！');
        }
    }
    //支付成功跳转的页面
    public function succ(){

        $id=I('id');
        $this -> assign('id',$id);
        $this -> display('succses');
    }
    //使用余额支付
    public function pay_amount(){
        M()->startTrans();
        $return_id = I('return_id');
        $address_id = I('address_id');
        $node = I('node');
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
        $orders['node'] = $node;
        $orders['status'] = 2;
        $res = M('orders') -> add($orders);
        //要对个人
        $user_info = M('users') -> where(['id' => $user['user_id']]) -> find();
        $u_data['balance'] = $user_info['balance'] - $orders['pay_amount'];
        $u_data['amount'] = $user_info['amount'] + $orders['pay_amount'];
        $u_res = M('users') -> where(['id' => $user['user_id']]) -> save($u_data);

        $pay_info['user_id'] = $user['user_id'];
        $pay_info['pay_no'] = $orders['order_no'];
        $pay_info['amount'] = $orders['pay_amount'];
        $pay_info['pay_time'] = $orders['time'];
        $pay_info['pay_name'] = '付款';
        $p_res = M('user_pay') -> add($pay_info);

        //项目要达成目标 以及支持人数要改变

        $project_info = M('project') -> field('reach_amount,people_num') ->where(['id' => $orders['project_id']]) -> find();
        $p_data['reach_amount'] = $project_info['reach_amount'] + $orders['pay_amount'];
        $p_data['people_num'] = $project_info['people_num'] + 1;
        $pr_res = M('project') ->where(['id' => $orders['project_id']]) -> save($p_data);

        if($res && $u_res && $p_res && $pr_res){
            M()->commit();
            $this -> success('支付成功！');
        }else{
            M()->rollback();
            $this -> error('支付失败！');
        }
    }

}