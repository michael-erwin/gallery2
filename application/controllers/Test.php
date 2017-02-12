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
        $fruits = [
            'orange_',
            '"banana"',
            'guava,',
            'apple.'
        ];
        $fruits_clean = [];
        foreach ($fruits as $value) {
            $fruits_clean[] = clean_alpha_text($value);
        }

        header("Content-Type: text/plain");
        print_r($fruits);
        echo "\n";
        print_r($fruits_clean);
    }
}
