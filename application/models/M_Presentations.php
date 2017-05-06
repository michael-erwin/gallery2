<?php
class M_Presentations extends MY_Model
{
    protected $table = "presentations";
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
}