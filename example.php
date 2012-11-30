<?php
	include_once('phpKeySafe.php');
	
	$Safe = new phpKeySafe(realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'examplekeys/');
	$MyKey = $Safe->getKey('example.secret');
	
	print_r($MyKey);
	
	// Prints: moo