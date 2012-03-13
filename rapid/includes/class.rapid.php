<?php
	session_start();
	
	require_once("class.user.php");
	require_once("class.rapid_content.php");

	class rapid	{
		public $page;
		public $blocks;
		public $user;
		
		public function __construct() {
			$this->user = new user;
		}
		
		public function content($name, $tag='div', $default="<h1>New Content Block.</h1>") {
			$this->load($name, $default);
			$this->blocks[$name]->show($tag);
		}
		
		public function load($name, $default='') {
			$this->blocks[$name] = new rapid_content($name, $default);
		}
		
		public function load_all() {
			// TODO: load all the blocks using $this->load
		}
	}
?>