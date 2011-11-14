<?php
	/*
	 * The Model class extends the SQLQuery class which basically is an abstraction layer for the database connectivity.
	 * Depending on your requirements you can specify any other DB connection class that you may require
	 * Default DB environment: MySQL
	*/
	class Model extends SQLQuery{
		protected $_model;

		function __construct(){
			global $inflect;

			$this->connect(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);
			$this->_limit = PAGINATE_LIMIT;
			$this->_model = get_class($this);
			$this->_table = strtolower($inflect->pluralize($this->_model));

			if (!isset($this->abstract)){
				$this->_describe();
			}
		}

		function __destruct(){}
	}
