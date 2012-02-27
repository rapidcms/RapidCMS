<?php
	class rapid_content {
		// we made this private to ensure that it gets sanitized.
		private $content;
		private $db;
		public $name;
		
		public function __construct($name) {
			$this->connect();
			$this->name = $this->clean($name);
			
			if (!$this->load()) {
				$this->create();
			}
		}
		
		private function connect() {
			$this->db = new mysqli(DBHOST, DBUSER, DBPASS, DBNAME);
		}
		
		public function show($tag='div') {
			GLOBAL $cms;
			GLOBAL $hooks;

			$show  = "<" . $tag . " class=\"block\" id=\"block_" . $this->name . "\">\n\t";
			$show .= $this->content;
			$show .= "</" . $tag . ">\n";
			echo $hooks->add_filter('show_content', $show);
		}
		
		private function load() {
			GLOBAL $cms;
			GLOBAL $hooks;

			$sql = "SELECT * FROM blocks WHERE name='" . $this->name . "';";
			$result = $this->db->query($sql);
			if ($result->num_rows > 0) {
				$row = $result->fetch_object();
				$this->content = $hooks->add_filter('load_content', $row->content);
				$result->close();
				return true;
			} else {
				$result->close();
				return false;
			}
		}
		
		private function create() {
			GLOBAL $cms;
			GLOBAL $hooks;

			$new_block = $hooks->add_filter('new_content', 'New Content Block.');
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
			GLOBAL $cms;
			GLOBAL $hooks;

			$string = trim($string);
			$string = $this->db->real_escape_string($string);
			$string = $hooks->add_filter('clean_content', $string);
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