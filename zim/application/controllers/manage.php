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
		$ret_val = 0;
		
		$this->load->library('parser');
		$this->lang->load('manage', $this->config->item('language'));
		$this->lang->load('printerstate/index', $this->config->item('language'));
		
		$this->load->helper(array('zimapi', 'printerstate'));
		if (!ZimAPI_cameraOn(ZIMAPI_PRM_CAMERA_PRINTSTART))
		{
			$this->load->helper('printerlog');
			PrinterLog_logError('can not set camera', __FILE__, __LINE__);
		}
		$ret_val = PrinterState_getStripLedStatus($status_strip);
		if ($ret_val != ERROR_OK)
		{
			$status_strip = FALSE;
		}
		$ret_val = PrinterState_getTopLedStatus($status_head);
		if ($ret_val != ERROR_OK) {
			$status_head = FALSE;
		}
		
		//parse the main body
		$template_data = array(
				'back'			=> t('back'),
				'loading_player'	=> t('loading_player'),
				'platform_view_title' => t('platform_view_title'),
				'reset'			=> t('reset_title'),
				'head'			=> t('head_title'),
				'platform'		=> t('platform_title'),
				'filament'		=> t('filament_title'),
				'manage_left'	=> t('manage_left'),
				'manage_right'	=> t('manage_right'),
				'bed_title'		=> t('bed_title'),
				'bed_hint'		=> t('bed_hint'),
				'video_error'	=> t('video_error'),
				'video_url'		=> $this->config->item('video_url'),
				'lighting_title'=> t('lighting_title'),
				'strip_led'		=> t('strip_led'),
				'head_led'		=> t('head_led'),
				'led_on'		=> t('led_on'),
				'led_off'		=> t('led_off'),
				'left'		=> t('left'),
				'right'		=> t('right'),
				'strip_led_on'	=> ($status_strip == TRUE) ? "selected=selected" : NULL,
				'head_led_on'	=> ($status_head == TRUE) ? "selected=selected" : NULL,
		);
		$body_page = $this->parser->parse('template/manage/index', $template_data, TRUE);
		
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
				$cr = PrinterState_move($axis, (int)$value, (int)$speed * 60);
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

	public function level($point = NULL) {
		$cr = 0;
		$array_cmd = array();
	
		if (is_null($point)) {
			$this->output->set_status_header(403);
			return;
		}
	
		$this->load->helper(array('printerstate', 'errorcode'));
		$cr = PrinterState_relativePositioning(FALSE);
		if ($cr != ERROR_OK) {
			$point = 'error';
		}
		switch ($point) {
			case 'step1':
				$array_cmd = array(
				'X'	=> 79,
				'Y'	=> 75,
				);
				break;
	
			case 'step2':
				$array_cmd = array(
				'X'	=> 35,
				'Y'	=> 150,
				);
				break;
	
			case 'step3':
				$array_cmd = array(
				'X'	=> 124,
				'Y'	=> 150,
				);
				break;
			default:
				$this->output->set_status_header(403);
				return;
				break; // never reach here
		}
	
		foreach ($array_cmd as $axis => $value) {
			$cr = PrinterState_move($axis, $value, 2000);
			if ($cr != ERROR_OK)
			{
				$this->output->set_status_header(403);
				return;
			}
		}	
		$this->output->set_status_header(200);
		return;
	}

	public function filament_ajax($side)
	{
		$this->load->library('parser');
		$this->load->helper('printerstate');
		$this->lang->load('manage/filament_ajax', $this->config->item('language'));

		$json_cartridge = array();
		$ret_val = PrinterState_getCartridgeAsArray($json_cartridge, $side);
		
		if ($ret_val != ERROR_MISS_LEFT_CART && $ret_val != ERROR_MISS_RIGT_CART)
		{
			$action = PrinterState_getFilamentStatus($side) ? t('loaded_action') : t('unloaded_action');
			$initial = intval($json_cartridge['initial']);
			$used = intval($json_cartridge['used']);
			$template_data = array(
								'visibility'=> "visible",
								'color'		=> $json_cartridge['color'],
								'material'	=> "<br />" . strtoupper($json_cartridge['material'])."<br />",
								'length'	=> number_format(round(($initial - $used) / 1000, 2, PHP_ROUND_HALF_DOWN), 2),
								'length_text'	=> t('length_text'),
								'action'		=> $action);
			$this->parser->parse('template/manage/manage_filament_ajax', $template_data);
		}
		else
		{
			$template_data = array(
					'visibility'=> "hidden",
					'color'		=> "#FFFFFF",
					'material'	=> "",
					'length'	=> "",
					'length_text'	=> "",
					'action'		=> t('insert_action')  . "<br /><br /><br /><br />");
			$this->parser->parse('template/manage/manage_filament_ajax', $template_data);
		}
		return;
	}
}
