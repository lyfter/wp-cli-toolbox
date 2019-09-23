<?php


namespace Lyfter\WP_CLI\Commands;

use WP_CLI;

class Replace
{
    private $wpdb;
    private $args;
    private $search;
    private $replace;
    private $searchLike;
    
    /**
     * The lyfter replace command.
     *
     * This command takes two arguments.
     * The first arguments is the value that needs to be searched for.
     * The second argument is the value that we will replace the matching content with
     *
     * Example usage;
     * wp lyfter replace example.lyfter.com example.com
     *
     * @param $args
     */
    public function call($args)
    {
        global $wpdb;
        
        $this->setWpdb($wpdb);
        $this->setArgs($args);
        $this->validateArguments();
        
        $this->replacePostContent();
        $this->replacePostMeta();
        $this->replaceOptions();
        
    }
    
    /**
     * Validates if the commands given arguments are valid
     * The command requires two arguments
     */
    private function validateArguments()
    {
        if (count($this->getArgs()) !== 2) {
            WP_CLI::error('Command requires 2 arguments, ' . count($this->getArgs()) . ' given.');
        }
        
        $this->setSearch($this->getArgs()[0]);
        $this->setReplace($this->getArgs()[1]);
        $this->setSearchLike('%' . $this->getWpdb()->esc_like($this->getSearch()) . '%');
    }
    
    /**
     * Replace all post content
     */
    private function replacePostContent()
    {
        WP_CLI::log('Replacing post content');
        
        $query = $this->getWpdb()->prepare("UPDATE wp_posts SET post_content = REPLACE(post_content, %s, %s) WHERE post_content LIKE %s", $this->getSearch(), $this->getReplace(), $this->getSearchLike());
        $updatePostContent = $this->getWpdb()->query($query);
        
        if ($updatePostContent !== false) {
            WP_CLI::success('Updated post content');
        }
    }
    
    /**
     * Replace all post meta
     * Makes sure all serialized data is properly saved
     */
    private function replacePostMeta()
    {
        WP_CLI::log('Replacing post meta');
        $postMetaRows = $this->getWpdb()->get_results($this->getWpdb()->prepare("SELECT * FROM wp_postmeta WHERE meta_value LIKE %s", $this->getSearchLike()));
        
        // Replace all meta values that contain serialized arrays
        $this->replaceSerializedContent($postMetaRows, 'wp_postmeta', 'meta_value', 'meta_id');
        
        // Replace all other meta values
        $this->getWpdb()->query($this->getWpdb()->prepare("UPDATE wp_postmeta SET meta_value = REPLACE(meta_value, %s, %s) WHERE meta_value LIKE %s", $this->getSearch(), $this->getReplace(), $this->getSearchLike()));
        
        
        WP_CLI::success('Updated post meta');
    }
    
    /**
     * Replace all wordpress options
     * Makes sure all serialized data is properly saved
     */
    private function replaceOptions()
    {
        WP_CLI::log('Replacing options');
        $optionRows = $this->getWpdb()->get_results($this->getWpdb()->prepare("SELECT * FROM wp_options WHERE option_value LIKE %s", $this->getSearchLike()));
        
        // Replace all meta values that contain serialized arrays
        $this->replaceSerializedContent($optionRows, 'wp_options', 'option_value', 'option_id');
        
        // Replace all other meta values
        $this->getWpdb()->query($this->getWpdb()->prepare("UPDATE wp_options SET option_value = REPLACE(option_value, %s, %s) WHERE option_value LIKE %s", $this->getSearch(), $this->getReplace(), $this->getSearchLike()));
        
        WP_CLI::success('Updated options');
        
    }
    
    /**
     * Replace all serialized content from a row set
     *
     * @param $rows | Set of rows
     * @param $table | Table name
     * @param $column | Name of the column to replace content in
     * @param $identifier | The tables unique identifier
     */
    private function replaceSerializedContent($rows, $table, $column, $identifier)
    {
        if (!empty($rows)) {
            foreach ($rows as $row) {
                $updatedRow = false;
                
                if (!empty($row->$column) && is_serialized($row->$column)) {
                    $value = unserialize($row->$column);
                    
                    
                    foreach ($value as $key => $item) {
                        if (is_array($item)) {
                            foreach ($item as $subKey => $subItem) {
                                if (strpos($subItem, $this->getSearch()) !== false) {
                                    $item[$subKey] = str_replace($this->getSearch(), $this->getReplace(), $subItem);
                                    $updatedRow = true;
                                }
                            }
                        } else {
                            if (strpos($item, $this->getSearch()) !== false) {
                                $value[$key] = str_replace($this->getSearch(), $this->getReplace(), $item);
                                $updatedRow = true;
                            }
                        }
                        
                    }
                    
                    if ($updatedRow) {
                        $this->getWpdb()->update($table, array($column => serialize($value)), array($identifier => $row->$identifier));
                    }
                }
            }
        }
    }
    
    /**
     * @param mixed $args
     */
    public function setArgs($args)
    {
        $this->args = $args;
    }
    
    /**
     * @param mixed $replace
     */
    public function setReplace($replace)
    {
        $this->replace = $replace;
    }
    
    /**
     * @param mixed $search
     */
    public function setSearch($search)
    {
        $this->search = $search;
    }
    
    /**
     * @param mixed $searchLike
     */
    public function setSearchLike($searchLike)
    {
        $this->searchLike = $searchLike;
    }
    
    /**
     * @param mixed $wpdb
     */
    public function setWpdb($wpdb)
    {
        $this->wpdb = $wpdb;
    }
    
    /**
     * @return mixed
     */
    public function getArgs()
    {
        return $this->args;
    }
    
    /**
     * @return mixed
     */
    public function getReplace()
    {
        return $this->replace;
    }
    
    /**
     * @return mixed
     */
    public function getSearch()
    {
        return $this->search;
    }
    
    /**
     * @return mixed
     */
    public function getSearchLike()
    {
        return $this->searchLike;
    }
    
    /**
     * @return mixed
     */
    public function getWpdb()
    {
        return $this->wpdb;
    }
    
    
}
