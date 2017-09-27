$(function() {
    function _popup(Msg, url) {
        $(".popup").remove();
        var sender = $("<div class='popup' style='z-index: 9999;position: fixed;top: 30%;left: 0;width: 100%;text-align: center;'><span class='popup_msg' style='display: none;padding: 10px;background: #f45353;border-radius:3px;z-index: 99999;color: #FFF;font-size: 16px;'>" + Msg + "</span></div>").appendTo("body");
        $(".popup_msg", sender).fadeIn();
        setTimeout(function () {
            sender.animate({top: '10px', opacity: 0}, function () {
                if (url != undefined && url) {
                    location.href = url;
                }
            });
        }, 1000);
    }
})