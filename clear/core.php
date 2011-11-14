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

	/** Secondary Call Function **/
	function performAction($controller,$action,$queryString = null,$render = 0){
		$controllerName = strtolower('controller_'.$controller);
		$dispatch = new $controllerName($controller,$action);
		$dispatch->render = $render;
		return call_user_func_array(array($dispatch,$action),$queryString);
	}

	/** Routing **/
	function routeURL($url){
		global $routing;
		foreach($routing as $pattern => $result){
			if(preg_match($pattern, $url)){
				return preg_replace($pattern, $result, $url);
			}
		}
		return ($url);
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

		After the separation is done, it creates a new object of the class ”controller_”.$controller and calls the method $action of the class.
	*/
	function callHook() {
		global $url;
		global $default;

		$queryString = array();

		if(!isset($url)){ // $url is set in public/index.php -- line 24
			$controller = $default['controller']; // this is set in configuration/routing.php -- line 16
			$action = $default['action']; // this is set in configuration/routing.php -- line 17
		}else{
			$url = routeURL($url); // look at line 25
			$urlArray = array();
			$urlArray = explode("/",$url); // break apart the URL, using "/" as the delimiter
			// identify the Controller
			// Controllers will always be lowercase, plural and begin with "controller_" i.e. ctrl_items, ctrl_cars, ctrl_users
			$controller = strtolower($urlArray[0]);
			array_shift($urlArray); // array_shift | shifts the first value of the array off and returns the array, minus one element and moving everything down
			if(isset($urlArray[0])){
				$action = $urlArray[0];
				array_shift($urlArray); // array_shift | shifts the first value of the array off and returns the array, minus one element and moving everything down
			}else{
				$action = 'index'; // Default Action
			}
			$queryString = $urlArray; // the remainder of the array is the query string
		}

		$controllerName = strtolower('controller_'.$controller); //$controllerName is the file & class name of the controller
		$dispatch = new $controllerName($controller,$action);

		// method_exists — Checks if the class method exists -- If in this class: $controllerClass, this method: $action exists...
		// the (int) part casts it as an integer so it will display 0 if false and 1 if true
		if ((int)method_exists($controllerName, $action)) {
			// call_user_func_array — Call a user function given with an array of parameters
			/*
			* primarily useful as a way to dynamically call functions and methods at runtime without having to use eval() 
			* call_user_func_array() passes the elements in argument_array to function_name as an argument list. This makes it easy to pass an unknown number 
			* of arguments to a function.
			* 
			* Normally, functions are called with the following syntax: function_name('arg one', 'arg two', ...);
			* Calling the same function using call_user_func_array() would look like this: call_user_func_array ('function_name', array ('arg one', 'arg two', ...));
			*/
			call_user_func_array(array($dispatch,"beforeAction"),$queryString);
			call_user_func_array(array($dispatch,$action),$queryString);
			call_user_func_array(array($dispatch,"afterAction"),$queryString);
		}else{
			/* Error Generation Code Here */
		}
	}

	/** Autoload any classes that are required **/
	function __autoload($className){
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

	$cache = new Cache();
	$inflect = new Inflection();

	setReporting();
	callHook();
?>