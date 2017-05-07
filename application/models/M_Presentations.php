<?php
class M_Presentations extends MY_Model
{
    protected $table = "presentations";
    protected $columns = [
                "id" => "int(255) UNSIGNED AUTO_INCREMENT PRIMARY KEY",
                "title" => "varchar(32) NOT NULL",
                "description" => "varchar(128) NOT NULL",
                "items" => "text NOT NULL",
                "share_level" => "text NOT NULL",
                "pvt_share_id" => "varchar(32",
                "date_added" => "bigint(255) UNSIGNED NOT NULL",
                "date_modified" => "bigint(255) UNSIGNED NOT NULL"
            ];

    function __construct()
    {
        parent::__construct();
    }
}