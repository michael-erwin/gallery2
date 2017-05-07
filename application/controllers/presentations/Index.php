<?php
class Index extends CI_Controller
{
    private $permissions = [];
    
    function __construct()
    {
        parent::__construct();
        $this->permissions = $this->auth->get_permissions();
        $this->load->model('M_Presentations');
    }

    public function _remap()
    {
        show_404(); exit();
    }
}
