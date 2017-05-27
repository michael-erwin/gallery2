<?php
class Presentation extends CI_Controller
{
    private $sidebar_menus, $page_title, $page_description, $breadcrumbs;
    private $permissions = [];

    function __construct()
    {
        parent::__construct();
        $this->permissions = $this->auth->get_permissions();
        if(!in_array('all', $this->permissions) && !in_array('admin_access', $this->permissions))
        {
            $query_strings = '?redir='.base_url(uri_string());
            $query_strings .= '&auth_error=Please login using authorized account to access page.';
            header("Location: ".base_url('account/signin').$query_strings);
            exit();
        }
        $this->page_title = "Presentation";
        $this->page_description = "Gallery";
        $this->breadcrumbs =
        [
            ["text"=>"Admin","link"=>base_url("admin/dashboard")],
            ["text"=>"Presentation","link"=>""]
        ];
        $this->sidebar_menus = ["media","presentation"];
    }

    public function index()
    {
        // Permissions.
        $data['permissions'] = json_encode($this->permissions);

        // Sidebar - User Panel.
        $data['sidebar_user_panel'] = $this->load->view('admin/v_sidebar_user_panel','',true);

        // Sidebar - Menu.
        $data['sidebar_menu'] = $this->load->view('admin/v_sidebar_menu','',true);

        // Content.
        $data['content'] = $this->load->view('admin/v_content_presentation','',true);

        // JSON Data used by js to initialize contents.
        $data['json']['sidebar_menus'] = json_encode($this->sidebar_menus);
        $data['json']['title'] = "{\"text\":\"{$this->page_title}\",\"small\":\"{$this->page_description}\"}";
        $data['json']['breadcrumbs'] = json_encode($this->breadcrumbs);

        // Page objects.
        $data['objects']  = $this->load->view('admin/v_object_presentation_entry_editor','',true);
        $data['objects'] .= $this->load->view('admin/v_object_presentation_items_editor','',true);
        $data['objects'] .= $this->load->view('admin/v_object_visibility_editor','',true);

        // JS Scripts.
        $data['js_scripts'] = $this->load->view('admin/scripts/v_scripts_media_presentation','',true);

        // Page Template.
        $this->load->view('v_admin_layout',$data);
    }

    public function json($option=null)
    {
        $body = clean_whitespace($this->load->view('admin/v_content_presentation','',true));
        // Page objects.
        $objects  = $this->load->view('admin/v_object_presentation_entry_editor','',true);
        $objects .= $this->load->view('admin/v_object_presentation_items_editor','',true);
        $objects .= $this->load->view('admin/v_object_visibility_editor','',true);

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
        $this->load->view('admin/scripts/v_scripts_media_presentation');
    }
}
