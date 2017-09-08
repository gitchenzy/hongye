<?php
/**
 * Created by PhpStorm.
 * User: Huge
 * Date: 2016/1/23 0023
 * Time: 10:42
 */
namespace Admin\Controller;

class ConfigController extends AdminController {

    protected function _initialize(){
        parent::_initialize();
        $this->emp = M("employee");
        $this->config = M("config");

    }

    public function index(){
        $this -> display();
    }
    public function loadOpen(){

        $value = i('search');
        $where['DelFlag'] = 0;
        $offset = i("offset");
        $limit = i("limit");
        if($value){
            $where['Value'] = array('LIKE',"%$value%");
        }

        $count = $this->config -> where($where) -> order('Sort desc') -> count();
        $res = $this->config -> where($where) -> limit($offset,$limit) -> order('Sort desc') -> select();
        foreach($res as &$v){
            $v['OperatorName'] =$this->emp -> where(array('EmployeeID' => $v['Operator'])) -> getField('Name');
        }
        // $list_array= $res?$res:array();
        $list_array= array("total"=>$count,"rows"=>$res?$res:array());
        echo json_encode($list_array);
    }
    //添加
    public function addOpen(){
        if(IS_POST){
            $data = i('post.');
            $data['CreateTime'] = date('Y-m-d H:i:s');
            $data['Operator'] = $this->user['EmployeeID'];
            $data['ModifyTime'] = date('Y-m-d H:i:s');
            $this->config -> add($data);
            $this->success('增加成功！');
        }else{
            $this-> display('editOpen');
        }
    }
    //修改
    public function editOpen(){
        $id = i('id');
        $where['id'] = $id;
        $where['DelFlag'] = 0;
        if(IS_POST){
            $data = i('post.');
            $data['ModifyTime'] = date('Y-m-d H:i:s');
            $data['Operator'] = $this->user['EmployeeID'];
            $this -> config -> where($where) -> save($data);
            $this->success('修改成功！');
        }else{
            //客户信息
            $res = $this-> config -> where($where)-> find();
            $this->assign('info',$res);
            $this-> display();
        }
    }
    //删除合同类型
    public function delOpen(){
        $array_id['id'] = array('in',$_POST['ids']);
        $data['DelFlag'] = 1;
        $data['ModifyTime'] = date('Y-m-d H:i:s');
        $data['Operator'] = $this->user['EmployeeID'];

        $this -> config -> where($array_id) -> save($data);

        $this -> success('删除成功！');
    }

}