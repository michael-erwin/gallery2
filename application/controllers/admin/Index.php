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
            $query_strings = '?redir='.base_url(uri_string());
            $query_strings .= '&auth_error=Please login using authorized account to access page.';
            header("Location: ".base_url('account/signin').$query_strings);
            exit();
        }
        else
        {
            header("Location: ".base_url('admin/dashboard'));
        }
    }
}