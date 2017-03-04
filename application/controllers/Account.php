<?php
/**
* Tester
*/
class Account extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
    }

    public function _remap()
    {
        $task = $this->uri->segment(2);
        $redirect = $this->input->get('redirect');

        if($task == "signup")
        {
            $this->signUp($redirect);
        }
        elseif($task == "forgot")
        {
            $this->forgot($redirect);
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
        $this->load->view('v_portal_layout',$data);
    }

    private function signUp($redirect)
    {
        $data['title']     = "Gallery - Sign Up";
        $data['main_form'] = $this->load->view('common/v_sign_up',null,true);
        $this->load->view('v_portal_layout',$data);
    }

    private function forgot($redirect)
    {
        $data['title']     = "Gallery - Reset Password";
        $data['main_form'] = $this->load->view('common/v_forgot_pw',null,true);
        $this->load->view('v_portal_layout',$data);
    }
}
