<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no"/>
    <link rel="stylesheet" type="text/css" href="/Public/Home/css/main.css">
    <script type="text/javascript" src="/Public/Home/js/jquery-1.7.1.min.js"></script>
    <script type="text/javascript" src="/Public/Home/js/jquery.event.drag-1.5.min.js"></script>
    <script type="text/javascript" src="/Public/Home/js/jquery.touchSlider.js"></script>
    <script src="/Public/Home/js/o_4.js"></script>
    <title>红叶帮帮忙</title>
</head>
<body>

<div style="border-bottom:1px solid #cccccc" class="top">
    <div onclick="window.location.href='mine.html'" class="top_arrow"></div>
    <h4>实名认证</h4>
</div>
<div style="border-bottom:1px #CCCCCC solid;" class="top">
    <div class="top">
        <div class="helftop">
            <div class="midtopbox">
            </div>
            <div style="width:40px" class="helftopbox">
                <h1><a href="#">个人</a></h1>
            </div>
        </div>
        <div class="helftop">
            <div style="border:none;width:40px" class="helftopbox">
                <h1 id="activa"><a  style="color:#CCCCCC" href="#">机构</a></h1>
            </div>
        </div>
    </div>
</div>
<div class="police">
    <div class="police_in">
        <div class="police_left">
            <div class="police_text">
                <p>真实姓名:</p>
                <input type="text" value="请输入姓名">
            </div>
            <div class="police_text">
                <p>身份证号码:</p>
                <input type="text" value="请输入身份证号">
            </div>
            <div style="border:none;" class="police_text">
                <p>手机号：</p>
                <input type="text" value="请输入手机号">
            </div>
        </div>
        <div class="police_img">
        </div>
    </div>
</div>
<div class="police_blow">
    <div class="police_bin">
        <p>验证码：</p>
        <input type="text" value="请输入验证码">
        <button class="police_lbtn" type="submit">获取验证码</button>
    </div>
</div>
<div class="police_bg">
    <button type="submit" class="police_bbtn">提交</button>
</div>
</body>
</html>