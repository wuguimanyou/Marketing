<?php
header("Content-type: text/html; charset=utf-8"); 
require('../config.php');
require('../customer_id_decrypt.php');
require('../common/utility.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');

//头文件----start
require('../common/common_from.php');
require('select_skin.php');
$from_type = -1;    
$user_id   = -1;
if(empty($_SESSION["user_id_".$customer_id]) or empty($_SESSION["fromuser_".$customer_id])){
	$CF = new CheckFrom();
	$CF->isFrom($customer_id);
}else{
	$user_id   = $_SESSION["user_id_".$customer_id]; 
	$from_type = $_SESSION["from_type_".$customer_id];  //从哪里进来 0:网页 1:微信 2:APP 3:支付宝	
}
//头文件----end

$ids = $configutil->splash_new($_POST["ids"]);

//var_dump($ids);
$collect_ids = explode(',',$ids);
//var_dump($collect_ids);

	
?>
<!DOCTYPE html>
<html>
<head>
    <title>编辑</title>
    <!-- 模板 -->
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
    <link type="text/css" rel="stylesheet" href="./css/css_<?php echo $skin ?>.css" />  
    
    
    <!-- 模板 -->
    
    
    <!-- 页联系style-->
    <link type="text/css" rel="stylesheet" href="./css/goods/global.css" />
    <link type="text/css" rel="stylesheet" href="./css/goods/wode_shoucang_bianji1.css" />
    
    <!-- 页联系style-->
       <STYLE>
 .out{opacity:0.3;}   
   </STYLE> 
    
    
</head>

<body data-ctrl=true>
	<!-- header部门-->
	<!-- <header data-am-widget="header" class="am-header am-header-default header" style = "z-index:102; position : fixed; top:0px;background:black;">
		<div class="am-header-left am-header-nav header-btn" onclick="location.href='./my_collect.php?customer_id=<?php echo $customer_id_en;?>'">
			<img class="am-header-icon-custom"  src="./images/center/nav_bar_back.png"/><span>返回</span>
		</div>
	    <h1 class="header-title">编辑</h1>
	    <div class="am-header-right am-header-nav">
		</div>
	</header>
	<div class="topDiv" style="height:49px"></div> -->  <!-- 暂时屏蔽头部 -->
	<!-- header部门-->
	<div class = "container-title" >
		<span style="color:#626365;">共收藏<font class = "font-red">0</font>个商品</span>
	</div>
    <div class="containerWrapper">
    	<ul class= "list-wrapper">
	
    	</ul>
	</div>
	<div class = "bottom-bar" style = "position: fixed;bottom: 0; height: 70px; width: 100%;background: white;border: 1px solid #eee;padding-left: 10px;line-height: 70px;">
		<div class = "bottom-bar-left1" style = "width: 20px;float: left;vertical-align: middle;line-height: 70px;">
			<img class="all-select" src="./images/list_image/checkbox_off.png" width="20p" height="20" style = "width: 20px;height: 20px;vertical-align: middle;"/>
		</div>
		<span class="bottom-bar-left1-span" style = "float: left; margin-left: 5px;line-height:73px;">全选</span>
		<div class = "bottom-bar-right" style = "float: right;width: 120px;line-height: 70px; text-align: center;">
			<span class = "bottom-bar-button del-btn" style = "padding:10px 20px; color:white;">删除</span>
		</div>
    </div>
   
   <script type="text/javascript" src="./assets/js/jquery.min.js"></script>    
    <script type="text/javascript" src="./assets/js/amazeui.js"></script>
    <script type="text/javascript" src="./js/global.js"></script>
    <script type="text/javascript" src="./js/loading.js"></script>
    <script src="./js/jquery.ellipsis.js"></script>
    <script src="./js/jquery.ellipsis.unobtrusive.js"></script>
 <!--引入侧边栏 start-->
<?php  include_once('float.php');?>
<!--引入侧边栏 end-->   
</body>		
<script>
var $skin_img='<?php echo $images_skin?>';
</script>
<script>
//初始化
var config = {
	customer_id:'<?php echo $customer_id?>',
	customer_id_en:'<?php echo $customer_id_en?>',
	user_id:'<?php echo $user_id?>',
}
</script>
<!-- 页联系js -->
<script src="./js/goods/global.js"></script>
<script src="./js/goods/my_collect_edit_product.js"></script>
</body>
</html>