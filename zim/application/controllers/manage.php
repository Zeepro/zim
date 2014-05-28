<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Manage extends MY_Controller {
	function __construct() {
		parent::__construct ();
		$this->load->helper( array(
				'url',
				'errorcode'
		) );
	}
	
	public function index() {
		$template_data = array();
		$body_page = NULL;
		
		$this->load->library('parser');
		$this->lang->load('manage', $this->config->item('language'));
		
		// parse the main body
		$template_data = array(
				'back'			=> t('back'),
				'head'			=> t('head_title'),
				'platform'		=> t('platform_title'),
				'filament'		=> t('filament_title'),
				'manage_left'	=> t('manage_left'),
				'manage_right'	=> t('manage_right'),
		);
		$body_page = $this->parser->parse('template/manage', $template_data, TRUE);
		
		// parse all page
		$template_data = array(
				'lang'			=> $this->config->item('language_abbr'),
				'headers'		=> '<title>' . t('manage_index_pagetitle') . '</title>' . "\n"
						. '<link rel="stylesheet" href="/assets/jquery-mobile-fluid960.min.css">',
				'contents'		=> $body_page,
		);
		
		$this->parser->parse('template/basetemplate', $template_data);
		
		return;
	}
	
	public function move($axis = NULL, $value = NULL, $speed = NULL) {
		if (is_null($axis) || is_null($value) || is_null($speed)
				|| ((int)$value == 0) || ((int)$speed == 0)) {
			$this->output->set_status_header(403);
			return;
		}
		else {
			$cr = 0;
			
			$this->load->helper(array('printerstate', 'errorcode'));
			
			$axis = strtoupper($axis);
			$cr = PrinterState_relativePositioning(TRUE);
			if ($cr == ERROR_OK) {
				$cr = PrinterState_move($axis, (int)$value, (int)$speed);
			}
			if ($cr == ERROR_OK) {
				$cr = PrinterState_relativePositioning(FALSE);
			}
			if ($cr == ERROR_OK) {
				$this->output->set_status_header(200);
				return;
			}
		}
		
		$this->output->set_status_header(403);
		return;
	}
	
	public function home($axis = 'ALL') {
		$cr = 0;
		
		$this->load->helper(array('printerstate', 'errorcode'));
		$axis = strtoupper($axis);
		$cr = PrinterState_homing($axis);
		if ($cr == ERROR_OK) {
			$this->output->set_status_header(200);
			return;
		}
		
		$this->output->set_status_header(403);
		return;
	}
}
