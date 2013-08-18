<?PHP 
/***************************************************************************
 *                               header.php
 *                            -------------------
 *   begin                : Saturday, Sept 24, 2005
 *   copyright            : (C) 2005 Paden Clayton - Fast Track Sites
 *   email                : sales@fasttacksites.com
 *
 *
 ***************************************************************************/

/***************************************************************************
Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions are met:
    * Redistributions of source code must retain the above copyright
      notice, this list of conditions and the following disclaimer.
    * Redistributions in binary form must reproduce the above copyright
      notice, this list of conditions and the following disclaimer in the
      documentation and/or other materials provided with the distribution.
    * Neither the name of the <organization> nor the
      names of its contributors may be used to endorse or promote products
      derived from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
DISCLAIMED. IN NO EVENT SHALL <COPYRIGHT HOLDER> BE LIABLE FOR ANY
DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 ***************************************************************************/
ini_set('arg_separator.output','&amp;');
//error_reporting(E_ALL); 
ini_set('display_errors', '0');
ini_set('display_errors', '1');

include('_db.php');
if (substr(phpversion(), 0, 1) == 5) { 
	include('includes/classes/php5/DB.php');
	include('includes/classes/php5/graphclass.php');
	include('includes/classes/php5/pageclass.php');
	include('includes/classes/php5/tableclass.php');
	$DB = new DB($server, $dbuser, $dbpass, $dbname); //initialize our DB connection
	$page = new pageClass; //initialize our page
}
else { 
	include('includes/classes/php5/DB.php');
	include('includes/classes/php5/graphclass.php');
	include('includes/classes/php4/pageclass.php');
	include('includes/classes/php4/tableclass.php');
	$DB = &new DB($server, $dbuser, $dbpass, $dbname); //initialize our DB connection
	$page = &new pageClass; //initialize our page
}

session_start();
include('config.php');
include('includes/menu.php');
include('includes/functions.php');
include('includes/constants.php');

// Get user's theme
$themeDir = $safs_config['ftssafs_theme'];

// Get our image array for our theme
include('themes/' . $themeDir . '/images.php');

//=====================================================
// Cookie login script by: Demio from:  
// Changed some items and set it to expire in 1 month
//=====================================================
$cookiename = $safs_config['ftssafs_cookie_name'];
if (isset($_COOKIE[$cookiename]) && $_SESSION['STATUS'] != "true" && !defined("IN_LOGIN")) {
	$data = explode("-", $_COOKIE[$cookiename]);

	$sql = "SELECT * FROM `" . USERSDBTABLEPREFIX . "users` WHERE id='" . keepsafe($data[0]) . "' AND password='" . keepsafe($data[1]) . "'";
	$result = $DB->query($sql);
	if ($result && $DB->num_rows() == 1) {
		$row = $DB->fetch_array($result);
		
		$_SESSION['STATUS'] = "true";
		$_SESSION['userid'] = $row['id'];
		$_SESSION['username'] = $row['username'];
		$_SESSION['epassword'] = $row['password'];
		$_SESSION['email_address'] = $row['email_address'];
		$_SESSION['first_name'] = $row['first_name'];
		$_SESSION['last_name'] = $row['last_name'];
		$_SESSION['website'] = $row['website'];
		$_SESSION['user_level'] = $row['user_level'];
		$_SESSION['script_locale'] = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
	}
	else {
		// Try the clients table
		$sql = "SELECT * FROM `" . DBTABLEPREFIX . "clients` WHERE id='" . keepsafe($data[0]) . "' AND password='" . keepsafe($data[1]) . "'";
		$result = $DB->query($sql);
		if ($result && $DB->num_rows() == 1) {
			$row = $DB->fetch_array($result);
			
			$_SESSION['STATUS'] = "true";
			$_SESSION['userid'] = $row['id'];
			$_SESSION['username'] = $row['username'];
			$_SESSION['epassword'] = $row['password'];
			$_SESSION['email_address'] = $row['email_address'];
			$_SESSION['first_name'] = $row['first_name'];
			$_SESSION['last_name'] = $row['last_name'];
			$_SESSION['website'] = $row['website'];
			$_SESSION['user_level'] = USER;
			$_SESSION['script_locale'] = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
		}
	}
}
?>
