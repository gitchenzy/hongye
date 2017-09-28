<?php
ini_set('date.timezone','Asia/Shanghai');
error_reporting(E_ERROR);

require_once "../lib/WxPay.Api.php";
require_once '../lib/WxPay.Notify.php';
require_once 'log.php';

//初始化日志
$logHandler= new CLogFileHandler("../logs/".date('Y-m-d').'.log');
$log = Log::Init($logHandler, 15);

class PayNotifyCallBack extends WxPayNotify
{
	//查询订单
	public function Queryorder($transaction_id)
	{
		$input = new WxPayOrderQuery();
		$input->SetTransaction_id($transaction_id);
		$result = WxPayApi::orderQuery($input);
		Log::DEBUG("query:" . json_encode($result));
		if(array_key_exists("return_code", $result)
			&& array_key_exists("result_code", $result)
			&& $result["return_code"] == "SUCCESS"
			&& $result["result_code"] == "SUCCESS")
		{
			return true;
		}
		return false;
	}
	
	//重写回调处理函数
	public function NotifyProcess($data, &$msg)
	{
		Log::DEBUG("call back:" . json_encode($data));

		//var_dump($data);
		$notfiyOutput = array();
		
		if(!array_key_exists("transaction_id", $data)){
			$msg = "输入参数不正确";
			return false;
		}
		//查询订单，判断订单真实性
		if(!$this->Queryorder($data["transaction_id"])){
			$msg = "订单查询失败";
			return false;
		}
		//有产生订单就是已经支付成功 则生成一定支付记录，并且修改订单的支出状态
        $order_id = $data['attach'];
        $mysql = new mysqli('localhost','root','2017hybbms.com','hongye');
        $mysql -> autocommit(FALSE);
        $time = time();
        Log::DEBUG("call sql:" . $order_id);
        $sql = 'update orders set pay_time = '.$time.',status = 2 where id ='.$order_id;
        Log::DEBUG("call sql:" . $sql);
        $res = $mysql -> query($sql);
        if($res){
            $sql = "select  * from orders WHERE id = {$order_id}";
            $res = $mysql -> query($sql);
            $list  = $res->fetch_assoc();
            $mysql -> set_charset("utf8");
            $sql = "insert into user_pay(user_id,pay_no,amount,pay_time,pay_name) value({$list['user_id']},{$list['order_no']},{$list['pay_amount']},{$time},'付款')";
            Log::DEBUG("call sql:" . $sql);
            $res = $mysql -> query($sql);
            if($res){
                $sql = 'update project set people_num = people_num + 1,reach_amount = reach_amount + '.$list['pay_amount'].' where id ='.$list['project_id'];
                $result = $mysql -> query($sql);
                if($result){
                    //更新会员的信息
                    $sql = 'update users set amount = amount + '.$list['pay_amount'].' where id ='.$list['user_id'];
                    $result = $mysql -> query($sql);
                    if($result){
                        $mysql->commit(); //提交事务
                        $mysql->autocommit(TRUE); //开启自动提交功能
                        return true;
                    }else{
                        $msg = "会员信息更新失败";
                        $mysql -> rollback();
                        return false;
                    }


                }else{
                    $msg = "项目更新失败";
                    $mysql -> rollback();
                    return false;
                }

            }else{
                $msg = "订单流水添加失败";
                $mysql -> rollback();
                return false;
            }
        }else{
            $msg = "订单更新失败";
            $mysql -> rollback();
            return false;
        }

	}
}

Log::DEBUG("begin notify");
$notify = new PayNotifyCallBack();
$notify->Handle(false);
