<?php
header("Content-type: text/html; charset=utf-8"); 
require('../config.php');
require('../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]


?>
<!DOCTYPE html>
<html>
<head>
    <title>填写地址</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta content="no" name="apple-touch-fullscreen">
    <meta name="MobileOptimized" content="320"/>
    <meta name="format-detection" content="telephone=no">
    <meta name=apple-mobile-web-app-capable content=yes>
    <meta name=apple-mobile-web-app-status-bar-style content=black>
    <meta http-equiv="pragma" content="nocache">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
	<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE8">
    
    <link type="text/css" rel="stylesheet" href="./assets/css/amazeui.min.css" />
    <link type="text/css" rel="stylesheet" href="./css/order_css/global.css" />
    <link type="text/css" rel="stylesheet" href="./css/css_<?php echo $skin ?>.css" />

<style type="text/css">
select {
  /*Chrome和Firefox里面的边框是不一样的，所以复写了一下*/
  border: solid 1px #000;

  /*很关键：将默认的select选择框样式清除*/
  appearance:none;
  -moz-appearance:none;
  -webkit-appearance:none;

  /*在选择框的最右侧中间显示小箭头图片*/
  background: url("images/list_image/tagbg_item_down.png") no-repeat scroll right center transparent;
	background-color:#fff;
	background-size: 15px 15px;

  /*为下拉小箭头留出一点位置，避免被文字覆盖*/
  padding-right: 15px;
}
	.pay_address{
		display: inline-block;
	}
	#location_p,#location_c,#location_a{
line-height: 18px;
    width: 70px;
    border: none;
    height: 28px;
    padding-right: 24px;
    overflow: hidden;
	}
	.list-one .left-title{
		width:25%;
		float: left;
		line-height: 24px;
	}
	.frame_image .area-one {
    position: relative;
    width: 90%;
    height: 150px;
    display: block;
	margin:10px auto 0;
}
	.frame_image .area-one p{
		width: 100%;
		height: 150px;
		text-align: center;
		border:1px solid #d1d1d1;
		line-height: 150px;
		background-color: #fff;
}
	.frame_image .area-one img{
		width: 100%;
		height: 150px;
		text-align: center;
		position: absolute;
    	top: 0;
    	left: 0;
}
	.frame_image_select {
    width: 100%;
    height: 150px;
    opacity: 0;
    position: absolute;
    top: 0px;
    left: 0px;
}
</style>

</head>
<link type="text/css" rel="stylesheet" href="./css/order_css/style.css" media="all">
<link type="text/css" rel="stylesheet" href="./css/order_css/dingdan.css"/>
<link type="text/css" rel="stylesheet" href="./css/order_css/address.css" />
<body id="mainBody" data-ctrl=true style="background:#f8f8f8;">
    <div id="mainDiv">

       <div class="frame_image" id="">
            <div class="area-one">
            	<p style="position:relative;">拍照</p>
                <img id="img_0" src="" >
                <input type="file" style="z-index:2;" id="image1"  accept="image/*" class="frame_image_select" name="Filedata_[]" value="" old_identityimgt="<?php echo $identityimgt;?>">
            </div>

        </div>

    </div>
    
    <script type="text/javascript" src="./assets/js/jquery.min.js"></script>    
    <script type="text/javascript" src="./assets/js/amazeui.js"></script>
    <script type="text/javascript" src="./js/global.js"></script>
    <script type="text/javascript" src="./js/loading.js"></script>
    <script src="./js/jquery.ellipsis.js"></script>
    <script src="./js/jquery.ellipsis.unobtrusive.js"></script>
    <script type="text/javascript" src="../common/region_select.js"></script>
    <script type="text/javascript" src="../common/js/common.js"></script>
</body>		

<script type="text/javascript">
	var is_default = <?php echo $is_default?>;
	var isDefault = false;//设为默认地址flag

	    //获取本地的图片
    function fileSelect_banner(evt) {
        if (window.File && window.FileReader && window.FileList && window.Blob) {
            currfile = evt;
            var files = evt.files;//直接传入file对象，evt.target改成evt
            var pid = $(evt).data("pid");	//现在选择的商品的pid
            var file;
            file = files[0];
            if (!file.type.match('image.*')) {
                return;
            }
            reader = new FileReader();
            reader.onload = (function (tFile) {
                return function (evt) {
                    dataURL = evt.target.result;
                    $(currfile).prev("img").eq(0).attr("src",dataURL);
                    }
            }(file));
            reader.readAsDataURL(file);
            sendFile = file;
        } else {
            alert('该浏览器不支持文件管理。');
        }
    }
</script>
<!--引入侧边栏 start-->
<?php  include_once('float.php');?>
<!--引入侧边栏 end-->
<?php require('../common/share.php'); ?>
</body>
</html>