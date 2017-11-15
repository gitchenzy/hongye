<?php
/**
 * Created by PhpStorm.
 * User: Huge
 * Date: 2016/3/5 0005
 * Time: 14:00
 */

namespace Admin\Controller;


class ProjectController extends AdminController
{
    protected function _initialize(){
        parent::_initialize();


    }

    public function index(){
        $this->display();
    }
    public function loadProject(){
        // sort:id order:desc limit:10 offset:0
        $where['p.del'] = 0;
        //$page = i('page');
        //$rows = i('rows');
        $offset = i("offset");

        $limit = i("limit");
        $status = i("status");
        $search_key = i('search_key');
        if(!empty($status) && isset($status)){
            $where['p.status'] = $status;
        }
        $search_value = i('search');
        if($search_value){
            $where['p.title|u.nick_name'] = array('LIKE',"%$search_value%");;
        }
        $sort = i('sort');
        $order = i('order');
        if(!empty($sort) && $sort!='aid'){
            $reorder = 'p.'.$sort." ".$order;
        }else{
            $reorder = 'p.id desc';
        }
        $list =  M('project')-> alias('p')
            ->join('users as u on u.id = p.user_id','left')
            ->join('project_type as t on t.id = p.type_id','left')
            -> field('p.*,u.nick_name user_name,t.title type_name')
            -> where($where)
            -> order($reorder)
            ->limit($offset,$limit)
            -> select();
//        dump(M('project')->getlastsql());
        foreach($list as &$v){
            $v['is_hot'] = $v['is_hot']==1?'是':'否';
            $v['status_name'] = getProjectStatus($v['status']);
            $v['pay_status'] = getProjectPayStatus($v['payment_status']);
            $v['start_time'] = date('Y-m-d',$v['start_time']);
            $v['end_time'] =  date('Y-m-d',$v['end_time']);
        }
        //dump($list);
        $count =  M('project')-> alias('p')
            ->join('users as u on u.id = p.user_id','left')
            ->join('project_type as t on t.id = p.type_id','left')
            -> field('p.*,u.nick_name user_name,t.title type_name')
            -> where($where)
            -> order($reorder)
            -> count();
        $list_array= array("total"=>$count,"rows"=>$list?$list:array());
        echo json_encode($list_array);
    }
    //添加项目信息
    public function addProject(){
       if(IS_POST){
            $data = i('post.');
           $data['start_time'] = strtotime($data['start_time']);
           $data['end_time'] = strtotime($data['end_time']);
           $data['father_type_id'] = M('project_type')->where(['id'=> $data['type_id']]) -> getfield('pid');
           $data['is_return'] = M('project_type')->where(['id'=> $data['type_id']]) -> getfield('is_return');
           $data['time'] = time();
           $res = M('project') -> add($data);
            if($res){
                $this->success('添加成功！');
            }else{
                $this -> error('添加失败！');
            }
       }else{
           $res = M('project_type') -> where(['pid'=>['GT',0]]) ->getField("id,title");
           //  $arr =$this->dal_cs ->where($where)->order("Sort asc")->getField("CtmStatusID,CtmStatusName");
           $this->assign('menu',$res);
           //dump($res);
           $this -> display('editproject');
       }
    }
    //修改项目
    public function editProject(){
        $id = i('id');
        $where['id'] = $id;
        $info = M('project') -> where($where) -> find();
        if(IS_POST){
            $data = i('post.');
            if($data['pic'] != $info['pic']){
                $file = ROOT_PATH.$info['pic'];
                unlink($file);
            }
            if($info['status']==1 && $data['status']==2){
                $xiaoxi['type'] = 2;
                $xiaoxi['content'] = '您的项目已经审核通过了';
                $xiaoxi['time'] = time();
                $xiaoxi['user_id'] = $info['user_id'];
                M('infos')->add($xiaoxi);
            }
            $data['start_time'] = strtotime($data['start_time']);
            $data['end_time'] = strtotime($data['end_time']);

            M('project') -> where($where) -> save($data);
            $this -> success('修改成功！');
        }else{
            $res = M('project_type') ->  where(['pid'=>['GT',0]]) -> getField("id,title");
            $info['start_time'] = date('Y-m-d',$info['start_time']);
            $info['end_time'] =  date('Y-m-d',$info['end_time']);
            $this->assign('menu',$res);
            $this->assign('info',$info);
            $this -> display();
        }
    }
    //删除项目
    public function delProject(){
        //$array_id = explode(';',$_POST['ids']);
        $arr["id"] = array("in",$_POST['ids']);
        $data['del'] = 1;
        if(M('project')->where($arr)->save($data) !== false){
            $this -> success('删除成功！');
        }else{
            $this -> error("删除失败！");
        }
    }
    //项目打款
    public function addBalance(){
        $project_id = I('id');
    //    $status = M('project') -> where(['id'=>$project_id]) -> getField('status');
        if(IS_POST){
            //要更新两个地方
            $tranDb = M();
            $tranDb->startTrans();
            $user_id = $tranDb -> table('project') -> where(['id'=>$project_id]) -> getField('user_id');

            $info['node'] = I('node');
            $balance = I('balance');

            $info['payment_status'] = I('payment_status') ;

            $res_node = $tranDb -> table('project') -> where(['id'=>$project_id]) -> save($info);
            $res_balance = $tranDb -> table('users') -> where(['id'=>$user_id]) -> setInc('balance',$balance);

            $add_user['user_id'] = $add['user_id'] = $user_id;
            $add['project_id'] = $project_id;
            $add_user['pay_time'] = $add['pay_time'] = time();
            $add_user['amount'] =  $add['amount'] = $balance;
            $add['pay_type'] = 3;
            $add_user['pay_no'] = $add['pay_no'] = get_pay_no();
            $add_user['pay_type'] = 2;
            $add_user['pay_name'] = '项目打款';

            $res_pay = $tranDb -> table('user_account')->add($add);

            $user_pay = $tranDb -> table('user_pay')->add($add_user);

            if($res_node && $res_balance && $res_pay && $user_pay){
                $tranDb -> commit();
                $this -> success('打款成功！');
            }else{
                $tranDb -> rollback();
                $this -> error("打款失败！");

            }

        }else{


//            if($status ==5 || $status == 6){
//                $this -> assign('id',$project_id);
//                $this -> display();
//
//            }else{
//                echo '该项目不需要打款！';
//            }

            $this -> assign('id',$project_id);
            $this -> display();
        }

    }


    //项目类型
    public function proType(){
        $this -> display();
    }
    //加载项目类型
    public function loadProType(){
        $offset = i("offset");
        $limit = i("limit");
        $search_key = i('search_key');
        $search_value = i('search');
        $where['del'] = 0;
        if($search_value){
            $where['title'] = array('LIKE',"%$search_value%");;
        }
        $sort = i('sort');
        $order = i('order');
        if(!empty($sort)){
            $reorder = $sort." ".$order;
        }else{
            $reorder = 'id asc';
        }
        $list =  M('project_type')-> where($where) -> order($reorder) ->limit($offset,$limit) -> select();

        foreach ($list as &$v){
            if($v['pid'] == 0){
                $v['pid'] = '顶级栏目';
            }else{
                $v['pid'] = M('project_type')-> where(['id'=>$v['pid']]) -> getField('title');
            }
            $v['faqi'] = M('project') -> where(['type_id'=>$v['id']]) -> sum('target_amount');//目标金额
            $v['yic'] = M('project') -> where(['type_id'=>$v['id']]) -> sum('reach_amount');//目标金额
            if($v['yic'] > $v['faqi']){
                $v['haix'] = 0;
            }else{
                $v['haix'] = $v['faqi'] - $v['yic'];
            }

            $v['small_pic'] = "<img src='{$v['small_pic']}' width='50' alt='小图标'>";
        }
        //dump($list);
        $count =   M('project_type')-> where($where) -> order($reorder) -> count();
        $list_array= array("total"=>$count,"rows"=>$list?$list:array());
        echo json_encode($list_array);
    }
    //增加项目类型
    public function addProType(){
        if(IS_POST){
            $data = i('post.');
            M('project_type') -> add($data);
            $this->success('增加成功！');
        }else{

            $res = M('project_type') -> getField("id,title");
            $this->assign('menu',$res);
            $this-> display('editProType');
        }
    }
    //修改项目类型
    public function editProType(){
        $id = i('id');
        $where['id'] = $id;
        $res = M('project_type') -> where($where)-> find();
        if(IS_POST){
            $data = i('post.');

            if($data['small_pic'] != $res['small_pic']){
                unlink(ROOT_PATH.$res['small_pic']);
            }
            M('project_type') -> where($where) -> save($data);
            $this->success('修改成功！');
        }else{
            $this->assign('info',$res);
            $res = M('project_type') -> getField("id,title");
            $this->assign('menu',$res);
            $this-> display();
        }
    }
    //删除项目类型
    public function delProType(){
        $array_id['id'] = array('in',$_POST['ids']);
        M('project_type') -> where($array_id) -> save(['del'=>1]);
        $this -> success('删除成功！');
    }

//广告
    public function ad(){
        $this -> display();
    }
    //加载广告
    public function loadAd(){
        $offset = i("offset");
        $limit = i("limit");
        $search_key = i('search_key');
        $search_value = i('search');
        if($search_value){
            $where['title'] = array('LIKE',"%$search_value%");;
        }
        $sort = i('sort');
        $order = i('order');
        if(!empty($sort)){
            $reorder = $sort." ".$order;
        }else{
            $reorder = 'id asc';
        }
        $list =  M('ad')-> where($where) -> order($reorder) ->limit($offset,$limit) -> select();

        foreach ($list as &$v){
            $v['pic'] = "<img src='{$v['pic']}' width='250' alt='广告图'>";
            $v['status'] = $v['status']==1?'下线':'上线';
        }
        //dump($list);
        $count =   M('ad')-> where($where) -> order($reorder) -> count();
        $list_array= array("total"=>$count,"rows"=>$list?$list:array());
        echo json_encode($list_array);
    }
    //增加广告
    public function addAd(){
        if(IS_POST){
            $data = i('post.');
            M('ad') -> add($data);
            $this->success('增加成功！');
        }else{

            $this-> display('editad');
        }
    }
    //修改广告
    public function editAd(){
        $id = i('id');
        $where['id'] = $id;
        $res = M('ad') -> where($where)-> find();
        if(IS_POST){
            $data = i('post.');

            if($data['pic'] != $res['pic']){
                unlink(ROOT_PATH.$res['pic']);
            }
            M('ad') -> where($where) -> save($data);
            $this->success('修改成功！');
        }else{
            $this->assign('info',$res);
            $this-> display();
        }
    }
    //删除广告
    public function delAd(){
        $array_id['id'] = array('in',$_POST['ids']);
        $pic = M('ad') -> where($array_id) -> field('pic') -> select();
        $res = M('ad') -> where($array_id) -> delete();
        if($res){
            foreach($pic as $p){
                unlink(ROOT_PATH.$p['pic']);
            }
            $this -> success('删除成功！');
        }else{
            $this -> error('删除失败！');
        }
    }
    //订单
    public function orders(){
        //echo 1111;
        $p_id = I('id');
        $result = M('orders') -> where(['project_id'=> $p_id,'status'=>['GT',1]]) -> select();
        $sum = M('orders') -> where(['project_id'=> $p_id,'status'=>['GT',1]]) -> sum('pay_amount');
        foreach($result as &$v){
            $v['status'] = getOrderStatus($v['status']);
            $v['nick_name'] = M('users') -> where(['id'=>$v['user_id']]) -> getField('nick_name');
            $v['pay_time'] = date('Y-m-d H:i:s',$v['pay_time']);
        }
        $this->assign('info',$result);
        $this->assign('sum',$sum);
        $this->assign('id',$p_id);
        $this -> display();
    }
    //加载订单

    public function lorders(){
        $id = I('id');
        $this->assign('id',$id);
        $this -> display();
    }
    public function loadOrders(){
        $offset = i("offset");
        $limit = i("limit");
        $search_key = i('search_key');
        $search_value = i('search');

        $order = i('order');
        $sort = i('sort');
        if(!empty($sort)){
            $reorder = $sort." ".$order;
        }else{
            $reorder = 'id asc';
        }

        $p_id = i('id');
        $list = M('orders') -> where(['project_id'=> $p_id,'status'=>['GT',1]]) -> order($reorder) -> select();
        foreach($list as &$v){
            $v['status'] = getOrderStatus($v['status']);
            $v['nick_name'] = M('users') -> where(['id'=>$v['user_id']]) -> getField('nick_name');
            $v['weixin_no'] = M('user_pay') -> where(['pay_no'=>$v['order_no']]) -> getField('weixin_no');
            $v['weixin_no'] = '`'.$v['weixin_no'];
            $v['pay_time'] = date('Y-m-d H:i:s',$v['pay_time']);
        }

//        dump($list);

        $count = M('orders') -> where(['project_id'=> $p_id,'status'=>['GT',1]]) -> order($reorder) -> count();
        //dump($list);
        $list_array= array("total"=>$count,"rows"=>$list?$list:array());
        echo json_encode($list_array);
    }
    //添加项目汇报
    public function addReturn(){
        if(IS_POST){
            $data = i('post.');
            $data['time'] = time();
            M('project_return') -> add($data);
            $this->success('增加成功！');

        }else{
            $id = I('id');
            $info = M('project_return')-> where(['project_id'=>$id]) -> select();
            $this -> assign('info',$info);
            $this -> display();
        }

    }
    public function editReturn(){
        $data = i('post.');
        $info[$data['name']] = $data['v'];
        $where['id'] = $data['id'];
        $res = M('project_return')-> where($where) -> save($info);
        if($res){
            $this->success('修改成功！');
        }
    }
    public function delReturn(){
        $data = i('id');
        $where['id'] = $data;
        $res = M('project_return')-> where($where) -> delete();
        if($res){
            $this->success('删除成功！');
        }
    }
    //修改项目已达金额
    public function blance(){

        if(IS_POST){
            $data = i('post.');
            $where['id'] = $data['id'];
            $info  = M('project') -> where($where) -> find();
            $obj  = M('project');
            $obj -> startTrans();
            $p_info['people_num'] = $info['people_num'] + $data['num'];
            $p_info['reach_amount'] = $info['reach_amount'] + $data['balance'];
            $result  = $obj -> where($where) -> save($p_info);
            $pay_info['pay_no'] = get_pay_no();
            $pay_info['pay_time'] = time();
            $pay_info['project_id'] = $data['id'];
            $pay_info['amount'] = $data['balance'];
            $pay_info['pay_type'] = 4;
            $res = M('user_account') -> add($pay_info);
            if($res){
                $obj ->commit();
                $this->success('修改金额成功！');
            }else{
                $obj ->rollback();
                $this->error('修改金额失败！');
            }

        }else{
            $this->display();
        }


    }


}
