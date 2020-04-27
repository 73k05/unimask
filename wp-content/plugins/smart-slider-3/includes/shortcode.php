<?php

class N2SS3Shortcode {

    private static $cacheSliderOutput = array();

    public static $iframe = false;

    public static $iframeReason = '';

    public static $disablePointer = false;

    public static function forceIframe($reason, $disablePointer = false) {
        self::$iframe         = true;
        self::$iframeReason   = $reason;
        self::$disablePointer = $disablePointer;
    }

    public static function doShortcode($parameters) {

        if (self::$shortcodeMode == 'noop') {
            return '';
        }

        if (is_admin() && isset($_GET['nextendajax'])) {
            return '';
        }

        if (!empty($parameters['alias'])) {
            $parameters['slider'] = $parameters['alias'];
        }

        if (isset($parameters['iframe'])) {
            self::forceIframe($parameters['iframe']);
        }

        if (self::$iframe) {
            if (isset($parameters['slider'])) {
                return self::renderIframe($parameters['slider']);
            }

            return 'Smart Slider - Please select a slider!';
        }

        return self::render($parameters);
    }

    public static function renderIframe($sliderIDorAlias) {
        $script = file_get_contents(dirname(__FILE__) . '/iframe.min.js');
    

        $attributes = array(
            'class'       => "n2-ss-slider-frame",
            'style'       => 'width:100%;display:block;border:0;' . (self::$disablePointer ? 'pointer-events:none;' : ''),
            'frameborder' => 0,
            'src'         => site_url('/') . '?n2prerender=1&n2app=smartslider&n2controller=slider&n2action=iframe&sliderid=' . $sliderIDorAlias . '&hash=' . md5($sliderIDorAlias . NONCE_SALT)
        );
        $html       = '';

        switch (self::$iframeReason) {
            case 'divi':
                $attributes['onload'] = str_replace(array(
                        "\n",
                        "\r",
                        "\r\n",
                        '"',
                    ), array(
                        "",
                        "",
                        "",
                        "'"
                    ), $script) . 'n2SSIframeLoader(this);';
                break;
            case 'visualcomposer':
            default:
                $attributes['onload'] = str_replace(array(
                        "\n",
                        "\r",
                        "\r\n"
                    ), "", $script) . 'n2SSIframeLoader(this);';
                break;
        }

        return $html . '<div>' . N2HTML::tag('iframe', $attributes) . '</div>';
    }

    public static function render($parameters, $usage = 'WordPress Shortcode') {

        $parameters = shortcode_atts(array(
            'id'        => md5(time()),
            'slider'    => '',
            'logged_in' => null,
            'role'      => null,
            'cap'       => null,
            'page'      => null,
            'lang'      => null,
            'slide'     => null,
            'get'       => null
        ), $parameters);

        if (empty($parameters['slider'])) {
            return '';
        }

        if ($parameters['logged_in'] !== null) {
            $logged_in = !!$parameters['logged_in'];
            if (is_user_logged_in() !== $logged_in) {
                return '';
            }
        }

        if ($parameters['role'] !== null || $parameters['cap'] !== null) {
            $current_user = wp_get_current_user();

            if ($parameters['role'] !== null) {
                $current_user_roles = $current_user->roles;
                if (!in_array($parameters['role'], $current_user_roles)) {
                    return '';
                }
            }

            if ($parameters['cap'] !== null) {
                $current_user_caps = $current_user->allcaps;
                if (!isset($current_user_caps[$parameters['cap']]) || !$current_user_caps[$parameters['cap']]) {
                    return '';
                }
            }
        }

        if ($parameters['page'] !== null) {
            if ($parameters['page'] == 'home') {
                $condition = (!is_home() && !is_front_page());
            } else {
                $condition = ((get_the_ID() != intval($parameters['page'])) || (is_home() || is_front_page()));
            }
            if ($condition) {
                return '';
            }
        }

        if ($parameters['lang'] !== null) {
            if ($parameters['lang'] != N2Localization::getLocale()) {
                return '';
            }
        }

        if (!isset(self::$cacheSliderOutput[$parameters['slider']])) {

            if ((is_numeric($parameters['slider']) && intval($parameters['slider']) > 0) || !is_numeric($parameters['slider'])) {
                ob_start();

                $slideTo = false;
                if ($parameters['slide'] !== null) {
                    $slideTo = intval($parameters['slide']);
                }

                if ($parameters['get'] !== null && !empty($_GET[$parameters['get']])) {
                    $slideTo = intval($_GET[$parameters['get']]);
                }

                if ($slideTo) {
                    echo "<script type=\"text/javascript\">window['ss" . $parameters['slider'] . "'] = " . ($slideTo - 1) . ";</script>";
                }

                N2Base::getApplication("smartslider")
                      ->getApplicationType('frontend')
                      ->render(array(
                          "controller" => 'home',
                          "action"     => 'wordpress',
                          "useRequest" => false
                      ), array(
                          $parameters['slider'],
                          $usage
                      ));

                self::$cacheSliderOutput[$parameters['slider']] = ob_get_clean();
            } else {
                return '';
            }
        }

        return self::$cacheSliderOutput[$parameters['slider']];
    }

    private static $shortcodeMode = 'shortcode';

    public static function changeShortcodeMode($mode) {
        if (self::$shortcodeMode != $mode) {
            self::$shortcodeMode = $mode;
        }
    }

    public static function shortcodeModeToNormal() {
        self::changeShortcodeMode('shortcode');
    }

    public static function shortcodeModeToNoop() {
        self::changeShortcodeMode('noop');
    }

    public static function shortcodeModeToSkip() {
        self::removeShortcode();
    }

    public static function shortcodeModeRestore() {
        self::addShortCode();
    }

    public static function addShortCode() {
        add_shortcode('smartslider3', 'N2SS3Shortcode::doShortcode');
    }

    private static function removeShortcode() {
        remove_shortcode('smartslider3');
    }

    public static function headStart() {
        self::shortcodeModeToNoop();

        add_action('wp_enqueue_scripts', 'N2SS3Shortcode::shortcodeModeToNormal', -1000000);
        add_action('wp_enqueue_scripts', 'N2SS3Shortcode::shortcodeModeToNoop', 1000000);

    }

    public static function headEnd() {

        remove_action('wp_enqueue_scripts', 'N2SS3Shortcode::shortcodeModeToNormal', -1000000);
        remove_action('wp_enqueue_scripts', 'N2SS3Shortcode::shortcodeModeToNoop', 1000000);

        self::shortcodeModeToNormal();
    }
}

N2SS3Shortcode::addShortCode();

if (defined('DOING_AJAX') && DOING_AJAX) {
    if (isset($_POST['action']) && ($_POST['action'] == 'stpb_preview_builder_item' || $_POST['action'] == 'stpb_load_builder_templates' || $_POST['action'] == 'stpb_load_template')) {
        N2SS3Shortcode::shortcodeModeToSkip();
    }
}

/**
 * There should not be sliders in the head
 */
add_action('wp_head', 'N2SS3Shortcode::headStart', -10000);
add_action('wp_head', 'N2SS3Shortcode::headEnd', 10000);


add_action('woocommerce_shop_loop', 'N2SS3Shortcode::shortcodeModeToNoop', 9);
add_action('woocommerce_shop_loop', 'N2SS3Shortcode::shortcodeModeToNormal', 11);

add_action('woocommerce_single_product_summary', 'N2SS3Shortcode::shortcodeModeToNoop', 59);
add_action('woocommerce_single_product_summary', 'N2SS3Shortcode::shortcodeModeToNormal', 61);

/**
 * Remove Smart Slider from feeds
 */
add_action('do_feed_rdf', 'N2SS3Shortcode::shortcodeModeToNoop', 0);
add_action('do_feed_rss', 'N2SS3Shortcode::shortcodeModeToNoop', 0);
add_action('do_feed_rss2', 'N2SS3Shortcode::shortcodeModeToNoop', 0);
add_action('do_feed_atom', 'N2SS3Shortcode::shortcodeModeToNoop', 0);

/**
 * Sliders are not available over REST API! Fixes Gutenberg save problems.
 */
add_action('rest_api_init', 'N2SS3Shortcode::shortcodeModeToNoop', 0);

/**
 * Remove sliders from the AMP version of the site
 * @url https://wordpress.org/plugins/amp/
 */
add_action('pre_amp_render_post', 'N2SS3Shortcode::shortcodeModeToNoop', 0);


add_action('after_setup_theme', function () {
    if (function_exists('KTT_share_args_for_posts')) {
        /**
         * Theme: Narratium
         * @url https://themeforest.net/item/narratium-simplicity-for-authors/20844434
         */
        add_action('wp', 'N2SS3Shortcode::shortcodeModeToNoop', 0);
        add_action('wp', 'N2SS3Shortcode::shortcodeModeToNormal', 11);
    }
});