<?php
/**
 * Plugin Name:			Storefront Footer Text
 * Plugin URI:			http://wooassist.com/
 * Description:			Lets you edit the footer credit text for Storefront theme easily from customizer.
 * Version:				1.0.1
 * Author:				Wooassist
 * Author URI:			http://wooassist.com/
 * Requires at least:	4.0.0
 * Tested up to:		5.2.3
 *
 * Text Domain: storefront-footer-text
 * Domain Path: /languages/
 *
 * @package Storefront_Footer_Text
 * @category Core
 * @author Wooassist
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Returns the main instance of Storefront_Footer_Text to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object Storefront_Footer_Text
 */
function Storefront_Footer_Text() {
	return Storefront_Footer_Text::instance();
} // End Storefront_Footer_Text()

Storefront_Footer_Text();

/**
 * Main Storefront_Footer_Text Class
 *
 * @class Storefront_Footer_Text
 * @version	1.0.0
 * @since 1.0.0
 * @package	Storefront_Footer_Text
 */
final class Storefront_Footer_Text {
	/**
	 * Storefront_Footer_Text The single instance of Storefront_Footer_Text.
	 * @var 	object
	 * @access  private
	 * @since 	1.0.0
	 */
	private static $_instance = null;

	/**
	 * The token.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $token;

	/**
	 * The version number.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $version;

	// Admin - Start
	/**
	 * The admin object.
	 * @var     object
	 * @access  public
	 * @since   1.0.0
	 */
	public $admin;

	/**
	 * Constructor function.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function __construct() {
		$this->token 			= 'storefront-footer-text';
		$this->plugin_url 		= plugin_dir_url( __FILE__ );
		$this->plugin_path 		= plugin_dir_path( __FILE__ );
		$this->version 			= '1.0.0';

		register_activation_hook( __FILE__, array( $this, 'install' ) );

		add_action( 'init', array( $this, 'woa_sfft_setup' ) );

		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'woa_sfft_plugin_links' ) );
	}

	/**
	 * Main Storefront_Footer_Text Instance
	 *
	 * Ensures only one instance of Storefront_Footer_Text is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see Storefront_Footer_Text()
	 * @return Main Storefront_Footer_Text instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) )
			self::$_instance = new self();
		return self::$_instance;
	} // End instance()


	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), '1.0.0' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), '1.0.0' );
	}

	/**
	 * Plugin page links
	 *
	 * @since  1.0.0
	 */
	public function woa_sfft_plugin_links( $links ) {
		$plugin_links = array(
			'<a href="https://wordpress.org/support/plugin/storefront-footer-text">' . __( 'Support', 'storefront-footer-text' ) . '</a>',
		);

		return array_merge( $plugin_links, $links );
	}

	/**
	 * Installation.
	 * Runs on activation. Logs the version number and assigns a notice message to a WordPress option.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function install() {
		$this->_log_version_number();

		if( 'storefront' != basename( TEMPLATEPATH ) ) {
			deactivate_plugins( plugin_basename( __FILE__ ) );
			wp_die( 'Sorry, you can&rsquo;t activate this plugin unless you have installed the Storefront theme.' );
		}

		// get theme customizer url
		$url = admin_url() . 'customize.php?';
		$url .= 'url=' . urlencode( site_url() . '?storefront-customizer=true' ) ;
		$url .= '&return=' . urlencode( admin_url() . 'plugins.php' );
		$url .= '&storefront-customizer=true';

		$notices 		= get_option( 'woa_sfft_activation_notice', array() );
		$notices[]		= sprintf( __( '%sThanks for installing the Storefront Footer Text extension. To get started, visit the %sCustomizer%s.%s %sOpen the Customizer%s', 'storefront-footer-text' ), '<p>', '<a href="' . esc_url( $url ) . '">', '</a>', '</p>', '<p><a href="' . esc_url( $url ) . '" class="button button-primary">', '</a></p>' );

		update_option( 'woa_sfft_activation_notice', $notices );
	}

	/**
	 * Log the plugin version number.
	 * @access  private
	 * @since   1.0.0
	 * @return  void
	 */
	private function _log_version_number() {
		// Log the version number.
		update_option( $this->token . '-version', $this->version );
	}

	/**
	 * Setup all the things.
	 * Only executes if Storefront or a child theme using Storefront as a parent is active and the extension specific filter returns true.
	 * @return void
	 */
	public function woa_sfft_setup() {
		$theme = wp_get_theme();

		if ( 'Storefront' == $theme->name || 'storefront' == $theme->template && apply_filters( 'Storefront_Footer_Text_supported', true ) ) {
			add_action( 'customize_register', array( $this, 'woa_sfft_customize_register') );
			add_action( 'admin_notices', array( $this, 'woa_sfft_customizer_notice' ) );
			// Hide the 'More' section in the customizer
			add_filter( 'storefront_customizer_more', '__return_false' );
			add_action( 'init', array( $this, 'woa_sfft_layout_adjustments' ),100 );
			
		}
	}

	/**
	 * Admin notice
	 * Checks the notice setup in install(). If it exists display it then delete the option so it's not displayed again.
	 * @since   1.0.0
	 * @return  void
	 */
	public function woa_sfft_customizer_notice() {
		$notices = get_option( 'woa_sfft_activation_notice' );

		if ( $notices = get_option( 'woa_sfft_activation_notice' ) ) {

			foreach ( $notices as $notice ) {
				echo '<div class="updated">' . $notice . '</div>';
			}

			delete_option( 'woa_sfft_activation_notice' );
		}
	}

	/**
	 * Customizer Controls and settings
	 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
	 */
	public function woa_sfft_customize_register( $wp_customize ) {
		/**
		 * Add new settings
		 */
		$wp_customize->add_setting( 'woa_sfft_footer_text', array(
        'default'           => "Custom Footer Text by Wooassist"));
		/**
		 * Add new controls and assigning the settings and it's section
		 */
		$wp_customize->add_control(
	        new WP_Customize_Control(
	            $wp_customize,
	            'woa_sfft_footer_text',
	            array(
	                'label'      => __( 'Footer Credit Text', 'storefront-footer-text' ),
					'description' => __( 'Enter your Credit Texts here.', 'storefront-footer-text' ),
	                'section'    => 'storefront_footer',
	                'settings'   => 'woa_sfft_footer_text',
	                'type'		 => 'textarea',
					'priority'      => 45,
	                )
	            )
	        );
	}
	/**
	 * Layout
	 * Adjusts the default Storefront layout when the plugin is active
	 */
	public function woa_sfft_layout_adjustments() {
			remove_action( 'storefront_footer', 'storefront_credit', 20 );
			add_action( 'storefront_footer', array( $this, 'woa_sfft_custom_storefront_credit' ),20 );
	}
	public function woa_sfft_custom_storefront_credit() {
		$options = array(
			'%current_year%',
			'%copy%'
		);
		$replace = array(
			date('Y'),
			'&copy;'
		);

		$new_footer_text = get_theme_mod( 'woa_sfft_footer_text' );
		$new_footer_text = str_replace( $options, $replace, get_theme_mod( 'woa_sfft_footer_text' ) );
			
		?>
		<div class="site-info">
			<?php echo do_shortcode( $new_footer_text ); ?>
		</div><!-- .site-info -->
		<?php
	}

} // End Class
