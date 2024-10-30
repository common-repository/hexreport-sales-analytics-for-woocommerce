<?php

namespace HexReport\App\Core;

use HexReport\App\Core\Lib\SingleTon;

class AssetsManager
{
	use SingleTon;

	private $version = '';
	private $configs = [];

	/**
	 * @package hexreport
	 * @author WpHex
	 * @since 1.0.0
	 * @method register
	 * @return void
	 * Register all hooks that are needed for file equation
	 */
	public function register()
	{
		$this->configs = hexreport_get_config();

		$this->before_register_assets();

		add_action( 'admin_enqueue_scripts', [ $this, 'admin_scripts' ] );
	}

	/**
	 * @package hexreport
	 * @author WpHex
	 * @since 1.0.0
	 * @method before_register_assets
	 * @return int
	 * Determine the plugin version
	 */
	private function before_register_assets()
	{
		if ( $this->configs['dev_mode'] ) {

			return $this->version = time();
		}

		$this->version = $this->configs['plugin_version'];
	}

	/**
	 * @package hexreport
	 * @author WpHex
	 * @since 1.0.0
	 * @method admin_scripts
	 * @return void
	 * Enqueue styles and scripts for the admin pages.
	 */
	public function admin_scripts()
	{
		//load css only on the plugin page
		$screen = get_current_screen();

		if ( $screen->base === "toplevel_page_hexreport-page" ) {
			wp_enqueue_script(
				hexreport_prefix( 'main' ),
				hexreport_url( "/dist/assets/index.js" ),
				['jquery','wp-element'],
				$this->version,
				true
			);

			wp_enqueue_style(
				hexreport_prefix( 'main' ),
				hexreport_url( "/dist/assets/index.css" ),
				[],
				$this->version,
				"all"
			);
		}

		wp_localize_script( hexreport_prefix('main' ), 'hexReportData', [
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce' => wp_create_nonce( 'hexReportData-react_nonce' ),
			'translate_array' => [
			]
		] );

		wp_set_script_translations( 'main', 'hexreport-lite', plugin_dir_path( __FILE__ ) . 'languages' );
	}
}
