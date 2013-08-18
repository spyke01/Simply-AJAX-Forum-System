<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" 
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"> 
	<head>
		<title>Fast Track Sites Simply AJAX Forum System - <? $page->printTemplateVar("PageTitle");  ?></title>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<meta http-equiv="content-language" content="en-us" />
		<!--Stylesheets Begin-->
			<link rel="stylesheet" type="text/css" href="themes/style.css" />
			<link rel="stylesheet" type="text/css" href="themes/lightbox.css" />
			<link rel="stylesheet" type="text/css" href="<?= $themedir; ?>/main.css" />
			<link rel="stylesheet" type="text/css" href="<?= $themedir; ?>/dim.css" />
			<!--[if lt IE 7]>
				<style>
				</style>
			<![endif]-->			
		<!--Stylesheets End-->
	
		<!--Javascripts Begin-->
			<script type="text/javascript" src="javascripts/scriptaculous1.8.2.js"></script>
			<script type="text/javascript" src="javascripts/validation.js"></script>
			<script type="text/javascript">
				var saforum_var_base_url = "<? echo "http://" . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\'); ?>/";
			</script>
			<script type="text/javascript" src="javascripts/ssf_global.js"></script>
			<script type="text/javascript" src="javascripts/bbcode.js"></script>
			<script type="text/javascript" src="javascripts/pm-box.js"></script>
			<script type="text/javascript" src="javascripts/confirm.js"></script>
			<script src="javascripts/lightbox.js" type="text/javascript"></script>
			<!--[if lt IE 7]>
				<script src="javascripts/pngfix.js" defer type="text/javascript"></script>
			<![endif]-->	
		<!--Javascripts End-->
		<link rel="shortcut icon" href="favicon.ico">
	</head>
	<body>
		<div id="container">
			<div id="page">
				<div id="header">
				<?
				echo "\n			</div>";
				echo "\n			<div id=\"top-nav\">";
				$page->printMenu("top", "ul", "", "", "nav", "", ""); 
				?>
				</div>		
				<div id="content">
						<? $page->printTemplateVar('PageHeader'); ?>
						<? $page->printTemplateVar('PageContent'); ?>
						<? $page->printTemplateVar('PageFooter'); ?>
				</div>		
				<form name="styleChangerForm" action="<?= $menuvar['SWITCHER']; ?>" method="post">		
					<div id="footer">				
						<div id="footer-leftcol" class="FForumBorder">
							<select name="style" onchange="javascript: document.styleChangerForm.submit()">
								<option selected>::Style::</option>
							<?
								$stylepath = 'themes'; //our style directory
											
								// Gets all the images in $stylepath
								if($dir = opendir($stylepath)){												
									$x = '0';
									$style_images = array();
									$sub_dir_names = array();
									
									while (false !== ($file = readdir($dir))) {
										//select only directories
										if ($file != "." && $file != ".." && $file != "installer" && !is_file($stylepath . '/' . $file) && !is_link($stylepath . '/' . $file)) {
											//holds our directories so that we can get our files
											$sub_dir = @opendir($stylepath . '/' . $file);
											$sub_dir_names[$file] .= '';
										}
									}									
									
									ksort($sub_dir_names); //sort by name
								
									foreach($sub_dir_names as $key => $value) { 								
										$selected = ( $key == $category ) ? ' selected="selected"' : '';
										echo "\n								<option value='$key'$selected>$key</option>";									
									}
								}
							?>
							</select>
						&nbsp;&nbsp;&nbsp;Copyright &copy; 2007 Fast Track Sites
						</div>
						<div id="footer-rightcol">
							Powered By: <a href="http://www.fasttracksites.com">Fast Track Sites Simply AJAX Forum System</a>
						</div>
					</div>
				</form>
			</div>
		</div>
	</body>
</html>
