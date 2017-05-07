<?php
class Delete extends CI_Controller
{
    private $permissions = [];
    
    function __construct()
    {
        parent::__construct();
        $this->permissions = $this->auth->get_permissions();
        $this->load->model('M_Presentations');
        if(!in_array('all',$this->permissions) && !in_array('presentation_delete',$this->permissions))
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

        $id = explode(',', $this->input->post('id'));
        $delete_result = $this->M_Presentations->delete($id);

        if($delete_result['code'] == 'SUCCESS')
        {
            $response['status'] = "ok";
            $response['code'] = 200;
            $response['message'] = "Items deleted: {$delete_result['info']['affected']}.";
        }
        else
        {
            $response['message'] = "Database error has occured.";
            $response['dbg_info'] = $delete_result;
        }

        header("Content-Type: application/json");
        echo json_encode($response);
    }
}
