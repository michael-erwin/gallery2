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
        if(!in_array('all',$this->permissions) && !in_array('photo_edit',$this->permissions))
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
        $share_with  = $this->input->post('user_ids');
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
                    $set_sql   = "UPDATE `photos` SET `share_level`='{$share_level}' WHERE `id` IN({$clean_ids})";
                }
                elseif($share_level == 'protected')
                {
                    if($share_with)
                    {
                        $share_with = explode(',', $share_with);
                        $clean_share_with = [];

                        foreach ($share_with as $user_id) {
                            $clean_user_id = clean_numeric_text($user_id);
                            if(strlen($clean_user_id) > 0) $clean_share_with[] = '['.$clean_user_id.']';
                        }

                        if(count($clean_share_with) > 0)
                        {
                            $clean_share_with = implode(',', $clean_share_with);
                            $set_sql  = "UPDATE `photos` SET `share_level`='{$clean_share_with}' WHERE `id` IN({$clean_ids})";
                        } else { $errors++; }
                    }  else { $errors++; }
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
}
