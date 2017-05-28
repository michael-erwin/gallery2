<?php
/**
* Video uploader.
*/
class Presentation_item extends CI_Controller
{
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
    }

    function _remap()
    {
        $this->upload();
    }

    private function upload()
    {
        $entry_id = $this->input->post('id');

        if(isset($_FILES['file']) && $entry_id)
        {
            $response = [
                'status' => 'error',
                'code' => 500,
                'message' => 'Internal server error.',
                'data' => null
            ];

            if(is_numeric($entry_id))
            {
                $path        = $this->media_path;
                $uid         = str_replace('.', '_', microtime(true));
                $title       = preg_replace('/\.jpg/', "", $_FILES['file']['name']);
                $source      = $_FILES['file']['tmp_name'];
                $picture     = $this->simpleimage->load($source);
                $width       = $picture->get_width();
                $height      = $picture->get_height();
                $checksum    = md5_file($source);
                $orientation = ($width > $height)? "horizontal" : "vertical";
                // Save original dimension to file system.
                $full_size_file = "{$path}/presentation_items/full_size/{$uid}.jpg";
                $picture->save($full_size_file);

                // Get the saved file size.
                $size = filesize($full_size_file);

                // Save 256px square box cover dimension to file system.
                if ($orientation == "horizontal") { $picture->fit_to_height(256); } else { $picture->fit_to_width(256); };
                $picture->save("{$path}/presentation_items/256/{$uid}.jpg");

                // Save 128px square box cover dimension to file system.
                if ($orientation == "horizontal") { $picture->fit_to_height(128); } else { $picture->fit_to_width(128); };
                $picture->save("{$path}/presentation_items/128/{$uid}.jpg");

                // DB operation.
                $data = [
                    'uid' => $uid,
                    'title' => $title,
                    'width' => $width,
                    'height' => $height,
                    'file_size' => $size,
                    'parent_id' => $entry_id
                ];

                // Insert Item.
                $db_insert = $this->M_Presentation_Items->insert($data);
                $id_insert = $db_insert['info']['insert_id'];

                if ($db_insert['code'] == 'SUCCESS')
                {
                    $db_get_parent = $this->M_Presentations->get_where(['items'],"`id`={$entry_id}");
                    $parent_items = [];
                    if(strlen($db_get_parent['items'][0]['items']) > 0) $parent_items = explode(',', $db_get_parent['items'][0]['items']);
                    array_push($parent_items,$id_insert);
                    $update_items = implode(',', $parent_items);

                    // Update parent entry items list.
                    $db_update = $this->M_Presentations->update(['items'=>$update_items],[$entry_id]);

                    if($db_update['code'] == 'SUCCESS')
                    {
                        $response['status'] = "ok";
                        $response['code'] = 200;
                        $response['message'] = "Item added.";
                        $response['data'] = array_merge(['id'=>$id_insert],$data);
                    }
                    else
                    {
                        $response['message'] = "Parent table update failed.";
                        $response['dbg_info'] = $db_update;
                    }
                    
                }
                else
                {
                    $response['message'] = "Database write failed.";
					$response['dbg_info'] = $db_insert;
                    @unlink($this->media_path."/presentation_items/full_size/{$uid}.jpg");
					@unlink($this->media_path."/presentation_items/256/{$uid}.jpg");
					@unlink($this->media_path."/presentation_items/128/{$uid}.jpg");
                }
            }
            else
            {
                $response['message'] = "Id is invalid.";
            }

            header("Content-Type: application/json");
            echo json_encode($response);
        }
    }
}