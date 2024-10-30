<?php
/*
Plugin Name: Minchu API Manager
Plugin URI: 
Description: みんちゅうの車両情報を表示するプラグイン
Version: 1.0.0
Author: vipkomatsu
Author URI: https://www.vip-soft.co.jp
License: GPL2
*/
?>
<?php
/*  Copyright 2019 komatsu (email : komatsu@vip-soft.jp)
 
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
?>
<?php
class MinchuApiPageTemplater {

	//A reference to an instance of this class.
	private static $instance;
	//The array of templates that this plugin tracks.
	protected $templates;

	//Returns an instance of this class.
	public static function get_instance() {
		if ( null == self::$instance ) {
			self::$instance = new MinchuApiPageTemplater();
		}
		return self::$instance;
	}

	//Initializes the plugin by setting filters and administration functions.
	private function __construct() {
		$this->templates = array();
		// Add a filter to the attributes metabox to inject template into the cache.
		if ( version_compare( floatval( get_bloginfo( 'version' ) ), '4.7', '<' ) ) {
			// 4.6 and older
			add_filter(
				'page_attributes_dropdown_pages_args',
				array( $this, 'register_project_templates' )
			);
		} else {
			// Add a filter to the wp 4.7 version attributes metabox
			add_filter(
				'theme_page_templates', array( $this, 'add_new_template' )
			);
		}

		// Add a filter to the save post to inject out template into the page cache
		add_filter(
			'wp_insert_post_data',
			array( $this, 'register_project_templates' )
		);
		// Add a filter to the template include to determine if the page has our
		// template assigned and return it's path
		add_filter(
			'template_include',
			array( $this, 'view_project_template')
		);
		// Add your templates to this array.
		$this->templates = array(
			'templates/page-minchu-cars.php' => 'みんちゅう車両一覧',
      'templates/page-minchu-car.php' => 'みんちゅう車両詳細'
		);
	}

	/**
	 * Adds our template to the page dropdown for v4.7+
	 *
	 */
	public function add_new_template( $posts_templates ) {
		$posts_templates = array_merge( $posts_templates, $this->templates );
		return $posts_templates;
	}

	/**
	 * Adds our template to the pages cache in order to trick WordPress
	 * into thinking the template file exists where it doens't really exist.
	 */
	public function register_project_templates( $atts ) {

		// Create the key used for the themes cache
		$cache_key = 'page_templates-' . md5( get_theme_root() . '/' . get_stylesheet() );

		// Retrieve the cache list.
		// If it doesn't exist, or it's empty prepare an array
		$templates = wp_get_theme()->get_page_templates();
		if ( empty( $templates ) ) {
			$templates = array();
		}

		// New cache, therefore remove the old one
		wp_cache_delete( $cache_key , 'themes');

		// Now add our template to the list of templates by merging our templates
		// with the existing templates array from the cache.
		$templates = array_merge( $templates, $this->templates );

		// Add the modified cache to allow WordPress to pick it up for listing
		// available templates
		wp_cache_add( $cache_key, $templates, 'themes', 1800 );

		return $atts;

	}

	/**
	 * Checks if the template is assigned to the page
	 */
	public function view_project_template( $template ) {
		// Return the search template if we're searching (instead of the template for the first result)
		if ( is_search() ) {
			return $template;
		}

		// Get global post
		global $post;

		// Return template if post is empty
		if ( ! $post ) {
			return $template;
		}

		// Return default template if we don't have a custom one defined
		if ( ! isset( $this->templates[get_post_meta(
			$post->ID, '_wp_page_template', true
		)] ) ) {
			return $template;
		}

		// Allows filtering of file path
		$filepath = apply_filters( 'page_templater_plugin_dir_path', plugin_dir_path( __FILE__ ) );

		$file =  $filepath . get_post_meta(
			$post->ID, '_wp_page_template', true
		);

		// Just to be safe, we check if the file exist first
		if ( file_exists( $file ) ) {
			return $file;
		} else {
			echo $file;
		}

		// Return template
		return $template;

	}

}
add_action( 'plugins_loaded', array( 'MinchuApiPageTemplater', 'get_instance' ) );
?>
<?php
// 1つ目、アクションフック
add_action( 'admin_menu', 'minchu_add_plugin_admin_menu' );
 
// 2つ目、アクションフックで呼ばれる関数
function minchu_add_plugin_admin_menu() {
     add_menu_page(
        'みんちゅうAPI設定', // page_title（オプションページのHTMLのタイトル）
        'みんちゅうAPI設定', // menu_title（メニューで表示されるタイトル）
        'administrator', // capability
        'minchu-api-setting', // menu_slug（URLのスラッグこの例だとoptions-general.php?page=hello-world）
        'minchu_display_plugin_admin_page', // function
        '',
        81
     );

  register_setting( 'minchu-setting-group', 'minchu_api_key' );
  register_setting( 'minchu-setting-group', 'page_minchu_list' );
  register_setting( 'minchu-setting-group', 'page_minchu_detail' );
}
 
// 3つ目、設定画面用のHTML
function minchu_display_plugin_admin_page() {
  $minchu_api_key = get_option('minchu_api_key');
  ?>
<div class="wrap">
  <h2>みんちゅうAPI設定</h2>
  <form method="post" action="options.php">
    <?php
       settings_fields( 'minchu-setting-group' );
       do_settings_sections( 'minchu-setting-group' );
      ?>
    <table class="form-table">
      <tbody>
        <tr>
          <th scope="row"><label for="minchu_api_key">みんちゅうAPI KEY</label></th>
          <td>
            <input type="text" name="minchu_api_key" id="minchu_api_key" size="50" value="<?php echo esc_attr( $minchu_api_key ); ?>" />
          </td>
        </tr>
        <tr>
          <th scope="row"><label for="minchu_api_key">車両一覧ページ</label></th>
          <td>
            <?php
              printf(
                wp_dropdown_pages(
                  array(
                    'name'              => 'page_minchu_list',
                    'echo'              => 0,
                    'show_option_none'  => __( '&mdash; Select &mdash;' ),
                    'option_none_value' => '0',
                    'selected'          => get_option( 'page_minchu_list' ),
                  )
                )
              );
            ?>
          </td>
        </tr>
        <tr>
          <th scope="row"><label for="minchu_api_key">車両詳細ページ</label></th>
          <td>
            <?php
              printf(
                wp_dropdown_pages(
                  array(
                    'name'              => 'page_minchu_detail',
                    'echo'              => 0,
                    'show_option_none'  => __( '&mdash; Select &mdash;' ),
                    'option_none_value' => '0',
                    'selected'          => get_option( 'page_minchu_detail' ),
                  )
                )
              );
            ?>
          </td>
        </tr>
      </tbody>
    </table>

    <?php submit_button(); ?>
  </form>
</div>
<?php } 

  function minchu_myplugin_load() {
    
    wp_enqueue_script('minchu_modernizr-2.7.1-csstransforms3d-canvas-touch_js', plugins_url('js/modernizr-2.7.1-csstransforms3d-canvas-touch.js', __FILE__ ), array(), '1.0', true);
    wp_enqueue_script('minchu_megapix-image_js', plugins_url('js/megapix-image.js', __FILE__ ), array(), '1.0', true);
    wp_enqueue_script('minchu_three.min_js', plugins_url('js/three.min.js', __FILE__ ), array(), '1.0', true);
    wp_enqueue_script('minchu_Detector_js', plugins_url('js/Detector.js', __FILE__ ), array(), '1.0', true);
    wp_enqueue_script('minchu_CSS3DRenderer_js', plugins_url('js/CSS3DRenderer.js', __FILE__ ), array(), '1.0', true);
    wp_enqueue_script('minchu_theta_viewer_js', plugins_url('js/theta-viewer.js', __FILE__ ), array(), '1.0', true);
    wp_enqueue_script('minchu_popup_js', plugins_url('js/popup.js', __FILE__ ), array(), '1.0', true);
    wp_enqueue_script('minchu_common_js', plugins_url('js/common.js', __FILE__ ), array(), '1.0', true);
    
    wp_register_style('minchu_config', plugins_url('css/config.css', __FILE__));
    wp_register_style('minchu_common', plugins_url('css/common.css', __FILE__));
    wp_register_style('minchu_style_list', plugins_url('css/style-list.css', __FILE__));
    wp_register_style('minchu_style_detail', plugins_url('css/style-detail.css', __FILE__));
    wp_enqueue_style('minchu_config');
    wp_enqueue_style('minchu_common');
    wp_enqueue_style('minchu_style_list');
    wp_enqueue_style('minchu_style_detail');
  }
  add_action('wp_enqueue_scripts', 'minchu_myplugin_load');
?>
