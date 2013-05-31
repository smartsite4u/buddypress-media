<?php

/**
 * Description of BPMediaUploadModel
 *
 * @author Joshua Abenazer <joshua.abenazer@rtcamp.com>
 */
class RTMediaUploadModel {
    public $upload = array(
        'mode' => 'file_upload',
        'context' => false,
        'context_id' => false,
        'privacy' => 0,
        'custom_fields' => array(),
        'taxonomy' => array(),
        'album_id' => false,
        'files' => false,
        'title' => false,
        'description' => false
    );

    function set_post_object() {
        $this->upload = wp_parse_args($_POST, $this->upload);
        $this->sanitize_object();
        return $this->upload;
    }

    function has_context() {
        if (!isset($this->upload['context_id']))
            return false;
    }


    function sanitize_object() {
        if (!$this->has_context())
            $context = new RTMediaContext();
			$this->upload['context']= $context->context;
			$this->upload['context_id'] = $context->context_id;

        if (!is_array($this->upload['taxonomy']))
            $this->upload['taxonomy'] = array($this->upload['taxonomy']);

        if (!is_array($this->upload['custom_fields']))
            $this->upload['custom_fields'] = array($this->upload['custom_fields']);

        if ( !$this->has_album_id() || !$this->has_album_permissions() )
            $this->set_album_id();

    }

    function has_album_id(){
        if(!$this->upload['album_id'])
            return false;
        return true;
    }

    function has_album_permissions(){
        return false;
    }

    function set_album_id(){
        if (class_exists('BuddyPress')) {
            $this->set_bp_album_id();
        } else {
            $this->set_wp_album_id();
        }
    }

    function set_bp_album_id(){
        if (bp_is_blog_page()) {
            $this->set_wp_album_id();
        } else {
            $this->set_bp_component_album_id();
        }
    }

    function set_wp_album_id(){
        $this->upload['album_id'] = $this->upload['context_id'];
    }

    function set_bp_component_album_id() {
        switch (bp_current_component()) {
            case 'groups': $this->upload['album_id'] = groups_get_groupmeta(bp_get_current_group_id(),'bp_media_default_album');
                break;
            default:
                $this->upload['album_id'] = get_user_meta(bp_displayed_user_id(),'bp-media-default-album',true);
                break;
        }
    }
}

?>