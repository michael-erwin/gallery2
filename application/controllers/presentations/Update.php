<?php
class Update extends CI_Controller
{
    private $permissions = [];
    
    function __construct()
    {
        parent::__construct();
        $this->permissions = $this->auth->get_permissions();
        $this->load->model('M_Presentations');
        $this->load->model('M_Presentation_Items');
    }

    public function _remap()
    {
        $item = $this->uri->segment(3);
        if($item == "item")
        {
            $this->update_item();
            /*$response = [
                'status' => 'error',
                'code' => 500,
                'message' => 'Invalid request.',
                'data' => [
                    'segment' => $item,
                    'id' => $this->input->post('id'),
                    'caption' => $this->input->post('caption')
                ]
            ];
            header("Content-Type: application/json");
            echo json_encode($response);*/
        }
        else
        {
            $this->update_entry();
        }
    }

    public function update_entry()
    {
        $id = $this->input->post('id');
        $response = [
            'status' => 'error',
            'code' => 500,
            'message' => 'Invalid request.'
        ];

        if(is_numeric($id))
        {
            $id = [$id];
            $fields = [];

            foreach ($_POST as $name => $value) if($name != "id") $fields[$name] = $value;

            if(count($id) > 0 && count($fields) > 0)
            {
                if(!in_array('all',$this->permissions) && !in_array('presentation_edit',$this->permissions))
                {
                    $response['code'] = '403';
                    $response['message'] = "You're not authorized to perform this action.";
                    header("Content-Type: application/json");
                    echo json_encode($response);
                    exit();
                }

                $update_result = $this->M_Presentations->update($fields,$id);
                if($update_result['code'] == 'SUCCESS')
                {
                    $response['status'] = "ok";
                    $response['code'] = 200;
                    $response['message'] = "Item updated.";
                }
                else
                {
                    $response['message'] = "Database error has occured.";
                    $response['dbg_info'] = $update_result;
                }
            }

            header("Content-Type: application/json");
            echo json_encode($response);
        }
        else
        {
            show_404(); exit();
        }
    }

    public function update_item()
    {
        $id = $this->input->post('id');
        $response = [
            'status' => 'error',
            'code' => 500,
            'message' => 'Invalid request.'
        ];

        if(is_numeric($id))
        {
            $id = [$id];
            $fields = [];

            foreach ($_POST as $name => $value) if($name != "id") $fields[$name] = $value;

            if(count($id) > 0 && count($fields) > 0)
            {
                if(!in_array('all',$this->permissions) && !in_array('presentation_edit',$this->permissions))
                {
                    $response['code'] = '403';
                    $response['message'] = "You're not authorized to perform this action.";
                    header("Content-Type: application/json");
                    echo json_encode($response);
                    exit();
                }

                $update_result = $this->M_Presentation_Items->update($fields,$id);
                if($update_result['code'] == 'SUCCESS')
                {
                    $response['status'] = "ok";
                    $response['code'] = 200;
                    $response['message'] = "Item updated.";
                }
                else
                {
                    $response['message'] = "Database error has occured.";
                    $response['dbg_info'] = $update_result;
                }
            }

            header("Content-Type: application/json");
            echo json_encode($response);
        }
        else
        {
            show_404(); exit();
        }
    }

    private function check_title($title)
    {
        return preg_match('/^[a-z][a-z0-9\-\_ ]*$/i', $title);
    }
}
