<?php
class M_Categories extends MY_Model
{
    protected $table = "categories";
    protected $media   = 'photo';
    protected $columns = [
                "id" => "int(255) UNSIGNED AUTO_INCREMENT PRIMARY KEY",
                "level" => "int(11) NOT NULL",
                "type" => "varchar(32) NOT NULL DEFAULT 'photo'",
                "title" => "varchar(32) NOT NULL",
                "icon" => "text NOT NULL",
                "icon_default" => "TINYINT(1) NOT NULL",
                "description" => "varchar(128) NOT NULL",
                "parent_id" => "int(255) NOT NULL",
                "published" => "enum('yes','no') NOT NULL DEFAULT 'yes'",
                "share_level" => "text NOT NULL",
                "pvt_share_id" => "varchar(32) NOT NULL",
                "core" => "enum('yes','no') NOT NULL DEFAULT 'no'",
                "date_added" => "bigint(255) UNSIGNED NOT NULL",
                "date_modified" => "bigint(255) UNSIGNED NOT NULL"
            ];

    function __construct()
    {
        parent::__construct();
    }

    /**
    * Function 'set_media_type' sets media type for which category entry to fetch.
    * This will result in fetching main category plus subcategory of particular type.
    * @param   string  $type  Name of media type under categories will be pulled.
    * @return  array          Array of rows.
    *
    */
    public function set_media_type($type)
    {
        $this->media = $type;
    }

    /**
    * Function 'create' creates a fresh table for this model in database with initial
    * core entry.
    * @return  boolean true on success, false on failure.
    *
    */
    public function create()
    {
        $table_create = MY_Model::create();

        if($table_create['code'] == 'SUCCESS')
        {
            $row_insert_fields = [
                "id" => 1,
                "level" => 1,
                "type" => "all",
                "title" => "Uncategorized",
                "icon" => "",
                "icon_default" => "",
                "description" => "System placeholder for un published items.",
                "published" => "no",
                "share_level" => "private",
                "parent_id" => 0,
                "core" => "yes"
            ];
            return $this->insert($row_insert_fields);
        }
        else
        {
            return $table_create;
        }
    }
}