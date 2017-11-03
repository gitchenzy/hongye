//呼吸色
var log=console.log;
function ele(){
    eles={
        style:function(stylesheets){
            var stylesheet=$('#btn_1').css(stylesheets);
            return stylesheet;
        },
        color:function(r){
            $('#btn_1').css('background-color','rgb('+r+', 83 , 83)')
        },
        n:244,
    };
    return eles;
};
ele();
function act(){
    var timer=setInterval(function(){
        eles.n--;
        eles.color(eles.n);   
        if(eles.n<=200){
            clearInterval(timer);
            eles.n===200;
            changes();
        };    
    },1000/50);
    function changes(){
        var othertimer=setInterval(function(){
            eles.n++;
            eles.color(eles.n);
            if(eles.n>=255){
                clearInterval(othertimer);
                eles.n===255;
                act();
            }
        },1000/50);
        return othertimer;
    }
    function actions(){
        if($('#btn_1').mouseleave){
            log(123);
            eles.n=244;
            eles.color(244);
            clearInterval(timer);
            clearInterval(changes())
        }
    }  ;
    actions();
    log(timer); 
};
$('#btn_1').mouseenter(act);

