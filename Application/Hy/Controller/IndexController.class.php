<?php
// 本类由系统自动生成，仅供测试用途
namespace Hy\Controller;
class IndexController extends CommonController {
    protected function _initialize(){
        parent::_initialize();
    }
    public function index(){
        echo 123;
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