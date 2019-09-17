<?php
header("Content-type: text/html; charset=utf-8"); 
require('../config.php');
require('../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
require('../common/utility.php');
require('../common/common_from.php');
require('select_skin.php');


// $customer_id = 3243;
// $user_id     = 195203;

/*
a_type: -1 默认个人中心 1确认订单跳转过来 2线下商场跳转过来
*/
$a_type   = 0;
if(!empty($_GET["a_type"])){
    $a_type = $configutil->splash_new($_GET["a_type"]);
        
    if($a_type ==-1 ){                              //个人中心过来清空session
        $_SESSION['a_type_'.$user_id] = '';
    }else{
        $_SESSION['a_type_'.$user_id] = $a_type;    //订单过来则保存session以便其他页面使用
    }
}else{
    
    if($a_type ==-1 ){          //个人中心过来
        $_SESSION['a_type_'.$user_id] = '';
    }

    $a_type = $_SESSION['a_type_'.$user_id];
}
if($from_type == 1){
    //微信JSK
    require('../common/jssdk.php');
    $jssdk = new JSSDK($customer_id);
    $signPackage = $jssdk->GetSignPackage();
    //微信JSK End
}


?>
<!DOCTYPE html>
<html>
<head>
    <title>地址管理</title>
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
    <script type="text/javascript" src="./assets/js/jquery.min.js"></script> 
    <script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.1.0.js"></script> 
    <link type="text/css" rel="stylesheet" href="./assets/css/amazeui.min.css" />
    <link type="text/css" rel="stylesheet" href="./css/order_css/global.css" />
    <link type="text/css" rel="stylesheet" href="./css/vic.css" />
    <link type="text/css" rel="stylesheet" href="./css/css_<?php echo $skin ?>.css" />       
    <!--<script src="./js/extends_js/vconsole.min.js"></script>-->
   
    <style type="text/css">
        .addressDiv{width: 100%;overflow-y: auto;}
        #bottomBar{padding: 0px 25px 0px 25px;height: 70px;    z-index: 1000;}
        #bottomBar a.am-btn{width: 100%;height: 44px;}
        #bottomBar a img{width: 15px;height: 15px;vertical-align: middle;}
        #bottomBar a span{height: 18px;line-height: 18px;vertical-align: middle;margin-left: 5px;}
        .edit-address{width: 20%;display: inline-block;}
        .remove-address{width: 21%;display: inline-block;padding-left: 10px;}
        .opr-icon{width: 20px;height:20px;}
        .opr-text{font-size: 13px;color: #666;line-height: 17px;height:17px;vertical-align: middle;}
        .m-direct-get{color:#222;float:right;margin-right: 10px;text-decoration:underline;margin-bottom:10px;font-size: 13px;}
    </style>
<?php if($from_type == 1){?>
<script language="javascript">
//加载新api
    wx.config({
        debug: true,
        appId: '<?php echo $signPackage["appId"];?>',
        timestamp: <?php echo $signPackage["timestamp"];?>,
        nonceStr: '<?php echo $signPackage["nonceStr"];?>',
        signature: '<?php echo $signPackage["signature"];?>',
        jsApiList: [
        'openAddress'
        ]
    });
     
    wx.ready(function () {              
    }); 
    //这里触发获取微信地址并保存为默认地址--star
    function getaddr(){
        var arr = new Array();
        wx.openAddress({
            success: function (res) {
                var name=res.userName;                          //收货人姓名
                var telN=res.telNumber;                         //收货人电话
                var addp=res.provinceName;                      //国标收货地址第一级地址
                var addc=res.cityName;                          //国标收货地址第二级地址
                var adda=res.countryName;                       //国标收货地址第三级地址
                var addd=res.detailInfo;                        //详细收货地址
                var type="wxAdress";                            //声明ajax的处理类型
                $.ajax({
                    url         : 'save_address.php',
                    type        : "post",
                    data        :{
                                  'type':type,
                                  'name':name,
                                  'phone':telN,
                                  'address':addd,
                                  'location_p':addp,
                                  'location_c':addc,
                                  'location_a':adda
                                 },
                    success:function(msg){
                        window.location.href="my_address.php?customer_id=<?php echo $customer_id_en?>&a_type=<?php echo $a_type;?>";
                    },
                    error:function(){
                        showAlertMsg("提示","获取地址失败，请重新点击","确定");
                    }
                });              
            },
            cancel: function () {
                showAlertMsg("提示","您取消了地址获取","确定");
            }
        });     
    }   
    //这里触发获取微信地址并保存为默认地址--end
    
</script>
<?php  }?>
</head>

<body data-ctrl=true class="gray-back">
    <!-- 基本地址目录地区 - 开始 -->
    <div class="addressDiv" style="height:600px;" id="addressDiv">
    </div>
    <!-- 基本地址目录地区 - 终结 -->
    
    <!-- 下面的按钮地区 - 开始 -->
 
    <?php       
        if($from_type == 1){            //微信端才有获取微信地址
        
        ?>
    <div id="bottomBar" data-am-widget="navbar" class="am-navbar am-cf am-navbar-default  am-no-layout" style="position:fixed;margin-bottom:50px;">
        <a class="am-btn am-btn-warning" href="#"  onclick="getaddr();" style="background: #3eb94e;border-color: #3eb94e;">
          <img src="./images/vic/icon_white_plus.png" alt="">
          <span>获取微信收货地址</span>
        </a>
    </div>
    <?php }?>
    <div id="bottomBar" data-am-widget="navbar" class="am-navbar am-cf am-navbar-default  am-no-layout">
        <a class="am-btn am-btn-warning" href="#" onclick="createAddress();">
          <img src="./images/vic/icon_white_plus.png" alt="">
          <span>新建地址</span>
        </a>
    </div>
 <!--引入侧边栏 start-->
<?php  include_once('float.php');?>
<!--引入侧边栏 end-->   
    <!-- 下面的按钮地区 - 终结 -->

    <script type="text/javascript" src="./assets/js/amazeui.js"></script>
    <script type="text/javascript" src="./js/global.js"></script>
    <script type="text/javascript" src="./js/loading.js"></script>
    <script src="./js/jquery.ellipsis.js"></script>
    <script src="./js/jquery.ellipsis.unobtrusive.js"></script>
    
<script type="text/javascript">
    var winWidth = $(window).width();
    var winheight = $(window).height();

    $(function() { 
        searchData();       //获取收货地址的方法，一开始自动加载 
        adjustSize();
    });
    
    $(window).resize(function() {
        winWidth = $(window).width();
        winheight = $(window).height();     
        adjustSize();
    });
    
    function adjustSize(){
        $("#addressDiv").height(winheight-119);
    }
    

<?php if( $a_type < 0  ){ ?>    //个人中心来源

    function searchData() {
        
        var content = "";
        var op = 'check';
        $.ajax({     
            type: "post",     
            url:'get_my_address.php',
            data:{'op':op},
            success:function(data){               
                var   data = eval(data);
                for(var i in data){
                    var is_default = data[i]['is_default'];
                    content += '<div class="address-item' + ((1==is_default)?' default':'') + '" id="address' + i + '">';
                    content += '    <div class="default-span">默认</div>';
                    content += '    <div class="content">';
                    content += '        <div>';
                    content += '            <div class="userinfo" style="width:75%;">'+data[i]['name']+'</div><span class="phone">'+data[i]['phone']+'</span>';
                    content += '            <div class="address oh">'+data[i]['location_p']+data[i]['location_c']+data[i]['location_a']+data[i]['address']+'</div>';
                    content += '        </div>';
                    content += '        <div>';
                    content += '        </div>';
                    content += '    </div>';
                    content += '    <div class="operator" idx="' + i + '">';
                    content += '        <div  class="set-default" val="'+data[i]['id']+'">设为默认地址</div>';
                    content += '        <div class="edit-address">';
                    content += '            <img class="opr-icon" src="./images/vic/icon_gray_edit2.png" alt="">';
                    content += '            <span class="opr-text" val="'+data[i]['id']+'">编辑</span>';
                    content += '        </div>';
                    content += '        <div class="remove-address">';
                    content += '            <img class="opr-icon" src="./images/vic/icon_gray_delete2.png" alt="">';
                    content += '            <span class="opr-text" val="'+data[i]['id']+'" >删除</span>';
                    content += '        </div>';
                    content += '    </div>';
                    content += '</div>';   
                }
               
                $("#addressDiv").html(content);
                //设置默认收货地址---start
                $(".set-default").unbind("click").click(function(){
                        $(".address-item").removeClass("default");             //设置为默认地址是去掉其他的默认class
                        $(this).parent().parent().addClass("default");
                        var type  = 'savedefault';                  
                        var id = $(this).attr('val'); 
                        $.ajax({                        //此ajax是为了设置当前地址是否默认
                            url:'save_address.php',
                            dataType: 'json',
                            type: "post",
                            data:{'type':type,'id':id},
                            success:function(data){
                                if(data=='ok'){                              
                                }else{
                                    alert("设置出错！");
                                }
                            }
                        });
                });
                //设置默认收货地址---end
               
                //修改收货地址--star
                $(".edit-address").unbind("click").click(function(){
                    idx = $(this).parent().attr("idx");
                    var id = $(this).find('.opr-text').attr('val');
                    window.location.href = "edit_address.php?customer_id=<?php echo $customer_id_en;?>&type=edit&id="+id;
                });
                //修改收货地址--end

                //删除收货地址--star
                $(".remove-address").unbind("click").click(function(){                   
                    idx = $(this).parent(".operator").attr("idx");
                    del_id = $(this).find('.opr-text').attr('val');
                    showConfirmMsg("提示","你确定删除该地址吗？","确定","取消",callbackfunc);
                    return false;

                });
                //删除收货地址--end
            }
        }); 
    }   
    //新建地址
    function createAddress(){
         window.location.href = "edit_address.php?customer_id=<?php echo $customer_id_en;?>&type=insert";
    }
    //删除收货地址--star
     function callbackfunc(){
        var id      = del_id;
        var d_id    = idx;       
        var op      = 'delete';              
        $.ajax({
                url:'get_my_address.php',
                dataType: 'json',
                type: "post",
                data:{'op':op,'id':id},
                success:function(data){
                    if(data>0){
                         $("#addressDiv").children("#address" + d_id).remove();
                    }
                }
         });
    }
        //删除收货地址--end 
<?php }else{?>
   function searchData() {
        

        var content = "";
        var op      = 'check';
        $.ajax({     
            type: "post",     
            url:'get_my_address.php',
            data:{'op':op},
            success:function(data){               
                var   data = eval(data);
                for(var i in data){
                    var is_default = data[i]['is_default'];
                    content += '<div class="address-item' + ((1==is_default)?' default':'') + '" id="address' + i + '" >';
					content += '    <div class="default-span">默认</div>';
                    content += '    <div class="content" onclick="go_detail('+data[i]['id']+');">';
                    content += '        <div>';
                    content += '            <div class="userinfo oh" style="width:75%;">'+data[i]['name']+'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span>'+data[i]['phone']+'</span></div>';
                    content += '            <div class="address oh">'+data[i]['location_p']+data[i]['location_c']+data[i]['location_a']+data[i]['address']+'</div>';
                    content += '        </div>';
                    content += '        <div>';
                    content += '        </div>';
                    content += '    </div>';
                    content += '    <div class="operator" idx="' + i + '">';
                    content += '        <div class="set-default" val="'+data[i]['id']+'">设为默认地址</div>';
                    content += '        <div class="edit-address">';
                    content += '            <img class="opr-icon" src="./images/vic/icon_gray_edit2.png" alt="">';
                    content += '            <span class="opr-text" val="'+data[i]['id']+'">编辑</span>';
                    content += '        </div>';
                    content += '        <div class="remove-address">';
                    content += '            <img class="opr-icon" src="./images/vic/icon_gray_delete2.png" alt="">';
                    content += '            <span class="opr-text" val="'+data[i]['id']+'" >删除</span>';
                    content += '        </div>';
                    content += '    </div>';
                    content += '</div>';   
                }
               
                $("#addressDiv").html(content);
                //设置默认收货地址---start
                $(".set-default").unbind("click").click(function(){
                        $(".address-item").removeClass("default");             //设置为默认地址是去掉其他的默认class
                        $(this).parent().parent().addClass("default");
                        var type    = 'savedefault';                  
                        var id      = $(this).attr('val'); 
                        $.ajax({                        //此ajax是为了设置当前地址是否默认
                            url:'save_address.php',
                            dataType: 'json',
                            type: "post",
                            data:{'type':type,'id':id},
                            success:function(data){
                                if( data == 'ok' ){                              
                                }else{
                                    alert("设置出错！");
                                }
                            }
                        });
                });
                //设置默认收货地址---end
               
                //修改收货地址--star
                $(".edit-address").unbind("click").click(function(){
                    idx = $(this).parent().attr("idx");
                    var id = $(this).find('.opr-text').attr('val');
                    window.location.href = "edit_address.php?customer_id=<?php echo $customer_id_en;?>&type=edit&id="+id;
                });
                //修改收货地址--end

                //删除收货地址--star
                $(".remove-address").unbind("click").click(function(){
                    idx = $(this).parent(".operator").attr("idx");
                    del_id = $(this).find('.opr-text').attr('val');
                    showConfirmMsg("提示","你确定删除该地址吗？","确定","取消",callbackfunc);
                    return false;

                });
                //删除收货地址--end
            }
        }); 
    }
    //删除收货地址--star
     function callbackfunc(){
        var id      = del_id;
        var d_id    = idx;
        var op      = 'delete';

        $.ajax({
                url     :'get_my_address.php',
                dataType: 'json',
                type    : "post",
                data    :{'op':op,'id':id},
                success:function(data){
                    if( data > 0 ){
                        $("#addressDiv").children("#address" + d_id).remove();
                    }
                }
         });
    }

        <?php if($a_type == 1){?>           //跳转商城支付页面
            function go_detail(id){           
                window.location.href="order_form.php?customer_id=<?php echo $customer_id_en;?>&aid="+id;         
            }
        <?php }elseif($a_type == 2){?>      //跳转线下商城支付页面
            function go_detail(id){           
                window.location.href="../city_area/shop/confirm_order.php?customer_id=<?php echo $customer_id_en;?>&aid="+id;
            }
        <?php }?>
            function createAddress(){
                 window.location.href = "edit_address.php?customer_id=<?php echo $customer_id_en;?>&type=insert";
            }
<?php }?>
    

</script>
</body>
<?php require('../common/share.php'); ?>
</html>