<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Account extends MY_Controller {
	//FIXME rewrite totally this controller and pass core function to helper
	private function _assign_wizard($email, $password) {
		$context = NULL;
		$printer_name = NULL;
		$cr = 0;
		$data = array();
		$option = array();
		
		$this->load->helper('zimapi');
		if (ERROR_OK != ZimAPI_getHostname($printer_name)) {
			$printer_name = 'zim';
		}
		$data = array('email' => $email, 'password' => $password, 'printersn' => ZimAPI_getSerial(), 'printername' => $printer_name);
		
		$options = array('http' => array('header'  => "Content-type: application/x-www-form-urlencoded\r\n",
				'method'  => 'POST',
				'content' => http_build_query($data)));
		$context = stream_context_create($options);
		@file_get_contents('https://sso.zeepro.com/addprinter.ashx', false, $context);
		$result = substr($http_response_header[0], 9, 3);
		if ($result == 200) {
			ZimAPI_setPrinterSSOName($printer_name);
			return TRUE;
		}
		
		return FALSE;
	}
	
	public function signin()
	{
		$this->load->library('parser');
		$data = array();
		$file = 'template/activation/index.php';
		$this->lang->load('activation/activation_form', $this->config->item('language'));
		
		if ($this->input->server('REQUEST_METHOD') == 'POST')
		{
			$this->load->library('form_validation');
			$this->form_validation->set_rules('email', 'Email', 'required|valid_email');
			$this->form_validation->set_rules('password', 'Password', 'required');
			
			if ($this->form_validation->run())
			{
				extract($_POST);
				$url = 'https://sso.zeepro.com/login.ashx';
				$data = array('email' => $email, 'password' => $password);

				$options = array('http' => array('header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        										'method'  => 'POST',
        										'content' => http_build_query($data)));
				$context  = stream_context_create($options);
				try
				{
					@file_get_contents($url, false, $context);
				}
				catch (Exception $e)
				{
					//TODO:error handling
					die();
				}
				
				$result = substr($http_response_header[0], 9, 3);
				if ($result == 202)
				{
					// if in wizard mode, we fix the printer name
					$this->load->helper('corestatus');
					if (CoreStatus_checkInConnection()) {
						$cr = $this->_assign_wizard($email, $password);
						if ($cr == TRUE) {
							$this->output->set_header('Location: /activation/wizard_confirm');
						}
						else {
							$this->output->set_header('Location: /activation/wizard_confirm/fail');
						}
						
						return;
					}
					
					$file = 'template/activation/activation_form';
					$data = array('email' =>$email, 'password' => $password, 'returnUrl' => isset($_GET['returnUrl']) ? ('?returnUrl='.$_GET['returnUrl']) : '');
				}
			}
		}
		else {
			// simply protect when not in post method
			$this->output->set_header('Location: /activation');
			return;
		}
		
		$body_page = $this->parser->parse($file, $data, TRUE);
		
		// parse all page
		$template_data = array(
				'lang'			=> $this->config->item('language_abbr'),
				'headers'		=> '<title>' . t('ZeePro Personal Printer 21 - Home') . '</title>',
				'contents'		=> $body_page,
				'back'			=> t('back'),
				'give_name'		=> t('give_name'),
				'activate'		=> t('activate')
		);
		$this->parser->parse('template/basetemplate', $template_data);
		return;
	}
	
	public function signup_confirmation()
	{
		$file = 'template/account/signup_confirmation';
		$data = array();
		$this->load->library('parser');
		$this->load->helper('url');
		
		// try to keep flashdata, but it seems not working
		$this->session->keep_flashdata('email');
		$this->session->keep_flashdata('password');
		
		if ($this->input->server('REQUEST_METHOD') == 'POST')
		{
			$this->load->library('form_validation');
			$this->form_validation->set_rules('code', 'Confirmation code', 'required');
			if ($this->form_validation->run())
			{
				extract($_POST);
				$url = 'https://sso.zeepro.com/confirmaccount.ashx';
				$data = array('email' => $email, 'code' => $code);
			
				$options = array('http' => array('header'  => "Content-type: application/x-www-form-urlencoded\r\n",
						'method'  => 'POST',
						'content' => http_build_query($data)));
				$context  = stream_context_create($options);
				@file_get_contents($url, false, $context);
				$result = substr($http_response_header[0], 9, 3);
				if ($result == 200) // perhaps we will have problem with 437
				{
// 					redirect('/');
					// if in wizard mode, we fix the printer name
					$this->load->helper('corestatus');
					if (CoreStatus_checkInConnection()) {
						$cr = $this->_assign_wizard($email, $password);
						if ($cr == TRUE) {
							$this->output->set_header('Location: /activation/wizard_confirm');
						}
						else {
							$this->output->set_header('Location: /activation/wizard_confirm/fail');
						}
						
						return;
					}
					
					$file = 'template/activation/activation_form';
					$data = array('email' =>$email, 'password' => $password, 'returnUrl' => '');
				}
			}
		}
		$body_page = $this->parser->parse($file, $data, TRUE);
		
		// parse all page
		$template_data = array(
				'lang'			=> $this->config->item('language_abbr'),
				'headers'		=> '<title>' . t('ZeePro Personal Printer 21 - Home') . '</title>',
				'contents'		=> $body_page,
				'back'			=> t('back'),
				'give_name'		=> t('give_name'),
				'activate'		=> t('activate')
		);
		$this->parser->parse('template/basetemplate', $template_data);
		return;
	}
	
	public function signup()
	{
		$body_page = NULL;
		$template_data = array();
		$data = array();
		
		$this->load->library('parser');
		$this->load->helper(array('url','corestatus'));
		
		// check network
		if (@file_get_contents("https://sso.zeepro.com/login.ashx") === FALSE) {
			if (CoreStatus_checkInConnection()) {
				$this->output->set_header('Location: /activation/wizard_confirm/fail');
			}
			else {
				$body_page = $this->parser->parse('/template/activation/network_error', array(), TRUE);
				$this->lang->load('activation/network_error', $this->config->item('language'));
				$template_data = array(
						'lang'			=> $this->config->item('language_abbr'),
						'headers'		=> '<title>' . t('ZeePro Personal Printer 21 - Home') . '</title>',
						'contents'		=> $body_page,
						'back'			=> t('back'),
						'network_err_msg'=> t('network_err_msg')
				);
				$this->parser->parse('template/basetemplate', $template_data);
			}
			
			return;
		}
		
		if ($this->input->server('REQUEST_METHOD') == 'POST') {
			$this->load->library('form_validation');
			$this->form_validation->set_rules('email', 'Email', 'required|valid_email');
			$this->form_validation->set_rules('password', 'Password', 'required|matches[confirm]');
			$this->form_validation->set_rules('confirm', 'Password confirmation', 'required');
				
			if ($this->form_validation->run()) {
				extract($_POST);
				$data = array(
						'email'		=> $email,
						'password'	=> $password
				);
				
				$options = array(
						'http' => array(
								'header'	=> "Content-type: application/x-www-form-urlencoded\r\n",
								'method'	=> 'POST',
								'content'	=> http_build_query($data)
						)
				);
				$context = stream_context_create($options);
				file_get_contents('https://sso.zeepro.com/createaccount.ashx', false, $context);
				$result = substr($http_response_header[0], 9, 3);
				if ($result == 200) {
					$this->session->set_flashdata('email', $email);
					$this->session->set_flashdata('password', $password);
					redirect('/account/signup_confirmation');
				}
			}
		}
		
		// add skip button if in wizard
		if (CoreStatus_checkInConnection())
		{
			$data['back_or_already'] = t('already');
			$data['has_skip'] = "block"; 			
			$data['btn_url'] ='/account/signin';
		}
		else
		{
			$data['back_or_already'] = t('back');
			$data['has_skip'] = "none";
			$data['btn_url'] ='javascript:history.back()';
		}
		$data['confcode_hint'] = t('confcode_hint');
		$data['signup_title'] = t('signup_title');
		$data['signup_text'] = t('signup_text');
		$body_page = $this->parser->parse('template/account/signup', $data, TRUE);
		
		// parse all page
		$template_data = array(
				'lang'			=> $this->config->item('language_abbr'),
				'headers'		=> '<title>' . t('ZeePro Personal Printer 21 - Home') . '</title>',
				'contents'		=> $body_page,
		);
		$this->parser->parse('template/basetemplate', $template_data);
		
		return;
	}
}