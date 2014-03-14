<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Test_cartridge extends CI_Controller {

	public function index() {
		$this->output->set_header('Location: /test_cartridge/');
		
		return;
	}
	
	public function remove($abb_cartridge = '') {
		$ret_val = 0;
		$display = NULL;
		$parameter = '';
		$output = array();
		$arcontrol_fullpath = $this->config->item('arcontrol_c');
		
		$this->load->helper('printerstate');
		
		switch (strtolower($abb_cartridge)) {
			case 'l':
				$parameter = ' -rmctl';
				break;
				
			case 'r':
				$parameter = ' -rmctr';
				break;
				
			default:
				$display = 'wrong cartridge';
				break;
		}
		if (is_null($display)) {
			$ret_val = PrinterState_getFilamentStatus(strtolower($abb_cartridge));
			if ($ret_val == FALSE) {
				exec($arcontrol_fullpath . $parameter, $output, $ret_val);
				if ($ret_val == ERROR_NORMAL_RC_OK) {
					$display = 'ok';
				}
				else {
					$display = 'internal command error';
				}
			}
			else {
				$display = 'filament status error';
			}
		}
		
		$this->output->set_content_type('txt_u');
		echo $display;
		
		return;
	}
	
	public function insert($abb_cartridge = '') {
		$ret_val = 0;
		$display = NULL;
		$parameter = '';
		$output = array();
		$arcontrol_fullpath = $this->config->item('arcontrol_c');
		
		$this->load->helper('printerstate');
		
		switch (strtolower($abb_cartridge)) {
			case 'l':
				$parameter = ' -isctl';
				break;
				
			case 'r':
				$parameter = ' -isctr';
				break;
				
			default:
				$display = 'wrong cartridge';
				break;
		}
		if (is_null($display)) {
			$ret_val = PrinterState_getFilamentStatus(strtolower($abb_cartridge));
			if ($ret_val == FALSE) {
				exec($arcontrol_fullpath . $parameter, $output, $ret_val);
				if ($ret_val == ERROR_NORMAL_RC_OK) {
					$display = 'ok';
				}
				else {
					$display = 'internal command error';
				}
			}
			else {
				$display = 'filament status error';
			}
		}
		
		$this->output->set_content_type('txt_u');
		echo $display;
		
		return;
	}

}
