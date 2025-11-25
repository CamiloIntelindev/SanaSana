<?php
/**
 * @package Sanasana
 * Reseñas Controller
 */

namespace SanasanaInit\General;

class ResenasController extends BaseController
{
    public function register()
    {
        add_action('init', [$this, 'register_resenas']);
        add_action('add_meta_boxes', [$this, 'add_price_table_metaboxes']);
        add_action('save_post', [$this, 'save_resena_meta']);
		add_shortcode('resenas_frontend', [$this, 'render_resenas_frontend']);
	
    }

    public function register_resenas()
    {
        $labels = array(
            'name'               => __('Reseñas', 'resenas'),
            'singular_name'      => __('Reseña', 'resenas'),
            'menu_name'          => __('Reseñas', 'resenas'),
            'name_admin_bar'     => __('Reseña', 'resenas'),
            'add_new'            => __('Add New', 'resenas'),
            'add_new_item'       => __('Add New Reseña', 'resenas'),
            'new_item'           => __('New Reseña', 'resenas'),
            'edit_item'          => __('Edit Reseña', 'resenas'),
            'view_item'          => __('View Reseña', 'resenas'),
            'all_items'          => __('All Reseñas', 'resenas'),
            'search_items'       => __('Search Reseñas', 'resenas'),
            'not_found'          => __('No Reseñas found.', 'resenas'),
            'not_found_in_trash' => __('No Reseñas found in Trash.', 'resenas')
        );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array('slug' => 'resena'),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 20,
            'menu_icon'          => 'dashicons-editor-table',
            'supports'           => array('title', 'thumbnail'),
            'show_in_rest'       => true,
        );

        register_post_type('resena', $args);
    }

    public function add_price_table_metaboxes()
    {
        add_meta_box(
            'resena_meta',
            __('Reseña Details', 'resenas'),
            [$this, 'render_resena_metabox'],
            'resena',
            'normal',
            'high'
        );
    }

    public function render_resena_metabox($post)
    {
        $name = get_post_meta($post->ID, '_resena_name', true);
        $age = get_post_meta($post->ID, '_resena_age', true);
        $content = get_post_meta($post->ID, '_resena_content', true);

        wp_nonce_field('save_resena_meta', 'resena_nonce');
        ?>
        <p>
            <label for="resena_name"><?php _e('Name', 'resenas'); ?></label>
            <input type="text" id="resena_name" name="resena_name" value="<?php echo esc_attr($name); ?>" style="width:100%;" />
        </p>
        <p>
            <label for="resena_age"><?php _e('Age', 'resenas'); ?></label>
            <input type="text" id="resena_age" name="resena_age" value="<?php echo esc_attr($age); ?>" style="width:100%;" />
        </p>
        <p>
            <label for="resena_content"><?php _e('Content', 'resenas'); ?></label>
            <textarea id="resena_content" name="resena_content" style="width:100%; height: 200px;"><?php echo esc_textarea($content); ?></textarea>
        </p>
        <?php
    }

    public function save_resena_meta($post_id)
    {
        // Check nonce
        if (!isset($_POST['resena_nonce']) || !wp_verify_nonce($_POST['resena_nonce'], 'save_resena_meta')) {
            return;
        }

        // Avoid autosaves
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        // Check user permissions
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        // Save data
        if (isset($_POST['resena_name'])) {
            update_post_meta($post_id, '_resena_name', sanitize_text_field($_POST['resena_name']));
        }

        if (isset($_POST['resena_age'])) {
            update_post_meta($post_id, '_resena_age', sanitize_text_field($_POST['resena_age']));
        }

        if (isset($_POST['resena_content'])) {
            update_post_meta($post_id, '_resena_content', sanitize_textarea_field($_POST['resena_content']));
        }
    }

    public function render_resenas_frontend()
	{
		$args = [
			'post_type'      => 'resena',
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'orderby'        => 'date',
			'order'          => 'DESC',
		];

		$query = new \WP_Query($args);

		if (!$query->have_posts()) {
			return '<p>' . __('No reseñas found.', 'resenas') . '</p>';
		}

		ob_start();
		echo '<div class="fl-col-content fl-node-content ui-sortable">';

		$modal_index = 0;

		while ($query->have_posts()) {
			$query->the_post();
			$post_id = get_the_ID();
			$name = get_post_meta($post_id, '_resena_name', true);
			$age = get_post_meta($post_id, '_resena_age', true);
			$content = get_post_meta($post_id, '_resena_content', true);

			// Limitar contenido a 18 palabras
			$words = explode(' ', strip_tags($content));
			$excerpt = implode(' ', array_slice($words, 0, 18)) . '...';

			$modal_id = 'resena-modal-' . $modal_index;

			echo '<div class="fl-col-group fl-col-group-nested resena_item">';
				echo '<div class="fl-col">';
					echo '<div class="fl-col-content">';

						// Resumen de contenido + botón
						echo '<div class="fl-module fl-module-rich-text resena_content">';
							echo '<div class="fl-module-content fl-node-content">';
								echo '<div class="fl-rich-text">';
									echo '<p>' . esc_html($excerpt) . '<span class="resena-ver-mas" data-modal="' . esc_attr($modal_id) . '">'.esc_html__("Ver más", "sanasana").'</span></p>';
								echo '</div>';
							echo '</div>';
						echo '</div>';

						// Nombre y edad
						echo '<div class="fl-module fl-module-rich-text resena_name">';
							echo '<div class="fl-module-content fl-node-content">';
								echo '<div class="fl-rich-text">';
									echo '<p>' . esc_html($name);
									/*if (!empty($age)) {
										echo ', ' . esc_html($age) . ' años';
									}*/
									echo '</p>';
								echo '</div>';
							echo '</div>';
						echo '</div>';

					echo '</div>';
				echo '</div>';
			echo '</div>';

			// Modal oculto
			echo '
			<div id="' . esc_attr($modal_id) . '" class="resena-modal">
				<img src="https://sanasanastoragews.blob.core.windows.net/blobwsdev/2025/04/close_btn.png" class="resena-close">
				<div class="resena-modal-content">
					<p>' . nl2br(esc_html($content)) . '</p>
					<p class="resena-modal-content_name" >' . esc_html($name) . (!empty($age) ? ', ' . esc_html($age) . ' años' : '') . '</p>
				</div>
			</div>';

			$modal_index++;
		}

		echo '</div>';
		wp_reset_postdata();

		return ob_get_clean();
	}
}