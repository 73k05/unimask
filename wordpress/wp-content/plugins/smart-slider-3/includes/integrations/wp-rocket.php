<?php

class NextendSmartSliderWPRocket {

    public function __construct() {

        add_action('init', array(
            $this,
            'init'
        ));
    }

    public function init() {
        if (function_exists('get_rocket_cdn_url') && get_rocket_option('cdn', 0)) {
            N2Pluggable::addFilter('n2_style_loader_src', array(
                $this,
                'filterSrcCDN'
            ));

            N2Pluggable::addFilter('n2_script_loader_src', array(
                $this,
                'filterSrcCDN'
            ));
        }
    }

    public function filterSrcCDN($src) {
        return get_rocket_cdn_url($src);
    }
}

new NextendSmartSliderWPRocket();