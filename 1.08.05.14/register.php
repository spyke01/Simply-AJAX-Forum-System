<?
/***************************************************************************
 *                               register.php
 *                            -------------------
 *   begin                : Saturday', Sept 24', 2005
 *   copyright            : ('C) 2005 Paden Clayton
 *   email                : padenc2001@gmail.com
 *
 *
 ***************************************************************************/

/***************************************************************************
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 ***************************************************************************/
include 'includes/header.php';

// Define REQUEST fields into simple variables
$first_name = $_POST['first_name'];
$last_name = $_POST['last_name'];
$email_address = $_POST['email_address'];
$username = $_POST['username'];
$password = $_POST['password'];
$password2 = $_POST['pass1'];
$country = $_POST['country'];
$info = $_POST['info'];
$gender = $_POST['gender'];
$current_time = time();


// strip away any dangerous tags
$username = keepsafe($username);
$password = keepsafe($password);
$password2 = keepsafe($password2);
$first_name = keepsafe($first_name);
$last_name = keepsafe($last_name);
$email_address = keepsafe($email_address);
$country = keeptasafe($country);
$info = keeptasafe($info);


/* Do some error checking on the form REQUESTed fields */

if((!$first_name) || (!$last_name) || (!$email_address) || (!$username)){
	echo 'You did not submit the following required information! <br />';
	if(!$first_name){
		echo "First Name is a required field. Please try again.<br />";
	}
	if(!$last_name){
		echo "Last Name is a required field. Please try again.<br />";
	}
	if(!$email_address){
		echo "Email Address is a required field. Please try again.<br />";
	}
	if(!$username){
		echo "Desired Username is a required field. Please try again.<br />";
	}
	/* End the error checking and if everything is ok, we'll move on to
	 creating the user account */
	exit(); // if the error checking has failed, we'll exit the script!
}

	
/* Let's do some checking and ensure that the user's email address or username
 does not exist in the database */

switch ($gender) {
	case 1: $gender="male"; break;
	case 2: $gender="female"; break;
	default: $gender="none specified"; break;
}
 
 $sql_email_check = mysql_query("SELECT users_email_address FROM `users` WHERE users_email_address='$email_address'");
 $sql_username_check = mysql_query("SELECT users_username FROM `users` WHERE users_username='$username'");
 
 $email_check = mysql_num_rows($sql_email_check);
 $username_check = mysql_num_rows($sql_username_check);
 
 if(($email_check > 0) || ($username_check > 0)){
 	echo "Please fix the following errors: <br />";
 	if($email_check > 0){
 		echo "<strong>Your email address has already been used by another member in our database. Please submit a different Email address!<br />";
 		unset($email_address);
 	}
 	if($username_check > 0){
 		echo "The username you have selected has already been used by another member in our database. Please choose a different Username!<br />";
 		unset($username);
 	}
 	exit();  // exit the script so that we do not create this account!
 }
 
/* Everything has passed both error checks that we have done.
It's time to create the account! */

$db_password = md5($password);

// Enter info into the Database.
$info2 = htmlspecialchars($info);
$sql = mysql_query("INSERT INTO `users` (users_first_name, users_last_name, users_gender, users_email_address, users_username, users_password, users_country, users_info, users_signup_date, users_last_login)
		VALUES('$first_name', '$last_name', '$gender', '$email_address', '$username', '$db_password', '$country', '$info', '$current_time', '$current_time')") or die (mysql_error());

if(!$sql){
	echo 'There has been an error creating your account. Please contact the webmaster.';
} else {
	$userid = mysql_insert_id();
	$oldmessage = "
Welcome $first_name $last_name,
Thank you for registering!

<!--
You are just two steps away from logging in and accessing our exclusive members area.
	
To activate your membership, please click <a href='http://spyke01.mfhosting.com/activate.php?id=$userid&code=$db_password'>here</a>.
	
Once you activate your memebership, you will be able to login with the following information:
-->

Username: $username
Password: $password
	
Thanks!
Spyke01";
	$message = "
Welcome $first_name $last_name,
Thank you for registering! 

You can now login with the following information:

Username: $username
Password: $password
	
Thanks!
Spyke01";

	echo '<pre>';
	echo $message;
	echo '</pre>';
}

include 'includes/footer.php';
?>