<?php
// 本类由系统自动生成，仅供测试用途
namespace Home\Controller;
class IndexController extends CommonController {
    protected function _initialize(){
        parent::_initialize();
    }
    public function index(){

            session('index',1);
        //便利,默认为梦想清单
        $fid = 1;
    //    $father_type = M('project_type') -> where(['pid'=> 0,'del'=>0]) -> select();
        $son_type = M('project_type') -> where(['pid'=>array('GT',0),'del'=>0]) -> select();

        //查询出关联的广告图

        $ad = M('ad') -> where(['positions'=> 'index_'.$fid]) -> select();

        $s_type = I("sid")?I("sid"):$son_type[0]['id'];

        //查出类别关联的项目
        $project = M('project') -> where(['type_id'=> $s_type,'del'=>0,'status'=>['GT',1]]) -> order('id desc') -> limit(0,5) -> select();
        foreach($project as &$v){
            $info = M('users') -> where(['id'=>$v['user_id']]) -> find();
            $v['name'] = $info['nick_name'];
            $v['head_pic'] = $info['pic'];
        //    $v['num'] = $v['reach_amount']/$v['target_amount']*100;

                    //进度条
        $v['num'] = floor($v['reach_amount']/$v['target_amount']*100);
        if($v['num'] >=100){
            $v['num_1'] = 0;
        }else{
            $v['num_1'] = 100-$v['num']-13.5;
        }        
        }
      //  $this -> assign('father_type',$father_type);
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
        $where['status'] = ['GT',1];
        $count = I('time');
        $project = M('project') -> where($where) -> order('id desc') ->limit(5*$count,5) -> select();

        if($project){
            foreach($project as &$v){
                $info = M('users') -> where(['id'=>$v['user_id']]) -> find();
                $v['name'] = $info['nick_name'];
                $v['head_pic'] = $info['pic'];
                $v['num'] = floor($v['reach_amount']/$v['target_amount']*100);
                if($v['num'] >=100){
                    $v['num_1'] = 0;
                }else{
                    $v['num_1'] = 100-$v['num']-14;
                }     

            }
            $this -> assign('project',$project);
            $this->success($this->fetch(),"",true);
        }else{
            $this -> error('没有更多数据！');
        }
    }
    //list

}