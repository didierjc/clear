<?php
	/*
		Notice that I have purposely not included the closing "? >" This is to avoid injection of any extra whitespaces in our output || taken from the Zend coding style
		http://framework.zend.com/manual/en/coding-standard.coding-style.html
		For files that contain only PHP code, the closing tag ("? >") is never permitted. It is not required by PHP, and omitting it prevents the accidental injection of trailing 
		white space into the response.

		Allot of people think that a PHP file without a closing tag, is not also strange but wrong and won't compile. But that's far from the truth. In fact, even Zend 
		(the company behind the PHP language) forbids its use in the Zend Framework; IN PHP ONLY FILES, IT'S NOT RECOMMENDED!
		
		How come this is a good practice? First, the PHP interpreter won't complain about a missing closing tag. Second, the most important, is that leaving the file without a 
		closing tag will avoid accidental injection of trailing whitespace into the response.
	*/

	// On Windows, both slash (/) and backslash (\) are used as directory separator character. In other environments, it is the forward slash (/). With that in mind it seems kind of 
	// dumb to even worry about this anymore. Just use the *nix variety and you should be fine.
	define('DS', '/'); 
	define('ROOT', dirname(dirname(__FILE__))); // dirname — given a string containing the path of a file or directory, this function will return the parent directory's path.
	define('LIB', 'library');

	// URL: 
	$url = $_GET['url'];
	
	/*
		the "bootstrapping" approach is responsible for requiring all of the needed MVC components used for displaying the page the user requested. By filtering all requests to a 
		single file, we are able to use a development approach similar to traditional software development
		
		*** THIS FILE AUTOMATICALLY INCLUDES ALL NECESSARY FILES FOR THIS FRAMEWORK
	*/
	require_once(ROOT.DS.'clear'.DS.'bootstrap.php'); // all calls go via our index.php except for images/js/css