<?
/***************************************************************************
 *                               join.php
 *                            -------------------
 *   begin                : Saturday, Sept 24, 2005
 *   copyright            : (C) 2005 Paden Clayton - Fast Track Sites
 *   email                : sales@fasttacksites.com
 *
 *
 ***************************************************************************/

/***************************************************************************
 *
 * This program is licensed under the Fast Track Sites Program license 
 * located inside the license.txt file included with this program. This is a 
 * legally binding license, and is protected by all applicable laws, by 
 * editing this page you fall subject to these licensing terms.
 *
 ***************************************************************************/

if ($action == "newUser") {
	//=====================================================
	// Define variables from post data
	//=====================================================	
	$postfirst_name = $_POST['first_name'];
	$postlast_name = $_POST['last_name'];
	$postemail_address = $_POST['email_address'];
	$postusername = $_POST['username'];
	$postpassword = $_POST['password1'];
	$postcountry = $_POST['country'];
	$postinfo = $_POST['info'];
	$postgender = $_POST['gender'];
	$current_time = time();
	
	
	//=====================================================
	// Strip dangerous tags
	//=====================================================	
	$postusername = keepsafe($postusername);
	$postpassword = keepsafe($postpassword);
	$postfirst_name = keepsafe($postfirst_name);
	$postlast_name = keepsafe($postlast_name);
	$postemail_address = keepsafe($postemail_address);
	$postcountry = keeptasafe($postcountry);
	$postinfo = keeptasafe($postinfo);
	
	//=====================================================
	// Let's do some checking and ensure that the 
	// user's email address or username does not exist 
	// in the database.
	//=====================================================		
	
	switch ($postgender) {
		case 1: $postgender="male"; break;
		case 2: $postgender="female"; break;
		default: $postgender="none specified"; break;
	}
	 
	 $sql_email_check = mysql_query("SELECT users_email_address FROM `" . $DBTABLEPREFIX . "users` WHERE users_email_address='$postemail_address'");
	 $sql_username_check = mysql_query("SELECT users_username FROM `" . $DBTABLEPREFIX . "users` WHERE users_username='$postusername'");
	 
	 $email_check = mysql_num_rows($sql_email_check);
	 $username_check = mysql_num_rows($sql_username_check);
	 
	 if(($email_check > 0) || ($username_check > 0)){
	 	$content .= "Please fix the following errors: <br />";
	 	if($email_check > 0){
	 		$content .= "$T_Email_Address_Taken<br />";
	 		unset($postemail_address);
	 	}
	 	if($username_check > 0){
	 		$content .= "$T_Desired_Username_Taken<br />";
	 		unset($postusername);
	 	}
	 }
	 else {
		//=====================================================
		// Everything has passed both error checks that we 
		// have done. It's time to create the account!
		//=====================================================
	
		$db_password = md5($postpassword);
		$activationCode = md5($postpassword . $current_time);
		
		// generate SQL.
		if ($safs_config['ftssafs_activation_active'] == ACTIVE) {
			$sql = "INSERT INTO `" . $DBTABLEPREFIX . "users` (users_first_name, users_last_name, users_gender, users_email_address, users_username, users_password, users_country, users_info, users_signup_date, users_last_login, users_activation)
					VALUES('$postfirst_name', '$postlast_name', '$postgender', '$postemail_address', '$postusername', '$db_password', '$postcountry', '$postinfo', '$current_time', '$current_time', '$activationCode')";
		}
		else {
			$sql = "INSERT INTO `" . $DBTABLEPREFIX . "users` (users_first_name, users_last_name, users_gender, users_email_address, users_username, users_password, users_country, users_info, users_signup_date, users_last_login)
					VALUES('$postfirst_name', '$postlast_name', '$postgender', '$postemail_address', '$postusername', '$db_password', '$postcountry', '$postinfo', '$current_time', '$current_time')";
		}
		
		$result = mysql_query($sql);
		
		if(!$result){
			$content .= "There has been an error creating your account. Please contact the webmaster.";
		}
		else {
			$userid = mysql_insert_id();
			
			if ($safs_config['ftssafs_activation_active'] == ACTIVE) {
				// Send email
				// To send HTML mail, the Content-type header must be set
				$headers  = "MIME-Version: 1.0" . "\r\n";
				$headers .= "Content-type: text/html; charset=iso-8859-1" . "\r\n";
		
				// Additional headers
				$headers .= "To: " . $postemail_address . "\r\n";
				$headers .= "From: " . $safs_config['ftssafs_board_email'] . "\r\n";
				
				// Subject and message
				$subject = "Please activate your your " . $safs_config['ftssafs_board_name'] . " account";
				$message = "The administrator of " . $safs_config['ftssafs_board_name'] . " has required you to activate your new account, please follow the link below.<br /><br />
				<a href=\"" . $safs_config['ftssafs_board_url'] . "/index.php?p=join&action=activateaccount&id=$userid&code=$activationCode\">" . $safs_config['ftssafs_board_url'] . "/index.php?p=join&action=activateaccount&id=$userid&code=$activationCode</a><br /></br>
				" . bbcode($safs_config['ftssafs_board_email_sig']);
				
				// Send it
				mail($to, $subject, $message, $headers);
				
				$content .= "Thank you for registering! <br />
							<br />
							Before you can login you must first activate your account using the email that was sent to your email address. Once you have cliked the link you will be able to login.<br />	
							<style>
								#formHolder {
									display: none;
								}
							</style>";
			}
			else {
				$content .= "Welcome $postfirst_name $postlast_name,<br />
							Thank you for registering! <br />
							<br />
							You can now login with the following information:<br />
							<br />
							Username: $username<br />
							Password: $password<br />
	
							<style>
								#formHolder {
									display: none;
								}
							</style>";
			}
		}	
		session_destroy();
	}
	unset($_POST['submit']);
}
elseif ($action == "activateaccount") {
	$id = keepsafe($_GET['id']);
	$code = keepsafe($_GET['code']);
	
	$sql = "SELECT * FROM `" . $DBTABLEPREFIX . "users` WHERE users_id = '$id' AND users_activation = '$code'";
	$result = mysql_query($sql);
	
	if (mysql_num_rows($result) == 0) {
		$content .= "There was an error while activating your account please contact a board administrator at: " . $safs_config['ftssafs_board_email'] . "
							<style>
								#formHolder {
									display: none;
								}
							</style>";
	}
	else {
		$sql2 = "UPDATE `" . $DBTABLEPREFIX . "users` SET users_activation = '' WHERE users_id = '$id'";
		$result2 = mysql_query($sql2);
		
		$content .= "Your account has been activated and you can now login. Thank you for registering!
							<style>
								#formHolder {
									display: none;
								}
							</style>";
	}
	mysql_free_result($result);
}
	$content .= " 
	<center>
	<div id=\"formHolder\">
	<form id=\"newUserForm\" name=\"newUserForm\" method=\"post\" action=\"" . $menuvar['JOIN'] . "&action=newUser\">
			<table class=\"JForumBorder\" border=\"0\" cellpadding=\"0\" cellspacing=\"1\">
				<tr class=\"title1\">
					<td class=\"JT1\" colspan=\"2\">" . $T_User_Info . "</td>
				</tr>
				<tr class=\"title2\">
					<td class=\"JT2Notification\" colspan=\"2\">" . $T_Mandatory_Info_Warning . "</td>
				</tr><tr>
				<tr class=\"row1\"> 
					<td class=\"JT2Column1\">" . $T_Desired_Username . "</td>
					<td class=\"JR1Column1\"><div id=\"usernameCheckerHolder\" style=\"float: right;\"><a style=\"cursor: pointer; cursor: hand; color: red;\" onclick=\"new Ajax.Updater('usernameCheckerHolder', 'ajax.php?action=checkusername&value=' + document.newUserForm.username.value, {asynchronous:true});\">[Check]</a></div><input name=\"username\" type=\"text\" size=\"60\" id=\"username\" class=\"required validate-alphanum\" title=\"" . $T_Desired_Username_Error . "\" value=\"" . $_POST['username'] . "\" /></td>
				</tr>
				<tr class=\"row1\"> 
					<td class=\"JT2Column1\">" . $T_Password . "</td>
					<td class=\"JR1Column1\"><input name=\"password1\" type=\"password\" size=\"60\" id=\"password1\" class=\"required validate-password\" title=\"" . $T_Password_Error . "\" value=\"\" /></td>
				</tr>
				<tr class=\"row1\"> 
					<td class=\"JT2Column1\">" . $T_Confirm_Password . "</td>
					<td class=\"JR1Column1\"><input name=\"password2\" type=\"password\" size=\"60\" id=\"password2\" class=\"required validate-password-confirm\" value=\"\" /></td>
				</tr>
				<tr class=\"row1\"> 
					<td class=\"JT2Column1\">" . $T_Email_Address . "</td>
					<td class=\"JR1Column1\"><div id=\"emailaddressCheckerHolder\" style=\"float: right;\"><a style=\"cursor: pointer; cursor: hand; color: red;\" onclick=\"new Ajax.Updater('emailaddressCheckerHolder', 'ajax.php?action=checkemailaddress&value=' + document.newUserForm.email_address.value, {asynchronous:true});\">[Check]</a></div><input name=\"email_address\" type=\"text\" size=\"60\" id=\"email_address\" class=\"required validate-email\" value=\"" . $_POST['email_address'] . "\" /></td>
				</tr>
				<tr class=\"row1\"> 
					<td class=\"JT2Column1\">" . $T_First_Name . "</td>
					<td class=\"JR1Column1\"><input name=\"first_name\" type=\"text\" size=\"60\" id=\"first_name2\" class=\"required validate-alpha\" title=\"" . $T_First_Name_Error . "\" value=\"" . $_POST['first_name'] . "\" /></td>
				</tr>
				<tr class=\"row1\"> 
					<td class=\"JT2Column1\">" . $T_Last_Name . "</td>
					<td class=\"JR1Column1\"><input name=\"last_name\" type=\"text\" size=\"60\" id=\"last_name\" class=\"required validate-alpha\" title=\"" . $T_Last_Name_Error . "\" value=\"" . $_POST['last_name'] . "\" /></td>
				</tr>
				<tr class=\"row1\"> 
					<td class=\"JT2Column1\">" . $T_Gender . "</td>
					<td class=\"JR1Column1\">
						<input type=\"radio\" name=\"gender\" value=\"0\" /> None Specified 
						<input type=\"radio\" name=\"gender\" value=\"1\" /> Male
						<input type=\"radio\" name=\"gender\" value=\"2\" class=\"validate-one-required\" /> Female
					</td>
				</tr>
				<tr class=\"title2\">
					<td class=\"JT2divider\" colspan=\"2\"></td>
				</tr>
				<tr class=\"title1\">
					<td class=\"JT1\" colspan=\"2\">" . $T_Optional_Info . "</td>
				</tr>
				<tr class=\"title2\">
					<td class=\"JT2Notification\" colspan=\"2\">" . $T_Optional_Info_Warning . "</td>
				</tr>
				<tr class=\"row1\"> 
					<td class=\"JT2Column1\">" . $T_Country . "</td>
					<td class=\"JR1Column1\"><input type=\"text\" size=\"60\" name=\"country\" id=\"country\" value=\"" . $_POST['country'] . "\" /></td>
				</tr>
				<tr class=\"row1\"> 
					<td class=\"JT2Column1\">" . $T_Intrests . "</td>
					<td class=\"JR1Column1\"><textarea name=\"info\" cols=\"60\" rows=\"8\" id=\"info\">" . $_POST['info'] . "</textarea></td>
				</tr>
				<tr class=\"title2\"> 
					<td colspan=\"2\"><center><input type=\"submit\" class=\"button\" name=\"submit\" value=\"Join Now!\" /></center></td>
				</tr>
			</table>
	</form>
	</div>
	</center>
	<script type=\"text/javascript\">
		var valid = new Validation('newUserForm', {immediate : true, useTitles:true});
						Validation.addAllThese([
							['validate-password', 'Your password must be more than 6 characters and not be \'password\' or the same as your username', {
								minLength : 7,
								notOneOf : ['password','PASSWORD','1234567','0123456'],
								notEqualToField : 'username'
							}],
							['validate-password-confirm', 'Your confirmation password does not match your first password, please try again.', {
								equalToField : 'password1'
							}]
						]);
	</script>";	

$page->setTemplateVar("PageContent", $content);
?>