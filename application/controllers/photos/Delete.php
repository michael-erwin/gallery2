<?php
/**
* Delete photos
*/
class Delete extends CI_Controller
{
    private $media_path;
    private $permissions = [];

    function __construct()
    {
        parent::__construct();
        $this->permissions = $this->auth->get_permissions();
        $this->config->load('media_gallery');
        $this->media_path = $this->config->item('mg_media_path');
        $this->load->model("m_photo");
    }

    public function _remap()
    {
        $this->delete();
    }

    public function delete()
    {
        $id = $this->input->post('id');
        if($id)
        {
            $response = [
                "status" => "error",
                "message" => "Internal error has occured."
            ];
            if(!in_array('all',$this->permissions) && !in_array('photo_delete',$this->permissions))
            {
                $response['code'] = '403';
                $response['message'] = "You're not authorized to perform this action.";
                header("Content-Type: application/json");
                echo json_encode($response);
                exit();
            }
            if($deleted = $this->m_photo->delete($id))
            {
                $response["status"] = "ok";
                $response["message"] = "{$deleted} files deleted.";
            }

            header("Content-Type: application/json");
            echo json_encode($response);
        }
    }
}
