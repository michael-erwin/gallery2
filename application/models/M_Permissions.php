<?php
class M_Permissions extends MY_Model
{
    protected $table = "permissions";
    protected $columns = [
                "id" => "int(255) UNSIGNED AUTO_INCREMENT PRIMARY KEY",
                "name" => "varchar(32) NOT NULL",
                "description" => "text NOT NULL",
                "entity" => "varchar(25) NOT NULL",
                "date_added" => "bigint(255) UNSIGNED NOT NULL",
                "date_modified" => "bigint(255) UNSIGNED NOT NULL"
            ];

    function __construct()
    {
        parent::__construct();
    }
}