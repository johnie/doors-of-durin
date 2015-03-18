<?php
/*
* Plugin Name: Doors of Durin
* Plugin URI: https://github.com/johnie/plugin-boilerplate
* Description:
* Version: 0.0.1
* Author: Johnie Hjelm
* Author URI: http://johnie.se
* License: MIT
*/

/*
Copyright 2015 Johnie Hjelm <johniehjelm@me.com> (http://johnie.se)

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.

*/

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'DoorsOfDurin' ) ) {

  class DoorsOfDurin {

    private static $instance;

    /**
     * Tag identifier used by file includes and selector attributes.
     * @var string
     */

    public $tag;

    /**
     * User friendly name used to identify the plugin.
     * @var string
     */

    public $name;

    /**
     * Description of the plugin.
     * @var string
     */

    public $description;

    /**
     * Current version of the plugin.
     * @var string
     */

    public $version;

    /**
     * Plugin loader instance.
     *
     * @since 1.0.0
     *
     * @return object
     */

    public static function instance() {
      if ( ! isset( self::$instance ) ) {
        self::$instance = new static;
        self::$instance->setup_globals();
        self::$instance->setup_actions();
      }

      return self::$instance;
    }

    /**
     * Initiate the plugin by setting the default values and assigning any
     * required actions and filters.
     *
     * @access private
     */

    private function setup_actions() {

			add_action( 'wp_enqueue_scripts', array( $this, 'doors_of_durin' ), 999 );
    	add_action( 'wp_ajax_moria_enter', array( $this, 'speak_friend_and_enter' ) );
			add_action( 'wp_ajax_nopriv_moria_enter', array( $this, 'speak_friend_and_enter' ) );

    }

    private function constants() {
      if ( ! defined( 'DOORSOFDURIN_PLUGIN_URL' ) ) {
        $plugin_url = plugin_dir_url( __FILE__ );
        define( 'DOORSOFDURIN_PLUGIN_URL', $plugin_url );
      }
    }

    function moria_excerpt_more( $more ){
			// Get the current post
			global $post;

			// Create the new link
			$output  = "&hellip;";
			$output .= '<br />';
			$output .= '<a class="moria-read-more" data-post_id="'. $post->ID .'" href="'. get_permalink($post->ID) . '" title="Read ' . get_the_title($post->ID).'">';
			$output .= "Read more";
			$output .= '</a>';

			// Return it to the original function
			return $output;
		}

    function doors_of_durin() {
    	// Register our js to load in the footer
			// Tell WP to make sure jQuery is also loaded first
			wp_register_script( 'moria', DOORSOFDURIN_PLUGIN_URL . 'js/moria.js', array( 'jquery' ), '', true );

		  $args = array(
		    'ajaxurl' => admin_url( 'admin-ajax.php' ),
		    'nonce'   => wp_create_nonce( 'moria_riddle' )
		  );
		  wp_localize_script( 'moria', 'durin', $args );
		}

    function speak_friend_and_enter() {
		  if ( empty( $_POST['post_id'] ) || empty( $_POST['nonce'] ) ) die('empty');
		  // Verify the nonce
		  if (! wp_verify_nonce( $_POST['nonce'], 'moria_riddle' ) ) die('bad nonce bad');
		  // Get the post
		  $post = get_post( $_POST['post_id'] );
		  // Apply the_content filters to add proper formatting
		  echo apply_filters( 'the_content', $post->post_content );
		  // Die, so that admin-ajax.php doesn't return false
		  die;
		}

    /**
 		 * Initiate the globals
 		 *
     * @access private
 		 */

    private function setup_globals() {
      $this->tag = 'doorsofdurin';
      $this->name = 'Doors of Durin';
      $this->description = 'Speak friend and enter the gates of Moria';
      $this->version = '1.0.0';
    }

  }

}

if ( !function_exists( 'doorsofdurin' ) ) {
  function doorsofdurin() {
    return DoorsOfDurin::instance();
  }
}

add_action( 'plugins_loaded', 'doorsofdurin' );
