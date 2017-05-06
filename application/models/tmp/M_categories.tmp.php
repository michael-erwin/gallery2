<?php
/**
* CI model for categories under Media Gallery project.
*
* @package  Media Gallery
* @author   Michael Erwin Virgines <michael.erwinp@gmail.com>
*
*/
class M_categories extends CI_Model
{
    private $table   = 'categories'; // Default name of table for this model.
    private $media   = 'photo';
    private $columns = [
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
    // This will be the standard response format. Put in place in order to
    // capture all possible errors.
    private $result  = [
                'code'    => 'ERROR',    // ERROR or SUCCESS
                'message' => "",         // Custom error or MySQL error.
                'items'   => null,       // Rows returned.
                'info'    => [
                    'affected'  => 0,    // For affected rows.
                    'insert_id' => null  // Id of item inserted.
                ]
            ];

    function __construct()
    {
        parent::__construct();
        
        // Apply table prefix.
        $this->config->load('media_gallery');
        $this->table = $this->config->item('mg_table_prefix').$this->table;
    }

    /**
    * Function 'set_table_name' set new table name.
    * @param   string  $table_name  Name of table to assign. 
    * @return  void
    *
    */
    public function set_table_name($table_name)
    {
        $this->table = $table_name;
    }
    
    /**
    * Function 'get_table_name' get the currently assigned table name.
    * @return  string  Name of table.
    *
    */
    public function get_table_name()
    {
        return $this->table;
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
    * Function 'clean_write_fields' prepare valid key-value pairs for insert operation.
    * @param   array  $fields  Key-value pair for each column to update..
    * @return  array           Ready for implode clean queries.
    *
    */
    private function clean_write_fields($fields)
    {
        $clean_fields = [];
        foreach ($fields as $field => $value)
        {
            if(array_key_exists($field, $this->columns))
            {
                $clean_fields[] = "`{$field}`='{$this->db->escape_str($value)}'";
            }
        }
        return $clean_fields;
    }

    /**
    * Function 'clean_ids' strips non numeric value from array.
    * @param   array  $ids  Ids to clean.
    * @return  array        Cleaned ids.
    *
    */
    private function clean_ids($ids)
    {
        $clean_ids = [];
        foreach ($ids as $id) {
            $cleaned_id = preg_replace('/[^0-9]/', "", $id);
            if(strlen($clean_ids) > 0) $clean_ids[] = $cleaned_id;
        }
        return $clean_ids;
    }

    /**
    * Function 'clean_id' strips non numeric value from a string.
    * @param   string  $id  Id to clean.
    * @return  number       Cleaned id.
    *
    */
    private function clean_id($id)
    {
        return preg_replace('/[^0-9]/', "", $id);
    }

    /**
    * Function 'clean_field_name' strips none alphanumeric and underscore characters.
    * @param   string  $fieldname  Name of field or column.
    * @return  string              Clean string.
    *
    */
    private function clean_field_name($fieldname)
    {
        return preg_replace('/[^a-zA-Z0-9_]/', '', $fieldname);
    }

    /**
    * Function 'log_error' logs to models.log file found in logs folder.
    * @param   string  $message  Custom error message to log.
    * @return  void
    *
    */
    private function log_error($message)
    {
        $date = date("Y-m-d H:i:s T", time());
        $DS = DIRECTORY_SEPARATOR;
        $log_file = APPPATH.'logs'.$DS.'models.log';
        @file_put_contents($log_file, "{$date} -> [{$this->table}] {$message}\n", FILE_APPEND);
    }

    /**
    * Function 'add' inserts new entry to table.
    * @param   array  $fields  Array of key-value pair that represents each column of
    *                          table.
    * @return  number|boolean  Id number of inserted row on success, false on failure.
    *
    */
    public function add ($fields)
    {
        $fields_total = count($fields);

        if ($fields_total > 0)
        {
            $clean_columns = $this->clean_write_fields($set_columns);
            $clean_total = count($clean_columns);

            if($clean_total > 0 && $clean_total == $fields_total)
            {
                $clean_columns[] = '`date_added`='.time();
                try
                {
                    if($this->db->query("INSERT INTO `{$this->table}` SET ".implode(',', $clean_columns)))
                    {
                        return $this->db->insert_id();
                    }
                    else
                    {
                        $error_message = preg_replace("\n", "", $this->db->_error_message());
                        $this->log_error($error_message);
                        return false;
                    }
                }
                catch (Exception $e)
                {
                    $error_message = preg_replace("\n", "", $e->getMessage());
                    $this->log_error($error_message);
                    return false;
                }
            }
            else
            {
                $this->log_error("Column count mismatch for insert operation.");
                return false;
            }
        }
        else
        {
            return false;
        }
    }

    /**
    * Function 'update' update table row(s) using ids.
    * @param  array  $fields   Key-value pair for each column to update.
    * @param  array  $ids      Ids of rows to update.
    * @return number|boolean   Affected rows on success, false on failure.
    *
    */
    public function update ($fields, $ids)
    {
        $fields_total = count($fields);
        $clean_ids = $this->clean_ids($ids);

        if ($fields_total > 0 && count($clean_ids) > 0)
        {
            $clean_columns = $this->clean_write_fields($fields);
            $clean_total = count($clean_columns);

            if($clean_total > 0 && $clean_total == $fields_total)
            {
                $clean_columns[] = '`date_modified`='.time();
                try
                {
                    if($this->db->query("UPDATE `{$this->table}` SET {implode(',', $clean_columns))} WHERE `id` IN ({implode(',', $clean_ids)})"))
                    {
                        return $this->db->affected_rows();
                    }
                    else
                    {
                        $error_message = preg_replace("\n", "", $this->db->_error_message());
                        @$this->log_error($error_message);
                        return false;
                    }
                }
                catch (Exception $e)
                {
                    $error_message = preg_replace("\n", "", $e->getMessage());
                    @$this->log_error($error_message);
                    return false;
                }
            }
            else
            {
                @$this->log_error("Column count mismatch for update operation.");
                return false;
            }
        }
        else
        {
            @$this->log_error("Fields are empty for update operation.");
            return false;
        }
    }

    /**
    * Function 'update_where' update table row(s) with where conditional string.
    * @param  array   $fields  Key-value pair for each column to update.
    * @param  string  $where   Conditional statement.
    * @return number|boolean   Affected rows on success, false on failure.
    *
    */
    public function update_where ($fields, $where)
    {
        $fields_total = count($fields);

        if ($fields_total > 0 && strlen($where) > 0)
        {
            $clean_columns = $this->clean_write_fields($fields);
            $clean_total = count($clean_columns);

            if($clean_total > 0 && $clean_total == $fields_total)
            {
                $clean_columns[] = '`date_modified`='.time();
                try
                {
                    if($this->db->query("UPDATE `{$this->table}` SET {implode(',', $clean_columns))} WHERE {$where}"))
                    {
                        return $this->db->affected_rows();
                    }
                    else
                    {
                        $error_message = preg_replace("\n", "", $this->db->_error_message());
                        @$this->log_error($error_message);
                        return false;
                    }
                }
                catch (Exception $e)
                {
                    $error_message = preg_replace("\n", "", $e->getMessage());
                    @$this->log_error($error_message);
                    return false;
                }
            }
            else
            {
                @$this->log_error("Column count mismatch for update-where operation.");
                return false;
            }
        }
        else
        {
            @$this->log_error("Fields are empty for update-where operation.");
            return false;
        }
    }

    /**
    * Function 'delete' delete table row(s) using id(s).
    * @param  array  $ids     Ids of row(s) to delete.
    * @return number|boolean  Affected rows on success, false on failure.
    *
    */
    public function delete ($ids)
    {
        $clean_ids = $this->clean_ids($ids);
        if(count($clean_ids) > 0)
        {
            try
            {
                if($this->db->query("DELETE FROM `{$this->table}` WHERE `id` IN {implode(',', $ids)}"))
                {
                    return $this->db->affected_rows();
                }
                else
                {
                    $error_message = preg_replace("\n", "", $this->db->_error_message());
                    @$this->log_error($error_message);
                    return false;
                }
            }
            catch (Exception $e)
            {
                $error_message = preg_replace("\n", "", $e->getMessage());
                @$this->log_error($error_message);
                return false;
            }
        }
        else
        {
            @$this->log_error("Empty id for delete operation.");
            return false;
        }
    }

    /**
    * Function 'get' fetch all category entries.
    * @param   array   $fields  Column names to select.
    * @param   array   $order   Column names to order.
    * @param   string  $sort    ASC or DESC.
    * @param   number  $limit   Number of rows to get (optional).
    * @param   number  $offset  Starting number of row (optional).
    * @return  array            Detailed result having keys code,message,data,affected
    *
    */
    public function get($fields=[], $order=["level","title","date_added"], $sort="ASC",$limit=null,$offset=null)
    {
        $columns_select = [];
        if(count($fields) > 0)
        {
            foreach ($fields as $field) {
                if(strlen($this->clean_field_name($field)) > 0)$columns_select[] = "`{$field}`";
            }
            if(count($columns_select) > 0)
            {
                $columns_select = implode(',', $columns_select);
            }
            else
            {
                $columns_select = '*';
            }
        }
        else
        {
            $columns_select = '*';
        }

        $columns_order = [];
        if(count($order) > 0)
        {
            foreach ($order as $field) {
                if(strlen($this->clean_field_name($field)) > 0)$columns_order[] = "`{$field}`";
            }
            $columns_order = implode(',', $columns_order);
        }
        $columns_sort = ($sort == "ASC")? $sort : "DESC";

        $sql = "SELECT {$columns_select} FROM `{$this->table}` WHERE `type`='{$this->media}' OR `type`='all' ORDER BY {$columns_order} {$columns_sort}";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    /**
    * Function 'create' creates a fresh table for this model in database.
    * @return  boolean true on success, false on failure.
    *
    */
    public function create()
    {
        // Create table in correct definition.
        $columns = [];
        foreach($this->columns as $column=>$value) $columns[] = "{$column} {$value}";
        $columns = implode(',', $columns);
        $create_sql = "CREATE TABLE IF NOT EXISTS `{$this->table}` ({$columns}) ENGINE=InnoDB DEFAULT CHARSET=utf8";
        $insert_sql = "INSERT INTO `{$this->table}` SET ";
        
        try
        {
            if($this->db->query($create_sql)) 
            {
                // Populate table with system core defaults.
                $fields = [
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
                return $this->add($fields);
            }
            else
            {
                $error_message = preg_replace("\n", "", $this->db->_error_message());
                @$this->log_error($error_message);
                return false;
            }
        }
        catch(Exception $e)
        {
            $error_message = preg_replace("\n", "", $e->getMessage());
            @$this->log_error($error_message);
            return false;
        }
    }
}
