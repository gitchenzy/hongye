<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no"/>

    <link rel="stylesheet" type="text/css" href="/Public/Home/css/main.css" />


    <title>红叶帮帮忙</title>
</head>

<body>





    <div style="border-bottom:1px solid #cccccc" class="top">
        <div class="top_arrow"></div>
        <h4>我的关注</h4>
    </div>
    <div class="stantic_3">
        <div class="stantic_3_in">
            <div class="stantic_3_in_text">
                <p>2017.09.09</p>
                <p>09:09</p>
            </div>
            <div class="stantic_lang_2_img">
                <img src="/Public/Home/images/img_1.jpg">
            </div>
            <div class="stantic_lang_2_text">

                <h3>中国人的萌</h3>

                <div class="stantic_lang_2_icon">
                </div>
                <span>中国人的笛煽风点火的方式 </span>
            </div>
        </div>
    </div>
    <div class="stantic_3">
        <div class="stantic_3_in">
            <div class="stantic_3_in_text">
                <p>2017.09.09</p>
                <p>09:09</p>
            </div>
            <div class="stantic_lang_2_img">
                <img src="/Public/Home/images/img_1.jpg">
            </div>
            <div class="stantic_lang_2_text">

                <h3>中国人的萌</h3>

                <div class="stantic_lang_2_icon">
                </div>
                <span>中国人的笛煽风点火的方式</span>
            </div>
        </div>
    </div>
    <div class="stantic_3">
        <div class="stantic_3_in">
            <div class="stantic_3_in_text">
                <p>2017.09.09</p>
                <p>09:09</p>
            </div>
            <div class="stantic_lang_2_img">
                <img src="/Public/Home/images/img_1.jpg">
            </div>
            <div class="stantic_lang_2_text">

                <h3>中国人的萌</h3>

                <div class="stantic_lang_2_icon">
                </div>
                <span>中国人的笛煽风点火的方式</span>
            </div>
        </div>
    </div>
    



<div class="main_botoom">
    <div class="main_bottom_box">
        <div class="main_bottom_box_mid">

            <a href="index.html"><img width="30px" height="30px" src="/Public/Home/images/new/home_2.png"></a>

            <a style="color:#D33D3E" href="index.html">主页</a>
        </div>
    </div>
    <div class="main_bottom_box">
        <div class="main_bottom_box_mid">

            <a href="others/find.html"><img src="/Public/Home/images/new/find_1.png"></a>

            <a href="others/find.html">发现</a>
        </div>

    </div>
    <div onclick="window.location.href='others/original.html'" class="main_bottom_box">
        <div class="main_bottom_box_mid_icon">

            <a href="#"><img src="/Public/Home/images/new/go.png"></a>


        </div>
    </div>
    <div class="main_bottom_box">
        <div class="main_bottom_box_mid">

            <a href="others/news.html"><img src="/Public/Home/images/new/news_1.png"></a>

            <a href="">消息</a>
        </div>
    </div>
    <div class="main_bottom_box">
        <div class="main_bottom_box_mid">

            <a href="<?php echo U('Center/center');?>"><img src="/Public/Home/images/new/mine_1.png"></a>

            <a href="<?php echo U('Center/center');?>">我的</a>
        </div>
    </div>

</div>
<div class="blank">
</div>


<script tyep="text/javascript" src="/Public/Home/js/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="/Public/Home/js/jquery.event.drag-1.5.min.js"></script>
<script type="text/javascript" src="/Public/Home/js/jquery.touchSlider.js"></script>
<script type="text/javascript" src="/Public/Home/js/main.js"></script>
<script type="text/javascript">
    $(document).ready(function(){

        $(".main_visual").hover(function(){
            $("#btn_prev,#btn_next").fadeIn()
        },function(){
            $("#btn_prev,#btn_next").fadeOut()
        });

        $dragBln = false;

        $(".main_image").touchSlider({
            flexible : true,
            speed : 200,
            btn_prev : $("#btn_prev"),
            btn_next : $("#btn_next"),
            paging : $(".flicking_con a"),
            counter : function (e){
                $(".flicking_con a").removeClass("on").eq(e.current-1).addClass("on");
            }
        });

        $(".main_image").bind("mousedown", function() {
            $dragBln = false;
        });

        $(".main_image").bind("dragstart", function() {
            $dragBln = true;
        });

        $(".main_image a").click(function(){
            if($dragBln) {
                return false;
            }
        });

        timer = setInterval(function(){
            $("#btn_next").click();
        }, 2000);

        $(".main_visual").hover(function(){
            clearInterval(timer);
        },function(){
            timer = setInterval(function(){
                $("#btn_next").click();
            },2000);
        });

        $(".main_image").bind("touchstart",function(){
            clearInterval(timer);
        }).bind("touchend", function(){
            timer = setInterval(function(){
                $("#btn_next").click();
            }, 2000);
        });

    });
</script>







</body>
</html>