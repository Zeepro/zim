<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Menu extends MY_Controller {
	function __construct() {
		parent::__construct();
	}
	
	public function index() {
		$template_data = array();
		$need_update = FALSE;
		
		$this->load->library('parser');
		$this->lang->load('menu_home', $this->config->item('language'));
		$this->load->helper('zimapi');
		
		if (!ZimAPI_cameraOff()) {
			$this->load->helper('printerlog');
			PrinterLog_logError('can not turn off camera', __FILE__, __LINE__);
		}
		$need_update = !(ZimAPI_getVersion(TRUE) == ZimAPI_getVersion(FALSE));
		
		//parse the main body
		$template_data = array(
// 				'title'				=> t('Home'),
				'update_available'	=> $need_update ? t('update_available') : "",
				'update_hint'		=> t('update_available'),
				'my_library'		=> t('my_library'),
				'my_zim_shop'		=> t('my_zim_shop'),
				'menu_printlist'	=> t('Quick print'),
				'menu_printerstate'	=> t('Configuration'),
				'manage'			=> t('manage'),
				'upload'			=> t('upload'),
				'about'				=> t('about'),
				'library_visible'	=> ($this->config->item('use_sdcard') == TRUE) ? 'block' : 'none',
		);
		
		$this->_parseBaseTemplate(t('ZeePro Personal Printer 21 - Home'),
				$this->parser->parse('test_profile', $template_data, TRUE),
				'<script type="text/javascript" src="/assets/jssor/jssor.slider.mini.js"></script>
				<link rel="stylesheet" href="/assets/jssor/jssora.css" />');
		
		return;
	}
}