<?php
/*
Plugin Name: TalkHours Widget
Plugin URI: http://www.talkhours.com
Description: A plugin containing the TalkHours Wordpress widget
Version: 0.5
Author: Satoshi Kawase
Author URI: http://www.talkhours.com
Text Domain: talkhoursdomain
License: GPLv2
 
Copyright 2016  TalkHours
 
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.
 
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
 
 
class talkhours_widget extends WP_Widget {
 
	private $url_base = 'https://www.talkhours.com/api/wp_widget/';

    public function __construct() {
     
        parent::__construct(
            'talkhours_widget',
            __( 'TalkHours Widget', 'talkhoursdomain' ),
            array(
                'classname'   => 'talkhours_widget',
                'description' => __( 'Widget that adds your www.talkhours.com schedule onto your sidebar', 'talkhoursdomain' )
                )
        );
       
        load_plugin_textdomain( 'talkhoursdomain', false, basename( dirname( __FILE__ ) ) . '/languages' );
       
    }

    public function alt_embed_code($username=""){

      $ch = curl_init(); 
      // set url 
      curl_setopt($ch, CURLOPT_URL, $this->url_base . $username); 
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");

      //return the transfer as a string 
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 

      // // $output contains the output string 
      $output = curl_exec($ch); 

      if( ($httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE)) &&
          ($httpcode==200)
      ){
        curl_close($ch);
        return json_decode($output);
      }else{
        curl_close($ch);
        return false;
      }
    }

    public function get_embed_code($username=''){

      $ch = curl_init(); 
      // set url 
      curl_setopt($ch, CURLOPT_URL, $this->url_base . $username); 

      //return the transfer as a string 
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 

      // // $output contains the output string 
      $output = curl_exec($ch); 

      if( ($httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE)) &&
          ($httpcode==200)
      ){
        // // close curl resource to free up system resources 
        curl_close($ch);
        return json_decode($output);
      }else{
        curl_close($ch);
        return $this->alt_embed_code($username);
      }
    }

    /**  
     * Front-end display of widget.
     *
     * @see WP_Widget::widget()
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget( $args, $instance ) {    
         
        extract( $args );
         
        $title      = apply_filters( 'widget_title', $instance['title'] );
        $username    = $instance['username'];
         
        echo $before_widget;
         
        if ( $title ) {
            echo $before_title . $title . $after_title;
        }
                             
        echo $this->get_embed_code($username);

        echo $after_widget;
         
    }
 
  
    /**
      * Sanitize widget form values as they are saved.
      *
      * @see WP_Widget::update()
      *
      * @param array $new_instance Values just sent to be saved.
      * @param array $old_instance Previously saved values from database.
      *
      * @return array Updated safe values to be saved.
      */
    public function update( $new_instance, $old_instance ) {        
         
        $instance = $old_instance;
         
        $instance['title'] = strip_tags( $new_instance['title'] );
        $instance['username'] = strip_tags( $new_instance['username'] );
         
        return $instance;
         
    }
  
    /**
      * Back-end widget form.
      *
      * @see WP_Widget::form()
      *
      * @param array $instance Previously saved values from database.
      */
    public function form( $instance ) {    
     
        $title      = esc_attr( $instance['title'] );
        $username   = esc_attr( $instance['username'] );

        if(!$title)
          $title = 'TalkHours Schedule';   ?>
         
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('username'); ?>"><?php _e('talkhours username'); ?></label> 
            <input class="widefat" id="<?php echo $this->get_field_id('username'); ?>" name="<?php echo $this->get_field_name('username'); ?>" type="text" value="<?php echo $username; ?>" />
        </p> <?php 
    }
     
}
 
/* Register the widget */
add_action( 'widgets_init', function(){
     register_widget( 'talkhours_widget' );
});