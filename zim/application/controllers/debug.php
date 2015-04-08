<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Debug extends ZP_Controller {
	public function index() {
		$template_data = array();
		$level_log = $this->config->item('log_level');
		
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$ret_val = 0;
			$level_set = (int) $this->input->post('level');
				
			if ($level_set < 4 && $level_set >= 0) {
				$this->load->helper('corestatus');
				$ret_val = CoreStatus_setDebugLevel($level_set);
				if ($ret_val == TRUE) {
					$level_log = $level_set;
				}
			}
		}
		
		switch ($level_log) {
			case 0:
				$level_log .= ' None';
				break;
				
			case 1:
				$level_log .= ' Error';
				break;
				
			case 2:
				$level_log .= ' Message';
				break;
				
			case 3:
				$level_log .= ' Debug';
				break;
				
			default:
				break; // never reach here
		}
		
		$this->load->library('parser');
		$this->lang->load('debug', $this->config->item('language'));
		
		$template_data = array(
				'level'				=> $level_log,
				'txt_level_current'	=> t('txt_level_current'),
				'txt_level_set'		=> t('txt_level_set'),
				'level_array'		=> array(
						array('value' => 3, 'name' => 'Debug'),
						array('value' => 2, 'name' => 'Message'),
						array('value' => 1, 'name' => 'Error'),
						array('value' => 0, 'name' => 'None'),
				),
		);
		
		$this->_parseBaseTemplate(t('debug_index_pagetitle'),
				$this->parser->parse('debug', $template_data, TRUE));
		
		return;
	}
	
}