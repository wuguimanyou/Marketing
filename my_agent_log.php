<?php

header("Content-type: text/html; charset=utf-8");     
require('../config.php');
require('../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
require('../common/utility_fun.php');
mysql_select_db(DB_NAME) or die('Could not select database');

//头文件----start
require('../common/common_from.php');
//头文件----end

$query2="select agent_inventory,agent_getmoney from promoters where  status=1 and isvalid=true and user_id=".$user_id;
$result2 = mysql_query($query2) or die('Query failed: ' . mysql_error());
$agent_getmoney = 0;
$agent_inventory = 0;
while ($row2 = mysql_fetch_object($result2)) {
  $agent_inventory = $row2->agent_inventory;//代理库存余额
  $agent_getmoney = $row2->agent_getmoney;//代理得到的金额
  break;
}


?>
<!DOCTYPE html>
<html>
<head>
    <title>代理进出账日志</title>
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
    
 
    <link rel="stylesheet" id="wp-pagenavi-css" href="./css/list_css/pagenavi-css.css" type="text/css" media="all">
    <link rel="stylesheet" id="twentytwelve-style-css" href="./css/list_css/style.css" type="text/css" media="all">

    <!-- <script type="text/javascript" src="./js/r_jquery.mobile-1.2.0.min.js"></script> -->
    <link type="text/css" rel="stylesheet" href="./css/list_css/r_style.css" />
    <link type="text/css" rel="stylesheet" href="./css/goods/dialog.css" />
    <link type="text/css" rel="stylesheet" href="./css/self_dialog.css" />
    <link type="text/css" rel="stylesheet" href="./css/css_orange.css" />

    
<style>  
   .pinterest_title{ overflow: hidden;height: 36px;line-height: 19px;font-size:12px;color: #1c1f20;font-weight:bold;}
   .plus-tag-add{width:100%;min-width:320px;line-height:45px;padding-left:10px;border-bottom:1px solid #eee;}
   .list{padding:3px;margin-top:10px;height:107px;background-color:white;}
   .submenu{width:33%;height:42px;line-height:42px;float:left;text-align:center;}
   .area-line{height:25px;width:1px;float:left;margin-top: 10px;padding-top: 20px;border-left:1px solid #eee;}
   body .topDivSel{width:100%;padding:0px;background-color:white;}
   .info_left{width:60%;float:left;}
   .info_left .up{width:100%;float:left;text-align:left;line-height: 40px;color:black;}
   .info_left .down{width:100%;float:left;text-align:left;line-height: 5px;color:#ddd;}
   .info_right{width:40%;float:right;color:black;text-align:right;padding-right:10px;font-size:18px;}
   .my_info{width:100%;height:60px;line-height:60px;background-color:white;padding-left:10px;border-bottom:1px solid #eee;}
   .red{color:red;}
   .black{color:black;}
   .plus-tag-add{color:rgb(174, 174, 174);padding-left:0px;height: 43px;background-color: white;}
   .am-circle{float:left;margin-left:10%;width: 50px;height: 50px;}
   .info-one{width:100%;text-align: center;padding-top:30px;padding-bottom:10px;float:left;}
   .info-one span{float:left;font-size:16px;height:50px;line-height:50px;margin-left:20px;margin-top: 5px;}
   .wo_title{width:100%;text-align:center;font-size:15px;font-weight:200;height:135px;}
   .wo_title span{padding:3px 10px 3px 10px;}
   #editBanner{width: 20px;height: 15px;vertical-align:middle;}
   #allRecordDiv{width:100%;background-color:#f8f8f8;}
   .recordDiv{border-top:1px solid #eee;}
   .loading{width: 50px;
    display: block;
    margin: 50% auto;}
    .money{
      width: 100%;
      position: relative;
    }
    .money p{
          text-align: left;
          margin-left: 20%;
          line-height: 35px;
          font-size: 18px;
    }
    .tis{
          width: 100%;
          text-align: center;
          font-size: 20px;
          color: #999;
          display: none;
          margin-top: 15px;
    }
</style>


</head>
<!-- Loading Screen -->
<div id='loading' class='loadingPop'style="display: none;"><img src='./images/loading.gif' style="width:40px;"/><p class=""></p></div>

<body data-ctrl=true style="background:#fff;">
  <!-- <header data-am-widget="header" class="am-header am-header-default">
    <div class="am-header-left am-header-nav" onclick="goBack();">
      <img class="am-header-icon-custom" src="./images/center/nav_bar_back.png" style="vertical-align:middle;"/><span style="margin-left:5px;">返回</span>
    </div>
      <h1 class="am-header-title" style="font-size:18px;">我的购物币</h1>
  </header>
  <div class="topDiv"></div> -->  <!-- 暂时屏蔽头部 -->
    <div class="topDivSel">
      <div id="wodeInfoDiv" style="position:relative;">
              <div class="info-one">
                 <!--  <img class="am-circle" src="./images/info_image/gouwubi-orange.png" style="" alt=""> -->
                  <div class="money">
                      <p>库存余额：<?php echo $agent_inventory;?>元</p>
                      <p>进账金额：<?php echo $agent_getmoney;?>元</p>
                  </div>
                 
              </div>
              
              
              <div class="wo_title" style="">
              
              </div>
              
              <div style="position: absolute;top:10px;right:10px;"  >
              
              </div>
              
        </div>
      <div class="plus-tag-add" >
          <div id="all" class="submenu selected" onclick="viewRecord('agent_all');">全部</div>
          <div class="area-line" ></div>
          <div id="in" class="submenu"  onclick="viewRecord('agent_in');">收入</div>
          <div class="area-line" ></div>
          <div id="out" class="submenu"  onclick="viewRecord('agent_out');">支出</div>
          <input type="hidden" name="type_name" value="currency_all">
      </div>
      </div>
     <div style="height:178px;"></div> <!-- 占据DIV的高度 -->
      
    <div style="width:100%;height:8px;background:#f8f8f8;"></div>
    <!-- 所有零钱记录 start -->
    <div id="allRecordDiv" style="">
        <div class="recordDiv" id="recordContainer" style="">
        </div><!-- .entry-content -->
        <!-- <script src="./js/r_pinterest.js" type="text/javascript"></script> -->
        </div>
        <!-- 记录列表 end -->
        <p class="tis">---暂无更多记录---</p>
      </div>
      <img src="./images/loading.gif" alt="" class="loading">
    </div>

    <script type="text/javascript" src="./assets/js/jquery.min.js"></script>    
    <script type="text/javascript" src="./assets/js/amazeui.js"></script>
    <script type="text/javascript" src="./js/global.js"></script>
    <script type="text/javascript" src="./js/loading.js"></script>
    <script src="./js/jquery.ellipsis.js"></script>
    <script src="./js/jquery.ellipsis.unobtrusive.js"></script>
    <script src="./js/r_global_brain.js" type="text/javascript"></script>
  <!--引入侧边栏 start-->
<?php  include_once('float.php');?>
<!--引入侧边栏 end-->
</body>
<script type="text/javascript">
var pagenum = 1;
var data_null = 0;
searchRecord('agent_all',pagenum);

// function gotoZhuanZeng(){
//     window.location.href='currency_send.php?customer_id=<?php echo $customer_id_en;?>';
// }



function viewRecord(type){
  pagenum = 1;
  $('.my_info').remove();
    var cilck_type = type;
    $("#all").removeClass("selected");
    $("#in").removeClass("selected");
    $("#out").removeClass("selected");
    if(type=='agent_in'){
      $("#in").addClass("selected");
      type="agent_in";
    }else if(type=='agent_out'){
      $("#out").addClass("selected");
      type="agent_out";
    }else{
      $("#all").addClass("selected");
      type="agent_all";
    }
    $(".loading").show();
    $(".tis").hide();
    searchRecord(type,pagenum);

}

function gotoViewRerecordDetail(type,id){

    if(type==2){
        window.location.href='my_agent_out.php?customer_id=<?php echo $customer_id_en;?>&from=agent&id='+id;
    }else{
        window.location.href='my_agent_in.php?customer_id=<?php echo $customer_id_en;?>&from=agent&id='+id;
    }
}

//Jump to 转赠规则


  //获取零钱记录
function searchRecord(type,pagenum) {
window.Stype = type;
var user_id = <?php echo $user_id?>;
var from = "get_agent";
    $.ajax({
      url:'get_money_log.php',
      dataType: 'json',
      type: "post",
      data:{'from':from,'type':type,'user_id':user_id,'pagenum':pagenum},
      success:function(data){
          $(".loading").hide();
          var data = eval(data);
          var html = '';
          var content='';
          console.log(data);
          if(data==""){
            data_null = 1;
            $(".tis").show();
          }
          for ( var i in data ) {
                content += '<div class="my_info" onclick="gotoViewRerecordDetail('+data[i]["type"]+','+data[i]["id"]+');">';
                content += '    <div class="info_left" >';
                content += '    <div class="up" >'+data[i]["detail"]+'</div>';
                content += '    <div class="down" ><span>'+data[i]["createtime"]+'</span></div>';
                content += '</div>';
                if( data[i]["type"] == 2 )
                  content += '<div class="info_right" style=""><span class="red">+'+ data[i]["price"]+'</span></div>';
                
                else if( data[i]["type"] == 4)
                  content += '<div class="info_right" style=""><span class="black">'+data[i]["price"]+'</span></div>';
                  content += '</div>';
              
          }
             $("#allRecordDiv").append(content);   
      }
    });
   
} 
$(window).scroll(function () {//滑动至底部
if ($(window).scrollTop() == $(document).height() - $(window).height()) {
  if(data_null==1){
    return
  }

  pagenum++;
  //alert(pagenum);
  searchRecord(Stype,pagenum);
}
});


</script>

<!--引入侧边栏 start-->
<?php  include_once('float.php');?>
<!--引入侧边栏 end-->
</html>