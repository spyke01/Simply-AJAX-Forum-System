<?
	include '_db.php';
	
	for ($x = 1; $x < 100; $x++) {
		//echo "INSERT INTO `" . DBTABLEPREFIX . "topics` (forum_id, user_id, title, datetimestamp) VALUES (1, 1, 'Test #" . $x . "', '" . time() . "');<br />";
		echo "INSERT INTO `" . USERSDBTABLEPREFIX . "users` (username, signup_date) VALUES ('test" . $x . "', '" . time() . "');<br />";
	}
?>