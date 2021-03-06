<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class ZP_Controller extends CI_Controller {
	protected function _sendFileContent($file_path = NULL, $client_name = 'download.bin', $force_download = TRUE) {
		if (file_exists($file_path)) {
			$encoding_header = isset($_SERVER['HTTP_ACCEPT_ENCODING']) ? $_SERVER['HTTP_ACCEPT_ENCODING'] : NULL;
			$support_compress = strpos($encoding_header, 'gzip') !== FALSE;
			$content_type = 'application/octet-stream';
			
			if ($force_download == FALSE) {
				$this->load->helper('file');
				$type_by_extension = get_mime_by_extension($file_path);
				if ($type_by_extension != FALSE) $content_type = $type_by_extension;
			}
			
			header('Content-Description: File Transfer');
			header('Content-Type: ' . $content_type);
			header('Content-Disposition: attachment; filename=' . $client_name);
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
// 			header('Content-Length: ' . filesize($file_path));
			
			if ($support_compress && extension_loaded('zlib')
			&& (ini_get('output_handler') != 'ob_gzhandler')) {
				ini_set("zlib.output_compression", 1);
			}
			
			readfile($file_path);
			
			exit;
		}
		else {
			$this->_exitWithError500();
		}
		
		return; // never reach here
	}
	
	protected function _exitWithError500($display = NULL, $code = 500) {
		if (is_null($display)) {
			$display = 'throw system internal error';
		}
		
		$this->output->set_status_header($code);
		
		// optional
		$this->load->library('parser');
		$this->output->set_content_type('Content-type: text/plain; charset=UTF-8');
		$this->parser->parse('plaintxt', array('display' => $display));
		$this->output->_display();
		
		exit;
	}
	
	protected function _parseBaseTemplate($title, $content, $extra_header = NULL) {
		$this->load->library('parser');
		$this->lang->load('base', $this->config->item('language'));
		
		$template_data = array(
				'lang'			=> $this->config->item('language_abbr'),
				'headers'		=> '<title>' . $title . "</title>\n" . $extra_header,
				'contents'		=> $content,
				// navigator menu
				'back'			=> t('back'),
				'home'			=> t('home'),
		);
		
		$this->parser->parse('basetemplate', $template_data);
		
		return;
	}
	
	public function __construct() {
		global $CFG;
		global $PRINTER;
		
		parent::__construct();
		
		$this->load->helper('corestatus');
		
		// initialize status files
		if (!CoreStatus_initialFile()) {
			$this->load->helper('printerlog');
			PrinterLog_logError('status files initialisation error when MY_Controller started', __FILE__, __LINE__);
				
			// let request failed
			$this->_exitWithError500('file initialisation error');
		}
		
		// initialize printer variable
		if (!is_array($PRINTER)) $PRINTER = array();
		
		return;
	}
}

class MY_Controller extends ZP_Controller {
	public function errorToSSO($level, $msg, $file, $line, $context) {
		$message = NULL;
		
		// do nothing when level is 0 or with @ (we don't care about error)
		if (0 == ($level & error_reporting())) {
			return;
		}
		
		//TODO move this log function to printerlog helper
		$json_context = @json_encode($context);
		$message = strip_tags($msg . " in " . $file . " at " . $line. " with " . $json_context);
		$this->load->helper('printerlog');
		PrinterLog_logDebug('ErrorHandler ' . $level . ': ' . $message);
		
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
		else {
			PrinterLog_logSSO($level, 500, $message);
		}
		
		header('Location: /error');
		
		exit;
	}
	
	public function __construct() {
		global $CFG;
		
		parent::__construct();
// 		$this->load->helper(array('corestatus', 'url'));
		$this->load->helper(array('corestatus', 'userauth', 'printerlog'));
		
		// set proper error handler
		set_error_handler(array($this, 'errorToSSO'));
		
		// add header to disable cache (IE need these headers)
		$this->output->set_header('Pragma: no-cache');
		$this->output->set_header('Cache-Control: no-cache');
		
		// check tromboning autorisation
		if (CoreStatus_checkTromboning(FALSE)) {
			$this->load->helper(array('printerlog', 'errorcode'));
			PrinterLog_logMessage('detected and refused tromboning connection', __FILE__, __LINE__);
			
			// let request failed
			$this->_exitWithError500(ERROR_REMOTE_REFUSE . ' ' . MyERRMSG(ERROR_REMOTE_REFUSE), ERROR_REMOTE_REFUSE);
		}
		
		// initilize user session for authentification
		if (!UserAuth_initialSession()) {
			$this->load->helper('printerlog');
			PrinterLog_logError('user session initialisation error when MY_Controller started', __FILE__, __LINE__);
			
			// let request failed
			$this->_exitWithError500('user session error');
		}
		
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
			$array_status = array();
			
			// stats info (do not stats rest, app can initialize cookies in each request)
			$this->load->library('session');
			if (FALSE === $this->input->cookie('stats_browserLog')) {
				$this->input->set_cookie('stats_browserLog', 'ok', 2592000); // 30 days for browser stats
				PrinterLog_statsWebAgent();
			}
			
			// check user authentification
			if (!UserAuth_checkSessionExist()) {
				$this->load->helper('printerlog');
				PrinterLog_logError('Session expired or wrong');
				
				$url_redirect = USERAUTH_URL_REDIRECTION;
			}
			// check initialization issue
			else if (CoreStatus_checkInInitialization()) {
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
			// check debug interface
			else if (CoreStatus_checkCallDebug()) {
				// we always let these interfaces go for debug
				return;
			}
			// check connection issue
			else if (CoreStatus_checkInConnection()) {
				if (CoreStatus_checkCallNoBlockPageInConnection()) {
					return; // we are calling set hostname, activation or account page
				}
				if (CoreStatus_checkCallConnection($url_redirect)) {
					return; // we are calling the right page
				}
			}
			else if (CoreStatus_checkCallConnection()) {
				$url_redirect = '/';
			}
			// check working issue
			else if (!CoreStatus_checkInIdle($status_current, $array_status)) {
				switch($status_current) {
					case CORESTATUS_VALUE_RECOVERY: //TODO finish and test me
						if (CoreStatus_checkCallRecovery($url_redirect)) {
							return; // we are calling the right page
						}
						break;
						
					case CORESTATUS_VALUE_PRINT:
						if (CoreStatus_checkCallPrinting($array_status, $url_redirect)) {
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
				else if (CoreStatus_checkCallPrinting() || CoreStatus_checkCallCanceling()) {
					$url_redirect = '/';
				}
				else {
					if (CoreStatus_checkInPrinted()) {
						if (CoreStatus_checkCallEndPrinting($url_redirect) || CoreStatus_checkCallEndPrintingPlus()) {
							return;
						}
					}
					else if (CoreStatus_checkCallEndPrinting()) {
						$url_redirect = '/'; // redirect to homepage when we have no timelapse
					}
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