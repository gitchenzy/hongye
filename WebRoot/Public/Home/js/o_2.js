$(function(){
	$('.first_pic img').click(function(){
		$('first_box').css('display','block');
	})
	$('first_lowboxin:eq(0)').click(function(){
		$('first_box').css('display','none');
	})
	/*$('first_lowboxin:eq(1)').click(function(){
		$ajax()
	})*/
})