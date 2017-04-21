<?php
/**
* Default photo class.
*/
class Item extends CI_Controller
{
    private $permissions = [];

    function __construct()
    {
        parent::__construct();
        $this->permissions = $this->auth->get_permissions();
    }

    public function _remap()
    {
        $item_name = $this->uri->segment(3);
        $name_pattern = '/\-([a-z0-9_]+$)/';
        $mode = $this->input->get('m');
        if(preg_match($name_pattern, $item_name, $matches))
        {
            $info = $this->getInfo($matches[1]);
            if($info) {
                if($mode && $mode == "json")
                {
                    $this->displayJSON($info);
                }
                else
                {
                    $this->displayHTML($info);
                }
            }
            else {
                show_404();
            }
        }
        else
        {
            show_404();
        }
    }

    private function displayHTML($info)
    {
        $type = "photos";
        $crumbs = [
            'Categories' => base_url("categories"),
            $info['category']['main']['title'] => $info['category']['main']['link'],
            $info['category']['sub']['title'] => $info['category']['sub']['link'],
            $info['title'] => ""
        ];
        $media = [
            'uid' => $info['uid'],
            'title' => $info['title'],
            'description' => $info['description'],
            'dimension' => $info['width'].'x'.$info['height'],
            'file_size' => $this->formatSizeUnits($info['file_size']),
            'tags' => explode(' ',$info['tags']),
            'has_zip' => $info['has_zip'],
            'photo_url' => base_url()."photos/preview/lg/".preg_replace('/\s/','-', $info['title'])."-".$info['uid']
        ];
        $data['breadcrumbs'] = $this->load->view('common/v_breadcrumbs_frontend',['crumbs'=>$crumbs],true);
        $data['pagination'] = '';
        $data['search_widget'] = $this->load->view('common/v_search_widget',['type'=>'photos'],true);
        $data['media_item_details'] = $this->load->view('common/v_photo_item_page_frontend',$media,true);

        // Page meta SEO logic.
        $data['page_title'] = $info['title'];
        $data['meta_description'] = $info['description'];
        $data['meta_keywords'] = $info['tags'];

        // Account actions menu
        $data['account_actions'] = $this->load->view('common/v_menu_account_actions',null,true);

        // Pagination display logic.
        $pagination_data = [
            'current_page' => 1,
            'total_page' => 1,
            'prev_disabled' => true,
            'next_disabled' => true
        ];

        // Javscript triggers.
        $data['result_js_init'] = $this->load->view('scripts/v_scripts_photo_page','',true);

        $data['pagination'] = $this->load->view('common/v_pagination_widget',$pagination_data,true);
        $this->load->view("v_results_layout",$data);
    }

    private function displayJSON($info)
    {
        $info['dimension'] = $info['width'].'x'.$info['height'];
        $info['file_size'] = $this->formatSizeUnits($info['file_size']);
        $info['tags'] = explode(' ',$info['tags']);
        header("Content-Type: application/json");
        echo json_encode($info);
    }

    private function getInfo($uid)
    {
        //$visibility = isset($_SESSION['user']['id'])? "(`share_level`='public' OR `share_level` LIKE '%[".$_SESSION['user']['id']."]%')" : "`share_level`='public'";
        if(isset($_SESSION['user']['id']))
        {
            $item_visibility  = "(`photos`.`share_level`='public' OR `photos`.`share_level` LIKE '%[".$_SESSION['user']['id']."]%')";
            $item_visibility .= " AND (`photos`.`mc_share_level`='public' OR `photos`.`mc_share_level` LIKE '%[".$_SESSION['user']['id']."]%')";
            $item_visibility .= " AND (`photos`.`sc_share_level`='public' OR `photos`.`sc_share_level` LIKE '%[".$_SESSION['user']['id']."]%')";
        }
        else
        {
            $item_visibility = "`photos`.`share_level`='public' AND `photos`.`mc_share_level`='public' AND `photos`.`sc_share_level`='public'";
        }
        $item_sql = "SELECT `photos`.*,`categories`.`title` AS `sc_title`,`categories`.`parent_id` AS `mc_id` FROM `photos` INNER JOIN `categories` ON `photos`.`category_id`=`categories`.`id` WHERE `uid`='{$uid}' AND {$item_visibility}";
        $item_qry = $this->db->query($item_sql);
        $item_res = $item_qry->result_array();
        if(count($item_res) > 0)
        {
            $mc_id = $item_res[0]['mc_id'];
            $item_mc_sql = "SELECT `title` FROM `categories` WHERE `id`={$mc_id}";
            $item_mc_qry = $this->db->query($item_mc_sql);
            $item_mc_res = $item_mc_qry->result_array();
            $mc_title_sef = strtolower(preg_replace('/ /', '-', $item_mc_res[0]['title'])).'-'.$item_res[0]['mc_id'];
            $sc_title_sef = strtolower(preg_replace('/ /', '-', $item_res[0]['sc_title'])).'-'.$item_res[0]['category_id'];
            if(count($item_mc_res) > 0)
            {
                return [
                    'id' => $item_res[0]['id'],
                    'uid' => $item_res[0]['uid'],
                    'category' => [
                        'main' => [
                            'title' => $item_mc_res[0]['title'],
                            'link' => base_url('categories/'.$mc_title_sef.'/')
                        ],
                        'sub' => [
                            'title' => $item_res[0]['sc_title'],
                            'link' => base_url('categories/'.$mc_title_sef.'/'.$sc_title_sef.'/photos/')
                        ]
                    ],
                    'title' => $item_res[0]['title'],
                    'description' => $item_res[0]['description'],
                    'tags' => $item_res[0]['tags'],
                    'width' => $item_res[0]['width'],
                    'height' => $item_res[0]['height'],
                    'file_size' => $item_res[0]['file_size'],
                    'has_zip' => $item_res[0]['has_zip'],
                    'date_added' => $item_res[0]['date_added'],
                    'date_modified' => $item_res[0]['date_modified']
                ];
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }

    private function formatSizeUnits($bytes, $decimals = 2) {
        $size = ['B','kB','MB','GB','TB'];
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f ", $bytes / pow(1000, $factor)) . @$size[$factor];
    }
}
