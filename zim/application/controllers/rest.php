<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Rest extends MY_Controller {
	function __construct() {
		parent::__construct ();
		$this->load->helper ( array (
				'form',
				'url',
				'json' 
		) );
	}
	public function resetnetwork() {
		global $CFG;
		
		$arr = json_read ( $CFG->config ['conf'] . 'Work.json' );
		if (! $arr ["error"] and $arr ["json"] ["State"] == "Working") {
			// Work in progress
			http_response_code ( 446 );
			echo("Printer busy");
			exit ();
		}
		
		$arr = array (
				"Version" => "1.0",
				"Message" => array (
						"Context" => "BootMessage",
						"Id" => "Boot.Test" 
				) 
		);
		$fh = fopen ( $CFG->config ['conf'] . 'Boot.json', 'w' );
		fwrite ( $fh, json_encode ( $arr ) );
		fclose ( $fh );
	}
}