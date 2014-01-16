<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class MY_Controller extends CI_Controller {

	public function __construct() {
		global $CFG;
		
		parent::__construct();
// 		$this->load->helper(array('corestatus', 'url'));
		$this->load->helper('corestatus');
		
		// Workflow management
		if (CoreStatus_checkCallREST()) {
			// we place the control for REST web service in his own class
			// because there are the special error codes for REST web service
			// and we do not need them in normal condition
			return;
		}
		else {
			$url_redirect = '';
			
			// check initialization issue
			if (CoreStatus_checkInInitialization()) {
				if (!CoreStatus_checkCallInitialization()) {
// 					redirect('/initialization');
//					return;
					header('Location: /initialization');
					exit;
				}
			}
			else if (CoreStatus_checkCallInitialization()) {
// 				redirect('/');
// 				return;
				header('Location: /');
				exit;
			}
			
			// check connection issue
			if (CoreStatus_checkInConnection()) {
				if (!CoreStatus_checkCallConnection()) {
// 					redirect('/connection');
// 					return;
					header('Location: /connection');
					exit;
				}
			}
			else if (CoreStatus_checkCallConnection()) {
// 				redirect('/');
// 				return;
				header('Location: /');
				exit;
			}
			
			// check working issue
			if (!CoreStatus_checkInIdle($url_redirect) && $url_redirect
					&& $this->router->uri->uri_string != $url_redirect) {
// 				redirect($url_redirect);
// 				return;
				header('Location: ' . $url_redirect);
				exit;
			}
		}

	}

}