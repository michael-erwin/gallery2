<?php
class Dashboard extends CI_Controller
{
    private $sidebar_menus, $page_title, $page_description, $breadcrumbs;
    private $permissions = [];

    function __construct()
    {
        parent::__construct();
        $this->permissions = $this->auth->get_permissions();
        if(!in_array('all', $this->permissions) && !in_array('admin_access', $this->permissions))
        {
            header("Location: ".base_url('/'));
            exit();
        }
        $this->page_title = "Dashboard";
        $this->page_description = "Site Overview";
        $this->breadcrumbs =
        [
            ["text"=>"Admin","link"=>base_url("admin/dashboard")],
            ["text"=>"Dashboard","link"=>""]
        ];
        $this->sidebar_menus = ["dashboard"];
    }

    public function index($option=null)
    {
        // Sidebar - User Panel.
        $data['sidebar_user_panel'] = $this->load->view('admin/v_sidebar_user_panel','',true);

        // Sidebar - Menu.
        $data['sidebar_menu'] = $this->load->view('admin/v_sidebar_menu','',true);

        // Content.
        $sql_photos = "SELECT COUNT(`id`) as 'count' FROM `photos`";
        $sql_videos = "SELECT COUNT(`id`) as 'count' FROM `videos`";
        $sql_rusers = "SELECT COUNT(`id`) as 'count' FROM `users`";
        $tmp_photos = $this->db->query($sql_photos);
        $tmp_videos = $this->db->query($sql_videos);
        $tmp_rusers = $this->db->query($sql_rusers);
        $count_photos = $tmp_photos->result_array()[0];
        $count_videos = $tmp_videos->result_array()[0];
        $count_rusers = $tmp_rusers->result_array()[0];
        $content_data = [
            'photos_total' => $count_photos['count'],
            'videos_total' => $count_videos['count'],
            'users_count' => $count_rusers['count'],
            'unique_visits' => 0
        ];
        $data['content'] = $this->load->view('admin/v_content_dashboard',$content_data,true);

        // JSON Data
        $data['json']['sidebar_menus'] = json_encode($this->sidebar_menus);
        $data['json']['title'] = "{\"text\":\"{$this->page_title}\",\"small\":\"{$this->page_description}\"}";
        $data['json']['breadcrumbs'] = json_encode($this->breadcrumbs);

        // JS Scripts
        //$data['js_scripts'] = $this->load->view('admin/scripts/v_scripts_dashboard','',true);

        // Page Template.
        $this->load->view('v_admin_layout',$data);
    }

    public function json($option=null)
    {
        $sql_photos = "SELECT COUNT(`id`) as 'count' FROM `photos`";
        $sql_videos = "SELECT COUNT(`id`) as 'count' FROM `videos`";
        $sql_rusers = "SELECT COUNT(`id`) as 'count' FROM `users`";
        $tmp_photos = $this->db->query($sql_photos);
        $tmp_videos = $this->db->query($sql_videos);
        $tmp_rusers = $this->db->query($sql_rusers);
        $count_photos = $tmp_photos->result_array()[0];
        $count_videos = $tmp_videos->result_array()[0];
        $count_rusers = $tmp_rusers->result_array()[0];
        $content_data = [
            'photos_total' => $count_photos['count'],
            'videos_total' => $count_videos['count'],
            'users_count' => $count_rusers['count'],
            'unique_visits' => 0
        ];
        $body = clean_whitespace($this->load->view('admin/v_content_dashboard',$content_data,true));

        if ($option)
        {
            if ($option == "page_title") $response = $this->page_title;
            if ($option == "page_description") $response = $this->page_description;
            if ($option == "breadcrumbs") $response = $this->breadcrumbs;
            if ($option == "content") $response = $body;
        }
        else
        {
            $response = [
                "sidebar_menus" => $this->sidebar_menus,
                "page_title" => $this->page_title,
                "page_description" => $this->page_description,
                "breadcrumbs" => $this->breadcrumbs,
                "content" => $body
            ];
        }
        header("Content-Type: application/json");
        echo json_encode($response);
    }

    public function js()
    {
        header("Content-Type: application/javascript");
        //$this->load->view('admin/scripts/v_scripts_*');
    }
}