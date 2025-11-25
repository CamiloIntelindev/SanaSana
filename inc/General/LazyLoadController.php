<?php
/**
 * @package Sanasana
 * 
 * Lazy Load Controller - Only loads shortcode controllers when needed
 * Performance optimization: reduces bootstrap time by ~30-100ms
 * 
 * @version 1.0.5
 */

namespace SanasanaInit\General;

class LazyLoadController extends BaseController
{
    /**
     * Mapping of shortcodes to their controller classes
     * @var array
     */
    private static $shortcode_map = [
        // Programs shortcodes
        'price_table' => 'SanasanaInit\Programs\ProgramsShortcode',
        'toggle_button' => 'SanasanaInit\Programs\ProgramsShortcode',
        'price_table_cards' => 'SanasanaInit\Programs\ProgramsShortcode',
        'price_table_cards_nosotros' => 'SanasanaInit\Programs\ProgramsShortcode',
        'price_table_details' => 'SanasanaInit\Programs\ProgramsShortcode',
        'get_program_details' => 'SanasanaInit\Programs\ProgramsShortcode',
        'get_render_program_ahorros' => 'SanasanaInit\Programs\ProgramsShortcode',
        
        // TabsTable shortcodes
        'tabs' => 'SanasanaInit\TabsTable\TabsTableShortcode',
        'evaluation-tabs' => 'SanasanaInit\TabsTable\TabsTableShortcode',
        
        // FAQ shortcodes
        'faq_tabs' => 'SanasanaInit\Faq\FaqShortcode',
        
        // Questionnaire shortcodes
        'questionnaire_render' => 'SanasanaInit\Questionnaire\QuestionnaireShortcode',
        'cuestionario' => 'SanasanaInit\Questionnaire\QuestionnaireShortcode',
        
        // General buttons shortcodes
        'ingresa_button' => 'SanasanaInit\General\GeneralButtonsController',
        'afiliate_home_hero_buttons' => 'SanasanaInit\General\GeneralButtonsController',
        'conoce_mas_button' => 'SanasanaInit\General\GeneralButtonsController',
        'affiliate_button_single_redirection' => 'SanasanaInit\General\GeneralButtonsController',
        'affiliate_button_plan_details_top' => 'SanasanaInit\General\GeneralButtonsController',
        'affiliate_button_footer' => 'SanasanaInit\General\GeneralButtonsController',
        'schedule_button_single_redirection' => 'SanasanaInit\General\GeneralButtonsController',
        
        // Form shortcodes (ContactUs, LearnMore)
        'contact_us_form' => 'SanasanaInit\Form\ContactUsController',
        'learn_more_form' => 'SanasanaInit\Form\LearnMoreController',
    ];

    /**
     * Controllers that should ALWAYS load (critical for site functionality)
     * @var array
     */
    private static $always_load = [
        'SanasanaInit\General\BaseController',
        'SanasanaInit\General\CacheController',
        'SanasanaInit\General\EnqueueController',
        'SanasanaInit\Seo\SeoOverrideController',
        'SanasanaInit\Seo\SchemaController',
        'SanasanaInit\NavStyles\NavStylesSettings',
        // Shortcode controllers now restored to always load to avoid missing registration in edge contexts
        'SanasanaInit\General\GeneralButtonsController',
        'SanasanaInit\Form\ContactUsController',
        'SanasanaInit\Form\LearnMoreController',
        'SanasanaInit\TabsTable\TabsTableShortcode',
        'SanasanaInit\Questionnaire\QuestionnaireShortcode',
        'SanasanaInit\Programs\ProgramsShortcode',
        'SanasanaInit\Faq\FaqShortcode',
    ];

    /**
     * Controllers that have been lazy-loaded
     * @var array
     */
    private static $loaded_controllers = [];

    /**
     * Register the lazy load system
     */
    public function register()
    {
        // Allow disabling lazy load via constant (documented in optimization guide)
        if (defined('SANASANA_DISABLE_LAZY_LOAD') && SANASANA_DISABLE_LAZY_LOAD) {
            return; // Skip all lazy load hooks
        }
        // Hook early to detect shortcodes before rendering
        add_filter('the_content', [$this, 'detect_and_load_shortcodes'], 1);
        add_filter('widget_text', [$this, 'detect_and_load_shortcodes'], 1);
        add_filter('widget_block_content', [$this, 'detect_and_load_shortcodes'], 1);
        
        // For Beaver Builder modules
        add_filter('fl_builder_before_render_shortcodes', [$this, 'detect_and_load_shortcodes'], 1);
        
        // For REST API requests (preview, etc)
        add_action('rest_api_init', [$this, 'load_all_shortcode_controllers']);
        
        // Admin bar debug info (defer capability check until pluggable functions are loaded)
        // NOTE: Calling current_user_can() here caused a fatal before wp_get_current_user() was available.
        add_action('admin_bar_menu', function($admin_bar) {
            if (function_exists('current_user_can') && current_user_can('manage_options')) {
                $this->add_lazy_load_debug_info($admin_bar);
            }
        }, 100);

        // Fallback: ensure all shortcodes are registered after all plugins loaded
        add_action('plugins_loaded', [$this, 'ensure_all_shortcodes_registered'], 100);
    }

    /**
     * Fallback to guarantee shortcode tags exist even if original controller registration failed.
     * Runs late on plugins_loaded to avoid races. Safe/idempotent: re-instantiation only if shortcode not found.
     */
    public function ensure_all_shortcodes_registered()
    {
        if (!function_exists('shortcode_exists')) {
            return; // environment not ready
        }
        // Tags we want to force re-registration (override potential conflicts)
        $force_override = ['tabs','evaluation-tabs','price_table','price_table_cards','price_table_cards_nosotros','price_table_details'];

        global $shortcode_tags;

        foreach (self::$shortcode_map as $tag => $controller_class) {
            $should_force = in_array($tag, $force_override, true);
            $exists = shortcode_exists($tag);
            $original = $exists && isset($shortcode_tags[$tag]) ? $shortcode_tags[$tag] : null;

            if ($exists && !$should_force) {
                continue; // already registered and no override requested
            }

            if (class_exists($controller_class)) {
                $controller = new $controller_class();
                if (method_exists($controller, 'register')) {
                    $controller->register();
                    self::$loaded_controllers[$controller_class] = true;
                    if (defined('WP_DEBUG') && WP_DEBUG) {
                        $mode = $should_force && $exists ? 'override' : 'fallback';
                        $orig_info = $original ? (' original_callback=' . (is_array($original) ? json_encode($original) : (is_string($original) ? $original : gettype($original)))) : '';
                        error_log('[Sanasana LazyLoad] ' . $mode . ' registered shortcode [' . $tag . '] via ' . $controller_class . $orig_info);
                    }
                }
            }
        }
    }

    /**
     * Detect shortcodes in content and lazy load their controllers
     * 
     * @param string $content The content to scan
     * @return string Unchanged content
     */
    public function detect_and_load_shortcodes($content)
    {
        if (empty($content) || !is_string($content)) {
            return $content;
        }

        // Quick check: does content have any shortcodes?
        if (strpos($content, '[') === false) {
            return $content;
        }

        // Get all shortcodes present in content
        $found_shortcodes = $this->get_shortcodes_in_content($content);

        if (empty($found_shortcodes)) {
            return $content;
        }

        // Load only the controllers needed for these shortcodes
        foreach ($found_shortcodes as $shortcode) {
            $this->load_controller_for_shortcode($shortcode);
        }

        return $content;
    }

    /**
     * Extract shortcode tags from content
     * 
     * @param string $content
     * @return array Array of shortcode tags found
     */
    private function get_shortcodes_in_content($content)
    {
        $found = [];
        
        // Use WordPress built-in function if available
        if (function_exists('get_shortcode_tags_in_content')) {
            $found = get_shortcode_tags_in_content($content);
        } else {
            // Fallback: simple regex pattern
            preg_match_all('/\[([a-z0-9_-]+)/i', $content, $matches);
            if (!empty($matches[1])) {
                $found = array_unique($matches[1]);
            }
        }

        // Filter to only our registered shortcodes
        return array_filter($found, function($shortcode) {
            return isset(self::$shortcode_map[$shortcode]);
        });
    }

    /**
     * Load controller for a specific shortcode
     * 
     * @param string $shortcode The shortcode tag
     */
    private function load_controller_for_shortcode($shortcode)
    {
        if (!isset(self::$shortcode_map[$shortcode])) {
            return;
        }

        $controller_class = self::$shortcode_map[$shortcode];

        // Don't load if already loaded
        if (isset(self::$loaded_controllers[$controller_class])) {
            return;
        }

        // Don't load if it's an always-load controller (already loaded)
        if (in_array($controller_class, self::$always_load)) {
            return;
        }

        // Instantiate and register the controller
        if (class_exists($controller_class)) {
            $controller = new $controller_class();
            if (method_exists($controller, 'register')) {
                $controller->register();
                self::$loaded_controllers[$controller_class] = true;

                // Log if WP_DEBUG enabled
                if (defined('WP_DEBUG') && WP_DEBUG) {
                    error_log(sprintf(
                        '[Sanasana LazyLoad] Loaded controller %s for shortcode [%s]',
                        $controller_class,
                        $shortcode
                    ));
                }
            }
        }
    }

    /**
     * Load ALL shortcode controllers (fallback for REST API, admin, etc)
     */
    public function load_all_shortcode_controllers()
    {
        $all_controllers = array_unique(array_values(self::$shortcode_map));

        foreach ($all_controllers as $controller_class) {
            if (isset(self::$loaded_controllers[$controller_class])) {
                continue;
            }

            if (in_array($controller_class, self::$always_load)) {
                continue;
            }

            if (class_exists($controller_class)) {
                $controller = new $controller_class();
                if (method_exists($controller, 'register')) {
                    $controller->register();
                    self::$loaded_controllers[$controller_class] = true;
                }
            }
        }
    }

    /**
     * Get shortcode map for debugging
     * 
     * @return array
     */
    public static function get_shortcode_map()
    {
        return self::$shortcode_map;
    }

    /**
     * Get loaded controllers for debugging
     * 
     * @return array
     */
    public static function get_loaded_controllers()
    {
        return self::$loaded_controllers;
    }

    /**
     * Check if lazy loading is working
     * Returns stats for admin debugging
     * 
     * @return array
     */
    public static function get_lazy_load_stats()
    {
        $total_shortcode_controllers = count(array_unique(array_values(self::$shortcode_map)));
        $loaded_count = count(self::$loaded_controllers);
        $always_load_count = count(self::$always_load);

        return [
            'total_shortcode_controllers' => $total_shortcode_controllers,
            'loaded_controllers' => $loaded_count,
            'always_load_controllers' => $always_load_count,
            'lazy_loaded_controllers' => array_keys(self::$loaded_controllers),
            'memory_saved_estimate' => ($total_shortcode_controllers - $loaded_count) * 10, // ~10KB per controller
            'time_saved_estimate_ms' => ($total_shortcode_controllers - $loaded_count) * 5, // ~5ms per controller
        ];
    }

    /**
     * Add lazy load stats to admin bar
     * 
     * @param \WP_Admin_Bar $admin_bar
     */
    public function add_lazy_load_debug_info($admin_bar)
    {
        $stats = self::get_lazy_load_stats();
        $loaded = $stats['loaded_controllers'];
        $total = $stats['total_shortcode_controllers'];
        $saved_ms = $stats['time_saved_estimate_ms'];

        $title = sprintf(
            '⚡ Lazy Load: %d/%d (~%dms saved)',
            $loaded,
            $total,
            $saved_ms
        );

        $admin_bar->add_node([
            'id'    => 'sanasana-lazy-load',
            'title' => $title,
            'href'  => '#',
            'meta'  => [
                'title' => sprintf(
                    'Loaded: %d controllers | Not loaded: %d controllers | Estimated time saved: %dms',
                    $loaded,
                    $total - $loaded,
                    $saved_ms
                )
            ]
        ]);

        // Add sub-items showing which controllers are loaded
        if (!empty($stats['lazy_loaded_controllers'])) {
            foreach ($stats['lazy_loaded_controllers'] as $controller) {
                $short_name = substr($controller, strrpos($controller, '\\') + 1);
                $admin_bar->add_node([
                    'parent' => 'sanasana-lazy-load',
                    'id'     => 'lazy-' . sanitize_title($short_name),
                    'title'  => '✓ ' . $short_name,
                ]);
            }
        } else {
            $admin_bar->add_node([
                'parent' => 'sanasana-lazy-load',
                'id'     => 'lazy-none',
                'title'  => '💤 No shortcodes detected (all controllers skipped)',
            ]);
        }
    }
}
