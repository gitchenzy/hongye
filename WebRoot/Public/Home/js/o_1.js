$(function(){
    var chk=$('.original_in>ul li>input');
    chk.click(function(){
        if(chk.is(':checked')){
            chk.not(':checked').attr('disabled','ture');

        }
    })

})