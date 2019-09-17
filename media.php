<?php
header("Content-type: text/html; charset=utf-8");     
require('../config.php');
require('../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
//require('../back_init.php'); 
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD); 
mysql_select_db(DB_NAME) or die('Could not select database');
mysql_query("SET NAMES UTF8");
require('../common/jssdk.php');
//头文件----start
//require('../common/common_from.php');
//头文件----end

$pid               = 0;//产品OD
$sid               = 0;//商城ID
$isbrand_supply    = -1;//是否为供应商
$brand_supply_name = "";//供应商名称

if(!empty($_POST['pid'])){
	$pid = $_POST['pid'];
}
if(!empty($_POST['sid'])){
	$sid = $_POST['sid'];
}

if( $sid ){
	$sql = "select isbrand_supply,shopName,advisory_telephone from weixin_commonshop_applysupplys where isvalid=true and user_id=".$sid;
	$result = mysql_query($sql) or die('Query failed: ' . mysql_error());  
	while ($row = mysql_fetch_object($result)) {
		$isbrand_supply 	= $row->isbrand_supply;
		$brand_supply_name 	= $row->shopName;
	}
}
//初始化--star
 $query = "select product_voice,product_vedio,introduce,default_imgurl from weixin_commonshop_products where isvalid=true and id=".$pid." and customer_id=".$customer_id;
 $result = mysql_query($query) or die('Query failed: ' . mysql_error());
 $product_voice     = "";//音频
 $product_vedio     = "";//视频
 $product_introduce = "";//产品简介
 $voice_img         = "";//音频背景
while ($row = mysql_fetch_object($result)) {
	$product_voice     = $row->product_voice;
	$product_vedio     = $row->product_vedio;
	$product_introduce = $row->introduce;
	$voice_img         = $row->default_imgurl;
}
?>
<!DOCTYPE html>
<html>
<head>
<title>语音视频</title>
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
    <link type="text/css" rel="stylesheet" href="./css/goods/media.css" />
</head>
 <audio id="shakeAudio"><source src="<?php echo $product_voice; ?>"></audio>
<body data-ctrl=true>
<!-- header部门-->
	<!-- <header data-am-widget="header" class="am-header am-header-default header">
		<div class="am-header-left am-header-nav" onclick="goBack();">
			<img class="am-header-icon-custom header-btn"  src="./images/center/nav_bar_back.png" /><span  class = "header-btn">返回</span>
		</div>
	    <h1 class="header-title">语音视频</h1>
	    <div class="am-header-right am-header-nav">
		</div>
	</header>
	<div class='topDiv' style="height:49px;"></div> --> <!-- 暂时隐藏掉头部 -->
	<!-- header部门-->
<?php if(($product_voice=="" && $product_vedio!="")||($product_vedio=="" && $product_voice!="")){ ?><div class="heade"></div><?php } ?>
<?php if($product_vedio!=""){?>
<?php if($product_voice!=""){ ?><div class="head_word">视频</div><?php } ?>
<div class="video">
	<?php 
	$str1 = -1;
	$str2 = -1;
	$$str3 = -1;
	$str1 = strpos($product_vedio,"iframe");//把整个iframe代码上传情况
	$str2 = strpos($product_vedio,"embed");//把整个embed代码上传情况
	$str3 = strpos($product_vedio,".swf");//把整个flash代码上传情况
	if($str1==1 || $str2==1){//iframe或embed地址
		echo $product_vedio;
	}elseif($str3>0){//flash地址
	?>
	<embed src="<?php echo $product_vedio; ?>" allowFullScreen="true" quality="high" width="99%" height="320" style="margin:0.5%;" align="middle" allowScriptAccess="always" type="application/x-shockwave-flash"  loop="true" autostart=true></embed>
	<?php
	}else{//视频地址
	?> 
	<iframe frameborder="0" width="99%" height="320" style="margin:0.5%" src='<?php echo $product_vedio; ?>' allowfullscreen></iframe>
	<?php 
	} 
	if($str3>0){//flash格式情况
	?>
	<span style="color:red">(该视频为flash，需先安装flash插件才可看视频)<span>
	<?php
	}
	?>
</div>
<?php } ?>
<?php if($product_voice!=""){ ?>
<?php if($product_vedio != ""){ ?><div class="middle_word">语音</div><?php } ?>
<?php if($product_vedio==""){ ?><div class="voice_img"><img src="<?php echo $voice_img; ?>" width="100%" height="320" /></div><?php } ?>
<div class="music"><img src="images/goods_image/music.png"/><span id="changeword" >点击播放 语音介绍</span><span class="m_time">读取时长中</span></div>
<?php } ?>
<input type=hidden id="music_val" value="1">
<div class="foot">
	<?php if($isbrand_supply==1){ ?><div class="welcome"><?php echo $brand_supply_name; ?></div><?php } ?>
	<div class="introduct_word">商品简介</div>
	<div class="introduct"><?php echo $product_introduce; ?></div>	
</div>

 <script type="text/javascript" src="./assets/js/jquery.min.js"></script> 
<!--引入侧边栏 start-->
<?php  include_once('float.php');?>
<!--引入侧边栏 end-->
<script type="text/javascript">
	 var shakeAudio = null;	 
	 shakeAudio = document.getElementById("shakeAudio");
	 
	 setTimeout(function(){//读取音频时长，预先加载文件1秒
	 var m_min = parseInt(shakeAudio.duration/60);//音频分钟
	 var m_sec = parseInt(shakeAudio.duration%60);//音频秒数
	  $('.m_time').html(m_min+':'+m_sec+'s');//显示音频长度
	  $('.m_time').css('font-size','15px');}
	  ,3000)
	 $('.music').click(function(){//音频播放状态
		 if($('#music_val').val()==1){
			 shakeAudio.play();
			 $('#changeword').css('color','#D15C56');
			 $('#music_val').val(2);
	
		 }else{//音频暂停状态
			 $('#changeword').css('color','white');
			 $('#music_val').val(1);
			 shakeAudio.pause();
		 }
	 });
	 	$(".header-btn").click(
		function(){
			history.back();
		}
	);
	/*初始化视频规格*/
	$('iframe').attr("width","99%");
	$('iframe').attr("height","320");
	$('iframe').css("margin","0.5%");
	
	$('embed').attr("width","99%");
	$('embed').attr("height","320");
	$('embed').css("margin","0.5%");
</script>
</body>
</html>