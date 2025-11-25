<?php
/**
 * @package Sanasana
 * Centralized Cache Management System
 * 
 * Handles transient caching for WP_Query results, post meta, and fragment caching
 * to reduce database queries and improve performance.
 * 
 * @since 1.0.4
 */

namespace SanasanaInit\General;

class CacheController extends BaseController
{
    /**
     * Default cache TTL (Time To Live) in seconds
     * 12 hours = 43200 seconds
     */
    const DEFAULT_TTL = 43200;
    
    /**
     * Cache group prefix for transients
     */
    const CACHE_PREFIX = 'sanasana_cache_';
    
    /**
     * Cache version for easy invalidation
     */
    const CACHE_VERSION = '1.0.4';

    public function register()
    {
        // Flush cache on post save/update
        add_action('save_post', [$this, 'flush_post_cache'], 10, 2);
        add_action('post_updated', [$this, 'flush_post_cache'], 10, 2);
        add_action('delete_post', [$this, 'flush_post_cache']);
        
        // Flush cache on admin request
        add_action('admin_bar_menu', [$this, 'add_flush_cache_button'], 100);
        add_action('admin_post_sanasana_flush_cache', [$this, 'handle_flush_cache']);
        
        // Auto-cleanup old transients (daily)
        if (!wp_next_scheduled('sanasana_cleanup_transients')) {
            wp_schedule_event(time(), 'daily', 'sanasana_cleanup_transients');
        }
        add_action('sanasana_cleanup_transients', [$this, 'cleanup_old_transients']);
    }

    /**
     * Get cached WP_Query results
     * 
     * @param string $cache_key Unique identifier for this query
     * @param array $args WP_Query arguments
     * @param int $ttl Time to live in seconds
     * @return \WP_Query
     */
    public static function get_query_cache($cache_key, $args, $ttl = self::DEFAULT_TTL)
    {
        $transient_key = self::CACHE_PREFIX . 'query_' . md5($cache_key . serialize($args) . self::CACHE_VERSION);
        
        // Try to get cached data
        $cached_data = get_transient($transient_key);
        
        if ($cached_data !== false && is_array($cached_data)) {
            // Reconstruct WP_Query from cached data
            $query = new \WP_Query();
            $query->posts = $cached_data['posts'];
            $query->post_count = $cached_data['post_count'];
            $query->found_posts = $cached_data['found_posts'];
            $query->max_num_pages = $cached_data['max_num_pages'];
            
            return $query;
        }
        
        // No cache, run query
        $query = new \WP_Query($args);
        
        // Cache the results
        $cache_data = [
            'posts' => $query->posts,
            'post_count' => $query->post_count,
            'found_posts' => $query->found_posts,
            'max_num_pages' => $query->max_num_pages,
        ];
        
        set_transient($transient_key, $cache_data, $ttl);
        
        return $query;
    }

    /**
     * Get cached post meta with object cache
     * 
     * @param int $post_id Post ID
     * @param string $meta_key Meta key
     * @param bool $single Return single value
     * @return mixed
     */
    public static function get_meta_cache($post_id, $meta_key, $single = true)
    {
        $cache_key = self::CACHE_PREFIX . "meta_{$post_id}_{$meta_key}_" . ($single ? '1' : '0');
        
        // Try object cache first (faster than transients)
        $cached = wp_cache_get($cache_key, 'sanasana_meta');
        
        if ($cached !== false) {
            return $cached;
        }
        
        // Get from database
        $value = get_post_meta($post_id, $meta_key, $single);
        
        // Cache for 1 hour in object cache (memory)
        wp_cache_set($cache_key, $value, 'sanasana_meta', 3600);
        
        return $value;
    }

    /**
     * Get cached fragment (HTML output)
     * 
     * @param string $fragment_key Unique identifier
     * @param callable $callback Function to generate HTML
     * @param int $ttl Time to live in seconds
     * @return string HTML output
     */
    public static function get_fragment_cache($fragment_key, $callback, $ttl = self::DEFAULT_TTL)
    {
        $transient_key = self::CACHE_PREFIX . 'fragment_' . md5($fragment_key . self::CACHE_VERSION);
        
        $cached_html = get_transient($transient_key);
        
        if ($cached_html !== false) {
            return $cached_html . '<!-- Cached: ' . date('Y-m-d H:i:s') . ' -->';
        }
        
        // Generate fresh HTML
        ob_start();
        call_user_func($callback);
        $html = ob_get_clean();
        
        // Cache it
        set_transient($transient_key, $html, $ttl);
        
        return $html;
    }

    /**
     * Flush cache for specific post type
     * 
     * @param string $post_type
     */
    public static function flush_post_type_cache($post_type)
    {
        global $wpdb;
        
        $pattern = self::CACHE_PREFIX . '%' . $post_type . '%';
        
        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
                $pattern
            )
        );
        
        // Clear object cache group
        wp_cache_flush_group('sanasana_meta');
    }

    /**
     * Flush cache when post is saved/updated
     * 
     * @param int $post_id
     * @param \WP_Post $post
     */
    public function flush_post_cache($post_id, $post = null)
    {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        if (!$post) {
            $post = get_post($post_id);
        }
        
        if (!$post) {
            return;
        }
        
        // Flush cache for this post type
        self::flush_post_type_cache($post->post_type);

        // Also flush all fragment caches. Fragment keys are md5-hashed and do not include the post type string,
        // so pattern-based deletion in flush_post_type_cache will not catch them. We take a broad approach and
        // clear all fragment caches to avoid stale HTML after content updates.
        self::flush_fragment_cache();
    }

    /**
     * Flush all fragment (HTML) caches.
     * Fragment transient keys are generated as: sanasana_cache_fragment_{md5(fragment_key + version)}
     * Since the md5 removes readable context (like post type), targeted invalidation is not possible without
     * additional bookkeeping. Broad deletion remains performant given typical transient counts.
     */
    public static function flush_fragment_cache()
    {
        global $wpdb;
        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
                self::CACHE_PREFIX . 'fragment_%'
            )
        );
        // Clear any dedicated fragment cache group if used elsewhere.
        wp_cache_flush_group('sanasana_fragments');
    }

    /**
     * Flush all Sanasana cache
     */
    public static function flush_all_cache()
    {
        global $wpdb;
        
        // Delete all transients
        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
                self::CACHE_PREFIX . '%'
            )
        );
        
        // Clear object cache
        wp_cache_flush_group('sanasana_meta');
        
        // Clear fragment cache
        wp_cache_delete_group('sanasana_fragments');
    }

    /**
     * Add flush cache button to admin bar
     */
    public function add_flush_cache_button($admin_bar)
    {
        if (!current_user_can('manage_options')) {
            return;
        }
        
        $admin_bar->add_node([
            'id'    => 'sanasana-flush-cache',
            'title' => '🔄 Flush Sanasana Cache',
            'href'  => wp_nonce_url(admin_url('admin-post.php?action=sanasana_flush_cache'), 'sanasana_flush_cache'),
            'meta'  => [
                'title' => __('Clear all Sanasana plugin cache', 'sanasana'),
            ],
        ]);
    }

    /**
     * Handle cache flush request
     */
    public function handle_flush_cache()
    {
        check_admin_referer('sanasana_flush_cache');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('Unauthorized', 'sanasana'));
        }
        
        self::flush_all_cache();
        
        wp_redirect(wp_get_referer() ?: admin_url());
        exit;
    }

    /**
     * Cleanup old transients (runs daily)
     */
    public function cleanup_old_transients()
    {
        global $wpdb;
        
        // Delete expired transients
        $wpdb->query(
            "DELETE FROM {$wpdb->options} 
            WHERE option_name LIKE '_transient_timeout_" . self::CACHE_PREFIX . "%' 
            AND option_value < UNIX_TIMESTAMP()"
        );
        
        // Delete orphaned transients (without timeout)
        $wpdb->query(
            "DELETE FROM {$wpdb->options} 
            WHERE option_name LIKE '_transient_" . self::CACHE_PREFIX . "%' 
            AND option_name NOT IN (
                SELECT REPLACE(option_name, '_timeout', '') 
                FROM {$wpdb->options} 
                WHERE option_name LIKE '_transient_timeout_" . self::CACHE_PREFIX . "%'
            )"
        );
    }

    /**
     * Get cache stats for debugging
     * 
     * @return array
     */
    public static function get_cache_stats()
    {
        global $wpdb;
        
        $total_transients = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->options} WHERE option_name LIKE %s",
                '_transient_' . self::CACHE_PREFIX . '%'
            )
        );
        
        $total_size = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT SUM(LENGTH(option_value)) FROM {$wpdb->options} WHERE option_name LIKE %s",
                '_transient_' . self::CACHE_PREFIX . '%'
            )
        );
        
        return [
            'total_transients' => $total_transients,
            'total_size_bytes' => $total_size,
            'total_size_kb' => round($total_size / 1024, 2),
            'cache_version' => self::CACHE_VERSION,
        ];
    }
}
