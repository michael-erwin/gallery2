<?php
class Share extends CI_Controller
{
    private $permissions = [];
    
    function __construct()
    {
        parent::__construct();
        $this->permissions = $this->auth->get_permissions();
    }

    public function _remap()
    {
        $response = [
            "status" => "error",
            "code"=> 403,
            "message" => "You don't have enough permission. Please contact system administrator."
        ];
        if(!in_array('all',$this->permissions) && !in_array('video_edit',$this->permissions))
        {
            header("Content-Type: application/json");
            echo json_encode($response);
            exit();
        }
        $this->share();
    }

    private function share()
    {
        $response = [
            "status" => "error",
            "code" => 500,
            "message" => "Invalid ids."
        ];

        $id = $this->input->post('id');
        $share_level = $this->input->post('share_level');
        $user_ids = $this->input->post('user_ids');
        $errors = 0;

        if($id)
        {
            $id = explode(',', $id);
            $clean_ids = [];

            if(is_array($id))
            {
                foreach ($id as $raw_id) {
                    $clean_id = clean_numeric_text($raw_id);
                    if(strlen($clean_id) > 0) $clean_ids[] = $clean_id;
                }
            }
            else
            {
                $clean_id = clean_numeric_text($id);
                if(strlen($clean_id) > 0) $clean_ids[] = $clean_id;
            }

            if(count($clean_ids) == 0)
            {
                $error++;
            }
            else
            {
                $clean_ids = implode(',', $clean_ids);
                
                if($share_level == 'private' || $share_level == 'public')
                {
                    $set_sql   = "UPDATE `videos` SET `share_level`='{$share_level}' WHERE `id` IN({$clean_ids})";
                }
                elseif($share_level == 'protected')
                {
                    if($user_ids)
                    {
                        $user_ids = explode(',', $user_ids);
                        $clean_ids = [];

                        foreach ($user_ids as $user_id) {
                            $clean_id = clean_numeric_text($user_id);
                            if(strlen($clean_id) > 0)
                            {
                                $clean_ids[] = '['.$clean_id.']';
                            }
                        }

                        if(count($clean_ids) > 0)
                        {
                            $clean_ids = implode(',', $clean_ids);
                            $set_sql   = "UPDATE `videos` SET `share_level`='{$clean_ids}' WHERE `id`={$id}";
                        }
                    }
                } else { $errors++; }
            }

            if($errors == 0)
            {
                if($this->db->query($set_sql))
                {
                    $response['status'] = "ok";
                    $response['code'] = 200;
                    $response['message'] = "Share level updated.";
                    $response['dbg_info'] = [
                        'syntax' => $set_sql,
                        'share_level' => $share_level
                    ];
                }
                else
                {
                    $response['code'] = 403;
                    $response['message'] = "Db error has occured.";
                }
            }
        }

        header("Content-Type: application/json");
        echo json_encode($response);
    }

    private function share_old()
    {
        $response = [
            "status" => "error",
            "code" => 500,
            "message" => "Invalid ids."
        ];

        $id = clean_numeric_text($this->input->post('id'));
        $share_level = $this->input->post('share_level');
        $user_ids = $this->input->post('user_ids');
        $errors = 0;

        if(strlen($id) > 0)
        {
            if($share_level == 'private' || $share_level == 'public')
            {
                $set_sql   = "UPDATE `videos` SET `share_level`='{$share_level}' WHERE `id`={$id}";
            }
            elseif($share_level == 'protected')
            {
                if($user_ids)
                {
                    $user_ids = explode(',', $user_ids);
                    $clean_ids = [];

                    foreach ($user_ids as $user_id) {
                        $clean_id = clean_numeric_text($user_id);
                        if(strlen($clean_id) > 0)
                        {
                            $clean_ids[] = '['.$clean_id.']';
                        }
                    }

                    if(count($clean_ids) > 0)
                    {
                        $clean_ids = implode(',', $clean_ids);
                        $set_sql   = "UPDATE `videos` SET `share_level`='{$clean_ids}' WHERE `id`={$id}";
                    }
                }
            } else { $errors++; }

            if($errors == 0)
            {
                if($this->db->query($set_sql))
                {
                    $response['status'] = "ok";
                    $response['code'] = 200;
                    $response['message'] = "Share level updated.";
                    $response['dbg_info'] = [
                        'syntax' => $set_sql,
                        'share_level' => $share_level
                    ];
                }
                else
                {
                    $response['code'] = 403;
                    $response['message'] = "Db error has occured.";
                }
            }
        }

        header("Content-Type: application/json");
        echo json_encode($response);
    }
}