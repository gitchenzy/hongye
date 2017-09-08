$(function(){
var w_1=$(".helftopbox:eq(1)");
var w_2=$(".helftopbox:eq(1)>h1 a");
var w_3=$(".helftopbox:eq(0)");
var w_4=$(".helftopbox:eq(0)>h1 a");
var w_5=$(".pay_pic:eq(0)>img");
var w_6=$(".pay_pic:eq(1)>img");
w_1.click(function(){
    w_1.css("border-bottom","2px solid #D33D3E");
    w_3.css("border","none");
    w_4.css("color","#CCCCCC");
    w_2.css("color","#D33D3E");
})
w_3.click(function(){
    w_1.css("border-bottom","none");
    w_3.css("border-bottom","2px solid #D33D3E");
    w_4.css("color","#D33D3E");
    w_2.css("color","#CCCCCC"); 
})
w_5.click(function(){
    if(w_5.attr("src")=="__PUBLIC__/Home/images/评论.png"){
        w_5.attr("src","__PUBLIC__/Home/images/评论1.png");
    }else{
        w_5.attr("src","__PUBLIC__/Home/images/评论.png");
    }
})
w_6.click(function(){
    if(w_6.attr("src")=="__PUBLIC__/Home/images/关注2.png"){
        w_6.attr("src","__PUBLIC__/Home/images/关注1.png");
    }else{
        w_6.attr("src","__PUBLIC__/Home/images/关注2.png");
    }
})

})