<?php
/**
*
*/
class Roles extends CI_Controller
{
    private $permissions = [];

    function __construct()
    {
        parent::__construct();
        $this->permissions = $this->auth->get_permissions();
    }

    /**
    * Function description.
    * @param  type  $variable  Variable description.
    * @return type             Return value description.
    *
    */
    public function _remap()
    {
        $param_1 = $this->uri->segment(2);
        $param_2 = $this->uri->segment(3);
        $param_3 = $this->uri->segment(4);
        $param_4 = $this->uri->segment(5);

        if($param_1 == "manage")
        {
            $response = [
                "status" => "error",
                "code"=> 403,
                "message" => "You don't have enough permission. Please contact system administrator.",
                "data" => null,
                "page" => null
            ];

            if($param_2 == "add")
            {
                if(!in_array('all',$this->permissions) && !in_array('role_add',$this->permissions))
                {
                    header("Content-Type: application/json");
                    echo json_encode($response);
                    exit();
                }
                $name = clean_title_text($this->input->post('name'));
                $description = $this->input->post('description');
                $permissions = $this->input->post('permission');
                $permissions_clean = [];
                if(is_array($permissions))
                {
                    foreach($permissions as $permission) {
                        $cleaned_text = preg_replace('/[^a-z_]/',"",$permission);
                        if(strlen($cleaned_text) > 0) {
                            $permissions_clean[] = $cleaned_text;
                        }
                    };
                    $permissions = implode(',', $permissions_clean);
                }
                else
                {
                    $permissions = "";
                }
                $this->add($name,$description,$permissions);
            }
            elseif($param_2 == "update")
            {
                if(!in_array('all',$this->permissions) && !in_array('role_edit',$this->permissions))
                {
                    header("Content-Type: application/json");
                    echo json_encode($response);
                    exit();
                }
                $id = clean_numeric_text($this->input->post('id'));
                $name = clean_title_text($this->input->post('name'));
                $description = $this->input->post('description');
                $permissions = $this->input->post('permission');
                $permissions_clean = [];
                if(is_array($permissions))
                {
                    foreach($permissions as $permission) {
                        $cleaned_text = preg_replace('/[^a-z_]/',"",$permission);
                        if(strlen($cleaned_text) > 0) {
                            $permissions_clean[] = $cleaned_text;
                        }
                    };
                    $permissions = implode(',', $permissions_clean);
                }
                else
                {
                    $permissions = "";
                }
                $this->update($id,$name,$description,$permissions);
            }
            elseif($param_2 == "delete")
            {
                if(!in_array('all',$this->permissions) && !in_array('role_delete',$this->permissions))
                {
                    header("Content-Type: application/json");
                    echo json_encode($response);
                    exit();
                }
                $id = clean_numeric_text($this->input->post('id'));
                $this->delete($id);
            }
            elseif($param_2 == "get_all")
            {
                if(!in_array('all',$this->permissions) && !in_array('role_view',$this->permissions))
                {
                    header("Content-Type: application/json");
                    echo json_encode($response);
                    exit();
                }
                $this->get_all();
            }
            elseif($param_2 == "get_permissions")
            {
                if(!in_array('all',$this->permissions) && !in_array('role_view',$this->permissions))
                {
                    header("Content-Type: application/json");
                    echo json_encode($response);
                    exit();
                }
                $this->get_permissions();
            }
        }
        elseif($param_1 == "test")
        {
            print_r($this->permissions);
        }
    }

    /**
    * Insert new entry in roles table.
    * @param  String  $name         New role name.
    * @param  String  $description  New role description.
    * @param  String  $permissions  Comma separated value.
    * @return String                JSON formatted string.
    */
    private function add($name="",$description="",$permissions="")
    {
        $response = [
            "status" => "error",
            "message" => "Unknown error has occured.",
            "data" => null
        ];
        
        if(strlen($name) == 0)
        {
            $response['message'] = "Name field is missing.";
        }
        else
        {
            $check_sql = "SELECT `id` FROM `roles` WHERE `name`='{$name}'";
           
            if($check_qry = $this->db->query($check_sql))
            {
                $res_count = $check_qry->num_rows();

                // Check if name existed.
                if($res_count > 0)
                {
                    $response['message'] = "Role name already exist.";
                }
                else
                {
                    $set_sql = "INSERT INTO `roles` SET `name`='{$name}',`description`='{$description}',`permissions`='{$permissions}'";
                    if($this->db->query($set_sql))
                    {
                        $response['status'] = "ok";
                        $response['message'] = "New role added.";
                        $response['data'] = $this->get_all(true);
                    }
                    else
                    {
                        $response['message'] = "Database insert failed.";
                    }
                }
            }
            else
            {
                $response['message'] = "Database query failed.";
            }
                
        }
        
        header("Content-Type: application/json");
        echo json_encode($response);
    }

    /**
    * Update existing entry in roles table.
    * @param  Integer  $id           Id of existing record.
    * @param  String   $name         New role name.
    * @param  String   $description  New role description.
    * @param  String   $permissions  Comma separated value.
    * @return String                 JSON formatted string.
    */
    private function update($id="",$name="",$description="",$permissions="")
    {
        $response = [
            "status" => "error",
            "message" => "Unknown error has occured.",
            "data" => null
        ];
        
        if(!is_numeric($id))
        {
            $response['message'] = "ID field is missing.";
        }
        elseif(strlen($name) == 0)
        {
            $response['message'] .= " Name field is missing.";
        }
        else
        {
            // Check if name existed.
            $check_sql = "SELECT `id` FROM `roles` WHERE `name`='{$name}'";

            if($check_qry = $this->db->query($check_sql))
            {
                $set_sql = "UPDATE `roles` SET `name`='{$name}',`description`='{$description}',`permissions`='{$permissions}' WHERE `id`={$id}";
                $res_count = $check_qry->num_rows();
                if($res_count > 0)
                {
                    $res_data  = $check_qry->result_array()[0];
                    if($res_data['id'] != $id)
                    {
                        $response['message'] = "Role name already exist.";
                    }
                    else
                    {
                        if($this->db->query($set_sql))
                        {
                            $response['status'] = "ok";
                            $response['message'] = "Role updated.";
                        }
                        else
                        {
                            $response['message'] = "Database insert failed.";
                        }
                    }
                }
                else
                {
                    if($this->db->query($set_sql))
                    {
                        $response['status'] = "ok";
                        $response['message'] = "Role updated.";
                    }
                    else
                    {
                        $response['message'] = "Database insert failed.";
                    }
                }
            }
            else
            {
                $response['message'] = "Database query failed.";
            }
        }
        
        header("Content-Type: application/json");
        echo json_encode($response);
    }

    /**
    * Insert new entry in roles table.
    * @param  Integer  $id  ID of item to delete.
    * @return String        JSON formatted string.
    */
    private function delete($id=null)
    {
        $response = [
            "status" => "error",
            "message" => "Unknown error has occured.",
            "data" => null
        ];
        
        if(empty($id))
        {
            $response['message'] = "ID field is missing.";
        }
        else
        {
            // TODO: Include users that will be affected in query statement.
            $delete_sql = "DELETE FROM `roles` WHERE `id`={$id}";
            $users_sql  = "UPDATE `users` SET `role_id`=2 WHERE `role_id`={$id}";
           
            if($this->db->query($delete_sql))
            {
                if($this->db->query($users_sql))
                {
                    $response['status'] = "ok";
                    $response['message'] = "1 role deleted.";
                    $response['data'] = $this->get_all(true);
                }
                else
                {
                    $response['message'] = "Role deleted but unable to move users.";
                    $response['data'] = $this->get_all(true);
                }
                    
            }
            else
            {
                $response['message'] = "Database query failed.";
            }
                
        }
        
        header("Content-Type: application/json");
        echo json_encode($response);
    }

    /**
    * Fetch all all entries in roles table.
    * @return String  JSON formatted string.
    *
    */
    private function get_all($raw=null)
    {
        $get_sql = "SELECT * FROM `roles` ORDER BY `name`";
        $get_qry = $this->db->query($get_sql);
        $raw_data = null;

        $response = [
            "status" => "error",
            "message" => "Unknown error has occured.",
            "data" => null
        ];
        
        if($results  = $get_qry->result_array())
        {
            $response['status'] = "ok";
            $response['message'] = "Success.";
            $response['data'] = $results;
            $raw_data = $results;
        }
        else
        {
            $response['message'] = "Failed to get database results.";
        }
        
        if($raw)
        {
            return $raw_data;
        }
        else
        {
            header("Content-Type: application/json");
            echo json_encode($response);
        }
    }

    /**
    * Fetch all all permission entries in permissions table.
    * @return String  JSON formatted string.
    *
    */
    private function get_permissions()
    {
        $get_sql = "SELECT * FROM `permissions` ORDER BY `name`";
        $get_qry = $this->db->query($get_sql);

        $response = [
            "status" => "error",
            "message" => "Unknown error has occured.",
            "data" => null
        ];
        
        if($results  = $get_qry->result_array())
        {
            $response['status'] = "ok";
            $response['message'] = "Success.";
            $response['data'] = $results;
        }
        else
        {
            $response['message'] = "Failed to get database results.";
        }
        
        header("Content-Type: application/json");
        echo json_encode($response);
    }

    public function test()
    {
        header("Content-Type: text/plain");
        print_r($this->get_all(true));
    }
}
