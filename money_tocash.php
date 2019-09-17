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


$isOpen_alipay 	 	= 0;	//是否开启支付宝提现
$isOpen_wechat 	 	= 0;	//是否开启微信零钱提现
$isOpen_financial 	= 0;	//是否开启财付通提现
$isOpen_bank 	 	= 0;	//是否开启银行卡提现
$isOpen_agreement 	= 0;	//是否开启提现协议
$agreement_content 	= '';	//提现协议
$sql = "SELECT isOpen_alipay,isOpen_wechat,isOpen_financial,isOpen_bank,isOpen_agreement,remark,agreement_content FROM moneybag_rule WHERE isvalid=true AND customer_id=".$customer_id." LIMIT 1";
$res = mysql_query($sql) or die('Query failed 14: ' . mysql_error());
while( $row = mysql_fetch_object($res) ){
    $remark 			= $row->remark;
	$isOpen_alipay		= $row->isOpen_alipay;
	$isOpen_wechat		= $row->isOpen_wechat;
	$isOpen_financial 	= $row->isOpen_financial;
	$isOpen_bank		= $row->isOpen_bank;
	$isOpen_agreement	= $row->isOpen_agreement;
	$agreement_content	= $row->agreement_content;
}
//查看sys表是否存在数据------start
$sys_id = -1;
$pay_password = '';
$is_pw = 0;
$query = "SELECT id FROM system_user_t WHERE isvalid=true AND user_id=$user_id AND customer_id=$customer_id LIMIT 1";
$result= mysql_query($query) or die('Query failed 22: ' . mysql_error());
while( $row = mysql_fetch_object($result) ){
    $sys_id = $row->id;
}


$paypassword = '';
$query = "SELECT paypassword FROM user_paypassword WHERE isvalid=true AND user_id = $user_id LIMIT 1";
$result= mysql_query($query) or die('Query failed 37: ' . mysql_error());
while( $row = mysql_fetch_object($result) ){
    $paypassword = $row->paypassword;
}
if($paypassword == ''){
    $is_pw = 0;
}else{
    $is_pw = 1;
}

//提现规则
$start_time         = -1;   //提现开始时间
$end_time           = -1;   //提现结束时间
$week_time          = -1;   //提现星期几可以
$full_vpscore       =  0;   //VP值最低限制
$isOpen_alipay      =  0;   //支付宝提现开关 0：可提现 1：不可提现
$$isOpen_wechat     =  0;   //微信零钱提现开关 0：可提现 1：不可提现
$isOpen_financial   =  0;   //财付通提现开关 0：可提现 1：不可提现
$isOpen_bank        =  0;   //银行卡提现开关 0：可提现 1：不可提现
$isOpen_agreement   =  0;   //提现协议开关 0：关 1：开
$sql = "SELECT start_time,end_time,week_time,full_vpscore,isOpen_alipay,isOpen_wechat,isOpen_financial,isOpen_bank,isOpen_agreement FROM moneybag_rule WHERE isvalid=true AND customer_id=".$customer_id." LIMIT 1";
$res = mysql_query($sql) or die('Query failed 45: ' . mysql_error());
while( $row = mysql_fetch_object($res) ){
    $start_time         = $row->start_time;
    $end_time           = $row->end_time;
    $week_time          = $row->week_time;
    $full_vpscore       = $row->full_vpscore;
    $isOpen_alipay      = $row->isOpen_alipay;
    $isOpen_wechat      = $row->isOpen_wechat;
    $isOpen_financial   = $row->isOpen_financial;
    $isOpen_bank        = $row->isOpen_bank;
    $isOpen_agreement   = $row->isOpen_agreement;

}
//个人VP总值查询
$my_vpscore = 0;
$sql2 = "SELECT my_vpscore FROM weixin_user_vp WHERE customer_id=".$customer_id." AND user_id=".$user_id." LIMIT 1";
$res2 = mysql_query($sql2) or die('Query failed 55: ' . mysql_error());
while( $row = mysql_fetch_object($res2) ){
    $my_vpscore = $row->my_vpscore;
}
//个人VP总值查询


//查询商家开启的提现方式

//


$data = getdate(time());
$today_m = $data['mday'];//今天是当月中的多少天
$today_w = $data['wday'];//今天是当周中的多少天

$is_allow = 0;
if($today_m >= $start_time && $today_m <= $end_time && $my_vpscore>=$full_vpscore){
    if($week_time==-1){
        $is_allow = 1;
    }elseif( $week_time == $today_w ){
        $is_allow = 1;
    }else{
        $is_allow = 0;
    } 
}
$acc_id = -1;
$query = "SELECT id FROM moneybag_account WHERE isvalid=true AND user_id = ".$user_id." LIMIT 1";
$result= mysql_query($query) or die('Query failed 65: ' . mysql_error());
while( $row = mysql_fetch_object($result) ){
    $acc_id = $row->id;
}

$query = "SELECT id,type,real_name,phone,bind_account,bind_band,bind_bang_address FROM moneybag_account WHERE isvalid=true AND user_id=".$user_id;


?>
<!DOCTYPE html>
<html>
<head>
    <title>零钱提现</title>
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
	<link type="text/css" rel="stylesheet" href="./css/list_css/r_style.css" />
    <link type="text/css" rel="stylesheet" href="./css/password.css" />
    <link type="text/css" rel="stylesheet" href="./css/tixian.css" />
   <!--  <link type="text/css" rel="stylesheet" href="./css/goods_css/dialog.css" /> -->
    <link type="text/css" rel="stylesheet" href="./css/self_dialog.css" />
    <style type="text/css">
        .Tisback{
            opacity: 0.5;
            background: black;
            position: fixed;
            width: 100%;
            height: 100%;
            z-index: 9;
            display:none;
        }
        .Tisborder{
            z-index: 10;
            position: fixed;
            margin-top: 30%;
            margin-left: 15%;
            width: 70%;
            background: #fff;
            border:1px solid #ccc;
            border-radius: 5px;
            padding: 20px;
            display:none;
        }
        .cont{
            font-size: 16px;
            line-height: 25px;
            position: absolute;
        }
        .ok{
            width: 100%;
            text-align: center;
            height: 40px;
            line-height: 40px;
            margin-top: 20px;
            color: #fff;
        }

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

    <div class="Tisback">  
    </div>

    <div class="Tisborder">
        <p class="cont"><?php echo $remark;?></p>
        <div class="ok" onclick="rule_ok();">
            <p>确定</p>
        </div>
    </div>
 <!--   <header data-am-widget="header" class="am-header am-header-default">
		<div class="am-header-left am-header-nav" onclick="goBack();">
			<img class="am-header-icon-custom" src="./images/center/nav_bar_back.png" style="vertical-align:middle;"/><span style="margin-left:5px;">返回</span>
		</div>
	    <h1 class="am-header-title" style="font-size:18px;">零钱提现</h1>
	</header>
	<div class="topDiv"></div> --><!-- 暂时隐藏头部导航栏 -->
	<div class="content_top">
		<div class="left" style="">
            <span style="vertical-align: middle;">提现到</span>
        </div>
        <div class="right" onclick="viewRecorder();">
            <img src="./<?php echo $images_skin?>/info_image/list-orange.png" alt="" style=""/>
            <span style="vertical-align: middle;">提现记录</span>
        </div>
    </div>
    
    
    <?php 
		$wc_sql = $query." AND type=1 LIMIT 1";
		$wc_res = mysql_query($wc_sql);
		$wx_id			= -1;
		$real_name      = "";
		$phone          = "";
		$bind_account   = "";
        while($row=mysql_fetch_object($wc_res)){
			$wx_id        = $row->id;
			$real_name = $row->real_name;
			$phone     = $row->phone;
			$phone     =substr($phone, 0, 3).'****'.substr($phone, 7);
			$bind_account = $row->bind_account;
        }
			
          
    ?>
    <!-- 微信零钱 start -->
	<?php if($isOpen_wechat){ ?>
    <div class="infoBox"  id="weixin_info" data-type="0" onclick="selectMethod('weixin');" >
         <div class="info_header border-bottom-color-green" style="background-color: #21ac45;">
            <div class="info_header_left">
               <img src="./images/info_image/weixin_white.png" alt="" style=""/>
               <span>微信零钱</span> 
            </div>
            <?php if($wx_id>0){?>
            <div class="info_header_right" onclick="viewInfo('weixin');">查看账号</div>
            <?php }?>
         </div>
         <div class="info_middle"></div>
         <div class="info_content" style="position: relative;">
            <div class="ele">
                <div class="left">真实姓名:</div>
                <div class="right"><?php echo $real_name;?> </div>
            </div>
            <div class="ele">
                <div class="left">手机号码:</div>
                <div class="right" id="tel"><?php echo $phone;?></div>
            </div>
            <div class="repair_btn" onclick="editInfo('weixin');">
                <img src="./images/info_image/xiugai.png" />
            </div>
            <div class="pop">
                <span class="menu_selected" id="arrow_1" style="color:#21ac45;">◆</span>
            </div>
         </div>
         <input type="hidden" id="weixin_click_status" value="-1" />
    </div>
	<?php } ?>
    <!-- 微信零钱 end -->
    
    <?php
          $wc_sql = $query." AND type=2 LIMIT 1";
          $wc_res = mysql_query($wc_sql);
		  $zfb_id = -1;
		  $real_name      = "";
		  $phone          = "";
		  $bind_account   = "";
          while($row=mysql_fetch_object($wc_res)){
                $zfb_id         = $row->id;
                $real_name  = $row->real_name;
                $phone      = $row->phone;
                $phone      =substr($phone, 0, 3).'****'.substr($phone, 7);
                $bind_account = $row->bind_account;
            }

    ?>
    <!-- 支付宝零钱 start -->
	<?php if($isOpen_alipay){ ?>
    <div class="infoBox"  id="zhifubao_info" data-type="1"  onclick="selectMethod('zhifubao');" >
         <div class="info_header border-bottom-color-blue" style="background-color: #2286bd;">
            <div class="info_header_left">
               <img src="./images/info_image/zhifubao-white.png"/>
               <span>支付宝</span> 
            </div>
            <?php if($zfb_id>0){?>
            <div class="info_header_right" onclick="viewInfo('zhifubao');">查看账号</div>
            <?php }?>
         </div>
         <div class="info_middle"></div>
         <div class="info_content" style="position: relative;">
            <div class="ele">
                <div class="left">真实姓名:</div>
                <div class="right"><?php echo $real_name;?></div>
            </div>
            <div class="ele">
                <div class="left">手机号码:</div>
                <div class="right" id="tel"><?php echo $phone;?></div>
            </div>
            <div class="ele">
                <div class="left">支付宝账户:</div>
                <div class="right"><?php echo $bind_account;?></div>
            </div>
            
            <div class="repair_btn" onclick="editInfo('zhifubao');">
                <img src="./images/info_image/xiugai.png" alt="" style=""/>
            </div>
            <div class="pop">
                <span class="menu_selected" id="arrow_1" style="color:#2286bd;">◆</span>
            </div>
        </div>
        <input type="hidden" id="zhifubao_click_status" value="1" />
    </div>
    <?php } ?>
    <!-- 支付宝零钱 end -->
    
    <?php
          $wc_sql = $query." AND type=3 LIMIT 1";
          $wc_res = mysql_query($wc_sql);
          $cft_id = -1;
          while($row=mysql_fetch_object($wc_res)){
                $cft_id = $row->id;
                $real_name = $row->real_name;
                $phone     = $row->phone;
                $phone     =substr($phone, 0, 3).'****'.substr($phone, 7);
                $bind_account = $row->bind_account;
            }
    ?>
    <!-- 财付通零钱 start -->
	<?php if($isOpen_financial){ ?>
    <div class="infoBox"  id="caifutong_info" data-type="2" onclick="selectMethod('caifutong');" >
         <div class="info_header border-bottom-color-yellow" style="background-color: #fb862f;">
            <div class="info_header_left">
               <img src="./images/info_image/caifutong_white.png"/>
               <span>财付通</span> 
            </div>
            <?php if($cft_id>0){?>
            <div class="info_header_right" onclick="viewInfo('caifutong');">查看账号</div>
            <?php }?>
         </div>
         <div class="info_middle"></div>
         <div class="info_content" style="position: relative;">
            <div class="ele">
                <div class="left">真实姓名:</div>
                <div class="right"><?php echo $real_name;?></div>
            </div>
            <div class="ele">
                <div class="left">手机号码:</div>
                <div class="right" id="tel"><?php echo $phone;?></div>
            </div>
            <div class="ele">
                <div class="left">财付通账户:</div>
                <div class="right"><?php echo $bind_account;?></div>
            </div>
            <div class="repair_btn" onclick="editInfo('caifutong');">
                <img src="./images/info_image/xiugai.png" />
            </div>
            <div class="pop">
                <span class="menu_selected" id="arrow_1" style="color:#fb862f;">◆</span>
            </div>
        </div>
        <input type="hidden" id="caifutong_click_status" value="1" />
    </div>
	<?php } ?>
    <!-- 财付通零钱 end -->

    <?php
          $wc_sql = $query." AND type=4 LIMIT 1";
          $wc_res = mysql_query($wc_sql);
          $yl_id = -1;
          while($row=mysql_fetch_object($wc_res)){
                $yl_id                 = $row->id;
                $real_name          = $row->real_name;
                $phone              = $row->phone;
                $phone              = substr($phone, 0, 3).'****'.substr($phone, 7);
                $bind_band          = $row->bind_band;
                $bind_account       = $row->bind_account;
                $bind_bang_address  = $row->bind_bang_address;
            }
                
    ?>
    <!-- 银行卡零钱 start -->
	<?php if($isOpen_bank){ ?>
    <div class="infoBox"  id="card_info" data-type="3" onclick="selectMethod('card');" >
         <div class="info_header border-bottom-color-red" style="background-color: #c2505d;">
            <div class="info_header_left">
               <img src="./images/info_image/card_white.png"/>
               <span>银行卡</span> 
            </div>
            <?php if($yl_id>0){?>
            <div class="info_header_right" onclick="viewInfo('card');">查看账号</div>
            <?php }?>
         </div>
         <div class="info_middle"></div>
         <div class="info_content" style="position: relative;">
            <div class="ele">
                <div class="left">真实姓名:</div>
                <div class="right"><?php echo $real_name;?></div>
            </div>
            <div class="ele">
                <div class="left">手机号码:</div>
                <div class="right" id="tel"><?php echo $phone;?></div>
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
                <div class="right"><?php echo '*** **** **** '.substr($bind_account,15,5);?></div>
            </div>
            <div class="repair_btn" onclick="editInfo('card');">
                <img src="./images/info_image/xiugai.png" />
            </div>
            <div class="pop">
                <span class="menu_selected" id="arrow_1" style="color:#c2505d;">◆</span>
            </div>
        </div>
        <input type="hidden" id="card_click_status" value="1" />
    </div>
	<?php } ?>
    <!-- 银行卡零钱 end -->
    <?php if($isOpen_agreement==1){?>
    <div class="content_bottom">
        <div style="width:100%;float:left;padding-left:20px;color: #707070;">
            <input class="ele" type="checkbox" id="rule" style="float:left;">         
            <span class="button buttonclick" style="vertical-align: middle;margin-left:3px;float:left;" onclick="viewRule();">提现规则</span>
            
        </div>
    </div>
    <?php }?>
    <div class="button buttonclick content_top">
        <div style="" >
            <img src="./images/info_image/xiugai.png" alt="" style="width: 20px;height: 15px;vertical-align:middle;"  onclick="viewAllMember();"/>
            <span style="vertical-align: middle;color: #707070;"  onclick="viewAllMember();">提现账号管理</span>
        </div>
    </div>
    <div class="btn" onclick="commit();"><span>确认</span></div>

   
        
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

    $(function(){

        var is_pw = "<?php echo $is_pw;?>";
        if( is_pw == 0 ){
            function callbackfunc2(){
                window.location.href="modify_password.php?customer_id=<?php echo $customer_id_en;?>";
            }
            showConfirmMsg("提示","您尚未设置支付密码，是否立即设置？","确定","取消",callbackfunc2);
            return false;
        }
        var acc_id = "<?php echo $acc_id;?>";
        if( acc_id < 0 ){
            function callbackfunc3(){
                window.location.href="money_tocash_admin.php?customer_id=<?php echo $customer_id_en;?>";
            }
            showConfirmMsg("提示","您尚未绑定任何提现方式，是否立即前往绑定？","确定","取消",callbackfunc3); 
            return false;
        }

    })

    function commit(){

        <?php if($is_allow==1){?>
        var i = $(".infoBox").is('.selected')?1:0;
        
        if(i==0){
            var title="提示";
            var content = "请选择提现方式";
            var cancel_btn = "确定";
            showAlertMsg(title,content,cancel_btn);
            return false;
        }
        <?php if( $isOpen_agreement==1 ){?>
        if($("#rule").is(':checked')==false){
            var title="提示";
            var content = "请阅读提现规则并勾选";
            var cancel_btn = "确定";
            showAlertMsg(title,content,cancel_btn);
            return false;
        }
        <?php }?>
            var choose  = $(".selected").data('type');
            
            if( choose == 0 ){
                var choose_type = "<?php echo $isOpen_wechat;?>";
                if( choose_type == 0 ){
                    showAlertMsg("温馨提示","商家暂不支持此类型提现，请重新选择","确定");
                    return false;
                }
                <?php if($isOpen_wechat ==1 ){?>
                    if( <?php echo $wx_id;?> < 0 ){
                        window.location.href="my_alipay.php?customer_id=<?php echo $customer_id_en;?>&t=1";
                    }else{
                        window.location.href="cash.php?customer_id=<?php echo $customer_id_en;?>&c_type=0";
                    }
                <?php }?>
            }else if( choose == 1 ){
                var choose_type = "<?php echo $isOpen_alipay;?>";
                if( choose_type == 0 ){
                    showAlertMsg("温馨提示","商家暂不支持此类型提现，请重新选择","确定");
                    return false;
                }
                <?php if($isOpen_alipay==1){?>
                    if( <?php echo $zfb_id;?> < 0 ){
                         window.location.href="my_alipay.php?customer_id=<?php echo $customer_id_en;?>&t=2";
                    }else{
                        window.location.href="cash.php?customer_id=<?php echo $customer_id_en;?>&c_type=1";
                    }
                <?php }?>
            }else if( choose == 2 ){
                var choose_type = "<?php echo $isOpen_financial;?>";
                if( choose_type == 0 ){
                    showAlertMsg("温馨提示","商家暂不支持此类型提现，请重新选择","确定");
                    return false;
                }
                <?php if($isOpen_financial==1){?>
                    if( <?php echo $cft_id;?> < 0 ){
                         window.location.href="my_alipay.php?customer_id=<?php echo $customer_id_en;?>&t=3";
                    }else{
                        window.location.href="cash.php?customer_id=<?php echo $customer_id_en;?>&c_type=2";
                    }
                <?php }?>
            }else if( choose == 3 ){
                var choose_type = "<?php echo $isOpen_bank;?>";
                if( choose_type == 0 ){
                    showAlertMsg("温馨提示","商家暂不支持此类型提现，请重新选择","确定");
                    return false;
                }
                <?php if($isOpen_bank == 1){?>
                    if( <?php echo $yl_id;?> < 0 ){
                         window.location.href="my_alipay.php?customer_id=<?php echo $customer_id_en;?>&t=4";
                    }else{
                        window.location.href="cash.php?customer_id=<?php echo $customer_id_en;?>&c_type=3";
                    }
                <?php }?>
            }



        <?php }else{?>
            var content = "尚未满足提现条件，请阅读提现规则";
            showAlertMsg("温馨提示",content,"确定");
            return false;

        <?php }?>
    }

    function viewRule(){
        $(".Tisback").show();
        $(".Tisborder").show();
    }
    function rule_ok(){
        $(".Tisback").hide();
        $(".Tisborder").hide();
    }

    function selectMethod(type)  //选择提现方式
    {
    	$(".infoBox").css("border","0px");
    	$("#"+type+"_info").css("border","3px solid #f4212b");
		$("#"+type+"_info").css("border-radius","7px");
        $(".infoBox").removeClass("selected");
        $("#"+type+"_info").addClass("selected");
    }
    
    function viewInfo(type){    //查看提现
        var status = $("#"+type+"_click_status").val();
        
        status = parseInt(status) * (-1);
        if(status==1)
            $("#"+type+"_info .info_content").slideUp('slow');
        else
            $("#"+type+"_info .info_content").slideDown('slow');
            
        $("#"+type+"_click_status").val(status);
    }   
    function viewAllMember(){
        window.location.href="money_tocash_admin.php?customer_id=<?php echo $customer_id_en;?>";
    }



    function editInfo(type){    //修改
       
       switch(type) {
           case 'weixin':
               window.location.href="my_alipay.php?t=1&customer_id=<?php echo $customer_id_en;?>";
            break;
            case 'zhifubao':
               window.location.href="my_alipay.php?t=2&customer_id=<?php echo $customer_id_en;?>";
            break;
            case 'caifutong':
               window.location.href="my_alipay.php?t=3&customer_id=<?php echo $customer_id_en;?>";
            break;
            case 'card':
               window.location.href="my_alipay.php?t=4&customer_id=<?php echo $customer_id_en;?>";
            break;
          
       }
    } 
    function viewRecorder(){ //jump to 提现记录

        window.location.href="my_cash_log.php?customer_id=<?php echo $customer_id_en;?>";
        
    }


</script>
<!--引入侧边栏 start-->
<?php  include_once('float.php');?>
<!--引入侧边栏 end-->
<?php require('../common/share.php'); ?>
</html>