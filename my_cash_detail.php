<?php
header("Content-type: text/html; charset=utf-8");     
require('../config.php');
require('../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../common/utility_fun.php');

$link = mysql_connect(DB_HOST,DB_USER,DB_PWD); 
mysql_select_db(DB_NAME) or die('Could not select database');
require('select_skin.php');
//头文件----start
require('../common/common_from.php');
//头文件----end
$key_id = -1;
$key_id = $configutil->splash_new($_GET["b"]);//订单bianhao 

$getmoney     =  0; //申请提现金额
$status       =  0; //状态
$remark       = ''; //备注
$cash_type    = -1; //提现类型
$createtime   = ''; //时间
$percentage   =  0; //折率
$surplus_type = -1; //折率类型
$batchcode    = -1; //订单号
$query = "SELECT getmoney,status,remark,cash_type,createtime,percentage,surplus_type,batchcode FROM weixin_cash_being_log WHERE isvalid=true AND customer_id=$customer_id AND user_id=$user_id AND batchcode=$key_id LIMIT 1";
//echo $query;die;
$result= mysql_query($query) or die('Query failed 17: ' . mysql_error());
while( $row = mysql_fetch_object($result) ){
    $getmoney     = cut_num($row->getmoney,2);
    $status       = $row->status;
    $remark       = $row->remark;
    $cash_type    = $row->cash_type;
    $createtime   = $row->createtime;
    $percentage   = $row->percentage;
    $surplus_type = $row->surplus_type;
    $batchcode    = $row->batchcode;



}
//计算实际到账金额
if($percentage>0 && $surplus_type>0){
    $real_fee = $getmoney*$percentage/1000;
    $real_money = cut_num($getmoney-$real_fee,2);
}else{
    $real_money = cut_num($getmoney,2);
}

$sql = "SELECT real_name,phone,bind_account,bind_band,bind_bang_address FROM moneybag_account WHERE isvalid=true AND user_id=$user_id AND customer_id=$customer_id";
switch ($cash_type) {
    case '0':
        $cash_name = "微信零钱";
        $sql = $sql." AND type=1 LIMIT 1";
        break;
    case '1':
        $cash_name = "支付宝";
        $sql = $sql." AND type=2 LIMIT 2";
        break;
    case '2':
        $cash_name = "财付通";
        $sql = $sql." AND type=3 LIMIT 3";
        break;
    case '3':
        $cash_name = "银行卡";
        $sql = $sql." AND type=4 LIMIT 4";
        break;
    default:
        $cash_name = "未知";
        break;
}
switch ($status) {
    case '0':
        $status = '未审核';
        break;
    case '1':
        $status = '已同意';
        break;
    case '2':
        $status = '被驳回';
        break;
    case '3':
        $status = '提现取消';
        break;
    
    default:
        # code...
        break;
}
$real_name          = '';   //该提现绑定的姓名
$phone              = '';   //该提现绑定的电话
$bind_account       = '';   //绑定的账号
$bind_band          = -1;   //绑定的银行
$bind_bang_address  = -1;   //绑定银行所属支行
//再根据提现方式，查对应绑定的账号等
$res = mysql_query($sql) or die('Query failed 67: ' . mysql_error());
while( $row2 = mysql_fetch_object($res) ){
    $real_name          = $row2->real_name;
    $phone              = $row2->phone;
    $bind_account       = $row2->bind_account;
    $bind_band          = $row2->bind_band;
    $bind_bang_address  = $row2->bind_bang_address;
}

//echo $bind_account;die;


$skin="red";
switch ($skin) {
    case 'red':
        $images_skin='images_red';
        break;
    case 'blue':
        $images_skin='images_blue';
        break;
    case 'green':
        $images_skin='images_green';
        break;
    case 'pruple':
        $images_skin='images_pruple';
        break;
    case 'black':
        $images_skin='images_black';
        break;      
    default:
        $images_skin='images';
        break;
}


?>
<!DOCTYPE html>
<html>
<head>
    <title>零钱明细</title>
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
    
    <script type="text/javascript" src="./assets/js/jquery.min.js"></script>    
    <script type="text/javascript" src="./assets/js/amazeui.js"></script>
    <script type="text/javascript" src="./js/global.js"></script>
    <script type="text/javascript" src="./js/loading.js"></script>
    <script src="./js/jquery.ellipsis.js"></script>
    <script src="./js/jquery.ellipsis.unobtrusive.js"></script>
    
    <link rel="stylesheet" id="wp-pagenavi-css" href="./css/list_css/pagenavi-css.css" type="text/css" media="all">
	<link rel="stylesheet" id="twentytwelve-style-css" href="./css/list_css/style.css" type="text/css" media="all">
	<script src="./js/r_global_brain.js" type="text/javascript"></script>
	<script type="text/javascript" src="./js/r_jquery.mobile-1.2.0.min.js"></script>
	<link type="text/css" rel="stylesheet" href="./css/list_css/r_style.css" />
	<link type="text/css" rel="stylesheet" href="./css/personal.css" />  
    <link type="text/css" rel="stylesheet" href="./new_mshop/css/tixian.css" />   
    
<style>
	.beizhu{border-bottom:none;height:auto;line-height:50px;}
</style>

</head>
<!-- Loading Screen -->
<div id='loading' class='loadingPop'style="display: none;"><img src='./images/loading.gif' style="width:40px;"/><p class=""></p></div>

<body data-ctrl=true style="background:#f8f8f8;">
	<header data-am-widget="header" class="am-header am-header-default">
		<div class="am-header-left am-header-nav" onclick="goBack();">
			<img class="am-header-icon-custom" src="./images/center/nav_bar_back.png" style="vertical-align:middle;"/><span style="margin-left:5px;">返回</span>
		</div>
	    <h1 class="am-header-title" style="font-size:18px;">零钱明细</h1>
	</header>
	<div class="topDiv"></div>
    
    <div class="my_info">
        <div class="left" style="text-align:left;"><span>实际入账金额</span></div>
    	<div class="right" ><span class="red">￥<?php echo $real_money;?></span></div>
    </div>
    <div class="container">
        <div class="detail">
            <div class="detail_left"><span>申请提现金额</span></div>
            <div class="detail_right"><span><?php echo $getmoney;?></span></div>
        </div>
    	<div class="detail">
        	<div class="detail_left"><span>状态</span></div>
    		<div class="detail_right"><span><?php echo $status;?></span></div>
    	</div>
    	<div class="detail">
        	<div class="detail_left"><span>时间</span></div>
    		<div class="detail_right"><span><?php echo $createtime;?></span></div>
    	</div>
    	<div class="detail">
        	<div class="detail_left"><span style="letter-spacing: 0px;">交易单号</span></div>
    		<div class="detail_right"><span><?php echo $batchcode;?></span></div>
    	</div>
    	<div class="detail">
        	<div class="detail_left"><span>类型</span></div>
    		<div class="detail_right"><span><?php echo $cash_name;?></span></div>
    	</div>
		<div class="detail">
        	<div class="detail_left"><span>姓名</span></div>
    		<div class="detail_right"><span><?php echo $real_name;?></span></div>
    	</div>
    	<div class="detail">
        	<div class="detail_left"><span>电话</span></div>
    		<div class="detail_right"><span><?php echo $phone;?></span></div>
    	</div>
        <?php if( $cash_type > 0 ){?>
        <div class="detail">
            <div class="detail_left"><span>绑定账号</span></div>
            <div class="detail_right"><span><?php echo $bind_account;?></span></div>
        </div>
        <?php }?>
        <?php if( $cash_type == 3 ){?>
        <div class="detail">
            <div class="detail_left"><span>所属银行</span></div>
            <div class="detail_right"><span><?php echo $bind_band;?></span></div>
        </div>
        <div class="detail">
            <div class="detail_left"><span>所属支行</span></div>
            <div class="detail_right"><span><?php echo $bind_bang_address;?></span></div>
        </div>
        <?php }?>
        
    
    	<div class="detail beizhu">
        	<div class="detail_left"><span>备注</span></div>
    		<div class="detail_right"><?php echo $remark;?></div>
    	</div>
    </div>
</body>		

<script type="text/javascript">
    var winWidth = $(window).width();
    var winheight = $(window).height();
    function gotoHongBao(){	  //红包单号
    	alert("红包单号");
    }
    function lingqian(){   //零钱交易单号
    	alert("零钱交易单号");
    }
</script>

</body>
<!--引入侧边栏 start-->
<?php  include_once('float.php');?>
<!--引入侧边栏 end-->
<?php require('../common/share.php'); ?>
</html>