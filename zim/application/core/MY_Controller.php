<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class MY_Controller extends CI_Controller {
	
	function errorToSSO($level, $msg, $file, $line, $context) {
		$message = NULL;
		
		// do nothing when level is 0 or with @ (we don't care about error)
		if (0 == ($level & error_reporting())) {
			return;
		}
		
		//TODO move this log function to printerlog helper
		$json_context = json_encode($context);
		$message = strip_tags($msg . " in " . $file . " at " . $line. " with " . $json_context);
		$this->load->helper('printerlog');
		PrinterLog_logSSO($level, 500, $message);
		
		// just display error for simulator (develop staff), and return 503 for ajax call
		if ($this->config->item('simulator')) {
			$protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
			header($protocol . ' 503');
			var_dump(array(
					'level'		=> $level,
					'message'	=> $message,
			));
			die("error");
		}
		
		header('Location: /error');
		
		exit;
	}
	
	public function __construct() {
		global $CFG;
		
		parent::__construct();
// 		$this->load->helper(array('corestatus', 'url'));
		$this->load->helper('corestatus');
		
		// set proper error handler
		set_error_handler(array($this, 'errorToSSO'));
		
		// initialisation status files
		if (!CoreStatus_initialFile()) {
			$this->load->helper('printerlog');
			PrinterLog_logError('status files initialisation error when MY_Controller started', __FILE__, __LINE__);
			
			// let request failed
			$protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
			header($protocol . ' 500');
			header('Content-type: text/plain; charset=UTF-8');
			echo 'file initialisation error';
			exit;
		}
		
		// check tromboning autorisation
		if (CoreStatus_checkTromboning()) {
			$status_text = ERROR_REMOTE_REFUSE . ' ' . MyERRMSG(ERROR_REMOTE_REFUSE);
			
			$this->load->helper(array('printerlog', 'errorcode'));
			PrinterLog_logMessage('detected and refused tromboning connection', __FILE__, __LINE__);
			
			// let request failed
			$protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
			
			header($protocol . ' ' . $status_text);
			header('Content-type: text/plain; charset=UTF-8');
			echo $status_text; //'connection refused';
			exit;
		}
		
		// Workflow management
		if (CoreStatus_checkCallREST()) {
			// we place the control for REST web service in his own class
			// because there are the special error codes for REST web service
			// and we do not need them in normal condition
			return;
		}
		else if (CoreStatus_checkCallNoBlockPage()) {
			// always allow certains pages pass, for example, set host name service
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
			else if (CoreStatus_checkInUSB()) {
				if (CoreStatus_checkCallUSB($url_redirect)) {
					return; // we are calling the right page
				}
			}
			else if (CoreStatus_checkCallUSB()) {
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
			// check we are in runGcode debug interface
			else if (CoreStatus_checkCallDebug()) {
				// we always let these interfaces go for debug
				return;
			}
			// check working issue
			else if (!CoreStatus_checkInIdle($status_current)) {
				switch($status_current) {
					case CORESTATUS_VALUE_RECOVERY: //TODO finish and test me
						if (CoreStatus_checkCallRecovery($url_redirect)) {
							return; // we are calling the right page
						}
						break;
						
					case CORESTATUS_VALUE_PRINT:
						if (CoreStatus_checkCallPrinting($url_redirect)) {
							return; // we are calling the right page
						}
						break;
						
					case CORESTATUS_VALUE_CANCEL:
						if (CoreStatus_checkCallCanceling($url_redirect)) {
							return; // we are calling the right page
						}
						break;
						
					case CORESTATUS_VALUE_LOAD_FILA_L:
					case CORESTATUS_VALUE_LOAD_FILA_R:
						if (CoreStatus_checkCallloading($url_redirect)) {
							return; // we are calling the right page
						}
// 						return; // we do not block users when charging filament
						break;
						
					case CORESTATUS_VALUE_UNLOAD_FILA_L:
					case CORESTATUS_VALUE_UNLOAD_FILA_R:
						//FIXME finish here to block users
						if (CoreStatus_checkCallUnloading($url_redirect)) {
							return; // we are calling the right page
						}
						break;
						
					case CORESTATUS_VALUE_SLICE:
						if (CoreStatus_checkCallSlicing($url_redirect)) {
							return;
						}
						break;
						
					default:
						$url_redirect = '/'; // internal error, never reach here normally
						break;
				}
			}
			else {
				if (CoreStatus_checkCallPrintingAjax() || CoreStatus_checkCallCancelingAjax()) {
					// let ajax request failed when we finishing printing / canceling
					$protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
					header($protocol . ' 403');
					header('Content-type: text/plain; charset=UTF-8');
					echo 'Not in printing / canceling';
					exit;
				}
				else if (CoreStatus_checkCallPrinting()) {
					$url_redirect = '/';
				}
				
				if ($url_redirect) {
					header('Location: ' . $url_redirect);
					exit;
				}
				return; // continue to generate the current page
			}
			
			// log error if we have no redirect url when reaching here
			if (is_null($url_redirect)) {
				$this->load->helper('printerlog');
				PrinterLog_logError('no redirect place when MY_Controller finished', __FILE__, __LINE__);
			}
			
			header('Location: ' . $url_redirect);
			exit;
		}

	}
}