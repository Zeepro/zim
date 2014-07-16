<?php

class Account extends MY_Controller
{  
	public function signin()
	{
		$this->load->library('parser');
		$data = array();
		$file = 'template/activation/activation_form';
		
		if ($this->input->server('REQUEST_METHOD') == 'POST')
		{
			$this->load->library('form_validation');
			$this->form_validation->set_rules('email', 'Email', 'required|valid_email');
			$this->form_validation->set_rules('password', 'Password', 'required');
			
			if ($this->form_validation->run())
			{
				extract($_POST);
				$url = 'http://sso.zeepro.com/login.ashx';
				$data = array('email' => $email, 'password' => $password);

				$options = array('http' => array('header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        										'method'  => 'POST',
        										'content' => http_build_query($data)));
				$context  = stream_context_create($options);
				file_get_contents($url, false, $context);
				$result = substr($http_response_header[0], 9, 3);
				if ($result == 202)
				{
					$file = 'template/activation/activation_form';
					$data = array('email' =>$email, 'password' => $password);
				}
				else
				{
					$this->load->helper('url');
					redirect('activation');
				}
			}
			else
			{
				var_dump('form fail');
				redirect('activation');
			}
		}
		
		$body_page = $this->parser->parse($file, $data, TRUE);
		
		// parse all page
		$template_data = array(
				'lang'			=> $this->config->item('language_abbr'),
				'headers'		=> '<title>' . t('ZeePro Personal Printer 21 - Home') . '</title>',
				'contents'		=> $body_page,
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
		if ($this->input->server('REQUEST_METHOD') == 'POST')
		{
			$this->load->library('form_validation');
			$this->form_validation->set_rules('code', 'Confirmation code', 'required');
			if ($this->form_validation->run())
			{
				extract($_POST);
				$url = 'http://sso.zeepro.com/confirmaccount.ashx';
				$data = array('email' => $email, 'code' => $code);
			
				$options = array('http' => array('header'  => "Content-type: application/x-www-form-urlencoded\r\n",
						'method'  => 'POST',
						'content' => http_build_query($data)));
				$context  = stream_context_create($options);
				file_get_contents($url, false, $context);
				$result = substr($http_response_header[0], 9, 3);
				if ($result == 200)
				{
					redirect('/');
				}
			}
		}
		$body_page = $this->parser->parse($file, $data, TRUE);
		
		// parse all page
		$template_data = array(
				'lang'			=> $this->config->item('language_abbr'),
				'headers'		=> '<title>' . t('ZeePro Personal Printer 21 - Home') . '</title>',
				'contents'		=> $body_page,
		);
		$this->parser->parse('template/basetemplate', $template_data);
		return;
	}
	
	public function signup()
	{
		$file = 'template/account/signup';
		$data = array();
		$this->load->library('parser');
		$this->load->helper('url');
		if ($this->input->server('REQUEST_METHOD') == 'POST')
		{
			$this->load->library('form_validation');
			$this->form_validation->set_rules('email', 'Email', 'required|valid_email');
			$this->form_validation->set_rules('password', 'Password', 'required|matches[confirm]');
			$this->form_validation->set_rules('confirm', 'Password confirmation', 'required');
				
			if ($this->form_validation->run())
			{
				extract($_POST);
				$url = 'http://sso.zeepro.com/createaccount.ashx';
				$data = array('email' => $email, 'password' => $password);
		
				$options = array('http' => array('header'  => "Content-type: application/x-www-form-urlencoded\r\n",
						'method'  => 'POST',
						'content' => http_build_query($data)));
				$context  = stream_context_create($options);
				file_get_contents($url, false, $context);
				$result = substr($http_response_header[0], 9, 3);
				if ($result == 200)
				{
					$this->session->set_flashdata('email', $email);
					redirect('/account/signup_confirmation');
				}
			}
		}
		$body_page = $this->parser->parse($file, $data, TRUE);
		
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