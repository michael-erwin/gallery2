<?php
class Share extends CI_Controller
{
    private $permissions = [];
    
    function __construct()
    {
        parent::__construct();
        $this->permissions = $this->auth->get_permissions();
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
                if($info = $this->getInfo($param_2))
                {
                    $this->displayHTML($info);
                }
                else
                {
                    show_404();
                    exit();
                }
            }
            else
            {
                show_404();
                exit();
            }
        }
        else
        {
            $response = [
                "status" => "error",
                "code"=> 403,
                "message" => "You don't have enough permission. Please contact system administrator."
            ];
            if(!in_array('all',$this->permissions) && !in_array('photo_edit',$this->permissions))
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
            "code" => 500,
            "message" => "Invalid ids."
        ];

        $id = $this->input->post('id');
        $share_level = $this->input->post('share_level');
        $share_with  = $this->input->post('user_ids');
        $pvt_share_id = clean_alphanum_hash($this->input->post('pvt_share_id'));
        $errors = 0;

        if($id)
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
                $clean_ids = implode(',', $clean_ids);
                
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
                $set_sql   = "UPDATE `photos` SET `share_level`='{$share_level}',`pvt_share_id`='{$pvt_share_id}' WHERE `id` IN({$clean_ids})";
                if($this->db->query($set_sql))
                {
                    $response['status'] = "ok";
                    $response['code'] = 200;
                    $response['message'] = "Share level updated.";
                    $response['dbg_info'] = [
                        'syntax' => $set_sql,
                        'share_level' => $share_level
                    ];
                }
                else
                {
                    $response['code'] = 403;
                    $response['message'] = "Db error has occured.";
                }
            }
        }

        header("Content-Type: application/json");
        echo json_encode($response);
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

    private function getInfo($share_id)
    {
        $item_sql  = "SELECT `photos`.*,`categories`.`title` AS `sc_title`,`categories`.`parent_id` AS `mc_id` FROM `photos`";
        $item_sql .= " INNER JOIN `categories` ON `photos`.`category_id`=`categories`.`id` WHERE `photos`.`share_level`='private' AND `photos`.`pvt_share_id`='{$share_id}'";
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
