$(function(){
    var cropper;
    $(".imgset_i").on("change", function () {
        var fr = new FileReader();
        var file = this.files[0];

        if (!/image\/\w+/.test(file.type)) {
            alert(file.name + "不是图片文件！");
            return false;
        } else if (file.size > 2 * 1024 * 1024) {
            alert('图片大小不能超过2M');
            return false;
        }

        fr.readAsDataURL(file);
        fr.onload = function () {
            //这里初始化cropper
            console.log(fr);
            $('.image').attr('src',fr.result);
            iniCropper()
        };
        $('.imgset_a').html('重新选择');
        $('.imgset_a').click(function ij(){
            if( $('.imgset_a').html()==='重新选择'){
                location=location;
            }
        })
    });

    var croppable = false;
    function iniCropper() {
        var $image = $('.image'),
            image = $image[0];
        cropper = new Cropper(image, {
            dragMode: 'move',
            aspectRatio: 1,
            autoCropArea: 0.5,
            restore: false,
            viewMode: 1,
            guides: false,
            center: false,
            highlight: false,
            cropBoxMovable: false,
            cropBoxResizable: false,
            toggleDragModeOnDblclick: false,
            zoomable:true,
            ready: function () {
                croppable = true;
            }
        });
    }

    $('.imgset_b').on('click', function () {
        var croppedCanvas;
        var rectCanvas;
        var rectImage;

        if (!croppable) {
            return false;
        }
        // Crop
        croppedCanvas = cropper.getCroppedCanvas();
        //Rect
        rectCanvas = getRectCanvas(croppedCanvas);
        // Show
        rectImage = document.createElement('img');
        rectImage.src = rectCanvas.toDataURL();

        $('.result').html('').append(rectImage);

        //var form=document.forms[0];
        var formData = new FormData();   //这里连带form里的其他参数也一起提交了,如果不需要提交其他参数可以直接FormData无参数的构造函数

        //convertBase64UrlToBlob函数是将base64编码转换为Blob
        formData.append("filename", convertBase64UrlToBlob(rectCanvas.toDataURL()));  //append函数的第一个参数是后台获取数据的参数名,和html标签的input的name属性功能相同
        //ajax 提交form

        return false;//不提交
        $.ajax({
            url: 'url',
            type: "POST",
            data: formData,
            dataType: "text",
            processData: false,         // 告诉jQuery不要去处理发送的数据
            contentType: false,        // 告诉jQuery不要去设置Content-Type请求头

            success: function (res) {
                var data = JSON.parse(res);
                if (data.status) {
                    hideLoading();
                    showTips(data.msg);
                    setTimeout(function () {
                        location.href = 'url?t=' + (new Date()).getTime();
                    }, 200);
                } else {
                    console.log(data);
                }
            },
            xhr: function () {            //在jquery函数中直接使用ajax的XMLHttpRequest对象
                var xhr = new XMLHttpRequest();
                xhr.upload.addEventListener("progress", function (evt) {
                    if (evt.lengthComputable) {
                        var percentComplete = Math.round(evt.loaded * 100 / evt.total);
                        console.log("正在提交..." + percentComplete.toString() + '%');        //在控制台打印上传进度
                    }
                }, false);

                return xhr;
            }

        });
    });

//绘制矩形canvas
    function getRectCanvas(sourceCanvas) {
        var canvas = document.createElement('canvas');
        var context = canvas.getContext('2d');
        var width = sourceCanvas.width;
        var height = sourceCanvas.height;

        canvas.width = width;
        canvas.height = height;

        context.imageSmoothingEnabled = true;
        context.drawImage(sourceCanvas, 0, 0, width, height);
        context.globalCompositeOperation = 'destination-in';
        context.beginPath();
        context.rect(0, 0, width, height);
        context.fill();

        return canvas;
    }

    /**
     * 将以base64的图片url数据转换为Blob
     * @param urlData
     * 用url方式表示的base64图片数据
     */
    function convertBase64UrlToBlob(urlData) {
        var bytes = window.atob(urlData.split(',')[1]);       //去掉url的头，并转换为byte
        //处理异常,将ascii码小于0的转换为大于0
        var ab = new ArrayBuffer(bytes.length);
        var ia = new Uint8Array(ab);
        for (var i = 0; i < bytes.length; i++) {
            ia[i] = bytes.charCodeAt(i);
        }
        return new Blob([ab], { type: 'image/png' });
    }//绘制矩形canvas
    function getRectCanvas(sourceCanvas) {
        var canvas = document.createElement('canvas');
        var context = canvas.getContext('2d');
        var width = sourceCanvas.width;
        var height = sourceCanvas.height;

        canvas.width = width;
        canvas.height = height;

        context.imageSmoothingEnabled = true;
        context.drawImage(sourceCanvas, 0, 0, width, height);
        context.globalCompositeOperation = 'destination-in';
        context.beginPath();
        context.rect(0, 0, width, height);
        context.fill();

        return canvas;
    }

    /**
     * 将以base64的图片url数据转换为Blob
     * @param urlData
     * 用url方式表示的base64图片数据
     */
    function convertBase64UrlToBlob(urlData) {
        var bytes = window.atob(urlData.split(',')[1]);       //去掉url的头，并转换为byte
        //处理异常,将ascii码小于0的转换为大于0
        var ab = new ArrayBuffer(bytes.length);
        var ia = new Uint8Array(ab);
        for (var i = 0; i < bytes.length; i++) {
            ia[i] = bytes.charCodeAt(i);
        }
        return new Blob([ab], { type: 'image/jpeg' });
    }
})