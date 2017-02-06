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
    $text = "John 
Doe";
        echo preg_replace(['/ /','/\n/'], ['_',''], $text);
    }
}
