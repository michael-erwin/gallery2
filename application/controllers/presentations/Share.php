<?php
class Share extends CI_Controller
{
    private $permissions = [];
    
    function __construct()
    {
        parent::__construct();
        $this->permissions = $this->auth->get_permissions();
        $this->load->model('M_Presentations');
        $this->load->model('M_Presentation_Items');
    }

    public function _remap()
    {
        $param_1 = $this->uri->segment(3);
        $param_2 = $this->uri->segment(4);
        $param_3 = $this->uri->segment(5);
        if($param_1 == "get")
        {
            if(strlen($param_2) == 32 && preg_match('/[a-z0-9]+/', $param_2))
            {
                $this->display_share($param_2);
            }
            else
            {
                show_404(); exit();
            }
        }
        else
        {
            $response = [
                "status" => "error",
                "code"=> 403,
                "message" => "You don't have enough permission. Please contact system administrator."
            ];
            if(!in_array('all',$this->permissions) && !in_array('presentation_edit',$this->permissions))
            {
                header("Content-Type: application/json");
                echo json_encode($response);
                exit();
            }
            $this->share();
        }
    }

    private function share()
    {
        $response = [
            "status" => "error",
            "code" => 400,
            "message" => "Invalid request."
        ];

        $id = $this->input->post('id');
        $share_level = $this->input->post('share_level');
        $share_with  = $this->input->post('user_ids');
        $pvt_share_id = clean_alphanum_hash($this->input->post('pvt_share_id'));
        $errors = 0;

        if($id && $share_level)
        {
            $id = explode(',', $id);
            $clean_ids = [];

            if(is_array($id))
            {
                foreach ($id as $raw_id) {
                    $clean_id = clean_numeric_text($raw_id);
                    if(strlen($clean_id) > 0) $clean_ids[] = $clean_id;
                }
            }
            else
            {
                $clean_id = clean_numeric_text($id);
                if(strlen($clean_id) > 0) $clean_ids[] = $clean_id;
            }

            if(count($clean_ids) == 0)
            {
                $error++;
            }
            else
            {
                if($share_level == 'private' || $share_level == 'public')
                {
                    $share_level = $share_level;
                }
                elseif($share_level == 'protected')
                {
                    if($share_with)
                    {
                        $share_with = explode(',', $share_with);
                        $clean_share_with = [];

                        foreach ($share_with as $user_id) {
                            $clean_user_id = clean_numeric_text($user_id);
                            if(strlen($clean_user_id) > 0) $clean_share_with[] = '['.$clean_user_id.']';
                        }

                        if(count($clean_share_with) > 0)
                        {
                            $share_level = implode(',', $clean_share_with);
                        } else { $errors++; }
                    }  else { $errors++; }
                } else { $errors++; }
            }

            if($errors == 0)
            {
                $fields = [ 'share_level' => $share_level, 'pvt_share_id' => $pvt_share_id ];
                $update_result = $this->M_Presentations->update($fields,$clean_ids);

                if($update_result['code'] == 'SUCCESS')
                {
                    $response['status'] = "ok";
                    $response['code'] = 200;
                    $response['message'] = "Item updated.";
                }
                else
                {
                    $response['message'] = "Database error has occured.";
                    $response['dbg_info'] = $update_result;
                }
            }

            header("Content-Type: application/json");
            echo json_encode($response);
        }
        else
        {
            show_404(); exit();
        }
    }

    private function display_share($share_id)
    {
        $entry_result = $this->M_Presentations->get_where([],"`pvt_share_id`='{$share_id}'");

        if($entry_result['code'] == 'SUCCESS')
        {
            if(count($entry_result['items']) > 0)
            {
                $entry = $entry_result['items'][0];
                $items = [];

                if(strlen($entry['items']) > 0)
                {
                    // Get associated items.
                    $items_result = $this->M_Presentation_Items->get_where([],"`parent_id`={$entry['id']}");

                    if($items_result['code'] == 'SUCCESS')
                    {
                        if(count($items_result['items']) > 0)
                        {
                            $sequence_source = explode(',', $entry['items']);
                            $sequence_output = [];
                            $sequence_items  = [];
                            foreach ($items_result['items'] as $sequence_item) $sequence_items[$sequence_item['id']] = $sequence_item;
                            foreach ($sequence_source as $sequence_id) $items[] = $sequence_items[$sequence_id];
                        }
                    }
                }

                // Display items.
                $entry['items'] = $items;
                $this->render_page($entry);
            }
            else
            {
                show_404();
            }
        }
        else
        {
            show_404();
        }    }

    private function render_page($result)
    {
        $data = [];
        $share_id = $result['pvt_share_id'];
        $data['page_title'] = $result['title'];
        $data['meta_description'] = $result['description'];
        $data['css_header_tags'] = $this->load->view('css/v_css_presentation',"",true);
        $data['account_actions'] = $this->load->view('common/v_menu_account_actions',null,true);
        $data['thumbs'] = '';
        $data['breadcrumbs'] = $this->load->view('common/v_breadcrumbs_frontend',['crumbs'=>[$result['title']=>'']],true);
        $data['search_widget'] = $this->load->view('common/v_search_widget',['type'=>'photos'],true);
        $data['result_js_init'] = $this->load->view('scripts/v_scripts_presentation',"",true);

        // Search widget and thumbnails display logic.
        if(count($result['items']) > 0)
        {
            foreach ($result['items'] as $item)
            {
                $thumb_data['id'] = $item['id'];
                $thumb_data['uid'] = $item['uid'];
                $thumb_data['title'] = $item['title'];
                $thumb_data['caption'] = $item['caption'];
                $thumb_data['parent_id'] = $item['parent_id'];
                $thumb_data['share_id'] = $share_id;
                $data['thumbs'] .= $this->load->view('common/v_result_thumbs_presentation',$thumb_data,true);
            }
        }
        else
        {
            $data['thumbs'] = $this->load->view('common/v_result_alert',['message'=>'No items.'],true);
        }

        $data['thumbs'] = compress_html($data['thumbs']);

        // Pagination display logic.
        $pagination_data = [
            'type' => 'photos',
            'keywords' => '',
            'current_page' => 1,
            'total_page' => 1,
            'prev_disabled' => true,
            'next_disabled' => true
        ];

        $data['pagination'] = $this->load->view('common/v_pagination_widget',$pagination_data,true);
        $this->load->view("v_results_layout",$data);
    }
}