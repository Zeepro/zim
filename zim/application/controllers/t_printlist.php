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
		$cr = 0; //return code
		
		$this->load->helper(array('form', 'printlist', 'errorcode'));
		
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			//validation
			$this->load->library('form_validation');
			
			$this->form_validation->set_rules('n', 'Modelname', 'required');
// 			$this->form_validation->set_rules('f', 'Gcodefile', 'required');
			
			if ($this->form_validation->run() == FALSE)
			{	// Here is where you do stuff when the submitted form is invalid.
// 				$data['error'] .= validation_errors();
				$data['error'] = ERROR_MISS_PRM . " " . t(MyERRMSG(ERROR_MISS_PRM));
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
					'allowed_types'	=> 'jpg|png|gcode',
// 					'allowed_types'	=> '*',
					'overwrite'		=> TRUE,
					'remove_spaces'	=> TRUE,
					'encrypt_name'	=> TRUE,
			);
			$this->load->library('upload', $upload_config);
// 			$this->upload->initialize();

			if ($this->upload->do_upload('f')) {
				$api_data['f'] = $this->upload->data();
			} else {
				//treat error - missing gcode file
// 				$data['error'] .= $this->upload->display_errors();
				$data['error'] = ERROR_MISS_PRM . " " . t(MyERRMSG(ERROR_MISS_PRM));
			}
			//picture
			for($i=1; $i <= PRINTLIST_MAX_FILE_PIC; $i++) {
				if ($this->upload->do_upload("p$i")) {
					$api_data["p$i"] = $this->upload->data();
// 				} else {
// 					//treat error - optional
// 					$data['error'] .= $this->upload->display_errors();
				}
			}
			
			//call function
			$cr = ModelList_add($api_data);
			if ($cr != ERROR_OK) {
				$data['error'] = $cr . " " . t(MyERRMSG($cr));
			} else { //TODO show a success message here
				$data['error'] = $cr . " " . t(MyERRMSG($cr));
			}
			
			$this->load->view('test/printlist_form', $data);
		} else {
			$data['error'] .= 'no post';
			$this->load->view('test/printlist_form', $data);
		}
		
		return;
	}
	
	public function delete() {
		$mid = '';
		$display = '';
		$cr = 0; //return code

		$this->load->helper(array('errorcode', 'printlist'));
		
		$mid = $this->input->get('id'); //return false if missing
		
		//check mid
		if ($mid) {
			//call function
			$cr = ModelList_delete($mid);
			if ($cr != ERROR_OK) {
				$display = $cr . " " . t(MyERRMSG($cr));
			} else { //TODO show a success message here
				$display = $cr . " " . t(MyERRMSG($cr));
			}
		} else {
			$display = ERROR_MISS_PRM . " " . t(MyERRMSG(ERROR_MISS_PRM));
		}
		
		echo $display;
		
		return;
	}
	
	public function mlist() {
		$display = '';
		$cr = 0; //return code
		
// 		$this->load->helper(array('errorcode', 'printlist'));
		$this->load->helper('printlist');
		
		$display = ModelList_list();
		
		echo $display;
		
		return;
	}
	
	public function getpicture() {
		$mid = '';
		$pid = 0;
		$url_pid = '';
		$display = '';
		$cr = 0; //return code
	
		$this->load->helper(array('errorcode', 'printlist', 'file'));
	
		$mid = $this->input->get('id'); //return false if missing
		$pid = (int)$this->input->get('p'); //return false if missing
	
		//check mid
		if ($mid && $pid) {
			//call function
			$cr = ModelList_getPic($mid, $pid, $path_pid);
			if ($cr != ERROR_OK) {
				$display = $cr . " " . t(MyERRMSG($cr));
			} else {
// 				header('Content-Length: ' . filesize($path_pid));
// 				header('Content-Type: ' . get_mime_by_extension($path_pid));
// 				header('Content-Disposition: inline; filename="img' . $pid . '";'); //filename header
// 				exit(file_get_contents($path_pid));
				$this->output->set_content_type(get_mime_by_extension($path_pid))->set_output(file_get_contents($path_pid));
				return;
			}
		} else {
			$display = ERROR_MISS_PRM . " " . t(MyERRMSG(ERROR_MISS_PRM));
		}
	
		echo $display;
	
		return;
	}
	
	public function mprint() {
		//TODO finish this controller
		echo 'under construction... :)';
		return;
	}
}
