<?php
class Item extends CI_Controller
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
        $item_id = $this->uri->segment(3);
        if(preg_match('/[a-z\-_]\-([0-9]+)$/', $item_id, $matches))
        {
            $id = $matches[1];
            $this->display_entry($id);
        }
        else
        {
            show_404(); exit();
        }
    }

    private function display_entry($id)
    {
        // Get item entry.
        $entry_result = $this->validate_request($id,$this->input->get('share_id'));

        if($entry_result['code'] == 'SUCCESS')
        {
            if(count($entry_result['items']) > 0)
            {
                $entry = $entry_result['items'][0];
                $items = [];

                if(strlen($entry['items']) > 0)
                {
                    // Get associated items.
                    $items_result = $this->M_Presentation_Items->get_where([],"`parent_id`={$id}");

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
        }
    }

    private function render_page($result)
    {
        $data = [];
        $share_id = $result['pvt_share_id'];
        $data['page_title'] = $result['title'];
        $data['meta_description'] = $result['description'];
        $data['account_actions'] = $this->load->view('common/v_menu_account_actions',null,true);
        $data['thumbs'] = '';
        $data['breadcrumbs'] = $this->load->view('common/v_breadcrumbs_frontend',['crumbs'=>[$result['title']=>'']],true);
        $data['search_widget'] = $this->load->view('common/v_search_widget',['type'=>'photos'],true);
        $data['presentation_js_init'] = $this->load->view('scripts/v_scripts_presentation',"",true);

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
        $this->load->view("v_presentation_layout",$data);
    }

    private function validate_request($id,$share_id=null)
    {
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
                            return $entry_result;
                        } else { return false; }
                    } else { return false; }
                }
                elseif($entry['share_level'] != 'public')
                {
                    if(isset($_SESSION['user']['id']))
                    {
                        if(preg_match('/\['.$_SESSION['user']['id'].'\]/', $entry['share_level']))
                        {
                            return $entry_result;
                        } else { return false; }
                    } else { return false; }
                } else { return $entry_result; }
            } else { return false; }
        } else { return false; }
    }
}
