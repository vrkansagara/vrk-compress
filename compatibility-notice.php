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

/**
 * Communicate a lack of compatibilty between the Vrk plugin for WordPress and the current site's server environment
 *
 * @since 1.0.1
 */
class Vrk_CompatibilityNotice {
	/**
	 * Minimum version of PHP required to run the plugin
	 *
	 * Format: major.minor(.release)
	 *
	 * @since 1.0.1
	 *
	 * @type string
	 */
	const MIN_PHP_VERSION = '5.6';

	/**
	 * Release dates of PHP versions greater than the WordPress minimum requirement and less than the plugin minimum requirement
	 *
	 * @since 1.0.1
	 *
	 * @type array
	 */
	public static $PHP_RELEASE_DATES = array(
		'5.3.29' => '2014-08-14',
		'5.3.28' => '2013-07-11',
		'5.3.27' => '2013-07-11',
		'5.3.26' => '2013-06-06',
		'5.3.25' => '2013-05-09',
		'5.3.24' => '2013-04-11',
		'5.3.23' => '2013-03-14',
		'5.3.22' => '2013-02-21',
		'5.3.21' => '2013-01-17',
		'5.3.20' => '2012-12-20',
		'5.3.19' => '2012-11-22',
		'5.3.18' => '2012-10-18',
		'5.3.17' => '2012-09-13',
		'5.3.16' => '2012-08-16',
		'5.3.15' => '2012-07-19',
		'5.3.14' => '2012-06-14',
		'5.3.13' => '2012-05-08',
		'5.3.12' => '2012-05-03',
		'5.3.11' => '2012-04-26',
		'5.3.10' => '2012-02-02',
		'5.3.9'  => '2012-01-10',
		'5.3.8'  => '2011-08-23',
		'5.3.7'  => '2011-08-18',
		'5.3.6'  => '2011-03-19',
		'5.3.5'  => '2011-01-06',
		'5.3.4'  => '2010-12-09',
		'5.3.3'  => '2010-07-22',
		'5.3.2'  => '2010-03-04',
		'5.3.1'  => '2009-11-19',
		'5.3.0'  => '2009-06-30',
		'5.2.17' => '2011-01-06',
		'5.2.16' => '2010-12-16',
		'5.2.15' => '2010-12-09',
		'5.2.14' => '2010-07-22',
		'5.2.13' => '2010-02-25',
		'5.2.12' => '2009-12-17',
		'5.2.11' => '2009-09-17',
		'5.2.10' => '2009-06-18',
		'5.2.9'  => '2009-02-26',
		'5.2.8'  => '2008-12-08',
		'5.2.7'  => '2008-12-04',
		'5.2.6'  => '2008-05-01',
		'5.2.5'  => '2007-11-08',
		'5.2.4'  => '2007-08-30',
	);

	/**
	 * Admin init handler
	 *
	 * @since 1.0.1
	 *
	 * @return void
	 */
	public static function adminInit()
	{
		// no action taken for ajax request
		// extra non-formatted output could break a response format such as XML or JSON
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return;
		}

		// only show notice to a user of proper capability
		if ( ! Vrk_CompatibilityNotice::currentUserCanManagePlugins() ) {
			return;
		}

		// display error messages in the site locale
		Vrk_CompatibilityNotice::loadTranslatedText();

		// trigger an E_USER_NOTICE for the built-in error handler
		trigger_error( sprintf( __( 'The Vrk plugin for WordPress requires PHP version %s or greater.', 'compressor' ), Vrk_CompatibilityNotice::MIN_PHP_VERSION ) );

		// deactivate the plugin
		Vrk_CompatibilityNotice::deactivatePlugin();

		// display an admin notice
		add_action( 'admin_notices', array( 'Vrk_CompatibilityNotice', 'adminNotice' ) );
	}

	/**
	 * Load translated text to display an error message in the site locale
	 *
	 * @since 1.0.1
	 *
	 * @uses load_plugin_textdomain()
	 * @return void
	 */
	public static function loadTranslatedText()
	{
		load_plugin_textdomain(
			'compressor',
			false, // deprecated parameter as of WP 2.7
			dirname( plugin_basename( __FILE__ ) ) . '/languages' // path to MO files
		);
	}

	/**
	 * Get the plugin path relative to the plugins directory
	 *
	 * Used to identify the plugin in a list of installed and activated plugins
	 *
	 * @since 1.0.1
	 *
	 * @return string Plugin path. e.g. compressor/compressor.php
	 */
	public static function getPluginPath()
	{
		return dirname( plugin_basename( __FILE__ ) ) . '/compressor.php';
	}

	/**
	 * Does the curent user have the capability to possibly fix the problem?
	 *
	 * @since 1.0.1
	 *
	 * @return bool True if the current user might be able to fix, else false
	 */
	public static function currentUserCanManagePlugins()
	{
		return current_user_can( is_plugin_active_for_network( Vrk_CompatibilityNotice::getPluginPath() ) ? 'manage_network_plugins' : 'activate_plugins' );
	}

	/**
	 * Deactivate the plugin due to incompatibility
	 *
	 * @since 1.0.1
	 *
	 * @return void
	 */
	public static function deactivatePlugin()
	{
		// test for plugin management capability
		if ( ! Vrk_CompatibilityNotice::currentUserCanManagePlugins() ) {
			return;
		}

		// deactivate with deactivation actions (non-silent)
		deactivate_plugins( array( Vrk_CompatibilityNotice::getPluginPath() ) );

		// remove activate state to prevent a "Plugin activated" notice
		// notice located in wp-admin/plugins.php
		unset( $_GET['activate'] );
	}

	/**
	 * Display an admin notice communicating an incompatibility
	 *
	 * @since 1.0.1
	 *
	 * @return void
	 */
	public static function adminNotice()
	{
		echo '<div class="notice error is-dismissible">';
		echo '<p>' . esc_html( sprintf( __( 'The Vrk plugin for WordPress requires PHP version %s or greater.', 'compressor' ), Vrk_CompatibilityNotice::MIN_PHP_VERSION ) ) . '</p>';

		$version = PHP_VERSION;

		$matches = array();
		// isolate major.minor(.release)
		preg_match('/^(5\.[2|3](\.[\d]{1,2})?).*/', $version, $matches );
		if ( isset( $matches[1] ) ) {
			$version = $matches[1];
			// account for one possible major.minor match in range
			if ( '5.3' === $version ) {
				$version = '5.3.0';
			}
		}
		unset( $matches );

		$release_date = _x( 'an unknown date', 'the day the event occurred is unknown', 'compressor' );
		if ( array_key_exists( $version, Vrk_CompatibilityNotice::$PHP_RELEASE_DATES ) ) {
			$release_date = date_i18n(
				get_option( 'date_format' ),
				strtotime( Vrk_CompatibilityNotice::$PHP_RELEASE_DATES[ $version ] ),
				/* GMT */ true
			);
		}
		echo '<p>' . esc_html( sprintf( _x( 'This server is running PHP version %1$s released on %2$s.', 'The web server is running a version of the PHP software released on a locale-formatted date', 'compressor' ), $version, esc_html( $release_date ) ) ) . '</p>';

		if ( is_plugin_inactive( Vrk_CompatibilityNotice::getPluginPath() ) ) {
			echo '<p>' . __( 'Plugin <strong>deactivated</strong>.' ) . '</p>';
		}

		echo '</div>';
	}
}
