<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');


class T_printlist extends MY_Controller {

	public function index()
	{
		$this->load->helper('form');
		$this->load->view('test/printlist_form');
	}
	
	public function send() {
		global $CFG;
		$data = array('error'=> '');
		$upload_config = NULL;
		$api_data = array();
		
		$this->load->helper('printlist');
		
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			//validation
			$this->load->library('form_validation');
			
			$this->form_validation->set_rules('n', 'Modelname', 'required');
// 			$this->form_validation->set_rules('f', 'Gcodefile', 'required');
			
			if ($this->form_validation->run() == FALSE)
			{	// Here is where you do stuff when the submitted form is invalid.
				$data['error'] .= validation_errors();
				$this->load->view('test/printlist_form', $data);
				return;
			}
			$api_data['n'] = $this->input->post('n');
			if ($this->input->post('d')) {
				$api_data['d'] = $this->input->post('d');
			}
			if ($this->input->post('t')) {
				$api_data['t'] = (int)$this->input->post('t');
			}
			if ($this->input->post('l1')) {
				$api_data['l1'] = (int)$this->input->post('l1');
			}
			if ($this->input->post('l2')) {
				$api_data['l2'] = (int)$this->input->post('l2');
			}
			
			$upload_config = array (
					'upload_path'	=> $CFG->config['temp'],
				//	'allowed_types'	=> 'gocde|jpg|png',
					'allowed_types'	=> '*',
					'overwrite'		=> TRUE,
					'remove_spaces'	=> TRUE,
					'encrypt_name'	=> TRUE,
			);
			$this->load->library('upload', $upload_config);
// 			$this->upload->initialize();

			if ($this->upload->do_upload('f')) {
				$api_data['f'] = $this->upload->data();
			} else {
				//treat error TODO return error
				$data['error'] .= $this->upload->display_errors();
			}
			//picture
			for($i=1; $i <= PRINTLIST_MAX_FILE_PIC; $i++) {
				if ($this->upload->do_upload("p$i")) {
					$api_data["p$i"] = $this->upload->data();
				} else {
					//treat error - optional
					$data['error'] .= $this->upload->display_errors();
				}
			}
			//TODO call helper function here
			
			$this->load->view('test/printlist_form', $data);
		} else {
			$data['error'] .= 'no post';
			$this->load->view('test/printlist_form', $data);
		}
		
		return;
	}
}
