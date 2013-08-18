<?php
/*
Uploadify v2.1.0
Release Date: August 24, 2009

Copyright (c) 2009 Ronnie Garcia, Travis Nickels

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
*/
if (!empty($_FILES)) {	
	$uploadDirectory = "files/";
	$tempFile = $_FILES['Filedata']['tmp_name'];
	$fileName = "";
	
	// Get our file name and extension so we can modify the name
	$explodedName = explode('.', $_FILES['Filedata']['name']);
	$finalKey = count($explodedName) - 1;
	
	for ($i = 0; $i <= $finalKey; $i++) {
		if ($i != $finalKey) {
			$fileName .=  (!empty($fileName)) ? "." : "";
			$fileName .= $explodedName[$i];
		}
		else { $fileExt =  $explodedName[$i]; }
	}
	
	// Append a timestamp on our filename			
	$targetFile =  $uploadDirectory . $fileName . "_" . date("mdyhi",time()) . "." . $fileExt;

	// Create the directory if it doesn't exist
	//mkdir($uploadDirectory, 0755, true);
	
	// Move the temp file
	move_uploaded_file($tempFile,$targetFile);
	
	// Spit back our filename
	echo $targetFile;
}
?>