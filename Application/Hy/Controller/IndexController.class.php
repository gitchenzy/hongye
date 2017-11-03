<?php
// 本类由系统自动生成，仅供测试用途
namespace Hy\Controller;
class IndexController extends CommonController {
    protected function _initialize(){
        parent::_initialize();
    }
    public function index(){

        //找出热门的项目
        $where['is_hot'] = 1;
        $where['del'] = 0;
        $where['status'] = array('GT',1);
        //是否最热
        $hot_info = M('project') -> where($where) -> limit(0,9) ->select();
        foreach($hot_info as $k => $v){
            $hot_info[$k]['typename'] = M('project_type') -> where(['id'=>$v['type_id']]) -> getfield('title');
            $hot_info[$k]['num'] = floor($v['reach_amount']/$v['target_amount']*100);
            if($hot_info[$k]['num'] >=100){
                $hot_info[$k]['num_1'] = 86;
            }else{
                $hot_info[$k]['num_1'] = $hot_info[$k]['num']-4;
                if($hot_info[$k]['num_1'] < 0){
                    $hot_info[$k]['num_1']=0;
                }
            }
        }
        $where['is_hot'] = 0;
        $info = M('project') -> where($where)-> order('id desc') -> limit(0,9) ->select();
        foreach($info as $ik => $iv){
            $info[$ik]['typename'] = M('project_type') -> where(['id'=>$iv['type_id']]) -> getfield('title');

            $info[$ik]['num'] = floor($iv['reach_amount']/$iv['target_amount']*100);
            if($info[$ik]['num'] >=100){
                $info[$ik]['num_1'] = 86;
            }else{
                $info[$ik] ['num_1'] = $info[$ik]['num']-4;
                if($info[$ik]['num_1'] < 0){
                    $info[$ik]['num_1']=0;
                }
            }
        }
        $this -> assign('hot_info',$hot_info);
        $this -> assign('info',$info);
        $this -> assign('pos',1);
        $this -> display();
    }
    public function loadProject(){

        $where['is_hot'] = 0;
        $where['del'] = 0;
        $count = I('time');
        $where['status'] = array('GT',1);
        $info = M('project') -> where($where) -> order('id desc') -> limit(9*$count,9) ->select();
        if($info){
            foreach($info as $ik => $iv){
                $info[$ik]['typename'] = M('project_type') -> where(['id'=>$iv['type_id']]) -> getfield('title');
                $info[$ik]['num'] = floor($iv['reach_amount']/$iv['target_amount']*100);
                if($info[$ik]['num'] >=100){
                    $info[$ik]['num_1'] = 86;
                }else{
                    $info[$ik] ['num_1'] = $info[$ik]['num']-4;
                    if($info[$ik]['num_1'] < 0){
                        $info[$ik]['num_1']=0;
                    }
                }
            }
            $this -> assign('info',$info);
            $this->success($this->fetch(),"",true);
        }else{
            $this -> error('没有更多数据！');
        }
    }
    public function lists(){
        $type_where['pid'] = array('GT',0);
        $type = M('project_type') -> field('title,id') ->where($type_where)  ->select();
        $where['status'] = array('GT',1);
        $where['del'] = 0;
        $pid = I('t_id');
        $orders = I('orders');
        $order = 'id desc';
        if(isset($pid) && !empty($pid)){
            $where['type_id'] = $pid;
        }
        if(isset($orders) && !empty($orders)){
            switch ($orders){
                case '1':
                    $order = 'start_time desc';
                    break;
                case '2':
                    $where['is_hot'] = 1;
                    break;
                case '3':
                    $order = 'end_time desc';
                    break;
            }
        }
        $info = M('project') -> where($where) -> order($order) -> limit(0,9) ->select();
        foreach($info as $ik => $iv){
            $info[$ik]['typename'] = M('project_type') -> where(['id'=>$iv['type_id']]) -> getfield('title');
            $info[$ik]['num'] = floor($iv['reach_amount']/$iv['target_amount']*100);
            if($info[$ik]['num'] >=100){
                $info[$ik]['num_1'] = 86;
            }else{
                $info[$ik] ['num_1'] = $info[$ik]['num']-4;
                if($info[$ik]['num_1'] < 0){
                    $info[$ik]['num_1']=0;
                }
            }
        }
        $this -> assign('info',$info);
        $this -> assign('type',$type);
        $this -> assign('pos',2);
        $this -> display();

    }
    public function loadLists(){

        $where['status'] = array('GT',1);
        $where['del'] = 0;
        $count = I('time');
        $pid = I('t_id');
        $orders = I('orders');
        $order = 'id desc';
        if(isset($pid) && !empty($pid)){
            $where['type_id'] = $pid;
        }
        if(isset($orders) && !empty($orders)){
            switch ($orders){
                case '1':
                    $order = 'start_time desc';
                    break;
                case '2':
                    $where['is_hot'] = 1;
                    break;
                case '3':
                    $order = 'end_time desc';
                    break;
            }
        }
        $info = M('project') -> where($where) -> order($order) -> limit(9*$count,9) ->select();
        if($info){
            foreach($info as $ik => $iv){
                $info[$ik]['typename'] = M('project_type') -> where(['id'=>$iv['type_id']]) -> getfield('title');
                $info[$ik]['num'] = floor($iv['reach_amount']/$iv['target_amount']*100);
                if($info[$ik]['num'] >=100){
                    $info[$ik]['num_1'] = 86;
                }else{
                    $info[$ik] ['num_1'] = $info[$ik]['num']-4;
                    if($info[$ik]['num_1'] < 0){
                        $info[$ik]['num_1']=0;
                    }
                }
            }
            $this -> assign('info',$info);
            $this->success($this->fetch(),"",true);
        }else{
            $this -> error('没有更多数据！');
        }

    }

}