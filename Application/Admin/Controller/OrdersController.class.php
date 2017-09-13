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
        if($search_value){
            $where['a.pay_no'] = array('LIKE',"%$search_value%");;
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
            }else{
                $v['pay_type'] = '项目打款';
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









}
