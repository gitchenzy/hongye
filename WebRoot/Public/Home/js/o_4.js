$(function(){
    $('.pay_pic:eq(1)').click(function(){
       if($('.pay_pic:eq(1)').attr('src')==='__PUBLIC__/Home/images/new/star_1.png'){
           $('.pay_pic:eq(1)').attr('src','__PUBLIC__/Home/images/new/stars.png');
       }else {
           $('.pay_pic:eq(1)').attr('src','__PUBLIC__/Home/images/new/star_1.png')
       }
    })
    $('.pay_pic:eq(0)').click(function(){
        $('.first_pic img').click(function(){
            $('first_box').css('display','block');
        });
        $('first_lowboxin:eq(0)').click(function(){
            $('first_box').css('display','none');
        });
    })
})