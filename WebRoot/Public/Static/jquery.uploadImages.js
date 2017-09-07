function getElementOffset(e){
    var t = e.offsetTop;
    var l = e.offsetLeft;
    var w = e.offsetWidth;
    var h = e.offsetHeight-1;

    while(e=e.offsetParent) {
        t+=e.offsetTop;
        l+=e.offsetLeft;
    }
    return {
        top : t,
        left : l,
        width : w,
        height : h
    }
}
function rename_pic(o){
    var info = $(o).parent();
    var info_txt = $(o).text();
    var str=$(o).parent().parent().attr('data-path');
    var arr=str.split('+');
    var path=arr[0];
    info.html('<input id="input_id" style="width: 98px;text-align: left;" type="text" value="'+info_txt+'" class="ipt_2" />');
    var input = $('#input_id');
    input.focus();
    input.select();
    input.blur(
        function(){
            if(info_txt!=this.value){
                var album=$('#album').val();
                var str1=path+'+'+info_txt;
                var str2=path+'+'+this.value;
                str=album.replace(str1,str2);
                //alert(str1+str2);
                info.html('<a onclick="rename_pic(this)">'+this.value+'</a>');
                $('#album').val(str);
            }else{
                info.html('<a onclick="rename_pic(this)">'+this.value+'</a>');
            }
        }
    );
}
function rename_pic1(o){
    var info = $(o).parent().parent().find('.info');
    var info_txt = info.text();
    var path=$(o).parent().parent().attr('data-path');
    info.html('<input id="input_id" style="width: 98px;text-align: left;" type="text" value="'+info_txt+'" class="ipt_2" />');
    var input = $('#input_id');
    input.focus();
    input.select();
    input.blur(
        function(){
            if(info_txt!=this.value){
                var album=$('#album').val();
                var arr=path.split('+');
                var str2=arr[0]+'+'+this.value;
                str=album.replace(path,str2);
                info.html('<a onclick="rename_pic(this)">'+this.value+'</a>');
                $('#album').val(str);
            }else{
                info.html('<a onclick="rename_pic(this)">'+this.value+'</a>');
            }
        }
    );
}
function del_pic(o){
    var parent=$(o).parent().parent();
    var str=parent.attr('data-path');
    var arr=str.split('+');
    var path=arr[0];
    if(confirm('确定要删除吗？')){
        $.ajax({
            type: "POST",   //访问WebService使用Post方式请求
            url: '{:U("Room/del_pic")}', //调用WebService的地址和方法名称组合---WsURL/方法名
            data:"path="+encodeURI(path),
            success: function(data){
                parent.animate({opacity: 'hide' }, "slow");
                var album=$('#album').val();
                var tmp=album.replace(str+',','');
                var tmp1=tmp.replace(','+str,'');
                var tmp2=tmp1.replace(str,'');
                $('#album').val(tmp2);
            }
        });
    }
}
function set_pic_cover(o){
    var str=$(o).parent().parent().attr('data-path');
    var arr=str.split('+');
    $('#thumb_path').val(arr[0]);
    var pos = getElementOffset($(o).parent().parent().find('span.img').get(0));
    //var pos = getElementOffset(o);
    $('#info_msg').css('top',pos.top+20);
    $('#info_msg').css('left',pos.left);
    //$('#info_msg').css('top',pos.top-50);
    $('#info_msg').html('已设为封面');
    //$('#info_msg').show();
    $('#info_msg').show().animate({opacity: 1.0}, 1000).fadeOut();
    $("div[class='selected']").hide();
    $(o).parent().parent().find("div[class='selected']").show();
}