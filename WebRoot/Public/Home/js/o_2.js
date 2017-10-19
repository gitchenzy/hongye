$(function(){
	$('.first_pic img').click(function(){

		var pid = $(this).attr('key');
		$('#pid').val(pid);
		$('.first_box').css('display','block');
	})
	$('.first_lowboxin:eq(0)').click(function(){
		$('.first_box').css('display','none');
	})
	$('.first_box textarea').focus(function(){
		if($('.first_box textarea').html()==='我也说一句'){
			$('.first_box textarea').html('');
		}
	})
	/*$('first_lowboxin:eq(1)').click(function(){
		$ajax()
	})*/
    $('.sub').click(function(){
        var content = $('#content').val();
        if(!content){
            alert('评论的内容不能为空！');
            return false;
        }
        var pid = $('#pid').val();
        var project_id = $('#project_id').val();
        var info = {content:content,project_id:project_id,pid:pid}
        $.post('/index.php/List/comments',info,function(datas){
            if(datas.info == 1){
                alert('请先登陆！');
                location.href = '/index.php/Login/index';
            }else{
               alert(datas.info);
                location.reload();
                $('.first_box').css('display','none');
            }
        })
    })


})