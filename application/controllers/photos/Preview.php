<?php
/**
* Preview photos
*/
class Preview extends CI_Controller
{
    private $media_path, $watermark;
    private $permissions = [];

    function __construct()
    {
        parent::__construct();
        $this->permissions = $this->auth->get_permissions();
        $this->load->library('SimpleImage');
        $this->config->load('media_gallery');
        $this->media_path = $this->config->item('mg_media_path');
        $this->watermark = $this->config->item('mg_watermark');
    }

    public function md($filename = null)
    {
        $name = explode('-', $filename);
        $uid = end($name);
        $path = $this->media_path;
        $mark = $this->watermark;
        $file = "{$path}/photos/private/full_size/{$uid}.jpg";
        $overlay = "{$path}/photos/private/watermark/{$mark}";

        $share_level = $this->validate_request($uid);

        if (file_exists($file))
        {
            if(!in_array('all',$this->permissions) && !in_array('media_view',$this->permissions) && $share_level !== "protected")
            {
                $picture = $this->simpleimage->load($file);
                $picture->best_fit(1280, 1280);
                $picture->overlay($overlay,"tile");
                $picture->output();
            }
            else
            {
                $picture = $this->simpleimage->load($file);
                $picture->best_fit(1280, 1280);
                $picture->output();
            }
        }
        else
        {
            show_404();
        }
    }

    public function lg($filename = null)
    {
        $name = explode('-', $filename);
        $uid = end($name);
        $path = $this->media_path;
        $mark = $this->watermark;
        $file = "{$path}/photos/private/full_size/{$uid}.jpg";
        $overlay = "{$path}/photos/private/watermark/{$mark}";

        $share_level = $this->validate_request($uid);

        if (file_exists($file))
        {
            if(!in_array('all',$this->permissions) && !in_array('media_view',$this->permissions) && $share_level !== "protected")
            {
                $picture = $this->simpleimage->load($file);
                $picture->best_fit(1920, 1920);
                $picture->overlay($overlay,"tile");
                $picture->output();
            }
            else
            {
                $picture = $this->simpleimage->load($file);
                $picture->best_fit(1920, 1920);
                $picture->output();
            }
        }
        else
        {
            show_404();
        }
    }
    
    public function full($filename = null)
    {
        $name = explode('-', $filename);
        $uid = end($name);
        $path = $this->media_path;
        $mark = $this->watermark;
        $file = "{$path}/photos/private/full_size/{$uid}.jpg";
        $overlay = "{$path}/photos/private/watermark/{$mark}";

        $share_level = $this->validate_request($uid);

        if (file_exists($file))
        {
            if(!in_array('all',$this->permissions) && !in_array('media_view',$this->permissions) && $share_level !== "protected")
            {
                $picture = $this->simpleimage->load($file);
                $picture->best_fit(1080, 1080);
                $picture->overlay($overlay,"tile");
                $picture->output();
            }
            else
            {
                header("Content-Type: image/jpg");
                readfile($file);
            }
        }
        else
        {
            show_404();
        }
    }

    private function validate_request($uid = null)
    {
        $uid = clean_alphanum_hash2($uid);
        $visibility = isset($_SESSION['user']['id'])? "AND (`share_level`='public' OR `share_level` LIKE '%[".$_SESSION['user']['id']."]%')" : "AND `share_level`='public'";
        if(in_array('all', $this->permissions) || in_array('photo_edit', $this->permissions))
        {
            $visibility = "";
        }
        $get_sql = "SELECT `id`,`share_level` FROM `photos` WHERE `uid`='{$uid}' {$visibility}";
        $get_qry = $this->db->query($get_sql);
        $item = $get_qry->result_array();
        if(count($item) > 0)
        {
            $share_level = $item[0]['share_level'];
            if($share_level !== "public" && $share_level !== "private") $share_level = "protected";
            return $share_level;
        }
        else
        {
            show_404();
            exit();
        }
    }
}
