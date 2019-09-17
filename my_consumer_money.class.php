<?php
header("Content-type: text/html; charset=utf-8"); //svn
require('../config.php');
if(!empty($_POST["customer_id"])){
	$customer_id = $configutil->splash_new($_POST["customer_id"]);
}
require('../customer_id_decrypt.php'); //µ¼ÈëÎÄ¼þ,»ñÈ¡customer_id_en[¼ÓÃÜµÄcustomer_id]ÒÔ¼°customer_id[ÒÑ½âÃÜ]
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
require('../proxy_info.php');
//require('../common/common_from.php');

$user_id     = -1;
$search_time = "";
$sear_being  = "";
$BeginDate   = "";
$end_time    = "";
if(!empty($_POST["user_id"])){
	$user_id = $configutil->splash_new($_POST["user_id"]);
}

$search_time = "";
if(!empty($_POST['search_time'])){//ËÑË÷µÄÊ±¼ä´Á
	$search_time = $configutil->splash_new($_POST['search_time']);						
	$sear_being  = strtotime($search_time);		//ËÑË÷ÔÂµÚÒ»ÌìÊ±¼ä´Á			
	$BeginDate	 = date('Y-m-01', strtotime($search_time));
	$end_time 	 = strtotime(date('Y-m-d', strtotime("$BeginDate +1 month ")));//µ±ÔÂ×îºóÒ»ÌìÊ±¼ä´Á
}
$arr = array();

// $query = "SELECT o.paystyle,o.createtime,o.totalprice,o.batchcode,p.default_imgurl FROM weixin_commonshop_orders o LEFT JOIN weixin_commonshop_products p ON o.pid=p.id WHERE o.isvalid=TRUE AND o.status=1 AND o.user_id=".$user_id." order by o.createtime desc";

// if(!empty($search_time)){
// 	$query = "SELECT o.paystyle,o.createtime,o.totalprice,o.batchcode,p.default_imgurl FROM weixin_commonshop_orders o LEFT JOIN weixin_commonshop_products p ON o.pid=p.id WHERE o.isvalid=TRUE AND o.status=1 AND o.user_id=".$user_id." and UNIX_TIMESTAMP(o.createtime)>=".$sear_being." and UNIX_TIMESTAMP(o.createtime)<=".$end_time." order by o.createtime desc";
// }

$query = "SELECT money,consume_way,pay_type,createtime,name,batchcode FROM consumption_log_t WHERE isvalid=true AND user_id=$user_id ORDER BY createtime desc";

if(!empty($search_time)){
	$query = "SELECT money,consume_way,pay_type,createtime,name,batchcode FROM consumption_log_t WHERE isvalid=true AND user_id=$user_id AND UNIX_TIMESTAMP(createtime)>=".$sear_being." and UNIX_TIMESTAMP(createtime)<=".$end_time." ORDER BY createtime desc";
}


//echo $query;


$result = mysql_query($query);
while($info = mysql_fetch_assoc($result)){
	
	// $query_name = "select pname from weixin_cityarea_orders where batchcode=".$info['batchcode'];
	// $result_name = mysql_query($query_name);
	// while ($name = mysql_fetch_object($result_name)) {
	//   $info['name'] = $name->pname;
	// }
	
	 $key_m = date('m', strtotime($info['createtime']));
	 $arr[$key_m][] = $info;
}

echo json_encode($arr);
?>