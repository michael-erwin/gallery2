<?php
/**
* Download a video.
*/
class Download extends CI_Controller
{
    private $permissions = [];

    public function __construct()
    {
        parent::__construct();
        $this->permissions = $this->auth->get_permissions();
        $this->config->load('media_gallery');
        $this->media_path = $this->config->item('mg_media_path');
    }

    public function full($filename = null)
    {
        $name = explode('-', $filename);
        $uid  = end($name);
        $path = $this->media_path;
        $mark = $this->watermark;
        $file_full = "{$path}/videos/private/full_size/{$uid}.mp4";
        $file_480p = "{$path}/videos/public/file_480p/{$uid}.mp4";
        $overlay = "{$path}/photos/private/watermark/{$mark}";
            
        if(in_array('all',$this->permissions) || in_array('media_view',$this->permissions))
        {
            if (file_exists($file_full))
            {
                header("Content-disposition: attachment; filename=\"{$filename}.mp4\"");
                readfile($file_full);
            }
            else
            {
                show_404();
            }
        }
        else
        {
            if (file_exists($file_480p))
            {
                header("Content-disposition: attachment; filename=\"{$filename}.mp4\"");
                readfile($file_480p);
            }
            else
            {
                show_404();
            }
        }
    }
}