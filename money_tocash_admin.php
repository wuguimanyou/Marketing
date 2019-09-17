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


$query = "SELECT id,type,real_name,phone,bind_account,bind_band,bind_bang_address FROM moneybag_account WHERE isvalid=true AND user_id=".$user_id." AND customer_id=".$customer_id;

$isOpen_alipay 	 	= 0;	//是否开启支付宝提现
$isOpen_wechat 	 	= 0;	//是否开启微信零钱提现
$isOpen_financial 	= 0;	//是否开启财付通提现
$isOpen_bank 	 	= 0;	//是否开启银行卡提现
$isOpen_agreement 	= 0;	//是否开启提现协议
$agreement_content 	= '';	//提现协议
$sql = "SELECT isOpen_alipay,isOpen_wechat,isOpen_financial,isOpen_bank,isOpen_agreement,remark,agreement_content FROM moneybag_rule WHERE isvalid=true AND customer_id=".$customer_id." LIMIT 1";
$res = mysql_query($sql) or die('Query failed 14: ' . mysql_error());
while( $row = mysql_fetch_object($res) ){
	$isOpen_alipay		= $row->isOpen_alipay;
	$isOpen_wechat		= $row->isOpen_wechat;
	$isOpen_financial 	= $row->isOpen_financial;
	$isOpen_bank		= $row->isOpen_bank;
	$isOpen_agreement	= $row->isOpen_agreement;
	$agreement_content	= $row->agreement_content;
}



?>
<!DOCTYPE html>
<html>
<head>
    <title>提现账号管理</title>
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
    
    
    
    <link rel="stylesheet" id="wp-pagenavi-css" href="./css/list_css/pagenavi-css.css" type="text/css" media="all">
	  <link rel="stylesheet" id="twentytwelve-style-css" href="./css/list_css/style.css" type="text/css" media="all">
    <!--  <link rel="stylesheet" id="twentytwelve-style-css" href="./css/goods_css/dialog.css" type="text/css" media="all"> -->
  	
  	<link type="text/css" rel="stylesheet" href="./css/list_css/r_style.css" />
    <link type="text/css" rel="stylesheet" href="./css/password.css" />
    
<style>  
   .selected{border-bottom: 5px solid black; color:black; }
   .list {margin: 10px 5px 0 3px;	overflow: hidden;}
   .area-line{height:25px;width:1px;float:left;margin-top: 10px;padding-top: 20px;border-left:1px solid #cdcdcd;}
   .topDivSel{width:100%;height:45px;top:50px;padding-top:0px;background-color:white;}
   .infoBox{width:90%;margin:10px auto;;background-color:white;color:white;box-shadow: 0px 2px 2px #888888;position: relative;}
   .infoBox .ele{height: 40px;width:90%;line-height: 40px;margin:0 auto;}
   .red{color:red;}
   .black{color:black}
   .content_top{height: 45px;line-height:45px;background-color:#f8f8f8;}
   .info_header{position:absolute;height:50px;line-height: 50px;border-top-left-radius:5px;border-top-right-radius:5px;z-index: 999;width: 100%;}
   .content_bottom{height: 22px;line-height:22px;background-color:#f8f8f8;}
   .btn span{width:100%;color:white;height:45px;line-height:45px; padding:10px;letter-spacing:3px;}
   .info_header_left{float:left;padding-left:20px;font-size:20px;width:70%;}
   .info_header_right{float:right;padding-right:10px;text-decoration: underline;}
   .info_header_left span{vertical-align: middle;margin-left: 10px;}
   .border-bottom-color-green{border-bottom: 4px solid #189c3a;}
   .border-bottom-color-blue{border-bottom: 4px solid #1b709f;}
   .border-bottom-color-yellow{border-bottom: 4px solid #cb6920;}
   .border-bottom-color-red{border-bottom: 4px solid #ac3d4a;}
   .info_content{margin:10px auto;;background-color:white;padding-bottom:10px;border-bottom-left-radius:5px;border-bottom-right-radius:5px;display:block;}
   .info_content .ele{height: 30px;width:90%;line-height: 30px;margin:0 auto;}
   .ele .left{width:40%;float:left;color:#707070}
   .ele .right{width:60%;float:left;color:#707070}
   .ele img{width: 20px;height: 20px;vertical-align:middle;}
   .repair_btn{position: absolute;float:right;right:15px;top:0px;}
   .pop{position: absolute;float:right;right:50px;top:-21px;font-size: 25px;}
   .info_middle{height:50px;}
   .repair_btn img{width: 20px;height: 15px;vertical-align:middle;}

   //ld 点击效果
        .button{ 
          -webkit-transition-duration: 0.4s; /* Safari */
          transition-duration: 0.4s;
        }
        .buttonclick:hover{
          box-shadow:  0 0 6px 0 rgba(0,0,0,0.24);
        }
   
</style>


</head>
<!-- Loading Screen -->
<div id='loading' class='loadingPop'style="display: none;"><img src='./images/loading.gif' style="width:40px;"/><p class=""></p></div>

<body data-ctrl=true style="background:#f8f8f8;">
	<!--
	<header data-am-widget="header" class="am-header am-header-default">
		<div class="am-header-left am-header-nav" onclick="goBack();">
			<img class="am-header-icon-custom" src="./images/center/nav_bar_back.png" style="vertical-align:middle;"/><span style="margin-left:5px;">返回</span>
		</div>
	    <h1 class="am-header-title" style="font-size:18px;">提现账号管理</h1>
	</header>
	<div class="topDiv"></div>
	-->
	<div class="content_top">
		<div style="width:100%;padding-left:20px;text-align: left;">
            <img src="./images/info_image/xiugai.png" alt="" style="width: 20px;height: 15px;vertical-align:middle;"/>
            <span style="vertical-align: middle;">提现账号管理</span>
        </div>
    </div>
    
    
    <?php
		$wc_sql = $query." AND type=1 LIMIT 1";
		$wc_res = mysql_query($wc_sql);
		$id     = -1;
		$real_name    = '尚未绑定';
		$phone        = '尚未绑定';
		$bind_account = '尚未绑定';
        while( $row = mysql_fetch_object( $wc_res ) ){
                $id           = $row->id;
                $real_name    = $row->real_name;
                $phone        = $row->phone;
                $phone        = substr($phone, 0, 3).'****'.substr($phone, 7);
                $bind_account = $row->bind_account;
          }
    ?>
    <!-- 微信零钱 start -->
	<?php if($isOpen_wechat){ ?>
    <div class="infoBox"  id="weixin_info">
         <div class="info_header border-bottom-color-green" style="background-color: #21ac45;">
            <div class="info_header_left">
               <img src="./images/info_image/weixin_white.png" alt="" style="width: 30px;height: 30px;vertical-align:middle;"/>
               <span>微信零钱</span> 
            </div>
         </div>
         <div class="info_middle"></div>
         <div class="info_content" style="position: relative;">
            <div class="ele">
                <div class="left">真实姓名:</div>
                <div class="right"><?php echo $real_name;?></div>
            </div>
            <div class="ele">
                <div class="left">手机号码:</div>
                <div class="right"><?php echo $phone;?></div>
            </div>
            <div class="repair_btn" onclick="editInfo('weixin');">
                <img class="button buttonclick" src="./images/info_image/xiugai.png" />
            </div>
            <div class="pop">
                <span class="menu_selected" id="arrow_1" style="color:#21ac45;">◆</span>
            </div>
        </div>
    </div>
	<?php } ?>
    <!-- 微信零钱 end -->
    
    <?php 
          $al_sql = $query." AND type=2 LIMIT 1";
          $al_res = mysql_query($al_sql);
          $id     = -1;
		  $real_name    = '尚未绑定';
		  $phone        = '尚未绑定';
		  $bind_account = '尚未绑定';
          while( $row2 = mysql_fetch_object( $al_res ) ){
                $id             = $row2->id;
                $real_name      = $row2->real_name;
                $phone          = $row2->phone;
                $phone          = substr($phone, 0, 3).'****'.substr($phone, 7);
                $bind_account   = $row2->bind_account;           
          }
        
         
    ?>
    <!-- 支付宝零钱 start -->
	<?php if($isOpen_alipay){ ?>
    <div class="infoBox"  id="zhifubao_info">
         <div class="info_header border-bottom-color-blue" style="background-color: #2286bd;">
            <div class="info_header_left">
               <img src="./images/info_image/zhifubao-white.png" alt="" style="width: 30px;height: 30px;vertical-align:middle;"/>
               <span>支付宝</span> 
            </div>
         </div>
         <div class="info_middle"></div>
         <div class="info_content" style="position: relative;">
            <div class="ele">
                <div class="left">真实姓名:</div>
                <div class="right"><?php echo $real_name;?></div>
            </div>
            <div class="ele">
                <div class="left">手机号码:</div>
                <div class="right"><?php echo $phone;?></div>
            </div>
            <div class="ele">
                <div class="left">支付宝账户:</div>
                <div class="right"><?php echo $bind_account;?></div>
            </div>
            
            <div class="repair_btn" onclick="editInfo('zhifubao');">
                <img class="button buttonclick" src="./images/info_image/xiugai.png" alt="" style=""/>
            </div>
            <div class="pop">
                <span class="menu_selected" id="arrow_1" style="color:#2286bd;">◆</span>
            </div>
        </div>
    </div>
	<?php } ?>
    <!-- 支付宝零钱 end -->
    
        
    <?php
          $cf_sql = $query." AND type=3 LIMIT 1";
          $cf_res = mysql_query($cf_sql);
          $id     = -1;
		  $real_name    = '尚未绑定';
		  $phone        = '尚未绑定';
		  $bind_account = '尚未绑定';
          while( $row3 = mysql_fetch_object($cf_res) ){
                $id           = $row3->id;
                $real_name    = $row3->real_name;
                $phone        = $row3->phone;
                $phone        = substr($phone, 0, 3).'****'.substr($phone, 7);
                $bind_account = $row3->bind_account;
                
          }
    ?>
    <!-- 财付通零钱 start -->
	<?php if($isOpen_financial){ ?>
    <div class="infoBox"  id="caifutong_info">
         <div class="info_header border-bottom-color-yellow" style="background-color: #fb862f;">
            <div class="info_header_left">
               <img src="./images/info_image/caifutong_white.png" alt="" style="width: 30px;height: 30px;vertical-align:middle;"/>
               <span>财付通</span> 
            </div>
         </div>
         <div class="info_middle"></div>
         <div class="info_content" style="position: relative;">
            <div class="ele">
                <div class="left">真实姓名:</div>
                <div class="right"><?php echo $real_name;?></div>
            </div>
            <div class="ele">
                <div class="left">手机号码:</div>
                <div class="right"><?php echo $phone;?></div>
            </div>
            <div class="ele">
                <div class="left">财付通账户:</div>
                <div class="right"><?php echo $bind_account;?></div>
            </div>
            <div class="repair_btn" onclick="editInfo('caifutong');">
                <img class="button buttonclick" src="./images/info_image/xiugai.png" />
            </div>
            <div class="pop">
                <span class="menu_selected" id="arrow_1" style="color:#fb862f;">◆</span>
            </div>
        </div>
    </div>
	<?php } ?>
    <!-- 财付通零钱 end -->
    

    <?php
		$yh_sql = $query." AND type=4 LIMIT 1";
		$yh_res = mysql_query($yh_sql);
		$id                 = -1;
		$real_name          = '尚未绑定';
		$phone              = '尚未绑定';
		$bind_account       = '尚未绑定';
		$bind_band          = '尚未绑定';
		$bind_bang_address  = '尚未绑定';
          while( $row4 = mysql_fetch_object( $yh_res ) ){
                $id                 = $row4->id;
                $real_name          = $row4->real_name;
                $phone              = $row4->phone;
                $phone              = substr($phone, 0, 3).'****'.substr($phone, 7);
                $bind_band          = $row4->bind_band;
                $bind_account       = $row4->bind_account;
                $bind_account       = '*** **** **** '.substr($bind_account,15,5);
                $bind_bang_address  = $row4->bind_bang_address;
                
          }
        
    ?>
    <!-- 银行卡零钱 start -->
	<?php if($isOpen_bank){ ?>
    <div class="infoBox"  id="card_info">
         <div class="info_header border-bottom-color-red" style="background-color: #c2505d;">
            <div class="info_header_left">
               <img src="./images/info_image/card_white.png" alt="" style="width: 30px;height: 30px;vertical-align:middle;"/>
               <span>银行卡</span> 
            </div>
         </div>
         <div class="info_middle"></div>
         <div class="info_content" style="position: relative;">
            <div class="ele">
                <div class="left">真实姓名:</div>
                <div class="right"><?php echo $real_name;?></div>
            </div>
            <div class="ele">
                <div class="left">手机号码:</div>
                <div class="right"><?php echo $phone;?></div>
            </div>
            <div class="ele">
                <div class="left">开户银行:</div>
                <div class="right"><?php echo $bind_band;?></div>
            </div>
            <div class="ele">
                <div class="left">所属支行:</div>
                <div class="right"><?php echo $bind_bang_address;?></div>
            </div>
            <div class="ele">
                <div class="left">开户账户:</div>
                <div class="right"><?php echo $bind_account;?></div>
            </div>
            <div class="repair_btn" onclick="editInfo('card');">
                <img class="button buttonclick" src="./images/info_image/xiugai.png" />
            </div>
            <div class="pop">
                <span class="menu_selected" id="arrow_1" style="color:#c2505d;">◆</span>
            </div>
        </div>
    </div>
	<?php } ?>
    <!-- 银行卡零钱 end -->
    <script type="text/javascript" src="./assets/js/jquery.min.js"></script>    
    <script type="text/javascript" src="./assets/js/amazeui.js"></script>
    <script type="text/javascript" src="./js/global.js"></script>
    <script type="text/javascript" src="./js/loading.js"></script>
    <script src="./js/jquery.ellipsis.js"></script>
    <script src="./js/jquery.ellipsis.unobtrusive.js"></script>
    <script src="./js/r_global_brain.js" type="text/javascript"></script>
    <script type="text/javascript" src="./js/r_jquery.mobile-1.2.0.min.js"></script>
    <script src="./js/sliding.js"></script>
</body>		

<script type="text/javascript">
   function editInfo(type){
       switch(type) {
         case 'weixin':
            window.location.href = "my_alipay.php?customer_id=<?php echo $customer_id_en;?>&t=1";
           break;
         case 'zhifubao':
            window.location.href = "my_alipay.php?customer_id=<?php echo $customer_id_en;?>&t=2";
           break;
         case 'caifutong':
            window.location.href = "my_alipay.php?customer_id=<?php echo $customer_id_en;?>&t=3";
           break;
         case 'card':
            window.location.href = "my_alipay.php?customer_id=<?php echo $customer_id_en;?>&t=4";
           break;
       }
       
    
   }



</script>
<!--引入侧边栏 start-->
<?php  include_once('float.php');?>
<!--引入侧边栏 end-->
</html>