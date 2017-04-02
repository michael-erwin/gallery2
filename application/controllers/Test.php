<?php
/**
* Tester
*/
class Test extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
    }
    public function _remap()
    {
        //echo $this->uri->segment(2);
        $this->index();
    }
    public function index()
    {
        // header("Content-Type: text/plain");
        // print_r($_SESSION);
        // echo parse_url(base_url())['host'];
        // echo $this->uri->segment(2);
        // echo date($this->config->item('log_date_format'),time());
        // echo $this->config->item('log_date_format');
    }
}
