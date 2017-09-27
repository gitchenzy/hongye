$(function(){
	var n_0=$("div.helftop:eq(0)");
	var n_1=$(".helftop:eq(1)");
	var n_2=$("div.helftopbox:eq(0) a");
	var n_3=$("div.helftopbox:eq(1) a");
	var n_4=$("div.helftopbox:eq(0)"); 
	var n_5=$("div.helftopbox:eq(1)");
	n_1.click(function(){
		n_2.css("color","#e5e5e5");
		n_3.css("color","#f45353");
		n_4.css("border","none");
		n_5.css("border-bottom","2px solid #f45353");
		
	})
	n_0.click(function(){
		n_2.css("color","#f45353");
		n_3.css("color","#e5e5e5");
		n_4.css("border-bottom","2px solid #f45353");
		n_5.css("border-bottom","none");
		
	})
	
	
	
	
	
	
	
	
	
	
})