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
        $this->load->model('m_users');
        $this->load->library('email');
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
                $this->add();
            }
            elseif($param_2 == "update")
            {
                if(!in_array('all',$this->permissions) && !in_array('user_edit',$this->permissions))
                {
                    header("Content-Type: application/json");
                    echo json_encode($response);
                    exit();
                }
                $this->update();
            }
            elseif($param_2 == "delete")
            {
                if(!in_array('all',$this->permissions) && !in_array('user_delete',$this->permissions))
                {
                    header("Content-Type: application/json");
                    echo json_encode($response);
                    exit();
                }
                $this->delete();
            }
            elseif($param_2 == "get_page")
            {
                if(!in_array('all',$this->permissions) && !in_array('user_view',$this->permissions))
                {
                    header("Content-Type: application/json");
                    echo json_encode($response);
                    exit();
                }
                $this->fetch();
            }
            elseif($param_2 == "search")
            {
                if(!in_array('all',$this->permissions) && !in_array('user_view',$this->permissions))
                {
                    header("Content-Type: application/json");
                    echo json_encode($response);
                    exit();
                }
                $this->search();
            }
        }
        elseif($param_1 == "sign-up")
        {
            $this->sign_up();
        }
        elseif($param_1 == "sign-in")
        {
            $this->sign_in();
        }
        elseif($param_1 == "forgot-pw")
        {
            $this->forgot_pw();
        }
        elseif($param_1 == "reset-pw")
        {
            $this->reset_pw();
        }
        elseif($param_1 == "test")
        {
            $this->test();
        }
    }

    /**
    * Fetch all all entries in roles table.
    * 
    * @return String  JSON formatted string.
    *
    */
    private function search()
    {
        $keywords = $this->input->get('kw');
        $page = ($this->input->get('page') !== null)? clean_numeric_text($this->input->get('page')) : 1;
        $limit = ($this->input->get('limit') !== null)? clean_numeric_text($this->input->get('limit')) : 15;

        $response = $this->m_users->search($keywords,$limit,$page);

        header("Content-Type: application/json");
        echo json_encode($response);
    }

    /**
    * Insert new entry in users table.
    *
    * @return String  JSON formatted string.
    */
    private function add()
    {
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
        $response = $this->m_users->add($fname,$lname,$email,$password,$role,$status,$limit,$page,$verify_email);
        

        header("Content-Type: application/json");
        echo json_encode($response);
    }

    /**
    * Updates entry in users table.
    * 
    * @return String  JSON formatted string.
    * 
    */
    private function update()
    {
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

        $response = $this->m_users->update($id,$fname,$lname,$email,$password,$role,$status,$limit,$page,$verify_email);

        header("Content-Type: application/json");
        echo json_encode($response);
    }

    /**
    * Delete user entry.
    * 
    * @return String  JSON formatted string.
    */
    private function delete()
    {
        $id = clean_numeric_text($this->input->post('id'));
        $page = is_numeric(clean_numeric_text($this->input->post('page')))? clean_numeric_text($this->input->post('page')) : 1;
        $limit = is_numeric(clean_numeric_text($this->input->post('limit')))? clean_numeric_text($this->input->post('limit')) : 15;
        
        $response = $this->m_users->delete($id,$limit,$page);
        
        header("Content-Type: application/json");
        echo json_encode($response);
    }

    /**
    * Fetch all all entries in roles table.
    * 
    * @return String  JSON formatted string.
    *
    */
    private function fetch()
    {
        $page = ($this->input->get('page') !== null)? clean_numeric_text($this->input->get('page')) : 1;
        $limit = ($this->input->get('limit') !== null)? clean_numeric_text($this->input->get('limit')) : 15;
        $response = $this->m_users->fetch($limit,$page);

        header("Content-Type: application/json");
        echo json_encode($response);
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

    /**
    * Insert new entry in users table for registering member.
    *
    * @return String  JSON formatted string.
    */
    private function sign_up()
    {
        $fname = clean_title_text($this->input->post('fname'));
        $lname = clean_title_text($this->input->post('lname'));
        $email = preg_replace('/[^a-zA-Z0-9\.\-_@]/', "", $this->input->post('email'));
        $password = password_hash($this->input->post('password'),PASSWORD_BCRYPT);
        $role = 2; // Default role.
        $status = 'inactive'; // Default status.
        $verify = true;
        $page = 1;
        $limit = 1;
        
        $response = $this->m_users->add($fname,$lname,$email,$password,$role,$status,$limit,$page,$verify);

        if($response['status'] == "ok")
        {
            $response['message'] = "Confirmation link was sent to your email. Please check your email for instructions.";
        }

        header("Content-Type: application/json");
        echo json_encode($response);
    }

    private function sign_in()
    {
        $response = [
            "status" => "error",
            "code" => 403,
            "message" => "Authentication failed.",
            "data" => null
        ];
        $errors = 0;
        $email_regex = '/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/';
        $email = $this->input->post('email');
        $password = $this->input->post('password');
        $remember = !empty($this->input->post('remember'))? true : false;
        $redirect = $this->input->post('redir');

        if(!$email)
        {
            $errors++;
            $response['code'] = 500;
            $response['message'] = "Email is required.";
        }
        elseif(!preg_match($email_regex, $email))
        {
            $errors++;
            $response['code'] = 500;
            $response['message'] = "Email is invalid.";
        }
        elseif(!$this->auth->sign_in($email,$password,$remember))
        {
            $errors++;
        }
        else
        {
            $response['status'] = "ok";
            $response['code'] = 200;
            $response['message'] = "Authentication successful.";
            if(strlen($redirect) > 1)
            {
                $response['data'] = $redirect;
            }
            else
            {
                $response['data'] = base_url();
            }
        }

        header("Content-Type: application/json");
        echo json_encode($response);
    }

    private function forgot_pw()
    {
        $response = [
            "status" => "error",
            "code" => 500,
            "message" => "Internal server error.",
            "data" => null
        ];
        $errors = 0;
        
        $email = $this->input->post('email');

        if(!$email)
        {
            $errors++;
            $response['code'] = 500;
            $response['message'] = "Email is required.";
        }
        else
        {
            $response = $this->m_users->reset_pw($email);
        }

        header("Content-Type: application/json");
        echo json_encode($response);
    }

    private function reset_pw()
    {
        $response = [
            "status" => "error",
            "code" => 500,
            "message" => "Internal server error.",
            "data" => null
        ];
        $errors = 0;
        
        $email = $this->input->post('email');
        $token = $this->input->post('token');
        $passw = $this->input->post('npassword');

        if(!$email && !$token && !$passw)
        {
            $errors++;
            $response['message'] = "Data sent has missing fields. Please contact administrator.";
        }

        // If no errors, update via user model.
        if($errors == 0)
        {
            $password = password_hash($passw,PASSWORD_BCRYPT);
            $response = $this->m_users->update_pw($email,$token,$password);
        }

        header("Content-Type: application/json");
        echo json_encode($response);
    }

    public function test()
    {
        
    }
}
