<?php
header("Content-type: text/html; charset=utf-8"); 
require('../config.php');
require('../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
require('../common/utility.php');
require('../common/utility_fun.php');
//头文件----start
require('../common/common_from.php');
//头文件----end
require('../proxy_info.php');

mysql_query("SET NAMES UTF8");




if($user_id==-1){
   echo "<script>document.location='errors.php';</script>";
   return;
}

//龙虎版

$user_id_tgy="";
$user_id_tgy1="";   
$user_arr=array();
$user_lhb=array();
$same_id="";  
$k=0;

/*$query_tgy="SELECT p.user_id,u.id, u.weixin_name, u.weixin_headimgurl FROM promoters p LEFT JOIN weixin_users u ON p.user_id = u.id WHERE p.isvalid=true and p.customer_id=".$customer_id." and p.status=true";
$result_tgy=mysql_query($query_tgy) or die ('tgy faild' .mysql_error());
while($row=mysql_fetch_object($result_tgy)){
		
		$user_lhb_id=$row->id;
		//$user_lhb_name=$row->weixin_name;
		//$user_lhb_weixin_headimgurl=$row->weixin_headimgurl;
		$user_lhb[$k]["id"]=$row->id;
		$user_lhb[$k]["name"]=$row->weixin_name;
		$user_lhb[$k]["weixin_headimgurl"]=$row->weixin_headimgurl;
		
	$k++;
}
*/
/*for($i=0;$i<$k;){
	$query_tgy2="select qrs.reward_money from weixin_qrs qrs inner join weixin_qr_infos qr on qrs.qr_info_id=qr.id where qrs.isvalid=true and qrs.customer_id=".$customer_id." and qrs.status=true and qr.isvalid=true and qr.customer_id=".$customer_id." and qr.foreign_id=".$user_lhb[$i]['id']." limit 0,1";
	$result_tgy2=mysql_query($query_tgy2) or die ('tgy faild' .mysql_error());
	while($row=mysql_fetch_object($result_tgy2)){
		//$reward_money =$row->reward_money;
		$user_lhb[$i]["reward_money"]=$row->reward_money;
	}
	$i++;
}*/
$i=0;
$query_tgy2="select qrs.reward_money,qr.foreign_id from weixin_qrs qrs inner join weixin_qr_infos qr on qrs.qr_info_id=qr.id where qrs.isvalid=true and qrs.customer_id=".$customer_id." and qrs.status=true and qr.isvalid=true and qr.customer_id=".$customer_id." order by reward_money desc  limit 0,10";
$result_tgy2=mysql_query($query_tgy2) or die ('tgy faild' .mysql_error());
while($row=mysql_fetch_object($result_tgy2)){
	$user_lhb[$i]["reward_money"]=$row->reward_money;
	$user_lhb[$i]["id"]=$row->foreign_id;
	
	
	$query_tgy="SELECT weixin_name, weixin_headimgurl FROM weixin_users  WHERE isvalid=true and customer_id=".$customer_id." and id=".$user_lhb[$i]['id']." limit 0,1";
	
	$result_tgy=mysql_query($query_tgy) or die ('tgy faild' .mysql_error());
	while($row=mysql_fetch_object($result_tgy)){
			
		$user_lhb_id=$row->id;
		$user_lhb[$i]["name"]=$row->weixin_name;
		$user_lhb[$i]["weixin_headimgurl"]=$row->weixin_headimgurl;
	}
	
	$i++;
	
}



$reward_money = array();
foreach ($user_lhb as $user) {
	$reward_money[] = $user['reward_money'];
}
array_multisort($reward_money, SORT_DESC, $user_lhb);



  

$title="";
$query="select name from weixin_commonshops where customer_id=".$customer_id;
$result = mysql_query($query) or die('Query failed: ' . mysql_error());;
while ($row = mysql_fetch_object($result)) {
   $title = $row->name;
   break;
}

?>
<!DOCTYPE html>
<html>
<head>
	<title>龙虎榜</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta  charset="utf-8">
    <meta  name="viewport"  content="initial-scale=1.0,maximum-scale=1.0,user-scalable=no">
    <meta  name="apple-mobile-web-app-capable"  content="yes">
    <meta  name="apple-mobile-web-app-status-bar-style"  content="black">
    <meta  name="format-detection"  content="telephone=no">    
    <link  rel="stylesheet"  type="text/css"  href="../common_shop/common/beijing/css/foundation.css">
	<link  rel="stylesheet"  type="text/css"  href="../common_shop/common/beijing/css/common-v2.0.css">
	<!--新增个人中心龙虎榜-->
    <link rel="stylesheet" type="text/css" href="../common_shop/common/beijing/longhu/css/style.css">
    <style type="text/css">
   
    </style>
           
</head>

<body  class="body-gray"> 
	<!-- <div class="mall-top">
        <a href="javascript:window.history.go(-1);"><span class="mall-top-back tpl-mall-back">上一页</span></a>
    </div> -->
    <div  class="panel member-nav" style="width:100%;"><!--龙虎榜开始-->    
        <div class="header"><!--头部开始-->
            <div class="title">
                <div class="title_left">
                    <p class="title_left_img">
                        <img src="../common_shop/common/beijing/longhu/css/images/title_left.png">
                    </p>
                </div>
                <div class="title_right">
                    <span class="shop_name">
                        <?php echo $title; ?>
                    </span>
                    <span class="money_list">
                        总奖金排行
                    </span>
                </div>
                
            </div>
        </div><!--头部结束-->
        <div class="content">
            <div class="top_money"><!--前三名开始-->
            	<?php 
					$i=1;
                	foreach($user_lhb as $k=>$val){
					   //echo $val['id'].$val['name'].$val['weixin_headimgurl'].$val['reward_money']."<br>";
						if($i==1){
							
						?>
                        <div class="top1">
                            <div class="top_logo">
                                <img src="<?php echo $val['weixin_headimgurl']?>" class="icon-level-dis_lhb">
                            </div>
                            <span class="top_name">
                                <?php echo  $val['name']?>
                            </span>
                            <span class="all_money">
                                ￥<?php echo sprintf("%.2f", $val['reward_money']);?>
                            </span>
                        </div>
						<?php if($user_id==$val['id']){$same_id=1;}}?>
						<?php
                        if($i==2){
						?>
                        	<div class="top2">
                            <div class="top_logo">
                                <img src="<?php echo $val['weixin_headimgurl']?>" class="icon-level-dis_lhb">
                            </div>
                            <span class="top_name">
                                <?php echo $val['name']?>
                            </span>
                            <span class="all_money">
                                ￥<?php echo sprintf("%.2f", $val['reward_money']);?>
                            </span>
                        </div>
							<?php if($user_id==$val['id']){$same_id=1;}}?>
						<?php
                        if($i==3){
						?>
                        	<div class="top3">
                            <div class="top_logo">
                                <img src="<?php echo $val['weixin_headimgurl']?>" class="icon-level-dis_lhb">
                            </div>
                            <span class="top_name">
                                <?php echo $val['name']?>
                            </span>
                            <span class="all_money">
                                ￥<?php echo sprintf("%.2f", $val['reward_money']);?>
                            </span>
                        </div>
						<?php if($user_id==$val['id']){$same_id=1;}}?>

					<?php $i++; }?>

            </div><!--前三名结束-->
            <div class="bottom_money"><!--4-10名开始-->
                <ul>
                	<?php 
                    	$j=1;
                		foreach($user_lhb as $k=>$val){
					   //echo $val['id'].$val['name'].$val['weixin_headimgurl'].$val['reward_money']."<br>";
						if($j>3&&$j<=10){
					?>
                    <li class="<?php if($user_id==$val['id']){echo "own_libg";}?>">
                        <span class="rank"><?php echo $j?></span>
                        <div class="logo_small">
                            <img src="<?php echo $val['weixin_headimgurl']?>" class="icon-level-dis_lhb">
                        </div>
                        <span class="bottom_name">
                            <?php echo $val['name']?>
                        </span>
                        <span class="bottom_all_money">
                            ￥<?php echo sprintf("%.2f", $val['reward_money']);?>
                        </span>
                    </li>
                    
                    <?php if($user_id==$val['id']){$same_id=1;}}
					$j++;$i=$j-1;}?>
                   
                    <?php 
						if(empty($same_id)){
					?>
                    <li style="text-align:center;height:10px;">▪</li>
                    <li style="text-align:center;height:10px;">▪</li>
                    <li style="text-align:center;height:25px;">▪</li>		
					<?php	foreach($user_lhb as $k=>$val1){
							if($user_id==$val1['id']){							
					?>
                     <li class="own_libg">
                        <span class="rank"><?php echo $i?></span>
                        <div class="logo_small">
                            <img src="<?php echo $val1['weixin_headimgurl']?>" class="icon-level-dis_lhb">
                        </div>
                        <span class="bottom_name">
                            <?php echo $val1['name']?>
                        </span>
                        <span class="bottom_all_money">
                            ￥<?php echo sprintf("%.2f", $val1['reward_money']);?>
                        </span>
                    </li>
                    <?php $i++;}}}?>                    
            	</ul>
             </div>   
        </div><!--end content-->	
   </div><!--龙虎榜结束-->   
<!--引入侧边栏 start-->
<?php  include_once('float.php');?>
<!--引入侧边栏 end-->
 </body>    