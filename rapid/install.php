<?php include("rapid.php"); ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
        "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>&equiv;RapidCMS &raquo; Administration</title>
<link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/3.2.0/build/cssreset/reset-min.css" />
<link rel="stylesheet" type="text/css" href="../../css/main.css" />
<style>
</style>
</head>
<body>
	<div id="wrapper">
	<h1 class="logo">&equiv; RapidCMS</h1>
	<h2 class="logo">Fast, simple, powerful.</h2>
<?php
require_once("includes/config.php");
// optionally we can open the file and eval the lines in the file... but that sounds dangerous, if we have problems writing the config.php file, then we'll have to do that.

session_start();

class install
{
	public $db_host;
	public $db_name;
	public $db_user;
	public $db_pass;
	
	public $config_already_setup;
	public $database_already_setup;
	public $user_already_setup;
	
	private $errors;
	
	public function __construct() 
	{
		if (defined('DBHOST')) { $this->db_host = DBHOST; }
		if (defined('DBNAME')) { $this->db_name = DBNAME; }
		if (defined('DBUSER')) { $this->db_user = DBUSER; }
		if (defined('DBPASS')) { $this->db_pass = DBPASS; }

		if (!$this->check_status()) 
		{
			if (!$this->config_already_setup)
			{
				if (!$this->setup_config())
				{
					$this->errors[] = "Error: setup_config()";
				}
			} 
			if (!$this->database_already_setup)
			{
				if(!$this->setup_db())
				{
					$this->errors[] = "Error: database_config()";
				}
			}
			if (!$this->user_already_setup)
			{
				if (!$this->setup_user())
				{
					$this->errors[] = "Error: user_config()";
				}
			}
		}
		else
		{
			echo "<h1>Nothing to do</h1>";
			echo "<p>We double checked for you! The config file has already been written, the database has been populated, and you have created an administrator account.</p>";
			exit();
		}
		
		if (isset($this->errors))
		{
			foreach ($this->errors as $error)
			{
				echo $error . "<br />";
			}
		}
		echo "<h1>Setup Complete</h1>";
		echo "<p>Setup is now complete, you can add blocks to your html pages to create easily editable content. For a quick start guide and detailed documentation, please visit <a href='http://rapidcms.org'>the RapidCMS Site</a>.";
		echo "<p>For Security reasons, it's probably a good idea to rename this file to something else so no one trys to mess with your setup</p>";
		echo "<p>To <a href='" . RAPID_DIR . "'>login</a> to RapidCMS go to <a href='" . RAPID_DIR . "'>http://" . $_SERVER['HTTP_HOST'] . RAPID_DIR . "</a>.";
	}
	
	public function check_status()
	{
		if (strlen(trim($this->db_host)) > 1)
		{
			$this->config_already_setup = true;
		}
		else
		{
			$this->config_already_setup = false;
			$this->database_already_setup = false;
			$this->user_already_setup = false;
			return false;
		}
		
		// since the config is already setup, check to see if db setup was completed
		if ($db = new mysqli($this->db_host, $this->db_user, $this->db_pass, $this->db_name))
		{
			// echo "Configuration files are setup correctly.<br />";
		}
		if ($result = $db->query("SELECT * FROM blocks"))
		{
			// echo "Database is setup.<br />";
			$this->database_already_setup = true;
		}
		else
		{
			$this->database_already_setup = false;
			$this->user_already_setup = false;
			return false;
		}
		
		$result = $db->query("SELECT * FROM users WHERE role='administrator'");
		if ($result->num_rows > 0) 
		{
			$this->user_already_setup = true;
		}
		else
		{
			$this->user_already_setup = false;
		}
		
		if ($this->config_already_setup && $this->database_already_setup && $this->user_already_setup)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	public function setup_db() 
	{
		if ($db = new mysqli($this->db_host, $this->db_user, $this->db_pass, $this->db_name))
		{
			// $result = $db->query("DROP TABLE IF EXISTS blocks;");

			if (!$result = $db->query("CREATE TABLE IF NOT EXISTS `blocks` (`id` int(11) NOT NULL AUTO_INCREMENT, `name` varchar(80) COLLATE utf8_unicode_ci NOT NULL, `content` longtext COLLATE utf8_unicode_ci, PRIMARY KEY (`id`));"))
			{
				echo $db->error;
				return false;
			}

			// $result = $db->query("DROP TABLE IF EXISTS `users`;");

			if (!$result = $db->query("CREATE TABLE IF NOT EXISTS `users` (`id` int(10) unsigned NOT NULL AUTO_INCREMENT, `name` varchar(45) COLLATE utf8_unicode_ci NOT NULL, `pass` varchar(45) COLLATE utf8_unicode_ci NOT NULL, `role` varchar(45) COLLATE utf8_unicode_ci NOT NULL, PRIMARY KEY (`id`));"))
			{
				echo $db->error;
				return false;
			}
		}
		else 
		{
			return false;
		}
	}
	
	public function setup_config()
	{
		if ($_POST['action'] == "setup_config") 
		{
			// $directory = dirname($_SERVER['DOCUMENT_ROOT'] . $_SERVER['SCRIPT_NAME']);
			if ($f = fopen("includes/config.php", "wb"))
			{
				$config  = "<?php\n";
				$config .= "define('RAPID_DIR', '" . dirname($_SERVER['SCRIPT_NAME']) ."');\n";
				$config .= "define('RAPID_JS', 'true');\n";
				$config .= "define('DBHOST', '" . $_POST['txt_DBHOST'] . "');\n";
				$config .= "define('DBNAME', '" . $_POST['txt_DBNAME'] . "');\n";
				$config .= "define('DBUSER', '" . $_POST['txt_DBUSER'] . "');\n";
				$config .= "define('DBPASS', '" . $_POST['txt_DBPASS'] . "');\n";
				fputs ($f, $config);
				fclose ($f);
				
				$this->db_host = $_POST['txt_DBHOST'];
				$this->db_name = $_POST['txt_DBNAME'];
				$this->db_user = $_POST['txt_DBUSER'];
				$this->db_pass = $_POST['txt_DBPASS'];
				return true;
			}
			else 
			{
				return false;
			}
		}
		else 
		{
			$this->show_config_form();
			exit();
		}
	}
	
	public function setup_user()
	{
		if ($_POST['action'] == "setup_user") 
		{
			if ($db = new mysqli($this->db_host, $this->db_user, $this->db_pass, $this->db_name))
			{
				if (!$result = $db->query("INSERT INTO users (name, pass, role) VALUES ('" . $db->real_escape_string($_POST['txt_username']) . "', '" . md5($_POST['txt_password']) . "', 'administrator');")) 
				{
					return false;
				}
				else
				{
					return true;
				}
			}
			else 
			{
				return false;
			}
		}
		else 
		{
			$this->show_user_form();
			exit();
		}
	}
	
	public function show_config_form()
	{
		?>
		<h1>Setup Database</h1>
		<p>RapidCMS uses the free mysql database to store information such as content and users. Please enter the host, database name, username, and password of the database you wish to use with RapidCMS.</p>
		<form action="#" method="post">
			<input type="hidden" name="action" value="setup_config" />
			<fieldset>
				<legend>Database Information</legend>
				<ul>
					<li>
						<label>Database Host:</label>
						<input type="text" name="txt_DBHOST" />
					</li>
					<li>
						<label>Database Name:</label>
						<input type="text" name="txt_DBNAME" />
					</li>
					<li>
						<label>User Name:</label>
						<input type="text" name="txt_DBUSER" />
					</li>
					<li>
						<label>Password:</label>
						<input type="password" name="txt_DBPASS" />
					</li>
					<li>
						<label>&nbsp;</label>
						<input type="submit" />
					</li>
				</ul>
			</fieldset>
		</form>
		<?php
	}
	
	public function show_user_form()
	{
		?>
		<h1>Setup Administrator</h1>
		<p>Now we will setup the administrator account. You will need to login to make changes to blocks of content.</p>
		<p>Right now, there isn't any use for multiple roles, but in future release it might be useful to have a role that allows editing of certain blocks only. This way, blocks you can restrict editing to just blog updates, or price changes while keeping things like the header, footer, and menu safe and sound!</p>
		<form action="#" method="post">
			<input type="hidden" name="action" value="setup_user" />
			<fieldset>
				<ul>
					<li>
						<label>Username:</label>
						<input type="text" name="txt_username" />
					</li>
					<li>
						<label>Password:</label>
						<input type="password" name="txt_password" />
					</li>
					<li>
						<label>&nbsp;</label>
						<input type="submit" />
					</li>
				</ul>
			</fieldset>
		</form>
		<?php
	}
}

$i = new install;
?>
</body>
</html>