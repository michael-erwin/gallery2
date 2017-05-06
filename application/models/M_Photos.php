<?php
class M_Photos extends MY_Model
{
    protected $table = "photos";
    protected $columns = [
                "id" => "int(255) UNSIGNED AUTO_INCREMENT PRIMARY KEY",
                "category_id" => "int(11) NOT NULL",
                "title" => "varchar(64) NOT NULL",
                "description" => "text NOT NULL",
                "tags" => "text NOT NULL",
                "uid" => "varchar(32) NOT NULL UNIQUE KEY",
                "width" => "varchar(128) NOT NULL",
                "height" => "int(128) NOT NULL",
                "file_size" => "int(128) NOT NULL",
                "has_zip" => "tinyint(1) NOT NULL DEFAULT '0'",
                "checksum" => "varchar(32) NOT NULL",
                "share_level" => "text NOT NULL",
                "mc_share_level" => "text NOT NULL",
                "sc_share_level" => "text NOT NULL",
                "pvt_share_id" => "text NOT NULL",
                "date_added" => "bigint(255) UNSIGNED NOT NULL",
                "date_modified" => "bigint(255) UNSIGNED NOT NULL"
            ];

    function __construct()
    {
        parent::__construct();
    }

    /**
    * Function 'create' creates a fresh table for this model in database.
    * @return  boolean true on success, false on failure.
    *
    */
    public function create()
    {
        $table_create = MY_Model::create();

        if($table_create['code'] == 'SUCCESS')
        {
            @$this->db->query("ALTER TABLE `{$this->table}` ADD FULLTEXT KEY `text_search` (`title`,`description`,`tags`)");
        }
        return $table_create;
    }
}