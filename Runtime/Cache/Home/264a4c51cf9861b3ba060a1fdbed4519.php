<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no"/>
    <link rel="stylesheet" type="text/css" href="/Public/Home/css/main.css">
    <title>红叶帮帮忙</title>
</head>
<body>

<div style="border-bottom:1px solid #cccccc" class="top">
    <div onclick="window.location.href='mine.html'" class="top_arrow"></div>
    <h4>我的钱包</h4>
</div>
<div class="cash_card">
    <div class="cash_card_1">
        <h1>账户余额</h1>
    </div>
    <div class="cash_card_2">
        <h2>¥&nbsp;10</h2>

    </div>
    <div class="cash_card_3">
        <div class="cash_card_btn">
            <div class="cash_btn_1">
                <p>提现</p>
            </div>
            <div class="cash_btn_2">
                <p>充值</p>
            </div>
        </div>
    </div>
</div>
<div class="cash_b">
    <div onclick="window.location.href='notes.html'" class="cash_bin">
        <div class="cash_bin25">
            <img src="/Public/Home/images/交易明细.png">
        </div>
        <p>交易明细</p>
        <div class="cash_bin2">
            <img src="/Public/Home/images/返回.png">
        </div>
    </div>
    <div  onclick="window.location.href='notes.html'" class="cash_bin">
        <div class="cash_bin25">
            <img src="/Public/Home/images/银行卡.png">
        </div>
        <p>我的银行卡</p>
        <div class="cash_bin2">
            <img src="/Public/Home/images/返回.png">
        </div>
    </div>
</div>


</body>
</html>