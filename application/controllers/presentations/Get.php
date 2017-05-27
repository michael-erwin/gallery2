<?php
class Get extends CI_Controller
{
    private $permissions = [];
    
    function __construct()
    {
        parent::__construct();
        $this->permissions = $this->auth->get_permissions();
        $this->load->model('M_Presentations');
        $this->load->model('M_Presentation_Items');
        if(!in_array('all',$this->permissions) && !in_array('presentation_add',$this->permissions) && !in_array('presentation_edit',$this->permissions))
        {
            $response['code'] = '403';
            $response['message'] = "You're not authorized to perform this action.";
            header("Content-Type: application/json");
            echo json_encode($response);
            exit();
        }
    }

    public function _remap()
    {
        $item_num = $this->uri->segment(3);
        $item_prop = $this->uri->segment(4);

        if(is_numeric($item_num))
        {
            if($item_prop == "items")
            {
                
                $response = [
                    'status' => 'error',
                    'code' => 500,
                    'message' => 'Invalid request.'
                ];

                $get_result = $this->M_Presentations->get_where([],"`id`='{$item_num}'");
                
                if($get_result['code'] == 'SUCCESS')
                {
                    if(isset($get_result['items'][0]))
                    {
                        $this->get_entry_items($get_result['items'][0]['items']);
                    }
                    else
                    {
                        $response['status'] = "ok";
                        $response['message'] = "";
                        $response['data'] = ['items'=>[]];
                        header("Content-Type: application/json");
                        echo json_encode($response);
                    }
                }
                else
                {
                    $response['message'] = "Database error has occured.";
                    $response['dbg_info'] = $get_result;
                    header("Content-Type: application/json");
                    echo json_encode($response);
                }
                
            }
            elseif(strlen($item_prop) == 0)
            {
                $this->get_entry($item_num);
            }
        }
        elseif(strlen($item_num) == 0)
        {
            $this->get_entries();
        }
    }

    private function get_entry($id)
    {
        $response = [
            'status' => 'error',
            'code' => 500,
            'message' => 'Invalid request.'
        ];

        $get_result = $this->M_Presentations->get_where([],"`id`='{$id}'");
        
        if($get_result['code'] == 'SUCCESS')
        {
            $response['status'] = "ok";
            $response['code'] = 200;
            $response['data']['items'] = $get_result['items'];
            $response['message'] = "Items found: {$get_result['info']['fetched']}.";
        }
        else
        {
            $response['message'] = "Database error has occured.";
            $response['dbg_info'] = $get_result;
        }

        header("Content-Type: application/json");
        echo json_encode($response);
    }

    private function get_entries()
    {
        $response = [
            'status' => 'error',
            'code' => 500,
            'message' => 'Invalid request.'
        ];

        $get_result = $this->M_Presentations->get();
        
        if($get_result['code'] == 'SUCCESS')
        {
            $response['status'] = "ok";
            $response['code'] = 200;
            $response['data']['items'] = $get_result['items'];
            $response['message'] = "Items found: {$get_result['info']['fetched']}.";
        }
        else
        {
            $response['message'] = "Database error has occured.";
            $response['dbg_info'] = $get_result;
        }

        header("Content-Type: application/json");
        echo json_encode($response);
    }

    private function get_entry_items($ids)
    {
        if(strlen(trim($ids)) > 0)
        {
            $sequence_source = explode(',', $ids);
            $sequence_output = [];

            $response = [
                'status' => 'error',
                'code' => 500,
                'message' => 'Invalid request.'
            ];

            $get_result = $this->M_Presentation_Items->get_where([],"`id` IN ($ids)");
            
            if($get_result['code'] == 'SUCCESS')
            {
                // Set the correct sequence.
                $sequence_items = [];
                foreach ($get_result['items'] as $sequence_item) $sequence_items[$sequence_item['id']] = $sequence_item;
                foreach ($sequence_source as $sequence_id) $sequence_output[] = $sequence_items[$sequence_id];
                $response['status'] = "ok";
                $response['code'] = 200;
                $response['data']['items'] = $sequence_output;
                $response['message'] = "Items found: {$get_result['info']['fetched']}.";
            }
            else
            {
                $response['message'] = "Database error has occured.";
                $response['dbg_info'] = $get_result;
            }
        }
        else
        {
            $response = [
                'status' => 'ok',
                'code' => 200,
                'message' => 'Success.',
                'data' => [
                    'items' => []
                ]
            ];
        }

        header("Content-Type: application/json");
        echo json_encode($response);
    }
}
