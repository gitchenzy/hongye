<?php
/**
 * Created by PhpStorm.
 * User: fely
 * Date: 1/19/15
 * Time: 1:57 PM
 */

namespace Admin\Controller;

use Think\Upload;
use Think\Log;

class CommonController extends AdminController{

    protected function _initialize() {
        parent::_initialize();
    }
    //修改密码
    public function editPass(){
        if(IS_POST){
            $id = get_user_id();
            $data = i('post.');
            $type = get_user_type();
            switch($type){
                case 1:
                    $table = 'employee';
                    $where['EmployeeID'] = $id;
                break;
                case 2:
                    $table = 'agent';
                    $where['AgentID'] = $id;
                break;
                case 3:
                    $table = 'customer';
                    $where['CustomerID'] = $id;
                break;
            }
            $info = M($table) -> where($where) -> find();
            //匹配传过来的旧密码
            if(get_guoyuanPWD($data['oldPass'],$info['Random']) == $info['Password']){
                if($data['pass'] ==$data['repass']){
                    $string = get_string();
                    $map['Random']= $string->rand_string(6,1);
                    $map['Password'] = get_guoyuanPWD($data['pass'],$map['Random']);
                    M($table) -> where($where) -> save($map);
                    $this->success('修改密码成功！');
                }else{
                    $this->error('确认密码错误，请重新输入！');
                }
            }else{
                $this->error('原密码错误，如忘记密码请联系管理员！');
            }
        }else{
            $this -> display();
        }
    }
    //修改个人资料
    public function editInfo1(){
        $where['EmployeeID'] = get_user_id();
        if(IS_POST){
            $data = I('post.');
            M('employee') -> where($where) -> save($data);
            $this->success('修改成功！');
        }else{
            $info = M('employee') -> where($where) -> find();
            $this -> assign('info',$info);
            $this -> display();
        }
    }
    public function editInfo2(){
        $where['AgentID'] = get_user_id();
        if(IS_POST){
            $data = I('post.');
            M('agent') -> where($where) -> save($data);
            $this->success('修改成功！');
        }else{
            $info = M('agent') -> where($where) -> find();
            $this -> assign('info',$info);
            $this -> display();
        }
    }
    public function editInfo3(){
        $where['CustomerID'] = get_user_id();
        if(IS_POST){
            $data = I('post.');
            M('customer') -> where($where) -> save($data);
            $this->success('修改成功！');
        }else{
            $info = M('customer') -> where($where) -> find();
            $this -> assign('info',$info);
            $this -> display();
        }
    }
    //ajax获取客户的信息
    public function getCustomer(){
        $where['ShortName'] = array('like',"%{$_POST['value']}%");
        $where['DelFlag'] = 0;
        $where['Status'] = 0;
        $res = M('customer') -> where($where) -> field('ShortName') -> select();
        echo json_encode($res);
    }

    public function getALLCustomer(){
        $where['ShortName'] = array('like',"%{$_GET['q']}%");
        $where['DelFlag'] = 0;
        $where['Status'] = 0;
        $res = M('customer') -> where($where) -> field('ShortName') ->order('ShortName') -> getField("ShortName",$_GET["limit"]);
        if(!$res){
            $res['0'] = '暂无数据！';
        }
        echo json_encode($res);
    }
    //定点访问。每天汇总bug跟需求给研发负责人 时间是空间的纬度。每时每刻，我们都在前进，前进的我们都在消耗我们的生命。想看到过去也不难，有本事你就逆着走，就能找到过去
    public function getBug(){
        //汇总前一天到现在的bug还没有分配的，和已经受理的需求
        //获取前一天
        $time = time() - 24*60*60;
        $a = date('Y-m-d H:i:s',$time);//前一天的时间
        $b = date('Y-m-d H:i:s',time());
        $where['FbTime'] = array('GT',$a);
        $where['FbTime'] = array('LT',$b);
        $where['FBtype'] = 2;
        $where['allotted'] = 0;
        $where['DelFlag'] = 0;
        $res = M('feedback') -> where($where) -> select();
        //编辑邮件内容
        $content = '';
        $zhengz = "/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg]))[\'|\"].*?[\/]?>/";
        if($res){
            foreach($res as $v){
                $v['Description'] = htmlspecialchars_decode($v['Description']);
                //过滤掉内容的图片
                $v['Description'] = preg_replace($zhengz,'',$v['Description']);
                $v['Description'] = msubstr($v['Description'],0,20);

                $content .= $v['FeedbackID'].':'.strip_tags($v['Description']).'<br>';
            }
        }
        //获取受理需求
        $info['Accept']=2;
        $info['DelFlag'] = 0;
        $result = M('feedback') -> where($info) -> select();
        if($result){
            foreach($result as $va){
                $va['Description'] = htmlspecialchars_decode($va['Description']);
                //过滤掉内容的图片
                $va['Description'] = preg_replace($zhengz,'',$va['Description']);
                $va['Description'] = msubstr($va['Description'],0,20);
                $content .= $va['FeedbackID'].':'.strip_tags($va['Description']).'<br>';
            }
        }
        //判断是否有bug跟需求
        if($content){
            $title = '今日汇总的bug以及需求!';
            //查出研发负责人
            $data['DepartmentNum'] = 3;
            $data['isPriority'] = 1;
            $data['DelFlag'] = 0;
            $pen = M('employee') -> field('Email') ->where($where) -> select();
            foreach($pen as $p){
                sendEmail($p['Email'],$title,$content);
            }
        }
    }
    //汇总所有的需求给产品经理
    public function getNeed(){
        $where['FBtype'] = 1;
        $where['FbStatusID'] = 1;
        $where['allotted'] = 0;
        $where['DelFlag'] = 0;
        $where['Accept'] = array('EXP','is null');
        $result = M('feedback') -> where($where) -> select();
        $content = '';
        $title = '有新的需求啦，请上crm受理新需求！';
        $zhengz = "/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg]))[\'|\"].*?[\/]?>/";
        if($result){
            foreach($result as $v){
                $v['Description'] = htmlspecialchars_decode($v['Description']);
                //过滤掉内容的图片
                $v['Description'] = preg_replace($zhengz,'',$v['Description']);
                $v['Description'] = msubstr($v['Description'],0,20);

                $content .= $v['FeedbackID'].':'.strip_tags($v['Description']).'<br>';
            }
          //  echo $content;
            //查询产品负责人
            $data['DepartmentNum'] = 7;
            $data['isPriority'] = 1;
            $data['DelFlag'] = 0;
            $pen = M('employee') -> field('Email') ->where($where) -> select();
            foreach($pen as $p){
                sendEmail($p['Email'],$title,$content);
            }
        }
    }
    //两个小时访问一次改变一次状态
    public  function changeSupport(){
        //两个小时改为过期状态
        $time = date('Y-m-d H:i:s',time()-2*60*60) ;
        $where['ResponseTime'] = array('gt',$time);
        $where['DelFlag'] = 0;
        $where['Status'] = array('lt',1);
        $res = M('support') -> where($where) -> select();
        //当查找有值是
        foreach($res as $v){
            M('support') -> where(array('SupportID'=>$v['SupportID'])) -> save(array('ResponseState'=>2));
        }
    }





}