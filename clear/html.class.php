<?php
	/*
	 * The HTML class is used to aid the template class. It allows you to use a few standard functions for creating links, adding
	 * javascript and css. I have also added a function to convert links to tinyurls.
	 * This class can be used only in the views e.g. $html->includeJs('generic.js');
	 */

	class HTML {
		private $js = array();

		function shortenUrls($data) {
			$data = preg_replace_callback('@(https?://([-\w\.]+)+(:\d+)?(/([\w/_\.]*(\?\S+)?)?)?)@', array(get_class($this), '_fetchTinyUrl'), $data);
			return $data;
		}

		private function _fetchTinyUrl($url) {
			/*
			 * cURL: specifically designed to safely fetch data from remote sites; allows you to connect and communicate to many different types of servers with many different types of protocols
			 * libcurl currently supports: http / HTTP POST / HTTP PUT, https & https certificates, ftp, gopher, telnet, dict, file, ldap protocols,
			 * 								FTP uploading (this can also be done with PHP's ftp extension), HTTP form based upload, proxies, cookies,
			 * 								and user+password authentication.
			 */
			// Initialize a cURL session
			$ch = curl_init();
			$timeout = 5;
			// Set an option for a cURL transfer; Sets an option on the given cURL session handle
			curl_setopt($ch,CURLOPT_URL,'http://tinyurl.com/api-create.php?url='.$url[0]); // The URL to fetch. This can also be set when initializing a session with curl_init().
			curl_setopt($ch,CURLOPT_RETURNTRANSFER,1); // TRUE to return the transfer as a string of the return value of curl_exec() instead of outputting it out directly.
			curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout); // The number of seconds to wait while trying to connect. Use 0 to wait indefinitely.
			$data = curl_exec($ch); // Perform a cURL session
			curl_close($ch);
			return '<a href="'.$data.'" target = "_blank" >'.$data.'</a>';
		}

		function sanitize($data,$type){
			// http://www.php.net/manual/en/filter.filters.php
			// http://net.tutsplus.com/tutorials/php/sanitize-and-validate-data-with-php-filters/
			/*
			 * Common validate types:
			 * 	FILTER_VALIDATE_EMAIL
			 * 	FILTER_VALIDATE_INT
			 * 	FILTER_VALIDATE_IP
			 * 	FILTER_VALIDATE_REGEXP
			 * 	FILTER_VALIDATE_URL
			 *
			 * Common sanitize types:
			 * 	FILTER_SANITIZE_EMAIL
			 * 	FILTER_SANITIZE_NUMBER_INT
			 * 	FILTER_SANITIZE_STRING
			 * 	FILTER_SANITIZE_URL
			 */
			return filter_var($data, $type); // escape special characters in a string for use in an SQL statement
		}

		function link($text,$path,$prompt = null,$confirmMessage = "Are you sure?",$target = ''){
			$path = str_replace(' ','-',$path);
			if($prompt){
				$data = '<a href="javascript:void(0);" onclick="javascript:jumpTo(\''.BASE_PATH.'/'.$path.'\',\''.$confirmMessage.'\')">'.$text.'</a>';
			}else{
				$data = '<a href="'.BASE_PATH.'/'.$path.'target="'.$target.'">'.$text.'</a>';
			}
			return $data;
		}

		function includeJs($fileName = '',$bln_jqueryBase = false) {
			if(!$bln_jqueryBase){
				$data = '<script src="'.BASE_PATH.'/elements/javascripts/'.$fileName.'.js"></script>';
			}else{
				$data = 'https://ajax.googleapis.com/ajax/libs/jquery/1.7.0/jquery.min.js';
			}
			return $data;
		}

		function includeCss($fileName) {
			$data = '<style href="'.BASE_PATH.'/elements/stylesheets/'.$fileName.'.css"></script>';
			return $data;
		}
}