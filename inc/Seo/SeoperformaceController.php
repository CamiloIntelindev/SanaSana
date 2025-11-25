<?php
/**
 * @package PriceTable
 */

namespace SanasanaInit\Seo;

class SeoperformaceController 
{
    public function register()
    {
        add_action('init', [$this, 'register_cpt']);
        add_action('template_redirect', [$this, 'apply_page_optimizations']);
        add_filter('wp_get_attachment_image_attributes', [$this, 'exclude_lazy_load'], 10, 3);
        add_action('wp_head',[$this, 'preload_files']);
    }
	
     public function preload_files(){
        echo '<link rel="preload" href="/wp-content/uploads/bb-plugin/cache/12-layout.css" as="style" onload="this.onload=null;this.rel=\'stylesheet\'">';
        echo '<noscript><link rel="stylesheet" href="/wp-content/uploads/bb-plugin/cache/12-layout.css"></noscript>' ;
    }

    public function register_cpt()
    {
        register_post_type('seo_page_config', [
            'label' => 'SEO Performance Config',
            'public' => false,
            'show_ui' => true,
            'menu_icon' => 'dashicons-admin-generic',
            'supports' => ['title'],
        ]);
    }

    //Optimizations.
    public function apply_page_optimizations()
    {
        if (is_admin()) return;

        $current_page_id = get_queried_object_id();
        $current_slug = get_post_field('post_name', $current_page_id);

        $config = get_posts([
            'post_type'      => 'seo_page_config',
            'meta_key'       => 'page_slug',
            'meta_value'     => $current_slug,
            'posts_per_page' => 1,
            'fields'         => 'ids',
        ]);

        if ($config) {
            $post_id = $config[0];

            $excluded_css = array_map('trim', explode(',', get_post_meta($post_id, 'excluded_css_handles', true)));
            $excluded_js = array_map('trim', explode(',', get_post_meta($post_id, 'excluded_js_handles', true)));
            $excluded_image_ids = array_map('intval', explode(',', get_post_meta($post_id, 'excluded_image_ids', true)));

            // Guardar en global para lazy load
            global $excluded_image_ids_global;
            $excluded_image_ids_global = $excluded_image_ids;

            // Desregistrar CSS y JS
            add_action('wp_enqueue_scripts', function() use ($excluded_css, $excluded_js) {
                foreach ($excluded_css as $handle) {
                    if ($handle) wp_dequeue_style($handle);
                }
                foreach ($excluded_js as $handle) {
                    if ($handle) wp_dequeue_script($handle);
                }
            }, 100);
        }
    }
    //Lazy load images
    public function exclude_lazy_load($attr, $attachment, $size)
    {
        global $excluded_image_ids_global;

        if (!empty($excluded_image_ids_global) && in_array($attachment->ID, $excluded_image_ids_global)) {
            return $attr; // no aplicamos lazy
        }

        $attr['loading'] = 'lazy';
        return $attr;
    }


}
