<?php
	require_once 'class/Session.php';
	require_once 'class/FileHandler.php';

	$session = Session::getInstance();
	$file = new FileHandler;

	if(!$session->is_logged_in())
	{
		header("Location: login.php");
	}

	$filename = $_GET['file'];
    
	if (strpos($filename, DIRECTORY_SEPARATOR) !== false || empty($filename)) 
	{
		die("Invalid parameter 'file'");
	}
    
	$path = $file->get_downloads_folder() . DIRECTORY_SEPARATOR . $filename;
    
	$fp = fopen($path, 'rb');

	header("Content-Type: application/octet-stream");
	header('Content-Disposition: attachment; filename="' . $filename . '"');
	header("Content-Length: " . filesize($path));


	fpassthru($fp);

	exit;