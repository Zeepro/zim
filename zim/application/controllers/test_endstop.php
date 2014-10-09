<?php

class Test_endstop extends CI_Controller
{
	public function		index()
	{
		$this->load->library('parser');
		//$this->lang->load('test_endstop', $this->config->item('language'));
		$this->load->helper('printerstate');
		$endstop = array();

		$error = 'none';
		if (PrinterState_getEndstopList($endstop) != ERROR_OK)
			$error = 'block';

		$view_data = array(
			'error'		=> $error,
			'yback'		=> $endstop['ymax'] ? 'selected="selected"' : "",
			'yfront'	=> $endstop['ymin'] ? 'selected="selected"' : "",
			'xleft'		=> $endstop['xmin'] ? 'selected="selected"' : "",
			'xright'	=> $endstop['xmax'] ? 'selected="selected"' : "",
			'ztop'		=> $endstop['zmin'] ? 'selected="selected"' : "",
			'zbottom'	=> $endstop['zmax'] ? 'selected="selected"' : ""
		);

		$body_page = $this->parser->parse('/template/test_endstop', $view_data, TRUE);
		$template_data = array(
				'headers'		=> '<title>Zim</title>',
				'contents'		=> $body_page
		);
		$this->parser->parse('template/basetemplate', $template_data);
		return;
	}

	public function		endstop_ajax()
	{
		$this->load->helper('printerstate');
		$endstop = array();
		
		if (PrinterState_getEndstopList($endstop) != ERROR_OK)
		{
			$this->output->set_status_header(503);
			return;
		}
		
		$display = json_encode($endstop);
		echo $display;
		return;
	}
}