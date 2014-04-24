<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Test_compress extends CI_Controller {

	public function index() {
		$this->enable_compress();
		$this->load->helper(array('file'));
		
		$path = 'C:/Users/ZPFr2/Desktop/STL/schtroumph.stl';
		
		$this->output->set_content_type(get_mime_by_extension($path))->set_output(@file_get_contents($path));
		
		return;
	}
	
	private function enable_compress() {
		if( empty($_SERVER['HTTP_ACCEPT_ENCODING']) ) { return false; }
		
		//If zlib is not ALREADY compressing the page - and ob_gzhandler is set
		if (( ini_get('zlib.output_compression') == 'On'
				OR ini_get('zlib.output_compression_level') > 0 )
				OR ini_get('output_handler') == 'ob_gzhandler' ) {
			return false;
		}
		
		//Else if zlib is loaded start the compression.
		if ( extension_loaded( 'zlib' ) AND (strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== FALSE) ) {
			ob_start('ob_gzhandler');
		}
		return true;
	}

}
