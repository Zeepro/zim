<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Set_cookie extends ZP_Controller {
	public function __construct() {
		parent::__construct();
		
		// initialize session
		$this->load->helper('userauth');
		if (!UserAuth_initialSession()) {
			$this->_displayDenied('Session initialization error');
		}
		
		return;
	}
	
	private function _displayDenied($extra = NULL) {
		$this->_exitWithError500($extra, 403);
		
		return; // never reach here
	}
	
	private function _doUserAuth($user_token) {
		if (UserAuth_getUserAccess($user_token)) {
// 			$this->output->set_header('Location: /');
			return TRUE;
		}
		else {
			$this->_displayDenied('Please re-visit ' . USERAUTH_URL_REDIRECTION);
		}
		
		return FALSE;
	}
	
	private function _doRedirectURL($array_redirect) {
		$redirect_url = '/';
		
		if (is_array($array_redirect) && isset($array_redirect['url'])) {
			$redirect_url = $array_redirect['url'] . '?from=redirect';
			
			// treat get parameter
			if (isset($array_redirect['prm']) && is_array($array_redirect['prm'])) {
				foreach ($array_redirect['prm'] as $prm_key => $prm_val) {
					$redirect_url .= '&' . $prm_key . '=' . $prm_val;
				}
			}
			// treat cookie parameter
			if (isset($array_redirect['cookie']) && is_array($array_redirect['cookie'])) {
				$array_cookie = array();
				
				foreach ($array_redirect['cookie'] as $cookie_key => $cookie_value) {
					$array_cookie[$cookie_key] = $cookie_value;
				}
				$this->input->set_cookie('redirectData', json_encode($array_cookie), 60); // 1 min
			}
			
			// filter outside redirection
			if ($redirect_url[0] != '/') {
				$redirect_url = '/' . $redirect_url;
			}
		}
		
		return $redirect_url;
	}
	
	public function index() {
		$this->load->helper('printerlog');
		
		if ($this->input->server('REQUEST_METHOD') == 'POST') {
			$token_post = $this->input->post('token');
			$token_json = json_decode($token_post, TRUE);
				
			PrinterLog_logDebug('remote token: ' . $token_post);
			if (is_array($token_json) && isset($token_json['token'])) {
				$redirect_url = '/';
				
				// new token system
				$this->input->set_cookie('auth', $token_json['token'], 0);
				$this->input->set_cookie('token_system', 'new', 1800); // 30 mins
				
				if (isset($token_json['redirect'])) {
					$redirect_url = $this->_doRedirectURL($token_json['redirect']);
				}
				
				if (!isset($token_json['user'])
				|| !$this->_doUserAuth($token_json['user'])) {
					$this->_displayDenied('No user token found');
				}
				
				$this->output->set_header('Location: ' . $redirect_url);
			}
			else {
				// old token system
				$this->input->set_cookie('auth', $token_post, 0);
				$this->input->set_cookie('token_system', 'old', 1800); // 30 mins
				
// 				$this->output->set_header('Location: /');
				$this->_displayDenied('Old token system is out of support');
			}
		}
		else {
			PrinterLog_logError("SetCookie: method != POST");
			$this->_displayDenied();
		}
		
		return;
	}
	
	public function user() {
		if ($this->input->server('REQUEST_METHOD') == 'POST'
				&& $this->_doUserAuth($this->input->post('user'))) {
			$redirect_url = $this->_doRedirectURL(json_decode($this->input->post('redirect'), TRUE));
			
			$this->output->set_header('Location: ' . $redirect_url);
		}
		else {
			if (UserAuth_checkSessionExist()) {
				$this->output->set_status_header(200);
			}
			else {
				$this->output->set_status_header(403);
			}
		}
		
		return;
	}
	
	public function test_auth() {
		$_SESSION[USERAUTH_TITLE_TOKEN] = 'ok'; // wrong token, but it passed verification locally
		$_SESSION[USERAUTH_TITLE_ACCESS] = USERAUTH_FLAG_ALL_ACCESS; // need to change to correct access
		
		$this->output->set_header('Location: /');
		
		return;
	}
	
	public function clean_auth() {
		UserAuth_removeSessionData();
		
		return;
	}
}