<?php
/**
 * @package Programs
 */

namespace SanasanaInit\Programs;

use SanasanaInit\General\BaseController;
use WP_Query;

class ProgramsMetaBox extends BaseController
{
    public function register()
    {
        add_action('add_meta_boxes', [$this, 'add_programs_metaboxes']);
        add_action('save_post', [$this, 'save_programs_meta']);
		add_action('save_post', [$this, 'save_price_table_ahorros_meta']);
    }

    public function add_programs_metaboxes()
    {
        add_meta_box(
            'price_table_meta',
            __('Detalles de programas', 'pricetable'),
            [$this, 'render_programa_metabox'],
            'programa',
            'normal',
            'high'
        );
		
		add_meta_box(
			'price_table_ahorros_meta',
			__('Ahorros del Programa', 'pricetable'),
			[$this, 'render_programs_ahorros_metabox'],
			'programa',
			'normal',
			'high'
		);

    }

    public function render_programa_metabox($post)
    {
        $visible = get_post_meta($post->ID, '_price_table_visible', true);
		$recommended = get_post_meta(get_the_ID(), '_price_table_recommended', true);
        $description = get_post_meta($post->ID, '_price_table_description', true);
		$description_front = get_post_meta($post->ID, '_price_table_description_front', true);
        $price = get_post_meta($post->ID, '_price_table_price', true);
        $annual_text = get_post_meta($post->ID, '_price_table_annual_text', true);
//         $plan_url = get_post_meta($post->ID, '_price_table_plan_url', true);
        $plan_crm_id = get_post_meta($post->ID, '_price_table_plan_crm_id', true);
        $programs_prices = get_post_meta($post->ID, '_programs_prices', true);
        $program_benefits = get_post_meta($post->ID, '_program_benefits', true);
		

        if (!is_array($programs_prices)) {
            $programs_prices = [];
        }
        if (!is_array($program_benefits)) {
            $program_benefits = [];
        }

        wp_nonce_field('save_programs_meta', 'programs_nonce');

        ?>
        <p>
            <label for="price_table_visible"><?php _e('Visibility', 'pricetable'); ?></label>
            <select id="price_table_visible" name="price_table_visible">
                <option value="true" <?php selected($visible, 'true'); ?>><?php _e('Visible', 'pricetable'); ?></option>
                <option value="false" <?php selected($visible, 'false'); ?>><?php _e('Hidden', 'pricetable'); ?></option>
            </select>
        </p>

		<p>
            <label for="price_table_recommended"><?php _e('Recommended', 'pricetable'); ?></label>
            <select id="price_table_recommended" name="price_table_recommended">
                <option value="true" <?php selected($recommended, 'true'); ?>><?php _e('Recommended', 'pricetable'); ?></option>
                <option value="false" <?php selected($recommended, 'false'); ?>><?php _e('Regular', 'pricetable'); ?></option>
            </select>
        </p>

        <p>
            <label for="price_table_description"><?php _e('Brief Description (max 288 chars)', 'pricetable'); ?></label>
            <input type="text" id="price_table_description" name="price_table_description" value="<?php echo esc_attr($description); ?>" maxlength="288" style="width:100%;" />
        </p>
		<p>
            <label for="price_table_description_front"><?php _e('Brief Description front(max 288 chars) ', 'pricetable'); ?></label>
            <input type="text" id="price_table_description_front" name="price_table_description_front" value="<?php echo esc_attr($description_front); ?>" maxlength="288" style="width:100%;" />
        </p>
        <p>
            <label for="price_table_price"><?php _e('Price', 'pricetable'); ?></label>
            <input type="text" id="price_table_price" name="price_table_price" value="<?php echo esc_attr($price); ?>" style="width:100%;" />
        </p>
        <p>
            <label for="price_table_annual_text"><?php _e('Total Anual', 'pricetable'); ?></label>
            <input type="text" id="price_table_annual_text" name="price_table_annual_text" value="<?php echo esc_attr($annual_text); ?>" style="width:100%;" />
        </p>
<?php /*
        <p>
            <label for="price_table_plan_url"><?php _e('Plan URL', 'pricetable'); ?></label>
            <input type="text" id="price_table_plan_url" name="price_table_plan_url" value="<?php echo esc_attr($plan_url); ?>" style="width:100%;" />
        </p>
*/ ?>
        <p>
            <label for="price_table_plan_crm_id"><?php _e('Plan CRM ID', 'pricetable'); ?></label>
            <input type="text" id="price_table_plan_crm_id" name="price_table_plan_crm_id" value="<?php echo esc_attr($plan_crm_id); ?>" style="width:100%;" />
        </p>

        <h4><?php _e('Programs and Prices', 'pricetable'); ?></h4>
        <div id="programs_prices_container">
            <?php foreach ($programs_prices as $programs_price) : ?>
                <div class="programs_price-item">
                    <input type="text" name="programs_prices[]" value="<?php echo esc_attr($programs_price); ?>" style="width:80%;" />
                    <button type="button" class="remove-programs_price">✖</button>
                </div>
            <?php endforeach; ?>
        </div>
        <button type="button" id="add-programs_price"><?php _e('Add Program Item', 'pricetable'); ?></button>

        <h4><?php _e('Beneficios del Programa', 'pricetable'); ?></h4>
        <div id="program-benefits-wrapper">
            <?php foreach ($program_benefits as $bIndex => $benefit) : ?>
                <div class="program-benefit-block" data-index="<?php echo $bIndex; ?>" style="margin-bottom:20px; padding:15px; border:1px solid #ccc;">
                    <p>
                        <label><strong><?php _e('Título del Beneficio:', 'pricetable'); ?></strong></label><br>
                        <input type="text" name="program_benefits[<?php echo $bIndex; ?>][benefit]" value="<?php echo esc_attr($benefit['benefit'] ?? ''); ?>" style="width:100%;" />
                    </p>
                    <div class="benefit-items-wrapper">
                        <?php if (!empty($benefit['items'])) : ?>
                            <?php foreach ($benefit['items'] as $item) : ?>
                                <div class="benefit-item">
                                    <input type="text" name="program_benefits[<?php echo $bIndex; ?>][items][]" value="<?php echo esc_attr($item); ?>" style="width:80%;" />
                                    <button type="button" class="remove-benefit-item">✖</button>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <button type="button" class="add-benefit-item">Agregar Item</button>
                    <button type="button" class="remove-benefit-block" style="margin-left:10px;">Eliminar Beneficio</button>
                </div>
            <?php endforeach; ?>
        </div>
        <button type="button" id="add-program-benefit"><?php _e('Agregar Beneficio', 'pricetable'); ?></button>

        <script>
        jQuery(document).ready(function($) {
            var benefitIndex = <?php echo count($program_benefits); ?>;

            $('#add-programs_price').click(function() {
                $('#programs_prices_container').append('<div class="programs_price-item"><input type="text" name="programs_prices[]" style="width:80%;" /> <button type="button" class="remove-programs_price">✖</button></div>');
            });

            $(document).on('click', '.remove-programs_price', function() {
                $(this).parent().remove();
            });

            $('#add-program-benefit').click(function() {
                $('#program-benefits-wrapper').append(`
                    <div class="program-benefit-block" data-index="${benefitIndex}" style="margin-bottom:20px; padding:15px; border:1px solid #ccc;">
                        <p>
                            <label><strong>Título del Beneficio:</strong></label><br>
                            <input type="text" name="program_benefits[${benefitIndex}][benefit]" style="width:100%;" />
                        </p>
                        <div class="benefit-items-wrapper"></div>
                        <button type="button" class="add-benefit-item">Agregar Item</button>
                        <button type="button" class="remove-benefit-block" style="margin-left:10px;">Eliminar Beneficio</button>
                    </div>
                `);
                benefitIndex++;
            });

            $(document).on('click', '.add-benefit-item', function() {
                const $block = $(this).closest('.program-benefit-block');
                const currentIndex = $block.attr('data-index');
                $block.find('.benefit-items-wrapper').append(`
                    <div class="benefit-item">
                        <input type="text" name="program_benefits[${currentIndex}][items][]" style="width:80%;" />
                        <button type="button" class="remove-benefit-item">✖</button>
                    </div>
                `);
            });

            $(document).on('click', '.remove-benefit-item', function() {
                $(this).parent('.benefit-item').remove();
            });

            $(document).on('click', '.remove-benefit-block', function() {
                $(this).closest('.program-benefit-block').remove();
            });
        });
        </script>
        <?php
    }
	
	public function render_programs_ahorros_metabox($post)
	{
		$ahorros = get_post_meta($post->ID, '_price_table_ahorros', true);

		$titulo_ahorros = $ahorros['titulo'] ?? '';
		$ahorros_items = $ahorros['ahorros'] ?? [];

		?>
		<p>
			<label for="price_table_ahorros_titulo"><strong><?php _e('Título General de la Sección:', 'pricetable'); ?></strong></label><br>
			<input type="text" id="price_table_ahorros_titulo" name="price_table_ahorros[titulo]" value="<?php echo esc_attr($titulo_ahorros); ?>" style="width:100%;" />
		</p>

		<h4><?php _e('Lista de Ahorros (Texto y Porcentaje)', 'pricetable'); ?></h4>
		<div id="price_table_ahorros_items_wrapper">
			<?php foreach ($ahorros_items as $index => $item) : ?>
				<div class="ahorro-item" style="margin-bottom:10px;">
					<input type="text" name="price_table_ahorros[ahorros][<?php echo $index; ?>][texto]" value="<?php echo esc_attr($item['texto'] ?? ''); ?>" placeholder="Texto del beneficio" style="width:48%; margin-right:2%;" />
					<input type="text" name="price_table_ahorros[ahorros][<?php echo $index; ?>][valor]" value="<?php echo esc_attr($item['valor'] ?? ''); ?>" placeholder="Valor / Porcentaje" style="width:48%;" />
					<button type="button" class="remove-ahorro-item">✖</button>
				</div>
			<?php endforeach; ?>
		</div>

		<button type="button" id="add-price_table_ahorro_item"><?php _e('Agregar nuevo Ahorro', 'pricetable'); ?></button>

		<script>
		jQuery(document).ready(function($) {
			var ahorroIndex = <?php echo count($ahorros_items); ?>;

			$('#add-price_table_ahorro_item').click(function() {
				$('#price_table_ahorros_items_wrapper').append(`
					<div class="ahorro-item" style="margin-bottom:10px;">
						<input type="text" name="price_table_ahorros[ahorros][${ahorroIndex}][texto]" placeholder="Texto del beneficio" style="width:48%; margin-right:2%;" />
						<input type="text" name="price_table_ahorros[ahorros][${ahorroIndex}][valor]" placeholder="Valor / Porcentaje" style="width:48%;" />
						<button type="button" class="remove-ahorro-item">✖</button>
					</div>
				`);
				ahorroIndex++;
			});

			$(document).on('click', '.remove-ahorro-item', function() {
				$(this).parent('.ahorro-item').remove();
			});
		});
		</script>
		<?php
	}


    public function save_programs_meta($post_id)
    {
        if (!isset($_POST['programs_nonce']) || !wp_verify_nonce($_POST['programs_nonce'], 'save_programs_meta')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        update_post_meta($post_id, '_price_table_visible', isset($_POST['price_table_visible']) ? sanitize_text_field($_POST['price_table_visible']) : '');
		update_post_meta($post_id, '_price_table_recommended', isset($_POST['price_table_recommended']) ? sanitize_text_field($_POST['price_table_recommended']) : '');
		
        update_post_meta($post_id, '_price_table_description', isset($_POST['price_table_description']) ? sanitize_text_field($_POST['price_table_description']) : '');
		update_post_meta($post_id, '_price_table_description_front', isset($_POST['price_table_description_front']) ? sanitize_text_field($_POST['price_table_description_front']) : '');
        update_post_meta($post_id, '_price_table_price', isset($_POST['price_table_price']) ? sanitize_text_field($_POST['price_table_price']) : '');
        update_post_meta($post_id, '_price_table_annual_text', isset($_POST['price_table_annual_text']) ? sanitize_text_field($_POST['price_table_annual_text']) : '');
//         update_post_meta($post_id, '_price_table_plan_url', isset($_POST['price_table_plan_url']) ? esc_url($_POST['price_table_plan_url']) : '');
        update_post_meta($post_id, '_price_table_plan_crm_id', isset($_POST['price_table_plan_crm_id']) ? sanitize_text_field($_POST['price_table_plan_crm_id']) : '');
        update_post_meta($post_id, '_programs_prices', isset($_POST['programs_prices']) && is_array($_POST['programs_prices']) ? array_map('sanitize_text_field', $_POST['programs_prices']) : []);

        if (isset($_POST['program_benefits']) && is_array($_POST['program_benefits'])) {
            $benefits = [];
            foreach ($_POST['program_benefits'] as $benefit) {
                $sanitized_benefit = [
                    'benefit' => sanitize_text_field($benefit['benefit'] ?? ''),
                    'items' => []
                ];
                if (!empty($benefit['items']) && is_array($benefit['items'])) {
                    foreach ($benefit['items'] as $item) {
                        $sanitized_benefit['items'][] = sanitize_text_field($item);
                    }
                }
                $benefits[] = $sanitized_benefit;
            }
            update_post_meta($post_id, '_program_benefits', $benefits);
        } else {
            delete_post_meta($post_id, '_program_benefits');
        }
    }
	
	public function save_price_table_ahorros_meta($post_id)
	{
		if (!isset($_POST['programs_nonce']) || !wp_verify_nonce($_POST['programs_nonce'], 'save_programs_meta')) {
			return;
		}

		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return;
		}

		if (!current_user_can('edit_post', $post_id)) {
			return;
		}

		if (isset($_POST['price_table_ahorros']) && is_array($_POST['price_table_ahorros'])) {
			$ahorros_data = [
				'titulo' => sanitize_text_field($_POST['price_table_ahorros']['titulo'] ?? ''),
				'ahorros' => []
			];

			if (!empty($_POST['price_table_ahorros']['ahorros']) && is_array($_POST['price_table_ahorros']['ahorros'])) {
				foreach ($_POST['price_table_ahorros']['ahorros'] as $item) {
					$ahorro_item = [
						'texto' => sanitize_text_field($item['texto'] ?? ''),
						'valor' => sanitize_text_field($item['valor'] ?? '')
					];
					$ahorros_data['ahorros'][] = $ahorro_item;
				}
			}

			update_post_meta($post_id, '_price_table_ahorros', $ahorros_data);
		} else {
			delete_post_meta($post_id, '_price_table_ahorros');
		}
	}

}
