<?php
/**
* CI model for categories under Media Gallery project.
*
* @package  Media Gallery
* @author   Michael Erwin Virgines <michael.erwinp@gmail.com>
*
*/
class M_category extends CI_Model
{
    private $media_path, $max_thumbs;

    function __construct()
    {
        parent::__construct();
        $this->config->load('media_gallery');
        $this->media_path = $this->config->item('mg_media_path');
        $this->max_thumbs = $this->config->item('mg_max_thumbs');
    }

    /**
    * Function 'add' inserts new image entry to database.
    * @param  integer  $level        1 or 2.
    * @param  string   $type         Media type in singular form i.e. photo.
    * @param  string   $title        Title of the category.
    * @param  string   $description  Description.
    * @param  string   $publish      Whether to set public or not. Values can be 'yes' or 'no'.
    * @param  integer  $parent_id    Parent id where the new category will be placed (optional).
    * @return integer|boolean        Id number of inserted row on success, false on failure.
    *
    */
    public function add ($level=null, $type = null, $title = null, $description=null, $icon = null, $icon_default=0, $publish='yes', $parent_id = null)
    {
        $parent_id = isset($parent_id)? (is_numeric($parent_id)? $parent_id : 0) : 0;

        if ($title)
        {
            $allowed_types = ['all','photo','video'];
            $title = $this->db->escape_str($title);
            $type = $type;
            $description = $this->db->escape_str($description);
            $icon = $this->db->escape_str($icon);
            $publish = $this->db->escape_str($publish);
            $date = time();
            // Make sure to only insert allowed types.
            if(in_array($type,$allowed_types))
            {
                $sql  = "INSERT INTO `categories` SET `level`={$level}, `type`='{$type}', `title`='{$title}', `description`='{$description}',";
                $sql .= " `icon`='{$icon}', `icon_default`={$icon_default}, `published`='{$publish}', `share_level`='private', `parent_id`={$parent_id}, date_added={$date}";
                if ($query = $this->db->query($sql))
                {
                    return $this->db->insert_id();
                }
                else
                {
                    return false;
                }
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }

    /**
    * Function 'update' inserts new image entry to database.
    * @param  integer  $id           Id of the category.
    * @param  string   $title        Title of the category.
    * @param  string   $description  Description.
    * @param  string   $publish      Whether to set public or not. Values can be 'yes' or 'no'.
    * @param  integer  $parent_id    Parent id where the category will be placed (optional).
    * @return integer|boolean        True on success, false on failure.
    *
    */
    public function update ($id = null, $title = null, $description=null, $icon = null, $icon_default=0, $publish='yes', $parent_id = null)
    {
        $id = isset($id)? is_numeric($id)? $id : null : null;
        $parent_id = isset($parent_id)? (is_numeric($parent_id)? $parent_id : 0) : 0;

        if ($id && $title)
        {
            $title = $this->db->escape_str($title);
            $description = $this->db->escape_str($description);
            $publish = $this->db->escape_str($publish);
            $date = time();

            $sql = "UPDATE `categories` SET `title`='{$title}', `description`='{$description}', `icon`='{$icon}', `icon_default`={$icon_default}, `published`='{$publish}', `parent_id`={$parent_id}, date_modified={$date} WHERE `id`={$id}";

            if ($this->db->query($sql))
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }

    /**
    * Delete a single or multiple category entry.
    * @param   integer  $id     ID number of the category to delete.
    * @return  boolean          Returns numeric (affected rows) on success, false on failure.
    *
    */
    public function delete($id)
    {
        $ids = [];
        $id = clean_numeric_text($id);

        if (strlen($id) > 0)
        {
            $cat_ids  = []; // Used in delete category statement.
            $sub_ids  = []; // Used in update media statement.
            $affected = 0;

            // Determine category level to delete.
            $info_sql  = "SELECT `id`,`level`,`type`,`core` FROM `categories` WHERE `id`={$id}";
            $info_data = ($this->db->query($info_sql))->result_array();
            
            // Populate $cat_ids and $sub_ids.
            if(count($info_data) > 0)
            {
                $info_data = $info_data[0];

                if($info_data['core'] == "yes") return false; // Do no proceed if category is core item.

                $cat_ids[] = $id;

                if($info_data['level'] == 1) // Main category
                {
                    // Gt all subcategory ids.
                    $subs_sql  = "SELECT `id`,`level`,`type` FROM `categories` WHERE `parent_id`={$id}";
                    $subs_data = ($this->db->query($subs_sql))->result_array();
                    if(count($subs_data) > 0)
                    {
                        foreach($subs_data as $sub_data)
                        {
                            $cat_ids[] = $sub_data['id'];
                            $sub_ids[] = $sub_data['id'];
                        }
                    }
                }
                else
                {
                    $sub_ids[] = $id;
                }
                
                // Execute category delete.
                $cat_ids = implode(',', $cat_ids);
                $del_sql = "DELETE FROM `categories` WHERE `id` IN ({$cat_ids})";
                $this->db->query($del_sql);
                $affected += $this->db->affected_rows();

                // Execute media transfer.
                if(count($sub_ids) > 0)
                {
                    $sub_ids = implode(',', $sub_ids);
                    $move_photo_sql = "UPDATE `photos` SET `category_id`=1 WHERE `category_id` IN ({$sub_ids})";
                    $move_video_sql = "UPDATE `videos` SET `category_id`=1 WHERE `category_id` IN ({$sub_ids})";
                    $this->db->query($move_photo_sql);
                    $affected += $this->db->affected_rows();
                    $this->db->query($move_video_sql);
                    $affected += $this->db->affected_rows();
                }
                return $affected;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }

    /**
    * Function 'get_all' fetch all category entries.
    * @param   string  $type   Media type in singular form i.e. 'photo'.
    * @return  array           Array of category objects having $type media id and the uncategorized.
    *
    */
    public function get_all($type = 'photo')
    {
        $sql = "SELECT * FROM `categories` WHERE `type`='{$type}' OR `type`='all' ORDER BY `level`,`title`,`date_added` ASC";
        $query = $this->db->query($sql);
        return $query->result_array();
    }
}
