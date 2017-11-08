<?php
/**
 * Created by PhpStorm.
 * User: Huge
 * Date: 2016/3/5 0005
 * Time: 14:00
 */

namespace Admin\Controller;


class OrdersController extends AdminController
{
    protected function _initialize(){
        parent::_initialize();
    }

    public function index(){
        $this->display();
    }
    //加载流水账
    public function loadAccount(){
        // sort:id order:desc limit:10 offset:0
        //$page = i('page');
        //$rows = i('rows');
        $offset = i("offset");

        $limit = i("limit");
        $search_key = i('search_key');

        $search_value = i('search');
        $type = i('type');
        $timea = i('timea');
        $timeb = i('timeb');

        if(!$timea && $timeb){
            $timeb = strtotime($timeb);
            $where['a.pay_time'] = array('lt',$timeb);
        }

        if($timea && !$timeb){
            $timea = strtotime($timea);
            $where['a.pay_time'] = array('GT',$timea);
        }
        if($timea && $timeb){
            $timea = strtotime($timea);
            $timeb = strtotime($timeb);
            $where['a.pay_time'] = array('between',array($timea,$timeb));
        }

        if($search_value){
//            $where['a.pay_amount|u.nick_name'] = array('LIKE',"%$search_value%")
            $where['a.amount|u.nick_name'] = $search_value;
        }
        if(!empty($type) && isset($type)){
            $where['a.pay_type'] = $type;
        }
        $sort = i('sort');
        $order = i('order');
        if(!empty($sort)){
            $reorder = 'a.'.$sort." ".$order;
        }else{
            $reorder = 'a.id desc';
        }
        $list =  M('user_account')-> alias('a')
            ->join('users as u on u.id = a.user_id','left')
            -> field('a.*,u.nick_name')
            -> where($where)
            -> order($reorder)
            ->limit($offset,$limit)
            -> select();
//        dump(M('user_account')->getlastsql());
        foreach($list as &$v){

            $v['pay_time'] = date('Y-m-d H:i:s',$v['pay_time']);
            if($v['pay_type']==1){
                $v['pay_type'] = '充值';
            }elseif($v['pay_type']==2){
                $v['pay_type'] = '提现';
            }elseif($v['pay_type']==3){
                $v['nick_name'] = M('project') -> where(['id'=>$v['project_id']]) -> getfield('title');
                $v['pay_type'] = '项目打款';
            }else{
                $v['nick_name'] = M('project') -> where(['id'=>$v['project_id']]) -> getfield('title');
                $v['pay_type'] = '修改项目已达金额';
            }

        }
//        dump($list);
        $count =  M('user_account')-> alias('a')
            ->join('users as u on u.id = a.user_id','left')
            -> where($where)
            -> order($reorder)
            -> count();
        $list_array= array("total"=>$count,"rows"=>$list?$list:array());
        echo json_encode($list_array);
    }

    //支付记录
    public function pay(){
        $this -> display();
    }

    //加载流水账
    public function loadPay(){
        // sort:id order:desc limit:10 offset:0
        //$page = i('page');
        //$rows = i('rows');
        $offset = i("offset");

        $limit = i("limit");
        $search_key = i('search_key');

        $search_value = i('search');
        $type = i('type');
        $user_id = i('user_id');
        $timea = i('timea');
        $timeb = i('timeb');
        $where['a.pay_type'] = 1;
        $where['a.pay_name'] = '付款';
        if(!$timea && $timeb){
            $timeb = strtotime($timeb);
            $where['a.pay_time'] = array('lt',$timeb);
        }
        if($user_id){
            $where['a.user_id'] = $user_id;
        }

        if($timea && !$timeb){
            $timea = strtotime($timea);
            $where['a.pay_time'] = array('GT',$timea);
        }
        if($timea && $timeb){
            $timea = strtotime($timea);
            $timeb = strtotime($timeb);
            $where['a.pay_time'] = array('between',array($timea,$timeb));
        }

        if($search_value){
            $where['u.nick_name'] = array('LIKE',"%$search_value%");;
        }

        $sort = i('sort');
        $order = i('order');
        if(!empty($sort)){
            $reorder = 'a.'.$sort." ".$order;
        }else{
            $reorder = 'a.id desc';
        }
        $list =  M('user_pay')-> alias('a')
            ->join('users as u on u.id = a.user_id','left')
            -> field('a.*,u.nick_name')
            -> where($where)
            -> order($reorder)
            ->limit($offset,$limit)
            -> select();
//        dump(M('user_account')->getlastsql());

        foreach($list as &$v){

            $v['pay_time'] = date('Y-m-d H:i:s',$v['pay_time']);
            $v['weixin_no'] = '`'.$v['weixin_no'];
//            if($v['pay_type']==1){
//                $v['pay_type'] = '充值';
//            }elseif($v['pay_type']==2){
//                $v['pay_type'] = '提现';
//            }else{
//                $v['pay_type'] = '项目打款';
//            }
        }
//        dump($list);
        $count =  M('user_pay')-> alias('a')
            ->join('users as u on u.id = a.user_id','left')
            -> where($where)
            -> order($reorder)
            -> count();
        $list_array= array("total"=>$count,"rows"=>$list?$list:array());
        echo json_encode($list_array);
    }
    //会员提现申请
    public function payout(){
        $this -> display();
    }
    public function loadPayout(){
        $offset = i("offset");

        $limit = i("limit");
        $search_key = i('search_key');

        $search_value = i('search');
        $type = i('status');
        $timea = i('timea');
        $timeb = i('timeb');

        if(!$timea && $timeb){
            $timeb = strtotime($timeb);
            $where['a.time'] = array('lt',$timeb);
        }

        if($timea && !$timeb){
            $timea = strtotime($timea);
            $where['a.time'] = array('GT',$timea);
        }
        if($timea && $timeb){
            $timea = strtotime($timea);
            $timeb = strtotime($timeb);
            $where['a.time'] = array('between',array($timea,$timeb));
        }

        if($search_value){
            $where['u.nick_name'] = array('LIKE',"%$search_value%");;
        }
        if(!empty($type) && isset($type)){
            $where['a.status'] = $type;
        }
        $sort = i('sort');
        $order = i('order');
        if(!empty($sort)){
            $reorder = 'a.'.$sort." ".$order;
        }else{
            $reorder = 'a.id desc';
        }
        $list =  M('withdrawals')-> alias('a')
            ->join('users as u on u.id = a.user_id','left')
            -> field('a.*,u.nick_name')
            -> where($where)
            -> order($reorder)
            ->limit($offset,$limit)
            -> select();
//        dump(M('user_account')->getlastsql());
        foreach($list as &$v){

            $v['time'] = date('Y-m-d H:i:s',$v['time']);
            $v['status'] = $v['status']==1?'未处理':'已处理';


        }
//        dump($list);
        $count =  M('withdrawals')-> alias('a')
            ->join('users as u on u.id = a.user_id','left')
            -> where($where)
            -> order($reorder)
            -> count();
        $list_array= array("total"=>$count,"rows"=>$list?$list:array());
        echo json_encode($list_array);

    }
    //处理提现
    public function editCash(){

        $id = I('id');
        $info = M('withdrawals') -> where(['id'=> $id]) -> find();
        if(IS_POST){

            if($info['status'] == 2){
                $this->error('该条提现申请已经处理。');
            }

            $balance = M('users') -> where(['id' => $info['user_id']])->getfield('balance');

            if($balance > $info['amount']){
                $info['user_amount'] = $info['amount'];
            }else{
                $info['user_amount'] = floor($balance);
            }
            //确认提现，生成一条提现记录，并且扣掉响应的余额
            $tranDb = M();
            $tranDb -> startTrans();
            $res = $tranDb ->table('users') -> where(['id' => $info['user_id']])->setDec('balance',$info['user_amount']);
            if($res){
                //生成记录
                $pay['pay_no'] = get_pay_no();
                $pay['amount'] = $info['user_amount'];
                $pay['user_id'] = $info['user_id'];
                $pay['pay_time'] = time();
                $pay['pay_type'] = 2;
                $result = $tranDb ->table('user_account') -> add($pay);
                if($result){
                    $zhichu['pay_no'] =  $pay['pay_no'];
                    $zhichu['user_id'] =  $pay['user_id'];
                    $zhichu['pay_time'] =  $pay['pay_time'];
                    $zhichu['amount'] =  $info['user_amount'];
                    $zhichu['pay_type'] =  1;
                    $zhichu['pay_name'] =  '提现';
                    $result = $tranDb ->table('user_pay') -> add($zhichu);
                    if($result){
                        $info = $tranDb -> table('withdrawals') -> where(['id'=> $id])-> save(['status'=>2]);
                        if($info){
                            $tranDb -> commit();
                            $this->success('提现成功');
                        }else{
                            $tranDb->rollback();
                            $this->error('更新提现状态失败');
                        }
                    }else{
                        $tranDb->rollback();
                        $this->error('添加提现记录失败');
                    }
                }else{
                    $tranDb->rollback();
                    $this->error('添加流水失败');
                }
            }else{
                $tranDb->rollback();
                $this->error('会员余额扣减失败');
            }
        }else{
            //查询可以提现余额如果大于则按照记录来，如果小于则按照最大可提现的金额进行提现
            $balance = M('users') -> where(['id' => $info['user_id']])->getfield('balance');

            if($balance > $info['amount']){
               $info['user_amount'] = $info['amount'];
            }else{
                $info['user_amount'] = floor($balance);
            }

            $this->assign('info',$info);
            $this -> display();

        }

    }







}
