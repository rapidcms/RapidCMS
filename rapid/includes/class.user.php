<?php
require_once('config.php');

class user {
	private $db;

	public $id;
	public $username;
	public $password;
	public $unencrpted_password;
	public $role;
	public $error;
	
	public function __construct() {
		$this->connect();
	}

	private function connect() {
		$this->db = new mysqli(DBHOST, DBUSER, DBPASS, DBNAME);
	}
	private function sanitize($dirty) {
		return $this->db->real_escape_string($dirty);
	}
	
	public function load($name=false) {

		if ($name) {
			$sql = "SELECT * FROM " . DBPREFIX . "users where name='" . $this->sanitize($this->username) . "';";
		} else	{
			$sql = "SELECT * FROM " . DBPREFIX . "users where id='" . $this->sanitize($this->id) . "';";
		}

		$result = $this->db->query($sql);		

		if ($result->num_rows > 0) {	
			$row = $result->fetch_object();
			$this->id = $row->id;
			$this->role = $row->role;
			$this->username = $row->name;
			$this->password = $row->pass;
			return true;
		} else {
			$this->error[] = "Invalid username or password";
			return false;
		}
	}
	
	public function authenticate() {
		$this->db = new mysqli(DBHOST, DBUSER, DBPASS, DBNAME);

		if ($this->username == "") {
			$this->error[] = "Cannot log in: Username cannot be blank.";
			return false;
		}
		
		if ($this->unencrypted_password == "") {
			$this->error[] = "Cannot log in: Password cannot be blank.";
			return false;
		}

		$result = $this->db->query("SELECT * FROM " . DBPREFIX . "users where name='" . $this->sanitize($this->username) . "' AND pass='" . md5($this->sanitize($this->unencrypted_password)) . "';");

		if ($result->num_rows > 0) {	
			$row = $result->fetch_object();
			$this->id = $row->id;
			$this->username = $row->name;
			$this->password = $row->pass;
			$this->role = $row->role;
			
			return true;
		} else {
			$this->error[] = "Invalid username or password";
			return false;
		}
	}
	
	public function save() {
		if ($this->username == "") {
			$this->error[] = "Cannot create user: no user specified";
			return false;
		}
		
		if ($this->unencrypted_password == "") {
			// it's ok if the password is not here if we are updating a user
			// but if the user isn't being updated, then we throw an error
			if (!isset($this->id)) {
				$this->error[] = "Cannot create user: no password specified";
				return false;
			}
		}
		
		if (!isset($this->role)) {
			$this->role = "user";
		}
		
		if (isset($this->id)) {
			$sql  = "UPDATE " . DBPREFIX . "users SET name='" . $this->sanitize($this->username) . "'";
			if (strlen($this->unencrypted_password) > 0) {
				$sql .= ", pass='" . md5($this->sanitize($this->unencrypted_password)) . "'";
			}
			$sql .= ", role='" . $this->role . "'";
			$sql .= " WHERE id=" . $this->id . ";";
		} else {
			$sql = "INSERT INTO " . DBPREFIX . "users SET name='" . $this->sanitize($this->username) . "', pass='" . md5($this->sanitize($this->unencrypted_password)) . "', role='" . $this->sanitize($this->role) . "';";
		}
		
		$result = $this->db->query($sql);

		if ($result) {
			return true;
		} else {
			$this->error[] = "Failed to save user." . $sql;
			return false;
		}
	}

	public function delete() {
		$sql = "DELETE FROM " . DBPREFIX . "users WHERE name='" . $this->sanitize($this->username) . "';";
		$result = $this->db->query($sql);
		if ($result) {
			return true;
		} else {
			return false;
		}
	}
	
	public function logged_in() {
		if ($_SESSION['rapid_uid'] <> 0 && $_SESSION['rapid_uuid'] == RAPID_UUID) {
			return true;
		} else {
			return false;
		}
	}
}
?>