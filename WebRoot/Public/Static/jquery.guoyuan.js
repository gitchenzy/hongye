/**
 * Created by fely on 3/26/15.
 */
var fancy_box_setting = {
    fitToView: false,
    width: 470,
    autoSize: false,
    autoHeight: true,
    autoCenter: false,
    closeBtn: false,
    openEffect: 'none',
    closeEffect: 'none',
    padding: 0,
    wrapCSS: 'fancybox-wrap-empty'
};

var fancy_box_form = {
    fitToView: false,
    width: 470,
    autoSize: false,
    autoHeight: true,
    autoCenter: false,
    closeBtn: false,
    openEffect: 'none',
    closeEffect: 'none',
    padding: 0,
    wrapCSS: 'fancybox-wrap-empty',
    containerCSS: "fancybox-overlay-none"
};

var STRING_ERROR_TITLE = "错误";
var STRING_ERROR_ATTENTION = "提示";

var SCRIPT_ROOT = $("#guoyuan_script").attr("src").replace("jquery.guoyuan.js", '');
var SITE_ROOT = SCRIPT_ROOT.replace("/public/Static/", "");


$(function () {
    ///页面初始化设置
    $(".hover-list>.item").hover(function () {
        $(this).addClass("item-on");
    }, function () {
        $(this).removeClass("item-on");
    });

    ///删除没有权限的html
    $(".function_unauth").remove();

    //判断页面上是否存在时间选择框
    $(".form-control-date,.form-control-datetime").each(function (i) {
        var sender = this;
        if ($(this).is(".form-control-date")) {
            this.onfocus = function () {
                WdatePicker({
                    dateFmt: "yyyy/MM/dd", onpicked: function () {
                        if (typeof $.dateTimePicked == "function") {
                            $.dateTimePicked(sender);
                        }
                    }
                });
            }
        }
        else if ($(this).is(".form-control-datetime")) {
            this.onfocus = function () {
                WdatePicker({
                    dateFmt: "yyyy/MM/dd HH:mm:ss", onpicked: function () {
                        if (typeof $.dateTimePicked == "function") {
                            $.dateTimePicked(sender);
                        }
                    }
                });
            };
        }
    });
    ///日期选择控件自读
    $(".form-control-datetime").attr("readonly", "readonly");

    //页面上的必填项
    $(".required").append("*");

    //给页面上设置readonly的输入框加类名
    $("input[readonly=readonly]").addClass("readonly");

    //处理窗口大小改变的事件
    $(window).resize(function () {
        //设置为自适应高度的grid,进行resize
        $('.easyui-datagrid-auto').each(function (i, sender) {
            easyui_grid_resize($(sender));
        });

        //设置为自适应高度的分类Grid（左边树分类，右边详细数据）,进行resize
        $("#easyui-layout-fg").each(function (i, sender) {
            var sender = $("#easyui-layout-fg");
            var offset = sender.offset();
            var new_height = $(window).height() - offset.top;
            if (new_height > 0) {
                $("#easyui-layout-fg").layout('resize', {
                    height: (new_height)
                });
            }
        });

        //设置为自适应高度的panel,进行resize
        $(".pane-auto-height").each(function (i, sender) {
            sender = $(sender);
            var offset_top = sender.data("offset");
            if (!offset_top) {
                offset_top = 0;
            }
            var offset = sender.offset();
            if (offset) {
                var new_height = $(window).height() - offset_top - offset.top;
                if (new_height > 0) {
                    sender.height(new_height);
                }
            }
        });
    }).resize();

    //解决密码粘贴问题
    $("input:password").bind('contextmenu', function (e) {
        e.preventDefault();
    }).bind("paste", function (e) {
        e.preventDefault();
    });

    //對彈出選擇的控件加清空按鈕
    $(".filter-control-selector,.form-control-selector").each(function () {
        var controller = $(this);
        controller.append("<a href='javascript:;' class='close'></a>");
        controller.hover(function () {
            $("a.close", controller).css("display" , "inline-block");
        }, function () {
            $("a.close", controller).css("display", "none");
        });
        $("a.close", controller).click(function () {
            $("input", controller).val("");
        });
    });
});

/**
 * 重新计算表格的宽高
 * @param sender
 */
function easyui_grid_resize(sender) {
    var offset_top = sender.data("offset");
    if (!offset_top) {
        offset_top = 0;
    }
    var offset = $(sender.parents(".datagrid").get(0)).offset();
    if (offset) {
        var new_height = $(window).height() - offset_top - offset.top;
        if (new_height > 0) {
            sender.datagrid('resize', {
                height: new_height
            });
        }
    }
}

//删除前确认操作
function confirm_delete($message) {
    if (!$message) {
        $message = "是否确定删除数据？";
    }
    return confirm($message);
}

//操作前确认操作
function confirm_deal($message) {
    if (!$message) {
        $message = "是否确定操作数据？";
    }
    return confirm($message);
}

/**
 * 自定义alert
 * @param $message
 * @param $title
 */
function show_alert($message, $callback) {
    alert($message);
}

/**/
function show_pop($message, $option) {
    $options = $.extend({ delay: 500 }, $options);
    var pop = $("<span class='pop_message'>" + $message + "</span>").appendTo("body");
    pop.css("margin-left", -pop.width() / 2);
    window.setTimeout(function () {
        pop.animate({ top: 0, opacity: 0 });
    }, $option.delay);
}

/**
 * 打开图片上传的窗口
 */
function show_picture_uploader($callback, $options) {
    $options = $.extend({ title: '上传图片', width: 600, height: 250 }, $options);
    top.selectorHandler = function (selected) {
        $callback(selected);
    };
    top.showModal(
        SITE_ROOT + 'Upload/ShowImage', //使用C#生成url
        $options.title, //调用语言来设置标题
        {
            width: $options.width, //窗口高度
            height: $options.height, //窗口高度
            callBack: function () {
                top.selectorHandler = undefined;
            }
        }
    );
}

/**
 * 通用商品选择框
 * @param $callback
 * @param $options
 */
function show_product_select($callback, $options) {
    $options = $.extend({ title: '选择商品', width: 1004, height: 550 }, $options);
    top.selectorHandler = function (selected) {
        $callback(selected);
    };
    top.showModal(
        SITE_ROOT + 'Product/Product/Selector', //使用C#生成url
        $options.title, //调用语言来设置标题
        {
            width: $options.width, //窗口高度
            height: $options.height, //窗口高度
            callBack: function () {
                top.selectorHandler = undefined;
            }
        }
    );
}
/**
 * 供应商选择框
 * @param $callback
 * @param $options
 */
function show_Supplier($callback, $options) {
    $options = $.extend({ title: '选择供应商', width: 500, height: 600 }, $options);
    top.selectorHandler = function (selected) {
        $callback(selected);
    };
    top.showModal(
        SITE_ROOT + 'Finance/Self/Supplier', //使用C#生成url
        $options.title, //调用语言来设置标题
        {
            width: $options.width, //窗口高度
            height: $options.height, //窗口高度
            callBack: function () {
                top.selectorHandler = undefined;
            }
        }
    );
}
/**
 * 分销商选择框
 * @param $callback
 * @param $options
 */
function show_Customer($callback, $options) {
    $options = $.extend({ title: '选择分销商', width: 500, height: 600 }, $options);
    top.selectorHandler = function (selected) {
        $callback(selected);
    };
    top.showModal(
        SITE_ROOT + 'Finance/Self/Customer', //使用C#生成url
        $options.title, //调用语言来设置标题
        {
            width: $options.width, //窗口高度
            height: $options.height, //窗口高度
            callBack: function () {
                top.selectorHandler = undefined;
            }
        }
    );
}
/**
 * 通用商品选择框
 * @param $callback
 * @param $options
 */
function show_product_prop_select($callback, $options) {
    $options = $.extend({ title: '选择商品', width: 1004, height: 550, singleSelect: false }, $options);
    top.selectorHandler = function (selected) {
        $callback(selected);
    };
    url = SITE_ROOT + 'Product/Product/PropSelector';
    if ($options.singleSelect) {
        url = url + "?single=1";
    }
    top.showModal(
        url,
        $options.title, //调用语言来设置标题
        {
            width: $options.width, //窗口高度
            height: $options.height, //窗口高度
            callBack: function () {
                top.selectorHandler = undefined;
            }
        }
    );
}
/**
 * 分销供应商的通用商品选择框
 * @param $callback
 * @param $options
 */
function show_CustomerGoodsSelect($callback, $options) {
    $options = $.extend({ title: '选择供应商商品', width: 1004, height: 550, singleSelect: false }, $options);
    top.selectorHandler = function (selected) {
        $callback(selected);
    };
    url = SITE_ROOT + 'Product/Product/CustomerGoodsSelect';
    if ($options.singleSelect) {
        url = url + "?single=1";
    }
    top.showModal(
        url,
        $options.title, //调用语言来设置标题
        {
            width: $options.width, //窗口高度
            height: $options.height, //窗口高度
            callBack: function () {
                top.selectorHandler = undefined;
            }
        }
    );
}

/**
 * 分销商店铺选择框
 * @param $callback
 * @param $options
 */
function show_distributor_shop_select($callback, $options) {
    $options = $.extend({ title: '选择分销商店铺', width: 1004, height: 550, singleSelect: false }, $options);
    top.selectorHandler = function (selected) {
        $callback(selected);
    };
    url = SITE_ROOT + 'Channel/Saler/SalerShopSelector';
    if ($options.singleSelect) {
        url = url + "?single=1";
    }
    top.showModal(
        url,
        $options.title, //调用语言来设置标题
        {
            width: $options.width, //窗口高度
            height: $options.height, //窗口高度
            callBack: function () {
                top.selectorHandler = undefined;
            }
        }
    );
}

/**
 * 采购供应商的通用商品选择框
 * @param $callback
 * @param $options
 */
function show_SupplierGoodsSelect($supplierId, $callback, $options) {
    $options = $.extend({ title: '选择采购供应商商品', width: 1004, height: 550, singleSelect: false }, $options);
    top.selectorHandler = function (selected) {
        $callback(selected);
    };
    url = SITE_ROOT + 'Product/Product/PropSelectorSupplier?supplierId=' + $supplierId;
    if ($options.singleSelect) {
        url = url + "&single=1";
    }
    top.showModal(
        url,
        $options.title, //调用语言来设置标题
        {
            width: $options.width, //窗口高度
            height: $options.height, //窗口高度
            callBack: function () {
                top.selectorHandler = undefined;
            }
        }
    );
}

/**
 * 通用品牌选择框
 * @param $callback
 * @param $options
 */
function show_brand_select($callback, $options) {
    $options = $.extend({ title: '选择品牌', width: 700, height: 550 }, $options);
    top.selectorHandler = function (selected) {
        $callback(selected);
    };
    top.showModal(
        SITE_ROOT + 'Product/ProductBrand/Selector', //使用C#生成url
        $options.title, //调用语言来设置标题
        {
            width: $options.width, //窗口高度
            height: $options.height, //窗口高度
            callBack: function () {
                top.selectorHandler = undefined;
            }
        }
    );
}


/**
 * 查询条件->采购供应商选择
 * @param callback
 */
function show_purchase_supplier_selector($callback, $options) {
    $options = $.extend({ title: '选择采购供应商', width: 1004, height: 550, singleSelect: false }, $options);
    top.selectorHandler = function (data_list) {
        $callback(data_list);
    };
    url = SITE_ROOT + 'Purchase/Suppliers/Selector';
    if ($options.singleSelect) {
        url = url + "?single=1";
    //    //判断采购供应商性质
    //    if ($options.pay == 1) {
    //        url = url + "&pay=1";
    //    } else if ($options.pay == 0) {
    //        url = url + "&pay=0";
    //    }
    //} else {
    //    if ($options.pay == 1) {
    //        url = url + "?pay=1";
    //    } else if ($options.pay == 0) {
    //        url = url + "?pay=0";
    //    }
    }
    top.showModal(url,
            $options.title, //调用语言来设置标题
            {
                width: $options.width, //窗口高度
                height: $options.height, //窗口高度
                callBack: function () {
                    top.selectorHandler = undefined;
                }
            }
        );
}

/**
 * 查询条件->店铺信息
 * @param callback
 */
function show_store_selector($callback, $options) {
    $options = $.extend({ title: '查询条件->店铺信息', width: 800, height: 550, singleSelect: false,PlatformID:0 }, $options);
    top.selectorHandler = function (data_list) {
        $callback(data_list);
    };
    url = SITE_ROOT + 'Channel/AccountInfo/Selector?PlatformID=' + $options.PlatformID;
    if ($options.singleSelect) {
        url = url + "&single=1";
    }
    top.showModal(url,
            $options.title, //调用语言来设置标题
            {
                width: $options.width, //窗口高度
                height: $options.height, //窗口高度
                callBack: function () {
                    top.selectorHandler = undefined;
                }
            }
        );
}

/**
 * 查询条件->会员表
 * @param callback
 */
function show_employee_selector($callback, $options) {
    $options = $.extend({ title: '查询条件->会员列表', width: 1000, height: 700, singleSelect: false }, $options);
    top.selectorHandler = function (data_list) {
        $callback(data_list);
    };
    var url = '/Admin/Member/selector';
    if ($options.singleSelect) {
        url = url + "?single=1";
    }
    top.showModal(url,
            $options.title, //调用语言来设置标题
            {
                width: $options.width, //窗口高度
                height: $options.height, //窗口高度
                callBack: function () {
                    top.selectorHandler = undefined;
                }
            }
        );
}
/**
 * 查阅读公告
 * @param callback
 */
function show_announcement($id, $options) {
    $options = $.extend({ title: '查阅公告', width: 1000, height: 700, type: 0 }, $options);

    url = SITE_ROOT + 'Base/Announcement/Look?id=' + $id + '&type=' + $options.type;

    top.showModal(url,
            $options.title, //调用语言来设置标题
            {
                width: $options.width, //窗口高度
                height: $options.height, //窗口高度
                callBack: function () {

                }
            }
        );
}
/**
 * 查看收件箱的信息
 * @param callback
 */
function show_Letter($id, $options) {
    $options = $.extend({ title: '阅读信息', width: 400, height: 200 ,type:0}, $options);

    url = SITE_ROOT + 'Base/Letter/Look?id=' + $id + '&type=' + $options.type;

    top.showModal(url,
            $options.title, //调用语言来设置标题
            {
                width: $options.width, //窗口高度
                height: $options.height, //窗口高度
                callBack: function () {
                    if (typeof $options.callBack == 'function') {
                        $options.callBack();
                    }
                }
            }
        );
}

/**
设置列表
*/
function grid_setting($gridName, $options) {
    $options = $.extend({ title: '设置表格：' + $gridName, width: 500, height: 400 }, $options);
    url = SITE_ROOT + 'Base/Grid/Setting?gridName=' + encodeURI($gridName);

    top.showModal(url,
            $options.title, //调用语言来设置标题
            {
                width: $options.width, //窗口高度
                height: $options.height, //窗口高度
                callBack: function () {
                    if (typeof $options.callBack == 'function') {
                        $options.callBack();
                    }
                    else {
                        location.reload();
                    }
                }
            }
        );
}

/**
通用的选择框处理函数
*/
function selector_handler_common(sender, handler, displayField, valueField) {
    handler(function (data_list) {
        sender = $(sender);
        sender_target = sender.next("input:hidden"); //获取存放供应商ID的隐藏字段
        sender_text = sender.prev("input:text"); //获取存放供应商ID的隐藏字段
        if (!data_list || data_list.length <= 0) {
            sender_target.val("");
            sender_text.val("");
        }
        else {
            //组合数据
            var text_array = Array();
            var value_array = Array();
            $.each(data_list, function (i, itm) {
                text_array.push(itm[displayField]);
                value_array.push(itm[valueField]);
            });
            //供应商ID放入隐藏字段
            sender_target.val(value_array.join(";"));
            //供应商商名称放入显示字段
            if (value_array && value_array.length > 0) {
                sender_text.val(text_array.join(";"));
            }
            else {
                sender_text.val("");
            }
        }
    });
}

/**
 * 获取grid当前勾选的唯一一行数据
 * @param selector
 * @returns {*}
 */
function get_checked_row(selector, field_key) {
    var selected = $(selector).datagrid("getChecked");
    if (selected.length > 1) {
        show_alert('只能选择一条记录！');
        return null;
    }
    if (selected.length <= 0) {
        show_alert('请选择一条数据！');
        return null;
    }
    return selected[0][field_key];
}

/**
 * 获取grid的多行数据
 * @param selector
 * @param field_key
 * @returns {*}
 */
function get_checked_rows(selector, field_key, $options) {
    $options = $.extend({ msg: '一条数据', sep: ';' }, $options);
    var selected = $(selector).datagrid("getChecked");
    if (selected.length <= 0) {
        show_alert('请选择' + $options.msg + '！');
        return null;
    }
    var data_array = new Array();
    $.each(selected, function (i, data) {
        data_array.push(data[field_key]);
    });
    return data_array.join($options.sep);
}


/**
 * 获取treegrid当前勾选的唯一一行数据
 * @param selector
 * @returns {*}
 */
function get_treegrid_row(selector, field_key) {
    var selected = $(selector).treegrid("getSelected");
    if (selected.length > 1) {
        show_alert('只能选择一条记录！');
        return null;
    }
    if (selected.length <= 0) {
        show_alert('请选择一条数据！');
        return null;
    }
    return selected.field_key;
}

/**
 * 获取grid的多行数据
 * @param selector
 * @param field_key
 * @returns {*}
 */
function get_treegrid_rows(selector, field_key, $options) {
    $options = $.extend({ msg: '一条数据', sep: ';' }, $options);
    var selected = $(selector).treegrid("getSelected");
    if (selected.length <= 0) {
        show_alert('请选择' + $options.msg + '！');
        return null;
    }
    var data_array = new Array();
    $.each(selected, function (i, data) {
        data_array.push(data[field_key]);
    });
    return data_array.join($options.sep);
}


/**
 * 获取grid的多行数据 (没有选择数据放回空，不提示)
 * @param selector
 * @param field_key
 * @returns {*}
 */
function get_checked_rows_silent(selector, field_key) {
    var selected = $(selector).datagrid("getChecked");
    if (selected.length <= 0) {
        return null;
    }
    var data_array = new Array();
    $.each(selected, function (i, data) {
        data_array.push(data[field_key]);
    });
    return data_array.join(";");
}

//获取datagrid选择行的字段，不需要判断是否选择
function get_checked_field(selector, field_key, options) {
    options = $.extend({ sep: "," }, options);
    var selected = $(selector).datagrid("getChecked");
    var data_array = new Array();
    $.each(selected, function (i, data) {
        data_array.push(data[field_key]);
    });
    return data_array.join(options.sep);
};

/*果园自定义级联下拉插件*/
(function ($) {
    $.fn.cascade = function (option) {
        option = $.extend({ onChange: null, selectBy: 'value', url: '', rootParentValue: "1", emptyDeep: 100 }, option);
        $(this).each(function (i) {
            var container = $(this);
            var base_url = option.url;
            $(container).on("change", "select", function () {
                var parentSelect = $(this);
                if (typeof option.onChange == "function") {
                    //获取到当前选择的所有数据
                    var array_text = [];
                    var array_value = [];
                    $("select", container).each(function (i) {
                        array_text.push($("option:selected", this).text());
                        array_value.push($(this).val());
                    });
                    option.onChange(array_value, array_text);
                }
                if (!parentSelect.val()) {
                    $("select:gt(" + parentSelect.index() + ")", container).remove();//删除多余的
                    return;
                }
                $.getJSON(base_url + parentSelect.val(), function (data_list) {
                    if (data_list == null || data_list.length <= 0) {
                        $("select:gt(" + parentSelect.index() + ")", container).remove();//删除多余的
                        return;
                    }
                    var currentSelect = $($("select", container).get(parentSelect.index() + 1));
                    if (currentSelect.size() <= 0) {
                        container.append(" ");
                        currentSelect = $("<select></select>").appendTo(container);
                    }
                    currentSelect.empty();

                    if (currentSelect.index() >= option.emptyDeep) {
                        currentSelect.append("<option></option>");
                    }
                    $.each(data_list, function (i, data) {
                        var html_option = "";
                        var current_select = currentSelect.data("select");
                        var data_text = data.Text;
                        if (current_select && current_select.length > 2) {
                            current_select = current_select.substr(0, 2);
                        }
                        if (data_text && data_text.length > 2) {
                            data_text = data_text.substr(0, 2);
                        }
                        if (option.selectBy == "text" && current_select == data_text
                        || option.selectBy == "value" && data.Value == currentSelect.data("select")
                        ) {
                            html_option = "<option value=\"" + data.Value + "\" selected='selected'>" + data.Text + "</option>";
                        }
                        else {
                            html_option = "<option value=\"" + data.Value + "\">" + data.Text + "</option>";
                        }
                        currentSelect.append(html_option);
                    });
                    currentSelect.change();
                });
            });
            //获取并设置第一个下拉框
            $.getJSON(base_url + option.rootParentValue, function (data_list) {
                var sender_select = $("select:eq(0)", container);//触发第一个下拉框
                sender_select.empty();
                if (option.emptyDeep <= 0) {
                    sender_select.append("<option></option>");
                }
                $.each(data_list, function (i, data) {
                    var html_option = "";
                    var current_select = sender_select.data("select");
                    var data_text = data.Text;
                    if (current_select && current_select.length > 2) {
                        current_select = current_select.substr(0, 2);
                    }
                    if (data_text && data_text.length > 2) {
                        data_text = data_text.substr(0, 2);
                    }
                    if (option.selectBy == "text" && current_select == data_text
                        || option.selectBy == "value" && data.Value == sender_select.data("select")
                    ) {
                        html_option = "<option value=\"" + data.Value + "\" selected='selected'>" + data.Text + "</option>";
                    }
                    else {
                        html_option = "<option value=\"" + data.Value + "\">" + data.Text + "</option>";
                    }
                    sender_select.append(html_option);
                });
                sender_select.change();
            });
        });
    };
})(jQuery);


/*common formatter&styler for guoyuan 201507017*/

//图片格式器
function formatter_photo(value, row, index) {

    var column = $(this)[0];
    var column_value = row[column.field];

    if (column_value) {
        return "<img src='" + column_value + "' height='50' width='50'/>";
    }
    else {
        return "<img src='" + SITE_ROOT + "/public/no_photo.png' height='50' width='50'/>";
    }
}

//分润单位
function formatter_unit(value, row, index) {

    var column = $(this)[0];
    var column_value = row[column.field];

    if (column_value) {
        return column_value+"%";
    }
    else {
        return "";
    }
}

//图片格式器(自动高度)
function formatter_photo2(value, row, index) {

    var column = $(this)[0];
    var column_value = row[column.field];

    if (column_value) {
        return "<img src='" + column_value + "' height='30' />";
    }
    else {
        return "<img src='" + SITE_ROOT + "/public/no_photo.png' height='30' width='30'/>";
    }
}


    /**
 * 格式化日期
 * @param date
 */
function formatter_date(date) {
    if (typeof date == "string" && date.indexOf("/") == 0) {
        date = eval("new " + date.replace(/\//g, ""));
    }
    return $.format.date(date || "", "yyyy/MM/dd");
}

/**
 * 格式化时间
 * @param date
 */
function formatter_datetime(date) {
    if (typeof date == "string" && date.indexOf("/") == 0) {
        date = eval("new " + date.replace(/\//g, ""));
    }
    return $.format.date(date || "", "yyyy/MM/dd HH:mm:ss");
}

//头像处理样式
function styler_photo(value, row, index) {
    return "";
}

/*是否格式器*/
function formatter_boolean(value, row, index) {
    var column = $(this)[0];
    var column_value = row[column.field];
    if (value && value != "0" && value != "false") {
        return "<i class='icon icon-true'></i>";
    }
    else {
        return "<i class='icon icon-false'></i>";
    }
}


/*锁定格式器*/
function formatter_lock(value, row, index) {
    var column = $(this)[0];
    var column_value = row[column.field];
    if (value && value != "0" && value != "false") {
        return "<i class='icon icon-lock'></i>";
    }
    else {
        return "<i class='icon icon-unlock'></i>";
    }
}

/*是否格式器*/
function formatter_gender(value, row, index) {
    var column = $(this)[0];
    var column_value = row[column.field];
    if (value == '2') {
        return "<i class='icon icon-women'></i>";
    }
    else {
        return "<i class='icon icon-man'></i>";
    }
}

/*内容换行*/
function formatter_wrap(value, row, index) {
    var column = $(this)[0];
    var column_value = row[column.field];

    return "<div style='word-break:normal;word-wrap:break-word;'>" + column_value + "</div>";
}

//选择打印机
function CreatePrinterList(PrinterList) {
    if (document.getElementById(PrinterList).innerHTML != "") return;
    LODOP = getLodop();
    var iPrinterCount = LODOP.GET_PRINTER_COUNT();
    for (var i = 0; i < iPrinterCount; i++) {

        var option = document.createElement('option');
        option.innerHTML = LODOP.GET_PRINTER_NAME(i);
        option.value = i;
        document.getElementById(PrinterList).appendChild(option);
    };
};


//grid：        easyUI的datagrid对象
//refCols        合并参考列数组，及这些列都相等则合并rowFildNames指定的列
//rowFildNames： 和并列的field属性值及要合并的列数组
//mergeGridColCells($('#Table'),['serviceTypeId','areaId'],['areaName']);
//记得一定要写在window.onload事件里
function mergeGridColCells(grid, refCols, rowFildNames) {
    var rows = grid.datagrid('getRows');
    //alert(rows.length);
    //alert(rows[1][rowFildName]);
    var flag = false;
    var startIndex = 0;
    var endIndex = 0;
    if (rows.length < 1) {
        return;
    }
    $.each(rows, function (i, row) {
        $.each(refCols, function (j, refCol) {
            if (row[refCol] != rows[startIndex][refCol]) {
                flag = false;
                return false;
            }
            else {
                flag = true;
            }
        });
        //if(row[rowFildName]==rows[startIndex][rowFildName])
        if (flag) {
            endIndex = i;
        }
        else {
            $.each(rowFildNames, function (k, rowFildName) {
                grid.datagrid('mergeCells', {
                    index: startIndex,
                    field: rowFildName,
                    rowspan: endIndex - startIndex + 1,
                    colspan: null
                });
            });
            startIndex = i;
            endIndex = i;
        }
    });
    $.each(rowFildNames, function (k, rowFildName) {
        grid.datagrid('mergeCells', {
            index: startIndex,
            field: rowFildName,
            rowspan: endIndex - startIndex + 1,
            colspan: null
        });
    });
}


/**
对easyui的表格列头自定义
*/
function gd_CustomColumn(e, field) {
    var sender = $(this);
    e.preventDefault();
    var cmenu = $("#cmenu");
    if (cmenu.size() <= 0) {
        cmenu = $('<div id="cmenu"></div>').appendTo('body');
        cmenu.menu({
            onClick: function (item) {
                var isAdd = 1;
                if (item.iconCls == 'icon-ok') {
                    sender.datagrid('hideColumn', item.name);
                    cmenu.menu('setIcon', {
                        target: item.target,
                        iconCls: 'icon-empty'
                    });
                    isAdd = 0;
                } else {
                    sender.datagrid('showColumn', item.name);
                    cmenu.menu('setIcon', {
                        target: item.target,
                        iconCls: 'icon-ok'
                    });
                }
                var gridName = sender.data("grid");
                if (gridName) {
                    $.post("/Base/Grid/SetGridColumn", { columnName: item.name, gridName: gridName, isAdd: isAdd }, function (data) {
                        if (!data.status) {
                            alert(data.info);
                        }
                    }, 'json');
                }
            }
        });
        var fields = $('.easyui-datagrid').datagrid('getColumnFields');
        for (var i = 0; i < fields.length; i++) {
            var field = fields[i];
            var col = $('.easyui-datagrid').datagrid('getColumnOption', field);
            if (col.title) {
                cmenu.menu('appendItem', {
                    text: col.title,
                    name: field,
                    iconCls: col.hidden ? 'icon-empty' : 'icon-ok'
                });
            }
        }
    }
    cmenu.menu('show', {
        left: e.pageX,
        top: e.pageY
    });
}

//右键菜单
function onRowContextMenu(e, index, row) {
    $("#tg_main").datagrid('selectRow', index);
    e.preventDefault();
    $('#menu').menu('show', {
        left: e.pageX,
        top: e.pageY
    });
}




//提交一个Post,用于导出文件之类的操作
function postForm(url, data) {
    html_form = "<form name='form-modal-temp' id='form-temp' method='post' action='" + url + "'>";
    for (var perpertyName in data) {
        html_form += "<input type='hidden' name='" + perpertyName + "' value=\"" + data[perpertyName] + "\"/>";
    }
    html_form += "</form>";
    //开始POST
    var form = $(html_form).appendTo("body");
    form.submit();
    form.remove();
}
//获取UE编辑器按钮组 CDX
function GetUEToolBar(group)
{
    var ToolArray = new Array();
    group = !group ? "Default" : group;
    switch (group) {
        case "Default":
            ToolArray.push('source', '|', 'undo', 'redo', '|');
            ToolArray.push('bold', 'italic', 'underline', 'strikethrough', '|');
            ToolArray.push('forecolor', 'backcolor', 'fontfamily', 'fontsize', '|');
            ToolArray.push('indent', 'rowspacingtop', 'rowspacingbottom', 'lineheight', '|');
            ToolArray.push('justifyleft', 'justifycenter', 'justifyright', 'justifyjustify', '|');
            ToolArray.push('simpleupload', 'insertimage', '|');
            ToolArray.push('preview');
            break;
    }
    return ToolArray;
}
//平台商品选择框 CDX
function show_platproduct_select($callback, $options) {
    $options = $.extend({ title: '商品选择', width: 1000, height: 550, urlPara: "" }, $options);
    top.selectorHandler = function (selected) {
        $callback(selected);
    };
    top.showModal(
        '/Admin/Product/selector?'  + $options.urlPara,
        $options.title, //调用语言来设置标题
        {
            width: $options.width, //窗口高度
            height: $options.height, //窗口高度
            callBack: function () {
                top.selectorHandler = undefined;
            }
        }
    );
}

//平台选择商品
function show_platproduct_prop_select($callback, $options) {
    $options = $.extend({ title: '商品规格选择', width: 1000, height: 550, urlPara: "" }, $options);
    top.selectorHandler = function (selected) {
        $callback(selected);
    };
    top.showModal(
        '/Admin/Product/selectorProp?'  + $options.urlPara,
        $options.title, //调用语言来设置标题
        {
            width: $options.width, //窗口高度
            height: $options.height, //窗口高度
            callBack: function () {
                top.selectorHandler = undefined;
            }
        }
    );
}

//将东西, 插入到输入框的光标处;
(function($){
    $.fn.extend({
        insertAtCaret: function(myValue){
            var $t=$(this)[0];
            if (document.selection) {
                this.focus();
                sel = document.selection.createRange();
                sel.text = myValue;
                this.focus();
            }
            else
            if ($t.selectionStart || $t.selectionStart == '0') {
                var startPos = $t.selectionStart;
                var endPos = $t.selectionEnd;
                var scrollTop = $t.scrollTop;
                $t.value = $t.value.substring(0, startPos) + myValue + $t.value.substring(endPos, $t.value.length);
                this.focus();
                $t.selectionStart = startPos + myValue.length;
                $t.selectionEnd = startPos + myValue.length;
                $t.scrollTop = scrollTop;
            }
            else {
                this.value += myValue;
                this.focus();
            }
        }
    })
})(jQuery);