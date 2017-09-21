$(function(){
    $('.police_text input').blur(function(){
        var v_1=$('.police_text input').value;
        var reg = /^[\u4e00-\u9fa5]+$/i;
        if(!reg.v_1){
            _popup('请输入正确信息')
        }
    })
})