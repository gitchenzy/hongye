/**
 * Created by Administrator on 2016/3/16 0016.
 */
//toast 初始化
//toastr.options.positionClass = 'toast-top-center';
toastr.options = {
    "closeButton": true,
    "debug": true,
    "progressBar": true,
    "positionClass": "toast-top-center",
    "showDuration": "400",
    "hideDuration": "1000",
    "timeOut": "3000",
    "extendedTimeOut": "1000",
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut"
};
function responseHandler(res) {
    $.each(res.rows, function (i, row) {
        row.state = $.inArray(row.id, selections) !== -1;
    });
    return res;
}

function detailFormatter(index, row) {
    var html = [];
    $.each(row, function (key, value) {
        html.push('<p><b>' + key + ':</b> ' + value + '</p>');
    });
    return html.join('');
}

function getHeight() {
    return $(window).height()*0.8;
}
var eachSeries = function (arr, iterator, callback) {
    callback = callback || function () {};
    if (!arr.length) {
        return callback();
    }
    var completed = 0;
    var iterate = function () {
        iterator(arr[completed], function (err) {
            if (err) {
                callback(err);
                callback = function () {};
            }
            else {
                completed += 1;
                if (completed >= arr.length) {
                    callback(null);
                }
                else {
                    iterate();
                }
            }
        });
    };
    iterate();
};
function getScript(url, callback) {
    var head = document.getElementsByTagName('head')[0];
    var script = document.createElement('script');
    script.src = url;
    var done = false;
    script.onload = script.onreadystatechange = function() {
        if (!done && (!this.readyState ||
            this.readyState == 'loaded' || this.readyState == 'complete')) {
            done = true;
            if (callback)
                callback();
            script.onload = script.onreadystatechange = null;
        }
    };
    head.appendChild(script);
    return undefined;
}
function ajaxReturnMark(o){
    return o;
}
function successTips(msg){
    swal({
        title:"操作成功！",
        text:msg,
        type:"success"
    });
}
function confirmDelete(url,param,tableObj,delObj,field,ids){
    swal({
        title: "您确定要删除这些信息吗？",
        text: "删除后将无法恢复，请谨慎操作！",
        type: "warning",
        showCancelButton: true,
        cancelButtonText:"让我再考虑一下…",
        confirmButtonColor: "#1ab394",
        confirmButtonText: "删除",
        closeOnConfirm: false
    }, function () {
        $.ajax({
            url: url,
            data:param,
            dataType:"json",
            type: "post",
            async:false
        }).done(function(data) {
            if(data.status==1){
                swal({
                    title:"删除成功！",
                    text:"您已经永久删除了这些信息。",
                    type:"success",
                    showConfirmButton:false,
                    timer:1000,
                });
                tableObj.bootstrapTable('remove', {
                    field: field,
                    values: ids
                });
                delObj.prop('disabled', true);
                tableObj.bootstrapTable('refresh', {silent: true});
            }
        }).error(function(data) {
            swal("删除失败！", "删除操作失败了!", "error");
        });
    });
}
function getcustomer(){
}
