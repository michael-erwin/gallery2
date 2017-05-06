<?php
/**
* CI model for photos under Media Gallery project.
* @package  Media Gallery
* @author   Michael Erwin Virgines <michael.erwinp@gmail.com>
*
*/
class M_photos extends CI_Model
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
     * Function 'isPreset' check if file hash of photo item already exist in database. Returns
     * true if found otherwise it returns false.
     * 
     * @param  string  $file_hash Cryptographic md5 file signature generated by function md5_file.
     * @return boolean            true on success, false on failure.
     */
    public function isPresent ($file_hash,$category_id)
    {
        $sql = "SELECT `id` FROM `photos` WHERE `checksum`='{$file_hash}' AND `category_id`={$category_id}";
        $query = $this->db->query($sql);
        if($query->num_rows() > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
    * Function 'add' inserts new photo entry to database.
    *
    * @param string        $title     Initial title for the uploaded photo file (required).
    * @param string        $uid       Unique identifier that will map to physical file name (required).
    * @param integer       $width     Full width of the original photo in pixels (required).
    * @param integer       $height    Full height of the original photo in pixels (required).
    * @param integer       $size      File size in bytes (required).
    * @param string        $checksum  MD5 file hash (required).
    * @param integer|null  $category  Category id where the photo belong (optional).
    * @return integer|boolean         Id number of inserted row on success, false on failure.
    */
    public function add ($title = null, $uid = null, $width=null, $height=null, $size=null, $checksum=null, $category=null, $main_visi='public', $sub_visi='public')
    {
        if ($title && $uid && $width && $height)
        {
            $visi = "public";
            $title = $this->db->escape($title);
            $width = $this->db->escape($width);
            $height = $this->db->escape($height);
            $date = time();
            $category = $category? $this->db->escape($category) : 1;

            $sql = "INSERT INTO `photos` SET `category_id`={$category}, `title`={$title}, `uid`='{$uid}', `width`={$width}, `height`={$height}, `file_size`={$size}, `checksum`='{$checksum}', `share_level`='{$visi}', `mc_share_level`='{$main_visi}', `sc_share_level`='{$sub_visi}', `date_added`={$date}";

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

    /**
    * Function 'update' updates one or more photo entry in database.
    *
    * @param   integer|array  $id           Id or array of id of the photo.
    * @param   string         $title        Title of the photo.
    * @param   string         $description  Description of the entry.
    * @param   string         $tags         Tags of the photo entry.
    * @param   integer|null   $category     Category id where the photo belong.
    * @return  boolean                      Number of affected rows on success, false on failure.
    *
    */
    public function update ($id=null, $title = null, $description=null, $tags=null, $category=null)
    {
        $ids = [];
        $id = is_null($id)? "" : $id;

        if (is_array($id))
        {
            foreach ($id as $row) {
                $row = clean_numeric_text($row);
                if(strlen($row) > 0) $ids[] = $row;
            }
            if(count($ids) > 0)
            {
                $id = implode(',', $ids);
            }
            else
            {
                $id = "";
            }
        }
        else
        {
            $id = clean_numeric_text($id);
        }

        if (strlen($id) > 0)
        {
            $title = is_null($title)? "" : $this->db->escape_str(clean_title_text($title));
            $description = is_null($description)? "" : $this->db->escape_str(clean_body_text($description));
            $tags = is_null($tags)? "" : $this->db->escape_str(clean_title_text($tags));
            $category = is_null($category)? "" : clean_numeric_text($category);
            $date = time();

            $sql = "UPDATE `photos` SET";
            if (strlen($title) > 0) $sql .= " `title`='{$title}',";
            if (strlen($description) > 0) $sql .= " `description`='{$description}',";
            if (strlen($tags) > 0) $sql .= " `tags`='{$tags}',";
            if (strlen($category) > 0) $sql .= " `category_id`={$category},";
            $sql .= " `date_modified`={$date} WHERE `id` IN ({$id})";

            $this->db->query($sql);
            return $this->db->affected_rows();
        }
        else
        {
            return false;
        }
    }

    /**
    * Delete a single photo entry.
    * @param  integer $id Id of the photo to delete.
    * @return boolean     Returns true on success, false on failure.
    *
    */
    public function delete($id=null)
    {
        $ids = [];
        $id = is_null($id)? "" : $id;

        if (is_array($id))
        {
            foreach ($id as $row) {
                $row = clean_numeric_text($row);
                if(strlen($row) > 0) $ids[] = $row;
            }
            if(count($ids) > 0)
            {
                $id = implode(',', $ids);
            }
            else
            {
                $id = "";
            }
        }
        else
        {
            $id = clean_numeric_text($id);
        }

        if(strlen($id) > 0)
        {
            // Check if entry exists.
            $get_query = $this->db->query("SELECT `id`,`uid` FROM `photos` WHERE `id` IN ({$id})");
            $items = $get_query->result_array();

            if(count($items) == 0)
            {
                return false;
            }
            else
            {
                // Delete physical files.
                foreach($items as $item)
                {
                    $this->delete_file($item['uid']);
                }

                // Delete database entry.
                $this->db->query("DELETE FROM `photos` WHERE `id` IN ({$id})");

                return $this->db->affected_rows();
            }
        }
        else
        {
            return false;
        }
    }

    public function delete_file($uid=null)
    {
        if($uid)
        {
            // Delete full size photo from disk.
            unlink($this->media_path."/photos/private/full_size/{$uid}.jpg");

            // Delete 256 cover photo from disk.
            unlink($this->media_path."/photos/public/256/{$uid}.jpg");

            // Delete 128 cover photo from disk.
            unlink($this->media_path."/photos/public/128/{$uid}.jpg");
        }
    }

    /**
    * Function 'get_by_id'   fetch single or multiple photo entry by id.
    * @param  integer|array  $id Id of photo entry (required).
    * @return array          Array of data for entry.
    *
    */
    public function get_by_id($id)
    {
        $ids = [];

        if(is_array($id))
        {
            foreach ($id as $item) {
                $sanitized = clean_numeric_text($item);
                if(strlen($sanitized) > 0) $ids[] = $sanitized;
            }
        }
        else
        {
            $sanitized = clean_numeric_text($id);
            if(strlen($sanitized) > 0) $ids[] = $sanitized;
        }

        if(count($ids) > 0)
        {
            $ids = implode(',', $ids);
            $sql_get = "SELECT * FROM `photos` WHERE `id` IN ({$ids})";

            if($get_query = $this->db->query($sql_get))
            {
                $result = $get_query->result_array();
                if(count($result) > 0) return $result;
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
    * Function 'get_by_page' fetch photo entries by page.
    * @param  integer  $per_page  Number of entries to display per page (required).
    * @param  integer  $page      Page number to get (required).
    * @return array               Array of photo objects.
    *
    */
    public function get_all_by_page($per_page, $page)
    {
        $page -= 1;
        $offset = $per_page * $page;
        $sql = "SELECT * FROM `photos` LIMIT {$per_page} OFFSET {$offset}";
        $query = $this->db->query($sql);
        return $query->result_array();
    }
}
