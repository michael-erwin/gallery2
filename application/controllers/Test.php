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
        $favcolor = "";

        switch ($favcolor) {
            case "red":
                echo "Your favorite color is red!";
                break;
            case "blue":
                echo "Your favorite color is blue!";
                break;
            case "green":
                echo "Your favorite color is green!";
                break;
        }
    }
}
