<?php
class Index extends CI_Controller
{
    private $permissions = [];
    
    function __construct()
    {
        parent::__construct();
        $this->permissions = $this->auth->get_permissions();
    }

    public function index($option=null)
    {
        if(!in_array('all', $this->permissions) && !in_array('admin_access', $this->permissions))
        {
            header("Location: ".base_url('/'));
            exit();
        }
        else
        {
            header("Location: ".base_url('admin/dashboard'));
        }
    }
}