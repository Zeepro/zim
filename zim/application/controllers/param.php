<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Param extends MY_Controller {

    public function index() {
        global $CFG;

        $this->lang->load('master', $this->config->item('language'));
        $this->lang->load('ui', $this->config->item('language'));

        $this->template->set('lang', $CFG->config['language_abbr']);
        $this->template->set('header', "<title>" . t('ZeePro Personal Printer 21 - Parameters') . "</title>");
        $this->template->load('master', 'param');
    }

}