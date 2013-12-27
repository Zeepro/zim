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

		$this->form_validation->set_rules('ssid', $CFG->config['language_abbr'] . ':ssid error', 'callback_ssid_check');
				
		$this->lang->load ( 'connectionmaster', $this->config->item ( 'language' ) );
		$this->lang->load ( 'connectionwifissid', $this->config->item ( 'language' ) );
		
		$this->template->set ( 'lang', $CFG->config ['language_abbr'] );
		$this->template->set ( 'header', "<title>" . t ( 'ZeePro Personal Printer 21 - Connection configuration' ) . "</title>" );
		
		if ($this->form_validation->run () == FALSE) {
			$data['listSSID'] = ListSSID();
			
			$this->template->load ( 'connectionmaster', 'connectionwifissid', $data );
		} else {
			header ( "Location:/connection/wifipswd" );
		}
    }


    public function ssid_check($str)
    {
    	if ($str == 'choose-one')
    	{
    		$this->form_validation->set_message('ssid', t ( 'ssid error' ));
    		return FALSE;
    	}
    	else
    	{
    		return TRUE;
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
				
		$this->lang->load ( 'connectionmaster', $this->config->item ( 'language' ) );
		$this->lang->load ( 'connectionwifipswd', $this->config->item ( 'language' ) );
		
		$this->template->set ( 'lang', $CFG->config ['language_abbr'] );
		$this->template->set ( 'header', "<title>" . t ( 'ZeePro Personal Printer 21 - Connection configuration' ) . "</title>" );
		
		if ($this->form_validation->run () == FALSE) {
			$this->template->load ('connectionmaster', 'connectionwifipswd', array('ssid' => $ssid));
		} else {
			$arr = array (
					"Connection.Topology" => "Network",
					"Connection.Support" => "WiFi",
					"IP.addresses.V4" => array (
							array (
									"Address" => "0.0.0.0" 
							) 
					) 
			);
			
			$fh = fopen ( $CFG->config ['conf'] . 'Connection.json', 'w' );
			fwrite ( $fh, json_encode ( $arr ) );
			fclose ( $fh );
			
			header ( "Location:/" );
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
			$arr = array("Connection.Topology" => "Network",
            "Connection.Support" => "RJ45"
			);

	        $fh = fopen($CFG->config['conf'] . 'Connection.json', 'w');
	        fwrite($fh, json_encode($arr));
	        fclose($fh);
	
	        header ( "Location:/" . $CFG->config ['language_abbr'] );
        }
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
		
		if ($this->form_validation->run () == FALSE) {
			$this->template->load ( 'connectionmaster', 'connectionwiredadvanced', $data );
		} else {
			$arr = array("Connection.Topology" => "Network",
            "Connection.Support" => "RJ45"
			);

	        $fh = fopen($CFG->config['conf'] . 'Connection.json', 'w');
	        fwrite($fh, json_encode($arr));
	        fclose($fh);
	
	        header ( "Location:/" . $CFG->config ['language_abbr'] );
        }
    }
        
    public function wifip2p() {
        global $CFG;

        // To be managed by API...
        
        $arr = array("Connection.Topology" => "P2P",
            "Connection.Support" => "WiFi",
            "IP.addresses.V4" => array(array("Address" => "0.0.0.0")),
            "IP.lease" => standard_date("DATE_ISO8601", local_to_gmt(time()))
        );

        $fh = fopen($CFG->config['conf'] . 'Connection.json', 'w');
        fwrite($fh, json_encode($arr));
        fclose($fh);

        header("Location:/" . $CFG->config['language_abbr']);
    }
}