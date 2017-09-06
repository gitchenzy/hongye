$(function(){
	var re_0=$("#r_1>div.helftopbox");
	var re_1=$("#r_1>.helftopbox a");
	var re_2=$(".helftopbox:eq(0)");
	var re_3=$("#re_0 a");
	
		$("#r_1").click(function(){
		re_1.css("color","#D33D3E");
		re_0.css("border-bottom","2px solid #d33d3e");
		re_2.css("border","none");
		re_3.css("color","#CCCCCC");
		
	})
	$("#re_0").click(function(){
		re_1.css("color","#CCCCCC");
		re_0.css("border","none");
		re_2.css("border-bottom","2px solid #d33d3e");
		re_3.css("color","#D33D3E");
		
		
	})
	
	
})