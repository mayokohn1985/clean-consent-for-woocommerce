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
 * Auto-block tracking scripts (GA, FB, Hotjar, etc.)
 */
add_filter('script_loader_tag', function ($tag, $handle, $src) {

    // ignoruj admin, ajax, REST
    if (is_admin() || wp_doing_ajax() || wp_is_json_request()) {
        return $tag;
    }

    // definuj patterns
    $blocked = [
        'googletagmanager.com' => 'analytics',
        'google-analytics.com' => 'analytics',
        'gtag/js' => 'analytics',

        'connect.facebook.net' => 'marketing',
        'fbq(' => 'marketing',

        'clarity.ms' => 'analytics',
        'hotjar.com' => 'analytics',
    ];

    foreach ($blocked as $needle => $category) {

        if (stripos($src, $needle) !== false) {

            return sprintf(
                '<script type="text/plain" data-ccfw-category="%s" data-ccfw-original-src="%s"></script>',
                esc_attr($category),
                esc_url($src)
            );
        }
    }

    return $tag;

}, 10, 3);

add_filter('woocommerce_enable_order_attribution', '__return_false');

