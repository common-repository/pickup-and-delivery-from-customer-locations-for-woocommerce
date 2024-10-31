<?php

/**
 * Fired during plugin activation
 *
 * @link       https://powerfulwp.com
 * @since      1.0.0
 *
 * @package    Pdfclw
 * @subpackage Pdfclw/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Pdfclw
 * @subpackage Pdfclw/includes
 * @author     powerfulwp <apowerfulwp@gmail.com>
 */
class Pdfclw_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since 1.0.0
	 */
	public static function activate() {
		add_option( 'pdfclw_pickup_enable', '1' );
		add_option( 'pdfclw_pickup_mandatory', '1' );
		add_option( 'pdfclw_different_address', '1' );
	}

}
