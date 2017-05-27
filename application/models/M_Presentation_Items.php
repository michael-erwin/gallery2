<?php
class M_Presentation_Items extends MY_Model
{
    protected $table = "presentation_items";
    protected $columns = [
                "id" => "int(255) UNSIGNED AUTO_INCREMENT PRIMARY KEY",
                "uid" => "varchar(32) NOT NULL",
                "title" => "varchar(16) NOT NULL",
                "caption" => "varchar(64) NOT NULL",
                "width" => "int(255) UNSIGNED",
                "height" => "int(255) UNSIGNED",
                "file_size" => "int(255) UNSIGNED",
                "parent_id" => "bigint(255) NOT NULL",
                "date_added" => "bigint(255) UNSIGNED NOT NULL",
                "date_modified" => "bigint(255) UNSIGNED NOT NULL"
            ];

    function __construct()
    {
        parent::__construct();
    }
}