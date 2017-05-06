<?php
class M_Favorites extends MY_Model
{
    protected $table = "favorites";
    protected $columns = [
                "id" => "int(255) UNSIGNED AUTO_INCREMENT PRIMARY KEY",
                "user_id" => "int(11) NOT NULL",
                "media_id" => "int(255) NOT NULL",
                "published" => "enum('yes','no') NOT NULL DEFAULT 'yes'",
                "date_added" => "bigint(255) UNSIGNED NOT NULL",
                "date_modified" => "bigint(255) UNSIGNED NOT NULL"
            ];

    function __construct()
    {
        parent::__construct();
    }
}