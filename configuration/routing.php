<?php
	/*
		This configuration file enables us to specify default controller and action 
		
		We can also specify custom redirects using regular expressions. Currently I have specified only one redirect i.e. http://localhost/framework/admin/categories/view will become 
		http://localhost/framework/admin/categories_view where admin is the controller and categories_view is the action. This will enable us to create an administration centre with 
		pretty URLs. You can specify others as per your requirements.
		
		Look at these tutorials for more RegEx help: http://www.phpf1.com/tutorial/php-regular-expression.html
	*/
	$routing = array(
		'/admin\/(.*?)\/(.*?)\/(.*)/' => 'admin/\1_\2/\3'
	);

	$default['controller'] = '';
	$default['action'] = '';