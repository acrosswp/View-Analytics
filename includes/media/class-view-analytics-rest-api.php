<?php
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * The rest-api-facing functionality of the plugin.
 *
 * @link       https://acrosswp.com
 * @since      1.0.0
 *
 * @package    View_Analytics
 * @subpackage View_Analytics/rest-api
 */

 
 /**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    View_Analytics
 * @subpackage View_Analytics/View_Analytics_Media_Rest_Controller
 * @author     AcrossWP <contact@acrosswp.com>
 */
class View_Analytics_Media_Rest_Controller extends WP_REST_Controller {

    /**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * The ID of this media setting view.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $common;

    /**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
        $this->namespace     = '/'. $this->plugin_name .'/v1';
		$this->resource_name = 'attachment';

	}

    // Register our routes.
	public function register_routes() {

		register_rest_route(
			$this->namespace,
			'/' . $this->resource_name . '/(?P<key_id>\w+)',
			array(
				// Here we register the readable endpoint for collections.
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'who_view_media' ),
					'permission_callback' => array( $this, 'permissions_check' ),
				),
				// Register our schema callback.
				'schema' => array( $this, 'get_item_schema' ),
			)
		);
	}

	/**
	 * Check all the permission if the user has access to view the data from the endpoint or not
	 */
	public function permissions_check( $request ) {

        $retval = new WP_Error(
			'view_analytics_rest_authorization_required',
			__( 'Sorry, you are not allowed to perform this action.', 'view-analytics' ),
			array(
				'status' => rest_authorization_required_code(),
			)
		);

        $key_id = sanitize_text_field( $request->get_param( 'key_id' ) );
		if ( empty( $key_id ) ) {
			return new WP_Error(
				'view_analytics_rest_invalid_id',
				__( 'Invalid Attachment ID.', 'view-analytics' ),
				array(
					'status' => 404,
				)
			);
		}


		$this->common	= View_Analytics_Media_Common::instance();

		/**
		 * get the media details
		 */
		$details		= $this->common->table->get_details( $key_id );

		/**
		 * Get the Author ID
		 */
		$author_id	= empty( $details['author_id'] ) ? 0 : absint( $details['author_id'] );
		if( $this->common->access( $author_id, false, 'show_view_user_list' ) ) {
			return true;
		}

        return $retval;
    }

	/**
	 * Add Activity Meta to pin the comment in the Activity
	 *
	 * @param WP_REST_Request $request Current request.
	 */
	public function who_view_media( $request ) {

		global $wpdb;
		$mysql_time = $wpdb->get_var( 'select CURRENT_TIMESTAMP()' );

		$post_data 	= array();
		$schema 	= $this->get_item_schema();

		$key_id = sanitize_text_field( $request->get_param( 'key_id' ) );
		$media_detail = $this->common->table->get_details( $key_id );

		if ( empty( $media_detail ) ) {
			return rest_ensure_response( $post_data );
		}

		$users_list = unserialize( $media_detail['users_list'] );

		foreach ( $users_list as $user_id ) {

			/**
			 * Get the visitor
			 */
			$media_detail['viewer_id'] = $user_id;

			/**
			 * Action date
			 */
			$action_date = $this->common->table->get_user_log_view( $user_id, $key_id );
			$media_detail['action_date'] = $action_date['action_date'];

			$response = $this->prepare_item_for_response( $media_detail, $request, $mysql_time );
			$post_data[]   = $this->prepare_response_for_collection( $response );
		}

		return rest_ensure_response( $post_data );
	}

	/**
	 * Matches the post data to the schema we want.
	 *
	 * @param WP_Post $post The comment object whose response is being prepared.
	 */
	public function prepare_item_for_response( $media, $request, $mysql_time = false ) {

		$media_data = array();

		$schema = $this->get_item_schema();

		// We are also renaming the fields to more understandable names.
		if ( isset( $schema['properties']['id'] ) ) {
			$media_data['id'] = (int) $media['id'];
		}

		if ( isset( $schema['properties']['viewer_id'] ) ) {
			$media_data['user_id'] = (int) $media['viewer_id'];
		}

		if ( isset( $schema['properties']['key_id'] ) ) {
			$media_data['key_id'] = (int) $media['key_id'];
		}

		if ( isset( $schema['properties']['hash_id'] ) ) {
			$media_data['hash_id'] = (int) $media['hash_id'];
		}

		if ( isset( $schema['properties']['media_id'] ) ) {
			$media_data['media_id'] = (int) $media['media_id'];
		}

		if ( isset( $schema['properties']['key_id'] ) ) {
			$media_data['key_id'] = (int) $media['key_id'];
		}

		if ( isset( $schema['properties']['user_avatar_url'] ) ) {
			$media_data['user_avatar_url'] = get_avatar_url( $media['viewer_id'], 32 );
		}

		if ( isset( $schema['properties']['user_profile_url'] ) ) {
			$media_data['user_profile_url'] = bp_core_get_user_domain( $media['viewer_id'] );
		}

		if ( isset( $schema['properties']['user_profile_url'] ) ) {
			$media_data['user_profile_url'] = bp_core_get_user_domain( $media['viewer_id'] );
		}

		if ( isset( $schema['properties']['user_display_name'] ) ) {
			$media_data['user_display_name'] = bp_core_get_user_displayname( $media['viewer_id'] );
		}

		if ( isset( $schema['properties']['message'] ) ) {
			$media_data['message'] = $this->common->get_view_time_message( $media['action_date'], $mysql_time );
		}

		if ( isset( $schema['properties']['action_date'] ) ) {
			$media_data['action_date'] = $media['action_date'];
		}

		return rest_ensure_response( $media_data );
	}

	/**
	 * Get our sample schema for a post.
	 *
	 * @return array The sample schema for a post
	 */
	public function get_item_schema() {

		if ( $this->schema ) {
			// Since WordPress 5.3, the schema can be cached in the $schema property.
			return $this->schema;
		}

		$this->schema = array(
			// This tells the spec of JSON Schema we are using which is draft 4.
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			// The title property marks the identity of the resource.
			'title'      => 'acf-book',
			'type'       => 'object',
			// In JSON Schema you can specify object properties in the properties attribute.
			'properties' => array(
				'id'      => array(
					'description' => esc_html__( 'Unique identifier for the object.', 'view-analytics' ),
					'type'        => 'integer',
				),
				'viewer_id'  => array(
					'description' => esc_html__( 'The id of the user object', 'view-analytics' ),
					'type'        => 'integer',
				),
				'key_id'      => array(
					'description' => esc_html__( 'Unique identifier for the attachment.', 'view-analytics' ),
					'type'        => 'integer',
				),
				'media_id'      => array(
					'description' => esc_html__( 'Unique identifier for the BB media.', 'view-analytics' ),
					'type'        => 'integer',
				),
				'key_id'      => array(
					'description' => esc_html__( 'Unique identifier for the attachment.', 'view-analytics' ),
					'type'        => 'integer',
				),
				'user_avatar_url' => array(
					'description' => esc_html__( 'The Link of the User Profile Picture.', 'view-analytics' ),
					'type'        => 'string',
				),
				'user_profile_url' => array(
					'description' => esc_html__( 'The Link of the User Profile.', 'view-analytics' ),
					'type'        => 'string',
				),
				'user_display_name' => array(
					'description' => esc_html__( 'The display name of the users.', 'view-analytics' ),
					'type'        => 'string',
				),
				'message'   => array(
					'description' => esc_html__( 'The message that need to be diplay.', 'view-analytics' ),
					'type'        => 'string',
				),
				'action_date'   => array(
					'description' => esc_html__( 'The date on which the user view the Media', 'view-analytics' ),
					'type'        => 'string',
				),
			),
		);

		return $this->schema;
	}
}