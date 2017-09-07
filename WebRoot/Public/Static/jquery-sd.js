function checkDelBoxes(pForm, boxName, parent)
{
    for (i = 0; i < pForm.length; i++)
        if (pForm[i].name == boxName)
            pForm[i].checked = parent;
}

function randDecimal($value, $decimal) {
    $value = Math.ceil($value * Math.pow(10, $decimal));
    return $value / Math.pow(10, $decimal);
}

$(function() {
    //初始化时间
    $("input:text.date,input:text.datetime").each(function (i) {
        var sender = this;
        if ($(this).is(".date")) {
            this.onfocus = function () {
                WdatePicker({ dateFmt: "yyyy/MM/dd", onpicked: function () {
                    if (typeof $.dateTimePicked == "function") {
                        $.dateTimePicked(sender);
                    }
                }
                });
            }
        }
        else if ($(this).is(".datetime")) {
            this.onfocus = function () {
                WdatePicker({ dateFmt: "yyyy/MM/dd HH:mm", onpicked: function () {
                    if (typeof $.dateTimePicked == "function") {
                        $.dateTimePicked(sender);
                    }
                }
                });
            };
        }
    });
});


/**
 *
 * @param sender
 * @param attr
 * @param value
 */
function toggleAttr(sender , attr,  value) {
    sender = $(sender);
    if (sender.attr(attr)) {
        sender.removeAttr(attr);
    }
    else {
        sender.attr(attr, value);
    }
}


$.alert = function($option) {
    $option = $.extend({
        'title' : '提示' ,
        'message' : '' ,
        'callback' : '' ,
        'url' : '',
        'type' : 'danger'
    } , $option);
    var modal = $('<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">' +
  '<div class="modal-dialog">' +
    '<div class="modal-content">' +
      '<div class="modal-header">' +
        '<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>' +
        '<h4 class="modal-title" id="myModalLabel">' + $option['title'] + '</h4>' +
      '</div>' +
      '<div class="modal-body">' +
       '<h4 class="text-' + $option['type'] + '">' + $option['message'] + '</h4>' +
      '</div>' +
      '<div class="modal-footer">' +
        //'<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>' +
        '<button type="button" class="btn btn-primary">确定</button>' +
      '</div>' +
    '</div>' +
  '</div>' +
'</div>');
    var m = modal.appendTo("body").modal();
    m.on("hidden.bs.modal", function() {
        if (typeof $option.callback == "function") {
            $option.callback(true);
        }
        if ($option.url) {
            location.href = $option.url;
        }
    });
    $(".modal-footer>button.btn-primary").click(function() {
        m.modal('hide');
    });
};

/**
 * 添加到收藏
 * @param id
 * @param fav_type
 */
function addFav(id, fav_type) {
    $.post("/fav/addFav" , { id : id , fav_type : fav_type} , function(data) {
        if (data.status == 0) {
            alert(data.message);
            if (data.url) {
                location.href = data.href;
            }
        }
        else {
            loadFav();
        }
    }, 'json');
}

function loadFav() {
    $(".btn-fav-on .fa-star").removeClass("fa-star").addClass("fa-star-o");
    $(".btn-fav").removeClass('btn-fav-on');
    var fav_list = $.cookie("ls_hotel_FAV_LIST");
    if (fav_list) {
        fav_list = fav_list.split(',');
        for(var i = 0; i<fav_list.length; i++) {
            var fav_item = fav_list[i];
            if (fav_item) {
                $(".fav-" + fav_item).addClass('btn-fav-on');
            }
        }
    }
    $(".btn-fav-on .fa-star-o").removeClass("fa-star-o").addClass("fa-star");
}


$(function() {
    loadFav();
});
