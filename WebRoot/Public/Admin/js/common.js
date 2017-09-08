$(function(){
    //处理窗口大小改变的事件
    $(window).resize(function () {
        var sender = $("#easyui-layout-fg");
        var offset = sender.offset();
        var new_height = $(window).height() - offset.top;
        if (new_height > 0) {
            $("#easyui-layout-fg").layout('resize', {
                height: (new_height)
            });
        }
    }).resize();
});
//图片格式器
function formatter_photo(value, row, index) {

    var column = $(this)[0];
    var column_value = row[column.field];

    if (column_value) {
        return "<img src='" + column_value + "' height='50' width='50'/>";
    }
    else {
        return "<img src='" + SITE_ROOT + "public/no_photo.png' height='50' width='50'/>";
    }
}