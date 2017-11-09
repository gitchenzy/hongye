<?php
// 本类由系统自动生成，仅供测试用途
namespace Hy\Controller;
class CenterController extends CommonController {
    protected function _initialize(){
        parent::_initialize();
        $user = get_user_info();
       //  $user['user_id'] = 15;
        if (!$user) {
            $this->redirect("Login/index");
        }
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
        //判断是否有project_id
        $project_id = I('project_id');
        if($project_id){
            $project = M('project') -> where(['id'=>$project_id,'del'=>0]) -> find();
        }else{
            $project = 1;
        }
        $this->assign('type',$son_type);
        $this->assign('project',$project);
        $this -> display();
    }
    //添加项目第一步
    public function addProject(){

        $data = I('post.');
        if($data['id'] > 0){
            $res = M('project') ->where(['id' => $data['id']]) ->save($data);
            if($res){
                $url = U('Center/new_two',array('project_id'=>$data['id']));
                $this -> success($url);
            }else{
                $this -> error('添加失败！');
            }
        }else{
            unset($data['id']);
            $data['user_id'] = $this -> user_id;
            $info = M('project_type') -> where(['id'=>$data['type_id']])-> field('pid,is_return') -> find();
            $data['status'] = 0;//草稿状态
            $data['father_type_id'] = $info['pid'];
            $data['is_return'] = $info['is_return'];
            $data['time'] = time();
            $res = M('project') -> add($data);
            if($res){
                $url = U('Center/new_two',array('project_id'=>$res));
                $this -> success($url);
            }else{
                $this -> error('添加失败！');
            }
        }


    }
    public function new_two(){
        //便利类别

        $project_id = I('project_id');
        $project = M('project') -> where(['id'=>$project_id,'del'=>0]) -> find();
        $this->assign('project',$project);
        $this -> display();
    }
    //添加项目第二部
    public function addProjectDea(){

        $data = I('post.');
        $where['user_id'] = $this -> user_id;
        $where['id'] = $data['id'];
        $where['del'] = 0;

        $res = M('project') -> where($where) ->save(['des'=>$data['content']]);

        if($res){
            $url = U('Center/new_three',array('project_id'=>$data['id']));
            $this -> success($url);
        }else{
            $this -> error('添加内容失败！');
        }
    }
    //项目第三部
    public function new_three(){

        $project_id = I('project_id');

        $info = M('project_return') -> where(['project_id'=>$project_id]) -> select();
        $count = M('project_return') -> where(['project_id'=>$project_id]) -> count();

        $this -> assign('info',$info);
        $this -> assign('count',$count);
        $this -> assign('id',$project_id);

        $this -> display();
    }
    //添加下那个木回报
    public function addReturn(){
        $data = I('post.');
        $data['time'] = time();
        $info = M('project_return') -> add($data);
        if($info){
            $this -> success('添加成功！');
        }else{
            $this -> error('添加失败！');
        }
    }
    //修改回报
    public function editReturn(){
        $data = I('post.');
        $info = M('project_return') -> where(['id'=>$data['id']]) -> save([$data['name']=>$data['val']]);
        if($info){
            $this -> success('修改成功！');
        }else{
            $this -> error('修改失败！');
        }
    }
    //修改项目状态
    public function editProject(){

        $project_id = I('project_id');
        $where['user_id'] = $this -> user_id;
        $where['id'] = $project_id;
        $where['del'] = 0;
        $res = M('project') -> where($where) ->save(['status'=>1]);
        if($res){
            $this -> success('修改成功！');
        }else{
            $this -> error('修改失败！');
        }
    }


}