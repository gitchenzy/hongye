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


}