<?php
header("Content-type: text/html; charset=utf-8"); 
require('../config.php');
require('../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');

$pid = $configutil->splash_new($_POST["pid"]);

$storenum	  = 0;
$propertyids  = "";
$query="select storenum,propertyids from weixin_commonshop_products where isvalid=true and id=".$pid." and customer_id=".$customer_id;
//echo $query;
$result = mysql_query($query) or die('Query failed1: ' . mysql_error());
while ($row = mysql_fetch_object($result)) {
	$storenum 	  = $row->storenum;
	$propertyids  = $row->propertyids;
}
if( !empty( $propertyids ) ){
	$query="SELECT sum(storenum) as storenum FROM weixin_commonshop_product_prices where  product_id=".$pid;
	$result = mysql_query($query) or die('Query failed2: ' . mysql_error());
	while ($row = mysql_fetch_object($result)) {
		$storenum 	  = $row->storenum;
	}
}
if( $storenum <= 0 ){
	$query="update weixin_commonshop_products set isout=1 where isvalid=true and id=".$pid." and  customer_id=".$customer_id;
	//echo $query;
	mysql_query($query) or die('Query failed3: ' . mysql_error());
}
echo $storenum;
?>