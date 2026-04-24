<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'wp_enqueue_scripts', function () {
    wp_enqueue_style(
        'ccfw-consent',
        CCFW_URL . 'assets/consent.css',
        [],
        CCFW_VERSION
    );

    wp_enqueue_script(
        'ccfw-consent',
        CCFW_URL . 'assets/consent.js',
        [],
        CCFW_VERSION,
        true
    );
});

add_action( 'wp_footer', function () {
    ?>
    <div id="ccfw-banner" class="ccfw-banner" hidden>
        <div class="ccfw-box">
            <p class="ccfw-title">Privacy settings</p>

            <p class="ccfw-text">
                We use essential cookies to make this website work. With your consent, we may also use analytics and marketing cookies.
            </p>

            <div class="ccfw-actions">
                <button type="button" class="ccfw-btn ccfw-btn-secondary" data-ccfw-choice="reject">
                    Reject
                </button>

                <button type="button" class="ccfw-btn ccfw-btn-primary" data-ccfw-choice="accept">
                    Accept
                </button>
            </div>
        </div>
    </div>
    <?php
});

/**
 * Log detected tracking scripts.
 */
function ccfw_log_detected_script( $src, $category ) {

    $detected = get_option( 'ccfw_detected_scripts', [] );

    if ( ! is_array( $detected ) ) {
        $detected = [];
    }

    if ( count( $detected ) > 50 ) {
        array_shift( $detected );
    }

    $detected[ md5( $src ) ] = [
        'src'      => esc_url_raw( $src ),
        'category' => sanitize_text_field( $category ),
        'time'     => current_time( 'mysql' ),
    ];

    update_option( 'ccfw_detected_scripts', $detected, false );
}

/**
 * Auto-block tracking scripts loaded via wp_enqueue_script().
 */
add_filter( 'script_loader_tag', function ( $tag, $handle, $src ) {

    if ( is_admin() || wp_doing_ajax() || wp_is_json_request() ) {
        return $tag;
    }

    $blocked = [
        'googletagmanager.com' => 'analytics',
        'google-analytics.com' => 'analytics',
        'gtag/js'              => 'analytics',
        'connect.facebook.net' => 'marketing',
        'clarity.ms'           => 'analytics',
        'hotjar.com'           => 'analytics',
    ];

    foreach ( $blocked as $needle => $category ) {
        if ( stripos( $src, $needle ) !== false ) {

            ccfw_log_detected_script( $src, $category );

            return sprintf(
                '<script type="text/plain" data-ccfw-category="%s" data-ccfw-original-src="%s"></script>',
                esc_attr( $category ),
                esc_url( $src )
            );
        }
    }

    return $tag;

}, 10, 3 );

/**
 * Disable WooCommerce Order Attribution / Sourcebuster cookies.
 */
add_filter( 'woocommerce_enable_order_attribution', '__return_false' );
