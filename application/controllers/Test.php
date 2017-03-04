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
        echo CI_VERSION;
    }
}
