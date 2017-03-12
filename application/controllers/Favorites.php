<?php
/**
*
*/
class Favorites extends CI_Controller
{
    private $permissions = [];

    function __construct()
    {
        parent::__construct();
        $this->permissions = $this->auth->get_permissions();
    }

    public function _remap()
    {
        if(!in_array('all',$this->permissions) && !in_array('profile_view',$this->permissions))
        {
            $response = [
                'status' => 'error',
                'code' => 403,
                'message' => "You're not authorized to perform this action. Please contact administrator."
            ];
            header("Content-Type: application/json");
            echo json_encode($response);
            exit();
        }
        elseif(!isset($_SESSION['user']['id']))
        {
            $response = [
                'status' => "error",
                'code' => 500,
                'message' => "Your don't have active session. Please login to your account.",
                'data' => null
            ];
            header("Content-Type: application/json");
            echo json_encode($response);
            exit();
        }
        
        $action = $this->uri->segment(2);
        $media  = $this->uri->segment(3);

        if($action == "get")
        {
            $this->get_all();
        }
        elseif($action == "add")
        {
            $id = $this->input->get_post('id');
            if($media == "photo") $this->add_photo($id);
            elseif($media == "video") $this->add_video($id);
        }
        elseif($action == "remove")
        {
            $id = $this->input->get_post('id');
            if($media == "photo") $this->remove_photo($id);
            elseif($media == "video") $this->remove_video($id);
        }
    }
    
    private function get_all($raw=null)
    {
        $response = [
            'status' => "error",
            'code' => 500,
            'message' => "Unknown error has occured.",
            'data' => ['photos'=>"",'videos'=>""]
        ];

        $user_id = $_SESSION['user']['id'];
        $get_qry = "SELECT * FROM `favorites` WHERE `user_id`={$user_id} ORDER BY `id`";

        if($query = $this->db->query($get_qry))
        {
            $media = ['photo'=>[],'video'=>[]];
            $items = $query->result_array();
            
            foreach($items as $item)
            {
                $media[$item['media_type']][] = $item['media_id'];
            }

            $response['status'] = "ok";
            $response['code'] = 200;
            $response['message'] = "Items retreived.";
            $response['data'] = [
                'photos' => $media['photo'],
                'videos' => $media['video']
            ];
        }
        else{
            $response['message'] = "Database query failed.";
        }

        if($raw)
        {
            return $response['data'];
        }

        header("Content-Type: application/json");
        echo json_encode($response);
    }

    private function add_photo($id)
    {
        $response = [
            'status' => "error",
            'code' => 500,
            'message' => "Database insert failed.",
            'data' => ['photos'=>"",'videos'=>""]
        ];
        $id = clean_numeric_text($id);
        $user_id = $_SESSION['user']['id'];
        $check_qry = "SELECT `id` FROM `favorites` WHERE `media_type`='photo' AND `media_id`={$id}";
        $insert_qry = "INSERT INTO `favorites` SET `media_id`={$id},`media_type`='photo', `user_id`={$user_id}";

        if(strlen($id) == 0)
        {
            $response['message'] = "Invalid input id.";
        }
        elseif($check_data = $this->db->query($check_qry))
        {
            if($check_data->num_rows() > 0)
            {
                $response['code'] = 208;
                $response['message'] = "Media item already added.";
            }
            else{
                if($this->db->query($insert_qry))
                {
                    $response['status'] = "ok";
                    $response['message'] = "Photo entry added to favorites.";
                    $response['data'] = $this->get_all(true);
                }
            }
        }
        else
        {
            $response['message'] = "Database query failed.";
        }

        header("Content-Type: application/json");
        echo json_encode($response);
    }

    private function add_video($id)
    {
        $response = [
            'status' => "error",
            'code' => 500,
            'message' => "Database insert failed.",
            'data' => ['photos'=>"",'videos'=>""]
        ];
        $id = clean_numeric_text($id);
        $user_id = $_SESSION['user']['id'];
        $check_qry = "SELECT `id` FROM `favorites` WHERE `media_type`='video' AND `media_id`={$id}";
        $insert_qry = "INSERT INTO `favorites` SET `media_id`={$id},`media_type`='video', `user_id`={$user_id}";

        if(strlen($id) == 0)
        {
            $response['message'] = "Invalid input id.";
        }
        elseif($check_data = $this->db->query($check_qry))
        {
            if($check_data->num_rows() > 0)
            {
                $response['code'] = 208;
                $response['message'] = "Media item already added.";
            }
            else{
                if($this->db->query($insert_qry))
                {
                    $response['status'] = "ok";
                    $response['message'] = "Video added to favorites.";
                    $response['data'] = $this->get_all(true);
                }
            }
        }
        else
        {
            $response['message'] = "Database query failed.";
        }

        header("Content-Type: application/json");
        echo json_encode($response);
    }

    private function remove_photo($id)
    {
        $response = [
            'status' => "error",
            'code' => 500,
            'message' => "Database insert failed.",
            'data' => ['photos'=>"",'videos'=>""]
        ];
        $id = clean_numeric_text($id);
        $user_id = $_SESSION['user']['id'];
        $delete_qry = "DELETE FROM `favorites` WHERE `media_type`='photo' AND `media_id`={$id}";

        if(strlen($id) == 0)
        {
            $response['message'] = "Invalid input id.";
        }
        elseif($this->db->query($delete_qry))
        {
            $response['status'] = "ok";
            $response['code'] = 200;
            $response['message'] = "Photo entry removed from favorites.";
            $response['data'] = $this->get_all(true);
        }

        header("Content-Type: application/json");
        echo json_encode($response);
    }

    private function remove_video($id)
    {
        $response = [
            'status' => "error",
            'code' => 500,
            'message' => "Database insert failed.",
            'data' => ['photos'=>"",'videos'=>""]
        ];
        $id = clean_numeric_text($id);
        $user_id = $_SESSION['user']['id'];
        $delete_qry = "DELETE FROM `favorites` WHERE `media_type`='video' AND `media_id`={$id}";

        if(strlen($id) == 0)
        {
            $response['message'] = "Invalid input id.";
        }
        elseif($this->db->query($delete_qry))
        {
            $response['status'] = "ok";
            $response['code'] = 200;
            $response['message'] = "Video entry removed from favorites.";
            $response['data'] = $this->get_all(true);
        }

        header("Content-Type: application/json");
        echo json_encode($response);
    }
}
