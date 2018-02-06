<form id= "uploadForm">
    <p >上传文件： <input type="file" name="headimg"/></p>
    <input type="button" value="上传" onclick="doUpload()" />
</form>
<script src="/asset/jquery.min.js"></script>
<script>
    function doUpload() {
        var formData = new FormData($( "#uploadForm" )[0]);
        var url = 'http://api.hzz.com/uploadFile';
        formData.append("url",url);
        $.ajax({
            url: url ,
            type: 'POST',
            data: formData,
            async: false,
            cache: false,
            contentType: false,
            dataType:'json',
            processData: false,
            success: function (returndata) {
//                alert(returndata.data);
                $("#aaa").attr('src','./uploads/'+returndata.data);

            },
            error: function (returndata) {
//                alert(returndata.data);
                $("#aaa").attr('src','./uploads/'+returndata.data);

            }
        });
    }
</script>
<img src="" alt="" id="aaa">