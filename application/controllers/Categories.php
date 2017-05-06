<?php
/**
* Categories controller.
*/
class Categories extends CI_Controller
{
    private $permissions = [];

    function __construct()
    {
        parent::__construct();
        $this->permissions = $this->auth->get_permissions();
        $this->load->model('m_category');
    }

    /**
    * Function description.
    * @param  type  $variable  Variable description.
    * @return type             Return value description.
    *
    */
    public function _remap()
    {
        $param_1 = $this->uri->segment(2);
        $param_2 = $this->uri->segment(3);
        $param_3 = $this->uri->segment(4);
        $param_4 = $this->uri->segment(5);
        $allowed_media_types = ['photos','videos'];

        if($param_1 == "manage")
        {
            if($param_2 == "add")
            {
                if(!in_array('all',$this->permissions) && !in_array('category_add',$this->permissions))
                {
                    $response = [
                        "status" => "error",
                        "code"=> 403,
                        "message" => "You're not authorized to perform this action.",
                        "data" => null,
                        "page" => null
                    ];
                    header("Content-Type: application/json");
                    echo json_encode($response);
                    exit();
                }
                $this->add();
            }
            elseif($param_2 == "update")
            {
                if(!in_array('all',$this->permissions) && !in_array('category_edit',$this->permissions))
                {
                    $response = [
                        "status" => "error",
                        "code"=> 403,
                        "message" => "You're not authorized to perform this action.",
                        "data" => null,
                        "page" => null
                    ];
                    header("Content-Type: application/json");
                    echo json_encode($response);
                    exit();
                }
                $this->update();
            }
            elseif($param_2 == "delete")
            {
                if(!in_array('all',$this->permissions) && !in_array('category_delete',$this->permissions))
                {
                    $response = [
                        "status" => "error",
                        "code"=> 403,
                        "message" => "You're not authorized to perform this action.",
                        "data" => null,
                        "page" => null
                    ];
                    header("Content-Type: application/json");
                    echo json_encode($response);
                    exit();
                }
                $this->delete();
            }
            elseif($param_2 == "get_by_type")
            {
                if(!in_array('all',$this->permissions) && !in_array('category_view',$this->permissions))
                {
                    $response = [
                        "status" => "error",
                        "code"=> 403,
                        "message" => "You don't have authorization to view content.",
                        "data" => null,
                        "page" => null
                    ];
                    header("Content-Type: application/json");
                    echo json_encode($response);
                    exit();
                }
                $media_type = clean_alpha_text($this->input->get('type'));
                $this->get_by_type($media_type);
            }
        }
        elseif($param_1 == "share")
        {
            $task = $param_2;
            $pvt_link = $param_3;
            if($task == "get")
            {
                if(strlen($pvt_link) == 32 && preg_match('/[a-z0-9]+/', $pvt_link))
                {
                    if($info = $this->get_by_shareid($pvt_link))
                    {
                        if($info['level'] == 1)
                        {
                            $urn_data['main'] = $info;
                            $this->display_subcat_page($urn_data,$pvt_link);
                        }
                        elseif($info['level'] == 2)
                        {
                            if(!$urn_data['main'] = $this->get_by_id($info['parent_id']))
                            {
                                show_404(); exit();
                            }

                            $urn_data['sub'] = $info;
                            $urn_data['page'] = 1;
                            $this->display_media_page($urn_data,$pvt_link);
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
                if(!in_array('all',$this->permissions) && !in_array('category_edit',$this->permissions))
                {
                    header("Content-Type: application/json");
                    echo json_encode($response);
                    exit();
                }
                $this->share();
            }
        }
        else
        {
            $main_urn = strtolower($this->uri->segment(2));
            $subc_urn = strtolower($this->uri->segment(3));
            $type_urn = strtolower($this->uri->segment(4));
            $page_num = preg_match('/^[0-9]+$/', $this->uri->segment(5))? $this->uri->segment(5) : 1;
            $share_id = clean_alphanum_hash($this->input->get('share_id'));
            $urn_data = [];

            if(preg_match('/^([a-zA-z0-9\-\_]+)-([0-9]+)$/', $main_urn, $main_match)) // Main category
            {  // If $main_urn parameter follows "main-category-name-1" pattern.
               // i.e. http://domain.com/categories/main-category-name-1
               // Extract from URI name and id.

                $share_id_exist = false;

                if(strlen($share_id) == 32)
                {
                    $check_data =  $this->get_by_shareid($share_id);

                    if(!$check_data)
                    {
                        if(!$urn_data['main'] = $this->get_by_id($main_match[2]))
                        {
                            show_404(); exit();
                        }
                    }
                    elseif($check_data['level'] == 2)
                    {
                        $urn_data['sub'] = $check_data;
                        $share_id_exist = true;
                        if(!$urn_data['main'] = $this->get_by_id($main_match[2]))
                        {
                            show_404(); exit();
                        }
                    }
                    else
                    {
                        $urn_data['main'] = $check_data;
                        if($check_data['id'] == $main_match[2]) $share_id_exist = true;
                    }
                }
                else
                {
                    if(!$urn_data['main'] = $this->get_by_id($main_match[2]))
                    {
                        show_404(); exit();
                    }
                }

                if(preg_match('/^([a-zA-z0-9\-\_]+)-([0-9]+)$/', $subc_urn, $subc_match)) // Sub category
                {  // If $subc_urn parameter follows "sub-category-name-1" pattern.
                   // i.e. http://domain.com/categories/main-category-name-1/sub-category-name-2

                    if(preg_match('/^([a-zA-z]+)$/', $type_urn, $type_match)) // photos or videos
                    { // If $type_urn parameter follows "abcde" pattern.
                      // i.e. http://domain.com/categories/main-1/sub-2/photos

                        if(!in_array($type_match[0], $allowed_media_types))
                        {
                            $media_type = $allowed_media_types[0];
                        }
                        else 
                        { $media_type = $type_match[0]; }
                    }
                    else
                    {
                        $media_type = $allowed_media_types[0];
                    }

                    if(isset($urn_data['sub']))
                    {
                        if($subc_match[2] !== $urn_data['sub']['id'])
                        {
                            if(!$urn_data['sub'] = $this->get_by_id($subc_match[2]))
                            {
                                show_404(); exit();
                            }
                            elseif($urn_data['sub']['level'] != 2)
                            {
                                show_404(); exit();
                            }
                        }
                    }
                    else
                    {
                        if(!$urn_data['sub'] = $this->get_by_id($subc_match[2]))
                        {
                            show_404(); exit();
                        }
                    }

                    $urn_data['page'] = $page_num;

                    if($share_id_exist)
                    {
                        $this->display_media_page($urn_data,$share_id);
                    }
                    else
                    {
                        $this->display_media_page($urn_data);
                    }
                }
                elseif($share_id_exist)
                {
                    $this->display_subcat_page($urn_data,$share_id);
                }
                else
                {
                    $this->display_subcat_page($urn_data);
                }
            }
            else
            {
                $this->display_maincat_page();
            }
        }
    }

    /**
    * Renders HTML page with main category thumbnails.
    * @return  void  Flush HTML buffer to browser.
    *
    */
    private function display_maincat_page()
    {
        $type = 'photos'; // Default search type.
        // Page meta information.
        $data['page_title'] = "Categories";
        $data['meta_description'] = "All media categories.";
        $data['meta_keywords'] = "";
        // Account actions menu
        $data['account_actions'] = $this->load->view('common/v_menu_account_actions',null,true);
        // Build breadcrumbs html markups.
        $crumbs = ['Categories'=>""];
        $data['breadcrumbs'] = $this->load->view('common/v_breadcrumbs_frontend',['crumbs'=>$crumbs],true);
        // Build search wdiget html markups.
        $data['search_widget'] = $this->load->view('common/v_search_widget',['type'=>$type],true);
        // Build pagination html markups.
        $pagination_data = [
            'type' => $type,
            'keywords' => "",
            'category_id' => "",
            'category_name' => "",
            'current_page' => 1,
            'total_page' => 1,
            'prev_disabled' => true,
            'next_disabled' => true
        ];
        $data['pagination'] = $this->load->view('common/v_pagination_widget',$pagination_data,true);
        // Build category thumbs html markups.
        $data['category_thumbs'] = "";
        $category_sql  = "SELECT * FROM `categories` WHERE type='all' AND `level`=1 AND `published`='yes'";
        $category_sql .= isset($_SESSION['user']['id'])? " AND (`share_level`='public' OR `share_level` LIKE '%[".$_SESSION['user']['id']."]%')" : " AND `share_level`='public'";
        $category_sql .= " ORDER BY `title` ASC";
        $categories  = ($this->db->query($category_sql))->result_array();
        // Loop category items for html text.
        foreach($categories as $item)
        {
            $sef_title = preg_replace('/\s/','-',strtolower($item['title'])).'-'.$item['id'];
            $icon_url = empty(trim($item['icon']))? "" : empty(parse_url($item['icon'], PHP_URL_SCHEME))? base_url($item['icon']) : $item['icon'];
            $item_info = [
                'icon' => $icon_url,
                'title' => ucwords($item['title']),
                'link' => base_url("categories/{$sef_title}")
            ];
            $data['category_thumbs'] .= $this->load->view('common/v_category_main_thumb_frontend',$item_info,true);
        }
        $data['category_thumbs'] = compress_html($data['category_thumbs']);
        $this->load->view("v_results_layout",$data);
    }

    /**
    * Displays a page of subcategories under the given main catagory data.
    * @param  array  $main_url_data  Array containing id and title.
    * @return void                   Flush HTML buffer to browser.
    *
    */
    private function display_subcat_page($urn_data,$pvt_link=false)
    {
        // Prepare variables.
        $type = "photos"; // default media type.
        $share_id = $pvt_link? "?share_id={$pvt_link}" : "";
        $visibility = isset($_SESSION['user']['id'])? "(`share_level`='public' OR `share_level` LIKE '%[".$_SESSION['user']['id']."]%')" : "`share_level`='public'";
        $main_category  = $urn_data['main'];
        $sef_main_title = preg_replace('/\s/','-',strtolower($main_category['title'])).'-'.$main_category['id'];
        $sub_categories = [];

        $subc_sql = "SELECT * FROM `categories` WHERE `level`=2 AND `parent_id`={$main_category['id']} AND `published`='yes' AND {$visibility} ORDER BY `title` ASC";
        $sub_categories = ($this->db->query($subc_sql))->result_array();

        // Page meta information.
        $data['page_title'] = $main_category['title'];
        $data['meta_description'] = $main_category['description'];
        $data['meta_keywords'] = "";
        // Account actions menu
        $data['account_actions'] = $this->load->view('common/v_menu_account_actions',null,true);
        // Build breadcrumbs html markups.
        // $main_link = preg_replace('/\s/','-',strtolower($main_category['title'])).'-'.$main_category['id'];
        $crumbs = ['Categories'=>base_url('categories'), ucwords($main_category['title'])=>""];
        $data['breadcrumbs'] = $this->load->view('common/v_breadcrumbs_frontend',['crumbs'=>$crumbs],true);
        // Build search wdiget html markups.
        $data['search_widget'] = $this->load->view('common/v_search_widget',['type'=>$type],true);
        // Build pagination html markups.
        $pagination_data = [
            'type' => $type,
            'keywords' => "",
            'category_id' => "",
            'category_name' => "",
            'current_page' => 1,
            'total_page' => 1,
            'prev_disabled' => true,
            'next_disabled' => true
        ];
        $data['pagination'] = $this->load->view('common/v_pagination_widget',$pagination_data,true);
        $data['category_thumbs'] = "";
        $items_merge_data = [];

        foreach($sub_categories as $sub_category)
        {
            $sef_title = preg_replace('/\s/','-',strtolower($sub_category['title'])).'-'.$sub_category['id'];
            $title = preg_replace(['/ /','/\n/'], ['_',''], $sub_category['title']);
            $type = $sub_category['type'].'s';
            if(!isset($items_merge_data[$title])) $items_merge_data[$title] = ['title'=>ucwords($sub_category['title']),'icon'=>'','medias'=>[]];
            if(!empty($sub_category['icon_default']))
            {
                $items_merge_data[$title]['icon'] = empty(trim($sub_category['icon']))? "" : empty(parse_url($sub_category['icon'], PHP_URL_SCHEME))? base_url($sub_category['icon']) : $sub_category['icon'];
            }
            $items_merge_data[$title]['medias'][$type] = [
                'title' => ucwords($type),
                'link'  => base_url("categories/{$sef_main_title}/{$sef_title}/{$type}/{$share_id}")
            ];
        }
        foreach ($items_merge_data as $key => $value) {
            $data['category_thumbs'] .= $this->load->view('common/v_category_sub_thumb_frontend',$value,true);
        }

        $data['category_thumbs'] = compress_html($data['category_thumbs']);
        $this->load->view("v_results_layout",$data);
    }

    /**
    * Renders either JSON or HTML formatted response for items found under
    * specific subcategory. HTML output through display_items_page function.
    * @param  array         $urn_data  Main and sub category data.
    * @param  string        $share_id  Private share id.
    * @return array|string             Respose array for JSON string for HTML.
    *
    */
    private function display_media_page($urn_data,$share_id=false)
    {
        // Main variables.
        $main_category  = $urn_data['main'];
        $sub_category   = $urn_data['sub'];
        $type           = $sub_category['type'].'s';
        $page           = $urn_data['page']-1;
        $limit          = clean_numeric_text($this->input->get('l'));
        $limit          = empty($limit)? 20 : $limit;
        $offset         = $page * $limit;
        $mode           = $this->input->get("m");
        $pvt_link       = $share_id? "?share_id={$share_id}" : "";
        $sef_main_title = preg_replace('/\s/','-',strtolower($main_category['title'])).'-'.$main_category['id'];
        $sef_sub_title  = preg_replace('/\s/','-',strtolower($sub_category['title'])).'-'.$sub_category['id'];

        # Breadrumbs data.
        $crumbs = ['Categories' => base_url('categories'), ucwords($main_category['title']) => base_url("categories/{$sef_main_title}{$pvt_link}"), ucwords($sub_category['title']) => ""];
        # Media entries data.
        $get_items_sql   = "SELECT * FROM `{$type}` WHERE `category_id`={$sub_category['id']}";
        $count_items_sql = "SELECT count(`id`) as `total` FROM `{$type}` WHERE `category_id`={$sub_category['id']}";
        $get_where_sql   = "";
        # Apply visibility logic.
        if($type == "videos") $get_where_sql .= " AND `complete`=1";
        $visibility = isset($_SESSION['user']['id'])? "(`share_level`='public' OR `share_level` LIKE '%[".$_SESSION['user']['id']."]%')" : "`share_level`='public'";
        $get_where_sql  .= " AND {$visibility}";

        $get_limit_sql   = " ORDER BY `title` ASC LIMIT {$limit} OFFSET {$offset}";
        $tmp_items_count = $this->db->query($count_items_sql.$get_where_sql);
        $tmp_items_query = $this->db->query($get_items_sql.$get_where_sql.$get_limit_sql);
        $items_total     = $tmp_items_count->result_array()[0]['total'];
        $items_data      = $tmp_items_query->result_array();

        // Main response data.
        $response = [
            'type' => $type,
            'keywords' => "",
            'category_id' => $sub_category['id'],
            'category_name' => $sub_category['title'],
            'main_category_id' => $main_category['id'],
            'main_category_name' => $main_category['title'],
            'crumbs' => $crumbs,
            'route' => 'categories',
            'page' => [
                'current' => $page+1,
                'total' => ceil($items_total / $limit),
                'limit' => $limit,
                'share_id' => $share_id
            ],
            'items' => [
                'type' => $type,
                'entries' => $items_data,
                'total' => $items_total
            ]
        ];
        $page_meta = [
            'title' => $sub_category['title'],
            'description' => $sub_category['description'],
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
            $this->display_items_page($response,$page_meta);
        }
    }

    /**
    * Renders HTML page output in results view layout for all items found under
    * specific subcategory.
    * @param  array   $response   Result set containing the following keys:
    *                             type, keywords, category_name, category_id,
    *                             crumbs, route, page, & items.
    * @param  array   $page_meta  Set of information for page SEO metadata
    *                             containing keys title, description & keywords.
    * @return string              HTML output.
    *
    */
    private function display_items_page($response,$page_meta)
    {
        # Variables: Page meta information.
        $data['page_title'] = $page_meta['title'];
        $data['meta_description'] = $page_meta['description'];
        $data['meta_keywords'] = "";
        # String: HTML for breadcrumbs.
        $data['breadcrumbs'] = $this->load->view('common/v_breadcrumbs_frontend',['crumbs'=>$response['crumbs']],true);
        # String: HTML for search widget.
        $data['search_widget'] = $this->load->view('common/v_search_widget',['type'=>$response['type']],true);
        # Variables: Pagination widget.
        $type = $response['type'];
        $sef_link  = $response['main_category_name'].'-'.$response['main_category_id'].'/';
        $sef_link .= $response['category_name'].'-'.$response['category_id'];
        if($response['page']['current'] > 1)
        {
            $page_num      = $response['page']['current']-1;
            $prev_link     = base_url("categories/{$type}/{$sef_link}/{$page_num}");
            $prev_disabled = false;
        }
        else
        {
            $page_num      = 1;
            $prev_link     = base_url("categories/{$type}/{$sef_link}/{$page_num}");
            $prev_disabled = true;
        }
        if($response['page']['current'] < $response['page']['total'])
        {
            $npage_num     = $response['page']['current']+1;
            $next_link     = base_url("categories/{$type}/{$sef_link}/{$npage_num}");
            $next_disabled = false;
        }
        else
        {
            $npage_num     = $response['page']['total'];
            $next_link     = base_url("categories/{$type}/{$sef_link}/{$npage_num}");
            $next_disabled = true;
        }
        $pagination_data = [
            'type' => $type,
            'prev_link' => $prev_link,
            'next_link' => $next_link,
            'current_page' => $response['page']['current'],
            'total_page' => $response['page']['total'],
            'prev_disabled' => $prev_disabled,
            'next_disabled' => $next_disabled
        ];
        // Account actions menu
        $data['account_actions'] = $this->load->view('common/v_menu_account_actions',null,true);
        # String: Pagination widget.
        $data['pagination'] = $this->load->view('common/v_pagination_widget_categories',$pagination_data,true);
        # String: HTML for item thumbnails.
        $data['thumbs'] = "";
        $query_str = "";
        if(strlen($response['page']['share_id']) == 32) $query_str = "?share_id={$response['page']['share_id']}";
        if(count($response['items']['entries']) > 0)
        {
            foreach($response['items']['entries'] as $item)
            {
                $thumb_data['data'] = json_encode($item);
                $thumb_data['title'] = $item['title'];
                $thumb_data['uid'] = $item['uid'];
                $thumb_data['seo_title'] = preg_replace('/\s/', '-', $item['title']).'-'.$item['uid'];
                $thumb_data['query_str'] = $query_str;
                $data['thumbs'] .= $this->load->view("common/v_result_thumbs_{$type}",$thumb_data,true);
            }
            $data['thumbs'] = compress_html($data['thumbs']);
        }
        else
        {
            $data['thumbs'] = $this->load->view('common/v_result_alert',['message'=>'No items.'],true);
        }
        # Share id.
        if(isset($response['page']['share_id'])) $data['share_id'] = $response['page']['share_id'];
        # String: JS for results app to init.
        $data['result_js_init'] = $this->load->view('scripts/v_scripts_results',['result'=>$response],true);
        $this->load->view("v_results_layout",$data);
    }

    /**
    * Inserts new category record.
    * @return  string  JSON text response.
    *
    */
    private function add()
    {
        // Required params via post.
        $level = clean_numeric_text(trim($this->input->post('level')));
        $type = ($level == 1)? "all" : rtrim(clean_alpha_text(trim($this->input->post('type'))),'s'); // Strips 's' at the end.
        $level_type = ($level == 1)? rtrim(clean_alpha_text(trim($this->input->post('type'))),'s') : $type;
        $title = clean_title_text(trim($this->input->post('title')));
        $description = clean_body_text(trim($this->input->post('description')));
        $icon = trim($this->input->post('icon'));
        $icon_default = trim($this->input->post('icon_default'));
        $publish = clean_alpha_text(trim($this->input->post('publish')));
        $parent_id = clean_numeric_text(trim($this->input->post('parent_id')));

        $errors = 0;
        $response = [
            "status" => "error",
            "message" => "Unknown error has occured.",
            "data" => "null"
        ];
        // Process data.
        if(strlen($title) == 0)
        {
            $errors++;
            $response['message'] = "Title field is missing. ";
        }
        if(strlen($publish) == 0)
        {
            $publish = "yes";
        }
        // Check for duplicate entry.
        $sql_check = "SELECT `type`,`title` FROM `categories` WHERE `level`={$level} AND `type`='{$type}' AND `title`='{$title}'";
        $found = ($this->db->query($sql_check))->num_rows();
        if($found > 0)
        {
            $errors++;
            $response['message'] = "Category title already exist.";
        }
        else
        {
            $fields = [
                'level'=>$level,
                'type'=>$type,
                'title'=>$title,
                'icon'=>$icon,
                'icon_default'=>$icon_default,
                'description'=>$description,
                'parent_id'=>$parent_id,
                'published'=>'yes',
                'share_level'=>'public'
            ];
            if(!$this->m_category->add($fields))
            {
                $errors++;
                $response['message'] .= "Database insert failed. ";
            }
            if(!$data = $this->m_category->get_all($level_type))
            {
                $errors++;
                $response['message'] .= "Data fetch failed. ";
            }
        }

        // Check for errors before output.
        if($errors == 0)
        {
            $response['status'] = "ok";
            $response['message'] = "New entry added.";
            $response['data'] = $data;
        }

        // Generate output.
        header("Content-Type: application/json");
        echo json_encode($response);
    }

    /**
    * Update existing category record.
    * @return string  JSON text response.
    *
    */
    private function update()
    {
        // Required fields.
        $id = clean_numeric_text(trim($this->input->post('id')));
        $type = rtrim(clean_alpha_text(trim($this->input->post('type'))),'s'); // Strips 's' at the end.
        $level = clean_numeric_text($this->input->post('level'));
        $level_type = ($level == 1)? $type : $type;
        $title = clean_title_text(trim($this->input->post('title')));
        $description = clean_body_text(trim($this->input->post('description')));
        $icon = trim($this->input->post('icon'));
        $icon_default = trim($this->input->post('icon_default'));
        $parent_id = clean_numeric_text(trim($this->input->post('parent_id')));

        // Prepare valirables.
        $errors = 0;
        $fields = [
            'level'=>$level,
            'type'=>$type,
            'title'=>$title,
            'icon'=>$icon,
            'icon_default'=>$icon_default,
            'description'=>$description,
            'parent_id'=>$parent_id
        ];
        $response = [
            "status" => "error",
            "message" => "Unknown error has occured.",
            "data" => "null"
        ];

        // Process data.
        if(strlen($id) == 0)
        {
            $errors++;
            $response['message'] = "ID field is missing. ";
        }
        if(strlen($title) == 0)
        {
            $errors++;
            $response['message'] .= "Title field is missing. ";
        }

        // Check for duplicate entry.
        $sql_check = "SELECT `id`,`type`,`title` FROM `categories` WHERE `level`={$level} AND `type`='{$type}' AND `title`='{$title}'";
        $rows_found = ($this->db->query($sql_check))->result_array();

        if(count($rows_found) > 0)
        {
            $info = $rows_found[0];

            if($id != $info['id'])
            {
                $errors++;
                $response['message'] = "Category title already exist.";
            }
            else
            {
                // Clear all having same title to avoid duplicate if icon is set as default.
                if($icon_default == 1)
                {
                    $sql_clear_default = "UPDATE `categories` SET `icon_default`=0 WHERE `title`='{$title}' AND `icon_default`=1";
                    $this->db->query($sql_clear_default);
                }
                if(!$this->m_category->update($fields,$id))
                {
                    $errors++;
                    $response['message'] .= "Database update failed. ";
                }
                if(!$data = $this->m_category->get_all($level_type))
                {
                    $errors++;
                    $response['message'] .= "Data fetch failed. ";
                }
            }
        }
        else
        {
            if(!$this->m_category->update($fields,$id))
            {
                $errors++;
                $response['message'] .= "Database update failed. ";
            }
            if(!$data = $this->m_category->get_all($level_type))
            {
                $errors++;
                $response['message'] .= "Data fetch failed. ";
            }
        }

        // Check for errors before output.
        if($errors == 0)
        {
            $response['status'] = "ok";
            $response['message'] = "Entry updated.";
            $response['data'] = $data;
            $response['debug_info'] = $level_type;
        }
        else
        {
            $response['debug_info'] = "Error count: $errors";
        }

        // Generate output.
        header("Content-Type: application/json");
        echo json_encode($response);
    }
    /**
    * Function description.
    * @param   type  $variable  Variable description.
    * @return  type             Return value description.
    *
    */
    private function delete()
    {
        $errors = 0;
        $response = [
            "status" => "error",
            "message" => "Unknown error has occured. ",
            "data" => "null"
        ];
        $accept_media_type_ids = ["photo","video"];

        // Required fields.
        $media_type = clean_alpha_text($this->input->post('type'));
        $media_type_id = rtrim($media_type,'s');
        $item_id = $this->input->post('id');

        // Check media type is accurate.
        if(in_array($media_type_id,$accept_media_type_ids))
        {
            $ids = [];
            if(is_array($item_id))
            {
                foreach($item_id as $row)
                {
                    $entry = clean_numeric_text($row);
                    if(strlen($entry) == 0)
                    {
                        $errors++;
                        break;
                    }
                    elseif($row == 1)
                    {
                        $errors++;
                        $response['message'] = "Cannot delete default item. ";
                        break;
                    }
                    else
                    {
                        $ids[] = $entry;
                    }
                }
                if(count($ids) > 0)
                {
                    $item_id = $ids;
                }
                else
                {
                    $item_id = "";
                }
            }
            else {
                $item_id = clean_numeric_text($this->input->post('id'));
            }
            // Ensure $item_id is not empty.
            if(!is_array($item_id))
            {
                if(strlen($item_id) == 0)
                {
                    $errors++;
                    $response['message'] = "ID field is invalid. ";
                }
            }
            // Delete action:
            // Deletes all associated subcategories.
            // Move all associated media to category 1 (uncategorized).
            if(!$deleted_rows = $this->m_category->delete($item_id,$media_type_id))
            {
                $errors++;
                $response['message'] .= "Row delete failed. ";
            }
            // Get latest record after delete.
            if(!$data = $this->m_category->get_all($media_type_id))
            {
                $errors++;
                $response['message'] .= "Data fetch failed. ";
            }
        }
        else
        {
            $response['message'] .= "Unsupported media id : {$media_type_id}. ";
            $errors++;
        }
        // Check for errors before output.
        if($errors == 0)
        {
            $response['status'] = "ok";
            $response['message'] = "{$deleted_rows} category item(s) deleted.";
            $response['data'] = $data;
        }

        // Generate output.
        header("Content-Type: application/json");
        echo json_encode($response);
    }
    /**
    *
    */
    private function share()
    {
        $response = [
            "status" => "error",
            "code" => 500,
            "message" => "Invalid ids."
        ];

        $id = clean_numeric_text($this->input->post('id'));
        $sub_ids = explode(',', $this->input->post('sub_ids'));
        $clean_sub_ids = [];
        $level = $this->input->post('level');
        $share_level = $this->input->post('share_level');
        $pvt_share_id = clean_alphanum_hash($this->input->post('pvt_share_id'));
        $user_ids = $user_ids = explode(',', $this->input->post('user_ids'));
        $errors = 0;
        $parent_level = ($level==1)? "mc_share_level" : "sc_share_level";
        
        foreach ($sub_ids as $sub_id) {
            $clean_sub_id = clean_numeric_text($sub_id);
            if(strlen($clean_sub_id) > 0) $clean_sub_ids[] = $clean_sub_id;
        }
        if(strlen($id) > 0)
        {
            if($share_level == 'private' || $share_level == 'public')
            {
                $share_level = clean_alpha_text($share_level);
            }
            elseif($share_level == 'protected')
            {
                $clean_ids = [];
                foreach ($user_ids as $user_id) {
                    $clean_id = clean_numeric_text($user_id);
                    if(strlen($clean_id) > 0)
                    {
                        $clean_ids[] = '['.$clean_id.']';
                    }
                }
                if(count($clean_ids) > 0)
                {
                    $share_level =implode(',', $clean_ids);
                }
                else
                {
                    $share_level = 'private';
                }
            } else { $errors++; }

            if($errors == 0)
            {
                if($level == 1)
                {
                    $set_main_sql  = "UPDATE `categories` ";
                    $set_main_sql .= "SET `categories`.`share_level`='{$share_level}',`categories`.`pvt_share_id`='{$pvt_share_id}'";
                    $set_main_sql .= " WHERE `categories`.`id`={$id}";

                    if(count($clean_sub_ids) > 0)
                    {
                        $clean_sub_ids = implode(',', $clean_sub_ids);
                        $set_sub_sql  = "UPDATE `categories` ";
                        $set_sub_sql .= "LEFT JOIN `photos` ON `categories`.`id`=`photos`.`category_id` ";
                        $set_sub_sql .= "LEFT JOIN `videos` ON `categories`.`id`=`videos`.`category_id` ";
                        $set_sub_sql .= "SET `photos`.`mc_share_level`='{$share_level}',`videos`.`mc_share_level`='{$share_level}'";
                        $set_sub_sql .= " WHERE `categories`.`id` IN ({$clean_sub_ids})";
                    }
                }
                elseif($level == 2)
                {
                    $set_main_sql  = "UPDATE `categories` ";
                    $set_main_sql .= "LEFT JOIN `photos` ON `categories`.`id`=`photos`.`category_id` ";
                    $set_main_sql .= "LEFT JOIN `videos` ON `categories`.`id`=`videos`.`category_id` ";
                    $set_main_sql .= "SET `categories`.`share_level`='{$share_level}',`categories`.`pvt_share_id`='{$pvt_share_id}'";
                    $set_main_sql .= ",`photos`.`sc_share_level`='{$share_level}',`videos`.`sc_share_level`='{$share_level}'";
                    $set_main_sql .= " WHERE `categories`.`id`={$id}";
                }

                if($this->db->query($set_main_sql))
                {
                    $response['status'] = "ok";
                    $response['code'] = 200;
                    $response['message'] = "Target category updated.";

                    if(isset($set_sub_sql))
                    {
                        if($this->db->query($set_sub_sql))
                        {
                            $response['message'] .= " Associated media for subcats updated.";
                        }
                        else
                        {
                            $response['status'] = "error";
                            $response['code'] = 500;
                            $response['message'] .= " Associated media for subcats updated failed.";
                        }
                    }
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

    /**
    * Function description.
    * @param  type  $variable  Variable description.
    * @return type             Return value description.
    *
    */
    private function get_by_type($type)
    {
        $type = rtrim($type,'s'); // Removes letter 's' at the end.
        $response = [
            "status" => "error",
            "message" => "Unknown error has occured.",
            "data" => null
        ];
        if($data = $this->m_category->get_all($type))
        {
            $response['status'] = "ok";
            $response['message'] = "Success.";
            $response['data'] = $data;
        }
        header("Content-Type: application/json");
        echo json_encode($response);
    }

    /**
    *
    */
    private function get_by_id($id,$no_visi=false)
    {
        $clean_id = clean_numeric_text($id);
        $clean_id = (strlen($clean_id) > 0)? $clean_id : 0;
        $visibility = isset($_SESSION['user']['id'])? "(`share_level`='public' OR `share_level` LIKE '%[".$_SESSION['user']['id']."]%')" : "`share_level`='public'";
        $item_sql = "SELECT * FROM `categories` WHERE `id`={$clean_id} AND {$visibility}";
        $item_res = ($this->db->query($item_sql))->result_array();

        if(count($item_res) > 0)
        {
            return $item_res[0];
        }
        else
        {
            return false;
        }
    }

    /**
    *
    */
    private function get_by_shareid($share_id)
    {
        $share_id = clean_alphanum_hash($share_id);
        $item_sql = "SELECT * FROM `categories` WHERE `share_level`='private' AND `pvt_share_id`='{$share_id}'";
        $item_res = ($this->db->query($item_sql))->result_array();

        if(count($item_res) > 0)
        {
            return $item_res[0];
        }
        else
        {
            return false;
        }
    }
}
