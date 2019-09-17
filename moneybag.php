<?php
 header("Content-type: text/html; charset=utf-8"); 
require('../../../config.php');
require('../../../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../back_init.php');
$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
mysql_query("SET NAMES UTF8");
require('../../../proxy_info.php');
$head=0;//头部文件0商城资料，1分享设置,2购物设计


$isOpen_callback 	= 0; 	//是否开启零钱提现
$start_time 	 	= 1;	//每月提现开始日期
$end_time 		 	= 28;	//每月提现结束日期
$week_time 		 	= -1;	//提现可设置按每周几提现
$mini_callback	 	= 0;	//最低提现金额
$max_callback 	 	= 0;	//不可提现金额
$callback_currency 	= 0;	//提现反购物币 0：不返 大于0则反千分之几
$callback_fee 	 	= 0;	//提现手续费0：不收，大于0则收千分之几手续费
$full_vpscore 	 	= 0;	//提现vp值限制
$isOpen_alipay 	 	= 0;	//是否开启支付宝提现
$isOpen_wechat 	 	= 0;	//是否开启微信零钱提现
$isOpen_financial 	= 0;	//是否开启财付通提现
$isOpen_bank 	 	= 0;	//是否开启银行卡提现
$isOpen_agreement 	= 0;	//是否开启提现协议
$isOpen_massage 	= 0;
$agreement_content 	= '';	//提现协议

$query = "SELECT isOpen_callback,start_time,end_time,week_time,mini_callback,max_callback,callback_currency,callback_fee,full_vpscore,remark,isOpen_alipay,isOpen_wechat,isOpen_financial,isOpen_bank,isOpen_agreement,agreement_content,isOpen_massage FROM moneybag_rule where customer_id=".$customer_id." and isvalid=true LIMIT 1";
$result= mysql_query($query);
while($row=mysql_fetch_object($result)){
	$isOpen_callback 	= $row->isOpen_callback;
	$start_time 	 	= $row->start_time;
	$end_time 		 	= $row->end_time;
	$week_time 		 	= $row->week_time;
	$mini_callback	 	= $row->mini_callback;
	$max_callback 	 	= $row->max_callback;
	$callback_currency 	= $row->callback_currency;
	$callback_fee 	 	= $row->callback_fee;
	$full_vpscore 	 	= $row->full_vpscore;
	$remark			 	= $row->remark;
	$isOpen_alipay		= $row->isOpen_alipay;
	$isOpen_wechat		= $row->isOpen_wechat;
	$isOpen_financial 	= $row->isOpen_financial;
	$isOpen_bank		= $row->isOpen_bank;
	$isOpen_agreement	= $row->isOpen_agreement;
	$agreement_content	= $row->agreement_content;
	$isOpen_massage 	= $row->isOpen_massage;
}




	
?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<link rel="stylesheet" type="text/css" href="../../Common/css/Base/basicdesign/base_set.css">
<script type="text/javascript" src="../../../common/js/jquery-2.1.0.min.js"></script>
<script type="text/javascript" src="../../Common/js/Base/basicdesign/layer.js"></script>
<script type="text/javascript" src="../../../common/js_V6.0/jquery.ui.datepicker.js"></script>
<script type="text/javascript" src="../../../common/utility.js"></script>

<title>零钱设置</title>

<meta http-equiv="content-type" content="text/html;charset=UTF-8">
<style>
.distr_type_div i{margin-top:7px;}
.WSY_remind_dl02 .distr_type_div {height:35px;}
</style>
</head>
<body>
<div class="WSY_content">
	<div class="WSY_columnbox">
		<?php
			include("basic_head.php"); 
		?>		
	<form action="save_moneybag_rule.php?customer_id=<?php echo $customer_id_en; ?>" enctype="multipart/form-data" method="post" id="saveFrom" name="saveFrom">
		<input type=hidden name="shop_id" id="shop_id" value="<?php echo $shop_id; ?>" />
		<div class="WSY_remind_main">
			
			<dl class="WSY_remind_dl02"> 
				<dt>提现开关：</dt>
				<dd>
					<?php if($isOpen_callback==1){ ?>
						<ul style="background-color: rgb(255, 113, 112);margin-top:2px;">
							<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
							<li onclick="set_is_applymoney(0)" class="WSY_bot" style="left: 0px;"></li>
							<span onclick="set_is_applymoney(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
						</ul>																
					<?php }else{ ?>
						<ul style="background-color: rgb(203, 210, 216);margin-top:2px;">
							<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
							<li onclick="set_is_applymoney(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
							<span onclick="set_is_applymoney(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
						</ul>						
					<?php } ?>
					<input type="hidden" name="isOpen_callback" id="isOpen_callback" value="<?php echo $isOpen_callback; ?>" />	
				</dd>
			</dl>			
			<dl class="WSY_remind_dl02"> 
				<dt>支付宝提现开关：</dt>
				<dd>
					<?php if($isOpen_alipay==1){ ?>
						<ul style="background-color: rgb(255, 113, 112);margin-top:2px;">
							<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
							<li onclick="set_isOpen_alipay(0)" class="WSY_bot" style="left: 0px;"></li>
							<span onclick="set_isOpen_alipay(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
						</ul>																
					<?php }else{ ?>
						<ul style="background-color: rgb(203, 210, 216);margin-top:2px;">
							<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
							<li onclick="set_isOpen_alipay(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
							<span onclick="set_isOpen_alipay(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
						</ul>						
					<?php } ?>
					<input type="hidden" name="isOpen_alipay" id="isOpen_alipay" value="<?php echo $isOpen_alipay; ?>" />	
				</dd>
			</dl>		
			<dl class="WSY_remind_dl02"> 
				<dt>微信零钱提现开关：</dt>
				<dd>
					<?php if($isOpen_wechat==1){ ?>
						<ul style="background-color: rgb(255, 113, 112);margin-top:2px;">
							<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
							<li onclick="set_isOpen_wechat(0)" class="WSY_bot" style="left: 0px;"></li>
							<span onclick="set_isOpen_wechat(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
						</ul>																
					<?php }else{ ?>
						<ul style="background-color: rgb(203, 210, 216);margin-top:2px;">
							<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
							<li onclick="set_isOpen_wechat(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
							<span onclick="set_isOpen_wechat(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
						</ul>						
					<?php } ?>
					<input type="hidden" name="isOpen_wechat" id="isOpen_wechat" value="<?php echo $isOpen_wechat; ?>" />	
				</dd>
			</dl>			
			<dl class="WSY_remind_dl02"> 
				<dt>财付通提现开关：</dt>
				<dd>
					<?php if($isOpen_financial==1){ ?>
						<ul style="background-color: rgb(255, 113, 112);margin-top:2px;">
							<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
							<li onclick="set_isOpen_financial(0)" class="WSY_bot" style="left: 0px;"></li>
							<span onclick="set_isOpen_financial(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
						</ul>																
					<?php }else{ ?>
						<ul style="background-color: rgb(203, 210, 216);margin-top:2px;">
							<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
							<li onclick="set_isOpen_financial(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
							<span onclick="set_isOpen_financial(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
						</ul>						
					<?php } ?>
					<input type="hidden" name="isOpen_financial" id="isOpen_financial" value="<?php echo $isOpen_financial; ?>" />	
				</dd>
			</dl>		
			<dl class="WSY_remind_dl02"> 
				<dt>银行卡提现开关：</dt>
				<dd>
					<?php if($isOpen_bank==1){ ?>
						<ul style="background-color: rgb(255, 113, 112);margin-top:2px;">
							<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
							<li onclick="set_isOpen_bank(0)" class="WSY_bot" style="left: 0px;"></li>
							<span onclick="set_isOpen_bank(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
						</ul>																
					<?php }else{ ?>
						<ul style="background-color: rgb(203, 210, 216);margin-top:2px;">
							<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
							<li onclick="set_isOpen_bank(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
							<span onclick="set_isOpen_bank(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
						</ul>						
					<?php } ?>
					<input type="hidden" name="isOpen_bank" id="isOpen_bank" value="<?php echo $isOpen_bank; ?>" />	
				</dd>
			</dl>			
			<dl class="WSY_remind_dl02"> 
				<dt>提现规则开关：</dt>
				<dd>
					<?php if($isOpen_agreement==1){ ?>
						<ul style="background-color: rgb(255, 113, 112);margin-top:2px;">
							<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
							<li onclick="set_isOpen_agreement(0)" class="WSY_bot" style="left: 0px;"></li>
							<span onclick="set_isOpen_agreement(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
						</ul>																
					<?php }else{ ?>
						<ul style="background-color: rgb(203, 210, 216);margin-top:2px;">
							<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
							<li onclick="set_isOpen_agreement(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
							<span onclick="set_isOpen_agreement(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
						</ul>						
					<?php } ?>
					<input type="hidden" name="isOpen_agreement" id="isOpen_agreement" value="<?php echo $isOpen_agreement; ?>" />	
				</dd>
			</dl>

			<dl class="WSY_remind_dl02"> 
				<dt>支付密码短信重置：</dt>
				<dd>
					<?php if($isOpen_massage==1){ ?>
						<ul style="background-color: rgb(255, 113, 112);margin-top:2px;">
							<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
							<li onclick="isOpen_massage(0)" class="WSY_bot" style="left: 0px;"></li>
							<span onclick="isOpen_massage(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
						</ul>																
					<?php }else{ ?>
						<ul style="background-color: rgb(203, 210, 216);margin-top:2px;">
							<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
							<li onclick="isOpen_massage(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
							<span onclick="isOpen_massage(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
						</ul>						
					<?php } ?>
					<input type="hidden" name="isOpen_massage" id="isOpen_massage" value="<?php echo $isOpen_massage; ?>" />	
				</dd>
			</dl>

			<dl class="WSY_remind_dl02" id="distr_type_div_applymoney" <?php if($isOpen_callback==0) echo "style='display:none'"; ?>> 
				<dt>提现条件：</dt>
				<dd>
					
				<div class="distr_type_div" style="height:auto;">
				<i><span class="fleft">提现开始日期：</span><input class="distr_input"  type="text" name="start_time" id="start_time" value="<?php echo $start_time;?>" maxlength='2' 
						onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')" >
				</i>				
				<i><span class="fleft">提现结束日期：</span><input class="distr_input"  type="text" name="end_time" id="end_time" value="<?php echo $end_time;?>" maxlength='2' 
						onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')" >
				</i>
				<i>每周
				<select class="type" name="week_time" id="week_time">
						<option value="-1" <?php if(-1 == $week_time){?>selected<?php }?>>无</option>
						<option value="0"  <?php if( 0 == $week_time){?>selected<?php }?>>星期日</option>
						<option value="1"  <?php if( 1 == $week_time){?>selected<?php }?>>星期一</option>
						<option value="2"  <?php if( 2 == $week_time){?>selected<?php }?>>星期二</option>
						<option value="3"  <?php if( 3 == $week_time){?>selected<?php }?>>星期三</option>
						<option value="4"  <?php if( 4 == $week_time){?>selected<?php }?>>星期四</option> 
						<option value="5"  <?php if( 5 == $week_time){?>selected<?php }?>>星期五</option>
						<option value="6"  <?php if( 6 == $week_time){?>selected<?php }?>>星期六</option>
				</select>
				提现 
				</i>
				<br/>
				<i><span class="fleft">最低提现金额：</span><input class="distr_input"  type="text" value="<?php echo $mini_callback?>" id="mini_callback" name="mini_callback" maxlength='5' onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')">
				</i>
				<i><span class="fleft">不能提现金额：</span><input class="distr_input"  type="text" value="<?php echo $max_callback?>" id="max_callback" name="max_callback" maxlength='15' onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')">
				</i>
				<i><span class="fleft">消费累计</span><input class="distr_input" style="width:45px;height:20px;border-radius:2px;text-align:center;"  type="text" value="<?php echo $full_vpscore?>" id="full_vpscore" name="full_vpscore" maxlength='6' onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')">&nbsp;&nbsp;vp值
				</i>
				</div>		
				</dd>
			</dl>

			<dl  class="WSY_remind_dl02" id="is_fee_div">
				<dt>开启手续费：</dt>
				<dd>
					<dd>
					<?php if($callback_fee>0){ ?>
						<ul style="background-color: rgb(255, 113, 112);margin-top:2px;">
							<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
							<li onclick="set_rule_fee(0)" class="WSY_bot" style="left: 0px;"></li>
							<span onclick="set_rule_fee(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
						</ul>																
					<?php }else{ ?>
						<ul style="background-color: rgb(203, 210, 216);margin-top:2px;">
							<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
							<li onclick="set_rule_fee(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
							<span onclick="set_rule_fee(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
						</ul>						
					<?php } ?>
					<input type="hidden" name="is_fee" id="is_fee" value="<?php echo $callback_fee; ?>" />
				</dd>
				</dd>
			</dl>
			<dl class="WSY_remind_dl02" id="is_fee_input" name="is_fee_input" style="display:<?php if($callback_fee>0){ echo "block";}else{ echo "none";}?>" >
				<dt>手续费比例：</dt>
				<dd><input type="text"  placeHolder="收取提现金额的千分之xx作为手续费" id="is_fee_val" name="is_fee_val" value="<?php echo $callback_fee;?>"><span style="color:red;">（返佣购物币按此 "千分比" 计算，手续费和返送购物币最多只能选其一，两种不选则提现全额到账）</span></dd>
			</dl>

			<dl class="WSY_remind_dl02" id="is_curr_div">
				<dt>开启返送购物币：</dt>
				<dd>
					<dd>
					<?php if($callback_currency>0){ ?>
						<ul style="background-color: rgb(255, 113, 112);margin-top:2px;">
							<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
							<li onclick="set_rule_curr(0)" class="WSY_bot" style="left: 0px;"></li>
							<span onclick="set_rule_curr(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
						</ul>																
					<?php }else{ ?>
						<ul style="background-color: rgb(203, 210, 216);margin-top:2px;">
							<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
							<li onclick="set_rule_curr(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
							<span onclick="set_rule_curr(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
						</ul>						
					<?php } ?>
					<input type="hidden" name="is_currency" id="is_currency" value="<?php echo $callback_currency; ?>" /> 	
				</dd>
				</dd>
			</dl>

			<dl class="WSY_remind_dl02" id="is_curr_input" name="is_curr_val" style="display:<?php if($callback_currency>0){ echo "block";}else{ echo "none";}?>">
				<dt>返佣购物币比例：</dt>
				<dd><input type="text"  placeHolder="收取提现金额的千分之xx作为购物币返佣" id="is_curr_val" name="is_curr_val" value="<?php echo $callback_currency;?>"><span style="color:red;">（返佣购物币按此 "千分比" 计算，手续费和返送购物币最多只能选其一，两种不选则提现全额到账）</span></dd>
			</dl>

			<dl class="WSY_remind_dl02" id="">
				<dt class="editor edit1" id="edit1" style="background-color:white;">提现规则：</dt>
				<div class="text_box input remark" style="width: 40%;margin-left: 163px;">
                	<textarea id="editor1" name="remark"><?php echo $remark; ?></textarea>
                </div> 
			</dl>
			
		</div>
		
	</form>
	<div class="submit_div">
			<input type="button" class="WSY_button" value="提交" onclick="return saveData(this);" style="cursor:pointer;">
		</div>
	</div>
</div> 
<!--配置ckeditor和ckfinder-->
<script type="text/javascript" src="../../../../weixin/plat/Public/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="../../../../weixin/plat/Public/ckfinder/ckfinder.js"></script>
<!--编辑器多图片上传引入开始-->
<script type="text/javascript" src="../../../../weixin/plat/Public/js/jquery.dragsort-0.5.2.min.js"></script>
<script type="text/javascript" src="../../../../weixin/plat/Public/swfupload/swfupload/swfupload.js"></script>
<script type="text/javascript" src="../../../../weixin/plat/Public/swfupload/js/swfupload.queue.js"></script>
<script type="text/javascript" src="../../../../weixin/plat/Public/swfupload/js/fileprogress.js"></script>
<script type="text/javascript" src="../../../../weixin/plat/Public/swfupload/js/handlers.js"></script> 
<!--编辑器多图片上传引入结束-->
<script type="text/javascript" src="../../../common/js_V6.0/jquery.ui.datepicker.js"></script>
<script type="text/javascript" src="../../../common/js_V6.0/content.js"></script>
<script>
function saveData(){
	var isOpen_callback = $("#isOpen_callback").val();
	var start_time 		= $("#start_time").val();
	var end_time 		= $("#end_time").val();
	var week_time 		= $("#week_time").val();
	var mini_callback   = $("#mini_callback").val();
	var max_callback   	= $("#max_callback").val();
	var is_fee_input   	= $("#is_fee_input").val();
	var is_curr_input   = $("#is_curr_input").val();
	var full_vpscore   	= $("#full_vpscore").val();
	var menber 			= /^\d+(\.\d+)?$/;


	if( start_time>end_time ){
		alert("开始日期不能大于结束日期");
		return false;
	}
	if(start_time>31){
		alert("开始日期不能大于31号");
		return false;
	}
	if(week_time>31){
		alert("结束日期不能大于31号");
		return false;
	}
	if(mini_callback<0){
		alert("最低提现金额不得少于0");
		return false
	}
	if(!menber.test(mini_callback)){
		alert("请输入正确的最低提现金额");
		return false;
	}
	if(max_callback<0){
		alert("不可提现金额不得少于0");
		return false
	}
	if(!menber.test(max_callback)){
		alert("请输入正确的不可提现金额");
		return false;
	}
	if(full_vpscore<0){
		alert("最低vp值不得少于0");
		return false
	}
	if(!menber.test(full_vpscore)){
		alert("请输入正确的最低vp值");
		return false;
	}

	var fee_hidden 	= $("#is_fee").val();
	var is_currency = $("#is_currency").val();
	if(fee_hidden==1&&is_currency==1){
		alert("不能同时设置两种比例");
		return false;
	}



	document.getElementById("saveFrom").submit();	
	return true ;
}
function set_need_online(obj){debugger;
	$("#need_online").val(obj);
	if(obj==0){
		$("#distr_type_div").hide();
	}else{
		$("#distr_type_div").show();
	}
}
function set_advisory_telephone(obj){debugger;
	$("#advisory_flag").val(obj);
	if(obj==0){
		$("#advisory_telephone :input").val(0);
		$("#advisory_telephone").hide();
	}else{
		$("#advisory_telephone :input").val("");
		$("#advisory_telephone").show();
	}
}
function set_is_applymoney(obj){
	$("#isOpen_callback").val(obj);
	if(obj==0){
		document.getElementById("distr_type_div_applymoney").style.display="none";
	}else{
		document.getElementById("distr_type_div_applymoney").style.display="";
	}
}

function set_isOpen_alipay(obj){
	$("#isOpen_alipay").val(obj);
}
function set_isOpen_wechat(obj){
	$("#isOpen_wechat").val(obj);
}
function set_isOpen_financial (obj){
	$("#isOpen_financial").val(obj);
}
function set_isOpen_bank(obj){
	$("#isOpen_bank").val(obj);
}
function set_isOpen_agreement(obj){
	$("#isOpen_agreement").val(obj);
}
function isOpen_massage(obj){
	$("#isOpen_massage").val(obj);
}



function set_rule_fee(obj){
	$("#is_fee").val(obj);
	if(obj==0){
		$("#is_fee_input").hide();
		$("#is_fee_val").val(0);	
	}else{	
			$("#is_fee_input").show();
			$("#is_curr_input").hide();
			$("#is_curr_val").val(0);
			hide(2);
		
	}
}

function set_rule_curr(obj){
	$("#is_currency").val(obj);
	if(obj==0){
		$("#is_curr_input").hide();
		$("#is_curr_val").val(0);
		
	}else{		
			$("#is_curr_input").show();
			$("#is_fee_input").hide();
			$("#is_fee_val").val(0);
			hide(1);
			

	}
}

CKEDITOR.replace( 'editor1', //提现规则
{
extraAllowedContent: 'img iframe[*]',
filebrowserBrowseUrl : '../../../../weixin/plat/Public/ckfinder/ckfinder.html',
filebrowserImageBrowseUrl : '../../../../weixin/plat/Public/ckfinder/ckfinder.html?Type=Images',
filebrowserFlashBrowseUrl : '../../../../weixin/plat/Public/ckfinder/ckfinder.html?Type=Flash',
filebrowserUploadUrl : '../../../../weixin/plat/Public/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
filebrowserImageUploadUrl : '../../../../weixin/plat/Public/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images',
filebrowserFlashUploadUrl : '../../../../weixin/plat/Public/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash'
});



function hide(obj){//手续费、返购物币开关只能开一个动画
	if(obj==1){
		var action = "#is_fee_div .WSY_bot";
	}else{
		var action = "#is_curr_div .WSY_bot";
	}
		$(action).animate({left : '30px'});
		$(action).parent().find(".WSY_bot2").animate({left : '30px'});
		$(action).hide();
		$(action).parent().find(".WSY_bot2").show();
		$(action).parent().find("p").animate({margin : '0 0 0 13px'}, 500);
		
		$(action).parent().find("p").html('关');
		$(action).parent().css({backgroundColor : '#cbd2d8'});
		$(action).parent().find("p").css({color : '#7f8a97'});

}



</script>
</body>
</html>