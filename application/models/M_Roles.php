<?php
class M_Roles extends MY_Model
{
    protected $table = "roles";
    protected $columns = [
                "id" => "int(255) UNSIGNED AUTO_INCREMENT PRIMARY KEY",
                "name" => "varchar(32) NOT NULL",
                "description" => "text NOT NULL",
                "permissions" => "text NOT NULL",
                "core" => "enum('yes','no') NOT NULL DEFAULT 'no'",
                "date_added" => "bigint(255) UNSIGNED NOT NULL",
                "date_modified" => "bigint(255) UNSIGNED NOT NULL"
            ];

    function __construct()
    {
        parent::__construct();
    }
}