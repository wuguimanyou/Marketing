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

$type        = $configutil->splash_new($_GET["t"]);

switch ($type) {
  case '1':
    $pay_name = "微信支付";
    $pay_img  = './images/info_image/weixin.png';
    $bind_name= '';
  break;

  case '2':
    $pay_name = "支付宝";
    $pay_img  = './images/info_image/zhifubao.png';
    $bind_name= '支付宝账号';
  break;

  case '3':
    $pay_name = "财付通";
    $pay_img  = './images/info_image/caifutong.png';
    $bind_name= '财付通账号';
  break;

  case '4':
    $pay_name = "银行卡";
    $pay_img  = './images/info_image/card.png';
    $bind_name= '银行卡卡号';
    $account_bankname = '开户行';
    $account_address  = '所属支行';
  break;
  
  default:
    # code...
    break;
}

$id                 = -1;
$phone              = '';//绑定的电话号码
$real_name          = '';//真实姓名
$bind_account       = '';//绑定的账号
$bind_band          = '';//开户行
$bind_bang_address  = '';//所属支行

$query = "SELECT id,phone,real_name,bind_account,bind_band,bind_bang_address FROM moneybag_account where isvalid=true AND customer_id=".$customer_id." AND user_id=".$user_id." AND type=".$type." LIMIT 1";
$result= mysql_query($query)or die('Query failed 56: ' . mysql_error());
while($row=mysql_fetch_object($result)){
    $id                 = $row->id;
    $phone              = $row->phone;
    $real_name          = $row->real_name;
    $bind_account       = $row->bind_account;
    $bind_band          = $row->bind_band;
    $bind_bang_address  = $row->bind_bang_address;
}
$sys_id = -1;
$pay_password = '';
$query = "SELECT id FROM system_user_t WHERE isvalid=true AND customer_id=$customer_id AND user_id=$user_id LIMIT 1";
$result= mysql_query($query)or die('Query failed 67: ' . mysql_error());
while($row=mysql_fetch_object($result)){
    $sys_id = $row->id;

}

$paypassword = '';
$query = "SELECT paypassword FROM user_paypassword WHERE isvalid=true AND user_id = $user_id LIMIT 1";
$result= mysql_query($query) or die('Query failed 37: ' . mysql_error());
while( $row = mysql_fetch_object($result) ){
    $paypassword = $row->paypassword;
}

$is_set_password = 0;//是否已设置支付密码
if($paypassword != ''){
	$is_set_password = 1;
}



?>
<!DOCTYPE html>
<html>
<head>
    <title>账号管理</title>
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
    
    

    <link rel="stylesheet" id="wp-pagenavi-css" href="./css/list_css/pagenavi-css.css" type="text/css" media="all">
	<link rel="stylesheet" id="twentytwelve-style-css" href="./css/list_css/style.css" type="text/css" media="all">
    <!-- <link rel="stylesheet" id="twentytwelve-style-css" href="./css/goods_css/dialog.css" type="text/css" media="all"> -->
	
	<link type="text/css" rel="stylesheet" href="./css/list_css/r_style.css" />
    <link type="text/css" rel="stylesheet" href="./css/password.css" />
    
<style>  
   .selected{border-bottom: 5px solid black; color:black; }
   .list {margin: 10px 5px 0 3px;	overflow: hidden;}
   .area-line{height:25px;width:1px;float:left;margin-top: 10px;padding-top: 20px;border-left:1px solid #cdcdcd;}
   .topDivSel{width:100%;height:45px;top:50px;padding-top:0px;background-color:white;}
   .infoBox{width:100%;margin:10px auto;;background-color:white;border-top:1px solid #eee;border-bottom:1px solid #eee;}
   .infoBox .ele{height: 40px;width:90%;line-height: 40px;margin:0 auto;}
   .ele .left{width:40%;float:left;color:#333333}
   .ele .right{width:60%;float:left;color:#a1a1a1;}
   .ele img{width: 20px;height: 20px;vertical-align:middle;}
   .red{color:red;}
   .black{color:black}
   .line{background-color: #eee;margin-left: 10px;height: 1px;}
   .content_top{background-color:#f8f8f8;}
   .content_bottom{height: 22px;line-height:22px;background-color:#f8f8f8;}
   .btn{width:80%;margin:20px auto;text-align:center;}
  .btn span{width:100%;height:45px;line-height:45px; padding:10px;letter-spacing:3px;}
  .content_top .detail{width:100%;text-align: center;font-size:20px;height: 35px;line-height:35px;margin-bottom: 10px;}
  .am-share1{position: absolute; width: 100%;top:30%;z-index: 1100;display: none;}*/
  .pass_bgc{position: absolute;width: 100%;height:200%;opacity: 0.5;background: #000;z-index: 999;display: none;}
</style>


</head>
<!-- Loading Screen -->
<div id='loading' class='loadingPop'style="display: none;"><img src='./images/loading.gif' style="width:40px;"/><p class=""></p></div>
<div class="am-share1">
        <div class="box">
            <h1>输入支付密码</h1>
            <label for="ipt">
                <ul>
                    <li></li>
                    <li></li>
                    <li></li>
                    <li></li>
                    <li></li>
                    <li></li>
                </ul>
            </label>
            <input type="tel" id="ipt" maxlength="6">
            <div style="width:100%;text-align: right;;"> <a onclick='modify_password();'>密码管理</a></div>
            <a class="commtBtn" onclick="submit();" style="display:none;" href="javascript:void(0);">确认</a>
        </div>
  </div>
  <div class="pass_bgc" id="pass_bgc" style="position: absolute;width: 100%;height:200%;opacity: 0.5;background: #000;z-index: 999;display: none;"></div>
<body data-ctrl=true style="background:#f8f8f8;">
	<!--<header data-am-widget="header" class="am-header am-header-default">
		<div class="am-header-left am-header-nav" onclick="goBack();">
			<img class="am-header-icon-custom" src="./images/center/nav_bar_back.png" style="vertical-align:middle;"/><span style="margin-left:5px;">返回</span>
		</div>
	    <h1 class="am-header-title" style="font-size:18px;">账号管理</h1>
	</header>
	-->
	<div class="topDiv"></div>
	    
	<div class="content_top">
		<div class="detail">
            <img src="<?php echo $pay_img;?>" alt="" style="width: 48px;vertical-align:top;height:43px;"/>
        </div>
        <div class="detail" style="font-size: 15px;"><span><?php echo $pay_name;?></span></div>
    </div>
 
    <div class="infoBox">
        <div class="ele">
            <div class="left">手机号码</div>
            <div class="right"><input type="tel" id="phoneNum" value="<?php echo $phone;?>" style="border: none;" placeholder="请填写您的电话号码" /></div>
        </div>
        <div class="line" style="margin-right: 10px;"></div>
        <div class="ele">
            <div class="left">真实姓名</div>
            <div class="right"><input type="text" id="realName" value="<?php echo $real_name;?>"  style="border: none;" placeholder="请填写您的真实姓名" /></div>
        </div>
        <div class="line" style="margin-right: 10px;"></div>
        <?php if($type !=1 ){?>
        <div class="ele">
            <div class="left"><? echo $bind_name;?></div>
            <div class="right"><input type="text" id="account" value="<?php echo $bind_account;?>"  style="border: none;" placeholder="请填写您需绑定的账号" /></div>
        </div>
        <?php }?>
        <?php if($type==4){?>
        <div class="line" style="margin-right: 10px;"></div>
        <div class="ele">
            <div class="left"><? echo $account_bankname;?></div>
            <div class="right"><input type="text" id="account_bankname" name="account_bankname" value="<?php echo $bind_band;?>"  style="border: none;" placeholder="请填写您的开户行" /></div>
        </div>

        <div class="line" style="margin-right: 10px;"></div>
        <div class="ele">
            <div class="left"><? echo $account_address;?></div>
            <div class="right"><input type="text" id="account_address" name="account_address" value="<?php echo $bind_bang_address;?>"  style="border: none;" placeholder="开户行所属支行" /></div>
        </div>
        <?php }?>
    </div>
    <div class="btn" onclick="commit();"><span>确认</span></div>
    
    <script type="text/javascript" src="./assets/js/jquery.min.js"></script>    
    <script type="text/javascript" src="./assets/js/amazeui.js"></script>
    <script type="text/javascript" src="./js/global.js"></script>
    <script type="text/javascript" src="./js/loading.js"></script>
    <!-- <script type="text/javascript" src="./js/my_favourite.js"></script> -->
    <script src="./js/jquery.ellipsis.js"></script>
    <script src="./js/jquery.ellipsis.unobtrusive.js"></script>
    <script src="./js/r_global_brain.js" type="text/javascript"></script>
    <script type="text/javascript" src="./js/r_jquery.mobile-1.2.0.min.js"></script>
    <script src="./js/sliding.js"></script>
    
</body>		

<script type="text/javascript">

	$(function(){
		var is_set_password = "<?php echo $is_set_password;?>";
		if( is_set_password == 0 ){
			function callbackfunc(){
            	window.location.href="modify_password.php?customer_id=<?php echo $customer_id_en;?>";
            }
            showConfirmMsg("提示","您尚未设置支付密码，是否立即前往设置？","确定","取消",callbackfunc);
		}
	})

    $("#pass_bgc").click(function() {
		$('#ipt').val("");
		$('li').text("");
        $('#ipt').val('');
        $(this).hide();
        $(".am-share1").hide();
    });

    function commit(){

    	var customer_id = "<?php echo $customer_id_en;?>";
		var phone       = $("#phoneNum").val();
		var name        = $("#realName").val();
		var account     = $("#account").val();
		var type        = "<?php echo $type;?>";
    	//假如绑定的是银行卡，则需要取下面2个值
        if(type==4){
            var account_bankname = $("#account_bankname").val();
            var account_address  = $("#account_address").val();
        }else{
            var account_bankname = -1;
            var account_address  = -1;
        }
        
        if(phone==''){
            showAlertMsg('提醒','手机不能为空','确认');
            return false;
        }
        if(name==''){
            showAlertMsg('提醒','真实姓名不能为空','确认');
            return false;
        }
        if(account==''){
            showAlertMsg('提醒','请填写需绑定的账号','确认');
            return false;
        }
        if(type==4){
            var account_bankname = $("#account_bankname").val();
            var account_address  = $("#account_address").val();
            if(account_bankname  ==''){
                showAlertMsg('提醒','请填写需绑定的开户行','确认');
                return false;
            }
            if(account_bankname==''){
                showAlertMsg('提醒','请填写需绑定的所属支行','确认');
                return false;
            }
        }
        var phoneNum = /^1[34578]{1}\d{9}$/;
        if(!phoneNum.test(phone)){
           showAlertMsg('提醒','您输入的电话号码有误，请重新输入','确认');
           return false; 
        }
      	var save_type = "CheckPassword";
        $.ajax({
        	url 		: 'save_pay_account.php',
        	dataType 	: 'json',
            type 		: "post",
            data:{
            	customer_id:customer_id,
            	save_type:save_type
            },
            success:function(data){

       			if(data==40001){
        			function callbackfunc(){
                    	window.location.href="modify_password.php?customer_id=<?php echo $customer_id_en;?>";
                    }
                    showConfirmMsg("提示","您尚未设置支付密码，是否立即前往设置？","确定","取消",callbackfunc);
        		}else if(data==40002){
        			$("#pass_bgc").show();
        			$(".am-share1").show();
        		}else if(data==40005){
                    showAlertMsg('提醒','您输入的电话号码有误，请重新输入','确认');
                    return false;
                }
            } 
        }); 
    }

    function submit(){
    	var customer_id = "<?php echo $customer_id_en;?>";
		var phone       = $("#phoneNum").val();
		var name        = $("#realName").val();
		var account     = $("#account").val();
		var type        = "<?php echo $type;?>";  
    	var password    = $('input').val();
        var pw_lenght   = $('input').val().length;

        if( pw_lenght != 6 ){
            showAlertMsg('提醒','请输入六位长度的数字密码','确认');
            return false;
        }
        if(password==''){
            showAlertMsg('提醒','密码不能为空','确认');
            return false;
        }
        //假如绑定的是银行卡，则需要取下面2个值
        if(type==4){
            var account_bankname = $("#account_bankname").val();
            var account_address  = $("#account_address").val();
        }else{
            var account_bankname = -1;
            var account_address  = -1;
        }
        var phoneNum = /^1[34578]{1}\d{9}$/;
        if(!phoneNum.test(phone)){
           showAlertMsg('提醒','您输入的电话号码有误，请重新输入','确认');
           return false; 
        }
        var save_type = "bind_account";
        $.ajax({
            url 		:'save_pay_account.php',
            dataType 	: 'json',
            type 		:"post",
            data 		:{
            			  'pw':password,
		                  'phone':phone,
		                  'name':name,
		                  'account':account,
		                  'type':type,
		                  'customer_id':customer_id,
		                  'account_bankname':account_bankname,
		                  'account_address':account_address,
		                  'save_type':save_type
		                },
            success:function(data){
                if(data=='400'){
                     $(".am-share1").hide();
                     $(".pass_bgc").hide();
                     showAlertMsg('提醒','您输入的密码有误，请重新输入','确认');
                     return false;
                }else if(data=='401' || data=='402'){
                     $(".am-share1").hide();
                     $(".pass_bgc").hide();
                     window.location.href="money_tocash.php?customer_id=<?php echo $customer_id_en;?>";
                     return false;
                }else if(data=='40001'){
                	function callbackfunc(){
                        	window.location.href="modify_password.php?customer_id=<?php echo $customer_id_en;?>";
	                    }
	                showConfirmMsg("提示","您尚未设置支付密码，是否立即前往设置？","确定","取消",callbackfunc);
                }else if(data==40005){
                    showAlertMsg('提醒','您输入的电话号码有误，请重新输入','确认');
                    return false;
                }
            }
        });
    }

    function modify_password(){
    	window.location.href="modify_password.php?customer_id=<?php echo $customer_id_en;?>";
    }

$('input').on('input', function (e){
        var numLen = 6;
        var pw = $('input').val();
        var list = $('li');
        for(var i=0; i<numLen; i++){
            if(pw[i]){
                $(list[i]).text('·');
            }else{
                $(list[i]).text('');
            }
        }
    });
$('#ipt').on('keyup', function (e){
        var num_len = $('input').val().length;
        if(num_len == 6){
            $(".commtBtn").show();
        }else{
            $(".commtBtn").hide();
        }
    });
</script>
<!--引入侧边栏 start-->
<?php  include_once('float.php');?>
<!--引入侧边栏 end-->
</html>