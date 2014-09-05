<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

class Test_version extends CI_Controller {
	function __construct() {
		parent::__construct ();
		$this->load->helper( array(
				'printerstate',
				'url',
				'json'
		) );
	}
	
	public function ssh() {
		$output = array();
		$ret_val = 0;
		if (!file_exists('/tmp/remoteSSH')) {
			exec('/etc/init.d/remote_ssh start');
		}
		exec('/etc/init.d/remote_ssh status', $output, $ret_val);
		
		var_dump(array(
				'ret_code'	=> $ret_val,
				'output'	=> $output,
		));
		
		return;
	}

	public function index() {
		$template_data = array();
		$body_page = NULL;
		$temp_info = array();
		$array_info = array();
		$sso_name = NULL;
	
		$this->load->helper(array('printerstate', 'zimapi'));
		$this->load->library('parser');
		$this->lang->load('printerstate/printerinfo', $this->config->item('language'));
		ZimAPI_getPrinterSSOName($sso_name);
	
		$temp_info = PrinterState_getInfoAsArray();
		$array_info = array(
				array(
						'title'	=> t('version_title'),
						'value'	=> $temp_info[PRINTERSTATE_TITLE_VERSION],
				),
				array(
						'title'	=> t('next_version_title'),
						'value'	=> $temp_info[PRINTERSTATE_TITLE_VERSION_N],
				),
				array(
						'title'	=> t('type_title'),
						'value'	=> $temp_info[PRINTERSTATE_TITLE_TYPE],
				),
				array(
						'title'	=> t('serial_title'),
						'value'	=> $temp_info[PRINTERSTATE_TITLE_SERIAL],
				),
				array(
						'title'	=> t('extruder_title'),
						'value'	=> $temp_info[PRINTERSTATE_TITLE_NB_EXTRUD],
				),
				array(
						'title'	=> t('marlin_title'),
						'value'	=> $temp_info[PRINTERSTATE_TITLE_VER_MARLIN],
				),
				array(
						'title' => t('ip_address'),
						'value'	=> $temp_info[ZIMAPI_TITLE_IP],
				)
		);
	
		// parse the main body
		$template_data = array(
				'array_info'	=> $array_info,
		);
	
		$body_page = $this->parser->parse('template/test_version', $template_data, TRUE);
	
		// parse all page
		$template_data = array(
				'lang'			=> $this->config->item('language_abbr'),
				'headers'		=> '<title>' . t('printerstate_printerinfo_pagetitle') . '</title>',
				'contents'		=> $body_page,
		);
	
		$this->parser->parse('template/basetemplate', $template_data);
	
		return;
	}
}