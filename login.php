<?php
header("Content-type: text/html; charset=utf-8"); //svn
require('../config.php');
require('../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
require('../proxy_info.php');
require('select_skin.php');


/* 判断是否为微信浏览器 */

if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ) {
	$_SESSION["from_type_".$customer_id] = 1;  //从哪里进来 0:网页 1:微信 2:APP 3:支付宝	
}
if($_SESSION["from_type_".$customer_id]==1){
	$url="errors.php";
	header("Location:".$url."");
	exit();
}	
	

$new_baseurl = "http://".$http_host; //新商城图片显示
$shop_headimgurl="";//商城头像
$login_username="";//用户名
$login_password="";//密码有误
//查询商城LOGO
$logo_query="select i.imgurl from images i inner join weixin_baseinfos wb where i.isvalid=true and i.type=2 and i.foreign_id=wb.id and wb.customer_id=".$customer_id."";
$logo_result=mysql_query($logo_query) or die ("logo_query faild" .mysql_error());
while($row=mysql_fetch_object($logo_result)){
	$shop_headimgurl=$row->imgurl;
}
if($shop_headimgurl){
	$headimgurl=$new_baseurl.":8080/WeixinManager/logos/".$shop_headimgurl;
	
}
if($_COOKIE["login_headimgurl"]){
	$headimgurl=$_COOKIE["login_headimgurl"];
}
if($_COOKIE["login_username"]){
	$login_username=$_COOKIE["login_username"];
}
if($_COOKIE["login_password"]){
	$login_password=$_COOKIE["login_password"];
}
/*
echo $_SESSION["user_id_".$customer_id];
echo $_SESSION["myfromuser_".$customer_id];
echo $_SESSION["fromuser_".$customer_id];
*/
/*
$_SESSION["user_id_".$customer_id] = "";
$_SESSION["from_type_".$customer_id] = "";
$_SESSION["is_bind_".$customer_id] = 0;
*/

$from_type =  0;  
if(!empty($_SESSION["from_type_".$customer_id])){
	$from_type = $_SESSION["from_type_".$customer_id];  //从哪里进来 0:网页 1:微信 2:APP 3:支付宝	
}

//查询商城是否开启网页注册
$is_web_reg = 0;
$query = "select is_web_reg from weixin_commonshops where isvalid=true and customer_id=".$customer_id."";
$result=mysql_query($query)or die('Query failed'.mysql_error());
while($row=mysql_fetch_object($result)){
	$is_web_reg = $row->is_web_reg;
}

?>
<!DOCTYPE html>
<html>
<head>
	<title>登录界面</title>
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
	
	<style>
	*{margin: 0;padding: 0;}
	img{max-width: 100%;height:auto;border:none;}
	a{text-decoration: none;color: black;}
	body{font-family:"Microsoft YaHei",Arial,Helvetica,sans-serif;-webkit-text-size-adjust:none;}
	input[type='number'],input[type='reset'],input[type='submit'],input[type='button'],input[type='tel'],button,textarea{-webkit-appearance:none;border-radius: 0;border:1px solid #ddd;} /*去掉苹果的默认UI来渲染按钮*/
	.clear{clear: both; display: block; height: 0; overflow: hidden; visibility: hidden; width: 0;}
	ol,ul {list-style: none;}  
	h1,h2,h3,h4,h5,h6 {font-weight: normal;}
	body{background-color: #f8f8f8;}
	.tou{width: 100%;}
	.tou img{display:block;width: 22%;margin: 70px auto 30px;    border: 1px solid #fff;border-radius: 50%;}
	.logintext{font-size: 0;width: 76%;margin: 0 auto;}
	.logintext input{display: inline-block;margin-top: 10px;height: 27px; width: 60%;border:none;}
	.usernameDiv{height: 45px;border: 1px solid #ddd;border-bottom: none;border-radius: 3px 3px 0 0;background-image:url(./images/username.png);background-repeat: no-repeat;background-position: 15%;background-size: 23px 23px;text-indent: 30%;font-size: 18px;background-color: #fff;}
	.passwordDiv{height: 45px;border: 1px solid #ddd;border-radius: 0 0 3px 3px;background-image:url(./images/password.png);background-repeat: no-repeat;background-position: 15%;background-size: 20px 23px;text-indent: 30%;font-size: 19px;background-color: #fff;}
	.btulog{display:block;width: 76%;margin:30px auto 0;color: #fff;padding: 15px 0;border-radius: 3px;border:none;font-size: 21px;font-weight: 600;}
	p{color: #c9cacb;font-size: 14px;text-align: center;margin-top: 10px;}
	a{color: #c9cacb;}
	input:-webkit-autofill {-webkit-box-shadow: 0 0 0px 1000px white inset;}
	</style>
	<link type="text/css" rel="stylesheet" href="./css/css_<?php echo $skin ?>.css" />
	<link type = "text/css" rel = "stylesheet" href = "css/order_css/global.css" />
	
</head>
<body>
	<div class="tou">
		<img src="<?php echo $headimgurl;?>">
	</div>
	<form name="checkuser_login" id="checkuser_login" action="checkuser_login.php" method="post">
		<div class="logintext">
			<div class="usernameDiv">
				<input name="username" id="username" class="username" type="text" value="<?php echo $login_username;?>">
			</div>
			<div class="passwordDiv">
				<input name="password" id="password" class="password" type="password" value="<?php echo $login_password;?>">
			</div>
		</div>
		
		<button class="btulog" type="button" onclick="check();">登录</button>
	</form>
	<p><a href="forget_password.php?customer_id=<?php echo $customer_id_en;?>">忘记密码？</a>
	<?php if($from_type == 0 && $is_web_reg ==1 ){   //如果是网页端且商家开启网页注册 ?>
	/<a href="bind_phone.php?customer_id=<?php echo $customer_id_en;?>">立即注册</a>
	<?php }?>
	</p>
	<script type="text/javascript" src="./js/global.js"></script>
	<script type="text/javascript" src="assets/js/jquery.min.js"></script>
	<script type="text/javascript" src="../common/js/common.js"></script>
<script type="text/javascript">

	function check(){
		var username=$("#username").val();
		var password=$("#password").val();
		
		
		if(!chkPhoneNumber(username)){//检查电话号码
			showAlertMsg ("提示：","请输入正确的电话号码","知道了");
			return false;
		}

		if(password=="" || check_illegalchar(password)){//检查密码
			showAlertMsg ("提示：","您输入的是非法字符!请重新输入!","知道了");				
			return false;
		}
		
		$.ajax({
			type:"POST",
			url:"checkuser_login.php",
			dataType:"json",
			data:{op:"login",username:username,password:password},
			success:function(result){
				switch(result.status){
					
					case -1:
						showAlertMsg ("提示：","您输入的用户名/密码有误!请重新输入!","知道了");				
						return false;
						break;
					case -2:
						showAlertMsg ("提示：","您输入了非法数据!请重新输入!","知道了");				
						return false;
						break;
					case -3:
						showAlertMsg ("提示：","查询不到正确的商家","知道了");				
						return false;
						break;	
					case 1:
						window.location.href=result.url;
						break;
				}
				
			}
		
			
		});

	}
</script>	
	
</body>
</html>