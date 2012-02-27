<?php
// add this later <meta name="generator" content="WordPress">
session_start();

require_once("rapid.php");	

// if the config.php files hasn't been setup then go to the install page
if (!defined('DBHOST')) {
	header("location: http://" . dirname($_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME']) . "/install.php");
}

// if we are logging out then unset uid
if ($_GET['action'] == "logout") {
	unset($_SESSION['rapid_uid']);
	unset($_SESSION['rapid_uuid']);
}

// if we are not logged in.
if (!isset($_SESSION['rapid_uid'])) {
	
	// if we have not sent the login form?
	if (!isset($_POST['txt_username'])) {
		show_login_page();
		exit();
	} else {
		$cms->user->username = $_POST['txt_username'];
		$cms->user->unencrypted_password = $_POST['pwd_password'];
		$cms->user->authenticate();
		
		if ($cms->user->error <> "") {
			include("header.php");

			$cms->hooks->add_action('admin_login_error_header');
			
			echo "<h1>Oops. Try again.</h1>";
			
			foreach ($cms->user->error as $error) {
				echo "<p>$error</p>";
			}
			
			echo "<a href=\"./\">Give it another try</a>";
			
			$cms->hooks->add_action('admin_login_error_footer');
			
			include("footer.php");
			
			exit();
		}
		
		$_SESSION['rapid_uid'] = $cms->user->id;
		$_SESSION['rapid_uuid'] = RAPID_UUID;
		header('Location: #');
		$cms->hooks->add_action('admin_login');
	}
} else  {
	if ($_SESSION['rapid_uuid'] <> RAPID_UUID) {
		echo "<h1>Invalid UUID</h1>";
		echo "<p><a href='?action=logout'>Logout and try again.</a></p>";
		
		exit();
	}

	if (!isset($_GET['action'])) {
		include("header.php");

		echo "<h2>Blocks</h2>";
		
		// TODO: change this to the $cms->load_all() method once it's finished
		$db = new mysqli(DBHOST, DBUSER, DBPASS, DBNAME);
		$result = $db->query("SELECT * FROM blocks");
		
		?>
		<p>Click on the name of the block you wish to edit.</p>
		
		<table>
			<tr>
				<th>Block</th>
				<th>Edit</th>
				<th>Delete</th>
			</tr>
		<?php
		
		// TODO: change this to foreach($cms->blocks as $block) when method is finished.
		while ($row = $result->fetch_object()) {
			echo "<tr>";
			echo "\t<td>" . $row->name . "</td>";
			echo "\t<td><a href=\"?name=" . $row->name . "&action=edit_block\">Edit</a></td>";
			echo "\t<td><a href=\"?name=" . $row->name . "&action=delete_block\">Delete</a></td>";
			echo "</tr>";
		}
		
		?>
		</table>
		<p>[ <a href='?action=add_block'>Add New</a> ]</p>
		
		<?php
		global $cms;

		$cms->user->id = $_SESSION['uid'];
		$cms->user->load();
		
		if ($cms->user->role == "administrator") {
			$db = new mysqli(DBHOST, DBUSER, DBPASS, DBNAME);
			$result = $db->query("SELECT * FROM users;");
			
			echo "<h2>Users</h2>";
			echo "<table><tr>";
			echo "<th>ID</th>";
			echo "<th>Username</th>";
			echo "<th>Role</th>";
			echo "<th>Edit</th>";
			echo "<th>Delete</th>";
			echo "</tr>";
			
			while ($row = $result->fetch_object()) {
				echo "<tr>";
				echo "<td>" . $row->id . "</td>";
				echo "<td>" . $row->name . "</td>";
				echo "<td>" . $row->role . "</td>";
				echo "<td><a href='?name=" . $row->name . "&action=edit_user'>Edit</a></td>";
				echo "<td><a href='?name=" . $row->name . "&action=delete_user'>Delete</a></td>";
				echo "</tr>";
			}

			echo "</table>";
			echo "<p>[ <a href='?action=add_user'>Add New</a> ]</p>";
		}

		include("footer.php");
	} else {
		switch ($_GET['action']) {
			case 'edit_block':
				include("header.php");
				
				?>
				<h1>Edit Block</h1>

				<fieldset>
					<legend>Block Name: <?php echo $_GET['name'] ?></legend>
					<?php $cms->content($_GET['name']); ?>
				</fieldset>
					
				<h3>Instructions</h3>

				<p>Click the edit button to turn on editing mode. With editing mode on, you can click the content block to start editing. You might notice that it doesn't match the styles of your site. To edit the content with instant preview of the styles, you have to edit this block on that page.
				
				<?php		
				include("footer.php");
				break;
			case 'delete_block':
				include("header.php");
				
				?>
				<h1>Delete Block?</h1>
				
				<p>Are you sure you want to delete the block named "<?php echo $_GET['name']; ?>"</p>
				
				<p>
					<a href="?name=<?php echo $_GET['name']; ?>&action=block_deleted">Yes, delete the block.</a> |
					<a href="<?php echo RAPID_DIR; ?>">No, take me back!</a>
				</p>
				
				<?php			
				include("footer.php");
				break;
			case 'block_deleted':
				include("header.php");
				
				$cms->load($_GET['name']);
				$cms->blocks[$_GET['name']]->delete();
				?>
				
				<h1>Block Deleted</h1>
				<p>The block has been successfully deleted.</p>
				<p><a href="<?php echo RAPID_DIR; ?>">Return to the Main Page</a><p>
				
				<?php			
				include("footer.php");
				break;
			
			case 'add_block':
				include("header.php");
				?>
				
				<h1>Add New Block</h1>
				<p>Please enter a name for your block.</p>
				<form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
					<input type="hidden" name="action" value="block_added" />
					<ul>
						<li>
							<label>Name:</label>
							<input type="text" name="name" />
						</li>
						<li>
							<label>&nbsp;</label>
							<input type="submit" value="Add Block" />
						</li>
					</ul>
				</form>
				
				<?php
				include("footer.php");
				break;
			case 'block_added':
				include("header.php");
				
				$cms->content($_GET['name']);
				?>
				
				<h1>Block Added</h1>
				<p>Your block has been added successfully.</p>
				<p><a href="<?php echo RAPID_DIR; ?>">Return to the Main Page</a><p>
				
				<?php			
				include("footer.php");
				break;
			case 'add_user':
				include("header.php");
				?>
				
				<h1>Add New User</h1>
				<p>Please enter the user information.</p>
				<form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
					<input type="hidden" name="action" value="user_added" />
					<ul>
						<li>
							<label>Username:</label>
							<input type="text" name="username" />
						</li>
						<li>
							<label>Password:</label>
							<input type="password" name="password" />
						</li>
						<li>
							<label>Confirm Password:</label>
							<input type="password" name="confirm_password" />
						</li>
						<li>
							<label>Role:</label>
							<select name="role">
								<option value="user">User</option>
								<option value="administrator">Administrator</option>
							</select>
						</li>						
						<li>
							<label>&nbsp;</label>
							<input type="submit" value="Add User" />
						</li>
					</ul>
				</form>
				
				<?php			
				include("footer.php");
				break;
			case 'user_added':
				include_once("includes/class.user.php");
				
				$u = new user;
				$u->username = $_GET['username'];
				$u->unencrypted_password = $_GET['password'];
				$u->role = $_GET['role'];
				
				if ($u->save()) {
					include("header.php");
					?>
					
					<h1>User Added</h1>
					<p>The user has been added successfully.</p>
					<p><a href="<?php echo RAPID_DIR; ?>">Return to the Main Page</a><p>
					
					<?php			
					include("footer.php");
				} else {
					include("header.php");
					?>

					<h1>Error adding users</h1>
					<p>The user hasn't been added, there was an error.</p>
					
					<?php
						foreach ($u->error as $error) {
							echo "<p>" . $error . "</p>";
						}
					?>
					
					<p><a href="<?php echo RAPID_DIR; ?>">Return to the Main Page</a><p>
					
					<?php
					include("footer.php");
				}
				break;
			case 'edit_user':
				include_once("includes/class.user.php");
				
				$u = new user();
				$u->username = $_GET['name'];
				$u->load(true);
				
				include("header.php");
				?>

				<h1>Edit User</h1>
				<p>Please update the user information.</p>
				<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?action=user_edited">
					<ul>
						<li>
							<label>Username:</label>
							<input type="text" name="name" value="<?php echo $u->username; ?>" readonly="readonly" />
						</li>
						<li>
							<label>Password:</label>
							<input type="password" name="password" />
						</li>
						<li>
							<label>Confirm Password:</label>
							<input type="password" name="confirm_password" />
						</li>
							<li>
							<label>Role:</label>
							<select name="role">
								<option value="administrator" <?php if ($u->role == "administrator") echo "selected=selected";?>>Administrator</option>
								<option value="user" <?php if ($u->role == "user") echo "selected=selected";?>>User</option>
							</select>
						</li>
						<li>
							<label>&nbsp;</label>
							<input type="submit" value="Add User" />
						</li>
					</ul>
				</form>
				
				<?php
				include("footer.php");
				break;
			case 'user_edited':
				include_once("includes/class.user.php");
				
				$u = new user();
				$u->username = $_POST['name'];
				$u->load(true);
				$u->unencrypted_password = $_POST['password'];
				$u->role = $_POST['role'];
				
				echo "<pre>";
				print_r($_POST);
				print_r($u);
				echo "</pre>";

				if ($u->save()) {
					include("header.php");
					?>
					
					<h1>User Updated</h1>
					<p>The user has been updated successfully.</p>
					<p><a href="<?php echo RAPID_DIR; ?>">Return to the Main Page</a><p>
					
					<?php
					include("footer.php");
				} else {
					include("header.php");
					?>

					<h1>Error updating user</h1>
					<p>The user hasn't been updated, there was an error.</p>
					
					<?php
					foreach ($u->error as $error) {
						echo "<p>" . $error . "</p>";
					}
					?>
					
					<p><a href="<?php echo RAPID_DIR; ?>">Return to the Main Page</a><p>
					
					<?php
					include("footer.php");
				}
				break;
			case 'delete_user':
				include("header.php");
				?>

				<h1>Delete User?</h1>
				<p>Are you sure you want to delete the user named "<?php echo $_GET['name']; ?>"</p>
				<p>
					<a href="?name=<?php echo $_GET['name']; ?>&action=user_deleted">Yes, delete this user.</a> |
					<a href="<?php echo RAPID_DIR; ?>">No, take me back!</a>
				</p>
				
				<?php
				include("footer.php");
				break;
			case 'user_deleted':
				include_once("includes/class.user.php");
				
				$u = new user();
				$u->username = $_GET['name'];
				
				if ($u->delete()) {
					include("header.php");
					?>
					
					<h1>User Deleted</h1>
					<p>The user has been deleted successfully.</p>
					<p><a href="<?php echo RAPID_DIR; ?>">Return to the Main Page</a><p>
					
					<?php
					include("footer.php");
				} else {
					include("header.php");
					?>

					<h1>Error deleting users</h1>
					<p>The user hasn't been deleted, there was an error.</p>
					
					<?php
					foreach ($u->error as $error) {
						echo "<p>" . $error . "</p>";
					}
					?>
			
					<p><a href="<?php echo RAPID_DIR; ?>">Return to the Main Page</a><p>
			
					<?php
					include("footer.php");
				}
				break;
			default:
				break;
		}
	}
}

function show_login_page() {
	include("header.php");
	global $hooks;

	$hooks->add_action('admin_login_form');
	?>

	<h1>Login</h1>
	<p>To enable editing of pages, you must be logged into the system. Please enter your username and password below to continue.</p>
	<form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
		<fieldset>
			<ul>
				<li>
					<label>Username: </label>
					<input type="text" name="txt_username" />
				</li>
				<li>
					<label>Password: </label>
					<input type="password" name="pwd_password" />
				</li>
				<li>
					<label>&nbsp;</label>
					<input type="submit" />
				</li>
			</ul>
		</fieldset>
	</form>

	<?php
	include("footer.php");
}
?>