<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class MY_Controller extends CI_Controller
{	
	function errorToSSO($level, $msg, $file, $line, $context)
	{
		$this->load->helper('zimapi');
		$json_context = json_encode($context);
		$url = 'https://sso.zeepro.com/errorlog.ashx';
		$data = array(	'printersn' => ZimAPI_getSerial(),
				'printertime' => date("Y-m-d H:i:s\Z", time()),
				'level' => $level,
				'code' => 500,
				'message' => $msg . " in $file at $line with $json_context");
	
		$options = array('http' => array('header'  => "Content-type: application/x-www-form-urlencoded\r\n",
				'method'  => 'POST',
				'content' => http_build_query($data)));
		$context  = stream_context_create($options);
		file_get_contents($url, false, $context);
		return ;
	}
	
	public function __construct() {
		global $CFG;
		
		parent::__construct();
// 		$this->load->helper(array('corestatus', 'url'));
		$this->load->helper('corestatus');

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
						return; // we do not block users when charging filament
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