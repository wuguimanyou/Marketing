<?php

header("Content-type: text/html; charset=utf-8"); 
require('../config.php');
require('../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
require('../common/utility.php');
require('select_skin.php');
//头文件----start
require('../common/common_from.php');
//头文件----end


?>
<!DOCTYPE html>
<html>
<head>
    <title>积分记录</title>
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
    
<style>  
   .list {  	margin: 10px 5px 0 3px;	overflow: hidden;}
   .pinterest_title{ overflow: hidden;height: 36px;line-height: 19px;font-size:12px;color: #1c1f20;font-weight:bold;}
   .plus-tag-add{width:100%;min-width:320px;height:44px;line-height:43px;padding-left:10px;border-bottom:1px solid #eee;}
   .list{padding:3px;margin-top:10px;height:107px;background-color:white;}
   .submenu{width:33.3%;height:45px;line-height:45px;float:left;text-align:center;}
   .submenu .line{width: 1px;height: 29px;margin-top: 8px;background-color: #eee;float: right;}
   .area-line{height:25px;width:1px;float:left;margin-top: 10px;padding-top: 20px;border-left:1px solid #cdcdcd;}
   .topDivSel{width:100%;height:50px;top:0px;padding-top:0px;background-color:white;}
   .info_left{width:60%;float:left;}
   .info_left img{width: 60px;height: 60px; float: left;margin-right: 2%;border-radius: 2px;}
   .info_left .up{width:100%;text-align:left;line-height: 30px;color:black;text-overflow: ellipsis;white-space: nowrap;margin-top: 4px;font-size: 15px;overflow:hidden;}
   .up span{color:#bebebe;font-size: 12px;}
   .info_right{width:40%;float:right;color:black;text-align:right;padding-right:10px;}
   .my_info{width:94%;line-height:60px;background-color:white;border-bottom:1px solid #eee;margin:0 auto;}
   .red{color:#f53b44;font-size: 18px;}
   .black{color:black;}
   .tisp{text-align: center;font-family: "微软雅黑";font-size: 26px;color:#ccc;margin-top: 3%;}
</style>


</head>
<!-- Loading Screen -->

<body data-ctrl=true style="background:#fff;">
	<!--
	<header data-am-widget="header" class="am-header am-header-default">
		<div class="am-header-left am-header-nav" onclick="goBack();">
			<img class="am-header-icon-custom" src="./images/center/nav_bar_back.png" style="vertical-align:middle;"/><span style="margin-left:5px;">返回</span>
		</div>
	    <h1 class="am-header-title" style="font-size:18px;">积分明细</h1>
	</header>
	-->
	<div class="topDivPanel" style="height: 50px;">
	    <div class="topDivSel">
		    <div class="plus-tag-add" style="color:rgb(174, 174, 174);padding-left:0px;">
				<div id="all" class="submenu selected" style="" onclick="viewRecord('score_all');">全部<div class="line"></div></div>
				<div id="in" class="submenu"  onclick="viewRecord('score_in');">收入<div class="line"></div></div>
				<div id="out" class="submenu"  onclick="viewRecord('score_out');">支出</div>
                <!-- div id="bianji" class="submenu"  onclick="viewRecord('bianji');">编辑<div class="area-line" ></div></div> -->
			</div>
	    </div>
    </div>
    <!-- 所有零钱记录 start -->
    <div id="allRecordDiv" style="width:100%;padding-top:50px;">
        <!-- <div class="recordDiv" id="recordContainer">
    		<!-- 记录列表 start -->
		    	<!-- <div class="entry-content">
					<ul class="pinterestUl col2" id="pinterestList" style="transition: height 1s; padding:4px;margin-top:22px;margin-bottom:-64px;background-color:#f8f8f8;" fixcols="1">
						<!-- 记录列表-->
					<!-- </ul>
					<!-- <p id="pinterestMore" style="display: block;">----- 向下滚动加载更多 -----</p>
					<p id="pinterestDone">----- 加载完毕 -----</p> -->
				<!-- </div><!-- .entry-content -->
				<!-- <script src="./js/r_pinterest.js" type="text/javascript"></script> -->
        
	    	<!-- </div>
	    	<!-- 记录列表 end -->
     
    	</div>
		<div id='loading' class='loadingPop'style="display: none;text-align:center"><img src='./images/loading.gif' style="width:40px;"/><p class=""></p></div>
       <div class="tisp" id="tisp" style="display:none">---已无更多记录---</div>

    <script type="text/javascript" src="./assets/js/jquery.min.js"></script>
    <script src="./js/r_global_brain.js" type="text/javascript"></script>
    <script type="text/javascript" src="./js/r_jquery.mobile-1.2.0.min.js"></script>    
    <script type="text/javascript" src="./assets/js/amazeui.js"></script>
    <script type="text/javascript" src="./js/global.js"></script>
    <script type="text/javascript" src="./js/loading.js"></script>
    <script src="./js/jquery.ellipsis.js"></script>
    <script src="./js/jquery.ellipsis.unobtrusive.js"></script>
    </body>		
<!--引入侧边栏 start-->
<?php  include_once('float.php');?>
<!--引入侧边栏 end-->
<script type="text/javascript">
var pagenum = 1;
var data_null = 0;
searchRecord('score_all',pagenum);
    var customer_id_en = '<?php echo $customer_id_en;?>';
    //Jump to 详细
    function gotoViewRerecordDetail(recordID,status){
		if(status==1){
			window.location.href="my_money_out.php?customer_id="+customer_id_en+"&id="+recordID+"&from=score";
    }
		else{
			window.location.href="my_money_in.php?customer_id="+customer_id_en+"&id="+recordID+"&from=score";
    }
			
    }
    
    function viewRecord(type){
		pagenum = 1;
  //   	cur_type = type;
		// $('body,html').animate({scrollTop:0},500);
		// var downFlag = false; // 是否加载全部
  //   	var pageNum = 1, isMore = true; // 总笔数
		$('.my_info').remove();
		$("#all").removeClass("selected");
		$("#in").removeClass("selected");
		$("#out").removeClass("selected");
        //$("#bianji").removeClass("selected");
		if(type=='score_in')
		{
			$("#in").addClass("selected");
			type="score_in";                 //积分收入
		}else if(type=='score_out'){
			$("#out").addClass("selected");
			type="score_out";                //积分出账
		}else if(type=='score_all'){
			$("#all").addClass("selected");
			type="score_all";                //所有积分
		}

		searchRecord(type,pagenum);
	}

//获取零钱记录
function searchRecord(type,pagenum) {
	window.Stype = type;
	var user_id = <?php echo $user_id?>;
	var from = 'score';
  //var tis = document.getElementById("tisp");
  //$("#tisp").show();
	$.ajax({
        url:'get_money_log.php',
        dataType: 'json',
        type: "post",
        data:{'from':from,'type':type,'user_id':user_id,'pagenum':pagenum},
        success:function(data){
            var data = eval(data);//格式化json
            var content = "";
            //console.log(data);
            if(data==''){
			   data_null = 1;
               $("#tisp").show();
            }else if(data !== ''){
                for(i in data){
                if(data[i]["score"]>0){
                    content += '<div class="my_info" onclick="gotoViewRerecordDetail('+data[i]["id"]+',2);">';
                }else{
                    content += '<div class="my_info" onclick="gotoViewRerecordDetail('+data[i]["id"]+',1);">';
                }
                content += '    <div class="info_left" >';
				if(data[i]["remark"]==""){
					content += '        <div class="up" >'+data[i]["remark"]+'</div>';
				}else{
					content += '        <div class="up" >'+data[i]["remark"]+'...</div>';
				}
                content += '        <div class="up" ><span>'+data[i]["createtime"]+'</span></div>';
                content += '    </div>';
                if(data[i]["type"]==0)
                content += '    <div class="info_right" style=""><span class="red">+'+data[i]["score"]+'</span></div>';
                else if(data[i]["type"]==1)
                content += '    <div class="info_right" style=""><span class="black">'+data[i]["score"]+'</span></div>';
               content += '<div style="clear:both;"></div>';
                content += '</div>';

            }
			$("#allRecordDiv").append(content);
          }
			   
           // tis.style.display="none";
           // $("#tisp").hide();
        }
    });

} 
$(window).scroll(function () {//滑动至底部
if ($(window).scrollTop() == $(document).height() - $(window).height()) {
	if(data_null==1){
		return
	}
	pagenum++;
	searchRecord(Stype,pagenum);
}
});   
</script>




    
</body>
<!--引入侧边栏 start-->
<?php  include_once('float.php');?>
<!--引入侧边栏 end-->
<?php require('../common/share.php'); ?>
</html>