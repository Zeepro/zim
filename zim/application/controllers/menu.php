<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Menu extends MY_Controller {
	function __construct() {
		parent::__construct();
	}
	
	public function index() {
		$this->load->helper(array('zimapi', 'userauth', 'url'));
		
		if (!ZimAPI_cameraOff()) {
			$this->load->helper('printerlog');
			PrinterLog_logError('can not turn off camera', __FILE__, __LINE__);
		}
		
		$this->m_total();
// 		redirect(USERAUTH_URL_REDIRECTION . USERAUTH_URI_REMOTE_INDEX);
		
		return;
	}
	
	public function home() {
		$this->load->helper(array('userauth', 'url'));
		
		redirect(USERAUTH_URL_REDIRECTION . USERAUTH_URI_REMOTE_INDEX);
		
		return;
	}
	
	public function local() {
		$this->load->helper('url');
		redirect('/menu_home');
		
		return;
	}
	
	public function m_total() {
		$need_update = FALSE;
		$template_data = NULL; //array()
		
		$this->load->helper(array('userauth', 'zimapi'));
		$this->load->library('parser');
		$this->lang->load('menu_home', $this->config->item('language'));
		$this->lang->load('menu/config', $this->config->item('language'));
		$this->lang->load('menu/print', $this->config->item('language'));
		
		$need_update = !(ZimAPI_getVersion(TRUE) == ZimAPI_getVersion(FALSE));
		
		$template_data = array(
				'link_import_once'	=> t('link_import_once'),
				'link_userlib'		=> t('link_userlib'),
				'link_printlist'	=> t('link_printlist'),
				'link_preset'		=> t('link_preset'),
				'library_visible'	=> ($this->config->item('use_sdcard') == TRUE) ? 'block' : 'none',
				'link_manage_user'	=> t('link_manage_user'),
				'link_control'		=> t('link_control'),
				'link_config'		=> t('link_config'),
				'link_about'		=> t('link_about'),
				'show_mange_user'	=> (UserAuth_checkAccount()) ? 'true' : 'false',
				'show_control'		=> (UserAuth_checkManage()) ? 'true' : 'false',
				'update_available'	=> $need_update ? t('update_available') : '',
				'update_hint'		=> t('update_available'),
		);
		
		$this->_parseBaseTemplate(t('pagetitle_menu_print'),
				$this->parser->parse('menu/total', $template_data, TRUE));
		
		return;
	}
	
	public function m_print() {
		$template_data = NULL; //array()
		
		$this->load->library('parser');
		$this->lang->load('menu/print', $this->config->item('language'));
		
		$template_data = array(
				'link_import_once'	=> t('link_import_once'),
				'link_userlib'		=> t('link_userlib'),
				'link_printlist'	=> t('link_printlist'),
				'link_preset'		=> t('link_preset'),
				'library_visible'	=> ($this->config->item('use_sdcard') == TRUE) ? 'block' : 'none',
		);
		
		$this->_parseBaseTemplate(t('pagetitle_menu_print'),
				$this->parser->parse('menu/print', $template_data, TRUE));
		
		return;
	}
	
	public function m_config() {
		$template_data = NULL; //array()
		
		$this->load->helper('userauth');
		$this->load->library('parser');
		$this->lang->load('menu/config', $this->config->item('language'));
		
		$template_data = array(
				'link_manage_user'	=> t('link_manage_user'),
				'link_control'		=> t('link_control'),
				'link_config'		=> t('link_config'),
				'link_about'		=> t('link_about'),
				'show_mange_user'	=> (UserAuth_checkAccount()) ? 'true' : 'false',
				'show_control'		=> (UserAuth_checkManage()) ? 'true' : 'false',
		);
		
		$this->_parseBaseTemplate(t('pagetitle_menu_config'),
				$this->parser->parse('menu/config', $template_data, TRUE));
		
		return;
	}
	
	/*
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
	 */
}