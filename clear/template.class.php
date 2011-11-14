<?php
	/*
	 * added variable called "doNotRenderHeader" which will enable you to not output headers for a particular action.
	 * This can be used in AJAX calls when you do not want to return the headers. It has to be called by the controller
	 * 	e.g. $this->doNotRenderHeader = 1;
	 */

	class Template{
		protected $variables = array();
		protected $_controller;
		protected $_action;

		function __construct($controller,$action){
			$this->_controller = $controller;
			$this->_action = $action;
		}

		/** Set Variables **/
		function set($name,$value){
			$this->variables[$name] = $value;
		}

		/** Display Template **/
		function render(){
			extract($this->variables);

			// if it does not find header and footer in the view/controllerName folder then it goes for the global header and footer in the view folder
			if (file_exists(ROOT.DS.'application'.DS.'views'.DS.$this->_controller.DS.'header.php')){
				include(ROOT.DS.'application'.DS.'views'.DS.$this->_controller.DS.'header.php');
			}else{
				include(ROOT.DS.'application'.DS.'views'.DS.'g_header.php');
			}

			include(ROOT.DS.'application'.DS.'views'.DS.$this->_controller.DS.$this->_action.'.php');

			if(file_exists(ROOT.DS.'application'.DS.'views'.DS.$this->_controller.DS.'footer.php')){
				include(ROOT.DS.'application'.DS.'views'.DS.$this->_controller.DS.'footer.php');
			}else{
				include(ROOT.DS.'application'.DS.'views'.DS.'g_footer.php');
			}
		}
	}