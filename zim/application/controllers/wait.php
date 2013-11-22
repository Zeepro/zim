<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Wait extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper(array('form', 'url', 'json'));
    }

    public function index() {
        // General purpose wait controler (no control)
        global $CFG;

        $arr = json_read($CFG->config['base_data'] . 'conf\\Work.json');

        if ($arr["error"] or $arr["json"]["State"] == "Halted") {
            header("Location:/error");
            exit;
        }

        if ($arr["json"]["State"] == "Done") {
            header("Location:/");
            exit;
        }

        $this->lang->load('master', $this->config->item('language'));
        $this->lang->load('wait', $this->config->item('language'));

        $this->template->set('lang', $CFG->config['language_abbr']);

        $this->load->view('/wait', array("lang" => $CFG->config['language_abbr']));
    }

    public function printing() {
        // Wait controler for printing
        global $CFG;

        $this->lang->load('wait', $this->config->item('language'));

        switch ($_SERVER['REQUEST_METHOD']) {
            case 'POST':
                $arr = json_read($CFG->config['base_data'] . 'conf\\Work.json');

                if ($arr["error"] or $arr["json"]["State"] == "Halted") {
                    header("Location:/error");
                    exit;
                }

                switch ($_POST['submitbutton']) {
                    case t("Cancel"):
                        exec('"' . $CFG->config['bin'] . 'CancelPrintModel" -callbackurl "/wait"');
                        header("Location:/wait");
                        exit;
                }
            default:
                $arr = json_read($CFG->config['base_data'] . 'conf\\Work.json');

                if ($arr["error"] or $arr["json"]["State"] == "Halted") {
                    header("Location:/error");
                    exit;
                }

                if ($arr["json"]["State"] == "Done") {
                    header("Location:/");
                    exit;
                }
        }

        $this->lang->load('master', $this->config->item('language'));

        $this->template->set('lang', $CFG->config['language_abbr']);

        $this->load->view('/waitprinting', array("lang" => $CFG->config['language_abbr']));
    }

}