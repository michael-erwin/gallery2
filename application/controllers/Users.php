<?php
/**
*
*/
class Users extends CI_Controller
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
        $response = [
            "status" => "error",
            "code"=> 403,
            "message" => "You don't have enough permission. Please contact system administrator.",
            "data" => null,
            "page" => null
        ];
        $param_1 = $this->uri->segment(2);
        $param_2 = $this->uri->segment(3);
        $param_3 = $this->uri->segment(4);
        $param_4 = $this->uri->segment(5);

        if($param_1 == "manage")
        {
            if($param_2 == "add")
            {   
                if(!in_array('all',$this->permissions) && !in_array('user_add',$this->permissions))
                {
                    header("Content-Type: application/json");
                    echo json_encode($response);
                    exit();
                }
                $fname = clean_title_text($this->input->post('fname'));
                $lname = clean_title_text($this->input->post('lname'));
                $email = preg_replace('/[^a-zA-Z0-9\.\-_@]/', "", $this->input->post('email'));
                $role = clean_numeric_text($this->input->post('role'));
                $status = clean_alpha_text($this->input->post('status'));
                $verify = clean_numeric_text($this->input->post('verify_email'));
                $page = is_numeric(clean_numeric_text($this->input->post('page')))? clean_numeric_text($this->input->post('page')) : 1;
                $limit = is_numeric(clean_numeric_text($this->input->post('limit')))? clean_numeric_text($this->input->post('limit')) : 15;
                
                if($verify && $verify == 1)
                {
                    $password = password_hash(time(),PASSWORD_BCRYPT);
                    $verify_email = true;
                }
                else
                {
                    $password = password_hash($this->input->post('password'),PASSWORD_BCRYPT);
                    $verify_email = false;
                }
                $this->add($fname,$lname,$email,$password,$role,$status,$limit,$page,$verify_email);
            }
            elseif($param_2 == "update")
            {
                if(!in_array('all',$this->permissions) && !in_array('user_edit',$this->permissions))
                {
                    header("Content-Type: application/json");
                    echo json_encode($response);
                    exit();
                }
                $id = clean_numeric_text($this->input->post('id'));
                $fname = clean_title_text($this->input->post('fname'));
                $lname = clean_title_text($this->input->post('lname'));
                $email = preg_replace('/[^a-zA-Z0-9\.\-_@]/', "", $this->input->post('email'));
                $role = clean_numeric_text($this->input->post('role'));
                $status = clean_alpha_text($this->input->post('status'));
                $verify = clean_numeric_text($this->input->post('verify_email'));
                $page = is_numeric(clean_numeric_text($this->input->post('page')))? clean_numeric_text($this->input->post('page')) : 1;
                $limit = is_numeric(clean_numeric_text($this->input->post('limit')))? clean_numeric_text($this->input->post('limit')) : 15;
                
                if($verify && $verify == 1)
                {
                    $password = password_hash(time(),PASSWORD_BCRYPT);
                    $verify_email = true;
                }
                else
                {
                    $password = password_hash($this->input->post('password'),PASSWORD_BCRYPT);
                    $verify_email = false;
                }
                $this->update($id,$fname,$lname,$email,$password,$role,$status,$limit,$page,$verify_email);
            }
            elseif($param_2 == "delete")
            {
                if(!in_array('all',$this->permissions) && !in_array('user_delete',$this->permissions))
                {
                    header("Content-Type: application/json");
                    echo json_encode($response);
                    exit();
                }
                $id = clean_numeric_text($this->input->post('id'));
                $page = is_numeric(clean_numeric_text($this->input->post('page')))? clean_numeric_text($this->input->post('page')) : 1;
                $limit = is_numeric(clean_numeric_text($this->input->post('limit')))? clean_numeric_text($this->input->post('limit')) : 15;
                $this->delete($id,$limit,$page);
            }
            elseif($param_2 == "get_page")
            {
                if(!in_array('all',$this->permissions) && !in_array('user_view',$this->permissions))
                {
                    header("Content-Type: application/json");
                    echo json_encode($response);
                    exit();
                }
                $page = ($this->input->get('page') !== null)? clean_numeric_text($this->input->get('page')) : 1;
                $limit = ($this->input->get('limit') !== null)? clean_numeric_text($this->input->get('limit')) : 15;
                $this->fetch($limit,$page);
            }
            elseif($param_2 == "search")
            {
                if(!in_array('all',$this->permissions) && !in_array('user_view',$this->permissions))
                {
                    header("Content-Type: application/json");
                    echo json_encode($response);
                    exit();
                }
                $keywords = $this->input->get('kw');
                $page = ($this->input->get('page') !== null)? clean_numeric_text($this->input->get('page')) : 1;
                $limit = ($this->input->get('limit') !== null)? clean_numeric_text($this->input->get('limit')) : 15;
                $this->search($keywords,$limit,$page);
            }
        }
        elseif($param_1 == "test")
        {
            $this->test();
        }
    }

    /**
    * Fetch all all entries in roles table.
    * @return String  JSON formatted string.
    *
    */
    private function search($keywords,$limit,$page)
    {
        $keywords = $this->db->escape_str($keywords);
        $offset = $limit * ($page-1);
        $get_sql  = "SELECT `users`.`id`,`users`.`first_name`,`users`.`last_name`,`users`.`email`,`users`.`status`";
        $get_sql .= ",`users`.`role_id`,`users`.`date_added`,`users`.`date_modified`";
        $get_sql .= ",`roles`.`name` AS `role_name`  FROM `users` INNER JOIN `roles` ON `roles`.`id`=`users`.`role_id`";

        if(!empty(trim($keywords)))
        {
             $get_sql .= " WHERE `users`.`email` LIKE '%{$keywords}%' OR `users`.`first_name` LIKE '%{$keywords}%'";
             $get_sql .= " OR `users`.`last_name` LIKE '%{$keywords}%' OR `users`.`status` LIKE '%{$keywords}%'";
        }
                    
        $search_sql = $get_sql." LIMIT {$limit} OFFSET {$offset}";
                    
        $raw_data = null;

        $response = [
            "status" => "error",
            "message" => "Unknown error has occured.",
            "data" => null,
            "page" => [
                "current" => 0,
                "limit" => 0,
                "total" => 0
            ]
        ];
        
        if($search_qry = $this->db->query($search_sql))
        {
            $results = $search_qry->result_array();
            if($total_qry = $this->db->query($get_sql))
            {
                $item_total = $total_qry->num_rows();
                $page_total = ceil($item_total/$limit);
                $response['status'] = "ok";
                $response['message'] = "Success.";
                $response['data'] = $results;
                $response['page'] = ["current" => $page, "limit" => $limit, "total" => $page_total];
                $response['dbg_info'] = $item_total;
            }
            else
            {
                $response['message'] = "Cannot get number of rows.";
            }
        }
        else
        {
            $response['message'] = "Failed to get database results.";
        }

        header("Content-Type: application/json");
        echo json_encode($response);
    }

    /**
    * Insert new entry in users table.
    * @param  String   $fname 
    * @param  String   $lname 
    * @param  String   $email 
    * @param  String   $password 
    * @param  Integer  $role 
    * @param  String   $status 
    * @param  Integer  $limit 
    * @param  Integer  $page 
    * @param  String   $verify_email 
    *
    */
    private function add($fname="",$lname="",$email="",$password="",$role=0,$status="inactive",$limit=15,$page=1,$verify_email)
    {
        $errors = 0;
        $response = [
            "status" => "error",
            "message" => "Unknown error has occured.",
            "data" => null
        ];
        
        // Validate first name.
        if(strlen($fname) < 2)
        {
            $response['message'] = "First name is invalid.";
            $errors++;
        }

        // Validate last name.
        elseif(strlen($fname) < 2)
        {
            $response['message'] = "Last name is invalid.";
            $errors++;
        }

        // Validate email.
        elseif(!preg_match('/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/', $email))
        {
            $response['message'] = "Email is invalid.";
            $errors++;
        }
        else
        {
            if($email_query = $this->db->query("SELECT `id` FROM `users` WHERE `email`='{$email}'"))
            {
                if($email_query->num_rows() > 0)
                {
                    $response['message'] = "Email already exist. ";
                    $errors++;
                }
            }
            else{
                $response['message'] = "Unable to query database. ";
                $errors++;
            }
                    
        }

        // Email verification.
        $token = $verify_email? hash('sha256',time()) : "";
        $date  = time();

        if($errors === 0)
        {
            $add_sql = "INSERT INTO `users` SET "
                        ."`first_name`='{$fname}',`last_name`='{$lname}',`email`='{$email}',`password`='{$password}',"
                        ."`status`='{$status}',`role_id`={$role},`token`='{$token}',`date_added`={$date}";
            if($this->db->query($add_sql))
            {
                $response['status'] = "ok";
                $response['message'] = "New user added.";
                $response['data'] = $this->fetch($limit,$page,true);
            }
        }
        

        header("Content-Type: application/json");
        echo json_encode($response);
    }

    /**
    * Updates entry in users table.
    * @param  String   $fname 
    * @param  String   $lname 
    * @param  String   $email 
    * @param  String   $password 
    * @param  Integer  $role 
    * @param  String   $status 
    * @param  Integer  $limit 
    * @param  Integer  $page 
    * @param  String   $verify_email 
    *
    */
    private function update($id="",$fname="",$lname="",$email="",$password="",$role=0,$status="inactive",$limit=15,$page=1,$verify_email)
    {
        $errors = 0;
        $response = [
            "status" => "error",
            "message" => "Unknown error has occured.",
            "data" => null
        ];

        // Validate id.
        if(!is_numeric($id))
        {
            $response['message'] = "ID is invalid.";
            $errors++;
        }
        
        // Validate first name.
        elseif(strlen($fname) < 2)
        {
            $response['message'] = "First name is invalid.";
            $errors++;
        }

        // Validate last name.
        elseif(strlen($fname) < 2)
        {
            $response['message'] = "Last name is invalid.";
            $errors++;
        }

        // Validate email.
        elseif(!preg_match('/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/', $email))
        {
            $response['message'] = "Email is invalid.";
            $errors++;
        }
        else
        {
            if($email_query = $this->db->query("SELECT `id` FROM `users` WHERE `email`='{$email}'"))
            {
                if($email_query->num_rows() > 0)
                {
                    $check_data = $email_query->result_array()[0];
                    if($check_data['id'] != $id)
                    {
                        $response['message'] = "Email already in use by other user. ";
                        $errors++;
                    }
                        
                }
            }
            else{
                $response['message'] = "Unable to query database. ";
                $errors++;
            }
                    
        }

        // Email verification.
        $token = $verify_email? hash('sha256',time()) : "";
        $date  = time();

        if($errors === 0)
        {
            $add_sql = "UPDATE `users` SET "
                        ."`first_name`='{$fname}',`last_name`='{$lname}',`email`='{$email}',`password`='{$password}',"
                        ."`status`='{$status}',`role_id`={$role},`token`='{$token}',`date_modified`={$date} WHERE `id`={$id}";
            if($this->db->query($add_sql))
            {
                $response['status'] = "ok";
                $response['message'] = "User data updated.";
                $response['data'] = $this->fetch($limit,$page,true);
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
    private function delete($id=null,$limit,$page)
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
        if(!is_numeric($id))
        {
            $response['message'] = "ID value is invalid.";
        }
        else
        {
            // TODO: Include users that will be affected in query statement.
            $delete_sql = "DELETE FROM `users` WHERE `id`='{$id}'";
           
            if($this->db->query($delete_sql))
            {
                $response['status'] = "ok";
                $response['message'] = "1 user deleted.";
                $response['data'] = $this->fetch($limit,$page,true);
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
    private function fetch($limit,$page,$raw=false)
    {
        $offset = $limit * ($page-1);
        $get_sql = "SELECT `users`.`id`,`users`.`first_name`,`users`.`last_name`,`users`.`email`,`users`.`status`,`users`.`role_id`"
                    .",`roles`.`name` AS `role_name`,`users`.`token`,`users`.`date_added`,`users`.`date_modified`"
                    ." FROM `users`  INNER JOIN `roles` ON `roles`.`id`=`users`.`role_id` LIMIT {$limit} OFFSET {$offset}";
        $get_qry = $this->db->query($get_sql);
        $raw_data = null;

        $response = [
            "status" => "error",
            "message" => "Unknown error has occured.",
            "data" => null,
            "page" => [
                "current" => 0,
                "limit" => 0,
                "total" => 0
            ]
        ];
        
        if($results = $get_qry->result_array())
        {
            if($page_qry = $this->db->query("SELECT COUNT(id) AS total FROM `users`"))
            {
                $page_total = ceil(($page_qry->result_array()[0]['total'])/$limit);
                $response['status'] = "ok";
                $response['message'] = "Success.";
                $response['data'] = $results;
                $response['page'] = ["current" => $page, "limit" => $limit, "total" => $page_total];
                $raw_data = $results;
            }
            else
            {
                $response['message'] = "Failed to get total entry count.";
            }
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
        
    }
}
