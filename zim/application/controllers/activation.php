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
}