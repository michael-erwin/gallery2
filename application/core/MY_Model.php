<?php
/**
* CI base model for Media Gallery project.
*
* @package  Media Gallery
* @author   Michael Erwin Virgines <michael.erwinp@gmail.com>
*
*/
class MY_Model extends CI_Model
{
    protected $table   = "";
    protected $columns = [];
    protected $rcodes  = ['FAILED','SUCCESS']; // Response codes.

    // This will be the standard response format. Put in place in order to
    // capture all possible errors.
    public $result  = [
                'code'    => "",
                'message' => "",
                'items'   => [],
                'info'    => [
                    'fetched' => 0,
                    'affected'  => 0,
                    'insert_id' => null
                ]
            ];

    function __construct()
    {
        parent::__construct();

        // Initialize code as failed.
        $this->result['code'] = $this->rcodes[0];

        // Load config and apply table prefix.
        $this->config->load('media_gallery');
        $this->table = trim($this->config->item('mg_table_prefix')).$this->table;
    }

    /**
    * Function 'set_table_name' set new table name with prefix if available.
    * @param   string  $table_name  Name of table to assign. 
    * @return  void
    *
    */
    public function set_table_name($table_name)
    {
        $this->table = trim($this->config->item('mg_table_prefix')).$table_name;
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
    * Function 'clean_write_fields' prepare valid key-value pairs for insert operation.
    * @param   array  $fields  Key-value pair for each column to update..
    * @return  array           Ready for implode clean queries.
    *
    */
    public function clean_write_fields($fields)
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
    public function clean_ids($ids)
    {
        $clean_ids = [];
        foreach ($ids as $id) {
            $cleaned_id = preg_replace('/[^0-9]/', "", $id);
            if(strlen($cleaned_id) > 0) $clean_ids[] = $cleaned_id;
        }
        return $clean_ids;
    }

    /**
    * Function 'clean_id' strips non numeric value from a string.
    * @param   string  $id  Id to clean.
    * @return  number       Cleaned id.
    *
    */
    public function clean_id($id)
    {
        return preg_replace('/[^0-9]/', "", $id);
    }

    /**
    * Function 'clean_field_name' strips none alphanumeric and underscore characters.
    * @param   string  $fieldname  Name of field or column.
    * @return  string              Clean string.
    *
    */
    public function clean_field_name($fieldname)
    {
        return preg_replace('/[^a-zA-Z0-9_]/', '', $fieldname);
    }

    /**
    * Function 'log_error' logs to models.log file found in logs folder.
    * @param   string  $message  Custom error message to log.
    * @return  void
    *
    */
    public function log_error($message)
    {
        $date = date("Y-m-d H:i:s T", time());
        $DS = DIRECTORY_SEPARATOR;
        $log_file = APPPATH.'logs'.$DS.'models.log';
        @file_put_contents($log_file, "{$date} -> [{$this->table}] {$message}\n", FILE_APPEND);
    }

    /**
    * Function 'insert' inserts new entry to table.
    * @param   array  $fields  Array of key-value pair that represents each column of
    *                          table.
    * @return  array           Formatted result.
    *
    */
    public function insert ($fields)
    {
        $response = $this->result;
        $fields_total = count($fields);

        if ($fields_total > 0)
        {
            $clean_columns = $this->clean_write_fields($fields);
            $clean_total = count($clean_columns);

            if($clean_total > 0 && $clean_total == $fields_total)
            {
                $clean_columns[] = '`date_added`='.time();
                try
                {
                    if($this->db->query("INSERT INTO `{$this->table}` SET ".implode(',', $clean_columns)))
                    {
                        $error_message = preg_replace("/\n/", "", $this->db->error()['message']);
                        $response['code'] = $this->rcodes[1];
                        $response['message'] = $error_message;
                        $response['info']['insert_id'] = $this->db->insert_id();
                    }
                    else
                    {
                        $error_message = preg_replace("/\n/", "", $this->db->error()['message']);
                        $response['code'] = $this->rcodes[0];
                        $response['message'] = $error_message;
                    }
                }
                catch (Exception $e)
                {
                    $error_message = preg_replace("\n", "", $e->getMessage());
                    $response['code'] = $this->rcodes[0];
                    $response['message'] = $error_message;
                }
            }
            else
            {
                $error_message = "Column count mismatch for insert operation.";
                $response['code'] = $this->rcodes[0];
                $response['message'] = $error_message;
            }
        }
        else
        {
            $error_message = "Field is empty.";
            $response['code'] = $this->rcodes[0];
            $response['message'] = $error_message;
        }
        return $response;
    }

    /**
    * Function 'update' update table row(s) using ids.
    * @param  array  $fields   Key-value pair for each column to update.
    * @param  array  $ids      Ids of rows to update.
    * @return array            Formatted result.
    *
    */
    public function update ($fields, $ids)
    {
        $response = $this->result;
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
                    if($this->db->query("UPDATE `{$this->table}` SET ".implode(',', $clean_columns)." WHERE `id` IN (".implode(',', $clean_ids).")"))
                    {
                        $response['code'] = $this->rcodes[1];
                        $response['message'] = "Table updated with {$this->db->affected_rows()} rows changed.";
                        $response['info']['affected'] = $this->db->affected_rows();
                    }
                    else
                    {
                        $error_message = preg_replace("/\n/", "", $this->db->error()['message']);
                        $response['code'] = $this->rcodes[0];
                        $response['message'] = $error_message;
                    }
                }
                catch (Exception $e)
                {
                    $error_message = preg_replace("\n", "", $e->getMessage());
                    $response['code'] = $this->rcodes[0];
                    $response['message'] = $error_message;
                }
            }
            else
            {
                $error_message = "Column count mismatch for update operation.";
                $response['code'] = $this->rcodes[0];
                $response['message'] = $error_message;
            }
        }
        else
        {
            $error_message = "Fields are empty for update operation.";
            $response['code'] = $this->rcodes[0];
            $response['message'] = $error_message;
        }
        return $response;
    }

    /**
    * Function 'update_where' update table row(s) with where conditional string.
    * @param  array   $fields  Key-value pair for each column to update.
    * @param  string  $where   Conditional statement.
    * @return array            Formatted result.
    *
    */
    public function update_where ($fields, $where)
    {
        $response = $this->result;
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
                    if($this->db->query("UPDATE `{$this->table}` SET ".implode(',', $clean_columns)." WHERE {$where}"))
                    {
                        $response['code'] = $this->rcodes[1];
                        $response['message'] = "Table updated with {$this->db->affected_rows()} rows changed.";
                        $response['info']['affected'] = $this->db->affected_rows();
                    }
                    else
                    {
                        $error_message = preg_replace("/\n/", "", $this->db->error()['message']);
                        $response['code'] = $this->rcodes[0];
                        $response['message'] = $error_message;
                    }
                }
                catch (Exception $e)
                {
                    $error_message = preg_replace("\n", "", $e->getMessage());
                    $response['code'] = $this->rcodes[0];
                    $response['message'] = $error_message;
                }
            }
            else
            {
                 $error_message = "Column count mismatch for update-where operation.";
                $response['code'] = $this->rcodes[0];
                $response['message'] = $error_message;
            }
        }
        else
        {
            $error_message = "Fields are empty for update-where operation.";
            $response['code'] = $this->rcodes[0];
            $response['message'] = $error_message;
        }

        return $response;
    }

    /**
    * Function 'delete' delete table row(s) using id(s).
    * @param  array  $ids     Ids of row(s) to delete.
    * @return array           Formatted result.
    *
    */
    public function delete ($ids)
    {
        $response  = $this->result;
        $clean_ids = $this->clean_ids($ids);

        if(count($clean_ids) > 0)
        {
            try
            {
                if($this->db->query("DELETE FROM `{$this->table}` WHERE `id` IN (".implode(',', $clean_ids).")"))
                {
                    $response['code'] = $this->rcodes[1];
                    $response['message'] = "Table updated with {$this->db->affected_rows()} rows deleted.";
                    $response['info']['affected'] = $this->db->affected_rows();
                }
                else
                {
                    $error_message = preg_replace("/\n/", "", $this->db->error()['message']);
                    $response['code'] = $this->rcodes[0];
                    $response['message'] = $error_message;
                }
            }
            catch (Exception $e)
            {
                $error_message = preg_replace("\n", "", $e->getMessage());
                $response['code'] = $this->rcodes[0];
                $response['message'] = $error_message;
            }
        }
        else
        {
            $error_message = "Cannot delete using empty id.";
            $response['code'] = $this->rcodes[0];
            $response['message'] = $error_message;
        }

        return $response;
    }

    /**
    * Function 'delete' delete table row(s) using id(s).
    * @param  string   $where   Conditional clause.
    * @return array             Formatted result.
    *
    */
    public function delete_where ($where)
    {
        $response  = $this->result;

        try
        {
            if($this->db->query("DELETE FROM `{$this->table}` WHERE {$where}"))
            {
                $response['code'] = $this->rcodes[1];
                $response['message'] = "Table updated with {$this->db->affected_rows()} rows deleted.";
                $response['info']['affected'] = $this->db->affected_rows();
            }
            else
            {
                $error_message = preg_replace("/\n/", "", $this->db->error()['message']);
                $response['code'] = $this->rcodes[0];
                $response['message'] = $error_message;
            }
        }
        catch (Exception $e)
        {
            $error_message = preg_replace("\n", "", $e->getMessage());
            $response['code'] = $this->rcodes[0];
            $response['message'] = $error_message;
        }

        return $response;
    }

    /**
    * Function 'get' fetch all category entries.
    * @param   array   $fields  Column names to select (optional).
    * @param   array   $order   Column names to order (optional).
    * @param   string  $sort    ASC or DESC (optional).
    * @param   number  $limit   Number of rows to get (optional).
    * @param   number  $offset  Starting number of row (optional).
    * @return  array            Formatted result.
    *
    */
    public function get($fields=[], $order=[], $sort="", $limit=null, $offset=null)
    {
        $response = $this->result;
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
        } else $columns_order = "";

        $columns_sort = $sort? ($sort == "ASC")? $sort : "DESC" : "";

        $sql = "SELECT {$columns_select} FROM `{$this->table}`";
        if(strlen($columns_order) > 0)  $sql .= " ORDER BY {$columns_order}";
        if(strlen($columns_sort) > 0)   $sql .= " {$columns_sort}";
        if(is_numeric($limit))          $sql .= " LIMIT {$limit}";
        if(is_numeric($offset))         $sql .= " OFFSET {$offset}";

        try
        {
            if($query = $this->db->query($sql))
            {
                $response['code'] = $this->rcodes[1];
                $response['message'] = "Items retrieved.";
                $response['items'] = $query->result_array();
                $response['info']['fetched'] = count($response['items']);
            }
            else
            {
                $error_message = preg_replace("/\n/", "", $this->db->error()['message']);
                $response['code'] = $this->rcodes[0];
                $response['message'] = $error_message;
                $response['info']['syntax'] = $sql;
            }
        }
        catch (Exception $e)
        {
            $error_message = preg_replace("\n", "", $e->getMessage());
            $response['code'] = $this->rcodes[0];
            $response['message'] = $error_message;
        }

        return $response;
    }

    /**
    * Function 'get' fetch all category entries.
    * @param   array   $fields  Column names to select.
    * @param   string  $where   MySQL WHERE clause.
    * @param   array   $order   Column names to order (optional).
    * @param   string  $sort    ASC or DESC (optional).
    * @param   number  $limit   Number of rows to get (optional).
    * @param   number  $offset  Starting number of row (optional).
    * @return  array            Formatted result.
    *
    */
    public function get_where($fields=[], $where='', $order=[], $sort=null, $limit=null, $offset=null)
    {
        $response = $this->result;
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
        } else $columns_order = "";

        $columns_sort = $sort? ($sort == "ASC")? $sort : "DESC" : "";

        $sql = "SELECT {$columns_select} FROM `{$this->table}`";
        if(strlen($where) > 0)          $sql .= " WHERE {$where}";
        if(strlen($columns_order) > 0)  $sql .= " ORDER BY {$columns_order}";
        if(strlen($columns_sort) > 0)   $sql .= " {$columns_sort}";
        if(is_numeric($limit))          $sql .= " LIMIT {$limit}";
        if(is_numeric($offset))         $sql .= " OFFSET {$offset}";

        try
        {
            if($query = $this->db->query($sql))
            {
                $response['code'] = $this->rcodes[1];
                $response['message'] = "Items retrieved.";
                $response['items'] = $query->result_array();
                $response['info']['fetched'] = count($response['items']);
            }
            else
            {
                $error_message = preg_replace("/\n/", "", $this->db->error()['message']);
                $response['code'] = $this->rcodes[0];
                $response['message'] = $error_message;
                $response['info']['syntax'] = $sql;
            }
        }
        catch (Exception $e)
        {
            $error_message = preg_replace("\n", "", $e->getMessage());
            $response['code'] = $this->rcodes[0];
            $response['message'] = $error_message;
        }

        return $response;
    }

    /**
    * Function 'create' creates a fresh table for this model in database.
    * @return  boolean true on success, false on failure.
    *
    */
    public function create()
    {
        $response = $this->result;

        if(strlen($this->table) == 0)
        {
            $error_message = "Can't create table. No table name defined.";
            $response['message'] = $error_message;
        }
        elseif(count($this->columns) == 0)
        {
            $error_message = "Can't create table. No columns defined.";
            $response['message'] = $error_message;
        }
        else
        {
            // Create table in correct definition.
            $columns = [];
            foreach($this->columns as $column=>$value) $columns[] = "{$column} {$value}";
            $columns = implode(',', $columns);
            $create_sql = "CREATE TABLE IF NOT EXISTS `{$this->table}` ({$columns}) ENGINE=InnoDB DEFAULT CHARSET=utf8";
            
            try
            {
                if($this->db->query($create_sql)) 
                {
                    $error_message = preg_replace("/\n/", "", $this->db->error()['message']);
                    $response['code'] = $this->rcodes[1];
                }
                else
                {
                    $error_message = preg_replace("/\n/", "", $this->db->error()['message']);
                    $response['code'] = $this->rcodes[0];
                    $response['message'] = $error_message;
                }
            }
            catch(Exception $e)
            {
                $error_message = preg_replace("\n", "", $e->getMessage());
                $response['message'] = $error_message;
            }
        }
        return $response;
    }
}
