<?php


namespace Vrk\Compressor\Admin\Settings;

/**
 * Initialize hooks related to WordPress admin interface settings
 *
 * @since 1.0.0
 */
class Loader
{
	/**
	 * Add hooks
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function init()
	{
		add_action( 'admin_menu', array( '\Twitter\WordPress\Admin\Settings\SinglePage', 'menuItem' ) );
		add_filter( 'plugin_action_links', array( __CLASS__, 'pluginActionLinks' ), 10, 2 );
	}

	/**
	 * Link to settings from the plugin listing page
	 *
	 * @since 1.0.0
	 *
	 * @param array  $links links displayed under the plugin
	 * @param string $file  plugin main file path relative to plugin dir
	 *
	 * @return array links array passed in, possibly with our settings link added
	 */
	public static function pluginActionLinks( $links, $file )
	{
		if ( plugin_basename( \Twitter\WordPress\PluginLoader::getPluginMainFile() === $file ) ) {
			array_unshift( $links, '<a href="' . esc_url( admin_url( 'admin.php' ) . '?' . http_build_query( array( 'page' => \Twitter\WordPress\Admin\Settings\SinglePage::PAGE_SLUG ) ) ) . '">' . __( 'Settings' ) . '</a>' );
		}

		return $links;
	}
}
