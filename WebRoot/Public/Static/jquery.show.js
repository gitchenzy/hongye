/**
 * Created by fely on 3/24/2015.
 */
function loadData(action, callBack ) {
    jQuery.ajax({
        url : REMOTE_SERVER + action ,
        cache : false ,
        dataType : 'json' ,
        error : function(sender, message) {
            _popup("连接服务器失败，可能是没有网络");
        },
        success : function(data) {
            if (data.status === 0) {
                _popup(data.info);
                return ;
            }
            if (typeof  callBack == "function") {
                callBack(data.info);
            }
        },
        timeout : 10000
    });
}

//
function loadJson(action, callBack) {
    jQuery.ajax({
        url : REMOTE_SERVER + action ,
        cache : false ,
        dataType : 'json' ,
        error : function(sender, message) {
            _popup('连接服务器失败，可能是没有网络');
        },
        success : function(data) {
            callBack(data);
        },
        timeout : 10000
    });
}

//弹出层
function _popup(Msg, url){
    $(".popup").remove();
    var sender = $("<div class='popup' style='z-index: 9999;position: fixed;top: 15%;left: 0;width: 100%;text-align: center;'><span class='popup_msg' style='display: none;padding: 19px 67px;background: #5cb85c;z-index: 99999;color: #FFF;font-size: 18px;'>" + Msg + "</span></div>").appendTo("body");
    $(".popup_msg", sender).fadeIn();
    setTimeout(function() {
        sender.animate({ top : '10px' , opacity : 0}, function() {
            if (url != undefined && url) {
                location.href = url;
            }
        });
    }, 1000);
}



var $_GET = (function(){
    var url = window.document.location.href.toString();
    var u = url.split("?");
    if(typeof(u[1]) == "string"){
        u = u[1].split("&");
        var get = {};
        for(var i in u){
            var j = u[i].split("=");
            get[j[0]] = j[1];
        }
        return get;
    } else {
        return {};
    }
})();

function randDecimal($value, $decimal) {

///
    $value = Math.ceil($value * Math.pow(10, $decimal));
    return $value / Math.pow(10, $decimal);
}


$(function() {
    //初始化日期控件
    $("body").on("click", ".date-input",function() {
        var sender = $(this);
        top.showPicker(function(date){
            sender.val(date.getFullYear()+"-"+(date.getMonth()+1)+"-"+date.getDate());
        });
    });
});
