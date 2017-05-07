<?php
class Get extends CI_Controller
{
    private $permissions = [];
    
    function __construct()
    {
        parent::__construct();
        $this->permissions = $this->auth->get_permissions();
        $this->load->model('M_Presentations');
        if(!in_array('all',$this->permissions) && !in_array('presentation_add',$this->permissions) && !in_array('presentation_edit',$this->permissions))
        {
            $response['code'] = '403';
            $response['message'] = "You're not authorized to perform this action.";
            header("Content-Type: application/json");
            echo json_encode($response);
            exit();
        }
    }

    public function _remap()
    {

        $response = [
            'status' => 'error',
            'code' => 500,
            'message' => 'Invalid request.'
        ];

        $get_result = $this->M_Presentations->get();
        
        if($get_result['code'] == 'SUCCESS')
        {
            $response['status'] = "ok";
            $response['code'] = 200;
            $response['data']['items'] = $get_result['items'];
            $response['message'] = "Items found: {$get_result['info']['fetched']}.";
        }
        else
        {
            $response['message'] = "Database error has occured.";
            $response['dbg_info'] = $get_result;
        }

        header("Content-Type: application/json");
        echo json_encode($response);
    }
}
