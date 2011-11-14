<?php
	/*
	 * Facebook Permissions:
	 * 		http://developers.facebook.com/docs/reference/api/permissions/
	 *
	 */

	require_once(ROOT.DS.LIB.DS.'facebook'.DS.'facebook.php');

	class cf_Facebook extends Facebook{
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

		public function setCallBack($CallBack){
			$this->CallBack = $CallBack;
		}

		public function setPermissions($Permissions){
			$this->Permissions = $Permissions;
		}

		public function setAppURL($Application_URL){
			$this->Application_URL = $Application_URL;
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

			if($user){
				try{
					// If the user has been authenticated then proceed
					$user_profile = $facebook->api('/me');
				}catch(FacebookApiException $e){
					error_log($e);
					$user = null;
				}
			}

			return "<div id=\"fb-root\"></div>
				<script>
					window.fbAsyncInit = function(){
						FB.init({
							appId   : '".$this->_facebook->getAppId()."',
							session : " . json_encode($session) . ",
							status  : true, // check login status
							cookie  : true, // enable cookies to allow the server to access the session
							xfbml   : true // parse XFBML
						});
						FB.Event.subscribe('auth.login', function(){
							window.location.reload();
						});
					};

					(function(){
						var e = document.createElement('script');
						e.src = document.location.protocol + '//connect.facebook.net/en_US/all.js';
						e.async = true;
						document.getElementById('fb-root').appendChild(e);
					}());
				</script>

				<fb:login-button perms=\"".$this->Permissions."\" onlogin='".$this->CallBack."'>Connect</fb:login-button>";
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

		public function fb_FQL($arrElements){
			$fql = "select ".$arrElements[0]." from ".$arrElements[1]." where uid=".$this->getFBUID();
			$param = array('method' => 'fql.query',
							'query' => $fql,
							'callback' => '');
			return $this->_facebook->api($param);
		}

		/**
		* Get Friend List
		*
		* This function will retrieve the friend list of any given facebook user id. Optionally, it allows a few parameters to
		* customize the list.
		*
		*/
		public function getFriendList($fbuserId, $appUser = false, $start = 0, $limit = 0){
			// Does the friends need to add the app to be qualified
			if ($appUser == false){
				$usersArray = $this->_facebook->api_client->fql_query("SELECT uid FROM user WHERE uid IN
				(SELECT uid2 FROM friend WHERE uid1 = {$this->getFBUID()})");
			}else{
				$usersArray = $this->_facebook->api_client->fql_query("SELECT uid FROM user WHERE has_added_app = 1 AND uid IN
				(SELECT uid2 FROM friend WHERE uid1 = {$this->getFBUID()})");
			}

			if(empty($usersArray)){
				return array();
			}

			// Make an array of the friends
			foreach($usersArray as $user){
				$users[] = $user['uid'];
			}

			// Put a limit of the friends if specified
			if ($appUser && !empty($users) && $limit){
				$users = array_slice($users, $start, $limit);
			}

			// Return the friend list
			return $users;
		}
	}