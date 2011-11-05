<?php
	class Template {
		protected $variables = array();
		protected $_controller;
		protected $_action;

		function __construct($controller,$action) {
			$this->_controller = $controller;
			$this->_action = $action;
		}

		/** Set Variables **/
		function set($name,$value) {
			$this->variables[$name] = $value;
		}

		/** Display Template **/
		function render() {
			extract($this->variables);

			// if it does not find header and footer in the view/controllerName folder then it goes for the global header and footer in the view folder
			if (file_exists(ROOT.DS.'application'.DS.'views'.DS.$this->_controller.DS.'header.php')){
				include(ROOT.DS.'application'.DS.'views'.DS.$this->_controller.DS.'header.php');
			}else{
				include(ROOT.DS.'application'.DS.'views'.DS.'header.php');
			}

			include(ROOT.DS.'application'.DS.'views'.DS.$this->_controller.DS.$this->_action.'.php');

			if(file_exists(ROOT.DS.'application'.DS.'views'.DS.$this->_controller.DS.'footer.php')){
				include(ROOT.DS.'application'.DS.'views'.DS.$this->_controller.DS.'footer.php');
			}else{
				include(ROOT.DS.'application'.DS.'views'.DS.'footer.php');
			}
		}
	}