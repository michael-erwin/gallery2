<?php
class Move extends CI_Controller
{
    private $permissions = [];

    function __construct()
    {
        parent::__construct();
        $this->permissions = $this->auth->get_permissions();
        $this->load->model('m_video');
        $this->load->model('m_tag');
    }

    public function _remap($method=null)
    {
        $this->update();
    }

    private function update()
    {
        $response = [
            "status" => "error",
            "message" => "No id is set.",
            "data" => null,
            "debug_info" => ["affected_rows"=>0]
        ];

        $id = $this->input->post('id');

        if($id)
        {
            if(!in_array('all',$this->permissions) && !in_array('video_change_category',$this->permissions))
            {
                $response['code'] = '403';
                $response['message'] = "You're not authorized to perform this action.";
                header("Content-Type: application/json");
                echo json_encode($response);
                exit();
            }

            $category = clean_numeric_text($this->input->post('category_id'));

            if($affected = $this->m_video->update($id,"","","",$category))
            {
                $response['status'] = "ok";
                $response['message'] = "{$affected} video(s) updated.";
                $response['debug_info']['affected_rows'] = $affected;
            }
            else
            {
                $response['message'] = "Update failed. Database error occured.";
            }
        }

        header("Content-Type: application/json");
        echo json_encode($response);
    }
}