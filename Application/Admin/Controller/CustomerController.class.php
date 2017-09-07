<?php
/**
 * Created by PhpStorm.
 * User: Huge
 * Date: 2016/3/5 0005
 * Time: 14:00
 */

namespace Admin\Controller;


class CustomerController extends AdminController
{
    protected function _initialize(){
        parent::_initialize();
        $this->dal = M("users");

    }

    public function customer(){

        $this->display();
    }
    public function index(){
        $this->display();
    }

    public function loadCustomer(){
        // sort:id order:desc limit:10 offset:0

        //$page = i('page');
        //$rows = i('rows');
        $offset = i("offset");

        $limit = i("limit");
        $search_key = i('search_key');

        $search_value = i('search');
        if($search_value){
            $where['nick_name|weixin'] = array('LIKE',"%$search_value%");;
        }

        $sort = i('sort');
        $order = i('order');
        if(!empty($sort)){
            $reorder = $sort." ".$order;
        }else{
            $reorder = 'reg_time desc';
        }
        $list =  M('users')-> where($where) -> order($reorder) ->limit($offset,$limit) -> select();
        $count =   M('users')-> where($where) -> order($reorder) -> count();
        $list_array= array("total"=>$count,"rows"=>$list?$list:array());
     //   dump($list);
        echo json_encode($list_array);
    }
    //添加会员信息
    public function addCustomer(){
       if(IS_POST){
            $data = i('post.');
            $data['Identifier'] = getCustomerIdentifier();
            $data['ModifyTime'] = date('Y-m-d H:i:s');
            $data['CreateTime'] = date('Y-m-d H:i:s');
            $data['OperatorID'] = $this->user['EmployeeID'];
            $where['LoginName'] = $data['LoginName'];
            //admin添加的都是公共的客户
           if(!get_user_name()){
               $data['DeveloperID'] = $this->user['EmployeeID'];
           }

            $data['Sources'] = get_user_type();
            $result = $this->dal -> where($where) -> find();
            if($result){
                $this -> error('账号已经存在，请重新输入账号！');
                exit;
            }
           if($data['ShortName']){
               $r = $this->dal -> where(array('ShortName'=>$data['ShortName'])) -> find();
               if($r){
                   $this -> error('该简称已经被使用，请重新输入简称！');
                   exit;
               }
           }
            $res = $this->dal -> add($data);
            if($res){
                //为会员赋权限
                $dutyinfo = M('duty') -> where(array('Type'=> 3,'Status'=>1)) -> find();
                if($dutyinfo){
                    M('employee_duty') -> add(array('MemberID'=>$res,'DutyID'=>$dutyinfo['ID'],'Type'=> 3));
                }else{
                    $result =  M('duty') -> add(array('DutyName'=>'客户权限组','Type'=>3,'Module'=>'admin'));
                    M('employee_duty') -> add(array('MemberID'=>$res,'DutyID'=>$result,'Type'=> 3));
                }
                //添加成功要给初始化密码
                $string = get_string();
                $sa['Random']= $string->rand_string(6,1);
                $sa["Password"] = md5(md5($sa['Random']).$sa['Random']);
                $this->dal -> where(array('CustomerID'=>$res)) -> save($sa);
                $this -> success('添加成功！您账号的初始密码为'.$sa['Random']);
            }else{
                $this -> error('添加失败！');
            }

       }else{
           $where['DelFlag'] = 0;
           $res['cs'] = $this->dal_cs -> where($where) -> getField("CtmStatusID,CtmStatusName");
           $res['cr'] = $this->dal_cr -> where($where) -> getField("CtmRoleID,CtmRoleName");
           $res['ck'] = $this->dal_ck -> where($where) ->getField("CtmRankID,CtmRankName");
           $res['co'] = $this->close -> where($where) -> getField("CtmCloseID,CtmCloseName");
         //  $arr =$this->dal_cs ->where($where)->order("Sort asc")->getField("CtmStatusID,CtmStatusName");
           $this->assign('cus',$res);
           $this -> display('editCustomer');
       }
    }
    //修改会员
    public function editCustomer(){
        $id = i('id');
        if(IS_POST){
            $data = i('post.');
            $data['ModifyTime'] = date('Y-m-d H:i:s');
            $data['OperatorID'] = $this->user['EmployeeID'];
            $where['CustomerID'] = $id;
            //为会员赋权限
            $dutyinfo = M('duty') -> where(array('Type'=> 3,'Status'=>1)) -> find();
            if($dutyinfo){
                $d = M('employee_duty') -> where(array('MemberID'=>$id,'DutyID'=>$dutyinfo['ID'],'Type'=> 3)) -> find();
                if(!$d){
                    M('employee_duty') -> add(array('MemberID'=>$id,'DutyID'=>$dutyinfo['ID'],'Type'=> 3));
                }
            }else{
                $result =  M('duty') -> add(array('DutyName'=>'客户权限组','Type'=>3,'Module'=>'admin'));
                M('employee_duty') -> add(array('MemberID'=>$id,'DutyID'=>$result,'Type'=> 3));
            }

            if($data['ShortName']){
                $map['ShortName']=$data['ShortName'];
                $map['CustomerID']=array('neq',$id);
                $r = $this->dal -> where($map) -> find();
                if($r){
                    $this -> error('该简称已经被使用，请重新输入简称！');
                    exit;
                }
            }

            $this->dal -> where($where) -> save($data);
            $this -> success('修改成功！');
        }else{
            $where['DelFlag'] = 0;
            $res['cs'] = $this->dal_cs -> where($where) -> getField("CtmStatusID,CtmStatusName");
            $res['cr'] = $this->dal_cr -> where($where) -> getField("CtmRoleID,CtmRoleName");
            $res['ck'] = $this->dal_ck -> where($where) ->getField("CtmRankID,CtmRankName");
            $res['co'] = $this->close -> where($where) -> getField("CtmCloseID,CtmCloseName");
            $where['CustomerID'] = $id;
            $info = $this->dal -> where($where) -> find();
            $this->assign('cus',$res);
            $this->assign('info',$info);
            $this -> display();
        }
    }
    //删除会员
    public function delCustomer(){
        //$array_id = explode(';',$_POST['ids']);
        $arr["CustomerID"] = array("in",$_POST['ids']);
        $data['DelFlag'] = 1;
        $data['ModifyTime'] = date('Y-m-d H:i:s');
        $data['OperatorID'] = $this->user['EmployeeID'];
        if($this->dal->where($arr)->save($data) !== false){
            $this -> success('删除成功！');
        }else{
            $this -> error("删除失败！");
        }

    }



}
