$(function(){

    var btn=$('.bind_box_in button');
    btn.click(function(){
        var n=60;
        var time=setInterval(function(){
            n--;
            if(n>0){
                btn.html(n)
            }else{
                clearInterval(time)
            }
            if(n===0){
                btn.html('重新获取')
            }
            var num=btn.html();
            if(num!=='获取验证码'||num!=='重新获取'){
                btn.attr('disabled','ture')
            }
            if(num==='重新获取'){
                btn.removeAttr('disabled')
            }

        },1000);

    })

})