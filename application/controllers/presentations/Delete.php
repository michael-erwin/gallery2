<?php
class Delete extends CI_Controller
{
    private $permissions = [];
    
    function __construct()
    {
        parent::__construct();
        $this->permissions = $this->auth->get_permissions();
        if(!in_array('all',$this->permissions) && !in_array('presentation_delete',$this->permissions))
        {
            $response['code'] = '403';
            $response['message'] = "You're not authorized to perform this action.";
            header("Content-Type: application/json");
            echo json_encode($response);
            exit();
        }
        $this->load->model('M_Presentations');
        $this->load->model('M_Presentation_Items');
        $this->config->load('media_gallery');
        $this->media_path = $this->config->item('mg_media_path');
    }

    public function _remap()
    {
        $item = $this->uri->segment(3);
        if($item == "item")
        {
            $this->delete_item();
        }
        else
        {
            $this->delete_entry();
        }
    }

    private function delete_entry()
    {
        $response = [
            'status' => 'error',
            'code' => 500,
            'message' => 'Invalid request.'
        ];

        $id = $this->input->post('id');

        if($id && is_numeric($id))
        {
            // Delete entry from presentations table.
            $delete_result = $this->M_Presentations->delete([$id]);

            if($delete_result['code'] == 'SUCCESS')
            {
                // Get associated item uids of entry.
                $get_item_uids_result = $this->M_Presentation_Items->get_where(['uid'],"`parent_id`={$id}");

                if($get_item_uids_result['code'] == 'SUCCESS')
                {
                    // Delete associated items.
                    $delete_items_result  = $this->M_Presentation_Items->delete_where("`parent_id`={$id}");

                    if($delete_items_result['code'] == 'SUCCESS')
                    {
                        // Delete associated files from disk.
                        foreach ($get_item_uids_result['items'] as $item)
                        {
                            $uid = $item['uid'];
                            @unlink($this->media_path."/presentation_items/full_size/{$uid}.jpg");
                            @unlink($this->media_path."/presentation_items/256/{$uid}.jpg");
                            @unlink($this->media_path."/presentation_items/128/{$uid}.jpg");
                        }
                            
                        // Response.
                        $response['status'] = "ok";
                        $response['code'] = 200;
                        $response['message'] = "Items deleted: {$delete_result['info']['affected']}.";
                    }
                    else
                    {
                        $response['message'] = "Delete of associated items failed.";
                        $response['dbg_info'] = $delete_items_result;
                    }
                }
                else
                {
                    $response['message'] = "Retrievieving of listed items for entry failed.";
                    $response['dbg_info'] = $get_item_uids_result;
                }
            }
            else
            {
                $response['message'] = "Database error has occured.";
                $response['dbg_info'] = $delete_result;
            }
        }

        header("Content-Type: application/json");
        echo json_encode($response);
    }

    private function delete_item()
    {
        $response = [
            'status' => 'error',
            'code' => 500,
            'message' => 'Invalid request.'
        ];

        $id = $this->input->post('id');

        if($id && is_numeric($id))
        {
            // Get uid info.
            $entry_table = $this->M_Presentations->get_table_name();
            $item_table  = $this->M_Presentation_Items->get_table_name();
            $get_common_sql = "SELECT `{$item_table}`.`uid`,`{$item_table}`.`parent_id`,`{$entry_table}`.`items` FROM `{$item_table}` INNER JOIN `{$entry_table}` ON `{$item_table}`.`parent_id`=`{$entry_table}`.`id` WHERE `{$item_table}`.`id`={$id}";
            $common_result = ($this->db->query($get_common_sql))->result_array();

            if(count($common_result) > 0)
            {
                $common_result = $common_result[0];

                $get_item_uids_result = $this->M_Presentation_Items->get_where(['uid','parent_id'],"`id`={$id}");

                if($get_item_uids_result['code'] == 'SUCCESS')
                {
                    // Delete item.
                    $delete_result = $this->M_Presentation_Items->delete([$id]);

                    if($delete_result['code'] == 'SUCCESS')
                    {
                        // Update parent entry items list.
                        $items_list = [];
                        if(strlen($common_result['items']) > 0) $items_list = explode(',', $common_result['items']);
                        if(count($items_list) > 0)
                        {
                            $items_list_new = [];
                            foreach ($items_list as $item) {
                                if($item != $id) $items_list_new[] = $item;
                            }
                            $items_list_new = implode(',', $items_list_new);
                            $this->M_Presentations->update(['items'=>$items_list_new],[$common_result['parent_id']]);
                        }

                        // Delete associated files from disk.
                        $uid = $get_item_uids_result['items'][0]['uid'];
                        @unlink($this->media_path."/presentation_items/full_size/{$uid}.jpg");
                        @unlink($this->media_path."/presentation_items/256/{$uid}.jpg");
                        @unlink($this->media_path."/presentation_items/128/{$uid}.jpg");

                        // Response.
                        $response['status'] = "ok";
                        $response['code'] = 200;
                        $response['message'] = "Items deleted: {$delete_result['info']['affected']}.";
                    }
                    else
                    {
                        $response['message'] = "Database error has occured.";
                        $response['dbg_info'] = $delete_result;
                    }
                }
                else
                {
                    $response['message'] = "Database error has occured. Data retreival failed.";
                    $response['dbg_info'] = $get_item_uids_result;
                }
            }
            else
            {
                $response['message'] = "Database error has occured. Common result for entry and items is empty.";
            }

            header("Content-Type: application/json");
            echo json_encode($response);
        }
    }
}
