var CreatedOKCNPrint7766 = null;

function getLodop(oOBJECT, oEMBED) {
    /**************************
      本函数根据浏览器类型决定采用哪个页面元素作为CNPrint对象：
      IE系列、IE内核系列的浏览器采用oOBJECT，
      其它浏览器(Firefox系列、Chrome系列、Opera系列、Safari系列等)采用oEMBED,
      如果页面没有相关对象元素，则新建一个或使用上次那个,避免重复生成。
    **************************/
    var strHtmInstall = "<br><font color='#FF00FF'>打印组件未安装！<a href='/public/Printer/install_CaiNiaoPrint32.exe' target='_self'>请点击这里</a>，下载32位安装程序，安装后请刷新页面或重新进入。如果是谷浏览器，必须在地址栏输入： chrome://flags/#enable-npapi，然后找到“启用NPAPI”，点击启用，根据提示重启chrome即可。</font>";
    var strHtmUpdate = "<br><font color='#FF00FF'>打印组件需要升级！点击这里<a href=' http://www.taobao.com/market/cainiao/eleprint.php' target='_self'>请点击这里</a>，下载32位安装程序，安装升级后请重新进入。</font>";

    var strHtm64_Install = "<br><font color='#FF00FF'>打印组件未安装！点击这里<a href='/public/Printer/install_CaiNiaoPrint64.exe' target='_self'>请点击这里</a>，下载64位安装程，安装后请刷新页面或重新进入。如果是谷浏览器，必须在地址栏输入： chrome://flags/#enable-npapi，然后找到“启用NPAPI”，点击启用，根据提示重启chrome即可。</font>";
    var strHtm64_Update = "<br><font color='#FF00FF'>打印组件需要升级！点击这里<a href=' http://www.taobao.com/market/cainiao/eleprint.php' target='_self'>请点击这里</a>，下载64位安装程序，安装升级后请重新进入。</font>";
    var LODOP;
    try {
        //=====判断浏览器类型:===============

        var isIE = (navigator.userAgent.indexOf('MSIE') >= 0) || (navigator.userAgent.indexOf('Trident') >= 0);

        var is64IE = isIE && (navigator.userAgent.indexOf('x64') >= 0);
        //=====如果页面有CNPrint就直接使用，没有则新建:==========
        if (oOBJECT != undefined || oEMBED != undefined) {
            if (isIE)
                LODOP = oOBJECT;
            else
                LODOP = oEMBED;
        } else {
            if (CreatedOKCNPrint7766 == null) {
                LODOP = document.createElement("object");
                LODOP.setAttribute("width", 0);
                LODOP.setAttribute("height", 0);
                LODOP.setAttribute("style", "position:absolute;left:0px;top:-100px;width:0px;height:0px;");
                if (isIE) LODOP.setAttribute("classid", "clsid:09896DB8-1189-44B5-BADC-D6DB5286AC57");
                else LODOP.setAttribute("type", "application/x-cainiaoprint");
                document.documentElement.appendChild(LODOP);
                CreatedOKCNPrint7766 = LODOP;
            } else
                LODOP = CreatedOKCNPrint7766;
        };
        //=====判断CNPrint插件是否安装过，没有安装或版本过低就提示下载安装:==========
        if ((LODOP == null) || (typeof (LODOP.VERSION) == "undefined")) {
            if (navigator.userAgent.indexOf('Chrome') >= 0)
                document.documentElement.innerHTML = strHtmChrome + document.documentElement.innerHTML;
            if (navigator.userAgent.indexOf('Firefox') >= 0)
                document.documentElement.innerHTML = strHtmFireFox + document.documentElement.innerHTML;
            if (is64IE) document.write(strHtm64_Install); else
                if (isIE) document.write(strHtmInstall); else
                    document.documentElement.innerHTML = strHtmInstall + document.documentElement.innerHTML;
            return LODOP;
        } else
            if (LODOP.VERSION < "1.0.0.0") {
                if (is64IE) document.write(strHtm64_Update); else
                    if (isIE) document.write(strHtmUpdate); else
                        document.documentElement.innerHTML = strHtmUpdate + document.documentElement.innerHTML;
                return LODOP;
            };
        //=====如下空白位置适合调用统一功能(如注册码、语言选择等):====	     


        //============================================================	     
        return LODOP;
    } catch (err) {
        if (is64IE)
            document.documentElement.innerHTML = strHtm64_Install + document.documentElement.innerHTML; else
            document.documentElement.innerHTML = strHtmInstall + document.documentElement.innerHTML;
        return LODOP;
    };
}
