<?php
// 本类由系统自动生成，仅供测试用途
namespace Home\Controller;
use Think\Controller;
class ListController extends Controller {
    public function index(){
        $where['del'] = 0;
        $type = I('type')?I('type'):'start';
        switch ($type){
            case 'start':
                $order = 'start_time desc';
            break;
            case 'hot':
                $where['is_hot'] = 1;
            break;
            case 'end':
                $order = 'end_time desc';
            break;
        }

        $info = M('project') -> where($where)-> order($order)-> limit(0,5) -> select();

        $this -> assign('info',$info);
        $this -> assign('type',$type);
        $this -> display();
    }
    //加载更多项目
    public function loadProject(){
        $type = I('type');
        $where['del'] = 0;
        switch ($type){
            case 'start':
                $order = 'start_time desc';
                break;
            case 'hot':
                $where['is_hot'] = 1;
                break;
            case 'end':
                $order = 'end_time desc';
                break;
        }
        $count = I('time');
        $project = M('project') -> where($where) -> order($order) ->limit(5*$count,5*($count+1)) -> select();

        if($project){

            $this -> assign('info',$project);
            $this->success($this->fetch(),"",true);
        }else{
            $this -> error('没有更多数据！');
        }
    }


}