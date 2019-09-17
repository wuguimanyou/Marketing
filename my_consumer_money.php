<?php
header("Content-type: text/html; charset=utf-8");     
require('../config.php');
require('../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD); 
mysql_select_db(DB_NAME) or die('Could not select database');


//头文件----start
require('../common/common_from.php');
//头文件----end

require('select_skin.php');
$host = $_SERVER["HTTP_HOST"];
$new_baseurl = "http://".$host;


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
    <link type="text/css" rel="stylesheet" href="./css/css_<?php echo $skin ?>.css" /> 

    <!-- <link type="text/css" rel="stylesheet" href="./css/goods_css/dialog.css" /> -->
    <link type="text/css" rel="stylesheet" href="./css/self_dialog.css" />
    <link type="text/css" rel="stylesheet" href="./css/personal.css" />
    <!-- global css-->
    

</head>
<style type="text/css">
	.time{
		width:100%;
		height:40px;
		text-align: center;
		font-size: 15px;
		color:#999;
		line-height: 40px;
		font-family: "微软雅黑";
	}
	.sharebg{
		display: none;
	}
	.tis{
		width: 100%;
		color: #999;
		text-align: center;
		margin-top: 20px;
	}
</style>
<body data-ctrl=true style="background:#f8f8f8;">
	<!-- Loading Screen -->
	<div id='loading' class='loadingPop'style="display: none;"><img src='./images/loading.gif'/><p>数据加载中</p></div>
	<!-- Loading Screen -->

    <div id="containerDiv" class="xiaofei">
  
		<!-- 所有消费记录 start -->
	</div>
	<div class="tis">---已无更多记录---</div>
	<!-- basic js -->
    <script type="text/javascript" src="./assets/js/jquery.min.js"></script>    
    <script type="text/javascript" src="./assets/js/amazeui.js"></script>
    <!-- basic js -->
	<!-- global js -->
	<script type="text/javascript" src="./js/global.js"></script>
	<script type="text/javascript" src="./js/loading.js"></script>
	<script src="./js/extends_js/global.js"></script>
	<script src="./js/extends_js/monthCtrl.js"></script>
	<!-- global js -->

	<script type="text/javascript">
	var user_id = '<?php echo $user_id; ?>';
	var customer_id = '<?php echo $customer_id; ?>';
	var customer_id_en = '<?php echo $customer_id_en;?>';
	var new_baseurl = '<?php echo $new_baseurl; ?>';
	$(function(){  
   		showCalendar(1);
   		var wh=$(window).height();
   		var ww=$(window).width();
   		if(ww>320){
   		$('.am-calendar').css('left',ww/2);
   		$('.am-calendar').css('marginLeft',-185);
   		}
		get_all();//显示所有
	});
	
    //Jump to 详细
    function gotoViewRerecordDetail(obj){
		var batchcode = $(obj).data('batchcode');
    	window.location.href="my_consumer_detail.php?customer_id="+customer_id_en+"&b="+batchcode;
	}   
	function showtime(){
		$(".am-share").show().css('z-index','3001');
		$(".sharebg").show();
	}
	
	function searchData(search_time){//查询数据
		 $.ajax({
			url:'my_consumer_money.class.php',
			dataType: 'json',
			type: "post",
			data:{
			  'search_time':search_time,
			  'user_id':user_id,
			  'customer_id':customer_id
			},
			success:function(res){
			  console.log(res);
			  var content = "";
			  content += '<div class="time" onclick="showtime();">';
			  if(search_time==""){
				  content += search_time;
			  }else{
				  content += '日期';
			  }
			  content += '</div>';
			  for(id in res){
				 var _len = res[id].length;
				 var i = 0;
				 content += '<div class="title-top" style="height: 45px;background-color:#f8f8f8;" >';
				 content += '	<div class="month">'+id+'月</div>';
				 content += '	<div class="sub">';
				 content += '		<span>月账单</span>';
				 content += '		<img src="./images/vic/right_arrow.png" />';
				 content += '	</div>';
				 content += '</div>';
				 for(i=0;i<_len;i++){
				 	switch(res[id][i]['pay_type']){
				 		case '1': 		//微信支付
				 			var paystyle = "./images/info_image/weixin.png";
				 		break;

				 		case '2': 		//支付宝支付
				 			var paystyle = './images/info_image/zhifubao.png';
				 		break;

				 		case '3':  		//购物币支付
				 			var paystyle = './images/info_image/gouwubi.png';
				 		break;

				 		case '4': 		//会员卡余额支付
				 			var paystyle = './images/info_image/huiyuanka.png';
				 		break;

				 		case '5': 		//钱包零钱支付
				 			var paystyle = './images/info_image/wode_qianbao.png';
				 		break;

				 		case '6': 		//通联支付
				 			var paystyle = './images/info_image/card.png';
				 		break;

				 		case '7': 		//货到付款
				 			var paystyle = './images/info_image/daofu.png';
				 		break;

				 		case '8': 		//找人代付
				 			var paystyle = './images/info_image/daifu.png';
				 		break;

				 		case '9': 		//后台支付
				 			var paystyle = './images/info_image/houtai.png';
				 		break;
				 	}
				 	switch(res[id][i]['consume_way']){
				 		case '0':
				 			var consume_way = '微商城';
				 		break;				 		

				 		case '1':
						case '2':
						case '3':
				 			var consume_way = '餐饮';
				 		break;

						case '20':
						case '21':
						case '22':
				 			var consume_way = '线下商城';
				 		break; 
						
				 		case '30':
						case '31':
				 			var consume_way = 'KTV';
				 		break; 

				 		case '60':
						case '61':
						case '62':
				 			var consume_way = '酒店';
				 		break;

				 		case '100':
				 			var consume_way = '收银系统';
				 		break;

				 		case '101':
				 			var consume_way = '大礼包';
				 		break;
				 	}
				 	
					 content += "<div class='contentDiv'  onclick='gotoViewRerecordDetail(this);' data-batchcode='"+res[id][i]['batchcode']+"'>";
					 content += '	<div class="content-left">';
					 content += '		<img class="" id="paystyle"  src="'+paystyle+'" />';
					 content += '	</div>';
					 content += '	<div class="content-right">';
					 content += '		<div class="content-right-up">';
					 content += '			<div class="content-right-up-left" style="width:60%;">'+res[id][i]['name']+'</div>';
					 content += '			<div class="content-right-up-right"  style="width:40%;font-size:20px;color:red;">'+parseFloat(res[id][i]['money']).toFixed(2)+'</div>';
					 content += '		</div>';
					 content += '		<div class="content-right-down">'+consume_way;
					 content += '				<span style="color:#a1a1a1;font-size:13px;padding-left:5px;">'+res[id][i]['createtime']+'</span></div>';
					 content += '	</div>';
					 content += '</div>';			

				 }
			  }
			$('.xiaofei').html(content);
			},
			error:function(res){
				alert("数据加载出错");
			}
		});
	}
	
	</script>
</body>
<!--引入侧边栏 start-->
<?php  include_once('float.php');?>
<!--引入侧边栏 end-->
</html>