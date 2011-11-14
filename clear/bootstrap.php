<?php
	//Yes these requires could be included directly in index.php -- but I am doing this to allow future expansion of the framework

	// CONFIGURATION
	require_once(ROOT.DS.'configuration'.DS.'config.php');
	require_once(ROOT.DS.'configuration'.DS.'routing.php');
	require_once(ROOT.DS.'configuration'.DS.'inflection.php');

	// CLEAR
	require_once(ROOT.DS.'clear'.DS.'core.php');
