<?php
header("Content-type: text/html; charset=utf-8"); 
require('../config.php');
require('../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]

$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
require('select_skin.php');
require('../common/common_from.php');


$advisory_telephone = '';
$advisory_flag      = 0;
$query = "SELECT advisory_telephone,advisory_flag FROM weixin_commonshops WHERE customer_id = $customer_id AND isvalid=true LIMIT 1";
$result= mysql_query($query)or die('Query failed 14: ' . mysql_error());
while( $row = mysql_fetch_object($result) ){
    $advisory_telephone = $row->advisory_telephone;
    $advisory_flag      = $row->advisory_flag;
}

//先判断用户是否有支付密码
$paypassword = '';
$is_pw = 0;
$query = "SELECT paypassword FROM user_paypassword WHERE isvalid=true AND user_id = $user_id LIMIT 1";
$result= mysql_query($query) or die('Query failed 37: ' . mysql_error());
while( $row = mysql_fetch_object($result) ){
    $paypassword = $row->paypassword;
}
if($paypassword == "" || $paypassword == NULL){
    $is_pw = 0;
}else{
    $is_pw = 1;
}

$query = "SELECT isOpen_massage FROM moneybag_rule WHERE isvalid=true AND customer_id = $customer_id LIMIT 1";
$result= mysql_query($query)or die('Query failed 34: ' . mysql_error());
while( $row = mysql_fetch_object($result) ){
    $isOpen_massage = $row->isOpen_massage;
}

$sys_id = -1;
$account = '';
$query = "SELECT id,account FROM system_user_t WHERE isvalid=true AND user_id=$user_id LIMIT 1";
$result= mysql_query($query)or die('Query failed 42: ' . mysql_error());
while( $row = mysql_fetch_object($result) ){
    $sys_id     = $row->id;
    $account    = $row->account;
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>密码管理 </title>
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
    <link rel="stylesheet" id="twentytwelve-style-css" href="./css/goods/dialog.css" type="text/css" media="all">
	
	<link type="text/css" rel="stylesheet" href="./css/list_css/r_style.css" />
    <link type="text/css" rel="stylesheet" href="./css/password.css" />
    
<style>  
   .selected{border-bottom: 5px solid black; color:black; }
   .list {margin: 10px 5px 0 3px;	overflow: hidden;}
   .area-line{height:25px;width:1px;float:left;margin-top: 10px;padding-top: 20px;border-left:1px solid #cdcdcd;}
   .topDivSel{width:100%;height:45px;top:50px;padding-top:0px;background-color:white;}
   .box{background-color: #f8f8f8;}
</style>


</head>
<!-- Loading Screen -->
<div id='loading' class='loadingPop'style="display: none;"><img src='./images/loading.gif' style="width:40px;"/><p class=""></p></div>

<body data-ctrl=true style="background:#f8f8f8;">
    
    <div class="box">
        <h1>
            <?php if( $is_pw == 0 ){ echo "支付密码";}elseif( $is_pw == 1 ){ echo "旧密码";}?>
        </h1>
        <label for="ipt">
            <ul id="cur">
                <li></li>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
            </ul>
        </label>
        <input type="tel" id="ipt" maxlength="6">
    </div>
    <?php if( $is_pw == 1 ){?>
    <div class="box">
        <h1>
            <?php if( $is_pw == 0 ){ echo "确认密码";}elseif( $is_pw == 1 ){ echo "新密码";}?>
        </h1>
        <label for="ipt1">
            <ul  id="new">
                <li></li>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
            </ul>
        </label>
        <input type="tel" id="ipt1" maxlength="6">
    </div>
    <?php }?>
        <div class="box">
        <h1>
            确认密码
        </h1>
        <label for="ipt2">
            <ul  id="new2">
                <li></li>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
            </ul>
        </label>
        <input type="tel" id="ipt2" maxlength="6">
        <a class="commtBtn" href="javascript:commit(0);"><?php if($is_pw == 1){ echo "确认修改";}else{ echo "确认";}?></a>
        <p style="color:#888;" onclick="Forgiven_pw();">忘记支付密码</p>
    </div>

    <script type="text/javascript" src="./assets/js/jquery.min.js"></script>    
    <script type="text/javascript" src="./assets/js/amazeui.js"></script>
    <script type="text/javascript" src="./js/global.js"></script>
    <script type="text/javascript" src="./js/loading.js"></script>
    <script src="./js/jquery.ellipsis.js"></script>
    <script src="./js/jquery.ellipsis.unobtrusive.js"></script>
    <script src="./js/r_global_brain.js" type="text/javascript"></script>
    <script type="text/javascript" src="./js/r_jquery.mobile-1.2.0.min.js"></script>
    <script src="./js/sliding.js"></script>
</body>		

<script type="text/javascript">
function Forgiven_pw(){
    // var advisory_telephone = '<?php echo $advisory_telephone;?>';
    <?php if($advisory_flag == 1){?>
    var advisory_telephone = '<?php echo $advisory_telephone;?>';
    <?php }else{?>
    var advisory_telephone = '';
    <?php }?>
    <?php if($isOpen_massage == 1){?>
        <?php if( $sys_id > 0 && $account != '' && $account != NULL ){?>
            window.location.href="forget_paypassword.php?customer_id=<?php echo $customer_id_en;?>";
        <?php }else{?>
            function callbackfunc(){
                window.location.href="bind_phone.php?customer_id=<?php echo $customer_id_en;?>";
            }
            showConfirmMsg("提示","您尚未绑定手机，请立即前往绑定","确定","取消",callbackfunc);
        <?php }?>
    <?php }else{?>
    showAlertMsg("提示","请联系商家 "+advisory_telephone,"确定");
    <?php }?>
}

    $('#ipt').on('input', function (e){
        var numLen = 6;
        var pw = $('#ipt').val();
        var list = $('#cur li');
        for(var i=0; i<numLen; i++){
            if(pw[i]){
                $(list[i]).text('·');
            }else{
                $(list[i]).text('');
            }
        }
    });
    $('#ipt1').on('input', function (e){
        var numLen = 6;
        var pw = $('#ipt1').val();
        var list = $('#new li');
        for(var i=0; i<numLen; i++){
            if(pw[i]){
                $(list[i]).text('·');
            }else{
                $(list[i]).text('');
            }
        }
    });

    $('#ipt2').on('input', function (e){
        var numLen = 6;
        var pw = $('#ipt2').val();
        var list = $('#new2 li');
        for(var i=0; i<numLen; i++){
            if(pw[i]){
                $(list[i]).text('·');
            }else{
                $(list[i]).text('');
            }
        }
    });

<?php if(!empty($paypassword)){?>
    function commit()
    {
        /*通用全局变量*/
            var href_Url    = '';
            var f_h         = '<?php echo $from?>';     //来源 ***暂不使用
            var pw_cur      = $('#ipt').val();          //第一个输入框密码
            var pw_new      = $('#ipt1').val();         //第二个输入框密码
            var pw_new2     = $('#ipt2').val();         //第三个输入框密码
            var pas_menber  = /^[0-9]*$/;
            var customer_id = "<?php echo $customer_id_en;?>";
            <?php if($is_pw == 1){?>
            var pw_lenght   = $('#ipt1').val().length;
            <?php }?>
        /*通用全局变量*/
        var SaveType = 'modify'; //来源于修改密码
        if( !pas_menber.test(pw_cur) || pw_lenght < 6 ){
            $('#ipt').val("");
            $('#ipt1').val("");
            $('#ipt2').val("");
            $('li').text("");
            showAlertMsg("提示","请输入6位数字密码","确定");
            return false;
        }
        if( !pas_menber.test(pw_new) || pw_lenght < 6 ){
            $('#ipt').val("");
            $('#ipt1').val("");
            $('#ipt2').val("");
            $('li').text("");
            showAlertMsg("提示","新密码只支持6位数字密码","确定");
            return false;
        }
        if(pw_new!=pw_new2){
            $('#ipt').val("");
            $('#ipt1').val("");
            $('#ipt2').val("");
            $('li').text("");
            showAlertMsg("提示","新密码两次输入不一致","确定");
            return false;
        }



        $.ajax({
            url         :   'save_pay_password.php',
            dataType    :   'json',
            type        :   "post",
            data        :{
                            'pw_cur':pw_cur,
                            'pw_new':pw_new,
                            'save_type':SaveType,
                            'customer_id':customer_id
                        },
            success:function(msg){
                var title = "提醒";
                var cancel_btn = "确定";
               if( msg.msg == 401 ){
                    var ok_btn      = "确定";
                    var cancel_btn  = "取消";
                    var content     = "密码修改成功！";
                    function callbackfunc(){
                        history.go(-1);
                    }
                    showConfirmMsg(title,content,ok_btn,cancel_btn,callbackfunc);
                    return false;
               }else if( msg.msg == 40001 ){
                    var content = "当前密码错误，请重新输入";
                    $('#ipt').val("");
                    $('#ipt1').val("");
                    $('#ipt2').val("");
                    $('li').text("");
                    showAlertMsg(title,content,cancel_btn);
                    return false;
               }else{
                    var content = "未知错误，请联系管理员";
                    $('#ipt').val("");
                    $('#ipt1').val("");
                    $('#ipt2').val("");
                    $('li').text("");
                    showAlertMsg(title,content,cancel_btn);
                    return false;
               }
            }
        });
    }
<?php }else{?>
    function commit(){
        /*通用全局变量*/
            var href_Url    = '';
            var f_h         = '<?php echo $from?>';     //来源 ***暂不使用
            var pw_cur      = $('#ipt').val();          //第一个输入框密码
            var pw_new      = $('#ipt2').val();         //第二个输入框密码
            var pas_menber  = /^[0-9]*$/;
            var customer_id = "<?php echo $customer_id_en;?>";
            
            var pw_lenght   = $('#ipt2').val().length;
            
        /*通用全局变量*/
        var SaveType = 'set_up'; //来源于创建新密码
        if(!pas_menber.test(pw_cur) || !pas_menber.test(pw_new) ){
            $('#ipt').val("");
            $('#ipt1').val("");
            $('#ipt2').val("");
            $('li').text("");
            showAlertMsg("提醒","密码只能是数字！","确定");
            return false;
        }

        if( pw_lenght < 6 ){
            $('#ipt').val("");
            $('#ipt1').val("");
            $('#ipt2').val("");
            $('li').text("");
            showAlertMsg("提醒","请输入6位数字密码","确定");
            return false;
        }

        if(pw_cur!==pw_new  || pw_lenght < 6 ){
            $('#ipt').val("");
            $('#ipt1').val("");
            $('#ipt2').val("");
            $('li').text("");
            showAlertMsg("提醒","两次密码输入不一致","确定");
            return false;
        }
        $.ajax({
            url        :    'save_pay_password.php',
            dataType   :    'json',
            type       :    'post',
            data       :{
                            'pw_cur':pw_cur,
                            'pw_new':pw_new,
                            'save_type':SaveType,
                            'customer_id':customer_id
            },
            success:function(data){
                function callbackfunc(){
                    history.go(-1);
                }

                if(data.msg==40001){
                    $('#ipt').val("");
                    $('#ipt1').val("");
                    $('#ipt2').val("");
                    $('li').text("");
                    showAlertMsg("提醒","用户已设置过支付密码","确定");
                    return false;
                }else if(data.msg==40002){
                    $('#ipt').val("");
                    $('#ipt1').val("");
                    $('#ipt2').val("");
                    $('li').text("");
                    showAlertMsg("提醒","两次密码不一致","确定");
                    return false;
                }else if(data.msg==40006){
                    $('#ipt').val("");
                    $('#ipt1').val("");
                    $('#ipt2').val("");
                    $('li').text("");
                    showAlertMsg("提醒","密码不能为空","确定");
                    return false;
                }else if(data.msg==401){
                    showConfirmMsg("提醒","支付密码设置成功！","确定","取消",callbackfunc);
                    return false;
                }
            }
        });
    }


<?php }?>
</script>
<!--引入侧边栏 start-->
<?php  include_once('float.php');?>
<!--引入侧边栏 end-->
<?php require('../common/share.php'); ?>
</html>