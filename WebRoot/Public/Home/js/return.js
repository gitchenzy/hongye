
$(function(){
    function _popup(Msg, url) {
        //$(".popup").remove();
        var sender = $("<div class='popup' style='z-index: 9999;position: fixed;top: 10%;left: 0;width: 100%;text-align: center;'><span class='popup_msg' style='display: none;padding: 10px;background: #d33d3e;border-radius:3px;z-index: 99999;color: #FFF;font-size: 16px;'>" + Msg + "</span></div>").appendTo("body");
        $(".popup_msg", sender).fadeIn();
        setTimeout(function () {
            sender.animate({top: '10px', opacity: 0}, function () {
                if (url != undefined && url) {
                    location.href = url;
                }
            });
        }, 1500);
    }
    function open_box(){
        $('.background').css('display','block')
        $('.tan').css('display','block')
    }
    function close_box(){
        $('.background').css('display','none')
        $('.tan').css('display','none')
    }

    function plus(){
        var datas=$('.return_re>ul li>input');
        var arr=[datas[0].value,datas[1].value,datas[2].value,datas[3].value]
        if(!arr[0]||!arr[1]||!arr[2]||!arr[3]){
            _popup('请输入正确的信息','')

        }/*else{
            var conts=$('<div class="return_all"><div class="return_in"><p>支持金额</p>'+arr[0]+'</div><div class="return_in"><p>回报标题</p>'+arr[1]+'</div><div class="return_bin"><p>回报简介</p><div class="return_b">'+arr[2]+'</div></div><div style="border:none" class="return_bin"><p>回报简介</p><div class="return_b">'+arr[3]+'</div></div>')
            $('.page').append(conts);
            close_box();
        }*/

    }
    var n=3;
    function plus_little(){
        var t=$('.return_re>ul li:eq(3)');
        var time = $('#time').val();
        var str = 'rule_'+time;
        // console.log(time);
        var t=$('.return_re>ul li:eq(3)');
        var cont=$('<li><p style="float:left">规则设置</p><input style="width:150px;float:left;position:relative;top:5px;"value="" id='+ str +' type="text" placeholder="设置回报规则"></li>')

        $('#time').val(parseInt(time) + 1)
        n+=1;
        if(n===7){
            $('.return_add').unbind('click');
        }
        var x=n-1;
        t=$('.return_re>ul li:eq('+x+')');
        y=$('.return_re>ul li:eq('+n+')');
        t.after(cont);
    }
    $('.return_add').bind('click',plus_little);
    $('#upload').click(plus);
    $('#add').bind('click',open_box);
    $('.in_return_r').bind('click',close_box)


})