<?php
	/** Check if environment is development and display errors **/
	function setReporting(){
		if(DEVELOPMENT_ENVIRONMENT == true){
			// error_reporting() | sets the error_reporting directive at runtime; PHP has many levels of errors, using this function sets that level for the duration of your script
			error_reporting(E_ALL); // E_ALL | All errors and warnings
			ini_set('display_errors', 1); // set the value of a configuration option -- display_errors: determines whether errors should be printed to the screen as part of the output
		}else{
			error_reporting(E_ALL);
			ini_set('display_errors', 0);
			ini_set('log_errors', 1); // script error messages will be logged to the server's error log
			ini_set('error_log', ROOT.DS.'tmp'.DS.'logs'.DS.'error_'.date("Ymd").'_'.date("His").'.log'); // name of the file where script errors should be logged
		}
	}

	/** Main Call Function **/
	/*
		our URLs will look - yoursite.com/controllerName/actionName/queryString
		
		callHook() basically takes the URL which we have received from index.php and separates it out as $controller, $action and the remaining as $queryString. 
		$model is the singular version of $controller.

		example: if our URL is example.com/items/del/1/first-item, then
			Controller	=> items
			Model		=> item (corresponding database table)
			View		=> del
			Action		=> del
			Query String is an array (1,first-item)

		After the separation is done, it creates a new object of the class $controller.”Controller” and calls the method $action of the class.
	*/
	function callHook() {
		global $url;

		$urlArray = array();
		$urlArray = explode("/",$url); // break apart the URL, using "/" as the delimiter

		// identify the Controller
		// Controllers will always be lowercase, plural and begin with "ctrl_" i.e. ctrl_items, ctrl_cars, ctrl_users
		$controller = $urlArray[0];
		$controller = strtolower($controller);
		$controllerName = 'ctrl_'.$controller; //$controllerName is the file name of the controller file
		$controllerClass = 'controller_'.$controller; //$controllerName is the file name of the controller file
		array_shift($urlArray); // array_shift | shifts the first value of the array off and returns the array, minus one element and moving everything down

		$action = $urlArray[0];
		array_shift($urlArray);

		$queryString = $urlArray; // the remainder of the array is the query string

		$model = rtrim($controller, 's');
		$model = strtolower('mod_'.$model); // Models will always be lowercase, singular and begin with "mod_" i.e. mod_item, mod_car, mod_user

		$dispatch = new $controllerClass($model,$controllerName,$action);

		// method_exists — Checks if the class method exists -- If in this class: $controllerClass, this method: $action exists...
		// the (int) part casts it as an integer so it will display 0 if false and 1 if true
		if((int)method_exists($controllerClass, $action)){
			// Call a user function -- the parameters to be passed to the function as an indexed array
			call_user_func_array(array($dispatch,$action), $queryString);
		}else{
			/* Error Generation Code Here */
		}
	}

	/** Autoload any classes that are required **/
	function __autoload($className) {
		if(file_exists(ROOT.DS.'clear'.DS.strtolower($className).'.class.php')){
			require_once(ROOT.DS.'clear'.DS.strtolower($className).'.class.php');
		}else if(file_exists(ROOT.DS.'application'.DS.'controllers'.DS.strtolower($className).'.php')){
			require_once(ROOT.DS.'application'.DS.'controllers'.DS.strtolower($className).'.php');
		}else if(file_exists(ROOT.DS.'application'.DS.'models'.DS.strtolower($className).'.php')){
			require_once(ROOT.DS.'application'.DS.'models'.DS.strtolower($className).'.php');
		}else{
			/* Error Generation Code Here */
		}
	}

	setReporting();
	callHook();