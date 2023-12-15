<?php
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Register all actions and filters for the plugin
 *
 * @link       https://acrosswp.com
 * @since      1.0.0
 *
 * @package    View_Analytics
 * @subpackage View_Analytics/includes
 */

/**
 * Register all actions and filters for the plugin.
 *
 * Maintain a list of all hooks that are registered throughout
 * the plugin, and register them with the WordPress API. Call the
 * run function to execute the list of actions and filters.
 *
 * @package    View_Analytics
 * @subpackage View_Analytics/includes
 * @author     AcrossWP <contact@acrosswp.com>
 */
class View_Analytics_Common {

	/**
	 * The single instance of the class.
	 *
	 * @var View_Analytics_Loader
	 * @since 1.0.0
	 */
	protected static $_instance = null;

	/**
	 * The ID of this media setting view.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	public $table;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {}

	/**
	 * Main View_Analytics_Loader Instance.
	 *
	 * Ensures only one instance of WooCommerce is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see View_Analytics_Loader()
	 * @return View_Analytics_Loader - Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
     * Return the View Analytics Media Count Key
     */
    public function delete_table_count_key() {
        return '_view_analytics_delete_table_key';
    }

	/**
	 * Create table
	 */
	public function default_value() {
		return array(
			'main' => 0,
		);
	}

	/**
	 * Create table
	 */
	public function get_key_default_value( $key ) {
		$default_value = $this->default_value();
		return isset( $default_value[ $key ] ) ? $default_value[ $key ] : false;
	}

	/**
	 * Create fiilter name by given key
	 */
	public function create_hooks_key( $key = '' ) {
		return $this->view_count_key() . '_' . $key;
	}

	/**
     * Return the View Analytics Media Count Key
     */
    public function get_view_setting() {
        return get_option( $this->view_count_key(), false );
    }

	/**
     * Return the View Analytics Avatar Count Key
     */
    public function get_view_setting_active( $key ) {
        $settings = $this->get_view_setting();
		return empty( $settings[ $key ] ) ? false : $settings[ $key ];
    }

	/**
     * Return the View Analytics Avatar Count Key
     */
    public function view_count_enable() {
        return $this->get_view_setting_active( 'main' );
    }


	/**
     * Return the View Analytics show count
	 * 
	 * $author_id = 0. Here this is added for the Media to view by the Non login user as well
     */
    public function access( $author_id = 0, $group_id = false, $key = false, $value = false ) {


		$key = empty( $key ) ? 'show_view_count' : $key;
		
		if( ! $this->view_count_enable() ) {
			return false;
		}

		/**
		 * if the user is the admin then return true
		 */
		if ( $this->is_admin( $author_id, $group_id ) ) {
			return true;
		}
		
		if ( ! $this->get_view_setting_active( $key ) ) {
			return false;
		}

		if ( ! empty( $author_id ) ) {
			/**
			 * If group id is there check for the Group Admin and Moderations
			 */
			if ( ! empty( $group_id )  ) {
				$value = groups_is_user_member( $author_id, $group_id );
			} else {
				$value = $this->is_author( $author_id );
			}
		}

		return apply_filters( $this->create_hooks_key( $key ), $value );
    }

	/**
	 * Removes the current session token from the database.
	 *
	 * @since 4.0.0
	 */
	function wp_get_current_session() {

		$session = false;
		$token = wp_get_session_token();
		if ( $token ) {
			$manager = WP_Session_Tokens::get_instance( get_current_user_id() );
			$session = $manager->get( $token );
			$session = $session['login'];
		}
		
		return $session;
	}
    
	/**
     * Return the View Analytics Media Count Key
     */
    public function get_filter_post_value( $key, $filter = FILTER_VALIDATE_INT ) {
		return filter_input( INPUT_POST,  $key, $filter );
    }

	/**
     * Return if BuddyBoss is active or not
     */
    public function is_buddyboss() {
		return defined( 'BP_PLATFORM_VERSION' );
    }

	/**
	 * Check if the current user is admin or if you want to check of the user is Group admin
	 */
	public function is_admin( $author_id = false, $group_id = false ) {

		/**
         * If user is site admin
         */
        if( current_user_can('administrator') ) {
            return true;
        }

		if( 
			! empty( $group_id ) 
			&& ! empty( $author_id )
			&& (
				groups_is_user_admin( $author_id, $group_id )  || groups_is_user_mod( $author_id, $group_id ) 
			)
		) {
			return true;
		}

		return false;
	}
	
	/**
	 * Check if the current user is allow to view the Media View List
	 */
	public function is_author( $author_id ) {
		return ( get_current_user_id() == $author_id ) ? true : false;
	}

	/**
	 * Return all the media type
	 */
	public function media_types() {
		return array( 'photo', 'video', 'document' );
	}


	/**
	 * Get the components of the current Group and Profile
	 * For Media view it is getting overwrittin in the Media Common file
	 */
	public function get_components( $slug, $default_component = '' ) {
		
		global $wp;
		
		$current_url = sanitize_text_field( $wp->request );
		$group_slug = sanitize_text_field( $slug );
		$group_slug = $group_slug .'/';

		/**
		 * Strting to array for the current url
		 */
		$current_url_array = explode( $group_slug, $current_url );
		
		/**
		 * Get the URL after the Group SLUG
		 */
		$components = empty( $current_url_array[1] ) ? false: $current_url_array[1];
		$components = empty( $components ) ? false: explode( '/', $components );


		$single_components = empty( $components[0] ) ? $default_component : $components[0];
		$single_object = empty( $components[1] ) ? '' : $components[1];
		$single_primitive = '';
		$single_variable = '';

		if ( ! empty( $components[2] ) ) {
			unset( $components[0] );
			unset( $components[1] );

			$single_primitive = implode( '/', $components );
		}

		if ( ! empty( $_GET ) ) {
			foreach( $_GET as $key => $value ) {

				$key = sanitize_text_field( $key );
				$value = sanitize_text_field( $value );

				if ( ! empty ( $single_variable ) ) {
					$single_variable .= '&';
				} else {
					$single_variable .= '?';
				}

				$single_variable .= $key . '=' . $value;
			}
		}

		return array(
			'url' => $current_url,
			'components' => $single_components,
			'object' => $single_object,
			'primitive' => $single_primitive,
			'variable' => $single_variable,
		);
	}
}
