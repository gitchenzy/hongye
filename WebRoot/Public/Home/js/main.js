
$(function(){
	$("#c2").click(function(){
	  $("#c2>.helftopbox").css("border-bottom","2px solid #d33d3e");
	  $("#ac").css({"border":"none","color":"#D33D3E"});
	  $("#c1").css("border","none");
	  $("#c1 a").css("color","#CCCCCC");	
	  $("#img1").attr("src","__PUBLIC__/Home/imagesnew/farm.png");
	  $("#img2").attr("src","__PUBLIC__/Home/imagesnew/create.png");
      $("#img3").attr("src","__PUBLIC__/Home/imagesnew/live.png");
	  var txt_1=$("<a>农业</a>");
	  var txt_2=$("<a>创意</a>");
	  var txt_3=$("<a>生活</a>");
	 
	  txt_1.css({"font-size":"13px","text-align":"center"});
	  txt_2.css({"font-size":"13px","text-align":"center"});
	  txt_3.css({"font-size":"13px","text-align":"center"});
	  $("a:contains('公益')").replaceWith(txt_1);
	  $("a:contains('拼团')").replaceWith(txt_2);
      $("a:contains('愿望')").replaceWith(txt_3);
	  
	})
	$("#c4").click(function(){
	  $("#c1").css("border-bottom","2px solid #d33d3e");
	  $("#ac").css({"border":"none","color":"#CCCCCC"});
	  $("#c1 a").css("color","#D33D3E");
	  $("#c2 :first-child").css("border","none");
	  $("#img1").attr("src","__PUBLIC__/Home/imagesnew/love.png")
	  $("#img2").attr("src","__PUBLIC__/Home/imagesnew/togather.png");
      $("#img3").attr("src","__PUBLIC__/Home/imagesnew/dream.png");
  	  var txt_4=$("<a>公益</a>");
	  var txt_5=$("<a>拼团</a>");
	  var txt_6=$("<a>愿望</a>");	
	  
  	  txt_4.css({"font-size":"13px","text-align":"center"});
	  txt_5.css({"font-size":"13px","text-align":"center"});
	  txt_6.css({"font-size":"13px","text-align":"center"});
	  $("a:contains('农业')").replaceWith(txt_4);
	  $("a:contains('创意')").replaceWith(txt_5);
      $("a:contains('生活')").replaceWith(txt_6); 
	})
		
		
		
})


	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
















 
