/**
 * Created by fely on 1/28/15.
 */

(function ($) {
    $.fn.clickout = function(callBack) {
        $(this).each(function(i){
            var sender = this;
            $(document).mousedown(function(e){
                var target = e.target || e.srcElement;
                while (target != document && target != sender) {
                    target = target.parentNode;
                }
                if (target != sender && typeof callBack == "function") {
                    callBack(e);
                }
            });
        });
    };

    /*fetch the checkbox value*/
    $.checkboxValue = function(checkboxName) {
        var c_array = Array();
        $('input[name=' + checkboxName + ']:checked').each(function(i) {
            c_array.push($(this).val());
        });
        return c_array.join(',');
    };
})(jQuery);