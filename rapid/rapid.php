<?php 
	session_start();
	include_once("includes/config.php");
	include_once("includes/class.hooks.php");
	include_once("includes/class.user.php");
	
	class rapid	{
		public $page;
		public $blocks;
		public $hooks;
		public $user;
		
		public function __construct() {
			$this->hooks = new hooks;
			$this->user = new user;
		}
		
		public function content($name) {
			
			$this->load($name);
			$this->blocks[$name]->show();
		}
		
		public function load($name) {
			$this->blocks[$name] = new rapid_content($name, $this->hooks);
		}
		
		public function load_all() {
			// TODO: load all the blocks using $this->load
		}
	}

	class rapid_content {
		// we made this private to ensure that it gets sanitized.
		private $content;
		private $db;
		public $name;
		public $hooks;
		
		public function __construct($name, $hooks) {
			$this->hooks = $hooks;
			$this->connect();
			$this->name = $this->clean($name);
			
			if (!$this->load()) {
				$this->create();
			}
		}
		
		private function connect() {
			$this->db = new mysqli(DBHOST, DBUSER, DBPASS, DBNAME);
		}
		
		public function show() {
			$show  = "<div class=\"block\" id=\"block_" . $this->name . "\">\n\t";
			$show .= $this->content;
			$show .= "</div>\n";
			echo $this->hooks->add_filter('show_content', $show);
		}
		
		private function load() {
			$sql = "SELECT * FROM blocks WHERE name='" . $this->name . "';";
			$result = $this->db->query($sql);
			if ($result->num_rows > 0) {
				$row = $result->fetch_object();
				$this->content = $this->hooks->add_filter('load_content', $row->content);
				$result->close();
				return true;
			} else {
				$result->close();
				return false;
			}
		}
		
		private function create() {
			$new_block = $this->hooks->add_filter('new_content', 'New Content Block.');
			$sql = "INSERT INTO blocks SET name='" . $this->name . "', content='$new_block';";
			$result = $this->db->query($sql);
			if ($result) {
				$this->content = $new_block;
				return true;
			} else {
				return false;
			}
		}
		
		private function clean($string) {
			$string = trim($string);
			$string = $this->db->real_escape_string($string);
			$string = $this->hooks->add_filter('clean_content', $string);
			return $string;
		}
		
		public function update($new_content='') {
			
			if ($new_content <> "") {
				$this->content = $this->clean($new_content);
			}
			$sql = "UPDATE blocks SET content='" . $this->content . "' WHERE name='" . $this->name . "';";
			$result = $this->db->query($sql);
			if ($result) {
				return true;
			} else {
				return false;
			}
		}
		
		public function delete() {
			// TODO: write the delete sql.
			$sql = "DELETE FROM blocks WHERE name='" . $this->name . "';";
			$result = $this->db->query($sql);
			if ($result) {
				return true;
			} else {
				return false;
			}
		}
		
		public function get_content() {
			return $this->content;
		}
	}

	class rapid_page {
		public $name;
		
		public function __construct() {
		}
		
		private function get_pagename() {
		}
		
	}
	
	$cms = new rapid;
	
	function head() {
		global $cms;
		if (empty($hooks)) {
			$hooks = new hooks;
		}		
		if (!isset($cms)) {
			$cms = new rapid;
		}
		include("js/init.php");
		$cms->hooks->add_action('head');
		/*
		echo "<pre>";
		var_dump($hooks);
		var_dump($cms);
		echo "</pre>";
		*/
	}

	function logged_in () {
		if ($_SESSION['rapid_uid'] <> 0) {
			return true;
		} else {
			return false;
		}
	}
?>