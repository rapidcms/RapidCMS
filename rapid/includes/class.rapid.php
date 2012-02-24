<?php

	require_once("class.user.php");
	require_once("class.rapid_content.php");

	class rapid	{
		public $page;
		public $blocks;
		public $user;
		
		public function __construct() {
			$this->user = new user;
		}
		
		public function content($name) {
			$this->load($name);
			$this->blocks[$name]->show();
		}
		
		public function load($name) {
			$this->blocks[$name] = new rapid_content($name);
		}
		
		public function load_all() {
			// TODO: load all the blocks using $this->load
		}
	}
?>