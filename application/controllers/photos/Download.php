<?php
/**
* Download photos
*/
class Download extends CI_Controller
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

    public function full($filename = null)
    {
        $name = explode('-', $filename);
        $uid  = end($name);
        $path = $this->media_path;
        $mark = $this->watermark;
        $file = "{$path}/photos/private/full_size/{$uid}.jpg";
        $overlay = "{$path}/photos/private/watermark/{$mark}";

        if (file_exists($file))
        {
            
            if(in_array('all',$this->permissions) || in_array('media_view',$this->permissions))
            {
                header("Content-disposition: attachment; filename=\"{$filename}.jpg\"");
                readfile($file);
            }
            else
            {
                $picture = $this->simpleimage->load($file);
                $picture->overlay($overlay,"tile");
                header("Content-disposition: attachment; filename=\"{$filename}.jpg\"");
                $picture->output();
            }
        }
        else
        {
            show_404();
        }
    }
    
    public function zip($filename = null)
    {
        $name = explode('-', $filename);
        $uid  = end($name);
        $path = $this->media_path;
        $mark = $this->watermark;
        $file = "{$path}/photos/private/zip/{$uid}.zip";

        if (file_exists($file))
        {
            if(in_array('all',$this->permissions) || in_array('media_view',$this->permissions))
            {
                header("Content-disposition: attachment; filename=\"{$filename}.zip\"");
                readfile($file);
            }
            else
            {
                show_404();
            }
        }
        else
        {
            show_404();
        }
    }
}
