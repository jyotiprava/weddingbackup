<?php
class weddingappModel extends Model {

function device($deviceid)
{
    $rest=array();
    $que = "SELECT id from `device` where deviceid=?";
    $stmt1=$this->query($que,array($deviceid));
    $affected_rows = $stmt1->rowCount();
    $db=$this->getdbh();
    $rest['count']=$affected_rows;
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
	$que = "SELECT `name`,`email` from `guest_login` where id=?";
	$stmt=$this->query($que,array($res['guest_id']));
	$res1=$stmt->fetch(PDO::FETCH_ASSOC);
	$rest['deviceid']=$res['id'];
	$rest['name']=$res1['name'];
	$rest['email']=$res1['email'];
    }
    
    return $rest;
}
function addguest($name,$email,$password,$phone,$foodpref,$culture,$deviceid)
{
$pass=md5($password);
$que = "SELECT id from `guest_login` where email=?";
$stmt1=$this->query($que,array($email));
$affected_rows = $stmt1->rowCount();
 $db=$this->getdbh();

if($affected_rows==0)
{
$sql = "INSERT INTO `guest_login` set name=?,email=?,password=?,phone=?,food_pref=?,culture=?";
$stmt=$this->query($sql,array($name,$email,$pass,$phone,$foodpref,$culture));
$lastinsertid=$db->lastInsertId();

$affected_rowsval = $stmt->rowCount();

$sql="UPDATE device SET guest_id=? WHERE id=?";
$stmt=$this->updatequery($sql,array($lastinsertid,$deviceid));

return $affected_rowsval;
}

}


function login($username,$password,$deviceid)
{
    $pass=md5($password);
    $res=$this->query('select * from `guest_login` where `email`=? and `password`=?',array($username,$pass));
    $res1=$res->fetch(PDO::FETCH_ASSOC);
    
$sql="UPDATE device SET guest_id=? WHERE id=?";
$stmt=$this->updatequery($sql,array($res1['id'],$deviceid));

 $affected_rows['count'] = $res->rowCount();
 $affected_rows['name']=$res1['name'];
 return $affected_rows;
}

function guestdetail($email)
{
    $que="select * from `guest_login` where `email`=?";
      $stmt=$this->query($que,array($email));
      $res=$stmt->fetchAll(PDO::FETCH_ASSOC);
      return $res;
}

function editname($name,$email)
{
   $sql="UPDATE guest_login SET name=? WHERE email=?";
$stmt=$this->updatequery($sql,array($name,$email));
$affected_rows = $stmt->rowCount();
return $affected_rows;
   
}

function editpwd($cpwd,$email,$password)
{
   $pass=md5($cpwd);
   $que = "SELECT id from `guest_login` where email=? and password=?";
   $stmt1=$this->query($que,array($email,$pass));
   $count = $stmt1->rowCount();
   if($count==1)
   {
      $password=md5($password);
       $sql="UPDATE guest_login SET password=? WHERE email=?";
      $stmt=$this->updatequery($sql,array($password,$email));
      $affected_rows = $stmt->rowCount();
      return $affected_rows;
   }
   else
   {
      return 5;
   }
}

function editphone($phone,$email)
{
     $sql="UPDATE guest_login SET phone=? WHERE email=?";
      $stmt=$this->updatequery($sql,array($phone,$email));
      $affected_rows = $stmt->rowCount();
      return $affected_rows;
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

function allevent($email,$cal)
{
   $rest=array();
   $que="select * from `allguest` where `email`=?";
      $stmt=$this->query($que,array($email));
      $res=$stmt->fetchAll(PDO::FETCH_ASSOC);
      
      $status='';
      $statusimg='';
      $colour='';
      $date=date('Y-m-d');
      foreach($res as $key=>$row)
      {
         $owner=$row['owner'];
         if($cal=='upcoming')
         {
         $que1="SELECT m.`wedding_date`,d.`name`,p.`groom_name`,p.`bride_name`,m.`banquet`,a.`roomname`,s.`profilepic`,m.`user_id` FROM `directory` d,`personal_detail` p,`allroom` a,`my_template` m,`signup` s where m.`user_id`=? and m.`location`=d.`id` and m.`roomname`=a.`id` and p.`user_id`=m.`user_id` and s.`id`=m.`user_id` and m.`wedding_date` > ?";
         }
         else
         {
          $que1="SELECT m.`wedding_date`,d.`name`,p.`groom_name`,p.`bride_name`,m.`banquet`,a.`roomname`,s.`profilepic`,m.`user_id` FROM `directory` d,`personal_detail` p,`allroom` a,`my_template` m,`signup` s where m.`user_id`=? and m.`location`=d.`id` and m.`roomname`=a.`id` and p.`user_id`=m.`user_id` and s.`id`=m.`user_id` and m.`wedding_date` < ?";  
         }
	 $stmtt1 = $this->query($que1,array($owner,$date));
	 $count=$stmtt1->rowCount();
	 $res1=$stmtt1->fetch();
         
         if($row['attending']=='Yes')
         {
            $status='Accepted';
            $statusimg='images/icon2.png';
            $colour='#77ab59';
         }
         elseif($row['attending']=='May be')
         {
            $status='Pending';
            $statusimg='images/icon15.png';
            $colour='#F2D44E';
         }
         elseif($row['attending']=='No')
         {
            $status='Not Sent';
            $statusimg='images/icon3.png';
            $colour='#dd726b';
         }
         elseif($row['attending']=='Reject')
         {
            $status='Reject';
            $statusimg='images/icon3.png';
            $colour='#dd726b';
         }
         
	 if($count>0)
	 {
         $rest[$key]['user_id']=$res1['user_id'];
         $rest[$key]['date']=date('d F Y',strtotime($res1['wedding_date']));
         $rest[$key]['location']=$res1['name'];
         $rest[$key]['weddingname']=$res1['groom_name'].' & '.$res1['bride_name'];
         $rest[$key]['coupleimg']='http://weddingpenguin.sg/weddingnew/'.$res1['profilepic'];
         $rest[$key]['time']=$res1['banquet'];
         $rest[$key]['roomname']=$res1['roomname'];
	 $rest[$key]['redpacket']=$row['redpacket'];
         $rest[$key]['statusname']=$status;
         $rest[$key]['statusimg']=$statusimg;
         $rest[$key]['colour']=$colour;
	 }
         
      }
   
    return $rest;   
}

function eventdetail($owner,$email)
{
   $rest=array();
   $status='';
      $statusimg='';
      $colour='';
   $que1="SELECT m.`wedding_date`,d.`name`,d.`address`,p.`groom_name`,p.`bride_name`,m.`banquet`,a.`roomname`,s.`profilepic`,m.`user_id`,g.`attending`,g.`food_preference` FROM `directory` d,`personal_detail` p,`allroom` a,`my_template` m,`signup` s,`allguest` g where m.`user_id`=? and m.`location`=d.`id` and m.`roomname`=a.`id` and p.`user_id`=m.`user_id` and s.`id`=m.`user_id` and m.`user_id` = g.`owner` and g.`email`=? group by m.`user_id`";
   $stmtt1 = $this->query($que1,array($owner,$email));
   
   $res1=$stmtt1->fetch();
   
   $que2="select `food` from `food_pref` where `id`=?";
   $stmtt2 = $this->query($que2,array($res1['food_preference']));
   
   $res2=$stmtt2->fetch();
   
   if($res1['attending']=='Yes')
         {
            $status='Accepted';
            $statusimg='images/icon2.png';
            $colour='#77ab59';
         }
         elseif($res1['attending']=='May be')
         {
            $status='Pending';
            $statusimg='images/icon15.png';
            $colour='#F2D44E';
         }
         elseif($res1['attending']=='No')
         {
            $status='Not Sent';
            $statusimg='images/icon3.png';
            $colour='#dd726b';
         }
         elseif($res1['attending']=='Reject')
         {
            $status='Reject';
            $statusimg='images/icon3.png';
            $colour='#dd726b';
         }
   
   $rest['profilepic']='http://weddingpenguin.sg/weddingnew/'.$res1['profilepic'];
   $rest['time']=$res1['banquet'];
   $rest['weddingname']=$res1['groom_name'].' & '.$res1['bride_name'];
   $rest['location']=$res1['name'];
   $rest['roomname']=$res1['roomname'];
   $rest['date']=date('d F Y',strtotime($res1['wedding_date']));
   $rest['statusname']=$status;
   $rest['statusimg']=$statusimg;
   $rest['colour']=$colour;
    $rest['address']=$res1['address'];
    $rest['foodslno']=$res1['food_preference'];
    $rest['foodpref']=$res2['food'];
   
   return $rest; 
}

function qrcode($eventcode,$owner,$email)
{
   $que = "SELECT id from `my_template` where event_keycode=? and user_id=?";
   $stmt1=$this->query($que,array($eventcode,$owner));
   $count = $stmt1->rowCount();
   
   $que = "SELECT table_no from `allguest` where owner=? and email=?";
   $stmt1=$this->query($que,array($owner,$email));
   $res1=$stmt1->fetch();
   
   $affected_rows['count'] =$count;
   $affected_rows['tableno']=$res1['table_no'];
   return $affected_rows; 
}

function getfoodpref($email)
{
   $que = "SELECT food_pref from `guest_login` where email=?";
   $stmt1=$this->query($que,array($email));
   $res1=$stmt1->fetch();
   return $res1['food_pref'];
}

function allfoodpref($owner)
{
   $que = "SELECT * from `food_pref` where user_id=?";
   $stmt1=$this->query($que,array($owner));
   $res1=$stmt1->fetchAll(PDO::FETCH_ASSOC);
   return $res1;
}

function savedetail($owner,$email,$status,$foodpref,$remark)
{
   $sql="UPDATE allguest SET attending=?,food_preference=?,remark=? WHERE email=? and owner=?";
      $stmt=$this->updatequery($sql,array($status,$foodpref,$remark,$email,$owner));
      $affected_rows = $stmt->rowCount();
      return $affected_rows;
}

function insertredpacket($email,$amount,$owner)
{
   $sql="UPDATE allguest SET redpacket=? WHERE email=? and owner=?";
      $stmt=$this->updatequery($sql,array($amount,$email,$owner));
      $affected_rows = $stmt->rowCount();
      return $affected_rows;
}

function getamount($email,$owner)
{
   $que = "SELECT redpacket,applyforparking from `allguest` where email=? and owner=?";
   $stmt1=$this->query($que,array($email,$owner));
   $res1=$stmt1->fetch();
   
   $affected_rows['redpacket'] =$res1['redpacket'];
   $affected_rows['applyforparking']=$res1['applyforparking'];
   return $affected_rows;   
}

function applyparking($email,$owner)
{
   $sql="UPDATE allguest SET applyforparking=? WHERE email=? and owner=?";
      $stmt=$this->updatequery($sql,array(1,$email,$owner));
      $affected_rows = $stmt->rowCount();
      return $affected_rows;
}

function insertrequest($email,$request,$owner)
{
$sql = "INSERT INTO `allrequest` set owner=?,email=?,request=?";
$stmt=$this->query($sql,array($owner,$email,$request));

$affected_rowsval = $stmt->rowCount();
return $affected_rowsval;
}

function allphoto($owner)
{
  $rest=array();
   $que="select `id` from `add_folder` where `user_id`=? and `type`!=?";
      $stmt=$this->query($que,array($owner,'Locked'));
      $res=$stmt->fetchAll(PDO::FETCH_ASSOC);
      
      $i=0;
      foreach($res as $key=>$row)
      {
       
         $que1="select `pic`,`is_guest`,`id` from `album_pic` where `fid`=?";
         $stmt1=$this->query($que1,array($row['id']));
         
         $que2="select `name` from `signup` where `id`=?";
         $stmt2=$this->query($que2,array($owner));
         $res2=$stmt2->fetch();
	 $uploadby=$res2['name'];
         
         while($res1=$stmt1->fetch())
         {
             $i++;
	     
	     if($res1['is_guest']!=0)
	     {
		$que3="select `name` from `allguest` where `id`=?";
		$stmt3=$this->query($que3,array($res1['is_guest']));
		$res3=$stmt3->fetch();
		$uploadby=$res3['name'];
	     }
	     
         $rest[$i]['pic']='http://weddingpenguin.sg/weddingnew/'.$res1['pic'];
         $rest[$i]['uploadby']=$uploadby;
	  $rest[$i]['id']=$res1['id'];
         }
      }
   return $rest;
}

function forgotpwd($email)
{
   $key=md5($email.time());
   $random=rand(99,9999);
   $pwd=md5($random);
   $sql="UPDATE guest_login SET uniquekey=?,password=? WHERE email=?";
      $stmt=$this->updatequery($sql,array($key,$pwd,$email));
      
   $que = "SELECT uniquekey from `guest_login` where email=?";
$stmt1=$this->query($que,array($email));
$affected_row = $stmt1->rowCount();
$res1=$stmt1->fetch();
$affected_rows['count']=$affected_row;
$affected_rows['key']=$res1['uniquekey'];
$affected_rows['pwd']=$random;

return $affected_rows;
}

function upload($email,$owner,$image,$size)
{
   //$file='http://weddingpenguin.sg/weddingnew/upload/'.time().'.jpg';
   $file='/var/www/public_html/weddingpenguin.sg/weddingnew/upload/'.time().'.jpg';
   $file1='upload/'.time().'.jpg';
   $x=fopen($file,'w');
   fwrite($x,base64_decode($image));
   fclose($x);
   
$que = "SELECT id from `allguest` where email=? and owner=?";
$stmt1=$this->query($que,array($email,$owner));
$res1=$stmt1->fetch();

$que1 = "select a.id as fid,s.id as sfid from (select id from add_folder where user_id=? and foldername=?)a join sub_folder s on a.id=s.fid and s.name=?";
$stmt2=$this->query($que1,array($owner,'Wedding Day','Guest'));
$res2=$stmt2->fetch();
   
   $sql = "INSERT INTO `album_pic` set user_id=?,fid=?,sfid=?,pic=?,is_guest=?,size=?";
$stmt=$this->query($sql,array($owner,$res2['fid'],$res2['sfid'],$file1,$res1['id'],$size));

$affected_rowsval = $stmt->rowCount();
return $affected_rowsval;
}

function noofphoto($owner,$email)
{
    $que = "SELECT id from `allguest` where email=? and owner=?";
    $stmt1=$this->query($que,array($email,$owner));
    $res1=$stmt1->fetch();
    
    $que1 = "SELECT id from `album_pic` where is_guest=?";
    $stmt2=$this->query($que1,array($res1['id']));
    $res2=$stmt2->rowCount();
    
    $res['count']=$res2;
    $res['userid']=$res1['id'];
    
    return $res;
}

function ownphoto($userid)
{
    $i=0;
    $que1="select `pic`,`id` from `album_pic` where `is_guest`=?";
         $stmt1=$this->query($que1,array($userid));
	 while($res1=$stmt1->fetch())
         {
             $i++;
         $rest[$i]['pic']='http://weddingpenguin.sg/weddingnew/'.$res1['pic'];
         $rest[$i]['id']=$res1['id'];
         }
	 
     return $rest;
}

}
?>