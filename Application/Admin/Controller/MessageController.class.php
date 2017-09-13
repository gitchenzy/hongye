<?php
/**
 * Created by PhpStorm.
 * User: Huge
 * Date: 2016/3/5 0005
 * Time: 14:00
 */

namespace Admin\Controller;


class MessageController extends AdminController
{
    protected function _initialize(){
        parent::_initialize();
    }

    public function index(){
        $this->display();
    }
    //加载投诉意见
    public function loadMessage(){
        $offset = i("offset");
        $limit = i("limit");
        $search_key = i('search_key');
        $search_value = i('search');
        if($search_value){
            $where['content'] = array('LIKE',"%$search_value%");;
        }
        $sort = i('sort');
        $order = i('order');
        if(!empty($sort)){
            $reorder = $sort." ".$order;
        }else{
            $reorder = 'id asc';
        }
        $list =  M('message')-> where($where) -> order($reorder) ->limit($offset,$limit) -> select();

        foreach ($list as &$v){
            $v['nick_name'] = M('users')-> where(['id'=>$v['user_id']]) -> getField('nick_name');
            $v['time'] = date('Y-m-d H:i:s',$v['time']);
            $v['status'] = $v['status']==1?'未处理':'已处理';
        }
        //dump($list);
        $count =   M('message')-> where($where) -> order($reorder) -> count();
        $list_array= array("total"=>$count,"rows"=>$list?$list:array());
        echo json_encode($list_array);
    }

    //处理投诉意见
    public function editMessage(){
        $id = i('id');
        $where['id'] = $id;
        if(IS_POST){
            $data['status'] =2;
            M('message') -> where($where) -> save($data);
            $this->success('处理成功！');
        }else{
            $res = M('message') -> where($where)-> find();
            $res['nick_name'] = M('users')-> where(['id'=>$res['user_id']]) -> getField('nick_name');
            $this->assign('info',$res);
            $this-> display();
        }
    }
    //删除投诉意见
    public function delMessage(){
        $array_id['id'] = array('in',$_POST['ids']);
        M('message') -> where($array_id) -> delete();
        $this -> success('删除成功！');
    }
















}
