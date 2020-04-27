<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

add_action('wp_ajax_tailor_render', function () {
    \N2SS3Shortcode::forceIframe('tailor', true);
}, -1);
