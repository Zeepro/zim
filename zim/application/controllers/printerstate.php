<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

class Printerstate extends MY_Controller {
	function __construct() {
		parent::__construct ();
		$this->load->helper( array(
				'printerstate',
				'url',
				'json'
		) );
	}
	
	private function _display_changecartridge_base($template_name, $template_data) {
		$this->load->library('parser');
		
		$this->parser->parse($template_name, $template_data);
		
		return;
	}
	
	private function _display_changecartridge_wait_unload_filament() {
		$this->lang->load('printerstate/changecartridge', $this->config->item('language'));
		$template_data = array (
				'next_phase'	=> PRINTERSTATE_CHANGECART_UNLOAD_F,
				'unload_button'	=> t('Unload'),
		);
		$template_name = 'template/printerstate/changecartridge_ajax/wait_unload_filament';
		$this->_display_changecartridge_base($template_name, $template_data);
		
		$this->output->set_status_header(202); // disable checking
		
		return;
	}
	
	private function _display_changecartridge_in_unload_filament() {
		$this->lang->load('printerstate/changecartridge', $this->config->item('language'));
		$template_data = array (
				'next_phase'	=> PRINTERSTATE_CHANGECART_REMOVE_C,
				'unload_info'	=> t('Wait for unloading...'),
		);
		$template_name = 'template/printerstate/changecartridge_ajax/in_unload_filament';
		$this->_display_changecartridge_base($template_name, $template_data);
		
		return;
	}
	
	private function _display_changecartridge_remove_cartridge($low_hint = FALSE) {
		$this->lang->load('printerstate/changecartridge', $this->config->item('language'));
		$template_data = array (
				'next_phase'	=> ($low_hint) ? PRINTERSTATE_CHANGECART_REINST_C : PRINTERSTATE_CHANGECART_INSERT_C,
				'low_hint'		=> ($low_hint) ? t('Not enough filament') : '',
		);
		$template_name = 'template/printerstate/changecartridge_ajax/remove_cartridge';
		$this->_display_changecartridge_base($template_name, $template_data);
		
		return;
	}
	
	private function _display_changecartridge_insert_cartridge() {
		$template_data = array (
				'next_phase'	=> PRINTERSTATE_CHANGECART_LOAD_F,
		);
		$template_name = 'template/printerstate/changecartridge_ajax/insert_cartridge';
		$this->_display_changecartridge_base($template_name, $template_data);
		
		return;
	}
	
	private function _display_changecartridge_wait_load_filament($change_able = TRUE) {
		$this->lang->load('printerstate/changecartridge', $this->config->item('language'));
		if ($change_able == TRUE) {
			$template_data = array (
					'next_phase'	=> PRINTERSTATE_CHANGECART_WAIT_F_C,
					'load_button'	=> t('Load'),
					'change_button'	=> t('Change'),
			);
			$template_name = 'template/printerstate/changecartridge_ajax/wait_load_change_filament';
		}
		else {
			$template_data = array (
					'next_phase'	=> PRINTERSTATE_CHANGECART_WAIT_F,
					'load_button'	=> t('Load'),
			);
			$template_name = 'template/printerstate/changecartridge_ajax/wait_load_filament';
			
		}
		$this->_display_changecartridge_base($template_name, $template_data);
		
		$this->output->set_status_header(202); // disable checking
		
		return;
	}
	
	private function _display_changecartridge_in_load_filament() {
		$this->lang->load('printerstate/changecartridge', $this->config->item('language'));
		$template_data = array (
				'next_phase'	=> PRINTERSTATE_CHANGECART_NEED_P,
				'load_info'		=> t('Waiting for loading...'),
		);
		$template_name = 'template/printerstate/changecartridge_ajax/in_load_filament';
		$this->_display_changecartridge_base($template_name, $template_data);
		
		return;
	}
	
	private function _display_changecartridge_need_prime($abb_cartridge, $id_model) {
		$this->lang->load('printerstate/changecartridge', $this->config->item('language'));
		$yes_url = '/printdetail/printmodel?id=';
		$no_url = '';
		
		$this->load->helper('printlist');
		if ($abb_cartridge == 'r') {
			$yes_url .= ModelList_codeModelHash(PRINTLIST_MODEL_PRIME_R);
		}
		else {
			$yes_url .= ModelList_codeModelHash(PRINTLIST_MODEL_PRIME_L);
		}
		if ($id_model) {
			$no_url = '/printmodel/detail?id=' . $id_model;
			$yes_url .= '&cb=' . $id_model;
		}
		else {
			$no_url = '/';
		}
		
		$template_data = array (
				'next_phase'	=> PRINTERSTATE_CHANGECART_FINISH,
				'question'		=> t('Need prime?'),
				'yes_button'	=> t('Yes'),
				'no_button'		=> t('No'),
				'yes_url'		=> $yes_url,
				'no_url'		=> $no_url,
		);
		$template_name = 'template/printerstate/changecartridge_ajax/need_prime';
		$this->_display_changecartridge_base($template_name, $template_data);
		
		$this->output->set_status_header(202); // disable checking
		
		return;
	}
	
	public function index() {
		$template_data = array();
		$body_page = NULL;
		
// 		$this->changecartridge();
		$this->load->library('parser');
		$this->lang->load('printerstate/index', $this->config->item('language'));

		// parse the main body
		$template_data = array(
				'reset_network'	=> t('Reset network'),
				'back'			=> t('back'),
		);
		
		$body_page = $this->parser->parse('template/printerstate/index', $template_data, TRUE);
		
		// parse all page
		$template_data = array(
				'lang'			=> $CFG->config ['language_abbr'],
				'headers'		=> '<title>' . t('ZeePro Personal Printer 21 - Configuration') . '</title>',
				'contents'		=> $body_page,
		);
		
		$this->parser->parse('template/basetemplate', $template_data);
		
		return;
	}
	
	public function changecartridge() {
		global $CFG;
		$template_data = array();
		$body_page = NULL;
		
		$abb_cartridge = $this->input->get('v');
		$need_filament = $this->input->get('f');
		$id_model = $this->input->get('id');
		
		if (!$abb_cartridge && !$need_filament && !in_array($abb_cartridge, array('l', 'r'))) {
			if (isset($_SERVER['HTTP_REFERER'])) {
				$this->output->set_header('Location: ' . $_SERVER['HTTP_REFERER']);
			}
			else {
				$this->output->set_header('Location: /');
			}
			return;
		}
		
		$this->load->library('parser');
		$this->lang->load('printerstate/changecartridge', $this->config->item('language'));
		
		// parse the main body
		$template_data = array(
				'title'			=> ($abb_cartridge == 'l') ? t('Left cartridge change') : t('Right cartridge change'),
				'wait_info'		=> t('Waiting for getting information...'),
				'first_status'	=> PRINTERSTATE_CHANGECART_UNLOAD_F,
				'insert_status'	=> PRINTERSTATE_CHANGECART_INSERT_C,
				'back'			=> t('back'),
				'abb_cartridge'	=> $abb_cartridge,
				'need_filament'	=> $need_filament,
				'id_model'		=> $id_model,
		);
		
		$body_page = $this->parser->parse('template/printerstate/changecartridge', $template_data, TRUE);
		
		// parse all page
		$template_data = array(
				'lang'			=> $CFG->config ['language_abbr'],
				'headers'		=> '<title>' . t('ZeePro Personal Printer 21 - Change cartridge') . '</title>',
				'contents'		=> $body_page,
		);
		
		$this->parser->parse('template/basetemplate', $template_data);
		
		return;
	}
	
	public function changecartridge_ajax() {
		global $CFG;
		$template_data = array();
		$body_page = NULL;
		$ret_val = 0;
		
		$abb_cartridge = $this->input->post('abb_cartridge');
		$need_filament = $this->input->post('need_filament');
		$id_model = $this->input->post('mid');
		$next_phase = $this->input->post('next_phase');
		$code_miss_cartridge = ($abb_cartridge == 'r') ? ERROR_MISS_RIGT_CART : ERROR_MISS_LEFT_CART;
		$code_low_filament = ($abb_cartridge == 'r') ? ERROR_LOW_RIGT_FILA : ERROR_LOW_LEFT_FILA;
		$code_miss_filament = ($abb_cartridge == 'r') ? ERROR_MISS_RIGT_FILA : ERROR_MISS_LEFT_FILA;
		$low_hint = FALSE;
		$change_able = TRUE;
		
		// treat input data
		if (!$abb_cartridge && !in_array($abb_cartridge, array('l', 'r'))) {
			if (isset($_SERVER['HTTP_REFERER'])) {
				$this->output->set_header('Location: ' . $_SERVER['HTTP_REFERER']);
			}
			else {
				$this->output->set_header('Location: /');
			}
			return;
		}
		if ($need_filament) {
			$need_filament = (int)$need_filament;
		}
		else {
			$need_filament = 0;
		}
		
		$this->load->helper(array('corestatus'));
		
		// detect status
		switch ($next_phase) {
			case PRINTERSTATE_CHANGECART_UNLOAD_F:
				// we call the page: wait unload filament, need checking status (first status page)
				if (PrinterState_getFilamentStatus($abb_cartridge)) {
					// have filament
					$status_current = '';
					$status_correct = ($abb_cartridge == 'r') ? CORESTATUS_VALUE_UNLOAD_FILA_R : CORESTATUS_VALUE_UNLOAD_FILA_L;
					$status_changed = ($abb_cartridge == 'r') ? CORESTATUS_VALUE_LOAD_FILA_R : CORESTATUS_VALUE_LOAD_FILA_L;
					
					if (CoreStatus_checkInIdle($status_current)) {
						// in idle
						$this->_display_changecartridge_wait_unload_filament();
					}
					else if ($status_current == $status_correct) {
						// in busy (normally only unloading is possible)
						$this->_display_changecartridge_in_unload_filament();
					}
					else if ($status_current == $status_changed) {
						// in busy (but in idle, status is changed in real)
						$ret_val = CoreStatus_setInIdle();
						if ($ret_val == FALSE) {
							$this->load->helper('printerlog');
							PrinterLog_logError('can not set idle after unloading filament');
							$this->output->set_status_header(202); // disable checking
						}
					}
					else {
						// in other busy status
						$this->load->helper('printerlog');
						PrinterLog_logError('error status when changing filament');
						$this->output->set_status_header(202); // disable checking
					}
				}
				else {
					// no filament
					$status_current = '';
					$status_correct = ($abb_cartridge == 'r') ? CORESTATUS_VALUE_LOAD_FILA_R : CORESTATUS_VALUE_LOAD_FILA_L;
					$status_changed = ($abb_cartridge == 'r') ? CORESTATUS_VALUE_UNLOAD_FILA_R : CORESTATUS_VALUE_UNLOAD_FILA_L;
					
					if (CoreStatus_checkInIdle($status_current)) {
						$ret_val = PrinterState_checkFilament($abb_cartridge, $need_filament);
						if ($ret_val == $code_miss_filament) {
							// have cartridge, enough filament
							$this->_display_changecartridge_wait_load_filament();
						}
						else if ($ret_val == $code_low_filament) {
							// have cartridge, low filament
							$this->_display_changecartridge_remove_cartridge(TRUE);
						}
						else if ($ret_val == $code_miss_cartridge) {
							// no cartridge
							$this->_display_changecartridge_insert_cartridge();
						}
						else {
							// error status
							$this->load->helper('printerlog');
							PrinterLog_logError('error checkfilament return status when changing filament (in starting)');
							$this->_display_changecartridge_remove_cartridge();
						}
					}
					else if ($status_current == $status_correct) {
						// in busy (normally only loading is possible)
						$this->_display_changecartridge_in_load_filament();
					}
					else if ($status_current == $status_changed) {
						// in busy (but in idle, status is changed in real)
						$ret_val = CoreStatus_setInIdle();
						if ($ret_val == FALSE) {
							$this->load->helper('printerlog');
							PrinterLog_logError('can not set idle after loading filament');
							$this->output->set_status_header(202); // disable checking
						}
					}
					else {
						// in other busy status
						$this->load->helper('printerlog');
						PrinterLog_logError('error status when changing filament');
						$this->output->set_status_header(202); // disable checking
					}
					
				}
				break;
				
			case PRINTERSTATE_CHANGECART_REMOVE_C:
				// we call the page: in unload filament
				if (PrinterState_getFilamentStatus($abb_cartridge)) {
					// have filament
					$this->_display_changecartridge_in_unload_filament();
				}
				else {
					// no filament
					$ret_val = CoreStatus_setInIdle();
					if ($ret_val == FALSE) {
						$this->load->helper('printerlog');
						PrinterLog_logError('can not set idle after unloading filament');
						$this->output->set_status_header(202); // disable checking
					}
					$this->_display_changecartridge_remove_cartridge();
				}
				break;

			case PRINTERSTATE_CHANGECART_REINST_C:
				// we use switch breakdown to continue the treatement
				$low_hint = TRUE;
				
			case PRINTERSTATE_CHANGECART_INSERT_C:
				// we call the page: remove / reinsert cartridge
				$ret_val = PrinterState_checkFilament($abb_cartridge, $need_filament);
				if ($ret_val == $code_miss_cartridge) {
					// no cartridge
					$this->_display_changecartridge_insert_cartridge();
				}
				else if ($ret_val == $code_low_filament) {
					// have cartridge, low filament
					$this->_display_changecartridge_remove_cartridge(TRUE);
				}
				else if ($ret_val == $code_miss_filament) {
					// have cartridge, no filemant
					$this->_display_changecartridge_remove_cartridge($low_hint);
				}
				else {
					// error status
					$this->load->helper('printerlog');
					PrinterLog_logError('error checkfilament return status when changing filament (in removing)');
					$this->_display_changecartridge_remove_cartridge();
				}
				break;
				
			case PRINTERSTATE_CHANGECART_LOAD_F:
				// we call the page: insert cartridge
				$ret_val = PrinterState_checkFilament($abb_cartridge, $need_filament);
				if ($ret_val == $code_miss_filament) {
					$this->_display_changecartridge_wait_load_filament(FALSE);
				}
				else if ($ret_val == $code_low_filament) {
					$this->_display_changecartridge_remove_cartridge(TRUE);
				}
				else if ($ret_val == $code_miss_cartridge) {
					// no cartridge
					$this->_display_changecartridge_insert_cartridge();
				}
				else {
					// error status
					$this->load->helper('printerlog');
					PrinterLog_logError('error checkfilament return status when changing filament (in inserting)');
					$this->_display_changecartridge_remove_cartridge();
				}
				break;
				
			case PRINTERSTATE_CHANGECART_WAIT_F:
				// we use switch breakdown to continue the treatement
				$change_able = FALSE;
				
			case PRINTERSTATE_CHANGECART_WAIT_F_C:
				// we call the page: wait load filament / change cartridge
				if (CoreStatus_checkInIdle()) {
					// in idle
					$this->_display_changecartridge_wait_load_filament($change_able);
				}
				else {
					// in busy (normally only loading is possible)
					$this->_display_changecartridge_in_load_filament();
				}
				break;
				
			case PRINTERSTATE_CHANGECART_NEED_P:
				// we call the page: in load filament
				if (PrinterState_getFilamentStatus($abb_cartridge)) {
					// have filament
					$ret_val = CoreStatus_setInIdle();
					if ($ret_val == FALSE) {
						$this->load->helper('printerlog');
						PrinterLog_logError('can not set idle after loading filament');
						$this->output->set_status_header(202); // disable checking
					}
					$this->_display_changecartridge_need_prime($abb_cartridge, $id_model);
				}
				else {
					// no filament
					$this->_display_changecartridge_in_load_filament();
				}
				break;
				
			default:
				break;
		}
		
		$this->output->set_content_type('text/plain; charset=UTF-8');
		
		return;
	}
	
	public function changecartridge_action($mode = '') {
		$abb_cartridge = $this->input->get('v');
		
		if (!$abb_cartridge && !in_array($abb_cartridge, array('l', 'r'))) {
			if (isset($_SERVER['HTTP_REFERER'])) {
				$this->output->set_header('Location: ' . $_SERVER['HTTP_REFERER']);
			}
			else {
				$this->output->set_header('Location: /');
			}
			return;
		}
		
		switch ($mode) {
			case 'unload':
				$ret_val = PrinterState_unloadFilament($abb_cartridge);
				if ($ret_val != ERROR_OK) {
					$this->output->set_status_header($ret_val);
				}
				break;
				
			case 'load':
				$ret_val = PrinterState_loadFilament($abb_cartridge);
				if ($ret_val != ERROR_OK) {
					$this->output->set_status_header($ret_val);
				}
				break;
				
			default:
				$this->output->set_status_header(403); // unknown request
				break;
		}
		
		return;
	}
	
	public function resetnetwork() {
		$template_data = array();
		$body_page = NULL;
		$error = '';

		$this->load->library('parser');
		$this->lang->load('printerstate/resetnetwork', $this->config->item('language'));
		
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$this->load->helper('zimapi');
			
			if (ZimAPI_resetNetwork()) {
// 				$error = t('wait a moment');
// 				$this->output->set_header("Location:/connection");
		
				// parse the main body
				$template_data = array(
						'hint'		=> t('Connect to the new network of printer, then press OK'),
						'ok_button'	=> t('OK'),
				);
				
				$body_page = $this->parser->parse('template/printerstate/resetnetwork_finish', $template_data, TRUE);
				
				// parse all page
				$template_data = array(
						'lang'			=> $CFG->config ['language_abbr'],
						'headers'		=> '<title>' . t('ZeePro Personal Printer 21 - Reset connection') . '</title>',
						'contents'		=> $body_page,
				);
				
				$this->parser->parse('template/basetemplate', $template_data);
				
				return;
			}
			else {
				$error = t('Reset error');
			}
			
		}
		
		// parse the main body
		$template_data = array(
				'hint'			=> t('Press to reset network'),
				'reset_button'	=> t('Reset'),
				'error'			=> $error,
				'back'			=> t('back'),
		);
		
		$body_page = $this->parser->parse('template/printerstate/resetnetwork', $template_data, TRUE);
		
		// parse all page
		$template_data = array(
				'lang'			=> $CFG->config ['language_abbr'],
				'headers'		=> '<title>' . t('ZeePro Personal Printer 21 - Reset connection') . '</title>',
				'contents'		=> $body_page,
		);
		
		$this->parser->parse('template/basetemplate', $template_data);
		
		return;
	}
}
