<?php
class Add extends CI_Controller
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
        $title = $this->input->post('title');
        $description = clean_body_text($this->input->post('description'));

        $response = [
            'status' => 'error',
            'code' => 500,
            'message' => 'Invalid request.'
        ];

        if($title && $description)
        {
            $title = trim($title);
            if(strlen($title) == 0)
            {
                $response['code'] = 400;
                $response['message'] = "Title cannot be empty.";
            }
            if(strlen($title) < 3)
            {
                $response['code'] = 403;
                $response['message'] = "Title is too short.";
            }
            elseif(!$this->check_title($title))
            {
                $response['code'] = 400;
                $response['message'] = "Title contains invalid characters.";
            }
            else
            {
                if(!in_array('all',$this->permissions) && !in_array('presentation_add',$this->permissions))
                {
                    $response['code'] = '403';
                    $response['message'] = "You're not authorized to perform this action.";
                    header("Content-Type: application/json");
                    echo json_encode($response);
                    exit();
                }

                $add_result = $this->M_Presentations->insert(['title'=>$title,'description'=>$description, 'share_level'=>'private']);
                if($add_result['code'] == 'SUCCESS')
                {
                    $response['status'] = "ok";
                    $response['code'] = 200;
                    $response['message'] = "New item added successfully.";
                }
                else
                {
                    $response['message'] = "Database error has occured.";
                    $response['dbg_info'] = $add_result;
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
