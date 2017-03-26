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

    public function md($name = null)
    {
        $path = $this->media_path;
        $mark = $this->watermark;
        $file = "{$path}/photos/private/full_size/{$uid}.jpg";
        $overlay = "{$path}/photos/private/watermark/{$mark}";

        if ($name && file_exists($file))
        {
            $picture = $this->simpleimage->load($file);
            $picture->best_fit(768, 768);
            $picture->overlay($overlay,"tile");
            $picture->output();
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

        if (file_exists($file))
        {
            $picture = $this->simpleimage->load($file);
            $picture->best_fit(1080, 1080);
            if(!in_array('all',$this->permissions) && !in_array('media_view',$this->permissions)) $picture->overlay($overlay,"tile");
            $picture->output();
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

        if (file_exists($file))
        {
            if(!in_array('all',$this->permissions) && !in_array('media_view',$this->permissions))
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
}
