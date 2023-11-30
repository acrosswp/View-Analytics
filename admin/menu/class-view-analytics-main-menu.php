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

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    View_Analytics
 * @subpackage View_Analytics/admin
 * @author     AcrossWP <contact@acrosswp.com>
 */
class View_Analytics_Admin_Main_Menu {

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
	 * Stroe all the sub menu of view analytics
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    sub menu of view analytics
	 */
	private $tabs;
	
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		
		$this->plugin_name = $plugin_name;
		$this->version = $version;

		add_action( 'admin_notices', array( $this, 'tabs' ) );
	}

	/**
	 * Load the menu into the Admin Dashboard area
	 */
	public function menu() {

		add_submenu_page(
			ACROSSWP_MAIN_MENU,
			__( 'Abouts', 'view-analytics' ),
			__( 'View Analytics', 'view-analytics' ),
			'manage_options',
			'view-analytics',
			array( $this, 'about_view_analytics' )
		);
	}

	/**
	 * Add Navigation tab on top of the page View Analytics
	 *
	 * @since BuddyBoss 1.0.0
	 */
	function tabs() {
		global $pagenow, $current_screen;

		if ( 
			'view-analytics' == $current_screen->parent_base
			|| 'acrosswp_page_view-analytics' == $current_screen->base
		) {
			?>
			<div class="wrap">
				<h2 class="nav-tab-wrapper"><?php $this->view_analytics_tabs(); ?></h2>
			</div>
			<?php
		}
	}

	/**
	 * Set the tab for the plugins
	 */
	function set_tabs() {

		global $submenu;

		foreach( $submenu['acrosswp'] as $child_menu ) {
			if( ! empty( $child_menu[2] ) && 'view-analytics' == $child_menu[2] ) {
				$this->tabs[] = $child_menu;
			}
		}

		foreach( $submenu['view-analytics'] as $child_menu ) {
			$this->tabs[] = $child_menu;
		}
	}

	/**
	 * Output the tabs in the admin area.
	 *
	 * @since BuddyBoss 1.0.0
	 *
	 * @param string $active_tab Name of the tab that is active. Optional.
	 */
	function view_analytics_tabs() {

		$this->set_tabs();
		$active_tab = $_GET['page'];

		$tabs_html    = '';
		$idle_class   = 'nav-tab';
		$active_class = 'nav-tab nav-tab-active';

		// Loop through tabs and build navigation.
		foreach ( array_values( $this->tabs ) as $tab_data ) {
			$is_current = (bool) ( $tab_data[2] == $active_tab );
			$tab_class  = $is_current ? $tab_data[2] . ' ' . $active_class : $tab_data[2] . ' ' . $idle_class;
			$tabs_html .= '<a href="' . esc_url( admin_url( 'admin.php?page='.$tab_data[2]  ) ) . '" class="' . esc_attr( $tab_class ) . '">' . esc_html( $tab_data[3] ) . '</a>';
		}

		echo $tabs_html;
	}

	/**
	 * Show the content on the main menu
	 */
	function about_view_analytics() {
		?>
		<style>
			.acrosswp-container {
				display: flex;
				flex-direction: column;
				align-items: center;
				justify-content: center;
				height: 100vh;
				background-color: #f7f7f7;
			}
	
			.acrosswp-logo img {
				max-width: 200px;
				height: auto;
			}
	
			.acrosswp-content {
				text-align: center;
				max-width: 600px;
				margin-top: 20px;
				padding: 20px;
				background-color: #fff;
				border-radius: 10px;
				box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
			}
	
			h2 {
				color: #0073e6;
				font-size: 24px;
			}
	
			h3 {
				color: #333;
				font-size: 20px;
			}
	
			ul {
				list-style-type: disc;
				padding-left: 20px;
				text-align: left;
			}
	
			p {
				font-size: 18px;
			}
		</style>
	
		<div class="acrosswp-container">
			<div class="acrosswp-logo">
				<img src="https://example.com/your-image.jpg" alt="AcrossWP Logo">
			</div>
	
			<div class="acrosswp-content">
				<h2>At AcrossWP</h2>
				<p style="text-align: left;">We understand the importance of customizing and creating plugins for WordPress to meet our clients’ unique needs.</p>
				<p style="text-align: left;">Our team of skilled developers has extensive experience in customizing and creating WordPress plugins that are tailored to our clients’ requirements.</p>
	
				<h3>Our Specializations:</h3>
				<ul>
					<li><strong>E-commerce Development:</strong> We specialize in developing e-commerce sites using WooCommerce, the most popular and widely used e-commerce platform in the world. Our team is proficient in building custom online stores that are visually appealing, user-friendly, and highly functional.</li>
	
					<li><strong>Social Networking:</strong> We also specialize in building social networking sites using AcrossWP. Whether you’re looking to build a community for your brand, an online marketplace, or a social network for a particular niche, our team has the expertise to deliver a social networking site that meets your needs.</li>
	
					<li><strong>Learning Management Systems (LMS):</strong> Furthermore, we have extensive experience in integrating Learning Management Systems (LMS) using LearnDash plugins. We believe that e-learning is the future, and we work tirelessly to develop online learning platforms that are engaging, interactive, and easy to use.</li>
				</ul>
	
				<h3>Why Choose Us?</h3>
				<ul>
					<li>Experienced and dedicated developers</li>
					<li>Custom solutions tailored to your unique requirements</li>
					<li>Stunning and user-friendly website designs</li>
					<li>Timely project delivery</li>
					<li>Exceptional customer support</li>
				</ul>
	
				<p>We are committed to delivering high-quality web development services that help our clients achieve their business goals.</p>
	
				<p>Contact us today to learn more about how we can help you with customizing and creating plugins for WordPress, building e-commerce sites, developing social networking sites, or integrating LMS using LearnDash plugins.</p>
			</div>
		</div>
		<?php
	}
}