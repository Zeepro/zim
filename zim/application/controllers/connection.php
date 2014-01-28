<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Connection extends MY_Controller {

    public function index() {
        global $CFG;

        $this->lang->load('connectionmaster', $this->config->item('language'));
        $this->lang->load('connection', $this->config->item('language'));

        $this->template->set('lang', $CFG->config['language_abbr']);
        $this->template->set('header', "<title>" . t('ZeePro Personal Printer 21 - Connection configuration') . "</title>");
        $this->template->load('connectionmaster', 'connection');
    }

    public function wifissid() {
        global $CFG;
    	
    	$this->load->helper ( array (
				'form',
				'url',
    			'zimapi'
		) );
		
		$this->load->library ( 'form_validation' );

		$this->lang->load ( 'connectionmaster', $this->config->item ( 'language' ) );
		$this->lang->load ( 'connectionwifissid', $this->config->item ( 'language' ) );
		
		$this->template->set ( 'lang', $CFG->config ['language_abbr'] );
		$this->template->set ( 'header', "<title>" . t ( 'ZeePro Personal Printer 21 - Connection configuration' ) . "</title>" );
		
		if ($this->form_validation->run () == FALSE) {
			$data['listSSID'] = ZimAPI_listSSID();
			
			$this->template->load ( 'connectionmaster', 'connectionwifissid', $data );
		} else {
			header ( "Location:/connection/wifipswd" );
		}
    }

    public function wifinotvisiblessid() {
        global $CFG;
    	
		$this->lang->load ( 'connectionmaster', $this->config->item ( 'language' ) );
		$this->lang->load ( 'connectionwifinotvisiblessid', $this->config->item ( 'language' ) );
		
        $this->load->helper ( array (
				'form',
				'url'
		) );
		
		$this->load->library ( 'form_validation' );

		$this->form_validation->set_rules('ssid', 'SSID', 'required');
		$this->form_validation->set_message('required', t ('required ssid'));
				
		$this->template->set ( 'lang', $CFG->config ['language_abbr'] );
		$this->template->set ( 'header', "<title>" . t ( 'ZeePro Personal Printer 21 - Connection configuration' ) . "</title>" );
		
		if ($this->form_validation->run () == FALSE) {
			$this->template->load ( 'connectionmaster', 'connectionwifinotvisiblessid');
		} else {
			header ( "Location:/connection/wifipswd?ssid=" . rawurlencode($this->input->post('ssid')));
		}
    }
    
    public function wifipswd() {
        global $CFG;
    	
        $ssid = $id=$this->input->get('ssid');
        
    	$this->load->helper ( array (
				'form',
				'url'
		) );
		
		$this->load->library ( 'form_validation' );
				
		$this->form_validation->set_rules('password', 'password', '');
		
		$this->lang->load ( 'connectionmaster', $this->config->item ( 'language' ) );
		$this->lang->load ( 'connectionwifipswd', $this->config->item ( 'language' ) );
		
		$this->template->set ( 'lang', $CFG->config ['language_abbr'] );
		$this->template->set ( 'header', "<title>" . t ( 'ZeePro Personal Printer 21 - Connection configuration' ) . "</title>" );
		
		if ($this->form_validation->run () == FALSE) {
			$this->template->load ('connectionmaster', 'connectionwifipswd', array('ssid' => $ssid));
		} else {
// 			$arr = array (
// 					"Connection.Topology" => "Network",
// 					"Connection.Support" => "WiFi",
// 					"IP.addresses.V4" => array (
// 							array (
// 									"Address" => "0.0.0.0" 
// 							) 
// 					) 
// 			);
			
// 			$fh = fopen ( $CFG->config ['conf'] . 'Connection.json', 'w' );
// 			fwrite ( $fh, json_encode ( $arr ) );
// 			fclose ( $fh );
			
			header ( "Location:/connection/confirmation" );
		}
    }

	public function wired() {
        global $CFG;
    	
    	$this->load->helper ( array (
				'form',
				'url'
		) );
		
		$this->load->library ( 'form_validation' );
				
		$this->lang->load ( 'connectionmaster', $this->config->item ( 'language' ) );
		$this->lang->load ( 'connectionwired', $this->config->item ( 'language' ) );
		
		$this->template->set ( 'lang', $CFG->config ['language_abbr'] );
		$this->template->set ( 'header', "<title>" . t ( 'ZeePro Personal Printer 21 - Connection configuration' ) . "</title>" );
		
		if ($this->form_validation->run () == FALSE) {
			$this->template->load ( 'connectionmaster', 'connectionwired', $data );
		} else {
// 			$arr = array("Connection.Topology" => "Network",
//             "Connection.Support" => "RJ45"
// 			);

// 	        $fh = fopen($CFG->config['conf'] . 'Connection.json', 'w');
// 	        fwrite($fh, json_encode($arr));
// 	        fclose($fh);
	
			header ( "Location:/connection/confirmation" );
	    }
    }
        
	public function wiredauto() {
        global $CFG;

        // To be managed by API...
        
//         $arr = array("Connection.Topology" => "P2P",
//             "Connection.Support" => "WiFi",
//             "IP.addresses.V4" => array(array("Address" => "0.0.0.0")),
//             "IP.lease" => standard_date("DATE_ISO8601", local_to_gmt(time()))
//         );

//         $fh = fopen($CFG->config['conf'] . 'Connection.json', 'w');
//         fwrite($fh, json_encode($arr));
//         fclose($fh);

		header ( "Location:/connection/confirmation" );
    }
    
    public function wiredadvanced() {
        global $CFG;
    	
    	$this->load->helper ( array (
				'form',
				'url'
		) );
		
		$this->load->library ( 'form_validation' );
				
		$this->lang->load ( 'connectionmaster', $this->config->item ( 'language' ) );
		$this->lang->load ( 'connectionwiredadvanced', $this->config->item ( 'language' ) );
		
		$this->template->set ( 'lang', $CFG->config ['language_abbr'] );
		$this->template->set ( 'header', "<title>" . t ( 'ZeePro Personal Printer 21 - Connection configuration' ) . "</title>" );
		
 		$this->form_validation->set_rules('ip', 'ip error', 'callback_ip_check');
		$this->form_validation->set_message('ip_check', t("ip error"));
 		$this->form_validation->set_rules('mask', 'mask error', 'callback_mask_check');
		$this->form_validation->set_message('mask_check', t("mask error"));
 		$this->form_validation->set_rules('gateway', 'gateway error', 'callback_gateway_check');
		$this->form_validation->set_message('gateway_check', t("gateway error"));
 		$this->form_validation->set_rules('dns', 'dns error', 'callback_ip_check');
 		$this->form_validation->set_message('ip_check', t("dns error"));
 		$this->form_validation->set_error_delimiters('<i>', '</i>');
 		 			
		if ($this->form_validation->run () == FALSE) {
			$this->template->load ( 'connectionmaster', 'connectionwiredadvanced', $data );
		} else {
// 			$arr = array (
// 					"Topology" => "Network",
// 					"Support" => "RJ45",
// 					"IPV4" => array (
// 							array (
// 									"Address" => set_value('ip'), 
// 									"Mask" => set_value('mask'), 
// 									"Gateway" => set_value('gateway'), 
// 									"DNS" => set_value('dns'), 
// 							) 
// 					) 
// 			);

// 	        $fh = fopen($CFG->config['conf'] . 'Connection.json', 'w');
// 	        fwrite($fh, json_encode($arr));
// 	        fclose($fh);
	
			header ( "Location:/connection/confirmation" );
        }
    }
    
	public function ip_check($ip) {
		if (filter_var ( $ip, FILTER_VALIDATE_IP )) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
    
	public function mask_check($mask) {
		if (! $m = ip2long ( $mask ))
			return false;
		
		$m = ~ $m;
		return $m && ~ $m && ! ($m & ($m + 1));
	}
    
	public function gateway_check($ip) {
    // @todo The gateway should be within the mask
		if (filter_var ( $ip, FILTER_VALIDATE_IP )) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
    
	public function wifip2p() {
		$ret_val = 0;
		$error = '';
		$template_data = array();
		$body_page = NULL;
		
		$this->load->helper(array('zimapi'));
		$this->lang->load('connection/wifip2p', $this->config->item('language'));
		
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			
			$this->load->library('form_validation');
			
			$this->form_validation->set_rules('ssid', 'SSID', 'required');
			
			if ($this->form_validation->run() == FALSE) {
				// Here is where you do stuff when the submitted form is invalid.
				$error = t('invalid SSID');
			}
			else {
				$ssid = $this->input->post('ssid');
				$passwd = $this->input->post('pwd');
				
				if (!ctype_print($ssid) || ($passwd && !ctype_print($passwd))) {
					$error = t('invalid data (special character)');
				}
				else {
					$ret_val = ZimAPI_setsWifi($ssid, $passwd);
					if ($ret_val != TRUE) {
						$error = t('invalid data');
					}
					else {
// 						$this->output->set_header("Location:/connection/confirmation");
						$this->confirmation();
						return; // end generating if successed
					}
				}
			}
		}
		
		// generate form to submit when not in post method
		$this->load->library('parser');

		// parse the main body
		$template_data = array(
				'title'			=> t('Personalize your network of printer'),
				'ssid_title'	=> t('Input your network name'),
				'pwd_title'		=> t('Input your password'),
				'error'			=> $error,
				'ok'			=> t('OK'),
				'back'			=> t('back'),
		);
		
		$body_page = $this->parser->parse('template/connection/wifip2p', $template_data, TRUE);
		
		// parse all page
		$template_data = array(
				'lang'			=> $CFG->config ['language_abbr'],
				'headers'		=> '<title>' . t('ZeePro Personal Printer 21 - Connection setting') . '</title>',
				'contents'		=> $body_page,
		);
		
		$this->parser->parse('template/basetemplate', $template_data);
		
		return;
	}
    
	public function confirmation() {
        global $CFG;

        $this->lang->load('connectionmaster', $this->config->item('language'));
        $this->lang->load('confirmation', $this->config->item('language'));

        $this->template->set('lang', $CFG->config['language_abbr']);
        $this->template->set('header', "<title>" . t('ZeePro Personal Printer 21 - Connection configuration') . "</title>");
        $this->template->load('connectionmaster', 'confirmation');

	}
    
}