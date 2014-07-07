<?php

class Account extends MY_Controller
{
	public function signin()
	{
		if ($this->input->server('REQUEST_METHOD') == 'POST')
		{
			$this->load->library('form_validation');
			$this->form_validation->set_rules('email', 'Email', 'required|valid_email');
			$this->form_validation->set_rules('password', 'Password', 'required');
			
			if ($this->form_validation->run())
			{
				extract($_POST);
				$tab = array("email" => $email, "password" => $password);
				$curl = curl_init();
				curl_setopt($curl, CURLOPT_URL, "http://sso.zeepro.com/login.ashx");
				curl_setopt($curl, CURLOPT_POST, 2);
				curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($tab));
				curl_exec($curl);
				$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
				curl_close($curl);
				if ($code == 202)
				{
					var_dump("OK");
				}
				else
					var_dump('FAIL' . $code);
			}
			else
				var_dump('form fail');
		}
	/*	$this->load->library('parser');
		
		$body_page = $this->parser->parse('template/menu_home', $template_data, TRUE);
		
		// parse all page
		$template_data = array(
				'lang'			=> $this->config->item('language_abbr'),
				'headers'		=> '<title>' . t('ZeePro Personal Printer 21 - Home') . '</title>',
				'contents'		=> $body_page,
		);
		
		$this->parser->parse('template/basetemplate', $template_data);
		
		return;*/
	}
	
	public function signup()
	{
		
	}
}