<?php
/**
* Users index.
*/
class Index extends CI_Controller
{
    private $permissions = [];
    
    function __construct()
    {
        parent::__construct();
        $this->permissions = $this->auth->get_permissions();
    }

       public function _remap()
    {
        
    }
}