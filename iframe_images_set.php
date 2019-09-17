<?php
header("Content-type: text/html; charset=utf-8"); 
require('../config.php');
require('../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../back_init.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');

require('../proxy_info.php');

mysql_query("SET NAMES UTF8");
$template_id =-1;
$position=1;
$p_pos=1;
if(!empty($_GET["template_id"])){
   $template_id = $configutil->splash_new($_GET["template_id"]);
}
if(!empty($_GET["position"])){
   $position = $configutil->splash_new($_GET["position"]);
}
if(!empty($_GET["p_pos"])){
   $p_pos = $configutil->splash_new($_GET["p_pos"]);
}
$new_baseurl = BaseURL."back_commonshop/";
?>

<!DOCTYPE html>
<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta charset="utf-8">
<title></title>
<link href="css/global.css" rel="stylesheet" type="text/css">
<link href="css/main.css" rel="stylesheet" type="text/css">
<link href="operamasks-ui.css" rel="stylesheet" type="text/css">
<link href="css/shop.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="js/jquery-1.7.2.min.js"></script>
<script>
  var imgids="";
</script>
</head>
<body style="font-size:12px;">
<div id="products" class="r_con_wrap">
<span class="input">
<span class="upload_file">
	<div>
	   <form action="save_defaultsetimg.php?customer_id=<?php echo $customer_id_en; ?>" id="frm_img" enctype="multipart/form-data" method="post">
			<div class="up_input">
			<input name="upfile" id="upfile" type="file"  width="120" height="30">
			<div id="PicUploadQueue" class="om-fileupload-queue"></div>
			</div>
			<input type=hidden name="customer_id" id="customer_id" value="<?php echo $customer_id_en; ?>" />
		</form>
		<div class="clear"></div>
	</div>
</span>


<div class="img" id="PicDetail">
  <?php 
    $query2 = "";
    $query2="select imgurl,id from weixin_commonshop_template_imgs where isvalid=true and template_id=".$template_id." and position='".$position."' limit 0,1";
	$result2 = mysql_query($query2) or die('Query failed: ' . mysql_error());
	while ($row2 = mysql_fetch_object($result2)) {
	   $imgurl=$row2->imgurl;
	   $i_id = $row2->id;
	
  ?>
    <script>
	   imgids = imgids + "<?php echo $i_id; ?> "+"_";
	</script>
    <div>
	     <a href="<?php echo $new_baseurl.$imgurl; ?>" target="_blank">
		 <img src="<?php echo $new_baseurl.$imgurl; ?>"></a>
		<input type="hidden" name="PicPath[]" value="<?php echo $imgurl; ?>">
	</div>
<?php } ?>


  
</div>
</span>
<div class="clear"></div>
</div>
<?php 

mysql_close($link);
?>
<script type="text/javascript">  
  
    function upload(){  
        var element = document.getElementById("upfile");  
        if("\v"=="v")  
        {  
            element.onpropertychange = uploadHandle;  
        }  
        else  
        {  
            element.addEventListener("change",uploadHandle,false);  
        }  
  
        function uploadHandle()  
        {  
            if(element.value)  
            {  
              
			  $("#frm_img").submit();
  
            }  
        }  
  
    } 
	
	if(imgids.length>0){
	   imgids= imgids.substring(0,imgids.length-1);
	   parent.setParentImgIds(imgids);
	}
	
	
  
</script>  
  
<script type="text/javascript">  
    upload();  
</script>  
</body>
</html>