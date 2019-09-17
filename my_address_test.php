<?php
header("Content-type: text/html; charset=utf-8");
require('../config.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
require('../common/common_from.php');

require('../common/jssdk.php');
$jssdk = new JSSDK($customer_id);
$signPackage = $jssdk->GetSignPackage();


?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
<script type="text/javascript" src="./assets/js/jquery.min.js"></script> 
<script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.1.0.js"></script>
<script language="javascript">
//加载新api
    wx.config({
        debug: false,
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
        wx.openAddress({
            success: function (res) {
                alert(1111);            
            },

        });     
    }   
    //这里触发获取微信地址并保存为默认地址--end
    
</script>
<style type="text/css">
    .box{
        width:100px;
        height: 50px;
        margin:50% auto;
        float: left;
        background: red;
        color:#fff;
    }
    a{
        font-size: 18px;
        color:#fff;
    }
</style>
</head>
<body>
    <div class="box" onclick="getaddr();">
        <a onclick="getaddr();">获取微信收货地址</a>
    </div>
</body>
<script type="text/javascript">



</script>
</html>