$(function(){

    var imgs = $('.image');
    var croppers = imgs.cropper({
            dragMode: 'none',
            aspectRatio: 1,
            autoCropArea: 0.5,
            restore: false,
            viewMode: 1,
            guides: false,
            center: true,
            highlight: false,
            cropBoxMovable: true,
            cropBoxResizable: false,
            mouseWheelZoom:false,
            toggleDragModeOnDblclick: false,
            zoomable:true,
            // ready: function () {
            //     croppable = true;
            // }
        }
    );

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
            //选择图片后重新初始裁剪区
            imgs.attr('src',this.result);
            imgs.cropper('reset', true).cropper('replace', this.result);
            console.log(file);
        };


        // $('.imgset_a').html('重新选择');
        // $('.imgset_a').click(function ij(){
        //     if( $('.imgset_a').html()==='重新选择'){
        //         location=location;
        //     }
        // })
    });

    // var croppable = false;
    function iniCropper() {
        var $image = $('.image'),
            image = $image[0];
     var   cropper = new Cropper(image, {
            dragMode: 'none',
            aspectRatio: 1,
            autoCropArea: 0.5,
            restore: false,
            viewMode: 1,
            guides: false,
            center: true,
            highlight: false,
            cropBoxMovable: true,
            cropBoxResizable: false,
            mouseWheelZoom:false,
            toggleDragModeOnDblclick: false,
            zoomable:true,
            // ready: function () {
            //     croppable = true;
            // }
        });
    }


    $('.imgset_b').on('click', function () {

        var type = imgs.attr('src').split(';')[0].split(':')[1];

        var canVas = imgs.cropper("getCroppedCanvas", {});
        //将裁剪的图片加载到face_image
        $('#caij').attr('src', canVas.toDataURL());

        $('#result').val(canVas.toDataURL());

        return false;



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