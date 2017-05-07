<?php
class Update extends CI_Controller
{
    private $permissions = [];
    
    function __construct()
    {
        parent::__construct();
        $this->permissions = $this->auth->get_permissions();
        $this->load->model('M_Presentations');
    }

    public function _remap()
    {
        $id = $this->input->post('id');
        $title = $this->input->post('title');
        $description = clean_body_text($this->input->post('description'));

        $response = [
            'status' => 'error',
            'code' => 500,
            'message' => 'Invalid request.'
        ];

        if(isset($_POST) && count($_POST) > 1)
        {
            $id = [];
            $fields = [];

            foreach ($_POST as $name => $value) {
                if($name == "id")
                {
                    if(is_numeric($value)) $id[] = $value;
                }
                else
                {
                    $fields[$name] = $value;
                }
            }

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

    private function check_title($title)
    {
        return preg_match('/^[a-z][a-z0-9\-\_ ]*$/i', $title);
    }
}
