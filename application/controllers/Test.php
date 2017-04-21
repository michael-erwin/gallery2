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
        $this->index();
    }
    public function index()
    {
        $share_level = isset($_SESSION['user']['id'])? "(`share_level`='public' OR `share_level` LIKE '%[{$_SESSION['user']['id']}]%')" : "`share_level`='public'";
        echo $share_level;
    }
}
