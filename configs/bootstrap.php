<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

define( 'HEXREPORT_DIR_PATH', plugin_dir_path( HEXREPORT_FILE ) );
define( 'HEXREPORT_PLUGIN_URL', plugins_url( '/', HEXREPORT_FILE ) );

if ( ! function_exists( 'hexreport_get_config' ) ) {
	/**
	 * get configs.
	 *
	 * @param string $name - plugin name.
	 *
	 * @return string
	 */
	function hexreport_get_config($name = '')
	{
		$configs = require HEXREPORT_DIR_PATH . '/configs/config.php';
		if ( $name ) {
			return isset($configs[$name]) ? $configs[$name] : false;
		}
		return $configs;
	}
}

if ( ! function_exists( 'hexreport_prefix' ) ) {
	/**
	 * Add prefix for the given string.
	 *
	 * @param string $name - plugin name.
	 *
	 * @return string
	 */
	function hexreport_prefix($name)
	{
		return hexreport_get_config('plugin_slug') . "-" . $name;
	}
}

if ( ! function_exists( 'hexreport_url' ) ) {
	/**
	 * Add prefix for the given string.
	 *
	 * @param  string $path
	 *
	 * @return string
	 */
	function hexreport_url($path)
	{
		return HEXREPORT_PLUGIN_URL . $path;
	}
}

if ( ! function_exists( 'hexreport_asset_url' ) ) {
	/**
	 * Add prefix for the given string.
	 *
	 * @param  string $path
	 *
	 * @return string
	 */
	function hexreport_asset_url($path)
	{
		return hexreport_url( "assets" . $path );
	}
}

if ( ! function_exists( 'hexreport_wp_ajax' ) ) {
	/**
	 * Wrapper function for wp_ajax_* and wp_ajax_nopriv_*
	 *
	 * @param  string $action - action name
	 * @param string $callback - callback method name
	 * @param bool   $public - is this a public ajax action
	 *
	 * @return mixed
	 */
	function hexreport_wp_ajax($action, $callback, $public = false)
	{
		add_action( 'wp_ajax_' . $action, $callback );
		if ( $public ) {
			add_action( 'wp_ajax_nopriv_' . $action, $callback );
		}
	}
}

if ( ! function_exists( 'hexreport_render_template' ) ) {
	/**
	 * Require a Template file.
	 *
	 * @param  string $file_path
	 * @param array  $data
	 *
	 * @return mixed
	 *
	 * @throws \Exception - if file not found throw exception
	 * @throws \Exception - if data is not array throw exception
	 */
	function hexreport_render_template($file_path, $data = array())
	{
		$file = HEXREPORT_DIR_PATH . "app" . $file_path;
		if ( ! file_exists( $file ) ) {
			throw new \Exception( esc_html__( "File not found", 'hexreport' ) );
		}
		if ( ! is_array( $data ) ) {
			throw new \Exception( esc_html__( "Expected array as data", 'hexreport' ) );
		}
		extract( $data, EXTR_PREFIX_SAME, hexreport_get_config('plugin_prefix') );	// @phpcs:ignore

		return require_once $file;
	}
}

if ( ! function_exists( 'hexreport_render_view_template' ) ) {
	/**
	 * Require a View template file.
	 *
	 * @param  string $file_path
	 * @param array  $data
	 *
	 * @return mixed
	 */
	function hexreport_render_view_template($file_path, $data = array())
	{
		return hexreport_render_template( "/Views" . $file_path, $data );
	}
}
