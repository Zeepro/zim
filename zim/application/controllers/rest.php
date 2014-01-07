<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

if (!defined('RETURN_CONTENT_TYPE')) {
	define('RETURN_CONTENT_TYPE', 'text/plain; charset=UTF-8');
}

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
	
		$this->load->helper(array('printlist', 'errorcode'));
	
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
				if ($this->input->post('c1')) {
					$api_data['c1'] = $this->input->post('c1');
				}
				if ($this->input->post('c2')) {
					$api_data['c2'] = $this->input->post('c2');
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
		$this->output->set_content_type(RETURN_CONTENT_TYPE);
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
		$this->output->set_content_type(RETURN_CONTENT_TYPE);
		echo $display; //optional
		
		return;
	}
	
	public function listmodel() {
		$display = '';
		
		$this->load->helper('printlist');
		
		$display = ModelList_list();
		$this->output->set_content_type(RETURN_CONTENT_TYPE);
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
		$this->output->set_content_type(RETURN_CONTENT_TYPE);
 		echo $display; //optional
		
		return;
	}
	
	public function preslicedprint() {
		$mid = '';
		$cr = 0; //return code
		
		$this->load->helper(array('errorcode', 'printlist'));
		
		$mid = $this->input->get('id'); //return false if missing
		
		if ($mid) {
			$cr = ModelList_print($mid);
		}
		else {
			$cr = ERROR_MISS_PRM;
		}
		
		if ($cr == ERROR_OK) {
			//TODO change status file to indicate we are in printing now,
			// but think another condition:
			// when we have finished printing, how can we know that?
			// arcontrol client return directly, and will not infect file system with json file.
			// perhaps we have to lance a thread of PHP to check print status by arcontrol
			// time by time util the printing is finished?
			// if not, we will rely on the client to check status,
			// that means we force the client only accessing check print status page when we are in printing.
			// in that way, we can know when the printing is finished, and then change the status in json file
		}

		$display = $cr . " " . t(MyERRMSG($cr));
		$this->output->set_status_header($cr, $display);
// 		http_response_code($cr);
		$this->output->set_content_type(RETURN_CONTENT_TYPE);
 		echo $display; //optional
		
		return;
	}
	
	
	//==========================================================
	//printer state web service
	//==========================================================
	public function status() {
		$display = NULL;
		
		$this->load->helper(array('errorcode', 'printerstate'));
		
		$display = PrinterState_checkStatus();
		$this->output->set_content_type(RETURN_CONTENT_TYPE);
		echo $display;
		
		return;
	}
	
	public function get() {
		$parameter = NULL;
		$cr = 0;
		$display = NULL;
		$api_prm = NULL;
		
		$this->load->helper(array('errorcode', 'printerstate'));
		
		$parameter = $this->input->get('p'); //return false if missing
		
		if ($parameter) {
			switch($parameter) {
				case PRINTERSTATE_PRM_EXTRUDER:
					$cr = PrinterState_getExtruder($display); //$abb_extruder
// 					if ($cr == ERROR_INTERNAL) {
// 						$display = 'INTERNAL';
// 					}
					break;
					
				case PRINTERSTATE_PRM_TEMPER:
					// check which temperature we want
					$has_e = $this->input->get('e');
					$has_h = $this->input->get('h');
					if (is_null($has_e) && is_null($has_h)) {
						$cr = ERROR_MISS_PRM;
					}
					else if ($has_e && $has_h) {
						$cr = ERROR_WRONG_PRM;
					}
					else {
						$api_prm = ($has_e) ? 'e' : 'h';
						$cr = PrinterState_getTemperature($display, $api_prm);
// 						if ($cr == ERROR_INTERNAL) {
// 							echo 'INTERNAL';
// 							return;
// 						}
					}
					break;
					
				case PRINTERSTATE_PRM_CARTRIDGE:
					$api_prm = $this->input->get('v');
					$cr = PrinterState_getCartridge($display, $api_prm);
					break;
					
				default:
					$cr = ERROR_WRONG_PRM;
					break;
			}
		} else {
			$cr = ERROR_MISS_PRM;
		}
		
		if ($cr != ERROR_OK) {
			$display = $cr . " " . t(MyERRMSG($cr));
		}
		$this->output->set_status_header($cr, $display);
// 		http_response_code($cr);
		$this->output->set_content_type(RETURN_CONTENT_TYPE);
 		echo $display;
		
		return;
	}
	
	public function set() {
		$parameter = NULL;
		$cr = 0;
		$display = NULL;
		$api_prm = NULL;
		
		$this->load->helper(array('errorcode', 'printerstate'));
		
		$parameter = $this->input->get('p'); //return false if missing
		
		if ($parameter) {
			switch($parameter) {
				case PRINTERSTATE_PRM_EXTRUDER:
					$api_prm = $this->input->get('v');
					if ($api_prm) {
						$cr = PrinterState_setExtruder($api_prm);
					} else {
						$cr = ERROR_MISS_PRM;
					}
					break;
					
				case PRINTERSTATE_PRM_TEMPER:
					// check which temperature we want
					$val_temper = 0;
					
					$val_temper = $this->input->get('v');
					$has_e = $this->input->get('e');
					$has_h = $this->input->get('h');
					if (is_null($has_e) && is_null($has_h)) {
						$cr = ERROR_MISS_PRM;
					}
					else if ($has_e && $has_h) {
						$cr = ERROR_WRONG_PRM;
					}
					else {
						$api_prm = ($has_e) ? 'e' : 'h';
						$cr = PrinterState_setTemperature($val_temper, $api_prm);
// 						if ($cr == ERROR_INTERNAL) {
// 							echo 'INTERNAL';
// 							return;
// 						}
					}
					break;
					
				default:
					$cr = ERROR_WRONG_PRM;
					break;
			}
		} else {
			$cr = ERROR_MISS_PRM;
		}
		
		if ($cr != ERROR_OK) {
			$display = $cr . " " . t(MyERRMSG($cr));
		}
		$this->output->set_status_header($cr, $display);
// 		http_response_code($cr);
		$this->output->set_content_type(RETURN_CONTENT_TYPE);
		echo $display; //optional
		
		return;
		
	}
	
	//==========================================================
	//another part (end of print list)
	//==========================================================
	
	
}