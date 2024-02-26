<?php
/**
 * BuddyBoss Compatibility Integration Class.
 *
 * @since AcrossWP 1.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://acrosswp.com
 * @since      1.0.0
 *
 * @package    View_Analytics_BuddyBoss_Integration_Media_Notifications
 * @subpackage View_Analytics_BuddyBoss_Integration_Media_Notifications/public
 */
if ( class_exists( 'BP_Core_Notification_Abstract' ) ) {

    /**
     * The public-facing functionality of the plugin.
     *
     * Defines the plugin name, version, and two examples hooks for how to
     * enqueue the public-facing stylesheet and JavaScript.
     *
     * @package    View_Analytics_BuddyBoss_Integration_Media_Notifications
     * @subpackage View_Analytics_BuddyBoss_Integration_Media_Notifications/public
     * @author     AcrossWP <contact@acrosswp.com>
     */
    class View_Analytics_BuddyBoss_Integration_Media_Notifications extends BP_Core_Notification_Abstract {
 
        /**
         * Instance of this class.
         *
         * @var object
         */
        private static $instance = null;

        /**
         * The ID of this plugin.
         *
         * @since    1.0.0
         * @access   private
         * @var      string    $plugin_name    The ID of this plugin.
         */
        private $plugin_name;

        /**
         * The Custom ID of this plugin.
         *
         * @since    1.0.0
         * @access   private
         * @var      string    $plugin_name    The Custom ID of this plugin.
         */
        private $plugin_name_action;

        /**
         * The Custom ID of this plugin.
         *
         * @since    1.0.0
         * @access   private
         * @var      string    $plugin_name    The Custom ID of this plugin.
         */
        private $plugin_name_message;

        /**
         * The ID of this media setting view.
         *
         * @since    1.0.0
         * @access   private
         * @var      string    $version    The current version of this plugin.
         */
        private $common;
     
        /**
         * Get the instance of this class.
         *
         * @return null|View_Analytics_BuddyBoss_Integration_Media_Notifications|Controller|object
         */
        public static function instance() {
            if ( is_null( self::$instance ) ) {
                self::$instance = new self();
            }
     
            return self::$instance;
        }
     
        /**
         * Constructor method.
         */
        public function __construct( $plugin_name ) {

            $this->plugin_name = $plugin_name;
		    $this->plugin_name_action = 'media_action';
		    $this->plugin_name_message = $plugin_name . '-media_message';

            $this->common = View_Analytics_Media_Common::instance();

            $this->start();

        }
     
        /**
         * Initialize all methods inside it.
         *
         * @return mixed|void
         */
        public function load() {
     
            /**
             * Register Notification Group.
             *
             * @param string $group_key         Group key.
             * @param string $group_label       Group label.
             * @param string $group_admin_label Group admin label.
             * @param int    $priority          Priority of the group.
             */
            $this->register_notification_group(
                $this->plugin_name,
                esc_html__( 'Notification on Your Media View', 'view-analytics-pro' ), // For the frontend.
                esc_html__( 'Notification on Author Media View', 'view-analytics-pro' ) // For the backend.
            );
     
            $this->register_custom_notification();
        }
     
        /**
         * Register notification for user mention.
         */
        public function register_custom_notification() {

            $notification_read_only    = true;
            $notification_tooltip_text = __( 'Requires Media Component to enable', 'view-analytics-pro' );

            if ( function_exists( 'bp_is_active' ) && true === bp_is_active( 'media' ) ) {
                $notification_tooltip_text = __( 'Required by Media Component', 'view-analytics-pro' );
                $notification_read_only    = false;
            }

            /**
             * Register Notification Type.
             *
             * @param string $notification_type        Notification Type key.
             * @param string $notification_label       Notification label.
             * @param string $notification_admin_label Notification admin label.
             * @param string $notification_group       Notification group.
             * @param bool   $default                  Default status for enabled/disabled.
             */
            $this->register_notification_type(
                $this->plugin_name_action,
                esc_html__( 'A member view your post', 'view-analytics-pro' ),
                esc_html__( 'Member view some author post', 'view-analytics-pro' ),
                $this->plugin_name,
                function_exists( 'function_exists' ) && true === bp_is_active( 'media' ),
                $notification_read_only,
                $notification_tooltip_text
            );
     
            /**
             * Add email schema.
             *
             * @param string $email_type        Type of email being sent.
             * @param array  $args              Email arguments.
             * @param string $notification_type Notification Type key.
             */
            $this->register_email_type(
                $this->plugin_name_message,
                array(
                    /* translators: do not remove {} brackets or translate its contents. */
                    'email_title'         => __( '[{{{site.name}}}] {{poster.name}} view your Media', 'view-analytics-pro' ),
                    /* translators: do not remove {} brackets or translate its contents. */
                    'email_content'       => __( "<a href=\"{{{poster_like.url}}}\">{{poster.name}}</a> have view your <a href=\"{{{media.url}}}\">Media</a>", 'view-analytics-pro' ),
                    /* translators: do not remove {} brackets or translate its contents. */
                    'email_plain_content' => __( "{{poster.name}} have view your media.\n\Media link: {{{media.url}}}", 'view-analytics-pro' ),
                    'situation_label'     => __( 'A posts author get view by members', 'view-analytics-pro' ),
                    'unsubscribe_text'    => __( 'You will no longer receive emails when someone view your medias.', 'view-analytics-pro' ),
                ),
                $this->plugin_name_action
            );
     
            /**
             * Register notification.
             *
             * @param string $component         Component name.
             * @param string $component_action  Component action.
             * @param string $notification_type Notification Type key.
             * @param string $icon_class        Notification Small Icon.
             */
            $this->register_notification(
                $this->plugin_name,
                $this->plugin_name_action,
                $this->plugin_name_action,
                ''
            );
     
            /**
             * Register Notification Filter.
             *
             * @param string $notification_label    Notification label.
             * @param array  $notification_types    Notification types.
             * @param int    $notification_position Notification position.
             */
            $this->register_notification_filter(
                __( 'Custom Notification Filter', 'view-analytics-pro' ),
                array( $this->plugin_name_action ),
                5
            );
        }
     
        /**
         * Format the notifications.
         *
         * @param string $content               Notification content.
         * @param int    $item_id               Notification item ID.
         * @param int    $secondary_item_id     Notification secondary item ID.
         * @param int    $action_item_count     Number of notifications with the same action.
         * @param string $component_action_name Canonical notification action.
         * @param string $component_name        Notification component ID.
         * @param int    $notification_id       Notification ID.
         * @param string $screen                Notification Screen type.
         *
         * @return array
         */
        public function format_notification( $content, $item_id, $secondary_item_id, $total_items, $component_action_name, $component_name, $notification_id, $screen ) {


            if ( 
                $this->plugin_name === $component_name 
                && $this->plugin_name_action === $component_action_name 
            ) {

                $name = bp_core_get_user_displayname( $secondary_item_id );
            
    
                $custom_text = sprintf( esc_html__( '%s view on your media', 'view-analytics-pro' ), $name );


                $media_details = $this->common->table->get_details( $item_id );
                $media_id = 0;
                if ( ! empty( $media_details ) ) {
                    $media_id = $media_details['media_id'];
                }
                $media_url = bp_media_get_preview_image_url( $media_id, $item_id, 'bb-media-activity-image', true, $secondary_item_id );

                $custom_link = add_query_arg( 'rid', (int) $notification_id, $media_url );
                
   
                $filter = $this->common->create_hooks_key( 'users_view_media_filter' );

                $content = apply_filters( $filter, array(
                    'text' => $custom_text,
                    'link' => $custom_link
                ), $custom_link, (int) $total_items, $custom_text );
            }
			
			return $content;
        }

    }
}
