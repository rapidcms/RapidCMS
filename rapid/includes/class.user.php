<?php
class user
{
	public $id;
	public $username;
	public $password;
	public $unencrpted_password;
	public $role;
	public $error;
	
	public function __construct()
	{
	}
	
	public function load($name=false)
	{
		if ($name) 
		{
			$sql = "SELECT * FROM users where name='" . $this->username . "';";
		}
		else
		{
			$sql = "SELECT * FROM users where id='" . $this->id . "';";
		}
		$db = new mysqli(DBHOST, DBUSER, DBPASS, DBNAME);
		$result = $db->query($sql);
		if ($result->num_rows > 0)
		{	
			
			$row = $result->fetch_object();
			$this->id = $row->id;
			$this->role = $row->role;
			$this->username = $row->name;
			$this->password = $row->pass;
			return true;
		}
		else
		{
			$this->error[] = "Invalid username or password";
			return false;
		}
	}
	
	public function authenticate()
	{
		if ($this->username == "")
		{
			$this->error[] = "Cannot log in: Username cannot be blank.";
			return false;
		}
		
		if ($this->unencrypted_password == "")
		{
			$this->error[] = "Cannot log in: Password cannot be blank.";
			return false;
		}
		$db = new mysqli(DBHOST, DBUSER, DBPASS, DBNAME);
		$result = $db->query("SELECT * FROM users where name='" . $this->username . "' AND pass='" . md5($this->unencrypted_password) . "';");
		if ($result->num_rows > 0)
		{	
			
			$row = $result->fetch_object();
			$this->id = $row->id;
			$this->username = $row->name;
			$this->password = $row->pass;
			$this->role = $row->role;
			
			return true;
		}
		else
		{
			$this->error[] = "Invalid username or password";
			return false;
		}
	}
	
	public function save()
	{
		if ($this->username == "")
		{
			$this->error[] = "Cannot create user: no user specified";
			return false;
		}
		
		if ($this->unencrypted_password == "")
		{
			// it's ok if the password is not here if we are updating a user
			// but if the user isn't being updated, then we throw an error
			if (!isset($this->id))
			{
				$this->error[] = "Cannot create user: no password specified";
				return false;
			}
		}
		
		if (!isset($this->role))
		{
			$this->role = "user";
		}
		
		if (isset($this->id)) 
		{
			$sql  = "UPDATE users SET name='" . $this->username . "'";
			if (strlen($this->unencrypted_password) > 0)
			{
				$sql .= ", pass='" . md5($this->unencrypted_password) . "'";
			}
			$sql .= ", role='" . $this->role . "'";
			$sql .= " WHERE id=" . $this->id . ";";
		}
		else
		{
			$sql = "INSERT INTO users SET name='" . $this->username . "', pass='" . md5($this->unencrypted_password) . "', role='" . $this->role . "';";
		}
		
		$db = new mysqli(DBHOST, DBUSER, DBPASS, DBNAME);
		$result = $db->query($sql);
		if ($result) 
		{
			return true;
		}
		else
		{
			$this->error[] = "Failed to save user." . $sql;
			return false;
		}
	}

	public function delete()
	{
		$sql = "DELETE FROM users WHERE name='" . $this->username . "';";
		$db = new mysqli(DBHOST, DBUSER, DBPASS, DBNAME);
		$result = $db->query($sql);
		if ($result)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	public function logged_in() {
		if ($_SESSION['rapid_uid'] <> 0) {
			return true;
		} else {
			return false;
		}
	}
}
?>