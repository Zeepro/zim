<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

class Gcode extends MY_Controller {
	function __construct() {
		parent::__construct ();
		$this->load->helper( array(
				'url',
		) );
	}

	public function index() {
		global $CFG;
		$template_data = array();
		$body_page = NULL;

		$this->load->library('parser');

		// parse the main body
		$template_data = array(
				'button_get'	=> 'GET',
				'button_post'	=> 'POST',
		);

		$body_page = $this->parser->parse('template/gcode', $template_data, TRUE);

		// parse all page
		$template_data = array(
				'lang'			=> $CFG->config ['language_abbr'],
				'headers'		=> '<title>Gcode</title>',
				'contents'		=> $body_page,
		);

		$this->parser->parse('template/basetemplate', $template_data);

		return;
	}

}
