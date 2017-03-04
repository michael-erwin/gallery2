<?php
class Roles extends CI_Controller
{
    private $sidebar_menus, $page_title, $page_description, $breadcrumbs,
            $permissions = [];

    function __construct()
    {
        parent::__construct();
        $this->page_title = "Roles";
        $this->page_description = "Manage User Roles";
        $this->breadcrumbs =
        [
            ["text"=>"Admin","link"=>base_url("admin/dashboard")],
            ["text"=>"Roles","link"=>""]
        ];
        $this->sidebar_menus = ["roles"];
    }

    public function index($option=null)
    {
        // Permissions.
        $data['permissions'] = json_encode($this->permissions);
        
        // Sidebar - User Panel.
        $data['sidebar_user_panel'] = $this->load->view('admin/v_sidebar_user_panel','',true);

        // Sidebar - Menu.
        $data['sidebar_menu'] = $this->load->view('admin/v_sidebar_menu','',true);

        // Content.
        $data['content'] = $this->load->view('admin/v_content_roles','',true);

        // JSON Data
        $data['json']['sidebar_menus'] = json_encode($this->sidebar_menus);
        $data['json']['title'] = "{\"text\":\"{$this->page_title}\",\"small\":\"{$this->page_description}\"}";
        $data['json']['breadcrumbs'] = json_encode($this->breadcrumbs);

        // Page objects.
        $data['objects'] = $this->load->view('admin/v_object_role_editor','',true);

        // JS Scripts.
        $data['js_scripts'] = $this->load->view('admin/scripts/v_scripts_roles','',true);

        // Page Template.
        $this->load->view('v_admin_layout',$data);
    }

    public function json($option=null)
    {
        $body = clean_whitespace($this->load->view('admin/v_content_roles','',true));
        $objects = clean_whitespace($this->load->view('admin/v_object_role_editor','',true));

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
                "content" => $body,
                "objects" => $objects
            ];
        }
        header("Content-Type: application/json");
        echo json_encode($response);
    }

    public function js()
    {
        header("Content-Type: application/javascript");
        $this->load->view('admin/scripts/v_scripts_roles');
    }
}
