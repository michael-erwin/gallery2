<?php
/**
* Main index page controller.
*/
class Index extends CI_Controller
{
    private $permissions;

    function __construct()
    {
        parent::__construct();
        $this->permissions = $this->auth->get_permissions();
    }

    public function index()
    {
        $data = [
            'account_actions' => $this->load->view('common/v_menu_account_actions',null,true),
            'backdrop_photo' => "",
            'category_thumbs' => ""
        ];

        // Get backdrop for CTA block.
        $folder = 'assets/img/backdrops/';
        $files = scandir('assets/img/backdrops/');
        $photos = [];
        foreach($files as $file)
        {
            if(is_file($folder.$file))
            {
                $photos[] = $file;
            }
        }
        $i = rand(0,count($photos)-1);
        $data['backdrop_photo'] = base_url($folder.$photos[$i]);

        // Get category for thumb listing.
        $sql = "SELECT * FROM `categories` WHERE type='all' AND `level`=1 AND `published`='yes' ORDER BY `title` ASC";
        $query = $this->db->query($sql); $categories = $query->result_array();
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

        $this->load->view('v_index_layout',$data);
    }
}
