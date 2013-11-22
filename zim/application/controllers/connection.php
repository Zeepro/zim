<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Connection extends MY_Controller {

    public function index() {
        global $CFG;

        $this->lang->load('master', $this->config->item('language'));
        $this->lang->load('connection', $this->config->item('language'));

        $this->template->set('lang', $CFG->config['language_abbr']);
        $this->template->set('header', "<title>" . t('ZeePro Personal Printer 21 - Connection configuration') . "</title>");
        $this->template->load('master', 'connection');
    }

    public function wifinetwork() {
        global $CFG;

        $this->load->helper('date');

        // To be managed by API...
        
        $arr = array("Connection.Topology" => "Network",
            "Connection.Support" => "WiFi",
            "IP.addresses.V4" => array(array("Address" => "0.0.0.0")),
            "IP.lease" => standard_date("DATE_ISO8601", local_to_gmt(time()))
        );

        $fh = fopen($CFG->config['base_data'] . 'conf\\Connection.json', 'w');
        fwrite($fh, json_encode($arr));
        fclose($fh);

        header("Location:/" . $CFG->config['language_abbr']);
    }

    public function wirednetwork() {
        global $CFG;

        $this->load->helper('date');

        // To be managed by API...
        
        $arr = array("Connection.Topology" => "Network",
            "Connection.Support" => "RJ45",
            "IP.addresses.V4" => array(array("Address" => "0.0.0.0")),
            "IP.lease" => standard_date("DATE_ISO8601", local_to_gmt(time()))
        );

        $fh = fopen($CFG->config['base_data'] . 'conf\\Connection.json', 'w');
        fwrite($fh, json_encode($arr));
        fclose($fh);

        header("Location:/" . $CFG->config['language_abbr']);
    }

    public function wifip2p() {
        global $CFG;

        $this->load->helper('date');

        // To be managed by API...
        
        $arr = array("Connection.Topology" => "P2P",
            "Connection.Support" => "WiFi",
            "IP.addresses.V4" => array(array("Address" => "0.0.0.0")),
            "IP.lease" => standard_date("DATE_ISO8601", local_to_gmt(time()))
        );

        $fh = fopen($CFG->config['base_data'] . 'conf\\Connection.json', 'w');
        fwrite($fh, json_encode($arr));
        fclose($fh);

        header("Location:/" . $CFG->config['language_abbr']);
    }

    public function wiredp2p() {
        global $CFG;

        $this->load->helper('date');

        // To be managed by API...
        
        $arr = array("Connection.Topology" => "P2P",
            "Connection.Support" => "RJ45",
            "IP.addresses.V4" => array(array("Address" => "0.0.0.0")),
            "IP.lease" => standard_date("DATE_ISO8601", local_to_gmt(time()))
        );

        $fh = fopen($CFG->config['base_data'] . 'conf\\Connection.json', 'w');
        fwrite($fh, json_encode($arr));
        fclose($fh);

        header("Location:/" . $CFG->config['language_abbr']);
    }

}