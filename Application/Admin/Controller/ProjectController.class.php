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
        if(!empty($sort)){
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
           $this -> display('editProject');
       }
    }
    //修改项目
    public function editProject(){
        $id = i('id');
        $where['id'] = $id;
        $info = M('project') -> where($where) -> find();
        if(IS_POST){
            $data = i('post.');

            M('project') -> where($where) -> save($data);
            $this -> success('修改成功！');
        }else{
            $res = M('project_type') ->  where(['pid'=>['GT',0]]) -> getField("id,title");
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
        $status = M('project') -> where(['id'=>$project_id]) -> getField('status');
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

            $add['user_id'] = $user_id;
            $add['project_id'] = $project_id;
            $add['pay_time'] = time();
            $add['amount'] = $balance;
            $add['pay_type'] = 3;
            $add['pay_no'] = get_pay_no();
            $res_pay = $tranDb -> table('user_account')->add($add);

            if($res_node && $res_balance && $res_pay){
                $tranDb -> commit();
                $this -> success('打款成功！');
            }else{
                $tranDb -> rollback();
                $this -> error("打款失败！");

            }

        }else{


            if($status ==5 || $status == 6){
                $this -> assign('id',$project_id);
                $this -> display();

            }else{
                echo '该项目不需要打款！';
            }

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

            $this-> display('editAd');
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














}
