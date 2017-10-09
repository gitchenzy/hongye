<?php 
ini_set('date.timezone','Asia/Shanghai');
//error_reporting(E_ERROR);
require_once "../lib/WxPay.Api.php";
require_once "WxPay.JsApiPay.php";
require_once 'log.php';

//初始化日志
$logHandler= new CLogFileHandler("../logs/".date('Y-m-d').'.log');
$log = Log::Init($logHandler, 15);

//得到要付款的金额
$order_id = $_GET['id'];
$user_id = $_GET['user_id'];
$jine = $order_id*100;
//①、获取用户openid
$tools = new JsApiPay();
$openId = $tools->GetOpenid();

//②、统一下单
$input = new WxPayUnifiedOrder();
$input->SetBody("test");
$input->SetAttach($order_id.','.$user_id);
$input->SetOut_trade_no(WxPayConfig::MCHID.date("YmdHis"));
$input->SetTotal_fee($jine);
$input->SetTime_start(date("YmdHis"));
$input->SetTime_expire(date("YmdHis", time() + 600));
$input->SetGoods_tag("test");
$input->SetNotify_url("http://www.hybbms.com/weixinpay/example/chongzhi_notify.php");
$input->SetTrade_type("JSAPI");
$input->SetOpenid($openId);
$order = WxPayApi::unifiedOrder($input);
//printf_info($order);
$jsApiParameters = $tools->GetJsApiParameters($order);

//echo $jsApiParameters;
//获取共享收货地址js函数参数
$editAddress = $tools->GetEditAddressParameters();

//③、在支持成功回调通知中处理成功之后的事宜，见 notify.php
/**
 * 注意：
 * 1、当你的回调地址不可访问的时候，回调通知会失败，可以通过查询订单来确认支付是否成功
 * 2、jsapi支付时需要填入用户openid，WxPay.JsApiPay.php中有获取openid流程 （文档可以参考微信公众平台“网页授权接口”，
 * 参考http://mp.weixin.qq.com/wiki/17/c0f37d5704f0b64713d5d2c37b468d75.html）
 */
?>

<html>
<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/> 
    <title>立即支付</title>
    <script type="text/javascript">
	//调用微信JS api 支付
	function jsApiCall()
	{
		WeixinJSBridge.invoke(
			'getBrandWCPayRequest',
			<?php echo $jsApiParameters; ?>,
			function(res){
				WeixinJSBridge.log(res.err_msg);
				//alert(res.err_code+res.err_desc+res.err_msg);

                if(res.err_msg == "get_brand_wcpay_request:ok"){
                    //alert(res.err_code+res.err_desc+res.err_msg);
                    window.location.href="/index.php/list/succ?id=3";
                }else{
                    //返回跳转到订单详情页面
                    window.location.href="/index.php/list/succ?id=4";

                }
			}
		);
	}

	function callpay()
	{
		if (typeof WeixinJSBridge == "undefined"){
		    if( document.addEventListener ){
		        document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
		    }else if (document.attachEvent){
		        document.attachEvent('WeixinJSBridgeReady', jsApiCall); 
		        document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
		    }
		}else{
		    jsApiCall();
		}
	}
	</script>
	<script type="text/javascript">
	//获取共享地址
	function editAddress()
	{
		WeixinJSBridge.invoke(
			'editAddress',
			<?php echo $editAddress; ?>,
			function(res){
				var value1 = res.proviceFirstStageName;
				var value2 = res.addressCitySecondStageName;
				var value3 = res.addressCountiesThirdStageName;
				var value4 = res.addressDetailInfo;
				var tel = res.telNumber;
				
			//	alert(value1 + value2 + value3 + value4 + ":" + tel);
			}
		);
	}
	
	window.onload = function(){
		if (typeof WeixinJSBridge == "undefined"){
		    if( document.addEventListener ){
		        document.addEventListener('WeixinJSBridgeReady', editAddress, false);
		    }else if (document.attachEvent){
		        document.attachEvent('WeixinJSBridgeReady', editAddress); 
		        document.attachEvent('onWeixinJSBridgeReady', editAddress);
		    }
		}else{
			editAddress();
		}
	};
	
	</script>
</head>
<style>
    *{
        margin:0px;
        padding:0px;
        list-style:none;
        text-decoration: none;

    }
    body{
        background: #f2f2f2;
    }
    .page{
        height: auto;
    }
    .tran{
        width:100%;;
        height: 70px;
        margin-bottom: 25px;
    }
    .tran_in{
        width:auto;
        height: 70px;
        margin: 25px auto 10px auto;
        text-align: center;
    }
    .tran_in p{
        font-size: 15px;
        color: #181818;
        line-height: 25px;
    }
    .tran_in h1{
        font-size: 36px;
        color: #000000;
    }
    .tran_b{
        width:100%;
        height: 89px;
        background: #ffffff;
    }
    .tran_bin{
        background: #ffffff;
        margin: 0 15px 0 15px;
        height: 44px;
        border-bottom: 1px solid #e5e5e5;
    }
    .tran_bin p{
        font-size: 14px;
        color: #a9a9a9;
        display: inline-block;
        line-height: 45px;
    }
    .tran_bin span{
        font-size: 14px;
        color: #000000;
        float:right;
        line-height: 45px;
    }
    .tran_last{
        margin:25px 15px 0 15px;
        height: 45px;
        border-radius: 3px;
        background: #d33d3e;
        text-align: center
    }
    .tran_last p{
        font-size: 16px;
        color: #ffffff;
        line-height: 45px;
    }

</style>
<body>
    <br/>
    <div class="page">
        <div class="tran">
            <div class="tran_in">
                <p>厦门红叶帮帮忙科技有限公司</p>
                <h1>¥<?php echo $order_id ?></h1>
            </div>
        </div>
        <div class="tran_b">
            <div class="tran_bin">
                <p>厦门红叶帮帮忙科技有限公司</p>
                <span>红叶筹</span>
            </div>
<!--            <div style="border:none;"class="tran_bin">-->
<!--                <p>订单号</p>-->
<!--                <span>--><?php //echo $list['order_no'] ?><!--</span>-->
<!--            </div>-->
        </div>
        <div class="tran_last">
            <p onclick="callpay();">立即支付</p>
        </div>
    </div>

<!--    <font color="#9ACD32"><b>该笔订单支付金额为<span style="color:#f00;font-size:50px">1分</span>钱</b></font><br/><br/>-->
<!--	<div align="center">-->
<!--		<button style="width:210px; height:50px; border-radius: 15px;background-color:#FE6714; border:0px #FE6714 solid; cursor: pointer;  color:white;  font-size:16px;" type="button" onclick="callpay()" >立即支付</button>-->
<!--	</div>-->
</body>

</html>