$(function(){
	$('.first_pic img').click(function(){
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
})