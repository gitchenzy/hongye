
//通用仓库选择框
function show_warehouse_select($callback, $options) {
    $options = $.extend({ title: '仓库选择', width: 500, height: 500, urlPara: "" }, $options);
    top.selectorHandler = function (selected) {
        $callback(selected);
    };
    top.showModal(
         'WMS/Warehouse/Selector?' + $options.urlPara, //使用C#生成url
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

//通用库位选择框
function show_whPosition_select($callback, $options) {
    $options = $.extend({ title: '库位选择', width: 700, height: 550, urlPara: "" }, $options);
    top.selectorHandler = function (selected) {
        $callback(selected);
    };
    top.showModal(
         'WMS/Whposition/Selector?' + $options.urlPara, //使用C#生成url
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

//通用库存选择框
function show_stock_select($callback, $options) {

    $options = $.extend({ title: '库存选择', width: 700, height: 550, urlPara: "" }, $options);
    top.selectorHandler = function (selected) {
        $callback(selected);
    };
    top.showModal(
        'WMS/Common/SelectStock?' + $options.urlPara, //使用C#生成url
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

//打开入库单详情
function show_storagein_detail(id)
{
    top.showModal(
        'WMS/StockIn/Detail?id='+id,
        '入库单详情', //调用语言来设置标题
        {
            width: 1100, //窗口宽度
            height: 650, //窗口高度
            callBack: function () {
                //窗口关闭回调函数中重新加载列表
                $("#tg_main").datagrid("reload");
            }
        }
    );
}

//打开出库单详情
function show_storageout_detail(id) {
    top.showModal(
        'WMS/StockOut/Detail?id=' + id,
        '出库单详情', //调用语言来设置标题
        {
            width: 1100, //窗口宽度
            height: 650, //窗口高度
            callBack: function () {
                //窗口关闭回调函数中重新加载列表
                $("#tg_main").datagrid("reload");
            }
        }
    );
}

//弹出采购单选择框
function show_purchase($callback, $options) {

    $options = $.extend({ title: '采购单选择', width: 1000, height: 600, urlPara: "" }, $options);
    top.selectorHandler = function (selected) {
        $callback(selected);
    };
    top.showModal(
         'Purchase/PurchaseOrder/Selector?' + $options.urlPara, //使用C#生成url
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

//弹出采购退货单选择框
function show_purchase_return($callback, $options) {

    $options = $.extend({ title: '采购退货单', width: 1000, height: 600, urlPara: "" }, $options);
    top.selectorHandler = function (selected) {
        $callback(selected);
    };
    top.showModal(
         'Purchase/PurchaseBackGood/Selector?' + $options.urlPara, //使用C#生成url
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