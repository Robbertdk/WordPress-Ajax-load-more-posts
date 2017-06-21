<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://www.robbertdekuiper.com
 * @since      0.1.0
 *
 * @package    Ajax_Filter_Posts
 */

/**
 * The core plugin class.
 *
 * The plugin logic lives here
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      0.1.0
 * @package    Ajax_Filter_Posts
 * @author     Robbert de Kuiper <mail@robbertdekuiper.com>
 */
class Ajax_Load_More_Posts {

  /**
   * The unique identifier of this plugin.
   *
   * @var      string    $plugin_name    The string used to uniquely identify this plugin.
   */
  protected $plugin_name;

  /**
   * The current version of the plugin.
   *
   * @var      String    $version    The current version of the plugin.
   */
  protected $version;


  /**
   * Define the core functionality of the plugin.
   *
   * Set the plugin name and the plugin version that can be used throughout the plugin.
   * Load the dependencies, define the locale, and set the hooks.
   *
   */
  public function __construct() {

    $this->plugin_name = 'ajax-load-more-posts';
    $this->version = '0.1.0';

    add_action( 'plugins_loaded', [$this, 'load_textdomain'] );
    add_action( 'wp_enqueue_scripts', [$this,'add_scripts'] );
    add_action( 'wp_ajax_process_load_more_trigger', [$this, 'process_load_more_trigger']);
    add_action( 'wp_ajax_nopriv_process_load_more_trigger', [$this, 'process_load_more_trigger']);
    add_action( 'navigation_markup_template', [$this, 'replace_page_navigation'], 10, 2);
  }

  /**
   * Set the plugins language domain
   */
  public function load_textdomain() {
    if ( strpos( __FILE__, basename( WPMU_PLUGIN_DIR ) ) ) {
      load_muplugin_textdomain( 'ajax-load-more-posts', basename(dirname( __FILE__ )) . '/languages' );
    } else {
      load_plugin_textdomain( 'ajax-load-more-posts', false, basename(dirname( __FILE__ )) . '/languages' );
    }
  }

  /**
   * Load the required assets for this plugin.
   *
   */
  public function add_scripts() {

    $script_variables = [
      'ajaxUrl' => admin_url( 'admin-ajax.php' ),
      'timeoutMessage' => __('It took to long the get the posts. Please reload the page and try again.', 'ajax-load-more-posts'),
      'serverErrorMessage' => __('Oops. Got no response. Please reload the page and try again.', 'ajax-load-more-posts'),
      'containerSelector' => '.posts'
    ];

    $script_variables = apply_filters('load-more-posts-js-vars', $script_variables );    

    wp_enqueue_script( 'ajax-load-more-posts', plugins_url('/assets/js/ajax-load-more-posts.js', __FILE__), [], '', true );
    wp_localize_script( 'ajax-load-more-posts', 'loadMore', $script_variables);
  }

  /**
   * Add extra variables to the url that can be used in next query
   * 
   * @param  string   $url  url of the next page
   * @return string   Updated url
   */
  private function get_url_vars($url) {

    /* IF WPML is installed add language variable to set variable later during the query
      WPML can't figure out which language to query, when posts are loaded via AJAX. */
    if (defined(ICL_LANGUAGE_CODE)) {
      $url = add_query_arg( 'wpml_lang', ICL_LANGUAGE_CODE, $url );
    }
    $url = apply_filters('load-more-posts-query-args', $url );
    return $url;
  }

  /**
   * Replace the default WordPress page navigation
   *
   * Wraps the default navigation in a no script tag
   * And adds our load more button
   * 
   * @param  string $template default HTML of pagenavigation
   * @return string Updated HTML of pagenavigation
   */
  public function replace_page_navigation($template) {
    global $wp_query;
    $current_page = $wp_query->get( 'paged' );

    // Set original template in a no-script tag
    $template = '<noscript>' . $template . '</noscript>';

    // And add our button
    if ( !$current_page || $current_page < $wp_query->max_num_pages ) {
        // You are not on the last page
      $template .= '
      <div class="posts__load-more">
        <a href="' . $this->get_url_vars(get_next_posts_page_link()) . '" class="button js-load-more">' . __('Load more', 'ajax-load-more-posts') . '</a>
      </div>';
    }

    $template = apply_filters('load-more-posts-page-navigation', $template );

    return $template;
  }
}
