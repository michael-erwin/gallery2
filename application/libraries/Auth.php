<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
* Simple authentication library for use in Gallery software release.
*  
* @package Auth class.
* @author  Michael Erwin Virgines <michael.erwinp@gmail.com>
*/
class Auth
{
    private $remember_time = 86400 * 14; // 14 days

    function __construct()
    {
        $this->ci =& get_instance();
        $this->db = $this->ci->db;
    }

    /**
     * Get current user's permissions.
     * 
     * @return Array Lists of user's permission.
     */
    function get_permissions()
    {
        $permissions = [];
        if(isset($_SESSION['user']))
        {
            if(isset($_SESSION['user']['id']))
            {
                if(isset($_SESSION['permissions']))
                {
                    $permissions = $_SESSION['permissions'];
                }
                else
                {
                    $sql_permissions = "SELECT `users`.`id`,`roles`.`permissions` FROM `users` INNER JOIN `roles` ON `users`.`role_id`=`roles`.`id` WHERE `users`.`id`=".$_SESSION['user']['id']." AND `status`='active'";
                    $query = $this->db->query($sql_permissions);
                    if($query->num_rows() > 0)
                    {
                        $result_data = $query->result_array()[0];
                        $permissions = explode(',', $result_data['permissions']);
                        $_SESSION['permissions'] = $permissions;
                        $this->ci->session->mark_as_temp('permissions',120); // Renew every 2 minutes.
                    }
                }
            }
        }
        return $permissions;
    }

    /**
     * Authenticate user credentials then create session with user's data if
     * login is valid.
     * 
     * @param  String  $email    User's email address.
     * @param  String  $password User's password.
     * @param  Boolean $remember If remember me feature is enabled.
     * @return Boolean           Returns true if authentication succeed, false
     *                           otherwise.
     */
    function sign_in($email,$password,$remember=false)
    {
        $sql_user = "SELECT `users`.*,`roles`.`id` AS `role_id`,`roles`.`name` AS `role_name` FROM `users` INNER JOIN `roles` ON `users`.`role_id`=`roles`.`id` WHERE `email`='{$email}' AND `status`='active'";
        $query = $this->db->query($sql_user);
        if($query->num_rows() > 0)
        {
            $user_data = $query->result_array()[0];
            if(password_verify($password,$user_data['password']))
            {
                unset($_SESSION['user']);
                $_SESSION['user'] = $user_data;
                if($remember) $this->ci->session->mark_as_temp('user',$this->remember_time); // Remember me.
                return true;
            }
            else
            {
                unset($_SESSION['user']);
                return false;
            }
        }
        else
        {
            unset($_SESSION['user']);
            return false;
        }
    }
}