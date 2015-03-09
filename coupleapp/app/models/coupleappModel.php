<?php
class coupleappModel extends Model {

function device($deviceid)
{
    $rest=array();
    $que = "SELECT id from `device` where deviceid=?";
    $stmt1=$this->query($que,array($deviceid));
    $affected_rows = $stmt1->rowCount();
    $rest['count']=$affected_rows;
    $db=$this->getdbh();
    if($affected_rows==0)
    {
	$sql = "INSERT INTO `device` set deviceid=?";
	$stmt=$this->query($sql,array($deviceid));
	$lastinsertid=$db->lastInsertId();
	$rest['deviceid']=$lastinsertid;
	$rest['name']='';
	$rest['email']='';
    }
    else
    {
	$res=$stmt1->fetch(PDO::FETCH_ASSOC);
	$que = "SELECT `name`,`email` from `signup` where id=?";
	$stmt=$this->query($que,array($res['guest_id']));
	$res1=$stmt->fetch(PDO::FETCH_ASSOC);
	$rest['deviceid']=$res['id'];
	$rest['name']=$res1['name'];
	$rest['email']=$res1['email'];
    }
    
    return $rest;
}

function addcouple($name,$email,$password,$eventtype,$eventname,$weddingdate,$acstatus,$groomname,$groomemail,$groomphone,$bridename,$brideemail,$bridephone,$imageData)
{
     $uniquekey=md5($name.'--'.$email.'--'.$password.'--'.time()); 
$password=md5($password);
$que = "SELECT id from `signup` where email=?";
$stmt11=$this->query($que,array($email));
$affected_rows = $stmt11->rowCount();
//echo $affected_rows;
$db=$this->getdbh();
if($affected_rows==0)
{
    if($imageData!='')
    {
     $file='http://weddingpenguin.sg/weddingnew/upload/'.time().'.jpg';
   $file1='upload/'.time().'.jpg';
   $x=fopen($file,'w');
   fwrite($x,base64_decode($imageData));
   fclose($x);
    }
    else
    {
	$file1='';
    }
 
 
if($eventtype=='new')
{
$sql = "INSERT INTO `signup` set name=?,email=?,password=?,profilepic=?,wedding_event=?,uniquekey=?";
$stmt=$this->query($sql,array($name,$email,$password,$file1,$eventname,$uniquekey));
$insertId = $db->lastInsertId();
}
else
{
$sql = "INSERT INTO `signup` set name=?,email=?,password=?,profilepic=?,existing_event=?,uniquekey=?";
$stmt=$this->query($sql,array($name,$email,$password,$file1,$eventname,$uniquekey));
$insertId = $db->lastInsertId();  
}

$sql1 = "INSERT INTO `personal_detail` set user_id=?,wedding_date=?,role=?,groom_name=?,bride_name=?,g_email=?,b_email=?,g_phone=?,b_phone=?";
$stmt1=$this->query($sql1,array($insertId,$weddingdate,$acstatus,$groomname,$bridename,$groomemail,$brideemail,$groomphone,$bridephone));

//add gallery
$sql2 = "INSERT INTO `add_folder` set user_id=?,foldername=?,type=?";
$stmt2=$this->query($sql2,array($insertId,'Wedding Day','Friends'));
$inId = $db->lastInsertId();

$sql3 = "INSERT INTO `sub_folder` set user_id=?,fid=?,name=?";
$stmt3=$this->query($sql3,array($insertId,$inId,'Guest'));
//add gallery

//add relation
$sql4 = "INSERT INTO `all_relation` set user_id=?,add_val=?";
$stmt4=$this->query($sql4,array($insertId,'Unsorted'));

//add relation


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

Thank you for registering for Wedding Penguin. You may now log in by clicking this link:

http://weddingpenguin.sg/weddingnew/validate.php?uniquekey=$uniquekey

This will verify your account and log you into the site. In the future you will be able to log in to your Wedding penguin Couple app by using the username and password that you created during registration.
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



$affected_rowsval = $stmt->rowCount();
return $affected_rowsval;
}
else
{
  return $affected_rows;  
}


}


function login($username,$password)
{
    $pass=md5($password);
    $res=$this->query('select * from `signup` where `email`=? and `password`=? and `status`=?',array($username,$pass,1));
    $res1=$res->fetch(PDO::FETCH_ASSOC);
    $count=$res->rowCount();
 if($count>0)
 {
 $affected_rows['count'] = $count;
 $affected_rows['name']=$res1['name'];
 $affected_rows['userid']=$res1['id'];
 $affected_rows['type']='';
 }
 else
 {
    
    $res3=$this->query('SELECT * FROM personal_detail where g_email=? or b_email=? and password=?',array($username,$username,$pass));
    $res4=$res3->fetch(PDO::FETCH_ASSOC);
    
    $qwe=$this->query('SELECT `status` FROM signup where id=?',array($res4['user_id']));
    $res2=$qwe->fetch(PDO::FETCH_ASSOC);
    
   
    if($res2['status']==1)
    {
	$affected_rows['count'] = $res3->rowCount();
	if($res4['g_email']==$username)
	{
	    
	    $affected_rows['name']=$res4['groom_name'];
	    $affected_rows['type']='groom';
	}
	else
	{
	    
	    $affected_rows['name']=$res4['bride_name'];
	    $affected_rows['type']='bride';
	}
	$affected_rows['userid']=$res4['user_id'];
    }
    else
    {
	$affected_rows['count'] =0;
	$affected_rows['name']='';
	$affected_rows['userid']='';
	$affected_rows['type']='';
    }
 
 }
 return $affected_rows;
}

function forgotpwd($email)
{
   $random=rand(99,9999);
   $pwd=md5($random);
   
$que = "SELECT id,uniquekey from `signup` where email=?";
$stmt11=$this->query($que,array($email));
$affected_row = $stmt11->rowCount();

if($affected_row>0)
{
   $sql="UPDATE signup SET password=? WHERE email=?";
    $stmt=$this->updatequery($sql,array($pwd,$email));
    
$res1=$stmt11->fetch();
$affected_rows['count']=$affected_row;
$affected_rows['key']=$res1['uniquekey'];
$affected_rows['pwd']=$random;
}
else
{
$affected_rows['count']=$affected_row;
$affected_rows['key']='';
$affected_rows['pwd']='';
    
}

return $affected_rows;
}


function coupledetail($userid)
{
    $que="SELECT s.*,p.* FROM signup s,personal_detail p where s.id=p.user_id and s.id=?";
      $stmt=$this->query($que,array($userid));
      $res=$stmt->fetchAll(PDO::FETCH_ASSOC);
      return $res;
}

function editname($name,$userid,$type)
{
    if($type=='')
    {
   $sql="UPDATE signup SET name=? WHERE id=?";
$stmt=$this->updatequery($sql,array($name,$userid));
$affected_rows = $stmt->rowCount();
    }
    else
    {
	if($type=='groom')
	{
	        $sql="UPDATE personal_detail SET groom_name=? WHERE user_id=?";
		$stmt=$this->updatequery($sql,array($name,$userid));
		$affected_rows = $stmt->rowCount();
	}
	else
	{
		$sql="UPDATE personal_detail SET bride_name=? WHERE user_id=?";
		$stmt=$this->updatequery($sql,array($name,$userid));
		$affected_rows = $stmt->rowCount();
	}
	
    }
    
return $affected_rows;
   
}

function editpwd($cpwd,$userid,$password,$type)
{
   $pass=md5($cpwd);
   if($type=='')
   {
    $que = "SELECT email from `signup` where id=? and password=?";
    $stmt1=$this->query($que,array($userid,$pass));
    $count = $stmt1->rowCount();
    if($count==1)
    {
       $password=md5($password);
	$sql="UPDATE signup SET password=? WHERE id=?";
       $stmt=$this->updatequery($sql,array($password,$userid));
       $affected_rows = $stmt->rowCount();
       return $affected_rows;
    }
    else
    {
       return 5;
    }
   }
   else
   {
        $que = "SELECT id from `personal_detail` where user_id=? and password=?";
	$stmt1=$this->query($que,array($userid,$pass));
	$count = $stmt1->rowCount();
	if($count==1)
	{
	   $password=md5($password);
	    $sql="UPDATE personal_detail SET password=? WHERE user_id=?";
	   $stmt=$this->updatequery($sql,array($password,$userid));
	   $affected_rows = $stmt->rowCount();
	   return $affected_rows;
	}
	else
	{
	   return 5;
	}
   }
}

function sendqrcode($email_send,$userid)
{
    
function generateRandomString($length = 5) {
    return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
}

$eventkeycode=generateRandomString();

$que1 = "SELECT id from `signup` where event_keycode!=? and id=?";
   $stmt1=$this->query($que1,array('',$userid));
$count=$stmt1->rowCount();
if($count==0)
{
    $que2 = "SELECT id from `signup` where event_keycode=?";
   $stmt2=$this->query($que2,array($eventkeycode));

    $rowcount=$stmt2->rowCount();
    if($rowcount>0)
    {
            $eventkeycode=$eventkeycode.rand(9,9999);
            
    }
    
$sql="UPDATE `signup` SET `event_keycode`=? WHERE id=?";
      $stmt=$this->updatequery($sql,array($eventkeycode,$userid));
      $affected_rows = 1;
 
//mail

$to = $email_send;
$subject = "Qrcode of your wedding event";

$message = "
<html>
<head>
<title>Qrcode of your wedding event</title>
</head>
<body>
<p>Dear $email_send,

Your Qrcode is $eventkeycode for your wedding event.
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

}
else
{
    $affected_rows = 0;
}

    
      return $affected_rows;
}

function imageupload($userid,$image)
{
    if($image!='')
    {
     $file='/var/www/public_html/weddingpenguin.sg/weddingnew/upload/'.time().'.jpg';
   $file1='upload/'.time().'.jpg';
   $x=fopen($file,'w');
   fwrite($x,base64_decode($image));
   fclose($x);
   
   $sql="UPDATE `signup` SET `profilepic`=? WHERE id=?";
    $stmt=$this->updatequery($sql,array($file1,$userid));
    $affected_rows = $stmt->rowCount();
    return $affected_rows;
    }
    else
    {
	return 0;
    }
}

function coupleupdate($eventname,$wedding_date,$solemn_date,$custom,$custom_date,$weddingstyle,$firstedit,$insertId)
{
   
if($firstedit==0)
{
    $db=$this->getdbh();
    //add todo
if($custom=='Chinese (Bethrothal)')
{
  
$thisguide=htmlentities('<a href="#">this guide</a>',ENT_QUOTES);
$thislist=htmlentities('<a href="#">this list</a>',ENT_QUOTES);
$clickhere=htmlentities('<a href="#">click here</a>',ENT_QUOTES);
$upcoming=htmlentities('<a href="#">upcoming shows</a>',ENT_QUOTES);

$date=date("Y-m-d");

//1
$due_date1=date("Y-m-d", strtotime( "$wedding_date -12 month" ) );
$due_date2=date("Y-m-d", strtotime( "$date +2 week" ) );
if(strtotime($due_date1)<strtotime($date))
{
    $due_date=$due_date2;
}
else
{
    $due_date=$due_date1;
}

$sql = "INSERT INTO `lebel_setting` set label=?,color=?,user_id=?";
$stmt=$this->query($sql,array('Venue/Hotel','rgb(240, 158, 81)',$insertId));
$labelIdvh = $db->lastInsertId();

$sql1 = "INSERT INTO `todo_list` set user_id=?,title=?,label=?,due_date=?,sub_item=?";
$stmt1=$this->query($sql1,array($insertId,'Venue/Hotel - confirm location',$labelIdvh,$due_date,"Shortlist hotel. Refer to $thisguide|Pay deposit and sign contract, refer to $thisguide"));

//2
$due_date1=date("Y-m-d", strtotime( "$wedding_date -10 month" ) );
$due_date2=$date;
if(strtotime($due_date1)<strtotime($date))
{
    $due_date=$due_date2;
}
else
{
    $due_date=$due_date1;
}

$sql2 = "INSERT INTO `lebel_setting` set label=?,color=?,user_id=?";
$stmt2=$this->query($sql2,array('Vendors','rgb(242, 207, 80)',$insertId));
$labelIdv = $db->lastInsertId();

$sql3 = "INSERT INTO `todo_list` set user_id=?,title=?,label=?,due_date=?,sub_item=?";
$stmt3=$this->query($sql3,array($insertId,'Bridal Studio - confirm vendor',$labelIdv,$due_date,"attend wedding shows, view $upcoming|test out gown, refer to $thisguide"));

//3
$due_date1=date("Y-m-d", strtotime( "$wedding_date -9 month" ) );
$due_date2=date("Y-m-d", strtotime( "$date +2 week" ) );
if(strtotime($due_date1)<strtotime($date))
{
    $due_date=$due_date2;
}
else
{
    $due_date=$due_date1;
}

$sql4 = "INSERT INTO `todo_list` set user_id=?,title=?,label=?,due_date=?,sub_item=?";
$stmt4=$this->query($sql4,array($insertId,'Photographer/videographer - confirm vendor',$labelIdv,$due_date,"decide on montage and actual day requirement, refer to $thisguide|shortlist photographer/videographer, refer to $thisguide"));

//4
$due_date1=date("Y-m-d", strtotime( "$wedding_date -9 month" ) );
$due_date2=date("Y-m-d", strtotime( "$date +2 week" ) );
if(strtotime($due_date1)<strtotime($date))
{
    $due_date=$due_date2;
}
else
{
    $due_date=$due_date1;
}

$sql5 = "INSERT INTO `lebel_setting` set label=?,color=?,user_id=?";
$stmt5=$this->query($sql5,array('People','rgb(148, 119, 78)',$insertId));
$labelIdp= $db->lastInsertId();

$sql6 = "INSERT INTO `todo_list` set user_id=?,title=?,label=?,due_date=?,sub_item=?";
$stmt6=$this->query($sql6,array($insertId,'Send out RSVP invite',$labelIdp,$due_date,"create a e-brochure or a website, refer to $thisguide|"));

//5
$due_date1=date("Y-m-d", strtotime( "$wedding_date -8 month" ) );
$due_date2=$date;
if(strtotime($due_date1)<strtotime($date))
{
    $due_date=$due_date2;
}
else
{
    $due_date=$due_date1;
}

$sql7 = "INSERT INTO `todo_list` set user_id=?,title=?,label=?,due_date=?,sub_item=?";
$stmt7=$this->query($sql7,array($insertId,'Decide Wedding Brothers and Sisters',$labelIdp,$due_date," "));

//6
$due_date1=date("Y-m-d", strtotime( "$solemn_date -3 month" ) );
$due_date2=$date;
if(strtotime($due_date1)<strtotime($date))
{
    $due_date=$due_date2;
}
else
{
    $due_date=$due_date1;
}

$sql5 = "INSERT INTO `lebel_setting` set label=?,color=?,user_id=?";
$stmt5=$this->query($sql5,array('Legal','rgb(199, 222, 140)',$insertId));
$labelIdl= $db->lastInsertId();

$sql8 = "INSERT INTO `todo_list` set user_id=?,title=?,label=?,due_date=?,sub_item=?";
$stmt8=$this->query($sql8,array($insertId,'ROM - book date for Solemnization',$labelIdl,$due_date,"Log on to ROM website|"));

//7
$due_date1=$solemn_date;
$due_date2=$date;
if(strtotime($due_date1)<strtotime($date))
{
    $due_date=$due_date2;
}
else
{
    $due_date=$due_date1;
}

$sql9 = "INSERT INTO `todo_list` set user_id=?,title=?,label=?,due_date=?,sub_item=?";
$stmt9=$this->query($sql9,array($insertId,'Solemnization',$labelIdl,$due_date,"bring IC and witness IC, refer to $thisguide|"));

//8
$due_date1=date("Y-m-d", strtotime( "$wedding_date -9 month" ) );
$due_date2=date("Y-m-d", strtotime( "$date +2 week" ) );
if(strtotime($due_date1)<strtotime($date))
{
    $due_date=$due_date2;
}
else
{
    $due_date=$due_date1;
}

$sql10 = "INSERT INTO `todo_list` set user_id=?,title=?,label=?,due_date=?,sub_item=?";
$stmt10=$this->query($sql10,array($insertId,'Wedding Ring - buy or custom make',$labelIdv,$due_date,"decide on the wedding ring, refer to $thisguide|"));

//9
$due_date1=date("Y-m-d", strtotime( "$wedding_date -3 month" ) );
$due_date2=$date;
if(strtotime($due_date1)<strtotime($date))
{
    $due_date=$due_date2;
}
else
{
    $due_date=$due_date1;
}

$sql11 = "INSERT INTO `todo_list` set user_id=?,title=?,label=?,due_date=?,sub_item=?";
$stmt11=$this->query($sql11,array($insertId,'Justice of Peace - make appointment','',$due_date,"Choose justice of peace from $thislist|"));

//10
$due_date1=date("Y-m-d", strtotime( "$wedding_date -6 month" ) );
$due_date2=date("Y-m-d", strtotime( "$date +1 week" ) );
if(strtotime($due_date1)<strtotime($date))
{
    $due_date=$due_date2;
}
else
{
    $due_date=$due_date1;
}

$sql12 = "INSERT INTO `todo_list` set user_id=?,title=?,label=?,due_date=?,sub_item=?";
$stmt12=$this->query($sql12,array($insertId,'Make-up Artist - (usually w bridal package)',$labelIdv,$due_date,"Check with bridal studio on MUA package, refer to $thisguide|book trail make-up timing"));

//11
$due_date1=date("Y-m-d", strtotime( "$wedding_date -6 month" ) );
$due_date2=date("Y-m-d", strtotime( "$date +1 week" ) );
if(strtotime($due_date1)<strtotime($date))
{
    $due_date=$due_date2;
}
else
{
    $due_date=$due_date1;
}

$sql13 = "INSERT INTO `todo_list` set user_id=?,title=?,label=?,due_date=?,sub_item=?";
$stmt13=$this->query($sql13,array($insertId,'Live entertainment/Emcee confirm vendor',$labelIdv,$due_date,""));

//12
$due_date1=date("Y-m-d", strtotime( "$wedding_date -6 month" ) );
$due_date2=date("Y-m-d", strtotime( "$date +1 week" ) );
if(strtotime($due_date1)<strtotime($date))
{
    $due_date=$due_date2;
}
else
{
    $due_date=$due_date1;
}

$sql14 = "INSERT INTO `todo_list` set user_id=?,title=?,label=?,due_date=?,sub_item=?";
$stmt14=$this->query($sql14,array($insertId,'Florists and deco - (usually with hotel package)',$labelIdvh,$due_date,"confirm the flower and decoration, refer to $thisguide|"));


//13
$due_date1=date("Y-m-d", strtotime( "$wedding_date -3 month" ) );
$due_date2=date("Y-m-d", strtotime( "$date +2 week" ) );
if(strtotime($due_date1)<strtotime($date))
{
    $due_date=$due_date2;
}
else
{
    $due_date=$due_date1;
}

$sql15 = "INSERT INTO `todo_list` set user_id=?,title=?,label=?,due_date=?,sub_item=?";
$stmt15=$this->query($sql15,array($insertId,'Order beverages (if hotel package not enough)',$labelIdv,$due_date,"confirm the quanitity of beer, wine, liquour to $thisguide|"));

//14
$due_date1=date("Y-m-d", strtotime( "$wedding_date -3 month" ) );
$due_date2=date("Y-m-d", strtotime( "$date +2 week" ) );
if(strtotime($due_date1)<strtotime($date))
{
    $due_date=$due_date2;
}
else
{
    $due_date=$due_date1;
}

$sql16 = "INSERT INTO `todo_list` set user_id=?,title=?,label=?,due_date=?,sub_item=?";
$stmt16=$this->query($sql16,array($insertId,'Bridal Car - Rent or borrow',$labelIdv,$due_date,""));


//15
$due_date1=date("Y-m-d", strtotime( "$wedding_date -6 month" ) );
$due_date2=date("Y-m-d", strtotime( "$date +2 week" ) );
if(strtotime($due_date1)<strtotime($date))
{
    $due_date=$due_date2;
}
else
{
    $due_date=$due_date1;
}

$sql17 = "INSERT INTO `todo_list` set user_id=?,title=?,label=?,due_date=?,sub_item=?";
$stmt17=$this->query($sql17,array($insertId,'Wedding Favours - (usually with hotel package)',$labelIdvh,$due_date,"choose an approporiate favour, refer to $thisguide|"));

//16
$due_date1=date("Y-m-d", strtotime( "$wedding_date -2 month" ) );
$due_date2=date("Y-m-d", strtotime( "$date +2 week" ) );
if(strtotime($due_date1)<strtotime($date))
{
    $due_date=$due_date2;
}
else
{
    $due_date=$due_date1;
}

$sql18 = "INSERT INTO `todo_list` set user_id=?,title=?,label=?,due_date=?,sub_item=?";
$stmt18=$this->query($sql18,array($insertId,'Preparation for  Brothers and Sisters',$labelIdp,$due_date,"decide on attire for sisters and brothers, refer to $thisguide|decide on role, refer to $thisguide"));

//17
$due_date1=date("Y-m-d", strtotime( "$wedding_date -3 month" ) );
$due_date2=date("Y-m-d", strtotime( "$date +2 week" ) );
if(strtotime($due_date1)<strtotime($date))
{
    $due_date=$due_date2;
}
else
{
    $due_date=$due_date1;
}

$sql19 = "INSERT INTO `todo_list` set user_id=?,title=?,label=?,due_date=?,sub_item=?";
$stmt19=$this->query($sql19,array($insertId,'Send out formal invitation to guests',$labelIdp,$due_date,"send the official paper invite to guest|"));

//18
$due_date1=date("Y-m-d", strtotime( "$wedding_date -5 month" ) );
$due_date2=date("Y-m-d", strtotime( "$date +1 month" ) );
if(strtotime($due_date1)<strtotime($date))
{
    $due_date=$due_date2;
}
else
{
    $due_date=$due_date1;
}

$sql20 = "INSERT INTO `todo_list` set user_id=?,title=?,label=?,due_date=?,sub_item=?";
$stmt20=$this->query($sql20,array($insertId,'Pre-wedding photoshoot',$labelIdv,$due_date,"liaise with photographer and bridal studio|for pre-wedding guide, $clickhere"));

//19
$due_date1=date("Y-m-d", strtotime( "$wedding_date -3 month" ) );
$due_date2=date("Y-m-d", strtotime( "$date +1 month" ) );
if(strtotime($due_date1)<strtotime($date))
{
    $due_date=$due_date2;
}
else
{
    $due_date=$due_date1;
}

$sql21 = "INSERT INTO `todo_list` set user_id=?,title=?,label=?,due_date=?,sub_item=?";
$stmt21=$this->query($sql21,array($insertId,'Food tasting with hotel',$labelIdvh,$due_date,"decide on the menu with the sales manager|"));

//20
$due_date=$custom_date;

$sql22 = "INSERT INTO `lebel_setting` set label=?,color=?,user_id=?";
$stmt22=$this->query($sql22,array('Ceremonial','rgb(112, 166, 93)',$insertId));
$labelIdc= $db->lastInsertId();

$sql23 = "INSERT INTO `todo_list` set user_id=?,title=?,label=?,due_date=?,sub_item=?";
$stmt23=$this->query($sql23,array($insertId,'Bethrothal',$labelIdc,$due_date,"Prepare bethrothal item, refer to $thisguide|"));
//

//21
$due_date1=date("Y-m-d", strtotime( "$wedding_date -2 month" ) );
$due_date2=date("Y-m-d", strtotime( "$date +1 month" ) );
if(strtotime($due_date1)<strtotime($date))
{
    $due_date=$due_date2;
}
else
{
    $due_date=$due_date1;
}

$sql24 = "INSERT INTO `todo_list` set user_id=?,title=?,label=?,due_date=?,sub_item=?";
$stmt24=$this->query($sql24,array($insertId,'Attire for Fathers/Mothers and in-laws',$labelIdv,$due_date,"Choose father s (in law) jacket if included in the bridal studio|Decide/buy mothers (in law) gown"));

//22
$due_date=date("Y-m-d", strtotime( "$wedding_date +1 day" ) );

$sql25 = "INSERT INTO `todo_list` set user_id=?,title=?,label=?,due_date=?,sub_item=?";
$stmt25=$this->query($sql25,array($insertId,'Honey moon',$labelIdc,$due_date,"make honey moon plan|"));

//23
$due_date=date("Y-m-d", strtotime( "$wedding_date -2 week" ) );

$sql26 = "INSERT INTO `todo_list` set user_id=?,title=?,label=?,due_date=?,sub_item=?";
$stmt26=$this->query($sql26,array($insertId,'Wedding Day Preparation',$labelIdp,$due_date,"Prepare itinerary, refer to $thisguide|Prepare Speech"));

//24
$due_date=date("Y-m-d", strtotime( "$wedding_date -2 week" ) );

$sql27 = "INSERT INTO `todo_list` set user_id=?,title=?,label=?,due_date=?,sub_item=?";
$stmt27=$this->query($sql27,array($insertId,'Finalize Guest List',$labelIdp,$due_date,"Send final reminder|Finalize seating"));
//add todo


}

$sqln="UPDATE personal_detail SET `wedding_date`=?,`solemn_date`=?,`custom`=?,`custom_date`=?,`wedding_style`=? WHERE user_id=?";
      $stmtn=$this->updatequery($sqln,array($wedding_date,$solemn_date,$custom,$custom_date,$weddingstyle,$insertId));
}

$sqln2="UPDATE personal_detail SET fstedit=? WHERE user_id=?";
      $stmtn2=$this->updatequery($sqln2,array(1,$insertId));
      
    $sqln1="UPDATE signup SET wedding_event=?, WHERE id=?";
      $stmtn1=$this->updatequery($sqln1,array($eventname,$insertId));
      $affected_rows = $stmtn1->rowCount();

return $affected_rows;
}
     

     
function alltodo($userid)
{
    $rest=array();
   $que="SELECT l.*,t.`due_date`,t.`title`,t.`sub_item`,t.`label` as tlabel,t.`id` as tid,t.`is_fav` FROM lebel_setting l,todo_list t where t.label=l.id and t.user_id=? order by `due_date` asc";
      $stmt=$this->query($que,array($userid));
      $res=$stmt->fetchAll(PDO::FETCH_ASSOC);
      $i=0;
    
		    foreach($res as $row)
			{
			    if($row['is_fav']==1)
			       {
			       $image="images/s1.png";
			       }
			       else
			       {
			       $image="images/star1.png";
			       }
			       
                                $rest[$i]['favicon']=$image;
                                $rest[$i]['label']=$row['label'];
                                $rest[$i]['labelcolor']=$row['color'];
                                $rest[$i]['tid']=$row['tid'];
                                $rest[$i]['title']=$row['title'];
                                $rest[$i]['tododate']=$row['due_date'];
                                $rest[$i]['subtitle']=$row['sub_item'];
                        $i++;
                        }
                        
   return $rest;  
}

function getfav($tid)
{
    $sqln1="update todo_list set `is_fav`= (case when is_fav=1 then 0 else 1 end) where id=?";
      $stmtn1=$this->updatequery($sqln1,array($tid));
    
    $que="select `is_fav` from `todo_list` where id=?";
      $stmt=$this->query($que,array($tid));
    $res=$stmt->fetch();
    
    return $res['is_fav'];
}

function getsh($did,$userid)
{
    $res=0;
    $que="SELECT * from `myshortlist` where `user_id`=? and `dir_id`=?";
      $stmt=$this->query($que,array($userid,$did));
      $count=$stmt->rowCount();
      if($count==0)
      {
	$sql27 = "INSERT INTO `myshortlist` set user_id=?,dir_id=?,is_sh=?";
	$stmt27=$this->query($sql27,array($userid,$did,1));
	$res=1;
      }
      else
      {
	$res1=$stmt->fetch();
	if($res1['is_sh']==1)
	{
	    $sqln1="update myshortlist set `is_sh`=? where id=?";
	    $stmtn1=$this->updatequery($sqln1,array(0,$res1['id']));
	    $res=0;
	}
	else
	{
	    $sqln1="update myshortlist set `is_sh`=? where id=?";
	    $stmtn1=$this->updatequery($sqln1,array(1,$res1['id']));
	    $res=1;
	}
      }
      
      return $res;
}

function alllevel($userid)
{
    $que="SELECT * from `lebel_setting` where `user_id`=?";
      $stmt=$this->query($que,array($userid));
      $res=$stmt->fetchAll(PDO::FETCH_ASSOC);
      return $res;
}



function addlevel($labelname,$labelcolor,$userid)
{
    $db=$this->getdbh();
    $affected_rowsval=array();
    $sql22 = "INSERT INTO `lebel_setting` set label=?,color=?,user_id=?";
    $stmt22=$this->query($sql22,array($labelname,$labelcolor,$userid));
    $labelIdc= $db->lastInsertId();
    $affected_rowsval['count'] = $stmt22->rowCount();
    $affected_rowsval['lid']=$labelIdc;
    
    return $affected_rowsval;
}

function leveledit($lid)
{
    $que="SELECT * from `lebel_setting` where `id`=?";
      $stmt=$this->query($que,array($lid));
      $res=$stmt->fetchAll(PDO::FETCH_ASSOC);
      return $res;
}

function leveldelete($lid)
{
    $que="delete from `lebel_setting` where `id`=?";
      $stmt=$this->query($que,array($lid));
      $affectrow=$stmt->rowCount();
      return $affectrow;
}

function updatelevel($labelname,$labelcolor,$lid)
{
    $affected_rowsval=array();
     $sqln1="update lebel_setting set `label`=?,color=? where id=?";
      $stmtn1=$this->updatequery($sqln1,array($labelname,$labelcolor,$lid));
      $affected_rowsval['count'] = $stmtn1->rowCount();
    $affected_rowsval['lid']=$lid;

      return $affected_rowsval;
}

function addtodolist($todolist,$lid,$tododate,$subtitle,$isfav,$userid)
{
    
$sql = "INSERT INTO `todo_list` set user_id=?,title=?,label=?,due_date=?,sub_item=?,is_fav=?";
$stmt=$this->query($sql,array($userid,$todolist,$lid,$tododate,$subtitle,$isfav));
$affect=$stmt->rowCount();

return $affect;
}

function todoedit($tid)
{
     $que="SELECT * from `todo_list` where `id`=?";
      $stmt=$this->query($que,array($tid));
      $res=$stmt->fetch();
      
      $res1[0]['title']=$res['title'];
      $res1[0]['label']=$res['label'];
      $res1[0]['due_date']=$res['due_date'];
      $res1[0]['sub_item']=htmlspecialchars_decode($res['sub_item']);
      $res1[0]['is_fav']=$res['is_fav'];
      
      return $res;
}

function subtitledelete($sid,$tid)
{
    $que="SELECT * from `todo_list` where `id`=?";
      $stmt=$this->query($que,array($tid));
      $res=$stmt->fetch();
      $sub_item=explode("|",$res['sub_item']);
      $subitem='';
      foreach($sub_item as $key=>$value)
      {
	if($key==$sid)
	{
	    $subitem=$value.'|';
	}
      }
     $subitem1=str_replace($subitem,"",$res['sub_item']);
      $sqln1="update todo_list set `sub_item`=? where id=?";
      $stmtn1=$this->updatequery($sqln1,array($subitem1,$tid));
      $affected_rowsval = $stmtn1->rowCount();
      
    return $affected_rowsval;
}

function edittodolist($todolist,$lid,$tododate,$subtitle,$isfav,$tid)
{
    $sqln1="update todo_list set title=?,label=?,due_date=?,sub_item=?,is_fav=? where id=?";
      $stmtn1=$this->updatequery($sqln1,array($todolist,$lid,$tododate,$subtitle,$isfav,$tid));
      $affected_rowsval = $stmtn1->rowCount();
      
    return $affected_rowsval;
}

function todolistdelete($tid)
{
    $que="delete from `todo_list` where `id`=?";
      $stmt=$this->query($que,array($tid));
      $affectrow=$stmt->rowCount();
      return $affectrow;
}

function gettodo($lid,$userid)
{
    $rest=array();
    if($lid!=0 && $lid!=-1)
    {
   $que="SELECT l.*,t.`due_date`,t.`title`,t.`sub_item`,t.`label` as tlabel,t.`id` as tid,t.`is_fav` FROM lebel_setting l,todo_list t where t.label=l.id and l.id=? order by `due_date` asc";
   $stmt=$this->query($que,array($lid));
    }
    elseif($lid==-1)
    {
	 $que="SELECT l.*,t.`due_date`,t.`title`,t.`sub_item`,t.`label` as tlabel,t.`id` as tid,t.`is_fav` FROM lebel_setting l,todo_list t where t.label=l.id and t.is_fav=? order by `due_date` asc";
    $stmt=$this->query($que,array(1));
    }
    else
    {
    $que="SELECT l.*,t.`due_date`,t.`title`,t.`sub_item`,t.`label` as tlabel,t.`id` as tid,t.`is_fav` FROM lebel_setting l,todo_list t where t.label=l.id and t.user_id=? order by `due_date` asc";
    $stmt=$this->query($que,array($userid));
    }
     
      $res=$stmt->fetchAll(PDO::FETCH_ASSOC);
      $i=0;
    
		    foreach($res as $row)
			{
			    if($row['is_fav']==1)
			       {
			       $image="images/s1.png";
			       }
			       else
			       {
			       $image="images/star1.png";
			       }
			       
                                $rest[$i]['favicon']=$image;
                                $rest[$i]['label']=$row['label'];
                                $rest[$i]['labelcolor']=$row['color'];
                                $rest[$i]['tid']=$row['tid'];
                                $rest[$i]['title']=$row['title'];
                                $rest[$i]['tododate']=$row['due_date'];
                                $rest[$i]['subtitle']=$row['sub_item'];
                        $i++;
                        }
                        
   return $rest;  
}

function allcategory($userid)
{
     $que="SELECT `slno`,`name`,`image`,`color` from `category`";
      $stmt=$this->query($que);
      $res=$stmt->fetchAll(PDO::FETCH_ASSOC);
      return $res;
}

function alldirectory($userid)
{
    $rest=array();
    $que="SELECT `slno`,`name`,`image`,`color`,`example` from `category`";
    $stmt=$this->query($que);
    $res=$stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach($res as $key=>$row)
    {
$totalavg=0;	
$food=0;
$service=0;
$price=0;

	$rest[$key]['cid']=$row['slno'];
	$rest[$key]['cname']=$row['name'];
	$rest[$key]['cimage']=$row['image'];
	$rest[$key]['ccolor']=$row['color'];

	$que1="select * from directory  where  category=? order by `priority` asc";
	$stmt11=$this->query($que1,array($row['slno']));
	while($res1=$stmt11->fetch())
	{
	    $qallsh="select `is_sh` from myshortlist where dir_id=? and user_id=?";
	    $stmsh=$this->query($qallsh,array($res1['id'],$userid));
	    $ressh=$stmsh->fetch();
	
	    $qallrev="select * from vendor_review where did=?";
	    $stmt1=$this->query($qallrev,array($res1['id']));
	    $total=$stmt1->rowCount();
	    while($rallrev=$stmt1->fetch())
	    {
	       $food+=$rallrev['food'];
	       $service+=$rallrev['service'];
	       $price+=$rallrev['price'];
	    }
		if($total>0)
		{
			    $totalavg=((($food/$total)+($service/$total)+($price/$total))/3)*(100/5);
		}
		else
		{
		    $totalavg=0;
		}
	    
	    $rest[$key]['directory'][$res1['id']]['dirimage']='http://weddingpenguin.sg/weddingnew/'.$res1['image'];
	    $rest[$key]['directory'][$res1['id']]['dirid']=$res1['id'];
	    $rest[$key]['directory'][$res1['id']]['dirname']=$res1['name'];
	    if($ressh['is_sh']==1)
	    {
	    $rest[$key]['directory'][$res1['id']]['issh']=$ressh['is_sh'];
	    }
	    $rest[$key]['directory'][$res1['id']]['totalavg']=$totalavg;
	    if(strpos(strtoupper($row['name']),'VENUES')!== false || strpos(strtoupper($row['name']),'HOTELS')!== false)
			{
	    $rest[$key]['directory'][$res1['id']]['iscap']=1;
	    $rest[$key]['directory'][$res1['id']]['cfrom']=$res1['capfrom'];
	    $rest[$key]['directory'][$res1['id']]['cto']=$res1['capto'];
	    $rest[$key]['directory'][$res1['id']]['exp']=$row['example'];
			}
			else
			{
			$rest[$key]['directory'][$res1['id']]['iscap']=0;
			}
			
	}
	
    }
    return $rest;
}

function getvenue($userid,$cid)
{
    $rest=array();
    if($cid==0 || $cid==-1)
    {
    $que="SELECT `slno`,`name`,`image`,`color`,`example` from `category`";
    $stmt=$this->query($que);
    }
    else
    {
	$que="SELECT `slno`,`name`,`image`,`color`,`example` from `category` where slno=?";
	 $stmt=$this->query($que,array($cid));
    }
    $res=$stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach($res as $key=>$row)
    {
$totalavg=0;	
$food=0;
$service=0;
$price=0;

	$rest[$key]['cid']=$row['slno'];
	$rest[$key]['cname']=$row['name'];
	$rest[$key]['cimage']=$row['image'];
	$rest[$key]['ccolor']=$row['color'];

	$que1="select * from directory  where  category=? order by `priority` asc";
	$stmt11=$this->query($que1,array($row['slno']));
	while($res1=$stmt11->fetch())
	{
	    $qallsh="select `is_sh` from myshortlist where dir_id=? and user_id=?";
	    $stmsh=$this->query($qallsh,array($res1['id'],$userid));
	    $ressh=$stmsh->fetch();
	
	
	    if($cid==-1)
	    {
		if($ressh['is_sh']==1)
		{
			$qallrev="select * from vendor_review where did=?";
			$stmt1=$this->query($qallrev,array($res1['id']));
			$total=$stmt1->rowCount();
			while($rallrev=$stmt1->fetch())
			{
			   $food+=$rallrev['food'];
			   $service+=$rallrev['service'];
			   $price+=$rallrev['price'];
			}
			    if($total>0)
			    {
					$totalavg=((($food/$total)+($service/$total)+($price/$total))/3)*(100/5);
			    }
			    else
			    {
				$totalavg=0;
			    }
			
			$rest[$key]['directory'][$res1['id']]['dirimage']='http://weddingpenguin.sg/weddingnew/'.$res1['image'];
			$rest[$key]['directory'][$res1['id']]['dirid']=$res1['id'];
			$rest[$key]['directory'][$res1['id']]['dirname']=$res1['name'];
			if($ressh['is_sh']==1)
			{
			$rest[$key]['directory'][$res1['id']]['issh']=$ressh['is_sh'];
			}
			$rest[$key]['directory'][$res1['id']]['totalavg']=$totalavg;
			if(strpos(strtoupper($row['name']),'VENUES')!== false || strpos(strtoupper($row['name']),'HOTELS')!== false)
				    {
			$rest[$key]['directory'][$res1['id']]['iscap']=1;
			$rest[$key]['directory'][$res1['id']]['cfrom']=$res1['capfrom'];
			$rest[$key]['directory'][$res1['id']]['cto']=$res1['capto'];
			$rest[$key]['directory'][$res1['id']]['exp']=$row['example'];
				    }
				    else
				    {
				    $rest[$key]['directory'][$res1['id']]['iscap']=0;
				    }
				    
		}
	    }
	    else
	    {
		$qallrev="select * from vendor_review where did=?";
	    $stmt1=$this->query($qallrev,array($res1['id']));
	    $total=$stmt1->rowCount();
	    while($rallrev=$stmt1->fetch())
	    {
	       $food+=$rallrev['food'];
	       $service+=$rallrev['service'];
	       $price+=$rallrev['price'];
	    }
		if($total>0)
		{
			    $totalavg=((($food/$total)+($service/$total)+($price/$total))/3)*(100/5);
		}
		else
		{
		    $totalavg=0;
		}
	    
	    $rest[$key]['directory'][$res1['id']]['dirimage']='http://weddingpenguin.sg/weddingnew/'.$res1['image'];
	    $rest[$key]['directory'][$res1['id']]['dirid']=$res1['id'];
	    $rest[$key]['directory'][$res1['id']]['dirname']=$res1['name'];
	    if($ressh['is_sh']==1)
	    {
	    $rest[$key]['directory'][$res1['id']]['issh']=$ressh['is_sh'];
	    }
	    $rest[$key]['directory'][$res1['id']]['totalavg']=$totalavg;
		    if(strpos(strtoupper($row['name']),'VENUES')!== false || strpos(strtoupper($row['name']),'HOTELS')!== false)
			{
	    $rest[$key]['directory'][$res1['id']]['iscap']=1;
	    $rest[$key]['directory'][$res1['id']]['cfrom']=$res1['capfrom'];
	    $rest[$key]['directory'][$res1['id']]['cto']=$res1['capto'];
	    $rest[$key]['directory'][$res1['id']]['exp']=$row['example'];
			}
			else
			{
			$rest[$key]['directory'][$res1['id']]['iscap']=0;
			}
		
			
	    }
	}
	
    }
    return $rest;

}

function venueserch($userid,$dirname,$cid)
{
    $rest=array();
    $que="SELECT `slno`,`name`,`image`,`color`,`example` from `category` where slno=?";
    $stmt=$this->query($que,array($cid));
    $res=$stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach($res as $key=>$row)
    {
$totalavg=0;	
$food=0;
$service=0;
$price=0;

	$rest[$key]['cid']=$row['slno'];
	$rest[$key]['cname']=$row['name'];
	$rest[$key]['cimage']=$row['image'];
	$rest[$key]['ccolor']=$row['color'];

	$que1="select * from directory  where  category=? and name like ? order by `priority` asc";
	$stmt11=$this->query($que1,array($row['slno'],"%$dirname%"));
	while($res1=$stmt11->fetch())
	{
	    $qallsh="select `is_sh` from myshortlist where dir_id=? and user_id=?";
	    $stmsh=$this->query($qallsh,array($res1['id'],$userid));
	    $ressh=$stmsh->fetch();
	
	    $qallrev="select * from vendor_review where did=?";
	    $stmt1=$this->query($qallrev,array($res1['id']));
	    $total=$stmt1->rowCount();
	    while($rallrev=$stmt1->fetch())
	    {
	       $food+=$rallrev['food'];
	       $service+=$rallrev['service'];
	       $price+=$rallrev['price'];
	    }
		if($total>0)
		{
			    $totalavg=((($food/$total)+($service/$total)+($price/$total))/3)*(100/5);
		}
		else
		{
		    $totalavg=0;
		}
	    
	    $rest[$key]['directory'][$res1['id']]['dirimage']='http://weddingpenguin.sg/weddingnew/'.$res1['image'];
	    $rest[$key]['directory'][$res1['id']]['dirid']=$res1['id'];
	    $rest[$key]['directory'][$res1['id']]['dirname']=$res1['name'];
	    if($ressh['is_sh']==1)
	    {
	    $rest[$key]['directory'][$res1['id']]['issh']=$ressh['is_sh'];
	    }
	    $rest[$key]['directory'][$res1['id']]['totalavg']=$totalavg;
	    if(strpos(strtoupper($row['name']),'VENUES')!== false || strpos(strtoupper($row['name']),'HOTELS')!== false)
			{
	    $rest[$key]['directory'][$res1['id']]['iscap']=1;
	    $rest[$key]['directory'][$res1['id']]['cfrom']=$res1['capfrom'];
	    $rest[$key]['directory'][$res1['id']]['cto']=$res1['capto'];
	    $rest[$key]['directory'][$res1['id']]['exp']=$row['example'];
			}
			else
			{
			$rest[$key]['directory'][$res1['id']]['iscap']=0;
			}
			
	}
	
    }
    return $rest;
    
}

function getfilter($cid)
{
    $rest=array();
     $que="SELECT * from `category` where slno=?";
    $stmt=$this->query($que,array($cid));
    $res=$stmt->fetch();
    $na='';
    $i=0;
   
	$qstmt = $this->query("SELECT ft.*, f.`id` as fid, f.`name` FROM  `filter_type` ft,`filter` f WHERE f.`id` = ft.`filter_id` AND ft.`id` IN ($res[filter_type])");
	while($rstmt=$qstmt->fetch())
	{
	   
	    $i++;
	    if($na!=$rstmt['name'])
	    {
		$rest[$rstmt['fid']]['filter']=$rstmt['name'];
	    }
	    $na=$rstmt['name'];
	    $rest[$rstmt['fid']]['cid']=$cid;
	    $rest[$rstmt['fid']]['ftype'][$i]['fid']=$rstmt['fid'];
	    $rest[$rstmt['fid']]['ftype'][$i]['ftid']=$rstmt['id'];
	    $rest[$rstmt['fid']]['ftype'][$i]['typename']=$rstmt['typename'];
	}
   return $rest;
}

function filtersrch($userid,$fid,$cid)
{
    $rest=array();
     $que="SELECT `slno`,`name`,`image`,`color`,`example` from `category` where slno=?";
    $stmt=$this->query($que,array($cid));
    $row=$stmt->fetch();
    
    $filterid=explode(",",$fid);
    
    $addd=''; $fid='';
    foreach($filterid as $value)
    {
	if($value!='')
	{
	    $val=explode("-",$value);
	    if($fid=='')
	    {
		$addd.= "and (`filter` like '%$val[1],%'";
		$fid=$val[0];
	    }
	    elseif($fid==$val[0])
	    {
		$addd.= " or `filter` like '%$val[1],%'";
		$fid=$val[0];
	    }
	    elseif($fid!=$val[0])
	    {
		$addd.=") and (`filter` like '%$val[1],%'";
		$fid=$val[0];
	    }
	}
    
    }
    if($_GET['filterid']!='')
    {
    $addd.=")";
    }

    
$totalavg=0;	$food=0; $service=0; $price=0;

	$rest[$cid]['cid']=$cid;
	$rest[$cid]['cname']=$row['name'];
	$rest[$cid]['cimage']=$row['image'];
	$rest[$cid]['ccolor']=$row['color'];

	
	$qven=$this->query("select * from directory where category=$cid $addd order by `name` asc ");
	while($res1=$qven->fetch())
	{
	    $qallsh="select `is_sh` from myshortlist where dir_id=? and user_id=?";
	    $stmsh=$this->query($qallsh,array($res1['id'],$userid));
	    $ressh=$stmsh->fetch();
	
	    $qallrev="select * from vendor_review where did=?";
	    $stmt1=$this->query($qallrev,array($res1['id']));
	    $total=$stmt1->rowCount();
	    while($rallrev=$stmt1->fetch())
	    {
	       $food+=$rallrev['food'];
	       $service+=$rallrev['service'];
	       $price+=$rallrev['price'];
	    }
		if($total>0)
		{
			    $totalavg=((($food/$total)+($service/$total)+($price/$total))/3)*(100/5);
		}
		else
		{
		    $totalavg=0;
		}
	    
	    $rest[$cid]['directory'][$res1['id']]['dirimage']='http://weddingpenguin.sg/weddingnew/'.$res1['image'];
	    $rest[$cid]['directory'][$res1['id']]['dirid']=$res1['id'];
	    $rest[$cid]['directory'][$res1['id']]['dirname']=$res1['name'];
	    if($ressh['is_sh']==1)
	    {
	    $rest[$cid]['directory'][$res1['id']]['issh']=$ressh['is_sh'];
	    }
	    $rest[$cid]['directory'][$res1['id']]['totalavg']=$totalavg;
	    if(strpos(strtoupper($row['name']),'VENUES')!== false || strpos(strtoupper($row['name']),'HOTELS')!== false)
			{
	    $rest[$cid]['directory'][$res1['id']]['iscap']=1;
	    $rest[$cid]['directory'][$res1['id']]['cfrom']=$res1['capfrom'];
	    $rest[$cid]['directory'][$res1['id']]['cto']=$res1['capto'];
	    $rest[$cid]['directory'][$res1['id']]['exp']=$row['example'];
			}
			else
			{
			$rest[$cid]['directory'][$res1['id']]['iscap']=0;
			}
			
	}
    
    
    
    
    return $rest;
    
    
}

function directorydetail($userid,$did)
{
    $rest=array();
    
    $que1="select * from directory  where  id=?";
    $stmt11=$this->query($que1,array($did));
    $res1=$stmt11->fetch();
	
    $que="SELECT `slno`,`name`,`image`,`color`,`example` from `category` where slno=?";
    $stmt=$this->query($que,array($res1['category']));
    $row=$stmt->fetch();
    
    
    
    $que2="SELECT `wedding_date` from `personal_detail` where `user_id`=?";
    $stmt2=$this->query($que2,array($userid));
    $row2=$stmt2->fetch();
    
$today=date('Y-m-d');
$totalavg=0;	
$food=0;
$service=0;
$price=0; 
	    $qallsh="select `is_sh` from myshortlist where dir_id=? and user_id=?";
	    $stmsh=$this->query($qallsh,array($res1['id'],$userid));
	    $ressh=$stmsh->fetch();
	
	    $qallrev="select * from vendor_review where did=?";
	    $stmt1=$this->query($qallrev,array($res1['id']));
	    $total=$stmt1->rowCount();
	    while($rallrev=$stmt1->fetch())
	    {
	       $food+=$rallrev['food'];
	       $service+=$rallrev['service'];
	       $price+=$rallrev['price'];
	    }
		if($total>0)
		{
		    $avgfood=($food/$total)*100/5;
		    $avgservice=($service/$total)*100/5;
		    $avgprice=($price/$total)*100/5;
		    $totalavg=((($food/$total)+($service/$total)+($price/$total))/3)*(100/5);
		}
		else
		{
		    $totalavg=0;
		    $avgfood=0;
		    $avgservice=0;
		    $avgprice=0;
		}
	    $rest['weddingdate']=$row2['wedding_date'];
	    $rest['cname']=$row['name'];
	    $rest['cid']=$row['slno'];
	    $rest['ccolor']=$row['color'];
	    $rest['dirimage']='http://weddingpenguin.sg/weddingnew/'.$res1['image'];
	    $rest['dirid']=$res1['id'];
	    $rest['dirname']=$res1['name'];
	    if($ressh['is_sh']==1)
	    {
	    $rest['issh']=$ressh['is_sh'];
	    }
	    $rest['totalavg']=$totalavg;
	    $rest['totalreview']=$total;
	    
	    if(strpos(strtoupper($row['name']),'VENUES')!== false || strpos(strtoupper($row['name']),'HOTELS')!== false)
			{
	    $rest['iscap']=1;
	    $rest['cfrom']=$res1['capfrom'];
	    $rest['cto']=$res1['capto'];
	    $rest['exp']=$row['example'];
			}
			else
			{
			$rest['iscap']=0;
			}
	    $rest['contact']=$res1['contact'];
	    $rest['pdf']=$res1['pdf'];
	    $rest['price']='http://weddingpenguin.sg/weddingnew/'.$res1['pdf'];
	    $rest['email']=$res1['email'];
	    $rest['address']=$res1['address'];
	    
	    $rest['avgfood']=$avgfood;
	    $rest['avgser']=$avgservice;
	    $rest['avgpr']=$avgprice;
	
	$que3="select * from `allroom` where `p_id`=?";
	$stmt3=$this->query($que3,array($res1['id']));

	while($rallroom=$stmt3->fetch())
	{

	    $imageall=explode("|",$rallroom['picture']);
	    $rest['allroom'][$rallroom['id']]['rid']=$rallroom['id'];
	    $rest['allroom'][$rallroom['id']]['allroomimage']=$rallroom['picture'];
	    $rest['allroom'][$rallroom['id']]['roomname']=$rallroom['roomname'];
	    $rest['allroom'][$rallroom['id']]['cap1']=$rallroom['cap1'];
	    $rest['allroom'][$rallroom['id']]['cap2']=$rallroom['cap2'];
	    $rest['allroom'][$rallroom['id']]['roomimage']='http://weddingpenguin.sg/weddingnew/'.$imageall[0];
	}
	
	$que4="select v.*,s.`profilepic`,s.`name` from vendor_review v,signup s where s.id=v.user_id and v.`did`=? and v.`date`<= NOW() - INTERVAL 1 DAY";
	$stmt4=$this->query($que4,array($res1['id']));

	while($review=$stmt4->fetch())
	{
	    $rest['review'][$review['id']]['name']=$review['name'];
	    $rest['review'][$review['id']]['date']=date('d M Y',strtotime($review['date']));
	    $rest['review'][$review['id']]['profilepic']='http://weddingpenguin.sg/weddingnew/'.$review['profilepic'];
	    $rest['review'][$review['id']]['food']=$review['food'];
	    $rest['review'][$review['id']]['service']=$review['service'];
	    $rest['review'][$review['id']]['price']=$review['price'];
	    $rest['review'][$review['id']]['rev']=$review['review'];
	}
	
	
	
    return $rest;
}

function addrev($did,$userid,$review,$food,$service,$price)
{
    $que1="SELECT v.`username`,v.`acctype` FROM  `directory` d, `admin` v WHERE d.v_id = v.slno AND d.id =?";
    $stmt11=$this->query($que1,array($did));
    $res1=$stmt11->fetch();
    
    $que2="SELECT name FROM  `signup` WHERE id =?";
    $stmt2=$this->query($que2,array($userid));
    $res2=$stmt2->fetch();
    
    $sql = "INSERT INTO `vendor_review` set user_id=?,did=?,food=?,service=?,price=?,review=?";
    $stmt=$this->query($sql,array($userid,$did,$food,$service,$price,$review));
    $affectrow=$stmt->rowCount();
    

if($res1['acctype']!='Free')
{
//mail
$nm=$res2['name'];
$to =$res1['username'];
$subject = "Review";
$message = "
<html>
<head>
<title>Review</title>
</head>
<body>
<p>Dear $to,
<br/>
Review by $nm
<br/>
$review
<br/>
--
<br/>
Wedding Penguin team</p>
</body>
</html>"; 
// Always set content-type when sending HTML email
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

// More headers
$headers .= 'From: <administrator@weddingpenguin.sg>' . "\r\n";
mail($to,$subject,$message,$headers);

//mail
}

return $affectrow;
}

function allguest($userid)
{
    $rest=array();
    $que="select * from `all_relation` where `user_id`=?";
    $stmt=$this->query($que,array($userid));
    $res=$stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach($res as $key=>$row)
    {
	
	$rest[$key]['relation']=$row['add_val'];
	$rest[$key]['relid']=$row['id'];
	
	$que1="select * from `allguest` where `owner`=? and `relation`=? and `attending`!=?";
	$stmt1=$this->query($que1,array($userid,$row['id'],'Archive'));
	$i=0;
	while($res1=$stmt1->fetch())
	{
	    $i++;
	    if($res1['attending']=='Yes')
	    {
		    $variable="Accept";
		    $class='g';
	    }
	    elseif($res1['attending']=='May be')
	    {
		    $variable="Waiting";
		    $class='y';
	    }
	    elseif($res1['attending']=='No')
	    {
		    $variable="Not Sent";
		    $class='p';
	    }
	    elseif($res1['attending']=='Reject')
	    {
		    $variable="Reject";
		    $class='p';
	    }
	    
	   $rest[$key]['allguest'][$row['id']][$res1['id']]['slno']=str_pad($i,2,"0",STR_PAD_LEFT);
	   $rest[$key]['allguest'][$row['id']][$res1['id']]['gid']=$res1['id'];
	   $rest[$key]['allguest'][$row['id']][$res1['id']]['gname']=$res1['name'];
	   $rest[$key]['allguest'][$row['id']][$res1['id']]['gtable']=str_pad($res1['table_no'],2,"0",STR_PAD_LEFT);
	   $rest[$key]['allguest'][$row['id']][$res1['id']]['gstatus']=$variable;
	   $rest[$key]['allguest'][$row['id']][$res1['id']]['gcolor']=$class;
	}
    }
    return $rest;
}

function guestinfo($userid)
{
    $rest=array();
    $que="SELECT COUNT(`id`) AS totalguest FROM  `allguest` WHERE  `owner` =?";
    $stmt=$this->query($que,array($userid));
    $res=$stmt->fetch();
    
    $que1="SELECT COUNT(`id`) AS totalacc FROM  `allguest` WHERE  `owner` =? and `attending`=?";
    $stmt1=$this->query($que1,array($userid,'Yes'));
    $res1=$stmt1->fetch();
    
    $que2="select count(`id`) as total_table,`total_seat` from `table_info` where `user_id`=?";
    $stmt2=$this->query($que2,array($userid));
    $res2=$stmt2->fetch();
    
    $fpref='';
    $que3="select * from `food_pref` where `user_id`=?";
    $stmt3=$this->query($que3,array($userid));
    while($rtotalf=$stmt3->fetch())
    {
	    $que4="select COUNT(`id`) AS totalf from `allguest` WHERE  `owner` =? and `food_preference`=?";
	    $stmt4=$this->query($que4,array($userid,$rtotalf['id']));
	    $rtf=$stmt4->fetch();
	    $fpref.=$rtf['totalf'].'  '.$rtotalf['food'].',';
    }
    $fpref=rtrim($fpref,',');

    $rest['totalguest']=$res['totalguest'];
    $rest['totalacc']=$res1['totalacc'];
    $rest['totaltable']=$res2['total_table'];
    $rest['totalseat']=$res2['total_seat'];
    $rest['foodpref']=$fpref;
    
    return $rest;
}

function addsetting($userid,$totaltable,$totalseat)
{
    $que="select * from `table_info` where `user_id`=?";
    $stmt=$this->query($que,array($userid));
    $count=$stmt->rowCount();

    if($count==0)
    {
	for($i=1;$i<=$totaltable;$i++)
	{
	$sql = "INSERT INTO `table_info` set user_id=?,total_table=?,total_seat=?";
	$stmt=$this->query($sql,array($userid,$i,$totalseat));
	$affectrow=$stmt->rowCount();
	}
    }
    else
    {
	$sql = "delete from `table_info` where `user_id`=?";
	$stmt=$this->query($sql,array($userid));
	
	for($i=1;$i<=$totaltable;$i++)
	{
	$sql1 = "INSERT INTO `table_info` set user_id=?,total_table=?,total_seat=?";
	$stmt1=$this->query($sql1,array($userid,$i,$totalseat));
	$affectrow=$stmt1->rowCount();
	}
    }
    
    return $affectrow;
    
}

function addrelation($userid,$relation)
{
    $db=$this->getdbh();
    $affectrow=array();
    $sql1 = "INSERT INTO `all_relation` set user_id=?,add_val=?";
	$stmt1=$this->query($sql1,array($userid,$relation));
	$affectrow['count']=$stmt1->rowCount();
	$lastinsertid=$db->lastInsertId();
	$affectrow['id']=$lastinsertid;
    return $affectrow;
}

function allrelation($userid)
{
     $que="select * from `all_relation` where `user_id`=?";
     $stmt=$this->query($que,array($userid));
     $res=$stmt->fetchAll(PDO::FETCH_ASSOC);
     
     return $res;
}

function addfoodpref($userid,$foodpref)
{
    $db=$this->getdbh();
    $affectrow=array();
    $sql1 = "INSERT INTO `food_pref` set user_id=?,food=?";
	$stmt1=$this->query($sql1,array($userid,$foodpref));
	$affectrow['count']=$stmt1->rowCount();
	$lastinsertid=$db->lastInsertId();
	$affectrow['id']=$lastinsertid;
    return $affectrow;
}

function allfoodpref($userid)
{
    $que="select * from `food_pref` where `user_id`=?";
     $stmt=$this->query($que,array($userid));
     $res=$stmt->fetchAll(PDO::FETCH_ASSOC);
     
     return $res;
}

function editrelation($rid)
{
    $res=array();
    $que="select * from `all_relation` where `id`=?";
    $stmt=$this->query($que,array($rid));
    $res1=$stmt->fetch();
    
    $res['add_val']=$res1['add_val'];
    return $res;
}

function updaterelation($rid,$relation)
{
    $sql="UPDATE all_relation SET add_val=? WHERE id=?";
    $stmt=$this->updatequery($sql,array($relation,$rid));
    $affect=$stmt->rowCount();
   
   return $affect;
}

function deleterelation($rid)
{
    $que="delete from `all_relation` where `id`=?";
    $stmt=$this->query($que,array($rid));
    $affectrow=$stmt->rowCount();
    return $affectrow;
}

function editfoodpref($fid)
{
    $res=array();
    $que="select * from `food_pref` where `id`=?";
    $stmt=$this->query($que,array($fid));
    $res1=$stmt->fetch();
    
    $res['food']=$res1['food'];
    return $res;
}

function updatefoodpref($fid,$food)
{
    $sql="UPDATE food_pref SET food=? WHERE id=?";
    $stmt=$this->updatequery($sql,array($food,$fid));
    $affect=$stmt->rowCount();
   
   return $affect;
}

function deletefoodpref($fid)
{
    $que="delete from `food_pref` where `id`=?";
    $stmt=$this->query($que,array($fid));
    $affectrow=$stmt->rowCount();
    return $affectrow;
}

function addguest($userid,$gname,$grelation,$gtable,$gfood,$email,$phone,$status)
{
    $sql1 = "INSERT INTO `allguest` set owner=?,name=?,attending=?,food_preference=?,email=?,phone=?,relation=?,table_no=?";
    $stmt1=$this->query($sql1,array($userid,$gname,$status,$gfood,$email,$phone,$grelation,$gtable));
    $affectrow=$stmt1->rowCount();
    
    return $affectrow;
}

function guestdetail($gid)
{
    $res=array();
    $que="select * from `allguest` where `id`=?";
    $stmt=$this->query($que,array($gid));
    $res1=$stmt->fetch();
    
    $res['name']=$res1['name'];
    $res['status']=$res1['attending'];
    $res['foodpref']=$res1['food_preference'];
    $res['email']=$res1['email'];
    $res['phone']=$res1['phone'];
    $res['relation']=$res1['relation'];
    $res['table_no']=$res1['table_no'];
     return $res;
}

function updateguest($gid,$gname,$grelation,$gtable,$gfood,$email,$phone,$status)
{
    $sql="UPDATE allguest SET name=?,attending=?,food_preference=?,email=?,phone=?,relation=?,table_no=? WHERE id=?";
    $stmt=$this->updatequery($sql,array($gname,$status,$gfood,$email,$phone,$grelation,$gtable,$gid));
    $affect=$stmt->rowCount();
   
   return $affect;
}

function deleteguest($gid)
{
    $que="delete from `allguest` where `id`=?";
    $stmt=$this->query($que,array($gid));
    $affectrow=$stmt->rowCount();
    return $affectrow;
}

function gettypedetail($userid,$type)
{
    $rest=array();
    
     $que2="select count(`id`) as total_table,`total_seat` from `table_info` where `user_id`=?";
    $stmt2=$this->query($que2,array($userid));
    $res2=$stmt2->fetch();
    
    if($type=='Relationship')
    {
    $que="select * from `all_relation` where `user_id`=?";
    $stmt=$this->query($que,array($userid));
    $res=$stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach($res as $key=>$row)
    {
	
	$rest[$key]['relation']=$row['add_val'];
	$rest[$key]['relid']=$row['id'];
	
	$que1="select * from `allguest` where `owner`=? and `relation`=? and `attending`!=?";
	$stmt1=$this->query($que1,array($userid,$row['id'],'Archive'));
	$i=0;
	while($res1=$stmt1->fetch())
	{
	    $i++;
	    if($res1['attending']=='Yes')
	    {
		    $variable="Accept";
		    $class='g';
	    }
	    elseif($res1['attending']=='May be')
	    {
		    $variable="Waiting";
		    $class='y';
	    }
	    elseif($res1['attending']=='No')
	    {
		    $variable="Not Sent";
		    $class='p';
	    }
	    elseif($res1['attending']=='Reject')
	    {
		    $variable="Reject";
		    $class='p';
	    }
	    
	   $rest[$key]['allguest'][$row['id']][$res1['id']]['slno']=str_pad($i,2,"0",STR_PAD_LEFT);
	   $rest[$key]['allguest'][$row['id']][$res1['id']]['gid']=$res1['id'];
	   $rest[$key]['allguest'][$row['id']][$res1['id']]['gname']=$res1['name'];
	   $rest[$key]['allguest'][$row['id']][$res1['id']]['gtable']=str_pad($res1['table_no'],2,"0",STR_PAD_LEFT);
	   $rest[$key]['allguest'][$row['id']][$res1['id']]['gstatus']=$variable;
	   $rest[$key]['allguest'][$row['id']][$res1['id']]['gcolor']=$class;
	}
    }
    
    }
    elseif($type=='Table')
    {
	for($p=0;$p<=$res2['total_table'];$p++)
    {
	if($p==0)
	{
	    $rest[$p]['relation']='No Table';
	}
	else
	{
	$rest[$p]['relation']='Table No.'.str_pad($p,2,"0",STR_PAD_LEFT);
	}
	
	$que1="select * from `allguest` where `owner`=? and `table_no`=? and `attending`!=?";
	$stmt1=$this->query($que1,array($userid,$p,'Archive'));
	$i=0;
	while($res1=$stmt1->fetch())
	{
	    $i++;
	    if($res1['attending']=='Yes')
	    {
		    $variable="Accept";
		    $class='g';
	    }
	    elseif($res1['attending']=='May be')
	    {
		    $variable="Waiting";
		    $class='y';
	    }
	    elseif($res1['attending']=='No')
	    {
		    $variable="Not Sent";
		    $class='p';
	    }
	    elseif($res1['attending']=='Reject')
	    {
		    $variable="Reject";
		    $class='p';
	    }
	    
	   $rest[$p]['allguest'][$p][$res1['id']]['slno']=str_pad($i,2,"0",STR_PAD_LEFT);
	   $rest[$p]['allguest'][$p][$res1['id']]['gid']=$res1['id'];
	   $rest[$p]['allguest'][$p][$res1['id']]['gname']=$res1['name'];
	   $rest[$p]['allguest'][$p][$res1['id']]['gtable']=str_pad($res1['table_no'],2,"0",STR_PAD_LEFT);
	   $rest[$p]['allguest'][$p][$res1['id']]['gstatus']=$variable;
	   $rest[$p]['allguest'][$p][$res1['id']]['gcolor']=$class;
	}
    }
    }
    
    elseif($type=='Alphabetical')
    {
	 
	$que1="select * from `allguest` where `owner`=? and `attending`!=? group by `name`";
	$stmt1=$this->query($que1,array($userid,'Archive'));
	$i=0;
	while($res1=$stmt1->fetch())
	{
	    $i++;
	    if($res1['attending']=='Yes')
	    {
		    $variable="Accept";
		    $class='g';
	    }
	    elseif($res1['attending']=='May be')
	    {
		    $variable="Waiting";
		    $class='y';
	    }
	    elseif($res1['attending']=='No')
	    {
		    $variable="Not Sent";
		    $class='p';
	    }
	    elseif($res1['attending']=='Reject')
	    {
		    $variable="Reject";
		    $class='p';
	    }
	    
	   $rest[$res1['id']]['slno']=str_pad($i,2,"0",STR_PAD_LEFT);
	   $rest[$res1['id']]['gid']=$res1['id'];
	   $rest[$res1['id']]['gname']=$res1['name'];
	   $rest[$res1['id']]['gtable']=str_pad($res1['table_no'],2,"0",STR_PAD_LEFT);
	   $rest[$res1['id']]['gstatus']=$variable;
	   $rest[$res1['id']]['gcolor']=$class;
    }
    }
    
    return $rest;

}

function guestsearch($userid,$value)
{
    $rest=array();
    $que1="select * from `allguest` where `owner`=? and `attending`!=? and `name` like ? group by `name`";
	$stmt1=$this->query($que1,array($userid,'Archive',"%$value%"));
	$i=0;
	while($res1=$stmt1->fetch())
	{
	    $i++;
	    if($res1['attending']=='Yes')
	    {
		    $variable="Accept";
		    $class='g';
	    }
	    elseif($res1['attending']=='May be')
	    {
		    $variable="Waiting";
		    $class='y';
	    }
	    elseif($res1['attending']=='No')
	    {
		    $variable="Not Sent";
		    $class='p';
	    }
	    elseif($res1['attending']=='Reject')
	    {
		    $variable="Reject";
		    $class='p';
	    }
	    
	   $rest[$res1['id']]['slno']=str_pad($i,2,"0",STR_PAD_LEFT);
	   $rest[$res1['id']]['gid']=$res1['id'];
	   $rest[$res1['id']]['gname']=$res1['name'];
	   $rest[$res1['id']]['gtable']=str_pad($res1['table_no'],2,"0",STR_PAD_LEFT);
	   $rest[$res1['id']]['gstatus']=$variable;
	   $rest[$res1['id']]['gcolor']=$class;
	}
    return $rest;
}

function allfolder($userid)
{
    $rest=array();
    $que1="select * from `add_folder` where `user_id`=?";
    $stmt1=$this->query($que1,array($userid));
    while($res1=$stmt1->fetch())
    {
	 $que="select `pic`, count(`id`) as cnt from `album_pic` where `user_id`=? and `fid`=?";
	    $stmt=$this->query($que,array($userid,$res1['id']));
	    $res=$stmt->fetch();
	    
	if($res1['image']=='')
	{
	    $image=$res['pic'];
	}
	else
	{
	    $image=$res1['image'];
	}
	
	if($res1['type']=='Locked')
	{
	    $img='images/icon31.png';
	}
	elseif($res1['type']=='Friends')
	{
	    $img='images/icon32.png';
	}
	elseif($res1['type']=='Everyone')
	{
	    $img='images/icon33.png';
	}
	    $rest[$res1['id']]['fid']=$res1['id'];
	    $rest[$res1['id']]['foldername']=$res1['foldername'];
	    $rest[$res1['id']]['ftype']=$img;
	    $rest[$res1['id']]['totalpic']=$res['cnt'];
	    $rest[$res1['id']]['folderimage']='http://weddingpenguin.sg/weddingnew/'.$image;
	    
	$que2="select * from `sub_folder` where `fid`=?";
	$stmt2=$this->query($que2,array($res1['id']));
	$count=$stmt2->rowCount();
	if($count>0)
	{
	while($res2=$stmt2->fetch())
	{
	    $rest[$res1['id']]['sub_folder'][$res2['id']]['sfid']=$res2['id'];
	    $rest[$res1['id']]['sub_folder'][$res2['id']]['name']=$res2['name'];
	}
	}
    
    }
    
    return $rest;
}

function addfolder($userid,$folder,$type)
{
     $db=$this->getdbh();
    $affectrow=array();
    $sql1 = "INSERT INTO `add_folder` set user_id=?,foldername=?,type=?";
	$stmt1=$this->query($sql1,array($userid,$folder,$type));
	$affectrow['count']=$stmt1->rowCount();
	$lastinsertid=$db->lastInsertId();
	$affectrow['id']=$lastinsertid;
    return $affectrow;
}

function editfolder($fid)
{
    $res=array();
    $que="select * from `add_folder` where `id`=?";
    $stmt=$this->query($que,array($fid));
    $res1=$stmt->fetch();
    
    if($res1['type']=='Locked')
	{
	    $img='images/icon44.png';
	}
	elseif($res1['type']=='Friends')
	{
	    $img='images/icon35.png';
	}
	elseif($res1['type']=='Everyone')
	{
	    $img='images/icon43.png';
	}
    
    $res['foldername']=$res1['foldername'];
    $res['img']=$img;
    
    $que1="select * from `sub_folder` where `fid`=?";
    $stmt1=$this->query($que1,array($fid));
    while($res2=$stmt1->fetch())
    {
	$res['sub_folder'][$res2['id']]['sfid']=$res2['id'];
	$res['sub_folder'][$res2['id']]['name']=$res2['name'];
    }
    return $res;
}


function updatefolder($folder,$subfolder,$userid,$fid,$sfid)
{
     $sql="UPDATE add_folder SET foldername=? WHERE id=?";
    $stmt=$this->updatequery($sql,array($folder,$fid));
    $affect=$stmt->rowCount();
    
    $sub_folder=explode("|",$subfolder);
    $sf_id=explode("|",$sfid);
  
    foreach($sub_folder as $key=>$value)
    {
	
	if($value!='')
	{
	if(isset($sf_id[$key]) && $sf_id[$key]!='')
	{
	    
	    $sql="UPDATE sub_folder SET name=? WHERE id=?";
	    $stmt=$this->updatequery($sql,array($sub_folder[$key],$sf_id[$key]));
	}
	else
	{
	    
	      $sql1 = "INSERT INTO `sub_folder` set user_id=?,fid=?,name=?";
	    $stmt1=$this->query($sql1,array($userid,$fid,$sub_folder[$key]));
	    
	    
	}
	}
	
    }
    
    return $affect;
}

function deletegfolder($fid)
{
    $que="delete from `add_folder` where `id`=?";
    $stmt=$this->query($que,array($fid));
    $affectrow=$stmt->rowCount();
    return $affectrow; 
}

function deletegsfolder($fid,$sfid)
{
     $que="delete from `sub_folder` where `id`=?";
    $stmt=$this->query($que,array($sfid));
    $affectrow=$stmt->rowCount();
    return $affectrow; 
}

function allpic($userid,$fid)
{
    $rest=array();
    $que1="select a.*,s.`name` from `album_pic` a,`signup` s where a.`fid`=? and s.`id`=a.`user_id`";
    $stmt1=$this->query($que1,array($fid));
    while($res1=$stmt1->fetch())
    {
	    
	    $rest[$res1['id']]['picid']=$res1['id'];
	    if($res1['is_guest']==0)
	    {
	    $rest[$res1['id']]['uploadby']=$res1['name'];
	    }
	    else
	    {
		$qname="select `name` from `allguest` where id=?";
		$st=$this->query($qname,array($res1['is_guest']));
		$rname=$st->fetch();
		 $rest[$res1['id']]['uploadby']=$rname['name'];
	    }
	    $rest[$res1['id']]['pic']='http://weddingpenguin.sg/weddingnew/'.$res1['pic'];
    }
    
    return $rest;
}

function folderdetail($userid,$fid)
{
    $rest=array();
    $que1="select * from `add_folder` where `id`=?";
    $stmt1=$this->query($que1,array($fid));
    $res=$stmt1->fetch();
    $rest['fname']=$res['foldername'];
    
    $que="select * from `sub_folder` where `fid`=?";
    $stmt=$this->query($que,array($fid));
    while($res1=$stmt->fetch())
    {
	$que2="select `pic`, count(`id`) as cnt from `album_pic` where `user_id`=? and `sfid`=?";
	    $stmt2=$this->query($que2,array($userid,$res1['id']));
	    $res2=$stmt2->fetch();
	    
	$rest['subfolder'][$res1['id']]['sfid']=$res1['id'];
	$rest['subfolder'][$res1['id']]['name']=$res1['name'];
	$rest['subfolder'][$res1['id']]['pic']='http://weddingpenguin.sg/weddingnew/'.$res2['pic'];
	$rest['subfolder'][$res1['id']]['totalpic']=$res2['cnt'];
    }
    
    return $rest;
}

function upload($userid,$fid,$image,$size)
{
   //$file='http://weddingpenguin.sg/weddingnew/upload/'.time().'.jpg';
   $file='/var/www/public_html/weddingpenguin.sg/weddingnew/upload/'.time().'.jpg';
   $file1='upload/'.time().'.jpg';
   $x=fopen($file,'w');
   fwrite($x,base64_decode($image));
   fclose($x);

   $sql = "INSERT INTO `album_pic` set user_id=?,fid=?,sfid=?,pic=?,is_guest=?,size=?";
$stmt=$this->query($sql,array($userid,$fid,0,$file1,0,$size));

$affected_rowsval = $stmt->rowCount();
return $affected_rowsval;
}

function upload1($userid,$fid,$sfid,$image,$size)
{
   //$file='http://weddingpenguin.sg/weddingnew/upload/'.time().'.jpg';
   $file='/var/www/public_html/weddingpenguin.sg/weddingnew/upload/'.time().'.jpg';
   $file1='upload/'.time().'.jpg';
   $x=fopen($file,'w');
   fwrite($x,base64_decode($image));
   fclose($x);

   $sql = "INSERT INTO `album_pic` set user_id=?,fid=?,sfid=?,pic=?,is_guest=?,size=?";
$stmt=$this->query($sql,array($userid,$fid,$sfid,$file1,0,$size));

$affected_rowsval = $stmt->rowCount();
return $affected_rowsval;
}

function allpic1($userid,$sfid)
{
    $rest=array();
    $que1="select a.*,s.`name` from `album_pic` a,`signup` s where a.`sfid`=? and s.`id`=a.`user_id`";
    $stmt1=$this->query($que1,array($sfid));
    while($res1=$stmt1->fetch())
    {
	    
	    $rest[$res1['id']]['picid']=$res1['id'];
	    if($res1['is_guest']==0)
	    {
	    $rest[$res1['id']]['uploadby']=$res1['name'];
	    }
	    else
	    {
		$qname="select `name` from `allguest` where id=?";
		$st=$this->query($qname,array($res1['is_guest']));
		$rname=$st->fetch();
		 $rest[$res1['id']]['uploadby']=$rname['name'];
	    }
	    $rest[$res1['id']]['pic']='http://weddingpenguin.sg/weddingnew/'.$res1['pic'];
    }
    
    return $rest;
}

function redpacket()
{
   $rest=array();
   $que="select * from `venue_detail`";
      $stmt=$this->query($que,array());
      $res=$stmt->fetchAll(PDO::FETCH_ASSOC);
      $i=0;
		    foreach($res as $row)
			{
				$vendorid=$row['vendor_id'];
				$hotelid=$row['hotel_id'];
                                $que1="SELECT * FROM directory where id=?";
			        $stmtt1 = $this->query($que1,array($hotelid));
			        $res1=$stmtt1->fetch();
                                
                                $rest[$i]['name']=$res1['name'];
                                $rest[$i]['image']='http://weddingpenguin.sg/weddingnew/'.$res1['image'];
                                $rest[$i]['slno']=$res1['id'];
                                $rest[$i]['lunch_basic_weekday']=$row['lunch_basic_weekday'];
                                $rest[$i]['lunch_basic_weekend']=$row['lunch_basic_weekend'];
                                $rest[$i]['lunch_basic_holiday']=$row['lunch_basic_holiday'];
                                $rest[$i]['lunch_premium_weekday']=$row['lunch_premium_weekday'];
                                $rest[$i]['lunch_premium_weekend']=$row['lunch_premium_weekend'];
                                $rest[$i]['lunch_premium_holiday']=$row['lunch_premium_holiday'];
                                $rest[$i]['dinner_basic_weekday']=$row['dinner_basic_weekday'];
                                $rest[$i]['dinner_basic_weekend']=$row['dinner_basic_weekend'];
                                $rest[$i]['dinner_basic_holiday']=$row['dinner_basic_holiday'];
                                $rest[$i]['dinner_premium_weekday']=$row['dinner_premium_weekday'];
                                $rest[$i]['dinner_premium_weekend']=$row['dinner_premium_weekend'];
                                $rest[$i]['dinner_premium_holiday']=$row['dinner_premium_holiday'];
                                
                        $i++;
                        }
                        
   return $rest;                     
   
}

function searchhotel($hotel)
{
    $rest=array();
   $que="select v.*,d.`name`,d.`image`,d.`id` did from `venue_detail` v,`directory` d where d.`id`=v.`hotel_id` and d.`name` like ?";
      $stmt=$this->query($que,array("%$hotel%"));
      $res=$stmt->fetchAll(PDO::FETCH_ASSOC);
      $i=0;
		    foreach($res as $row)
			{
				$vendorid=$row['vendor_id'];
				$hotelid=$row['hotel_id'];
                                
                                
                                $rest[$i]['name']=$row['name'];
                                $rest[$i]['image']='http://weddingpenguin.sg/weddingnew/'.$row['image'];
                                $rest[$i]['slno']=$row['did'];
                                $rest[$i]['lunch_basic_weekday']=$row['lunch_basic_weekday'];
                                $rest[$i]['lunch_basic_weekend']=$row['lunch_basic_weekend'];
                                $rest[$i]['lunch_basic_holiday']=$row['lunch_basic_holiday'];
                                $rest[$i]['lunch_premium_weekday']=$row['lunch_premium_weekday'];
                                $rest[$i]['lunch_premium_weekend']=$row['lunch_premium_weekend'];
                                $rest[$i]['lunch_premium_holiday']=$row['lunch_premium_holiday'];
                                $rest[$i]['dinner_basic_weekday']=$row['dinner_basic_weekday'];
                                $rest[$i]['dinner_basic_weekend']=$row['dinner_basic_weekend'];
                                $rest[$i]['dinner_basic_holiday']=$row['dinner_basic_holiday'];
                                $rest[$i]['dinner_premium_weekday']=$row['dinner_premium_weekday'];
                                $rest[$i]['dinner_premium_weekend']=$row['dinner_premium_weekend'];
                                $rest[$i]['dinner_premium_holiday']=$row['dinner_premium_holiday'];
                                
                        $i++;
                        }
                        
   return $rest;  
}

function storagedetail($userid)
{
    $rest=array();
    $que="select sum(`size`) as size from `gallery` where `user_id`=?";
    $qtotal=$this->query($que,array($userid));
    $rtotal=$qtotal->fetch();
    
    $que1="select sum(`size`) as size from `add_folder` where `user_id`=?";
    $qaddf=$this->query($que1,array($userid));
    $raddf=$qaddf->fetch();
    
    $que2="select sum(`size`) as size from `sub_folder` where `user_id`=?";
    $qaddsf=$this->query($que2,array($userid));
    $raddsf=$qaddsf->fetch();
    
    $que3="SELECT SUM(  `size` ) AS size, COUNT(  `id` ) AS count FROM  `album_pic` where  `user_id`=?";
    $qtotal1=$this->query($que3,array($userid));
    $rtotal1=$qtotal1->fetch();
    
    $totalsize=$rtotal['size']+$rtotal1['size']+$raddf['size']+$raddsf['size'];
    if($totalsize=='')
    {
	    $totalsize=0;
    }
    $percuse=$totalsize/1000;
    $rest['totalsize']=round($totalsize,2);
    $rest['percuse']=$percuse;
    
    return $rest;
}

function budgethistory($userid)
{
    $rest=array();
$actual=0;
$budget=0;
$paid=0;
$balance=0;

$que2="select * from `budget` where `user_id`=?";
$qaddsf=$this->query($que2,array($userid));
while($rtotal=$qaddsf->fetch())
{
	$actual+=$rtotal['actual'];
	$budget+=$rtotal['budget'];
	
	$amarr=explode("|",$rtotal['amount']);	
	foreach($amarr as $key=>$value)
	{
		$paid+=$value;
	}
}

$balance=$actual-$paid;

$rest['actual']=$actual;
$rest['budget']=$budget;
$rest['paid']=$paid;
$rest['balance']=$balance;

return $rest;
}

function allbudget($userid)
{
    $rest=array();
    $que2="select b.*,d.`name` as dname,c.`name` as cname,c.`color` from `budget` b,`directory` d,`category` c where b.`item`=c.`slno` and b.`vendor`=d.`id` and b.`user_id`=?";
    $qaddsf=$this->query($que2,array($userid));
    $res=$qaddsf->fetchAll(PDO::FETCH_ASSOC);
    
    foreach($res as $key=>$row)
    {
	$rest[$key]['bid']=$row['id'];
	$rest[$key]['cname']=$row['cname'];
	$rest[$key]['color']=$row['color'];
	$rest[$key]['dname']=$row['dname'];
	$rest[$key]['budget']=$row['budget'];
	$rest[$key]['actual']=$row['actual'];
	
	$total=0;
				   $budarr=explode("|",$row['amount']);
                                   $datearr=explode("|",$row['date']);
                                        foreach($budarr as $key1=>$value)
                                        {
                                        if($value!='')
                                        {
					$total+=$value;
					
					$rest[$key]['bdetail'][$key1]['pdate']=date('d / m / Y',strtotime($datearr[$key1]));
					$rest[$key]['bdetail'][$key1]['amount']=$value;
					}
					}
    }
    
    return $rest;
}

function getdir($cid)
{
     $que="SELECT `id`,`name` from `directory` where `category`=?";
      $stmt=$this->query($que,array($cid));
      $res=$stmt->fetchAll(PDO::FETCH_ASSOC);
      return $res;
}

function addbudget($userid,$category,$did,$budget,$actual,$amount,$date)
{
    $sql = "INSERT INTO `budget` set user_id=?,item=?,vendor=?,budget=?,actual=?,amount=?,date=?";
    $stmt=$this->query($sql,array($userid,$category,$did,$budget,$actual,$amount,$date));
    $affectrow=$stmt->rowCount();
    
    return $affectrow;
}

function getbudgetdetail($bid)
{
    $rest=array();
    $que2="select b.*,d.`name` as dname,c.`name` as cname,c.`color` from `budget` b,`directory` d,`category` c where b.`item`=c.`slno` and b.`vendor`=d.`id` and b.`id`=?";
    $qaddsf=$this->query($que2,array($bid));
    $row=$qaddsf->fetch();
    
   
	
	$rest['cid']=$row['item'];
	$rest['did']=$row['vendor'];
	$rest['budget']=$row['budget'];
	$rest['actual']=$row['actual'];
	
	$total=0;
				   $budarr=explode("|",$row['amount']);
                                   $datearr=explode("|",$row['date']);
                                        foreach($budarr as $key1=>$value)
                                        {
                                        if($value!='')
                                        {
					$total+=$value;
					
					$rest['bdetail'][$key1]['pdate']=date('d / m / Y',strtotime($datearr[$key1]));
					$rest['bdetail'][$key1]['amount']=$value;
					}
					}
    
    return $rest;
}

function editbudget($bid,$category,$did,$budget,$actual,$amount,$date)
{
    $sql="UPDATE `budget` set item=?,vendor=?,budget=?,actual=?,amount=?,date=? where id=?";
    $stmt=$this->updatequery($sql,array($category,$did,$budget,$actual,$amount,$date,$bid));
    $affect=$stmt->rowCount();
    
    return $affect;
}

function deletbudget($bid)
{
    $que2="delete from `budget` where id=?";
    $qaddsf=$this->query($que2,array($bid));
    $affect=$qaddsf->rowCount();
    
    return $affect;
}

}
?>