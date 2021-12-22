<?php
/*
  Plugin Name: Rather Simple Background Slideshow
  Version: 1.0
  Plugin URI:
  Update URI: false
  Author: Oscar Ciutat
  Author URI: http://oscarciutat.com/code/
  Description: A really simple background slideshow
  License: GPLv2 or later

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as 
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

class Rather_Simple_Background_Slideshow {  

    /**
     * Plugin instance.
     *
     * @since 1.0
     *
     */
    protected static $instance = null;

    /**
     * Access this plugin’s working instance
     *
     * @since 1.0
     *
     */
    public static function get_instance() {
        
        if ( !self::$instance ) {
            self::$instance = new self;
        }

        return self::$instance;

    }
    
    /**
     * Used for regular plugin work.
     *
     * @since 1.0
     *
     */
    public function plugin_setup() {

          $this->includes();

        add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );
        add_shortcode( 'bgslideshow', array( $this, 'display_shortcode' ) );
    
    }

    /**
     * Constructor. Intentionally left empty and public.
     *
     * @since 1.0
     *
     */
    public function __construct() {}
    
    /**
     * Includes required core files used in admin and on the frontend.
     *
     * @since 1.0
     *
     */
    protected function includes() {}

    /**
     * Enqueues scripts in the frontend.
     *
     * @since 1.0
     *
     */
    function wp_enqueue_scripts() {
        wp_enqueue_style( 'rather-simple-background-slideshow-css', plugins_url( '/assets/css/vegas.min.css', __FILE__ ) ); 
        wp_enqueue_script( 'rather-simple-background-slideshow', plugins_url( '/assets/js/vegas.min.js', __FILE__ ), array( 'jquery' ) ); 
    }

    /**
     * Shows a background slideshow
     *
     * @since 1.0
     *
     */
    function display_shortcode( $atts ) {

        $html = '';

        $args = array(
            'post_type' => 'attachment',
            'numberposts' => -1,    
            'post_status' => null,
            'post_parent' => get_the_ID(),
            'post_mime_type' => 'image',
            'orderby' => 'rand'
        );
    
        $list = '';
        $attachments = get_posts( $args );
        if ( $attachments ) {
            foreach ( $attachments as $attachment ) {
                $image_attributes = wp_get_attachment_image_src( $attachment->ID, 'full' );
                $list = $list . '{ src:"' . $image_attributes[0] . '" },';
            }
        }
        $list = rtrim( $list, ',' );

        $selector = apply_filters( 'rsbs_selector', 'body' );
            
        $html .= '<script>
            $( function() {
                $( "' . wp_strip_all_tags( $selector ) . '" ).vegas( {
                    slides: [' . $list . '],
                    delay: 15000,
                    timer: false
                } );
            } );
            </script>';

        return $html;
        
    }

}

add_action( 'plugins_loaded', array ( Rather_Simple_Background_Slideshow::get_instance(), 'plugin_setup' ) );
