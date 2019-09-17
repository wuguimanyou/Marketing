<?php
header("Content-type: text/html; charset=utf-8");     
require('../config.php');
require('../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]

$link = mysql_connect(DB_HOST,DB_USER,DB_PWD); 
mysql_select_db(DB_NAME) or die('Could not select database');

//头文件----start
require('../common/common_from.php');
//头文件----end

$batchcode = $_GET['b'];//获取订单号

$name 			= '';//产品名
$money 			=  0;//金额
$createtime 	= '';//时间
$pay_type 		= '';//支付方式
$remark 		= '';//支付时间
$custom 		= '';
$consume_way_type = '';//来源



$sql = "SELECT custom FROM weixin_commonshop_currency WHERE isvalid=true AND customer_id = $customer_id LIMIT 1";
$res = mysql_query($sql);
while( $row2 = mysql_fetch_array($res) ){
	$custom = $row2->custom;
}
if($custom == '' || $custom == NULL){
	$custom = "购物币";
}


$query = "SELECT name,money,createtime,pay_type,remark,consume_way,batchcode FROM consumption_log_t WHERE batchcode='$batchcode' AND isvalid=true LIMIT 1";
$result= mysql_query($query);
while($row=mysql_fetch_object($result)){
	$name 		= $row->name;
	$money 		= $row->money;
	$createtime = $row->createtime;
	$batchcode 	= $row->batchcode;
	$pay_type 	= $row->pay_type;
	switch ($pay_type) {
		case '1':
			$paystyle = "微信支付";
			break;
		case '2':
			$paystyle = "支付宝支付";
			break;
		case '3':
			$paystyle = $custom."支付";
			break;
		case '4':
			$paystyle = "会员卡余额支付";
			break;
		case '5':
			$paystyle = "钱包零钱支付";
			break;
		case '6':
			$paystyle = "通联支付";
			break;
		case '7':
			$paystyle = "货到付款";
			break;
		case '8':
			$paystyle = "找人代付";
			break;
		case '8':
			$paystyle = "后台支付";
			break;

		default:
			# code...
			break;
	}
	$consume_way = $row->consume_way;
	switch ($consume_way) {
		case '0':
			$consume_way_type = "微商城";
			break;
		case '1':
		case '2':
		case '3':
			$consume_way_type = '餐饮';
		break;
		case '20':
		case '21':
		case '22':
			$consume_way_type = '线下商城';
		break; 	
		case '30':
		case '31':
			$consume_way_type = 'KTV';
		break; 
		case '60':
		case '61':
		case '62':
			$consume_way_type = '酒店';
		break;
		case '100':
			$consume_way_type = "收银系统";
			break;
		case '101':
			$consume_way_type = "大礼包";
			break;
		
		default:
			# code...
			break;
	}
	$remark 	= $row->remark;
}
//echo $query;

?>
<!DOCTYPE html>
<html>
<head>
    <title>消费明细</title>
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
   	<!-- global css-->
    <link type="text/css" rel="stylesheet" href="./assets/css/amazeui.min.css" />
    <link type="text/css" rel="stylesheet" href="./css/extends_css/extends.css" />
    <!-- global css-->
    <!-- 页联系style-->
    <link type="text/css" rel="stylesheet" href="./css/extends_css/appxiazai1-1.css" />
    <!-- 页联系style-->
    
    
    
</head>
<style>  
   .beizhu .detail_right{text-align: right;}
   .container .beizhu .p-info{height: auto;line-height: 20px;text-align: left;padding: 15px 0}
</style>
<body data-ctrl=true style="background:#f8f8f8;">
	<!-- Loading Screen -->
	<div id='loading' class='loadingPop'style="display: none;"><img src='./images/loading.gif'/><p>数据加载中</p></div>
	<!-- Loading Screen -->
	<!-- header部门-->
	<!-- <header data-am-widget="header" class="am-header am-header-default header-wrapper">
		<div class="am-header-left am-header-nav header-btn" onclick="goBack();">
			<img class="am-header-icon-custom"  src="./images/center/nav_bar_back.png"/><span>返回</span>
		</div>
	    <h1 class="header-title">消费明细</h1>
	    <div class="am-header-right am-header-nav">
		</div>
	</header> --><!-- 暂时隐藏头部导航栏 -->
	<!-- header部门-->
    <div id="containerDiv" class="detail">
    	<!-- <div class="last-top"></div> --><!-- 占位DIV -->
	 	<div class="my_info">
	    	<div class="left" style="text-align:left;"><span >消费金额 </span></div>
	    	<div class="right" ><span class="red">￥<?php echo round($money,2);?></span></div>
	        
	    </div>
	    <div class="container">
	    	<div class="detail">
	        	<div class="detail_left"><span style="letter-spacing:0;">消费类型</span></div>
	    		<div class="detail_right"><span style="font-size:12px;"><?php echo $consume_way_type;?></span></div>
	    	</div>
	    	<div class="detail">
	        	<div class="detail_left"><span   style="letter-spacing: 0px;">支付时间</span></div>
	    		<div class="detail_right"><span><?php echo $createtime;?></span></div>
	    	</div>
	    	<div class="detail">
	        	<div class="detail_left"><span  style="letter-spacing: 0px;">商品订单号</span></div>
	    		<div class="detail_right" onclick='viewdetail("<?php echo $batchcode;?>","<?php echo $consume_way;?>");'>
	    			<span style="vertical-align:middle;"><?php echo $batchcode;?></span>
	    			<img src="./images/center/arrow3.png" style="width:8px;height:10px;" alt="">
	    		</div>
	    	</div>
	    	<div class="detail" >
	        	<div class="detail_left"><span  style="letter-spacing: 0px;">支付方式</span></div>
	    		<div class="detail_right"><span><?php echo $paystyle;?></span></div>
	    	</div>
	    	<!-- <div class="detail">
	        	<div class="detail_left"><span  style="letter-spacing: 0px;">订单类型</span></div>
	    		<div class="detail_right"><span>美食</span></div>
	    	</div> -->
	    	<div class="detail beizhu" style="border-bottom:none;">
	        	<div class="detail_left"><span  style="letter-spacing: 0px;">商品信息</span></div>
	    		<div class="detail_right p-info" ><span style="float: right;white-space: normal;width: 100%;text-align: right;"><?php echo $name;?></span></div>
	    	</div>
	    </div>
	</div>
	
	<!-- basic js -->
    <script type="text/javascript" src="./assets/js/jquery.min.js"></script>    
    <script type="text/javascript" src="./assets/js/amazeui.js"></script>
    <!-- basic js -->
	<!--引入侧边栏 start-->
<?php  include_once('float.php');?>
<!--引入侧边栏 end-->
	<!-- global js -->
	<script type="text/javascript" src="./js/loading.js"></script>

	<!-- global js -->

	<script type="text/javascript">
    //Jump to 详细
    function viewdetail(batchcode,type){
		switch(type) {
			case '0':
				window.location.href="orderlist_detail.php?customer_id=<?php echo $customer_id_en;?>&batchcode="+batchcode+"&user_id=<?php echo passport_encrypt($user_id)?>";
				break;
			case '101':
				window.location.href="order_packages_list.php?customer_id=<?php echo $customer_id_en;?>";
				break;
		}
    	
	}   
	</script>
    

</body>
<!--引入侧边栏 start-->
<?php  include_once('float.php');?>
<!--引入侧边栏 end-->
</html>