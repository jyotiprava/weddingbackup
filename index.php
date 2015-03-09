<?php
//ini_set('display_errors',1);
include_once("weddingnew/function.php");
$urlarr=explode(".",$_SERVER['HTTP_HOST']);
$url=$urlarr[0];
$qgettemp=$db->prepare('select `template`,`id`,`password` from `my_template` where `url`=?');
$qgettemp->execute(array($url));
$rgettemp=$qgettemp->fetch();
$rowcount=$qgettemp->rowCount();

if($rowcount>0)
{
 if($rgettemp['password']=='')
 {
 header("location:weddingnew/$rgettemp[template]/index.php?id=$rgettemp[id]");
 }
 else
 {
  
if(!isset($_SESSION['modal']))
{
  header("location:/#openModal");
  $_SESSION['modal']=0;
}

if(isset($_POST['submit']))
{
 $password=md5(htmlentities($_POST['password'],ENT_QUOTES));
 $qget=$db->prepare('select `id` from `my_template` where `password`=?');
$qget->execute(array($password));
$count=$qget->rowCount();
if($count>0)
{
 header("location:weddingnew/$rgettemp[template]/index.php?id=$rgettemp[id]");
}
else
{
 ?>
 <script>
  alert("Wrong password");
  window.location.href='cancel.php';
 </script>
<?php
}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>..::Wedding Penguin::..</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
<link href="weddingnew/style.css" type="text/css" rel="stylesheet"  media="screen"/>
</head>
<body>
 <div id="openModal" class="modalbg">
  <div class="dialog" style="width:250px; height: 200px;">
    <a href="#close" title="Close" class="close">x</a>
  	<h3>Password</h3>
	<form method="post" action="#" id="form_pwd" style="padding: 0%;">
		<table class="form" style="float: left;width: 300px;">
			
			<tr>
				<td>
					<input type="password" id="password" name="password" class="textbox" style="width: 200px;"/>
				</td>
			</tr>
		
                        <tr>
			    <td style=" border-bottom:none !important;">
				   <input type="submit" value="SUBMIT" name="submit" class="viewbutton2" style="float: left;margin-top: 0px;"/>
			    
				   <span style="float: left; margin-left:15px;color: #888; margin-top: 7px;">Or</span>
				  <a href="cancel.php" >
				   <input type="button" value="CANCEL" class="viewbutton2" style="background: #fff;float: left;font-family: 'latoregular';  color: #888; margin-top: 0px;"/></a>
			    </td>
                            
		        </tr>
		</table>
	</form>
 </div>
</div>
</body>
</html>
<?php
 }
}
else
{
header("location:weddingnew/");
}
?>