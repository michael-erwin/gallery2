<?php
class Update extends CI_Controller
{
    private $permissions = ['all'];
    
    function __construct()
    {
        parent::__construct();
        $this->load->model('m_photo');
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
            if(!in_array('all',$this->permissions) && !in_array('photo_edit',$this->permissions))
            {
                $response['code'] = '403';
                $response['message'] = "You're not authorized to perform this action.";
                header("Content-Type: application/json");
                echo json_encode($response);
                exit();
            }

            $title = clean_title_text($this->input->post('title'));
            $description = clean_body_text($this->input->post('description'));
            $tags = clean_title_text($this->input->post('tags'));
            $category = clean_numeric_text($this->input->post('category_id'));

            if($affected = $this->m_photo->update($id,$title,$description,$tags,$category))
            {
                $response['status'] = "ok";
                $response['message'] = "{$affected} photo(s) updated.";
                $response['debug_info']['affected_rows'] = $affected;
                if(strlen($tags) > 2) $this->m_tag->add(explode(' ', $tags));
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
