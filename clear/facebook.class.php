<?php
	
	require_once(ROOT.DS.LIB.DS.'facebook'.DS.'facebook.php');

	class cf_Facebook{
		protected $_facebook;
		protected $Application_Id;
		protected $Application_Secret;
		protected $Permissions;
		protected $CallBack;
		protected $Application_URL;
			
		/**
		 * setup the facebook login functionality!
		 * 
		 * @param string $Application_Id
		 * @param string $Application_Secret
		 * @param string $Permissions
		 * @param string $callback 
		 */
		public function __construct($Application_Id, $Application_Secret, $Permissions = '', $CallBack = '', $Application_URL = ''){
			$this->Application_Id = $Application_Id;
			$this->Application_Secret = $Application_Secret;
			$this->Permissions = $Permissions;
			$this->CallBack = $CallBack;
			$this->Application_URL = $Application_URL;
	
			// Create An instance of our Facebook Application.
			$this->_facebook = new Facebook(array(
				'appId' => $this->Application_Id,
				'secret' => $this->Application_Secret,
				'cookie' => true
			));
		}
	
		public function getAccessToken(){
			return $this->_facebook->getAccessToken();
		}

		public function api($params){
			return $this->_facebook->api($params);
		}

		public function stream_publish($uid,$msg,$actionLink){
			$param = array(
				'method' => 'stream.publish',
				'uid' => $uid,
				'message' => $msg,
				'access_token' => $this->getAccessToken(),
				'action_links' => json_encode($actionLink)
			);
			return $this->api($param);
		}
		
		public function FBlogout(){
			setcookie ('fbs_'.$this->Application_Id, "", time() - 3600);
		}
		
		public function getLogoutUrl($next){
			$params = array('next'=>$next);
			return $this->_facebook->getLogoutUrl($params);
		}

		/**
			* checks for a session
			* @return string html
		*/
		public function connection(){
			// Get the app User ID
			$user = $facebook->getUser();
			$me = NULL;

			if($user){
				try{
					// If the user has been authenticated then proceed
					$user_profile = $facebook->api('/me');
				}catch(FacebookApiException $e){
					error_log($e);
					$user = null;
				}
			}

			return "
				<div id=\"fb-root\"></div>
				<script>
					window.fbAsyncInit = function(){
						FB.init({
							appId   : '".$this->_facebook->getAppId()."',
							session : " . json_encode($session) . ",
							status  : true, // check login status
							cookie  : true, // enable cookies to allow the server to access the session
							xfbml   : true // parse XFBML
						});
			FB.Event.subscribe('auth.login', function()
			{
			window.location.reload();
			});
			};
			
			(function()
			{
			var e = document.createElement('script');
			e.src = document.location.protocol + '//connect.facebook.net/en_US/all.js';
			e.async = true;
			document.getElementById('fb-root').appendChild(e);
			}());
			</script>
			
			<fb:login-button perms=\"" . $this->Permissions . "\" onlogin='" . $this->CallBack . "'>Connect</fb:login-button>
			";
		}

		public function getFBUID(){
			return $user->id;
		}

		public function InformationInfo(){
			if($_REQUEST["fbs_" . $this->Application_Id] == ""){
				return "Permission Disallow!";
			}

			$PermissionCheck = split(",", $this->Permissions);
			
			$a = str_ireplace(array("\\",'"'), "", $_REQUEST["fbs_" . $this->Application_Id]);
			if(!$a){
				return "Permission Disallow!";
			}

			$user = json_decode(file_get_contents('https://graph.facebook.com/me?' . $a));
			if(isset($user->error->type)){
				throw new Facebook_Api_Exception(array('error_msg'=>$user->error->message));
			}

			$Result["UserID"] = $user->id;
			$Result["Name"] = $user->name;
			$Result["FirstName"] = $user->first_name;
			$Result["LastName"] = $user->last_name;
			$Result["ProfileLink"] = $user->link;
			$Result["ImageLink"] = "<img src='https://graph.facebook.com/" . $user->id . "/picture' />";
			$Result["About"] = $user->about;
			$Result["Quotes"] = $user->quotes;
			$Result["Gender"] = $user->gender;
			$Result["TimeZone"] = $user->timezone;

			if (in_array("email", $PermissionCheck)) {
				$Result["Email"] = $user->email;
			}
			if (in_array("user_birthday", $PermissionCheck)) {
				$Result["Birthday"] = $user->birthday;
			}
			if (in_array("user_location", $PermissionCheck)) {
				$Result["PermanentAddress"] = $user->location->name;
				$Result["CurrentAddress"] = $user->hometown->name;
			}

			return $Result;
		}
	}