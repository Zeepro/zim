<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Activation extends MY_Controller
{
	public function index()
	{
		$network_ok = false;
		$this->load->library('parser');

		if (!(@file_get_contents("https://sso.zeepro.com/login.ashx") === FALSE))
		{
			$network_ok = true;
		}
		if ($network_ok)
		{
			$body_page = $this->parser->parse('/template/activation/index', array(), TRUE);
			$this->lang->load('activation/activation', $this->config->item('language'));
			$template_data = array(
					'lang'			=> $this->config->item('language_abbr'),
					'headers'		=> '<title>' . t('ZeePro Personal Printer 21 - Home') . '</title>',
					'contents'		=> $body_page,
					'title'			=> t('title'),
					'password'		=> t('password'),
					'sign_in'		=> t('sign_in'),
					'sign_up'		=> t('sign_up'),
					'back'			=> t('back'),
					'create_account'=> t('create_account')
			);
		}
		else
		{
			$body_page = $this->parser->parse('/template/activation/network_error', array(), TRUE);
			$this->lang->load('activation/network_error', $this->config->item('language'));
			$template_data = array(
					'lang'			=> $this->config->item('language_abbr'),
					'headers'		=> '<title>' . t('ZeePro Personal Printer 21 - Home') . '</title>',
					'contents'		=> $body_page,
					'back'			=> t('back'),
					'network_err_msg'=> t('network_err_msg')
			);
		}
		$this->parser->parse('template/basetemplate', $template_data);
	}
	
	public function activation_form()
	{
		$this->load->library('parser');
		$this->lang->load('activation/activation_confirm', $this->config->item('language'));
		$file = 'template/activation/activation_form';
		if ($this->input->server('REQUEST_METHOD') == 'POST')
		{
			$this->load->library('form_validation');
			$this->form_validation->set_rules('printer_name', 'Printer name', 'required');
			if ($this->form_validation->run())
			{
				$this->load->helper('zimapi');
				extract($_POST);
				$url = 'https://sso.zeepro.com/addprinter.ashx';
				$data = array('email' => $email, 'password' => $password, 'printersn' => ZimAPI_getSerial(), 'printername' => $printer_name);
		
				$options = array('http' => array('header'  => "Content-type: application/x-www-form-urlencoded\r\n",
						'method'  => 'POST',
						'content' => http_build_query($data)));
				$context  = stream_context_create($options);
				@file_get_contents($url, false, $context);
				$result = substr($http_response_header[0], 9, 3);
				if ($result == 200)
				{
					ZimAPI_setPrinterSSOName($printer_name);
					$file = 'template/activation/activation_confirm';
				}
				else
					echo '<script>console.log("FAIL");</script>';
			}
		}
		$body_page = $this->parser->parse($file, array(), TRUE);
		// parse all page
		$template_data = array(
				'lang'			=> $this->config->item('language_abbr'),
				'headers'		=> '<title>' . t('ZeePro Personal Printer 21 - Home') . '</title>',
				'contents'		=> $body_page,
				'back'			=> t('back'),
				'congrats'		=> t('congrats'),
				'confirmation_message'	=> t('confirmation_message')
		);
		$this->parser->parse('template/basetemplate', $template_data);
	}
}