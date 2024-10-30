<?php

namespace HexReport\App\Core;

use HexReport\App\Core\Lib\SingleTon;

use CodesVault\Howdyqb\DB;

class AdminNoticeManager
{
	use SingleTon;

	private $woocommerce_plugin_url = 'woocommerce/woocommerce.php';

	/**
	 * @package hexreport
	 * @author WpHex
	 * @method register
	 * @return mixed
	 * @since 1.0.0
	 * Add all the necessary hooks that are needed.
	 */
	public function register()
	{
		// Hook for displaying a notice to activate and install the 'WooCommcrce' plugin
		add_action( 'admin_notices', [ $this, 'show_active_and_installation_notice_for_woocommerce' ] );
		// Hook for displaying a notice for checking the 'WordPress' version.
		add_action('admin_notices', [ $this, 'wordpress_version_notice' ] );
		// Hook for displaying a notice for checking the 'WooCommerce' version.
		add_action('admin_notices', [ $this, 'woocommerce_version_notice' ] );
		// Hook for displaying a notice for checking the 'PHP' version.
		add_action('admin_notices', [ $this, 'php_version_notice' ] );
	}

	/**
	 * @package hexreport
	 * @author WpHex
	 * @method get_wp_version
	 * @return string
	 * @since 1.0.0
	 * Get WordPress version.
	 */
	private function get_wp_version()
	{
		$current_wp_version = get_bloginfo( 'version' );
		return $current_wp_version;
	}

	/**
	 * @package hexreport
	 * @author WpHex
	 * @method get_current_php_version
	 * @return string
	 * @since 1.0.0
	 * Get current PHP version.
	 */
	private function get_current_php_version()
	{
		$php_version = phpversion();
		return $php_version;
	}

	/**
	 * @package hexreport
	 * @author WpHex
	 * @method get_plugin_wp_version
	 * @return string
	 * @since 1.0.0
	 * Get WordPress version from plugin meta-data.
	 */
	private function get_plugin_wp_version()
	{
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

		$plugin_data = get_plugin_data( plugin_dir_path(__FILE__) . '../../hexreport.php' );
		$plugin_version = $plugin_data['RequiresWP'];

		return $plugin_version;
	}

	/**
	 * @package hexreport
	 * @author WpHex
	 * @method get_plugin_wc_version
	 * @return string
	 * @since 1.0.0
	 * Get WooCommerce version from plugin meta-data.
	 */
	private function get_plugin_wc_version()
	{
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

		$plugin_data = get_plugin_data( plugin_dir_path(__FILE__) . '../../hexreport.php' );
		$plugin_version = ! empty( $plugin_data['WC requires at least'] ) ? $plugin_data['WC requires at least'] : '';

		return $plugin_version;
	}

	/**
	 * @package hexreport
	 * @author WpHex
	 * @method get_plugin_php_version
	 * @return string
	 * @since 1.0.0
	 * Get PHP version from plugin meta data.
	 */
	private function get_plugin_php_version()
	{
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

		$plugin_data = get_plugin_data( plugin_dir_path(__FILE__) . '../../hexreport.php' );
		$plugin_version = $plugin_data['RequiresPHP'];
		return $plugin_version;
	}

	/**
	 * @package hexreport
	 * @author WpHex
	 * @method show_`install`action_notice_for_woocommerce
	 * @since 1.0.0
	 * Display the 'WooCommerce' installation notice after 'Hexreport' plugin activation.
	 */
	public function show_active_and_installation_notice_for_woocommerce()
	{
		$installation_message = $this->get_woocommerce_install_notice_message();
		$activation_message = $this->get_woocommerce_active_notice_message();

		$allowed_html_tags = [
			'a' => [
				'href' => [],
			]
		];

		$all_plugins_list = get_plugins();

		if (  ! array_key_exists( 'woocommerce/woocommerce.php', $all_plugins_list ) ) {
			?>
			<div class="notice notice-error is-dismissible">
				<p><?php echo wp_kses( $installation_message, $allowed_html_tags ); ?></p>
			</div>
			<?php
		}
		else if ( ! class_exists( 'WooCommerce' ) ) {
			?>
			<div class="notice notice-error is-dismissible">
				<p><?php echo wp_kses( $activation_message, $allowed_html_tags ); ?></p>
			</div>
			<?php
		}
	}

	/**
	 * @package hexreport
	 * @author WpHex
	 * @method get_woocommerce_active_notice_message
	 * @return string
	 * @since 1.0.0
	 * Renders a message for WooCommerce activation notice for the users.
	 * */
	private function get_woocommerce_active_notice_message()
	{
		$activate_url = wp_nonce_url( admin_url( 'plugins.php?action=activate&plugin=' . urlencode( $this->woocommerce_plugin_url ) ), 'activate-plugin_' . $this->woocommerce_plugin_url );
		return sprintf( __( 'WooCommerce plugin is not active. Please <a href="%s">activate the WooCommerce</a> plugin to use HexReport features.','hexreport' ), esc_url( $activate_url ) );
	}

	/**
	 * @package hexreport
	 * @author WpHex
	 * @method get_woocommerce_install_notice_message
	 * @return string
	 * @since 1.0.0
	 * Renders a message for WooCommerce installation notice for the users.
	 * */
	private function get_woocommerce_install_notice_message()
	{
		$install_url = wp_nonce_url( admin_url( 'update.php?action=install-plugin&plugin=woocommerce' ), 'install-plugin_woocommerce' );
		return sprintf( __( 'WooCommerce plugin is not installed. Please <a href="%s">install the WooCommerce plugin</a> to use HexReport features.','hexreport' ), esc_url( $install_url ) );
	}

	/**
	 * @package hexreport
	 * @author WpHex
	 * @method get_wordpress_version_message
	 * @return string
	 * @since 1.0.0
	 * Renders a message for WordPress version notice for the users.
	 * */
	private function get_wordpress_version_message()
	{
		$wp_version = $this->get_plugin_wp_version();

		return sprintf(
			esc_html__( 'This plugin requires at least WordPress version of %s', 'hexreport' ),
			esc_html( $wp_version )
		);
	}

	/**
	 * @package hexreport
	 * @author WpHex
	 * @method get_woocommerce_version_message
	 * @return string
	 * @since 1.0.0
	 * Renders a message for WooCommerce version notice for the users.
	 * */
	private function get_woocommerce_version_message()
	{
		$wc_version = $this->get_plugin_wc_version();

		return sprintf(
			esc_html__( 'This plugin requires at least WooCommerce version of %s', 'hexreport' ),
			esc_html( $wc_version )
		);
	}

	/**
	 * @package hexreport
	 * @author WpHex
	 * @method get_php_version_message
	 * @return string
	 * @since 1.0.0
	 * Renders a message for PHP version notice for the users.
	 * */
	private function get_php_version_message()
	{
		$php_version = $this->get_plugin_php_version();

		return sprintf(
			esc_html__( 'This plugin requires at least PHP version of %s', 'hexreport' ),
			esc_html( $php_version )
		);
	}

	/**
	 * @package hexreport
	 * @author WpHex
	 * @method wordpress_version_notice
	 * @since 1.0.0
	 * Renders admin notice for WordPress version checking.
	 * */
	public function wordpress_version_notice()
	{
		$current_wp_version = $this->get_wp_version();
		$plugin_wp_version = $this->get_plugin_wp_version();

		if ( $current_wp_version < $plugin_wp_version ) {
			$wp_version_notice_message = $this->get_wordpress_version_message();
		}
		if ( ! empty( $wp_version_notice_message ) ) {
		?>
		<div class="notice notice-info is-dismissible">
			<p>
				<?php printf( esc_html__( '%s', 'hexreport' ), esc_html( $wp_version_notice_message ) ); ?>
			</p>
		</div>
		<?php
		}
	}

	/**
	 * @package hexreport
	 * @author WpHex
	 * @method woocommerce_version_notice
	 * @since 1.0.0
	 * Renders admin notice for WooCommerce version checking.
	 * */
	public function woocommerce_version_notice()
	{
		$plugin_wc_version = $this->get_plugin_wc_version();

		if ( defined('WC_VERSION') && class_exists( 'WooCommerce' ) && version_compare( WC_VERSION, $plugin_wc_version, '<' ) ) {
			$wc_version_notice_message = $this->get_woocommerce_version_message();
		}
		if ( ! empty( $wc_version_notice_message ) ) {
			?>
			<div class="notice notice-info is-dismissible">
				<p>
					<?php printf( esc_html__( '%s', 'hexreport' ), esc_html( $wc_version_notice_message ) ); ?>
				</p>
			</div>
			<?php
		}
	}

	/**
	 * @package hexreport
	 * @author WpHex
	 * @method php_version_notice
	 * @since 1.0.0
	 * Renders admin notice for PHP version checking.
	 * */
	public function php_version_notice()
	{
		$plugin_php_version = $this->get_plugin_php_version();
		$current_php_version = $this->get_current_php_version();

		if ( $current_php_version < $plugin_php_version ) {
			$php_version_notice_message = $this->get_php_version_message();
		}
		if ( ! empty( $php_version_notice_message ) ) {
		?>
		<div class="notice notice-info is-dismissible">
			<p>
				<?php printf( esc_html__( '%s', 'hexreport' ), esc_html( $php_version_notice_message ) ); ?>
			</p>
		</div>
		<?php
		}
	}
}
