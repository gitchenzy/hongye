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
        $search_key = i('search_key');

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
            $v['status_name'] = $v['status']==1?'下线':'上线';
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
            $res = M('users') -> add($data);
            if($res){
                $this->success('添加成功！');
            }else{
                $this -> error('添加失败！');
            }
       }else{
           $res = M('user_grade') -> getField("id,title");
           //  $arr =$this->dal_cs ->where($where)->order("Sort asc")->getField("CtmStatusID,CtmStatusName");
           $this->assign('grade',$res);
           //dump($res);
           $this -> display('editProject');
       }
    }
    //修改项目
    public function editCustomer(){
        $id = i('id');
        $where['id'] = $id;
        $info = M('users') -> where($where) -> find();
        if(IS_POST){
            $data = i('post.');
            //相等的话，则不做图片删除处理
            if($data['pic'] != $info['pic']){
                $file = ROOT_PATH.$info['pic'];
                unlink($file);
            }
            M('users') -> where($where) -> save($data);
            $this -> success('修改成功！');
        }else{
            $res = M('user_grade') -> getField("id,title");
            $this->assign('grade',$res);
            $this->assign('info',$info);
            $this -> display();
        }
    }
    //删除项目
    public function delCustomer(){
        //$array_id = explode(';',$_POST['ids']);
        $arr["id"] = array("in",$_POST['ids']);
        $data['del'] = 1;
        if(M('users')->where($arr)->save($data) !== false){
            $this -> success('删除成功！');
        }else{
            $this -> error("删除失败！");
        }
    }
    //项目类型
    public function proType(){
        $this -> display();
    }















}
