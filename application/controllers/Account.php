<?php
/**
* Tester
*/
class Account extends CI_Controller
{
    private $permissions = [];
    
    function __construct()
    {
        parent::__construct();
        $this->permissions = $this->auth->get_permissions();
    }

    public function _remap()
    {
        $task = $this->uri->segment(2);
        $redirect = $this->input->get('redirect');

        if($task == "signup")
        {
            $this->signUp($redirect);
        }
        elseif($task == "signout")
        {
            $_SESSION = array();
            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000,
                    $params["path"], $params["domain"],
                    $params["secure"], $params["httponly"]
                );
            }
            session_destroy();
            header("Location: ".base_url());
        }
        elseif($task == "forgot")
        {
            $this->forgot($redirect);
        }
        elseif($task == "confirm-email")
        {
            $this->confirm_email();
        }
        elseif($task == "reset-pw")
        {
            $this->reset_pw();
        }
        elseif($task == "permissions")
        {
            header("Content-Type: application/json");
            echo json_encode($this->permissions);
        }
        elseif($task == "md5")
        {
            $granted = array_intersect($this->permissions, ['all','photo_edit','video_edit','category_edit']);
            if(count($granted) > 0)
            {
                $response = [
                    "status" => "ok",
                    "code" => 200,
                    "message" => "Success.",
                    "data" => md5(uniqid($_SESSION['user']['id'],true))
                ];
            }
            else
            {
                $response = [
                    "status" => "error",
                    "code" => 500,
                    "message" => "Your session has expired.",
                    "data" => null
                ];
            }
            header("Content-Type: application/json");
            echo json_encode($response);
        }
        else
        {
            $this->signIn($redirect);
        }
    }

    private function signIn($redirect)
    {
        $data['title']     = "Gallery - Sign In";
        $data['main_form'] = $this->load->view('common/v_sign_in',null,true);
        $data['js_script'] = $this->load->view('scripts/v_scripts_portal_page',null,true);
        $this->load->view('v_portal_layout',$data);
    }

    private function signUp($redirect)
    {
        $data['title']     = "Gallery - Sign Up";
        $data['main_form'] = $this->load->view('common/v_sign_up',null,true);
        $data['js_script'] = $this->load->view('scripts/v_scripts_portal_page',null,true);
        $this->load->view('v_portal_layout',$data);
    }

    private function confirm_email()
    {
        $email = $this->input->get('email');
        $token = $this->input->get('token');

        if($email && $token)
        {
            if(!preg_match('/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/', $email))
            {
                show_404();
            }
            else
            {
                $token = clean_alphanum_hash($token);
                $sql_check = "SELECT `id`,`token_time` FROM `users` WHERE `email`='{$email}' AND `token`='{$token}'";
                $qry_check = $this->db->query($sql_check);
                if($qry_check->num_rows() !== 1)
                {
                    show_404();
                }
                else
                {
                    $hours_24 = 86400;
                    $userdata = $qry_check->result_array()[0];
                    $token_expire = (int) $userdata['token_time']+$hours_24;
                    if(time() > $token_expire)
                    {
                        exit("Token has expired.");
                    }
                    else
                    {
                        $sql_update = "UPDATE `users` SET `token`='',`token_time`=0,`status`='active' WHERE `email`='{$email}' AND `token`='{$token}'";
                        if($this->db->query($sql_update))
                        {
                            header("Location: ".base_url('account/signin'));
                        }
                    }
                }
            }
        }
        else
        {
            show_404();
        }
    }

    private function reset_pw()
    {
        $email = $this->input->get('email');
        $token = $this->input->get('token');

        if($email && $token)
        {
            if(!preg_match('/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/', $email))
            {
                show_404();
            }
            else
            {
                $token = clean_alphanum_hash($token);
                $sql_check = "SELECT `id`,`token_time` FROM `users` WHERE `email`='{$email}' AND `token`='{$token}'";
                $qry_check = $this->db->query($sql_check);
                if($qry_check->num_rows() !== 1)
                {
                    show_404();
                }
                else
                {
                    $hours_24 = 86400;
                    $userdata = $qry_check->result_array()[0];
                    $token_expire = (int) $userdata['token_time']+$hours_24;
                    if(time() > $token_expire)
                    {
                        exit("Token has expired.");
                    }
                    else
                    {
                        $data['title']     = "Gallery - Reset Your Password";
                        $data['main_form'] = $this->load->view('common/v_reset_pw',null,true);
                        $data['js_script'] = $this->load->view('scripts/v_scripts_portal_page',null,true);
                        $this->load->view('v_portal_layout',$data);
                    }
                }
            }
        }
        else
        {
            show_404();
        }
    }

    private function forgot($redirect)
    {
        $data['title']     = "Gallery - Reset Password";
        $data['main_form'] = $this->load->view('common/v_forgot_pw',null,true);
        $data['js_script'] = $this->load->view('scripts/v_scripts_portal_page',null,true);
        $this->load->view('v_portal_layout',$data);
    }
}
