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
        echo $this->uri->segment(2);
    }
    public function index()
    {
        // header("Content-Type: text/plain");
        // print_r($_SESSION['user']);
        // echo parse_url(base_url())['host'];
        echo $this->uri->segment(2);
    }
}
