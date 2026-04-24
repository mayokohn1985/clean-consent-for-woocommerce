<?php
/**
 * Plugin Name: Clean Consent for WooCommerce
 * Description: Lightweight cookie consent for WordPress and WooCommerce without bloat.
 * Version: 0.1.0
 * Author: Marián Kohn
 * Text Domain: clean-consent-woocommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'CCFW_VERSION', '0.1.0' );
define( 'CCFW_PATH', plugin_dir_path( __FILE__ ) );
define( 'CCFW_URL', plugin_dir_url( __FILE__ ) );

require_once CCFW_PATH . 'includes/frontend.php';
