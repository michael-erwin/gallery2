<?php
/**
* Tester
*/
class Test extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('M_Presentations');
    }
    public function _remap()
    {
        $this->index();
    }
    public function index()
    {
        
    }
}
