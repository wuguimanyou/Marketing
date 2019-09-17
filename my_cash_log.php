<?php
header("Content-type: text/html; charset=utf-8");     
require('../config.php');
require('../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]

$link = mysql_connect(DB_HOST,DB_USER,DB_PWD); 
mysql_select_db(DB_NAME) or die('Could not select database');
require('select_skin.php');

//头文件----start
require('../common/common_from.php');
//头文件----end

$host = $_SERVER["HTTP_HOST"];
$new_baseurl = "http://".$host;




?>
<!DOCTYPE html>
<html>
<head>
    <title>消费明细</title>
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
    <!-- global css-->
    <link type="text/css" rel="stylesheet" href="./assets/css/amazeui.min.css" />
    <link type="text/css" rel="stylesheet" href="./css/extends_css/extends.css" />
    <link type="text/css" rel="stylesheet" href="./css/css_<?php echo $skin ?>.css" />

    <!-- <link type="text/css" rel="stylesheet" href="./css/goods_css/dialog.css" /> -->
    <link type="text/css" rel="stylesheet" href="./css/self_dialog.css" />
    <link type="text/css" rel="stylesheet" href="./css/personal.css" />
    <!-- global css-->
    
    
</head>
<style type="text/css">
    .time{
        width:100%;
        height:40px;
        text-align: center;
        font-size: 15px;
        color:#999;
        line-height: 40px;
        font-family: "微软雅黑";
    }
    .sharebg{
        display: none;
    }
    .tis{
        width: 100%;
        color: #999;
        text-align: center;
        margin-top: 20px;
    }
</style>
<body data-ctrl=true style="background:#f8f8f8;">
    <!-- Loading Screen -->
    <div id='loading' class='loadingPop'style="display: none;"><img src='./images/loading.gif'/><p>数据加载中</p></div>
    <!-- Loading Screen -->
    <div class="time" id="time" onclick="showtime();">日期</div>
    <div id="containerDiv" class="xiaofei">
    
        <!-- 所有消费记录 start -->
    </div>
    <div class="tis">---已无更多记录---</div>
    <!-- basic js -->
    <script type="text/javascript" src="./assets/js/jquery.min.js"></script>    
    <script type="text/javascript" src="./assets/js/amazeui.js"></script>
    <!-- basic js -->
    <!-- global js -->
    <script type="text/javascript" src="./js/global.js"></script>
    <script type="text/javascript" src="./js/loading.js"></script>
    <script src="./js/extends_js/global.js"></script>
    <script src="./js/extends_js/monthCtrl.js"></script>
    <!-- global js -->
    
    <script type="text/javascript">
    var user_id = '<?php echo $user_id; ?>';
    var customer_id = '<?php echo $customer_id; ?>';
    var customer_id_en = '<?php echo $customer_id_en;?>';
    var new_baseurl = '<?php echo $new_baseurl; ?>';
    $(function(){  
        showCalendar(1);
        var wh=$(window).height();
        var ww=$(window).width();
        if(ww>320){
        $('.am-calendar').css('left',ww/2);
        $('.am-calendar').css('marginLeft',-185);
        }
        get_all();//显示所有
    });
    
    //Jump to 详细
    function gotoViewRerecordDetail(index){
            window.location.href="my_cash_detail.php?customer_id="+customer_id_en+"&b="+index;
    }   
    function showtime(){
        $(".am-share").show();
    }
    
    function searchData(search_time){//查询数据
         var from = 'cash_log';
         if(search_time==''){
            $('#time').html('日期');
          }else{
            $('#time').html(search_time);
          }
         $.ajax({
            url:'get_money_log.php',
            dataType: 'json',
            type: "post",
            data:{
              'search_time':search_time,
              'user_id':user_id,
              'customer_id':customer_id,
              'from':from
            },
            success:function(res){
              console.log(res);
              var content = "";
              // content += '<div class="time" onclick="showtime();">';
              // if(search_time==""){
              //     content += '日期';
              //     $('.time').html('日期');
              // }else{
              //     content += search_time;
              // }
              
              content += '</div>';
              for(id in res){
                 var _len = res[id].length;
                 var i = 0;
                 content += '<div class="title-top" style="height: 45px;background-color:#f8f8f8;" >';
                 content += '   <div class="month">'+id+'月</div>';
                 content += '   <div class="sub">';
                 content += '       <span>月账单</span>';
                 content += '       <img class="tubiao" src="./images/vic/right_arrow.png" />';
                 content += '   </div>';
                 content += '</div>';
                 for(i=0;i<_len;i++){
                     if(res[id][i]['cash_type']==0){
                            var url_img = './images/info_image/weixin.png';
                            var paystyle = '微信零钱';
                     }else if(res[id][i]['cash_type']==1){
                            var url_img = './images/info_image/zhifubao.png';
                            var paystyle = '支付宝';
                     }else if(res[id][i]['cash_type']==2){
                            var url_img = './images/info_image/caifutong.png';  
                            var paystyle = '财付通';                         
                     }else if(res[id][i]['cash_type']==3){
                            var url_img = './images/info_image/card.png';  
                            var paystyle = '银行卡';       
                     }
                     if(res[id][i]['status'] == 0){
                            var status = '<span style="color:#c22439;font-weight:blod;font-size:14px;">未审核</span>';
                     }else if(res[id][i]['status'] == 1){
                            var status = '<span style="color:#06a7e1;font-weight:blod;font-size:14px;">已同意</span>';
                     }else if(res[id][i]['status'] == 2){
                            var status = '<span style="color:#68af27;font-weight:blod;font-size:14px;">驳回</span>';
                     }else if(res[id][i]['status'] == 3){
                            var status = '<span style="color:#c22439;font-weight:blod;font-size:14px;">提现取消</span>';
                     }
                     content += "<div class='contentDiv'  onclick='gotoViewRerecordDetail("+res[id][i]['batchcode']+");'>";
                     content += '   <div class="content-left">';
                     content += '       <img style="width:48px;height:46px;border-radius:0;margin-left:5px;" src="'+url_img+'"  />';
                     content += '   </div>';
                     content += '   <div class="content-right">';
                     content += '       <div class="content-right-up">';
                     content += '           <div class="content-right-up-left">'+res[id][i]['getmoney']+'</div>';
                     content += '           <div class="content-right-up-right">'+status+'</div>';
                     content += '       </div>';
                     content += '       <div class="content-right-down">'+paystyle;
                     content += '               <span style="color:#a1a1a1;font-size:13px;padding-left:5px;">'+res[id][i]['createtime']+'</span></div>    ';
                     content += '   </div>';
                     content += '</div>';
                 }
              }
            $('.xiaofei').html(content);
            $('.time').html(search_time);

            },
            error:function(res){
                alert("数据加载出错");
            }
        });
    }
    
    </script>
</body>
<!--引入侧边栏 start-->
<?php  include_once('float.php');?>
<!--引入侧边栏 end-->
<?php require('../common/share.php'); ?>
</html>