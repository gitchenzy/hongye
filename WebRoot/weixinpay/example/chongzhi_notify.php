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
        $jine = $data['attach'];
		$arr = explode(',',$jine);
        $mysql = new mysqli('127.0.0.1','root','2017HYbbms.com','hongye');
        $mysql -> autocommit(FALSE);
        $time = time();
        //生成充值记录获取pay_no
        $times = strtotime(date('Y-m-d'));
        $sql = 'select pay_no from user_account where pay_time > '.$times.' order by id desc limit 0,1';
        $res = $mysql -> query($sql);
        $account_no  = $res->fetch_assoc();
        $sql = 'select order_no from orders where order_no > '.$times.' order by id desc limit 0,1';
        $res = $mysql -> query($sql);
        $order_no  = $res->fetch_assoc();
        if($account_no || $order_no){
            if($account_no && !$order_no){
                $no = $account_no['pay_no'] + 1;
            }else if(!$account_no && $order_no){
                $no = $order_no['order_no'] + 1;
            }else{
                if($account_no['pay_no'] < $order_no['order_no']){
                    $no = $order_no['order_no'] + 1;
                }else{
                    $no = $account_no['pay_no'] + 1;
                }
            }
        }else{
            $no = date('Ymd').'00001';
        }
        $sql = "insert into user_account(pay_no,user_id,amount,pay_time) value({$no},{$arr[1]},{$arr[0]},{$time})";
        $res = $mysql -> query($sql);

        if($res){
            //添加个人明细
            $sql = "insert into user_pay(user_id,pay_no,amount,pay_time,pay_name,pay_type) value({$arr[1]},{$no},{$arr[0]},{$time},'充值',2)";
            $res = $mysql -> query($sql);
            if($res){
                //修改个人账户余额
                $sql = 'update users set balance = balance + '.$arr[0].' where id ='.$arr[1];
                $res = $mysql -> query($sql);
                if($res){
                    $mysql->commit(); //提交事务
                    $mysql->autocommit(TRUE); //开启自动提交功能
                    return true;

                }else{
                    $msg = "修改个人账号细失败";
                    $mysql -> rollback();
                    Log::DEBUG("error log:" . $msg);
                    return false;
                }

            }else{
                $msg = "添加个人明细失败";
                $mysql -> rollback();
                Log::DEBUG("error log:" . $msg);
                return false;
            }


        }else{
            $msg = "添加流水账失败";
            $mysql -> rollback();
            Log::DEBUG("error log:" . $msg);
            return false;
        }

	}
}

Log::DEBUG("begin notify");
$notify = new PayNotifyCallBack();
$notify->Handle(false);
