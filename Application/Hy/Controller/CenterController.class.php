<?php
// 本类由系统自动生成，仅供测试用途
namespace Hy\Controller;
class CenterController extends CommonController {
    protected function _initialize(){
        parent::_initialize();
        $user = get_user_info();
         $user['user_id'] = 15;
//        if (!$user) {
//            $this->redirect("Index/index");
//        }
//
        $this -> user_id = $user['user_id'];
    }
    public function index(){

        $info = M('users') -> where(['id'=>$this -> user_id]) -> find();

        //我的项目
        $project = M('project') -> where(['user_id'=>$this -> user_id,'del'=>0]) -> select();
        foreach($project as &$p){
            $p['status_name'] = getProjectStatus($p['status']);
            $p['type_name'] = M('project_type') -> where(['id'=>$p['type_id']]) -> getfield('title');
        }
        $this->assign('info',$info);
        $this->assign('project',$project);

        $this -> display();
    }

    //发起新项目
    public function news(){

        //便利类别
        $son_type = M('project_type') -> where(['pid'=>array('GT',0),'del'=>0]) -> select();

        $this->assign('type',$son_type);
        $this -> display();
    }
    //添加项目第一步
    public function addProject(){

        $data = I('post.');
        $data['user_id'] = $this -> user_id;
        $info = M('project_type') -> where(['id'=>$data['type_id']])-> field('pid,is_return') -> find();
        $data['father_type_id'] = $info['pid'];
        $data['is_return'] = $info['is_return'];
        $data['status'] = 0;//草稿状态
        $data['time'] = time();
        $res = M('project') -> add($data);
        if($res){
            $url = U('Center/new_two',array('project_id'=>$res));
            $this -> success($url);
        }else{
            $this -> error('添加失败！');
        }
    }

    public function new_two(){

        //便利类别

        $this -> display();
    }

}