<?php
/**
 * @package PriceTable
 */

namespace SanasanaInit\Seo;

class SeoMetaboxesController 
{
    public function register()
    {
        add_action('add_meta_boxes', [$this, 'add_meta_boxes']);
        add_action('save_post', [$this, 'save_meta_boxes']);
    }

    public function add_meta_boxes()
    {
        add_meta_box(
            'seo_page_settings',
            'SEO Page Settings',
            [$this, 'render_meta_box'],
            'seo_page_config',
            'normal',
            'high'
        );
    }

    public function render_meta_box($post)
    {
        $pages = get_pages();
        $page_slug = get_post_meta($post->ID, 'page_slug', true);
        $excluded_image_ids = get_post_meta($post->ID, 'excluded_image_ids', true);
        $excluded_css = get_post_meta($post->ID, 'excluded_css_handles', true);
        $excluded_js = get_post_meta($post->ID, 'excluded_js_handles', true);

        echo '<p><strong>Página destino:</strong><br>';
        echo '<select name="page_slug">';
        foreach ($pages as $page) {
            $selected = ($page_slug == $page->post_name) ? 'selected' : '';
            echo '<option value="'. esc_attr($page->post_name) .'" '. $selected .'>'. esc_html($page->post_title) .'</option>';
        }
        echo '</select></p>';

        echo '<p><strong>IDs de imágenes excluidas (coma separados):</strong><br>';
        echo '<textarea name="excluded_image_ids" rows="2" style="width:100%;">'. esc_textarea($excluded_image_ids) .'</textarea></p>';

        echo '<p><strong>Handles de CSS a excluir (coma separados):</strong><br>';
        echo '<textarea name="excluded_css_handles" rows="2" style="width:100%;">'. esc_textarea($excluded_css) .'</textarea></p>';

        echo '<p><strong>Handles de JS a excluir (coma separados):</strong><br>';
        echo '<textarea name="excluded_js_handles" rows="2" style="width:100%;">'. esc_textarea($excluded_js) .'</textarea></p>';
    }

    public function save_meta_boxes($post_id)
    {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        if (!isset($_POST['page_slug'])) return; // aseguramos que viene de nuestro metabox

        $fields = ['page_slug', 'excluded_image_ids', 'excluded_css_handles', 'excluded_js_handles'];

        foreach ($fields as $field) {
            if (isset($_POST[$field])) {
                update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
            }
        }
    }
}
