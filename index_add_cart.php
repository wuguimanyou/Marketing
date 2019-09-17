<?php
/*
首页模板加入购物车
*/

header("Content-type: text/html; charset=utf-8");     
require('../config.php');
require('../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD); 
mysql_select_db(DB_NAME) or die('Could not select database');
mysql_query("SET NAMES UTF8");
require('../proxy_info.php');
require('../common/utility.php');



$pid = -1;//产品ID
$pro_card_level_id=-1; //购买产品需要会员卡的等级
$shop_card_id=-1;//购物卡ID
$op="";
if(!empty($_POST["pid"])){
	$pid = $configutil->splash_new($_POST["pid"]);
}
if(!empty($_POST["pro_card_level_id"])){
	$pro_card_level_id = $configutil->splash_new($_POST["pro_card_level_id"]);
}
if(!empty($_POST["shop_card_id"])){
	$shop_card_id = $configutil->splash_new($_POST["shop_card_id"]);
}

if(!empty($_POST["op"])){
	$op = $configutil->splash_new($_POST["op"]);
}

switch($op){
	
	//检查商品库存
	case "check_storenum":
	
		$storenum=0;
		$query="select storenum from weixin_commonshop_products where isvalid=true and id=".$pid."";
		//echo $query;
		$result = mysql_query($query) or die('Query failed1: ' . mysql_error());
		while ($row = mysql_fetch_object($result)) {
			$storenum 	  = $row->storenum; //库存
		}
		echo $storenum;
		
	break;
	
	//检查商品是否下架
	case "check_proout":
		
		$isout=false;	
		$query="select isout from weixin_commonshop_products where isvalid=true and id=".$pid." limit 0,1";
		//echo $query;
		$result = mysql_query($query) or die('Query failed2: ' . mysql_error());
		while ($row = mysql_fetch_object($result)) {
			$isout 	  = $row->isout; //库存
		}
		if($isout){//如果isout==true 即下架状态，输出0
			echo 0;
		}else{
			echo 1;
		}
		  
	break;
	
	//检查购买产品的限制
	case "check_cardLevel":
		
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

		if($user_card_level < $card_level){
			echo 0;
		}else{
			echo 1;	
		}
	
	break;
	
	default:
	break;
}

?>