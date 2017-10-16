<?php
// 本类由系统自动生成，仅供测试用途
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
            session('index',1);
        //便利,默认为梦想清单
        $fid = I("fid")?I("fid"):1;
        $father_type = M('project_type') -> where(['pid'=> 0,'del'=>0]) -> select();
        $son_type = M('project_type') -> where(['pid'=> $fid,'del'=>0]) -> select();

        //查询出关联的广告图

        $ad = M('ad') -> where(['positions'=> 'index_'.$fid]) -> select();

        $s_type = I("sid")?I("sid"):$son_type[0]['id'];

        //查出类别关联的项目
        $project = M('project') -> where(['type_id'=> $s_type,'del'=>0,'status'=>['GT',1]]) -> order('id asc') -> limit(0,5) -> select();
        foreach($project as &$v){
            $info = M('users') -> where(['id'=>$v['user_id']]) -> find();
            $v['name'] = $info['nick_name'];
            $v['head_pic'] = $info['pic'];
            $v['num'] = $v['reach_amount']/$v['target_amount']*100;
        }
        $this -> assign('father_type',$father_type);
        $this -> assign('son_type',$son_type);
        $this -> assign('s_type',$s_type);
        $this -> assign('f_type',$fid);
        $this -> assign('ad',$ad);
        $this -> assign('project',$project);
        $this -> display();
    }
    //首页加载更多项目
    public function loadProject(){
        $type = I('type');
        $id = I('id');
        $where[$type] = $id;
        $where['del'] = 0;
        $count = I('time');
        $project = M('project') -> where($where) -> order('id asc') ->limit(5*$count,5) -> select();

        if($project){
            foreach($project as &$v){
                $info = M('users') -> where(['id'=>$v['user_id']]) -> find();
                $v['name'] = $info['nick_name'];
                $v['head_pic'] = $info['pic'];
            }
            $this -> assign('project',$project);
            $this->success($this->fetch(),"",true);
        }else{
            $this -> error('没有更多数据！');
        }
    }
    //list




}