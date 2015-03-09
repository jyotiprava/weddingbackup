<?php

class input {

public $post;
public $get;
public $request;
public $session;

	function __construct(){

		foreach($_POST as $key => $value)
		$this->post[$key]=$value;


		foreach($_GET as $key => $value)
		$this->get[$key]=$value;



		foreach($_REQUEST as $key => $value)
		$this->request[$key]=$value;

		foreach($_SESSION as $key => $value)
		$this->session[$key]=$value;
		

	}

}



?>
