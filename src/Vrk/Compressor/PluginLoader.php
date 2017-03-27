<?php
/*
 * Plugin Name: VRK Compressor
 * Plugin URI: https://vrkansagara.in/plugin/vrk-compressor
 * Description: Compress all final output
 * Version: 1.0
 * Author: Vallabh Kansagara
 * Author URI: https://vrkansagara.in/author/vrk
 * Author Email: vrkansagara@gmail.com
*/

/*
BSD 3-Clause License

Copyright (c) 2017, Vallabh Kansagara
All rights reserved.

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions are met:

* Redistributions of source code must retain the above copyright notice, this
  list of conditions and the following disclaimer.

* Redistributions in binary form must reproduce the above copyright notice,
  this list of conditions and the following disclaimer in the documentation
  and/or other materials provided with the distribution.

* Neither the name of the copyright holder nor the names of its
  contributors may be used to endorse or promote products derived from
  this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE
FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/
namespace Vrk\Compressor;

/**
 * Hook the WordPress plugin into the appropriate WordPress actions and filters
 *
 * @since 1.0.0
 */
class PluginLoader
{
	const VERSION = '1.0.0';

	const TEXT_DOMAIN = 'compressor';

	/**
	 * Bind to hooks and filters
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function init()
	{
		$classname = get_called_class();

		// load translated text
		add_action( 'init', array( $classname, 'loadTranslatedText' ) );

		// compatibility wrappers to coexist with other popular plugins
		add_action( 'plugins_loaded', array( $classname, 'compatibility' ) );

		// make widgets available on front and back end
		add_action( 'widgets_init', array( $classname, 'widgetsInit' ) );

		// register Vrk JavaScript to eligible for later enqueueing
		add_action( 'wp_enqueue_scripts', array( $classname, 'registerScripts' ), 1, 0 );

		if ( is_admin() ) {
			// admin-specific functionality
			add_action( 'init', array( $classname, 'adminInit' ) );
		} else {
			// hooks to be executed on general execution of WordPress such as public pageviews
			add_action( 'init', array( $classname, 'publicInit' ) );
			add_action( 'wp_head', array( $classname, 'wpHead' ), 1, 0 );
		}

		// shortcodes
		static::registerShortcodeHandlers();
	}

	/**
	 * Full path to the directory containing the Vrk for WordPress plugin files
	 *
	 * @since 1.0.0
	 *
	 * @return string full directory path
	 */
	public static function getPluginDirectory()
	{
		return dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) . '/';
	}

	/**
	 * Full path to the main file of the Vrk for WordPress plugin
	 *
	 * @since 1.0.0
	 *
	 * @return string full path to file
	 */
	public static function getPluginMainFile()
	{
		return static::getPluginDirectory() . 'compressor.php';
	}

	/**
	 * Load translated strings for the current locale, if a translation exists
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function loadTranslatedText()
	{
		load_plugin_textdomain( static::TEXT_DOMAIN );
	}


	/**
	 * Hook into actions and filters specific to a WordPress administrative view
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function adminInit()
	{
		// User profile fields
		add_action( 'admin_init', array( '\Vrk\Compressor\Admin\Profile\User', 'init' ) );
		add_action( 'admin_init', array( '\Vrk\Compressor\Admin\Profile\PeriscopeUser', 'init' ) );

		$features = \Vrk\Compressor\Features::getEnabledFeatures();

		// Vrk settings menu
		\Vrk\Compressor\Admin\Settings\Loader::init();
	}

	/**
	 * Register actions and filters shown in a non-admin context
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function publicInit()
	{
		// enhance web browser view only
		if ( is_feed() ) {
			return;
		}

		$features = \Vrk\Compressor\Features::getEnabledFeatures();

		// do not add content filters to HTTP 404 response
		if ( is_404() ) {
			return;
		}
	}

}
