<?php
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://acrosswp.com
 * @since      1.0.0
 *
 * @package    View_Analytics
 * @subpackage View_Analytics/admin
 */

if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * The admin-specific functionality of the plugin class that will display our custom table records in nice table
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    View_Analytics
 * @subpackage View_Analytics/admin
 * @author     AcrossWP <contact@acrosswp.com>
 */
class View_Analytics_List_Media_Table extends WP_List_Table {

	/**
	 * The ID of this media setting view.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $common;


	/**
	 * The Post per page settings
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $per_page;

    /**
     * [REQUIRED] You must declare constructor and give some basic params
     */
    function __construct() {
        global $status, $page;

        parent::__construct(array(
            'singular'  => 'media',
            'plural'    => 'medias',
            'ajax'      => false      
        ));

		$this->common = View_Analytics_Media_Common::instance();

		$this->per_page = 20;
    }

    protected function get_views() {

        $url = admin_url( 'admin.php?page=view-analytics-media' );

        $status_links['all'] = sprintf( __( "<a href='%s'>All</a>", 'view-analytics' ), $url );
        
        foreach( $this->common->media_types() as $media_type ) {
            $status_links[ $media_type ] = sprintf( __( "<a href='%s&media_type=%s'>%s</a>", 'view-analytics' ), $url, $media_type, ucfirst( $media_type ) );
        }

        return $status_links;
    }

    /**
     * [REQUIRED] this is a default column renderer
     *
     * @param $item - row (key, value array)
     * @param $column_name - string (key)
     * @return HTML
     */
    function column_default( $item, $column_name ) {
        return $item[ $column_name ];
    }

    /**
     * [OPTIONAL] this is example, how to render column with actions,
     * when you hover row "Edit | Delete" links showed
     *
     * @param $item - row (key, value array)
     * @return HTML
     */
    function column_id( $item ) {
        $actions = array(
            'view_log' => sprintf('<a href="?page=view-analytics-media&id=%s">%s</a>', $item['id'], __( 'View Log', 'view-analytics' ) ),
            'delete' => sprintf('<a href="?page=%s&action=delete&id=%s">%s</a>', $_REQUEST['page'], $item['id'], __( 'Delete', 'view-analytics' ) ),
        );

        return sprintf('%s %s',
            $item['id'],
            $this->row_actions( $actions )
        );
    }

    /**
     * [REQUIRED] this is how checkbox column renders
     *
     * @param $item - row (key, value array)
     * @return HTML
     */
    function column_cb( $item ) {
        return sprintf(
            '<input type="checkbox" name="id[]" value="%s" />',
            $item['id']
        );
    }

    /**
     * [REQUIRED] this is how checkbox column renders
     *
     * @param $item - row (key, value array)
     * @return HTML
     */
    function column_type( $item ) {
        return ucfirst( $item['type'] );
    }

    /**
     * [REQUIRED] This method return columns to display in table
     * you can skip columns that you do not want to show
     * like content, or description
     *
     * @return array
     */
    function get_columns() {
        $columns = array(
            'cb' => '<input type="checkbox" />', //Render a checkbox instead of text
            'id' => __( 'ID', 'view-analytics'),
            'key_id' => __( 'Media ID', 'view-analytics'),
            'type' => __( 'Media Type', 'view-analytics'),
            'mime_type' => __( 'Mime Type', 'view-analytics'),
            'user_count' => __( 'User Count', 'view-analytics'),
            'ref_count' => __( 'View', 'view-analytics'),
            'session_count' => __( 'Session View', 'view-analytics'),
        );
        return $columns;
    }

    /**
     * [OPTIONAL] This method return columns that may be used to sort table
     * all strings in array - is column names
     * notice that true on name column means that its default sort
     *
     * @return array
     */
    function get_sortable_columns() {
        $sortable_columns = array(
            'user_count' => array( 'user_count', false),
            'ref_count' => array( 'ref_count', false),
            'session_count' => array( 'session_count', false),
        );
        return $sortable_columns;
    }

    /**
     * [OPTIONAL] Return array of bult actions if has any
     *
     * @return array
     */
    function get_bulk_actions() {
        $actions = array(
            'delete' => __( 'Delete', 'view-analytics')
        );
        return $actions;
    }

    /**
     * [OPTIONAL] This method processes bulk actions
     * it can be outside of class
     * it can not use wp_redirect coz there is output already
     * in this example we are processing delete action
     * message about successful deletion will be shown on page in next part
     */
    function process_bulk_action() {
        global $wpdb;
        $table_name = $this->common->table->table_name();
        $table_name_log = $this->common->table->table_name_log();

        if ( 'delete' === $this->current_action() ) {
            
            $ids = isset( $_REQUEST['id']) ? $_REQUEST['id'] : array();

            if ( is_array( $ids ) ) {
                $ids = implode(',', $ids);
            }

            if ( ! empty( $ids ) ) {
                $this->common->table->delete( $ids );
            }
        }
    }

    /**
     * [REQUIRED] This is the most important method
     *
     * It will get rows from database and prepare them to be showed in table
     */
    function prepare_items() {

        global $wpdb;

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        // here we configure table headers, defined in our methods
        $this->_column_headers = array( $columns, $hidden, $sortable );

        // [OPTIONAL] process bulk action if any
        $this->process_bulk_action();

        $media_type = isset( $_GET['media_type'] ) ? $_GET['media_type'] : false;

        // will be used in pagination settings
        $total_items = $this->common->table->get_all_details_count( $media_type );

        // prepare query params, as usual current page, order by and order direction
        
        $paged = isset( $_GET['paged'] ) ? max( 0, intval( $_GET['paged'] - 1 ) * $this->per_page ) : 0;
        
        $orderby = ( isset( $_GET['orderby'] ) && in_array( $_GET['orderby'], array_keys( $this->get_sortable_columns() ) ) ) ? $_GET['orderby'] : 'ref_count';
        
        $order = ( isset($_GET['order'] ) && in_array( $_GET['order'], array( 'asc', 'desc') ) ) ? $_GET['order'] : 'desc';

        // [REQUIRED] define $items array
        // notice that last argument is ARRAY_A, so we will retrieve array
        $this->items = $this->common->table->get_all_details( $orderby, $order, $this->per_page, $paged, $media_type );

        // [REQUIRED] configure pagination
        $this->set_pagination_args(array(
            'total_items' => $total_items, // total items defined above
            'per_page' => $this->per_page, // per page constant defined at top of method
            'total_pages' => ceil($total_items / $this->per_page ) // calculate pages count
        ));
    }
}