<?php

/*
 * 1 Flash Gallery Widget
 */

// No direct access to this file
defined('ABSPATH') or die('Restricted access');

class FgalleryWidget extends WP_Widget {
    /** constructor */
    function FgalleryWidget() {
        parent::WP_Widget(false, $name = '1 Flash Gallery Widget', array('description'=>__('Use this widget to place any gallery from 1 Flash Gallery plugin anywhere on your page')));	
    }

    /** @see WP_Widget::widget */
    function widget($args, $instance) {		
        extract( $args );
        $title = apply_filters('widget_title', $instance['title']);
		$gall_id = esc_attr($instance['gall_id']);
		$type = esc_attr($instance['type']);
                $ex = esc_attr($instance['ex']);
		$album = fgallery_get_album($gall_id);
        ?>
              <?php echo $before_widget; ?>
                  <?php if ( $title )
                        echo $before_title . $title . $after_title; ?>
                 <?php echo do_shortcode('[fgallery id='.$gall_id.' w='.$album['gall_width'].' h='.$album['gall_height'].' t='.$type.' ex='.$ex.']');?>
              <?php echo $after_widget; ?>
        <?php
    }

    /** @see WP_Widget::update */
    function update($new_instance, $old_instance) {				
		$instance = $old_instance;
		$instance['gall_id'] = strip_tags($new_instance['gall_id']);
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['type'] = strip_tags($new_instance['type']);
		$instance['ex'] = strip_tags($new_instance['ex']);
        return $instance;
    }

    /** @see WP_Widget::form */
    function form($instance) {				
        $gall_id = esc_attr($instance['gall_id']);
        $title = esc_attr($instance['title']);
        $type = esc_attr($instance['type']);
        $ex = esc_attr($instance['ex']);
        $albums = fgallery_get_albums(1,99999999,4);
        ?>
            <p>
                <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?>
                <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" 
                       name="<?php echo $this->get_field_name('title'); ?>" 
                       type="text" value="<?php echo $title; ?>" />
                </label>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('gall_id'); ?>"><?php _e('Choose gallery:', 'fgallery'); ?> 
                    <select class="widefat" id="<?php echo $this->get_field_id('gall_id'); ?>" name="<?php echo $this->get_field_name('gall_id'); ?>">
                        <?php foreach ($albums as $album): ?>
                             <option value="<?php echo $album['gall_id']?>" <?if ($album['gall_id'] == $gall_id) echo 'selected="selected"'?>>
                                 <?php echo $album['gall_name']?>
                             </option>
                        <?php endforeach; ?>
                    </select>
                </label>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('ex'); ?>"><?php _e('Show title and number of images:'); ?>
                    <select class="widefat" id="<?php echo $this->get_field_id('ex'); ?>"
                            name="<?php echo $this->get_field_name('ex'); ?>">
                        <option value="0" <?php if ($ex == 0) echo 'selected="selected"'?>><?php _e('No')?></option>
                        <option value="1" <?php if ($ex == 1) echo 'selected="selected"'?>><?php _e('Yes')?></option>
                        <option value="2" <?php if ($ex == 2) echo 'selected="selected"'?>><?php _e('Only Title')?></option>
                    </select>
                </label>
            </p>
            <p>
            <input type="radio" name="<?php echo $this->get_field_name('type'); ?>" value="0" <?php if ($type == 0) echo 'checked'?>> 
                <?php _e('Flash object', 'fgallery') ?> <br />
            <input type="radio" name="<?php echo $this->get_field_name('type'); ?>" value="1" <?php if ($type == 1) echo 'checked'?>> 
                <?php _e('Text link to gallery', 'fgallery'); ?> <br />
            <input type="radio" name="<?php echo $this->get_field_name('type'); ?>" value="2" <?php if ($type == 2) echo 'checked'?>> 
                <?php _e('Cover as link to gallery', 'fgallery'); ?><br />
            </p>
        <?php 
    }

} // class FGalleryWidge