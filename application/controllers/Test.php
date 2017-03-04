<?php
/**
* Tester
*/
class Test extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
    }

    // public function _remap()
    // {
    //     $permissions = $this->auth->get_permissions();
    //     header("Content-Type: text/plain");
    //     print_r($permissions);
        
    // }
    public function index()
    {
        header("Content-Type: text/plain");
        print_r($_SESSION);
    }
     
    public function login()
    {
        $email = $this->input->get('e');
        $passw = $this->input->get('p');
        if($this->auth->sign_in($email,$passw,true))
        {
            header("Content-Type: text/plain");
            print_r($_SESSION['user']);
        }
        else
        {
            echo "Login failed.";
            echo "<br>";
            echo "user: {$email}";
            echo "<br>";
            echo "pass: {$passw}";
        }
    }

    public function validate()
    {
        var_dump(password_verify('Billy','$2y$10$AjFw9yxAx/0Tr09AKZdzg.n/1BzTlb.i8CrTLdpSeTcLC0XtMh1Re'));
        
    }

    public function generate()
    {
        echo password_hash('John',PASSWORD_BCRYPT);
    }

    public function permissions()
    {
        $permissions = $this->auth->get_permissions();
        header("Content-Type: text/plain");
        print_r($permissions);
    }
}
