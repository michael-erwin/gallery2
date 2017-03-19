<?php
/**
* CI model for users under Media Gallery project.
* @package  Media Gallery
* @author   Michael Erwin Virgines <michael.erwinp@gmail.com>
*
*/
class M_users extends CI_Model
{

    function __construct()
    {
        parent::__construct();
    }

    /**
     * Search all user's fields by the supplied keywords.
     * 
     * @param  String  $keywords Keywords to match criteria.
     * @param  Integer $limit    Number of items to fetch per page.
     * @param  Integer $page     Page number of result set.
     * @param  String  $order    Column name or field to order.
     * @param  String  $sort     Asc or desc.
     * @return Array             Readily usable form for ajax response. Has basic fields
     *                           'status','code','message' and other relevant data.
     */
    public function search($keywords,$limit,$page,$order='date_modified',$sort='ASC')
    {
        $sort = strtoupper($sort);
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
                    
        $search_sql = $get_sql." ORDER BY `users`.`{$order}` {$sort},`users`.`id` {$sort} LIMIT {$limit} OFFSET {$offset}";
                    
        $raw_data = null;

        $response = [
            "status" => "error",
            "code" => 500,
            "message" => "Unknown error has occured.",
            "data" => null,
            "page" => [
                "current" => 0,
                "limit" => 0,
                "total" => 0
            ],
            "filter" => [
                "order" => $order,
                "sort" => $sort
            ]
        ];
        
        if($search_qry = $this->db->query($search_sql))
        {
            $results = $search_qry->result_array();
            if($total_qry = $this->db->query($get_sql))
            {
                $item_total = $total_qry->num_rows();
                $page_total = ceil($item_total/$limit);

                // Format date.
                $filtered_result = [];
                foreach($results as $item)
                {
                    $date_format = $this->config->item('log_date_format');
                    $item['date_added'] = date($date_format,$item['date_added']);
                    $item['date_modified'] = ($item['date_modified'] == 0)? '' : date($date_format,$item['date_modified']);
                    $filtered_result[] = $item;
                }

                $response['status'] = "ok";
                $response['code'] = 200;
                $response['message'] = "Success.";
                $response['data'] = $filtered_result;
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

        return $response;
    }

    /**
    * Insert new entry in users table.
    * 
    * @param  String   $fname         First name of user.
    * @param  String   $lname         Last name of user.
    * @param  String   $email         Email of user.
    * @param  String   $password      Password hash.
    * @param  Integer  $role          Role id that exist in roles table.
    * @param  String   $status        Either 'active' or 'inactive'.
    * @param  Integer  $limit         Number of entries to return after successful insert.
    * @param  Integer  $page          Page of the result set after insert.
    * @param  String   $verify_email  If present, a hash will be generated in token field 
    *                                 that will serve as reference for valid transaction.
    * @return Array                   Readily usable form for ajax response. Has basic fields
    *                                 'status','code','message' and other relevant data.
    *
    */
    public function add($fname="",$lname="",$email="",$password="",$role=0,$status="inactive",$limit=15,$page=1,$verify_email)
    {
        $errors = 0;
        $response = [
            "status" => "error",
            "code" => 500,
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
        elseif(!preg_match('/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/', $email))
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

        if($errors === 0)
        {
            $date  = time();
            $token = $verify_email? hash('sha256',$date) : "";

            $add_sql = "INSERT INTO `users` SET "
                        ."`first_name`='{$fname}',`last_name`='{$lname}',`email`='{$email}',`password`='{$password}',"
                        ."`status`='{$status}',`role_id`={$role},`token`='{$token}',`token_time`='{$date}',`date_added`={$date}";
            if($this->db->query($add_sql))
            {
                // Email verification
                if($verify_email)
                {
                    $from = 'no-reply@'.parse_url(base_url())['host'];
                    $vars = [
                        'name' => $fname,
                        'link' => base_url("account/confirm-email?email={$email}&token={$token}")
                    ];
                    $message = $this->load->view('admin/v_email_confirm',$vars,true);
                    $this->email->from($from, 'System Bot');
                    $this->email->to($email);
                    $this->email->subject("Confirm Your Registration");
                    $this->email->message($message);
                    $this->email->send();
                }

                // Final response.
                $response['status'] = "ok";
                $response['code'] = 200;
                $response['message'] = "New user added.";
                $response['data'] = $this->fetch($limit,$page,'date_added','DESC',true);
            }
        }
        return $response;
    }

    /**
    * Updates entry in users table.
    * 
    * @param  String   $fname         First name of user.
    * @param  String   $lname         Last name of user.
    * @param  String   $email         Email of user.
    * @param  String   $password      Raw password string.
    * @param  Integer  $role          Role id that exist in roles table.
    * @param  String   $status        Either 'active' or 'inactive'.
    * @param  Integer  $limit         Number of entries to return after successful insert.
    * @param  Integer  $page          Page of the result set after insert.
    * @param  String   $verify_email  If present, a hash will be generated in token field 
    *                                 that will serve as reference for valid transaction.
    * @return Array                   Readily usable form for ajax response. Has basic fields
    *                                 'status','code','message' and other relevant data.
    *
    */
    public function update($id="",$fname="",$lname="",$email="",$password="",$role=0,$status="inactive",$limit=15,$page=1,$verify_email)
    {
        $errors = 0;
        $response = [
            "status" => "error",
            "code" => 500,
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
        $date  = time();
        $token = $verify_email? hash('sha256',$date) : "";

        if($errors === 0)
        {
            $add_sql = "UPDATE `users` SET "
                        ."`first_name`='{$fname}',`last_name`='{$lname}',`email`='{$email}',`password`='{$password}',"
                        ."`status`='{$status}',`role_id`={$role},`token`='{$token}',`date_modified`={$date} WHERE `id`={$id}";
            if($this->db->query($add_sql))
            {
                // Email verification
                if($verify_email)
                {
                    $from = 'no-reply@'.parse_url(base_url())['host'];
                    $vars = [
                        'name' => $fname,
                        'link' => base_url("account/confirm-email?email={$email}&token={$token}")
                    ];
                    $message = $this->load->view('admin/v_email_confirm',$vars,true);
                    $this->email->from($from, 'System Bot');
                    $this->email->to($email);
                    $this->email->subject("Confirm Your Registration");
                    $this->email->message($message);
                    $this->email->send();
                }

                // Final response.
                $response['status'] = "ok";
                $response['code'] = 200;
                $response['message'] = "User data updated.";
                $response['data'] = $this->fetch($limit,$page,'date_added','DESC',true);
            }
        }
        
        return $response;
    }

    /**
     * Update database for password reset request and send reset link to email.
     * 
     * @param  String $email Associated email address of the user.
     * @return Array         Readily usable form for ajax response. Has basic fields 'status',
     *                       'code','message' and other relevant data.
     */
    public function reset_pw($email)
    {
        $response = [
            "status" => "error",
            "code" => 500,
            "message" => "Email does not exist.",
            "data" => null
        ];
        $email_regex = '/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/';

        if(!preg_match($email_regex, $email))
        {
            $response['code'] = 500;
            $response['message'] = "Email is invalid.";
        }
        else
        {
            $sql_check = "SELECT `id`,`first_name`,`status` FROM `users` WHERE `email`='{$email}'";
            $qry_check = $this->db->query($sql_check);
            if($qry_check->num_rows() == 1)
            {
                $userdata = $qry_check->result_array()[0];
                if($userdata['status'] == "active")
                {
                    $date  = time();
                    $token = hash('sha256',$date);
                    
                    $sql_update = "UPDATE `users` SET `token`='{$token}',`token_time`='{$date}' WHERE `email`='{$email}'";

                    if($this->db->query($sql_update))
                    {
                        
                        $from = 'no-reply@'.parse_url(base_url())['host'];
                        $vars = [
                            'name' => $userdata['first_name'],
                            'link' => base_url("account/reset-pw?email={$email}&token={$token}")
                        ];
                        $message = $this->load->view('admin/v_email_reset_password',$vars,true);
                        $this->email->from($from, 'System Bot');
                        $this->email->to($email);
                        $this->email->subject("Reset Your Password");
                        $this->email->message($message);
                        $this->email->send();

                        // Response
                        $response['status'] = "ok";
                        $response['code'] = 200;
                        $response['message'] = "Please check your email for the reset link.";
                        $response['data'] = base_url();
                        
                    }
                    else
                    {
                        $response['message'] = "Database update failed.";
                    }
                }
                else
                {
                    $response['message'] = "Account is inactive. Please contact administrator.";
                }
            }
            else
            {
                $response['message'] = "Account does not exist.";
            }
        }

        return $response;
    }

    /**
     * Update password for password reset request.
     * 
     * @param  String $email     User's email.
     * @param  String $token     Associated token to validate request.
     * @param  String $password  Password hash.
     * @return Array             Readily usable form for ajax response. Has basic fields
     *                           'status','code','message' and other relevant data.
     */
    public function update_pw($email,$token,$password)
    {
        $response = [
            "status" => "error",
            "code" => 500,
            "message" => "Email does not exist.",
            "data" => null
        ];
        $email_regex = '/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/';

        if(!preg_match($email_regex, $email))
        {
            $response['code'] = 500;
            $response['message'] = "Email is invalid. Please contact administrator.";
        }
        else
        {
            $token = clean_alphanum_hash($token);
            $sql_check = "SELECT `id`,`token_time`,`status` FROM `users` WHERE `email`='{$email}' AND `token`='{$token}'";
            $qry_check = $this->db->query($sql_check);

            if($qry_check->num_rows() == 1)
            {
                $userdata = $qry_check->result_array()[0];

                if($userdata['status'] == "active")
                {
                    $hours_24 = 86400;
                    $token_expire = (int) $userdata['token_time']+$hours_24;

                    if(time() > $token_expire)
                    {
                        $response['message'] = "Token has expired. Please try again.";
                    }
                    else
                    {
                        $sql_update = "UPDATE `users` SET `password`='{$password}',`token`='',`token_time`=0 WHERE `id`=".$userdata['id'];

                        if($this->db->query($sql_update))
                        {
                            $response['status'] = "ok";
                            $response['code'] = 200;
                            $response['message'] = "New password saved. You can now sign-in.";
                        }
                        else
                        {
                            $response['message'] = "Database update failed. Please contact administrator.";
                        }
                    }
                }
                else
                {
                    $response['message'] = "Account is inactive. Please contact administrator.";
                }
            }
            else
            {
                $response['message'] = "Request credential is invalid. Please contact administrator.";
            }
        }

        return $response;
    }

    /**
     * Delete user entry.
     * 
     * @param  Integer $id    User's id.
     * @param  Integer $limit Number of items to return after delete.
     * @param  Integer $page  Number of page to get after delete.
     * @return Array          Readily usable form for ajax response. Has basic fields
     *                        'status','code','message' and other relevant data.
     */
    public function delete($id=null,$limit,$page)
    {
        $response = [
            "status" => "error",
            "code" => 500,
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
            $delete_sql = "DELETE FROM `users` WHERE `id`='{$id}'";
           
            if($this->db->query($delete_sql))
            {
                $response['status'] = "ok";
                $response['code'] = 200;
                $response['message'] = "1 user deleted.";
                $response['data'] = $this->fetch($limit,$page,true);
            }
            else
            {
                $response['message'] = "Database query failed.";
            }
                
        }
        
        return $response;
    }

    /**
    * Fetch all all entries in roles table.
    * 
    *@return Array   Readily usable form for ajax response. Has basic fields
    *                'status','code','message' and other relevant data.
    */
    public function fetch($limit,$page,$order="date_added",$sort="DESC",$raw=false)
    {
        $sort = strtoupper($sort);
        $offset = $limit * ($page-1);
        $get_sql = "SELECT `users`.`id`,`users`.`first_name`,`users`.`last_name`,`users`.`email`,`users`.`status`,`users`.`role_id`"
                    .",`roles`.`name` AS `role_name`,`users`.`token`,`users`.`date_added`,`users`.`date_modified`"
                    ." FROM `users`  INNER JOIN `roles` ON `roles`.`id`=`users`.`role_id` ORDER BY `users`.`{$order}` {$sort},`users`.`id` {$sort} LIMIT {$limit} OFFSET {$offset}";
        $get_qry = $this->db->query($get_sql);
        $raw_data = null;

        $response = [
            "status" => "error",
            "code" => 500,
            "message" => "Unknown error has occured.",
            "data" => null,
            "page" => [
                "current" => 0,
                "limit" => 0,
                "total" => 0
            ],
            "filter" => [
                "sort" => $sort,
                "order" => $order
            ]
        ];
        
        if($results = $get_qry->result_array())
        {
            if($page_qry = $this->db->query("SELECT COUNT(id) AS total FROM `users`"))
            {
                // Format date.
                $filtered_result = [];
                foreach($results as $item)
                {
                    $date_format = $this->config->item('log_date_format');
                    $item['date_added'] = date($date_format,$item['date_added']);
                    $item['date_modified'] = ($item['date_modified'] == 0)? '' : date($date_format,$item['date_modified']);
                    $filtered_result[] = $item;
                }
                $page_total = ceil(($page_qry->result_array()[0]['total'])/$limit);
                $response['status'] = "ok";
                $response['code'] = 200;
                $response['message'] = "Success.";
                $response['data'] = $filtered_result;
                $response['page'] = ["current" => $page, "limit" => $limit, "total" => $page_total];
                $raw_data = $filtered_result;
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
            return $response;
        }
    }
}