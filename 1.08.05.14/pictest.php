<?php


	$key = "";
	$max_length_reg_key = 5;

	$chars = array(
		"a","b","c","d","e","f","g","h","i","j","k","l","m",
		"n","o","p","q","r","s","t","u","v","w","x","y","z",
		"A","B","C","D","E","F","G","H","I","J","K","L","M",
		"N","O","P","Q","R","S","T","U","V","W","X","Y","Z",
		"0","1","2","3","4","5","6","7","8","9");

	$count = count($chars) - 1;

	srand((double)microtime()*1234567);

	for($i = 0; $i < $max_length_reg_key; $i++)
	{
		$key .= $chars[rand(0, $count)];
	}

	//Generate my Image...
	$posx = $max_length_reg_key * 10;
	$img_number = imagecreate($posx,17); 
	$white = imagecolorallocate($img_number,255,255,255); 
	$black = imagecolorallocate($img_number,0,0,0); 

	imagefill($img_number,0,0,$white);
	ImageRectangle($img_number,0,0,$posx - 1,16,$black); //Drawing the border

	// Breaking the text ;)
	$posx = rand(5,$posx - 5);
	imageline( $img_number, $posx, 0, $posx, 17, $black);

	Imagestring($img_number,9,3,0,$key,$black); 	

	imagejpeg($img_number);
?>