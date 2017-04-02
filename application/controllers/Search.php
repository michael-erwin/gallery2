<?php
/**
*
*/
class Search extends CI_Controller
{
    private $permissions = [];

    function __construct()
    {
        parent::__construct();
        $this->permissions = $this->auth->get_permissions();
    }

    public function _remap($options=null)
    {
        $method = $this->uri->segment(2);
        $param_1 = $this->uri->segment(3);
        $param_2 = $this->uri->segment(4);

        if($method)
        {
            if($method == "videos")
            {
                $this->fetchMedia($method);
            }
            elseif($method == "category")
            {
                $type = ($param_1 == "videos")? $param_1 : "photos";
                $category_id = (!empty(clean_numeric_text($param_2)))? explode('-', $param_2) : 1;
                $category_id = is_array($category_id)? end($category_id) : $category_id;
                $this->fetchMedia($type,trim($category_id));
            }
            elseif($method == "tags") {
                $this->fetchTags();
            }
            elseif($method == "email")
            {
                if(!in_array('all',$this->permissions) && !in_array('user_view',$this->permissions))
                {
                    $response = [
                        "status" => "error",
                        "code" => 403,
                        "message" => "You don't have permission to perform this action."
                    ];
                    header("Content-Type: application/json");
                    echo json_encode($response);
                    exit();
                }
                $this->fetchEmail();
            }
            else
            {
                $this->fetchMedia($method);
            }
        }
        else
        {
            $this->fetchMedia("photos");
        }
    }

    private function fetchEmail()
    {
        $response = [
            "status" => "error",
            "code" => 500,
            "message" => "Unknown error has occured.",
            "data" => [ "items" => [], "total" => 0 ]
        ];

        $keyword = preg_replace('/[^a-zA-Z0-9\.\-_]/', '', $this->input->get('kw'));
        if(strlen($keyword) > 0)
        {
            $email_sql = "SELECT `id`,`email` FROM `users` WHERE `email` LIKE '%{$keyword}%' ORDER BY `email` ASC LIMIT 5";
            $email_qry = $this->db->query($email_sql);
            $results = $email_qry->result_array();
            $response['status'] = "ok";
            $response['code'] = 200;
            $response['message'] = "Success.";
            $response['data'] = [
                "items" => $results,
                "total" => count($results)
            ];
        }
        else
        {
            $response['status'] = "ok";
            $response['code'] = 200;
            $response['message'] = "Success.";
            $response['data'] = [
                "items" => [],
                "total" => 0
            ];
        }
        header("Content-Type: application/json");
        echo json_encode($response);
    }

    private function fetchTags()
    {
        $keyword = $this->input->get('kw');
        $response = [];
        if($keyword)
        {
            $keyword = clean_title_text($keyword);
            if(!empty($keyword)) {
                $sql = "SELECT `name` FROM `tags` WHERE `name` LIKE '%{$keyword}%' ORDER BY `name` LIMIT 5";
                $query = $this->db->query($sql);
                $response = $query->result_array();
            }
        }
        header("Content-Type: application/json");
        echo json_encode($response);
    }

    private function fetchMedia($type=null,$value=null)
    {
        $type = ($type != "")? $type : "photos";
        $crumbs = [
            'Home' => base_url(),
            ucfirst($type) => base_url("categories/{$type}"),
            'Search' => ""
        ];
        $keys = clean_title_text($this->input->get('kw'));
        $page = clean_numeric_text($this->input->get('p'));
        $page = empty($page)? 1 : $page;
        $page -= 1;
        $category = $value? $value : clean_numeric_text($this->input->get('c'));
        $limit = clean_numeric_text($this->input->get('l'));
        $limit = empty($limit)? 20 : $limit;
        $offset = $page * $limit;
        $mode = $this->input->get("m");
        $edit = $this->input->get('edit');

        if(strlen($keys) < 3)
        {
            $keys = $this->db->escape_like_str($keys);
            $sql  = "SELECT SQL_CALC_FOUND_ROWS * FROM `{$type}` WHERE";
            $tmp  = "";
            $tmp .= !empty($category)? " `category_id` = ".$category : "";
            $public_only = isset($_SESSION['user']['id'])? "(`share_level`='public' OR `share_level` LIKE '%[".$_SESSION['user']['id']."]%')" : "`share_level`='public'";
            $permission  = "photo_edit";

            if($type == "videos")
            {
                $permission = "video_edit";
                $tmp .= !empty($tmp)? " AND `complete`=1" : " `complete`=1";
            }
            if($edit){
                if(in_array('all', $this->permissions) || in_array($permission, $this->permissions))
                {
                    $public_only = "";
                }
            }

            $tmp .= !empty($tmp)? (!empty($public_only)? " AND {$public_only}" : "") : (!empty($public_only)? " {$public_only}" : "");
            $sql .= !empty($tmp)? "{$tmp} AND " : "";
            $sql .= " (`title` LIKE '%{$keys}%' OR `description` LIKE '%{$keys}%' OR `tags` LIKE '%{$keys}%') LIMIT {$limit} OFFSET {$offset}";
        }
        else
        {
            $keys = $this->db->escape_str(preg_replace('/-+$/', '', $keys));
            $sql  = "SELECT SQL_CALC_FOUND_ROWS * FROM `{$type}` WHERE";
            $tmp  = "";
            $tmp .= !empty($category)? " `category_id` = ".$category : "";
            $public_only = isset($_SESSION['user']['id'])? "(`share_level`='public' OR `share_level` LIKE '%[".$_SESSION['user']['id']."]%')" : "`share_level`='public'";
            $permission  = "photo_edit";

            if($type == "videos")
            {
                $permission = "video_edit";
                $tmp .= !empty($tmp)? " AND `complete`=1" : " `complete`=1";
            }
            if($edit){
                if(in_array('all', $this->permissions) || in_array($permission, $this->permissions))
                {
                    $public_only = "";
                }
            }

            $tmp .= !empty($tmp)? (!empty($public_only)? " AND {$public_only}" : "") : (!empty($public_only)? " {$public_only}" : "");
            $sql .= !empty($tmp)? "{$tmp} AND " : "";
            $sql .= " (MATCH (`title`,`description`,`tags`) AGAINST('*{$keys}*' IN BOOLEAN MODE)) LIMIT {$limit} OFFSET {$offset}";
        }

        $data = $this->db->query($sql);
        $rows = $this->db->query("SELECT FOUND_ROWS() AS `total`");

        $items = $data->result_array();
        $total = $rows->result_array()[0]['total'];

        $response = [
            'type' => $type,
            'keywords' => $keys,
            'category_name' => "",
            'category_id' => $category,
            'crumbs' => $crumbs,
            'route' => 'search',
            'page' => [
                'current' => $page+1,
                'total' => ceil($total / $limit),
                'limit' => $limit
            ],
            'items' => [
                'type' => $type,
                'entries' => $items,
                'total' => $total
            ],
            'dbg_info' => [
                'sql' => $sql
            ]
        ];

        $page_meta = [
            'title' => 'Gallery - Search Results',
            'description' => '',
            'keywords' => ''
        ];

        // Output types:
        if($mode && $mode == "json")
        {
            $response['page_meta'] = $page_meta;
            header("Content-Type: application/json");
            echo json_encode($response);
        }
        else
        {
            $this->index($response,$page_meta);
        }
    }

    private function index($result, $page_meta=null)
    {
        $data = [];
        $data['account_actions'] = $this->load->view('common/v_menu_account_actions',null,true);
        $data['thumbs'] = '';
        $data['breadcrumbs'] = $this->load->view('common/v_breadcrumbs_frontend',['crumbs'=>$result['crumbs']],true);
        $data['pagination'] = '';
        $data['result_js_init'] = $this->load->view('scripts/v_scripts_results',['result'=>$result],true);

        // Page meta SEO logic.
        if($page_meta)
        {
            $data['page_title'] = $page_meta['title'];
            $data['meta_description'] = $page_meta['description'];
            $data['meta_keywords'] = $page_meta['keywords'];
        }

        // Search widget and thumbnails display logic.
        if($result['items']['type'] == "photos")
        {
            $data['search_widget'] = $this->load->view('common/v_search_widget',['type'=>'photos'],true);

            if($result['items']['total'] > 0)
            {
                foreach ($result['items']['entries'] as $item)
                {
                    $thumb_data['data'] = json_encode($item);
                    $thumb_data['title'] = $item['title'];
                    $thumb_data['uid'] = $item['uid'];
                    $thumb_data['seo_title'] = preg_replace('/\s/', '-', $item['title']).'-'.$item['uid'];
                    $data['thumbs'] .= $this->load->view('common/v_result_thumbs_photos',$thumb_data,true);
                }
            }
            else
            {
                $data['thumbs'] = '<div class="alert alert-warning">No results.</div>';
            }

        }
        else if($result['items']['type'] == "videos")
        {
            $data['search_widget'] = $this->load->view('common/v_search_widget',['type'=>'videos'],true);

            if($result['items']['total'] > 0)
            {
                foreach ($result['items']['entries'] as $item)
                {
                    $thumb_data['data'] = json_encode($item);
                    $thumb_data['title'] = $item['title'];
                    $thumb_data['uid'] = $item['uid'];
                    $thumb_data['seo_title'] = preg_replace('/\s/', '-', $item['title']).'-'.$item['uid'];
                    $data['thumbs'] .= $this->load->view('common/v_result_thumbs_videos',$thumb_data,true);
                }
            }
            else
            {
                $data['thumbs'] = '<div class="alert alert-warning">No results.</div>';
            }

        }
        $data['thumbs'] = compress_html($data['thumbs']);

        // Pagination display logic.
        $pagination_data = [
            'type' => $result['type'],
            'keywords' => $result['keywords'],
            'category_id' => $result['category_id'],
            'category_name' => $result['category_name'],
            'current_page' => $result['page']['current'],
            'total_page' => $result['page']['total'],
            'prev_disabled' => false,
            'next_disabled' => false
        ];
        if($result['page']['current'] == 1) $pagination_data['prev_disabled'] = true;
        if($result['page']['current'] >= $result['page']['total']) $pagination_data['next_disabled'] = true;

        $data['pagination'] = $this->load->view('common/v_pagination_widget',$pagination_data,true);
        $this->load->view("v_results_layout",$data);
    }

    private function formatSizeUnits($bytes)
    {
        if ($bytes >= 1073741824)
        {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        }
        elseif ($bytes >= 1048576)
        {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        }
        elseif ($bytes >= 1024)
        {
            $bytes = number_format($bytes / 1024, 2) . ' kB';
        }
        elseif ($bytes > 1)
        {
            $bytes = $bytes . ' bytes';
        }
        elseif ($bytes == 1)
        {
            $bytes = $bytes . ' byte';
        }
        else
        {
            $bytes = '0 bytes';
        }

        return $bytes;
    }
}
