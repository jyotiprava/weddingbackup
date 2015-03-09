<?php
header('Content-type: application/javascript');
echo $jsonval;
if($row==1)
{

//mail

$to = $email;
$subject = "Activate your password at Wedding Penguin";

$message = "
<html>
<head>
<title>Activate password at Wedding Penguin</title>
</head>
<body>
<p>Dear $email,

Your temporary password - $pwd
<br/>
To reset your password please click this link:

http://192.168.2.99/weddingapp/changepassword.html?uniquekey=$key
<br/>
--
<br/>
Wedding Penguin team</p>
</body>
</html>
";


// Always set content-type when sending HTML email
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

// More headers
$headers .= 'From: <administrator@weddingpenguin.sg>' . "\r\n";

mail($to,$subject,$message,$headers);

?>
({
		
		
		"items":"1"
		
})
<?php
}
else
{
    ?>
 ({   
    "items":"0"
 })
    <?php
    
}
?>