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
        $sql = "SELECT COUNT(*) as 'count' FROM `photos` WHERE 1";
        $query = $this->db->query($sql);
        $result = $query->result_array()[0];
        print_r($result['count']);
    }
}
