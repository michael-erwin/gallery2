<?php
/**
* Preview photos
*/
class Photo extends CI_Controller
{
    private $media_path, $watermark;
    private $permissions = [];

    function __construct()
    {
        parent::__construct();
        $this->permissions = $this->auth->get_permissions();
        $this->load->library('SimpleImage');
        $this->load->model('M_Presentations');
        $this->load->model('M_Presentation_Items');
        $this->config->load('media_gallery');
        $this->media_path = $this->config->item('mg_media_path');
        $this->watermark = $this->config->item('mg_watermark');
    }

    public function md($uid = null)
    {
        if(preg_match('/([0-9]+)_([0-9]+_[0-9]+)$/', $item_id, $matches))
        {
            $parent_id = $matches[1];
            $uid  = $matches[2];
            $path = $this->media_path;
            $mark = $this->watermark;
            $file = "{$path}/presentation_items/full_size/{$uid}.jpg";
            $overlay = "{$path}/photos/private/watermark/{$mark}";

            if (file_exists($file))
            {
                if(!$this->validate_request($parent_id))
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
        else
        {
            show_404();
        }
    }

    public function lg($uid = null)
    {
        if(preg_match('/([0-9]+)_([0-9]+_[0-9]+)$/', $item_id, $matches))
        {
            $parent_id = $matches[1];
            $uid  = $matches[2];
            $path = $this->media_path;
            $mark = $this->watermark;
            $file = "{$path}/presentation_items/full_size/{$uid}.jpg";
            $overlay = "{$path}/photos/private/watermark/{$mark}";

            if (file_exists($file))
            {
                if(!$this->validate_request($parent_id))
                {
                    $picture = $this->simpleimage->load($file);
                    $picture->best_fit(1080, 1080);
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
        else
        {
            show_404();
        }
    }
    
    public function full($item_id = null)
    {

        if(preg_match('/([0-9]+)_([0-9]+_[0-9]+)$/', $item_id, $matches))
        {
            $parent_id = $matches[1];
            $uid  = $matches[2];
            $path = $this->media_path;
            $mark = $this->watermark;
            $file = "{$path}/presentation_items/full_size/{$uid}.jpg";
            $overlay = "{$path}/photos/private/watermark/{$mark}";

            if (file_exists($file))
            {
                if(!$this->validate_request($parent_id))
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
        else
        {
            show_404();
        }
    }

    private function validate_request($id = null)
    {
        $share_id = $this->input->get('share_id');
        $entry_result = $this->M_Presentations->get_where([],"`id`={$id}");

        if($entry_result['code'] == 'SUCCESS')
        {
            if(count($entry_result['items']) > 0)
            {
                $entry = $entry_result['items'][0];

                if($entry['share_level'] == 'private')
                {
                    if($share_id)
                    {
                        if($share_id == $entry['pvt_share_id'])
                        {
                            return true;
                        } else { return false; }
                    } else { return false; }
                }
                elseif($entry['share_level'] != 'public')
                {
                    if(isset($_SESSION['user']['id']))
                    {
                        if(preg_match('/\['.$_SESSION['user']['id'].'\]/', $entry['share_level']))
                        {
                            return true;
                        } else { return false; }
                    } else { return false; }
                } else { return true; }
            } else { return false; }
        } else { return false; }
    }
}
