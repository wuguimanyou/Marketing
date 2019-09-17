<?php
header("Content-type: text/html; charset=utf-8"); 
require('../config.php');
require('../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
$user_id 		= -1;
$user_id			= $configutil->splash_new($_POST["user_id"]);
require('../common/common_from.php'); 

$pid 				= $configutil->splash_new($_POST["pid"]);
$shop_card_id 		= $configutil->splash_new($_POST["shop_card_id"]);
$pro_card_level_id 	= $configutil->splash_new($_POST["pro_card_level_id"]);

//查看粉丝商城会员卡等级开始
$sql="select level_id from weixin_card_members where isvalid=true and user_id=".$user_id." and card_id=".$shop_card_id;
//echo $sql;
$user_card_level_id = -1;
$result = mysql_query($sql) or die('Query failed会员等级1: ' . mysql_error());
while ($row = mysql_fetch_object($result)) {
	$user_card_level_id = $row->level_id;
}
$sql="select level from weixin_card_levels where isvalid=true and id=".$user_card_level_id;
$user_card_level = -1;
$result = mysql_query($sql) or die('Query failed:会员等级2 ' . mysql_error());
while ($row = mysql_fetch_object($result)) {
	$user_card_level = $row->level;
}
//查看粉丝商城会员卡等级结束

//商品购买需要的等级开始
$sql="select level from weixin_card_levels where isvalid=true and id=".$pro_card_level_id;
$card_level = -1;
$result = mysql_query($sql) or die('Query failed:会员等级3 ' . mysql_error());
while ($row = mysql_fetch_object($result)) {
	$card_level = $row->level;
}
/* echo "user_card_level==".$user_card_level;
echo "card_level==".$card_level; */
if($user_card_level < $card_level){
	echo 0;
}else{
	echo 1;	
}
?>