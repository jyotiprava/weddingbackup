<?php
header('Content-type: application/javascript');
echo $jsonval;
if($row==1)
{

//mail

$to = $email;
$subject = "Account details for $name at Wedding Penguin";

$message = "
<html>
<head>
<title>Account details for $name at Wedding Penguin</title>
</head>
<body>
<p>Dear $name,

Thank you for registering for Wedding Penguin. You may now log in by using your email and password that you created during registration.

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
$headers .= 'From: <noreply@weddingpenguin.com>' . "\r\n";

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