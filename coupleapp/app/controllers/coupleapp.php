<?php
header('Content-type: application/javascript');
//$val=$_GET['jsoncallback'];
//echo $val;
class coupleapp extends Controller{
 function device()
 {
  $this->addcouple = new coupleappModel;
  $res=$this->addcouple->device($this->input->request['deviceid']);
  
 
  $data['count']=$res['count'];
  $data['deviceid']=$res['deviceid'];
  $data['name']=$res['name'];
  $data['email']=$res['email'];
  $data['jsonval']=$_GET['jsoncallback'];
 View::do_dump(VIEW_PATH.'coupleapp/device.php',$data); 
 }
 
 function couplesignin()
 {
  $this->addcouple = new coupleappModel;
  $res=$this->addcouple->addcouple($this->input->request['fname'],$this->input->request['email'],$this->input->request['password'],$this->input->request['eventtype'],$this->input->request['eventname'],$this->input->request['weddingdate'],$this->input->request['acstatus'],$this->input->request['groomname'],$this->input->request['groomemail'],$this->input->request['groomphone'],$this->input->request['bridename'],$this->input->request['brideemail'],$this->input->request['bridephone'],$this->input->request['imageData']);
  
 
  $data['row']=$res;
  //$data['name']=$name;
  //$data['email']=$this->input->request['email'];
  //$data['jsonval']=$_GET['jsoncallback'];
 View::do_dump(VIEW_PATH.'coupleapp/upload.php',$data); 
  
 }
 
 
 function couplelogin()
 {
  $this->addcouple = new coupleappModel;
  
   if(isset($this->input->request['email']) && isset($this->input->request['password'])){

	   $res=$this->addcouple->login($this->input->request['email'],$this->input->request['password']);
           $data['row']=$res['count'];
           $data['name']=$res['name'];
	   $data['userid']=$res['userid'];
	   $data['type']=$res['type'];
            $data['jsonval']=$_GET['jsoncallback'];
	 View::do_dump(VIEW_PATH.'coupleapp/login.php',$data); 	
 	}
 }
 
 function forgotpwd()
 {
  $this->addcouple = new coupleappModel;
  $res=$this->addcouple->forgotpwd($this->input->request['email']);
  
  $data['row']=$res['count'];
  $data['key']=$res['key'];
  $data['pwd']=$res['pwd'];
  $data['email']=$this->input->request['email'];
  $data['jsonval']=$_GET['jsoncallback'];
 View::do_dump(VIEW_PATH.'coupleapp/forgotpwd.php',$data); 
 }
 
 function coupledetail()
 {
   $this->addcouple = new coupleappModel;
   $res=$this->addcouple->coupledetail($this->input->request['userid']);
  $data['row']=$res;
  $data['jsonval']=$_GET['jsoncallback'];
 View::do_dump(VIEW_PATH.'coupleapp/guestdetail.php',$data); 	
 }
 
 function editname()
 {
  $this->addcouple = new coupleappModel;
  $res=$this->addcouple->editname($this->input->request['name'],$this->input->request['userid'],$this->input->request['type']);
  $data['row']=$res;
  $data['jsonval']=$_GET['jsoncallback'];
 View::do_dump(VIEW_PATH.'coupleapp/editname.php',$data); 
  
 }
 
 function editpwd()
 {
  $this->addcouple = new coupleappModel;
  $res=$this->addcouple->editpwd($this->input->request['cpwd'],$this->input->request['userid'],$this->input->request['password'],$this->input->request['type']);
  $data['row']=$res;
  $data['jsonval']=$_GET['jsoncallback'];
 View::do_dump(VIEW_PATH.'coupleapp/editpwd.php',$data); 
  
 }
 
 function sendqrcode()
 {
  $this->addcouple = new coupleappModel;
  $res=$this->addcouple->sendqrcode($this->input->request['email_send'],$this->input->request['userid']);
  $data['row']=$res;
  $data['jsonval']=$_GET['jsoncallback'];
 View::do_dump(VIEW_PATH.'coupleapp/editname.php',$data);
 
 }
 
 function imageupload()
 {
  $this->addcouple = new coupleappModel;
  $res=$this->addcouple->imageupload($this->input->request['userid'],$this->input->request['imageData']);
  
 
  $data['row']=$res;
 
 View::do_dump(VIEW_PATH.'coupleapp/upload.php',$data); 
 }
 
 function coupleupdate()
 {
   $this->addcouple = new coupleappModel;
    if(!isset($this->input->request['solemndate_edit']) && !isset($this->input->request['customedate_edit']) && !isset($this->input->request['weddingdate_edit'])){
     $this->input->request['solemndate_edit']='';
     $this->input->request['customedate_edit']='';
     $this->input->request['weddingdate_edit']='';
    }
  $res=$this->addcouple->coupleupdate($this->input->request['event_name_edit'],$this->input->request['weddingdate_edit'],$this->input->request['solemndate_edit'],$this->input->request['customs_edit'],$this->input->request['customedate_edit'],$this->input->request['wedding_style'],$this->input->request['firstedit'],$this->input->request['userid']);
  $data['row']=$res;
  $data['jsonval']=$_GET['jsoncallback'];
 View::do_dump(VIEW_PATH.'coupleapp/editname.php',$data); 
 }
 
 function alltodo()
 {
  $this->addcouple = new coupleappModel;
  $res=$this->addcouple->alltodo($this->input->request['userid']);
  $data['row']=$res;
  $data['jsonval']=$_GET['jsoncallback'];
 View::do_dump(VIEW_PATH.'coupleapp/guestdetail.php',$data);
 }
 
 function getfav()
 {
  $this->addcouple = new coupleappModel;
  $res=$this->addcouple->getfav($this->input->request['todoid']);
  $data['row']=$res;
  $data['jsonval']=$_GET['jsoncallback'];
 View::do_dump(VIEW_PATH.'coupleapp/editname.php',$data);
 }
 
 function alllevel()
 {
  $this->addcouple = new coupleappModel;
  $res=$this->addcouple->alllevel($this->input->request['userid']);
  $data['row']=$res;
  $data['jsonval']=$_GET['jsoncallback'];
 View::do_dump(VIEW_PATH.'coupleapp/guestdetail.php',$data);
 }
 
 function addlevel()
 {
  $this->addcouple = new coupleappModel;
  $res=$this->addcouple->addlevel($this->input->request['labelname'],$this->input->request['labelcolor'],$this->input->request['userid']);
  $data['count']=$res['count'];
  $data['lid']=$res['lid'];
  $data['jsonval']=$_GET['jsoncallback'];
 View::do_dump(VIEW_PATH.'coupleapp/addlevel.php',$data);
 }
 
 function leveledit()
 {
  $this->addcouple = new coupleappModel;
  $res=$this->addcouple->leveledit($this->input->request['lid']);
  $data['row']=$res;
  $data['jsonval']=$_GET['jsoncallback'];
 View::do_dump(VIEW_PATH.'coupleapp/guestdetail.php',$data);
 }
 
 function leveldelete()
 {
   $this->addcouple = new coupleappModel;
  $res=$this->addcouple->leveldelete($this->input->request['lid']);
  $data['row']=$res;
  $data['jsonval']=$_GET['jsoncallback'];
 View::do_dump(VIEW_PATH.'coupleapp/editname.php',$data);
 }
 
 function updatelevel()
 {
   $this->addcouple = new coupleappModel;
  $res=$this->addcouple->updatelevel($this->input->request['labelname'],$this->input->request['labelcolor'],$this->input->request['lid']);
  $data['count']=$res['count'];
  $data['lid']=$res['lid'];
  $data['jsonval']=$_GET['jsoncallback'];
 View::do_dump(VIEW_PATH.'coupleapp/addlevel.php',$data);
 }
 
 function addtodolist()
 {
  $this->addcouple = new coupleappModel;
  $res=$this->addcouple->addtodolist($this->input->request['todolist'],$this->input->request['lid'],$this->input->request['tododate'],$this->input->request['subtitle'],$this->input->request['todofav'],$this->input->request['userid']);
  $data['row']=$res;
  $data['jsonval']=$_GET['jsoncallback'];
 View::do_dump(VIEW_PATH.'coupleapp/editname.php',$data);
 }
 
 function todoedit()
 {
  $this->addcouple = new coupleappModel;
  $res=$this->addcouple->todoedit($this->input->request['tid']);
  $data['row']=$res;
  $data['jsonval']=$_GET['jsoncallback'];
 View::do_dump(VIEW_PATH.'coupleapp/guestdetail.php',$data);
 }
 
 function subtitledelete()
 {
   $this->addcouple = new coupleappModel;
  $res=$this->addcouple->subtitledelete($this->input->request['sid'],$this->input->request['tid']);
  $data['row']=$res;
  $data['jsonval']=$_GET['jsoncallback'];
 View::do_dump(VIEW_PATH.'coupleapp/editname.php',$data);
 }
 
 function edittodolist()
 {
  $this->addcouple = new coupleappModel;
  $res=$this->addcouple->edittodolist($this->input->request['todolist'],$this->input->request['lid'],$this->input->request['tododate'],$this->input->request['subtitle'],$this->input->request['todofav'],$this->input->request['tid']);
  $data['row']=$res;
 
 View::do_dump(VIEW_PATH.'coupleapp/upload.php',$data);
 }
 
 function todolistdelete()
 {
  $this->addcouple = new coupleappModel;
  $res=$this->addcouple->todolistdelete($this->input->request['tid']);
  $data['row']=$res;
  $data['jsonval']=$_GET['jsoncallback'];
 View::do_dump(VIEW_PATH.'coupleapp/editname.php',$data);
 }
 
 function gettodo()
 {
   $this->addcouple = new coupleappModel;
  $res=$this->addcouple->gettodo($this->input->request['lid'],$this->input->request['userid']);
  $data['row']=$res;
  $data['jsonval']=$_GET['jsoncallback'];
 View::do_dump(VIEW_PATH.'coupleapp/guestdetail.php',$data);
 
 }
 
 function allcategory()
 {
  $this->addcouple = new coupleappModel;
  $res=$this->addcouple->allcategory($this->input->request['userid']);
  $data['row']=$res;
  $data['jsonval']=$_GET['jsoncallback'];
 View::do_dump(VIEW_PATH.'coupleapp/guestdetail.php',$data);
 }
 
 function alldirectory()
 {
  $this->addcouple = new coupleappModel;
  $res=$this->addcouple->alldirectory($this->input->request['userid']);
  $data['row']=$res;
  $data['jsonval']=$_GET['jsoncallback'];
 View::do_dump(VIEW_PATH.'coupleapp/guestdetail.php',$data);
 }
 
 function getsh()
 {
   $this->addcouple = new coupleappModel;
  $res=$this->addcouple->getsh($this->input->request['did'],$this->input->request['userid']);
  $data['row']=$res;
  $data['jsonval']=$_GET['jsoncallback'];
 View::do_dump(VIEW_PATH.'coupleapp/editname.php',$data);
 }
 
 function getvenue()
 {
  $this->addcouple = new coupleappModel;
  $res=$this->addcouple->getvenue($this->input->request['userid'],$this->input->request['cid']);
  $data['row']=$res;
  $data['jsonval']=$_GET['jsoncallback'];
 View::do_dump(VIEW_PATH.'coupleapp/guestdetail.php',$data);
 }
 
 function venueserch()
 {
  $this->addcouple = new coupleappModel;
  $res=$this->addcouple->venueserch($this->input->request['userid'],$this->input->request['dirname'],$this->input->request['cid']);
  $data['row']=$res;
  $data['jsonval']=$_GET['jsoncallback'];
 View::do_dump(VIEW_PATH.'coupleapp/guestdetail.php',$data);
 }
 
 function getfilter()
 {
   $this->addcouple = new coupleappModel;
  $res=$this->addcouple->getfilter($this->input->request['cid']);
  $data['row']=$res;
  $data['jsonval']=$_GET['jsoncallback'];
 View::do_dump(VIEW_PATH.'coupleapp/guestdetail.php',$data);
 }
 
 function filtersrch()
 {
  $this->addcouple = new coupleappModel;
  $res=$this->addcouple->filtersrch($this->input->request['userid'],$this->input->request['filterid'],$this->input->request['cid']);
  $data['row']=$res;
  $data['jsonval']=$_GET['jsoncallback'];
 View::do_dump(VIEW_PATH.'coupleapp/guestdetail.php',$data);
 }
 
 function directorydetail()
 {
   $this->addcouple = new coupleappModel;
  $res=$this->addcouple->directorydetail($this->input->request['userid'],$this->input->request['did']);
  $data['row']=$res;
  $data['jsonval']=$_GET['jsoncallback'];
 View::do_dump(VIEW_PATH.'coupleapp/guestdetail.php',$data);
 }
 
 function addrev()
 {
  $this->addcouple = new coupleappModel;
  $res=$this->addcouple->addrev($this->input->request['did'],$this->input->request['userid'],$this->input->request['review_text'],$this->input->request['food'],$this->input->request['service'],$this->input->request['price']);
  $data['row']=$res;
  $data['jsonval']=$_GET['jsoncallback'];
 View::do_dump(VIEW_PATH.'coupleapp/editname.php',$data);
 }
 
 function allguest()
 {
  $this->addcouple = new coupleappModel;
  $res=$this->addcouple->allguest($this->input->request['userid']);
  $data['row']=$res;
  $data['jsonval']=$_GET['jsoncallback'];
 View::do_dump(VIEW_PATH.'coupleapp/guestdetail.php',$data);
 }
 
 function guestinfo()
 {
  $this->addcouple = new coupleappModel;
  $res=$this->addcouple->guestinfo($this->input->request['userid']);
  $data['row']=$res;
  $data['jsonval']=$_GET['jsoncallback'];
 View::do_dump(VIEW_PATH.'coupleapp/guestdetail.php',$data);
 }
 
 function addsetting()
 {
   $this->addcouple = new coupleappModel;
  $res=$this->addcouple->addsetting($this->input->request['userid'],$this->input->request['totaltable'],$this->input->request['totalseat']);
  $data['row']=$res;
  $data['jsonval']=$_GET['jsoncallback'];
 View::do_dump(VIEW_PATH.'coupleapp/editname.php',$data);
 }
 
 function addrelation()
 {
  $this->addcouple = new coupleappModel;
  $res=$this->addcouple->addrelation($this->input->request['userid'],$this->input->request['relation']);
  $data['row']=$res['count'];
  $data['name']=$res['id'];
  $data['jsonval']=$_GET['jsoncallback'];
 View::do_dump(VIEW_PATH.'coupleapp/getamount.php',$data);
 }
 
 function allrelation()
 {
  $this->addcouple = new coupleappModel;
  $res=$this->addcouple->allrelation($this->input->request['userid']);
  $data['row']=$res;
  $data['jsonval']=$_GET['jsoncallback'];
 View::do_dump(VIEW_PATH.'coupleapp/guestdetail.php',$data);
 }
 
 function addfoodpref()
 {
   $this->addcouple = new coupleappModel;
  $res=$this->addcouple->addfoodpref($this->input->request['userid'],$this->input->request['foodpref']);
  $data['row']=$res['count'];
  $data['name']=$res['id'];
  $data['jsonval']=$_GET['jsoncallback'];
 View::do_dump(VIEW_PATH.'coupleapp/getamount.php',$data);
 }
 
 function allfoodpref()
 {
   $this->addcouple = new coupleappModel;
  $res=$this->addcouple->allfoodpref($this->input->request['userid']);
  $data['row']=$res;
  $data['jsonval']=$_GET['jsoncallback'];
  View::do_dump(VIEW_PATH.'coupleapp/guestdetail.php',$data);
 }
 
 function editrelation()
 {
   $this->addcouple = new coupleappModel;
  $res=$this->addcouple->editrelation($this->input->request['rid']);
  $data['row']=$res;
  $data['jsonval']=$_GET['jsoncallback'];
 View::do_dump(VIEW_PATH.'coupleapp/guestdetail.php',$data);
 }
 
 function updaterelation()
 {
   $this->addcouple = new coupleappModel;
  $res=$this->addcouple->updaterelation($this->input->request['rid'],$this->input->request['relation']);
  $data['row']=$res;
  $data['jsonval']=$_GET['jsoncallback'];
 View::do_dump(VIEW_PATH.'coupleapp/editname.php',$data);
 }
 
 function deleterelation()
 {
  $this->addcouple = new coupleappModel;
  $res=$this->addcouple->deleterelation($this->input->request['rid']);
  $data['row']=$res;
  $data['jsonval']=$_GET['jsoncallback'];
 View::do_dump(VIEW_PATH.'coupleapp/editname.php',$data);
 }
 
 function editfoodpref()
 {
   $this->addcouple = new coupleappModel;
  $res=$this->addcouple->editfoodpref($this->input->request['fid']);
  $data['row']=$res;
  $data['jsonval']=$_GET['jsoncallback'];
 View::do_dump(VIEW_PATH.'coupleapp/guestdetail.php',$data);
 }
 
 function updatefoodpref()
 {
  $this->addcouple = new coupleappModel;
  $res=$this->addcouple->updatefoodpref($this->input->request['fid'],$this->input->request['food']);
  $data['row']=$res;
  $data['jsonval']=$_GET['jsoncallback'];
 View::do_dump(VIEW_PATH.'coupleapp/editname.php',$data);
 }
 
 function deletefoodpref()
 {
  $this->addcouple = new coupleappModel;
  $res=$this->addcouple->deletefoodpref($this->input->request['fid']);
  $data['row']=$res;
  $data['jsonval']=$_GET['jsoncallback'];
 View::do_dump(VIEW_PATH.'coupleapp/editname.php',$data);
 }
 
 function addguest()
 {
   $this->addcouple = new coupleappModel;
  $res=$this->addcouple->addguest($this->input->request['userid'],$this->input->request['guestname'],$this->input->request['grelation'],$this->input->request['gtable'],$this->input->request['gfoodpref'],$this->input->request['email'],$this->input->request['gphone'],$this->input->request['gstatus']);
  $data['row']=$res;
  $data['jsonval']=$_GET['jsoncallback'];
 View::do_dump(VIEW_PATH.'coupleapp/editname.php',$data);
 }
 
 function guestdetail()
 {
   $this->addcouple = new coupleappModel;
  $res=$this->addcouple->guestdetail($this->input->request['gid']);
  $data['row']=$res;
  $data['jsonval']=$_GET['jsoncallback'];
 View::do_dump(VIEW_PATH.'coupleapp/guestdetail.php',$data);
 }
 
 function updateguest()
 {
   $this->addcouple = new coupleappModel;
  $res=$this->addcouple->updateguest($this->input->request['gid'],$this->input->request['guestname'],$this->input->request['grelation'],$this->input->request['gtable'],$this->input->request['gfoodpref'],$this->input->request['email'],$this->input->request['gphone'],$this->input->request['gstatus']);
  $data['row']=$res;
  $data['jsonval']=$_GET['jsoncallback'];
 View::do_dump(VIEW_PATH.'coupleapp/editname.php',$data);
 }
 
 function deleteguest()
 {
  $this->addcouple = new coupleappModel;
  $res=$this->addcouple->deleteguest($this->input->request['gid']);
  $data['row']=$res;
  $data['jsonval']=$_GET['jsoncallback'];
 View::do_dump(VIEW_PATH.'coupleapp/editname.php',$data);
 }
 
 function gettypedetail()
 {
  $this->addcouple = new coupleappModel;
  $res=$this->addcouple->gettypedetail($this->input->request['userid'],$this->input->request['type']);
  $data['row']=$res;
  $data['jsonval']=$_GET['jsoncallback'];
 View::do_dump(VIEW_PATH.'coupleapp/guestdetail.php',$data);
 }
 
 function guestsearch()
 {
  $this->addcouple = new coupleappModel;
  $res=$this->addcouple->guestsearch($this->input->request['userid'],$this->input->request['value']);
  $data['row']=$res;
  $data['jsonval']=$_GET['jsoncallback'];
 View::do_dump(VIEW_PATH.'coupleapp/guestdetail.php',$data);
 }
 
 function allfolder()
 {
   $this->addcouple = new coupleappModel;
  $res=$this->addcouple->allfolder($this->input->request['userid']);
  $data['row']=$res;
  $data['jsonval']=$_GET['jsoncallback'];
  View::do_dump(VIEW_PATH.'coupleapp/guestdetail.php',$data);
 }
 
 function addfolder()
 {
  $this->addcouple = new coupleappModel;
  $res=$this->addcouple->addfolder($this->input->request['userid'],$this->input->request['folder'],$this->input->request['type']);
  $data['row']=$res['count'];
  $data['name']=$res['id'];
  $data['jsonval']=$_GET['jsoncallback'];
 View::do_dump(VIEW_PATH.'coupleapp/getamount.php',$data);
 }
 
 function editfolder()
 {
   $this->addcouple = new coupleappModel;
  $res=$this->addcouple->editfolder($this->input->request['fid']);
  $data['row']=$res;
  $data['jsonval']=$_GET['jsoncallback'];
 View::do_dump(VIEW_PATH.'coupleapp/guestdetail.php',$data);
 }
 
 function updatefolder()
 {
   $this->addcouple = new coupleappModel;
  $res=$this->addcouple->updatefolder($this->input->request['edit_folder'],$this->input->request['subfolder'],$this->input->request['userid'],$this->input->request['fid'],$this->input->request['sfid']);
  $data['row']=$res;
  $data['jsonval']=$_GET['jsoncallback'];
 View::do_dump(VIEW_PATH.'coupleapp/editname.php',$data);
 }
 
 function deletegfolder()
 {
  $this->addcouple = new coupleappModel;
  $res=$this->addcouple->deletegfolder($this->input->request['fid']);
  $data['row']=$res;
  $data['jsonval']=$_GET['jsoncallback'];
 View::do_dump(VIEW_PATH.'coupleapp/editname.php',$data);
 }
 
 function deletegsfolder()
 {
  $this->addcouple = new coupleappModel;
  $res=$this->addcouple->deletegsfolder($this->input->request['fid'],$this->input->request['sfid']);
  $data['row']=$res;
  $data['jsonval']=$_GET['jsoncallback'];
 View::do_dump(VIEW_PATH.'coupleapp/editname.php',$data);
 }
 
 function redpacket()
 {
  $this->addguest = new coupleappModel;
  $res=$this->addguest->redpacket();
  $data['row']=$res;
  $data['jsonval']=$_GET['jsoncallback'];
 View::do_dump(VIEW_PATH.'coupleapp/guestdetail.php',$data);
 }
 
 function allpic()
 {
   $this->addcouple = new coupleappModel;
  $res=$this->addcouple->allpic($this->input->request['userid'],$this->input->request['fid']);
  $data['row']=$res;
  $data['jsonval']=$_GET['jsoncallback'];
  View::do_dump(VIEW_PATH.'coupleapp/guestdetail.php',$data);
 }
 
 function folderdetail()
 {
   $this->addcouple = new coupleappModel;
  $res=$this->addcouple->folderdetail($this->input->request['userid'],$this->input->request['fid']);
  $data['row']=$res;
  $data['jsonval']=$_GET['jsoncallback'];
  View::do_dump(VIEW_PATH.'coupleapp/guestdetail.php',$data);
 }
 
 function upload()
 {
  $this->addcouple = new coupleappModel;
  $res=$this->addcouple->upload($this->input->request['userid'],$this->input->request['fid'],$this->input->request['image'],$this->input->request['bytes']);
  
  $data['row']=$res;
  View::do_dump(VIEW_PATH.'coupleapp/upload.php',$data);
 }
 
 function allpic1()
 {
   $this->addcouple = new coupleappModel;
  $res=$this->addcouple->allpic1($this->input->request['userid'],$this->input->request['sfid']);
  $data['row']=$res;
  $data['jsonval']=$_GET['jsoncallback'];
  View::do_dump(VIEW_PATH.'coupleapp/guestdetail.php',$data);
 }
 
 function upload1()
 {
  $this->addcouple = new coupleappModel;
  $res=$this->addcouple->upload1($this->input->request['userid'],$this->input->request['fid'],$this->input->request['sfid'],$this->input->request['image'],$this->input->request['bytes']);
  
  $data['row']=$res;
  View::do_dump(VIEW_PATH.'coupleapp/upload.php',$data);
 }
 
  function searchhotel()
 {
  $this->addcouple = new coupleappModel;
  $res=$this->addcouple->searchhotel($this->input->request['hotelname']);
  $data['row']=$res;
  $data['jsonval']=$_GET['jsoncallback'];
 View::do_dump(VIEW_PATH.'coupleapp/guestdetail.php',$data);
 }
 
 function storagedetail()
 {
  $this->addcouple = new coupleappModel;
  $res=$this->addcouple->storagedetail($this->input->request['userid']);
  $data['row']=$res['totalsize'];
  $data['name']=$res['percuse'];
  $data['jsonval']=$_GET['jsoncallback'];
 View::do_dump(VIEW_PATH.'coupleapp/getamount.php',$data);
 }
 
 function budgethistory()
 {
  $this->addcouple = new coupleappModel;
  $res=$this->addcouple->budgethistory($this->input->request['userid']);
  $data['row']=$res;
  $data['jsonval']=$_GET['jsoncallback'];
 View::do_dump(VIEW_PATH.'coupleapp/guestdetail.php',$data);
 }
 
 function allbudget()
 {
  $this->addcouple = new coupleappModel;
  $res=$this->addcouple->allbudget($this->input->request['userid']);
  $data['row']=$res;
  $data['jsonval']=$_GET['jsoncallback'];
 View::do_dump(VIEW_PATH.'coupleapp/guestdetail.php',$data);
 }
 
 function getdir()
 {
  $this->addcouple = new coupleappModel;
  $res=$this->addcouple->getdir($this->input->request['cid']);
  $data['row']=$res;
  $data['jsonval']=$_GET['jsoncallback'];
 View::do_dump(VIEW_PATH.'coupleapp/guestdetail.php',$data);
 }
 
 function addbudget()
 {
  $this->addcouple = new coupleappModel;
  $res=$this->addcouple->addbudget($this->input->request['userid'],$this->input->request['category'],$this->input->request['directory'],$this->input->request['budget'],$this->input->request['actual'],$this->input->request['paid'],$this->input->request['pdate']);
  $data['row']=$res;
  $data['jsonval']=$_GET['jsoncallback'];
 View::do_dump(VIEW_PATH.'coupleapp/editname.php',$data);
 }
 
 function getbudgetdetail()
 {
  $this->addcouple = new coupleappModel;
  $res=$this->addcouple->getbudgetdetail($this->input->request['bid']);
  $data['row']=$res;
  $data['jsonval']=$_GET['jsoncallback'];
 View::do_dump(VIEW_PATH.'coupleapp/guestdetail.php',$data);
 }
 
 function editbudget()
 {
  $this->addcouple = new coupleappModel;
  $res=$this->addcouple->editbudget($this->input->request['bid'],$this->input->request['category'],$this->input->request['directory'],$this->input->request['budget'],$this->input->request['actual'],$this->input->request['paid'],$this->input->request['pdate']);
  $data['row']=$res;
  $data['jsonval']=$_GET['jsoncallback'];
 View::do_dump(VIEW_PATH.'coupleapp/editname.php',$data);
 }
 
 function deletbudget($bid)
 {
  $this->addcouple = new coupleappModel;
  $res=$this->addcouple->deletbudget($this->input->request['bid']);
  $data['row']=$res;
  $data['jsonval']=$_GET['jsoncallback'];
 View::do_dump(VIEW_PATH.'coupleapp/editname.php',$data);
 }
 
 
}
?>
