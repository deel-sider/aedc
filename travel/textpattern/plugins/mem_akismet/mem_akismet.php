<?php

global $prefs, $event;


if (!isset($prefs['mem_akismet_submit_ham_spam'])) {
	set_pref('mem_akismet_submit_ham_spam', '0', 'comments', 0, 'yesnoradio');
}
if (!isset($prefs['mem_akismet_spam_server'])) {
	set_pref('mem_akismet_spam_server', 'rest.akismet.com', 'comments', 0);
}

if (!isset($prefs['mem_akismet_api_key'])) {
	set_pref('mem_akismet_api_key','','comments');
} else if (!empty($prefs['mem_akismet_api_key'])) {
	// This function gets called when Txp is about to save.
	register_callback('mem_akismet_check','comment.save');

 	if (@$prefs['mem_akismet_submit_ham_spam'] == 1 && @txpinterface == 'admin') {
		register_callback('mem_akismet_discuss_save', 'discuss', 'discuss_save', 1);
	}
}

if (@txpinterface == 'admin') {
	add_privs('mem_akismet','1');

	register_callback('mem_akismet_uninstall', 'mem_akismet','uninstall');

	function mem_akismet_uninstall()
	{
		global $event,$siteurl;
		safe_delete('txp_prefs', "`name` LIKE 'mem_akismet%'");

		$event = 'plugin';
		pagetop('','');

		echo '<div style="text-align:center;"><h1>mem_akismet Uninstallation</h1>' .
			'<div>Settings deleted.</div><br /><br />' .
			'<form method="post" action="index.php" onsubmit="' ."return confirm('Really delete?');". '"><input type="submit" name="" value="Remove mem_akismet plugin" class="smallerbox" /><input type="hidden" name="event" value="plugin" /><input type="hidden" name="step" value="plugin_delete" /><input type="hidden" name="name" value="mem_akismet" /></form></div>';
	}
}

function mem_akismet_get_err_text($akismet)
{
	if ($akismet->errorsExist())
	{
 		if($akismet->isError('AKISMET_INVALID_KEY'))
 		{
 			return 'Invalid Akismet key';
 		}
 		elseif($akismet->isError('AKISMET_RESPONSE_FAILED'))
 		{
 			return 'Akismet response failed';
		}
		elseif($akismet->isError('AKISMET_SERVER_NOT_FOUND'))
		{
			return 'Akismet server not found';
		}
	}

	return '';
}

function mem_akismet_discuss_save()
{
	global $prefs, $siteurl;

	extract(doSlash(gpsa(array('email','name','web','message','ip'))));
	extract(array_map('assert_int',gpsa(array('discussid','visible','parentid'))));

	$comment = array(
		'author'	=> $name,
		'email'		=> $email,
		'website'	=> $web,
		'body'		=> $message,
		'user_ip'	=> $ip,
		'referrer'	=> '',
		//'user_agent'	=> '', this is required
	);

	$apikey = @$prefs['mem_akismet_api_key'];
	$akismetServer = @$prefs['mem_akismet_spam_server'];

  $akismet = new Akismet($siteurl, $apikey, $comment, $akismetServer);

	if (!$akismet->errorsExist())
	{
		$rs = safe_row('email, name, visible', 'txp_discuss', "discussid = $discussid");

		if ($rs)
		{
			// moderated || visible --> spam, notify akismet of uncaught spam
			if ($visible == SPAM && ($rs['visible'] == VISIBLE || $rs['visible'] == MODERATE)) {
				$akismet->submitSpam();
			}
			// moderated || spam --> visible, notify akismet of false positive
			if ($visible == VISIBLE && ($rs['visible'] == SPAM || $rs['visible'] == MODERATE)) {
				$akismet->submitHam();
			}
		}
	}
	else
	{
		$err = mem_akismet_get_err_text($akismet);
		trigger_error("mem_akismet: " + $err, E_USER_WARNING);
	}
}



function mem_akismet_check()
{
	global $siteurl,$prefs;

	// If you wanted to check the regular comment-form variables, we we would use:
	extract(getComment());

	$comment = array(
		'author'	=> $name,
		'email'		=> $email,
		'website'	=> $web,
		'body'		=> $message,
		'permalink'	=> $backpage
	);

	$apikey = @$prefs['mem_akismet_api_key'];

  $akismet = new Akismet($siteurl, $apikey, $comment);

	// We get the evaluator instance. You always need this.
	$evaluator =& get_comment_evaluator();

	// moderate spam on akismet error
	if ($akismet->errorsExist())
	{
		$evaluator->add_estimate(MODERATE, 0.2, 'Akismet error.');
	}
	else
	{
		if ($akismet->isSpam()) {
			$evaluator->add_estimate(SPAM, 0.75, 'Akismet says spam');
		} else {
			$evaluator->add_estimate(VISIBLE, 0.75, 'Akismet didn\'t flag');
		}
	}
}


if (!class_exists('AkismetObject')) {
	/* Borrowed Akismet php class mentioned in help
	 * @author Bret Kuhns {@link www.miphp.net}
	 * @link http://www.miphp.net/blog/view/new_akismet_class/
	 * @version 0.3.4
	 * @license http://www.opensource.org/licenses/mit-license.php MIT License
	 */

	// Error constants
	define("AKISMET_SERVER_NOT_FOUND",	0);
	define("AKISMET_RESPONSE_FAILED",	1);
	define("AKISMET_INVALID_KEY",		2);

	// Base class to assist in error handling between Akismet classes
	class AkismetObject {
		var $errors = array();

		/**
		 * Add a new error to the errors array in the object
		 *
		 * @param	String	$name	A name (array key) for the error
		 * @param	String	$string	The error message
		 * @return void
		 */
		// Set an error in the object
		function setError($name, $message) {
			$this->errors[$name] = $message;
		}

		/**
		 * Return a specific error message from the errors array
		 *
		 * @param	String	$name	The name of the error you want
		 * @return mixed	Returns a String if the error exists, a false boolean if it does not exist
		 */
		function getError($name) {
			if($this->isError($name)) {
				return $this->errors[$name];
			} else {
				return false;
			}
		}

		/**
		 * Return all errors in the object
		 *
		 * @return String[]
		 */
		function getErrors() {
			return (array)$this->errors;
		}

		/**
		 * Check if a certain error exists
		 *
		 * @param	String	$name	The name of the error you want
		 * @return boolean
		 */
		function isError($name) {
			return isset($this->errors[$name]);
		}

		/**
		 * Check if any errors exist
		 *
		 * @return boolean
		 */
		function errorsExist() {
			return (count($this->errors) > 0);
		}
	}

	// Used by the Akismet class to communicate with the Akismet service
	class AkismetHttpClient extends AkismetObject {
		var $akismetVersion = '1.1';
		var $con;
		var $host;
		var $port;
		var $apiKey;
		var $blogUrl;
		var $errors = array();

		// Constructor
		function AkismetHttpClient($host, $blogUrl, $apiKey, $port = 80) {
			$this->host = $host;
			$this->port = $port;
			$this->blogUrl = $blogUrl;
			$this->apiKey = $apiKey;
		}

		// Use the connection active in $con to get a response from the server and return that response
		function getResponse($request, $path, $type = "post", $responseLength = 1160) {
			$this->_connect();

			if($this->con && !$this->isError(AKISMET_SERVER_NOT_FOUND)) {
				$request  =
						strToUpper($type)." /{$this->akismetVersion}/$path HTTP/1.1\r\n" .
						"Host: ".((!empty($this->apiKey)) ? $this->apiKey."." : null)."{$this->host}\r\n" .
						"Content-Type: application/x-www-form-urlencoded; charset=utf-8\r\n" .
						"Content-Length: ".strlen($request)."\r\n" .
						"User-Agent: Akismet PHP4 Class\r\n" .
						"\r\n" .
						$request
					;
				$response = "";

				@fwrite($this->con, $request);

				while(!feof($this->con)) {
					$response .= @fgets($this->con, $responseLength);
				}

				$response = explode("\r\n\r\n", $response, 2);
				return $response[1];
			} else {
				$this->setError(AKISMET_RESPONSE_FAILED, "The response could not be retrieved.");
			}

			$this->_disconnect();
		}

		// Connect to the Akismet server and store that connection in the instance variable $con
		function _connect() {
			if(!($this->con = @fsockopen($this->host, $this->port))) {
				$this->setError(AKISMET_SERVER_NOT_FOUND, "Could not connect to akismet server.");
			}
		}

		// Close the connection to the Akismet server
		function _disconnect() {
			@fclose($this->con);
		}
	}

	// The controlling class. This is the ONLY class the user should instantiate in
	// order to use the Akismet service!
	class Akismet extends AkismetObject {
		var $apiPort = 80;
		var $akismetServer = 'rest.akismet.com';
		var $akismetVersion = '1.1';
		var $http;

		var $ignore = array(
				'HTTP_COOKIE',
				'HTTP_X_FORWARDED_FOR',
				'HTTP_X_FORWARDED_HOST',
				'HTTP_MAX_FORWARDS',
				'HTTP_X_FORWARDED_SERVER',
				'REDIRECT_STATUS',
				'SERVER_PORT',
				'PATH',
				'DOCUMENT_ROOT',
				'SERVER_ADMIN',
				'QUERY_STRING',
				'PHP_SELF',
				'argv'
			);

		var $blogUrl = "";
		var $apiKey  = "";
		var $comment = array();


		/**
		 * Constructor
		 *
		 * Set instance variables, connect to Akismet, and check API key
		 *
		 * @param	String	$blogUrl	The URL to your own blog
		 * @param 	String	$apiKey		Your wordpress API key
		 * @param 	String[]	$comment	A formatted comment array to be examined by the Akismet service
		 * @return	Akismet
		 */
		function Akismet($blogUrl, $apiKey, $comment = array(), $akismetServer = 'rest.akismet.com') {
			$this->blogUrl = $blogUrl;
			$this->apiKey  = $apiKey;
			$this->setComment($comment);
			$this->akismetServer = empty($akismetServer) ? $this->akismetServer : $akismetServer;

			// Connect to the Akismet server and populate errors if they exist
			$this->http = new AkismetHttpClient($this->akismetServer, $blogUrl, $apiKey);
			if($this->http->errorsExist()) {
				$this->errors = array_merge($this->errors, $this->http->getErrors());
			}

			// Check if the API key is valid
			if(!$this->_isValidApiKey($apiKey)) {
				$this->setError(AKISMET_INVALID_KEY, "Your Akismet API key is not valid.");
			}
		}


		/**
		 * Query the Akismet and determine if the comment is spam or not
		 *
		 * @return	boolean
		 */
		function isSpam() {
			$response = $this->http->getResponse($this->_getQueryString(), 'comment-check');

			return ($response == "true");
		}


		/**
		 * Submit this comment as an unchecked spam to the Akismet server
		 *
		 * @return	void
		 */
		function submitSpam() {
			$this->http->getResponse($this->_getQueryString(), 'submit-spam');
		}


		/**
		 * Submit a false-positive comment as "ham" to the Akismet server
		 *
		 * @return	void
		 */
		function submitHam() {
			$this->http->getResponse($this->_getQueryString(), 'submit-ham');
		}


		/**
		 * Manually set the comment value of the instantiated object.
		 *
		 * @param	Array	$comment
		 * @return	void
		 */
		function setComment($comment) {
			$this->comment = $comment;
			if(!empty($comment)) {
				$this->_formatCommentArray();
				$this->_fillCommentValues();
			}
		}


		/**
		 * Returns the current value of the object's comment array.
		 *
		 * @return	Array
		 */
		function getComment() {
			return $this->comment;
		}


		/**
		 * Check with the Akismet server to determine if the API key is valid
		 *
		 * @access	Protected
		 * @param	String	$key	The Wordpress API key passed from the constructor argument
		 * @return	boolean
		 */
		function _isValidApiKey($key) {
			$keyCheck = $this->http->getResponse("key=".$this->apiKey."&blog=".$this->blogUrl, 'verify-key');

			return ($keyCheck == "valid");
		}


		/**
		 * Format the comment array in accordance to the Akismet API
		 *
		 * @access	Protected
		 * @return	void
		 */
		function _formatCommentArray() {
			$format = array(
					'type' => 'comment_type',
					'author' => 'comment_author',
					'email' => 'comment_author_email',
					'website' => 'comment_author_url',
					'body' => 'comment_content'
				);

			foreach($format as $short => $long) {
				if(isset($this->comment[$short])) {
					$this->comment[$long] = $this->comment[$short];
					unset($this->comment[$short]);
				}
			}
		}


		/**
		 * Fill any values not provided by the developer with available values.
		 *
		 * @return	void
		 */
		function _fillCommentValues() {
			if(!isset($this->comment['user_ip'])) {
				$this->comment['user_ip'] = ($_SERVER['REMOTE_ADDR'] != getenv('SERVER_ADDR')) ? $_SERVER['REMOTE_ADDR'] : getenv('HTTP_X_FORWARDED_FOR');
			}
			if(!isset($this->comment['user_agent'])) {
				$this->comment['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
			}
			if(!isset($this->comment['referrer'])) {
				$this->comment['referrer'] = $_SERVER['HTTP_REFERER'];
			}
			if(!isset($this->comment['blog'])) {
				$this->comment['blog'] = $this->blogUrl;
			}
		}


		/**
		 * Build a query string for use with HTTP requests
		 *
		 * @access	Protected
		 * @return	String
		 */
		function _getQueryString() {
			foreach($_SERVER as $key => $value) {
				if(!in_array($key, $this->ignore)) {
					if($key == 'REMOTE_ADDR') {
						$this->comment[$key] = $this->comment['user_ip'];
					} else {
						$this->comment[$key] = $value;
					}
				}
			}

			$query_string = '';

			foreach($this->comment as $key => $data) {
				$query_string .= $key . '=' . urlencode(stripslashes($data)) . '&';
			}

			return $query_string;
		}


	}
} // end if class_defined