<?php

class Activation extends MY_Controller
{
	public function index()
	{
		$network_ok = false;
		$this->load->library('parser');

		if (!(file_get_contents("http://sso.zeepro.com/login.ashx") === FALSE))
		{
			$network_ok = true;
		}
		
		$body_page = $this->parser->parse('/template/activation/' . ($network_ok ? 'index' : 'error'), array(), TRUE);
		// parse all page
		$template_data = array(
				'lang'			=> $this->config->item('language_abbr'),
				'headers'		=> '<title>' . t('ZeePro Personal Printer 21 - Home') . '</title>',
				'contents'		=> $body_page,
		);
		$this->parser->parse('template/basetemplate', $template_data);
	}
	
	public function activation_form()
	{
		$this->load->library('parser');
		$file = 'template/activation/activation_form';
		if ($this->input->server('REQUEST_METHOD') == 'POST')
		{
			$this->load->library('form_validation');
			$this->form_validation->set_rules('printer_name', 'Printer name', 'required');
			if ($this->form_validation->run())
			{
				$this->load->helper('zimapi');
				extract($_POST);
				$url = 'http://sso.zeepro.com/addprinter.ashx';
				$data = array('email' => $email, 'password' => $password, 'printersn' => ZimAPI_getSerial(), 'printername' => $printer_name);
		
				$options = array('http' => array('header'  => "Content-type: application/x-www-form-urlencoded\r\n",
						'method'  => 'POST',
						'content' => http_build_query($data)));
				$context  = stream_context_create($options);
				file_get_contents($url, false, $context);
				$result = substr($http_response_header[0], 9, 3);
				echo '<script>console.log("'.$result.'");</script>';
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
		);
		$this->parser->parse('template/basetemplate', $template_data);
	}
}