<?php
/**
 * Created by PhpStorm.
 * User: Huge
 * Date: 2016/3/5 0005
 * Time: 14:00
 */

namespace Admin\Controller;


class EmployeeController extends AdminController
{
    private $dep,$emp,$duty;
    protected function _initialize(){
        parent::_initialize();
        $this -> dep = M('department');
        $this -> emp = M('employee');
        $this->duty = M('duty');
    }
    public function index(){

        $this->display();
    }

    public function loadEmployee(){
        $value = i('search');
        $offset = i("offset");
        $limit = i("limit");
        $where['del'] = 0;
//        if(!get_user_name()){
//            $where['employeeID'] = get_user_id();
//        }else{
//            if($value){
//                $where['Name'] = array('LIKE',"%$value%");
//            }
//        }

        if($value){
            $where['employee_num|name'] = array('LIKE',"%$value%");
        }
        $count = $this->emp -> where($where) -> order('employeeID asc') -> count();
        $res = $this->emp -> where($where) -> limit($offset,$limit) -> order('employeeID asc') -> select();
        foreach($res as &$v){

//            $v['DepartmentName'] =$this->dep -> where(array('DepartmentNum' => $v['DepartmentNum'])) -> getField('departmentName');
            $v['sex_name'] = $v['sex']==1?'男':'女';
            $v['status_name'] = $v['status']==1?'锁定':'正常';
            $v['priority'] = $v['is_priority']==1?'是':'否';
            $v['register_time'] = date('Y-m-d',$v['register_time']);
        }
        $list_array= array("total"=>$count,"rows"=>$res?$res:array());
        echo json_encode($list_array);
    }
    //新增员工信息
    public function addEmployee(){
        if(IS_POST){
            $data = i('post.');
            $data['register_time'] = time();

            $res = $this -> emp -> where(array('employee_num' =>$data['employee_num'],'del'=>0)) -> find();
            if($res){
                $this->error('该账号已经有人使用，请输入新的账号！');
            }
            //dump($data);
            //exit;
            $result = $this->emp -> add($data);
            if($result){
//                if(isset($data['duty']) && !empty($data['duty'])){
//                    $info['MemberID'] = $result;
//                    $info['Type'] = 1;
//                    foreach($data['duty'] as $d){
//                        $info['DutyID'] = $d;
//                        M('employee_duty') -> add($info);
//                    }
//                }
                //添加成功要给初始化密码
                $string = get_string();
                $sa['random']= $string->rand_string(6,1);
                $sa["password"] = md5(md5('123456').$sa['random']);
                $this->emp -> where(array('employeeID'=>$result,'del'=>0)) -> save($sa);
                $this -> success('添加成功！您账号的初始密码为123456');
            }else{
                $this -> error('添加失败！');
            }

        }else{
            //遍历部门
        //    $where['del'] = 0;
        //    $res = $this->dep -> where($where) -> order('Sort desc') -> getField('DepartmentNum,DepartmentName');
         //   $result = $this->duty->where(array('Status'=>1))->select();
            //$result = $this->duty -> where(array('Status'=>1)) -> order('Sort desc') -> getField('ID,DutyName');
            //$result = $this->duty  -> order('Sort desc') -> getField('ID,DutyName');
        //    $this->assign('dr',$res);
        //    $this->assign('pr',$result);
            $this-> display('editemployee');
        }
    }
    //修改员工信息
    public function editEmployee(){
        $id = i('id');
        $where['employeeID'] = $id;
        $where['del'] = 0;
        if(IS_POST){
            $data = i('post.');
            // dump($data['duty']);
            $this -> emp ->startTrans();
            $res = $this -> emp -> where(array('del'=>0,'employee_num' =>$data['employee_num'],'employeeID'=>array('neq',$id))) -> find();
            if($res){
                $this->error('该账号已经有人使用，请输入新的账号！');
            }
//            if(isset($data['duty']) && !empty($data['duty'])){
//                $map['MemberID'] = $id;
//                $map['Type'] = 1;
//                $info['MemberID'] = $id;
//                $info['Type'] = 1;
//                //先删掉存在的数据在存进提交的数据
//                M('employee_duty') -> where($map) -> delete();
//
//                foreach($data['duty'] as $d){
//                    $info['DutyID'] = $d;
//                    M('employee_duty') -> add($info);
//                }
//            }
            $this -> emp -> where($where) -> save($data);
            //在修改之后先判断是否改掉的是ADMIN
            $resu = $this -> emp -> where(array('del'=>0,'employee_num' =>'admin')) -> find();
            if(!$resu){
                $this -> emp ->rollback();
                $this->error('该账号不能被修改！');
            }else{
                $this -> emp->commit();
                $this->success('修改成功！');
            }

        }else{
            //遍历部门
          //  $wheres['del'] = 0;
          //  $resu = $this->dep -> where($wheres) -> order('Sort desc') -> getField('DepartmentNum,DepartmentName');
           // $result = $this->duty -> where(array('Status' => 1)) -> getField('ID,DutyName');
          //  $result = $this->duty -> where(array('Status' => 1)) -> select();
          //  $r = M('employee_duty') -> where(array('MemberID'=> $id)) -> select();
//            $sting = '';
//            foreach($r as $s){
//                $sting .= $s['DutyID'];
//            }
//            foreach($result as &$p){
//                if(strpos($sting,$p['ID']) !== false){
//                    $p['biaozhi'] = 1;
//                }
//            }
           // dump($result);
          //  $this->assign('dr',$resu);
         //   $this->assign('pr',$result);
            $res = $this-> emp -> where($where)-> find();
            $this->assign('info',$res);
            $this-> display();
        }
    }
    //删除员工信息
    public function delEmployee(){
        $array_id['employeeID'] = array('in',$_POST['ids']);
        $data['del'] = 1;
        $this -> emp -> where($array_id) -> save($data);
        $this -> success('删除成功！');
    }
    //重置密码
    public function editPWD(){

        if(IS_POST){
            $data = i('post.');
            $id = i('id');
            if($data['pass'] != $data['repass']){
                $this -> error('两次密码不一致！');
                exit;
            }
            $string = get_string();
            $sa['random']= $string->rand_string(6,1);
            $sa["password"] = md5(md5($data['pass']).$sa['random']);
            $this->emp -> where(array('employeeID'=>$id)) -> save($sa);
            $this -> success('重置成功！');

        }else{
            $this -> display();
        }
    }

    public function department(){
        $this->assign("crumbs_title","部门信息");
        $this->display();
    }
    //加载部门信息
    public function loadDepartment(){
        $value = i('search');
        $offset = i("offset");
        $limit = i("limit");
        $where['DelFlag'] = 0;
        if($value){
            $where['DepartmentName'] = array('LIKE',"%$value%");
        }

        $count = $this->dep -> where($where) -> order('Sort desc') -> count();
        $res = $this->dep -> where($where) -> limit($offset,$limit) -> order('Sort desc') -> select();
        foreach($res as &$v){
            $v['OperatorName'] =$this->emp -> where(array('EmployeeID' => $v['OperatorID'])) -> getField('Name');
        }

        $list_array= array("total"=>$count,"rows"=>$res?$res:array());
        echo json_encode($list_array);
    }
    //新增部门信息
    public function addDepartment(){
        if(IS_POST){
            $data = i('post.');
            $data['OperatorID'] = $this->user['EmployeeID'];
            $data['ModifyTime'] = date('Y-m-d H:i:s');
            //判断部门编号不能重复
            $res = $this->dep -> where(array('DepartmentNum'=>$data['DepartmentNum'],'DelFlag'=>0)) ->find();
            if($res){
                $this -> error('该部门编号已经存在！');
                exit;
            }
            $this->dep -> add($data);
            $this->success('增加成功！');
        }else{
            $this-> display('editDepartment');
        }
    }
    //修改部门信息
    public function editDepartment(){
        $id = i('id');
        $where['DepartmentID'] = $id;
        $where['DelFlag'] = 0;
        if(IS_POST){
            $data = i('post.');
            $data['ModifyTime'] = date('Y-m-d H:i:s');
            $data['OperatorID'] = $this->user['EmployeeID'];

            $res = $this->dep -> where(array('DepartmentNum'=>$data['DepartmentNum'],'DepartmentID' =>array('NEQ',$id),'DelFlag'=>0)) ->find();
            if($res){
                $this -> error('该部门编号已经存在！');
                exit;
            }
            $this -> dep -> where($where) -> save($data);
            $this->success('修改成功！');
        }else{
            $res = $this-> dep -> where($where)-> find();
            $this->assign('info',$res);
            $this-> display();
        }
    }
    //删除部门信息
    public function delDepartment(){
        $array_id['DepartmentID'] = array('in',$_POST['ids']);
        $data['DelFlag'] = 1;
        $data['ModifyTime'] = date('Y-m-d H:i:s');
        $data['OperatorID'] = $this->user['EmployeeID'];
        $this -> dep -> where($array_id) -> save($data);
        $this -> success('删除成功！');
    }


    //权限管理 用户组管理
    public function userGroup(){
        $this->display();
    }
    //加载用户组
    public function loadUserGroup(){
        $offset = i("offset");
        $limit = i("limit");
        $where['Status'] = array('NEQ','-1');
        $count = M('duty') -> where($where) -> count();
        $res = M('duty') -> where($where) -> limit($offset,$limit) -> select();
        foreach($res as &$v){
            $v['StatusName'] =$v['Status']==1?'正常':'禁用';
        }
        $list_array= array("total"=>$count,"rows"=>$res?$res:array());
        echo json_encode($list_array);

    }
    //添加用户组
    public function addGroup(){

        if(IS_POST){
            $data = i('post.');
            M('duty') -> add($data);
            $this->success('增加成功！');
        }else{
            $this->display('editGroup');
        }

    }
    //修改用户组
    public function editGroup(){
        $id = i('id');
        $where['ID'] = $id;
        if(IS_POST){
            $data = i('post.');
            M('duty') -> where($where) -> save($data);
            $this->success('修改成功！');
        }else{
            $res = M('duty') -> where($where)-> find();
            $this->assign('info',$res);
            $this-> display();
        }
    }
    //删除用户组
    public function delGroup(){
        $array_id['ID'] = array('in',$_POST['ids']);
        $data['Status'] = -1;
        M('duty') -> where($array_id) -> save($data);
        $this -> success('删除成功！');
    }
    //权限管理
    public function editAuthority(){
        $dutyID = i('id');

        $res = M('menu') -> select();
        //用递归遍历菜单
       // $list = Employee($res,0);
       // dump($list);
        $rule = M('duty') -> where(array('ID'=>$dutyID)) -> getField('Rules');
        $is_array = explode(',',$rule);
        foreach($res as $k => $v){
            $array[$k]['id'] = $v['ID'];
            $array[$k]['pId'] = $v['PID'];
            $array[$k]['name'] = $v['Title'];
            if(in_array($v['ID'],$is_array)){
                $array[$k]['checked'] = true;
                $array[$k]['open'] = true;
            }
        }
        $str = (json_encode($array));
       // dump($str);
        $this->assign('res',$str);
        $this->display();

    }
    //修改权限
    public function modAuthority(){
        $ids = i('ids');
        $dutyID = i('dutyID');
        $data['Rules'] = implode(',',$ids);
        M('duty') -> where(array('ID'=>$dutyID)) -> save($data);
        $this -> success('修改成功！');
    }

    //菜单管理
    public function menu(){
        $this -> display();
    }
    //加载菜单
    public function loadMenu(){
        $offset = i("offset");
        $limit = i("limit");
        $value = i('search');
        if($value){
            $where['Title'] = array('like',"%$value%");
        }
        $count = M('menu') -> where($where) -> count();
        $res = M('menu') -> limit($offset,$limit) -> where($where) ->select();
        foreach($res as &$v){
            if($v['PID'] == 0){
                $v['PName'] = '顶级菜单';
            }else{
                $v['PName'] = M('menu') -> where(array('ID'=>$v['PID'])) -> getField('Title');
            }
        }
        $list_array= array("total"=>$count,"rows"=>$res?$res:array());
        echo json_encode($list_array);
    }
    //添加菜单
    public function addMenu(){
        if(IS_POST){
            $data = i('post.');
            $title = explode(',',$data['Title']);
            $url = explode(',',$data['Url']);
            $sort = explode(',',$data['Sort']);
            $info["PID"] = $data['PID'];
            $info["ShowType"] = $data['ShowType'];
            foreach($title as $k => $v){
                $info['Title'] = $v;
                if($url[$k]){
                    $info['Url'] = trimTring($url[$k]);
                }else{
                    $info['Url'] = '';
                }
                if($sort[$k]){
                    $info['Sort'] = $sort[$k];
                }else{
                    $info['Sort'] = 0;
                }
                M('menu') -> add($info);
            }
            $this->success('增加成功！');
        }else{
            $menu = M('menu') -> where(array('ShowType'=> 0)) -> getField('ID,Title');
            $menu['0'] = '顶级菜单';
            $this->assign('menu',$menu);
            $this->display('editMenu');
        }

    }
    //修改菜单
    public function editMenu(){
        $id = i('id');
        if(IS_POST){
            //修改
            $data = i('post.');
            M('menu') -> where(array('ID'=> $id)) ->  save($data);
            $this->success('修改成功！');
        }else{
            $info = M('menu') -> where(array('ID'=> $id)) ->  find();
            $menu = M('menu') -> where(array('ShowType'=> 0)) -> getField('ID,Title');
            $menu['0'] = '顶级菜单';
            $this->assign('menu',$menu);
            $this->assign('info',$info);
            $this->display('editMenu');
        }
    }
    //删除菜单
    public function delMenu(){
        $array_id['ID'] = array('in',$_POST['ids']);
        M('menu') -> where($array_id) -> delete();
        $this -> success('删除成功！');
    }

}
