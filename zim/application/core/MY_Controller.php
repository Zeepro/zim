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
			$status_current = '';
			$url_redirect = '';
			
			// check initialization issue
			if (CoreStatus_checkInInitialization()) {
				if (CoreStatus_checkCallInitialization($url_redirect)) {
					return; // we are calling the right page
				}
			}
			else if (CoreStatus_checkCallInitialization()) {
				$url_redirect = '/';
			}
			// check connection issue
			else if (CoreStatus_checkInConnection()) {
				if (CoreStatus_checkCallConnection($url_redirect)) {
					return; // we are calling the right page
				}
			}
			else if (CoreStatus_checkCallConnection()) {
				$url_redirect = '/';
			}
			// check working issue
			else if (!CoreStatus_checkInIdle($status_current)) {
				switch($status_current) {
					case CORESTATUS_VALUE_PRINT:
						if (CoreStatus_checkCallPrinting($url_redirect)) {
							return; // we are calling the right page
						}
						break;
						
					default:
						$url_redirect = '/'; // internal error, never reach here normally
						break;
				}
			}
			else {
				return; // continue to generate the current page
			}
			
// 			redirect($url_redirect);
// 			return;
			header('Location: ' . $url_redirect);
			exit;
		}

	}

}