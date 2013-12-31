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
	
	//==========================================================
	//index test view
	//==========================================================
// 	public function index()
// 	{
// 		$this->load->helper('form');
// 		$this->load->view('test/printlist_form');
// 	}
	
	
	//==========================================================
	//network web service
	//==========================================================
	public function resetnetwork() {
		global $CFG;
		
		$arr = json_read ( $CFG->config ['conf'] . 'Work.json' );
		if (! $arr ["error"] and $arr ["json"] ["State"] == "Working") {
			// Work in progress
// 			http_response_code ( 446 );
			$this->output->set_status_header(ERROR_BUSY_PRINTER, "Printer busy");
 			echo("Printer busy"); //optional
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
	
	
	//==========================================================
	//print list web service
	//==========================================================	
	public function storemodel() {
		global $CFG;
		$data = array('error'=> '');
		$upload_config = NULL;
		$api_data = array();
		$cr = 0; //return code
		$display = '';
	
		$this->load->helper(array('form', 'printlist', 'errorcode'));
	
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			//validation (not file check included)
			$this->load->library('form_validation');
			
			$this->form_validation->set_rules('n', 'Modelname', 'required');
			
			if ($this->form_validation->run() == FALSE) {
				// Here is where you do stuff when the submitted form is invalid.
				$cr = ERROR_MISS_PRM;
			} else {
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
// 						'allowed_types'	=> 'jpg|png|gcode',
	 					'allowed_types'	=> '*',
						'overwrite'		=> TRUE,
						'remove_spaces'	=> TRUE,
						'encrypt_name'	=> TRUE,
				);
				$this->load->library('upload', $upload_config);
//	 			$this->upload->initialize();
				
				//check gcode file required
				if ($this->upload->do_upload('f')) {
					//gcode file
					$api_data['f'] = $this->upload->data();
					
					//picture
					for($i=1; $i <= PRINTLIST_MAX_FILE_PIC; $i++) {
						if ($this->upload->do_upload("p$i")) {
							$api_data["p$i"] = $this->upload->data();
//						} else {
//							//treat error - optional
						}
					}
					
					//call function
					$cr = ModelList_add($api_data);
				} else {
					//treat error - missing gcode file
//					$display .= $this->upload->display_errors(); //test
					$cr = ERROR_MISS_PRM;
				}
			}
		} else {
			$this->load->view('test/printlist_form');
			return;
		}
		
		$display = $cr . " " . t(MyERRMSG($cr));
		$this->output->set_status_header($cr, $display);
// 		http_response_code($cr);
		echo $display; //optional
		
		return;
	}
	
	public function deletemodel() {
		$mid = '';
		$display = '';
		$cr = 0; //return code
		
		$this->load->helper(array('errorcode', 'printlist'));
		
		$mid = $this->input->get('id'); //return false if missing
		
		//check mid
		if ($mid) {
			//call function
			$cr = ModelList_delete($mid);
		} else {
			$cr = ERROR_MISS_PRM;
		}
		$display = $cr . " " . t(MyERRMSG($cr));

		$this->output->set_status_header($cr, $display);
// 		http_response_code($cr);
		echo $display; //optional
		
		return;
	}
	
	public function listmodel() {
		$display = '';
		$cr = 0; //return code
		
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
			if ($cr == ERROR_OK) {
// 				header('Content-Length: ' . filesize($path_pid));
// 				header('Content-Type: ' . get_mime_by_extension($path_pid));
// 				header('Content-Disposition: inline; filename="img' . $pid . '";'); //filename header
// 				exit(file_get_contents($path_pid));
				$this->output->set_content_type(get_mime_by_extension($path_pid))->set_output(file_get_contents($path_pid));
				return;
			}
		} else {
			$cr = ERROR_MISS_PRM;
		}

		$display = $cr . " " . t(MyERRMSG($cr));
		$this->output->set_status_header($cr, $display);
// 		http_response_code($cr);
 		echo $display; //optional
		
		return;
	}
	
	public function printmodel() {
		//TODO finish this controller
		echo 'under construction... :)';
		
		return;
	}
	
	
	//==========================================================
	//another part (end of print list)
	//==========================================================
	
	
}