<?php
require('kmvc_core.php');

//===============================================================
// Model/ORM
//===============================================================
class Model extends K_Model  {

  //Example of adding your own method to the core class
  function gethtmlsafe($key) {
    return htmlspecialchars($this->get($key));
  }

}

//===============================================================
// Controller
//===============================================================
class Controller extends K_Controller {

  public $input;

//Getting all the data request to the variable input.
//We can apply all the filter logic on these data as per our requirement
  function __construct(){

    	$this->input=new input;

  }
	
  //Example of overriding a core class method with your own
  function request_not_found($msg='') {
    $data['msg'] = 'hello how are you';
   View::do_dump(VIEW_PATH.'errors/404.php',$data);
    die(View::do_dump(VIEW_PATH.'layouts/mainlayout.php',$data));
  }

}

//===============================================================
// Router
//===============================================================
class Router extends K_Router {

  //Example of overriding a core class method with your own
  function request_not_found($msg='') {
    $data['msg'] = 'hello how are you';
   View::do_dump(VIEW_PATH.'errors/404.php',$data);
    //die(View::do_dump(VIEW_PATH.'layouts/mainlayout.php',$data));
  }

}

//===============================================================
// View
//===============================================================
class View extends K_View {

  //Example of overriding a constructor/method, add some code then pass control back to parent
  function __construct($file='',$vars='') {
    $file = VIEW_PATH.$file;
    return parent::__construct($file,$vars);
  }

}
