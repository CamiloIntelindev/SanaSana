<?php
/**
 * @package Programs
 */

namespace SanasanaInit\Programs;

use SanasanaInit\General\BaseController;
use WP_Query;

class ProgramsSettings extends BaseController
{
    // Nuevo Dominio de Traducción
    const TRANSLATION_DOMAIN = 'Sanasana Comparar programas';

    public function register()
    {
        add_action('admin_menu', [$this, 'add_settings_page']);
        add_action('admin_init', [$this, 'register_settings']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_media']);
    }

    public function add_settings_page()
    {
        add_menu_page(
            __('Ajustes de programas', self::TRANSLATION_DOMAIN), // Dominio actualizado
            __('Programas Avanzados', self::TRANSLATION_DOMAIN), // Dominio actualizado
            'manage_options',
            'price_table_settings',
            [$this, 'render_settings_page'],
            'dashicons-admin-generic',
            20
        );
    }

    public function register_settings()
    {
        register_setting('price_table_options_group', 'price_table_program_details', [
            'sanitize_callback' => [$this, 'sanitize_program_details']
        ]);
        register_setting('price_table_options_group', 'price_table_visibility', [
            'sanitize_callback' => [$this, 'sanitize_visibility']
        ]);
        register_setting('price_table_options_group', 'price_table_main_image', [
            'sanitize_callback' => 'esc_url_raw'
        ]);
    }

    public function enqueue_media()
    {
        wp_enqueue_media();
    }
    
    public function sanitize_program_details($input)
    {
        if (!is_array($input)) {
            return [];
        }

        $sanitized = [];
        foreach ($input as $index => $program_detail) {
            $title = sanitize_text_field($program_detail['title'] ?? '');
            $visible = isset($program_detail['visible']);

            // ✅ Registrar el título con el NUEVO Dominio
            if (function_exists('do_action')) {
                do_action('wpml_register_single_string', self::TRANSLATION_DOMAIN, "program_detail_title_{$index}", $title);
            }

            $items = [];
            if (!empty($program_detail['items']) && is_array($program_detail['items'])) {
                foreach ($program_detail['items'] as $item_index => $item) {
                    if (!empty($item['name'])) {
                        $name = sanitize_text_field($item['name']);
                        $info = wp_kses_post( $item['info'] ?? '' );

                        // ✅ Registrar cada campo con el NUEVO Dominio
                        if (function_exists('do_action')) {
                            do_action('wpml_register_single_string', self::TRANSLATION_DOMAIN, "item_name_{$index}_{$item_index}", $name);
                            do_action('wpml_register_single_string', self::TRANSLATION_DOMAIN, "item_info_{$index}_{$item_index}", $info);
                        }

                        $items[] = [
                            'name' => $name,
                            'info' => $info,
                        ];
                    }
                }
            }

            $sanitized[] = [
                'title' => $title,
                'visible' => $visible,
                'items' => $items,
            ];
        }

        return $sanitized;
    }


    public function sanitize_visibility($input)
    {
        if (!is_array($input)) {
            return [];
        }

        $sanitized = [];
        foreach ($input as $program_detail_index => $items) {
            foreach ($items as $item_index => $posts) {
                foreach ($posts as $post_id => $value) {
                    $sanitized[$program_detail_index][$item_index][$post_id] = (bool) $value;
                }
            }
        }
        return $sanitized;
    }

    public function render_settings_page()
    {
        $program_details = get_option('price_table_program_details', []);
        $program_details = is_array($program_details) ? $program_details : [];
        
        $visibility = get_option('price_table_visibility', []);
        $visibility = is_array($visibility) ? $visibility : [];
        
        $main_image = get_option('price_table_main_image', '');
        
        $posts = get_posts(['post_type' => 'programa', 'numberposts' => -1]);
        ?>
        <div class="wrap">
            <h1><?php _e('Price Table Settings', self::TRANSLATION_DOMAIN); ?></h1>
            <form method="post" action="options.php">
                <?php settings_fields('price_table_options_group'); ?>
                
                <h2><?php _e('Main Image', self::TRANSLATION_DOMAIN); ?></h2>
                <div class="container">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row image-container">
                                <div class="col-md-1">
                                    <input type="hidden" name="price_table_main_image" value="<?php echo esc_attr($main_image); ?>" class="image-url" />
                                    <button type="button" class="upload-image-button button"><?php _e('Select Image', self::TRANSLATION_DOMAIN); ?></button>
                                </div>
                                <div class="col-md-4">
                                    <img src="<?php echo esc_attr($main_image); ?>" class="image-preview" style="max-width: 100px; display: <?php echo empty($main_image) ? 'none' : 'block'; ?>;" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="container">
                    <div class="row">
                        <div class="col-md-12">
                            <h2><?php _e('Program Details & Items', self::TRANSLATION_DOMAIN); ?></h2>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div id="program-details-settings">
                                <div class="mb-5">
                                    <button type="button" id="add-program-detail" class="btn btn-primary" style="border-radius: 10px; padding: 2px 24px; font-size: 16px;">+ Add Program Detail</button>
                                </div>
                                <div id="program-detail-list">
                                    <?php foreach ($program_details as $index => $program_detail): ?>
                                        <div class="program-detail-item mb-5">
                                            <div class="row mt-2">
                                                <div class="col-md-6">
                                                    <input type="text" name="price_table_program_details[<?php echo $index; ?>][title]" value="<?php echo esc_attr($program_detail['title'] ?? ''); ?>" placeholder="Program Detail Title" class="form-control" />
                                                </div>
                                                <div class="col-md-1">
                                                    <label>
                                                        <input type="checkbox" name="price_table_program_details[<?php echo $index; ?>][visible]" <?php checked($program_detail['visible'] ?? false, true); ?> /> Visible
                                                    </label>
                                                </div>
                                                <div class="col-md-1">
                                                    <button type="button" class="remove-program-detail btn btn-danger" style="border-radius: 10px; display: flex; justify-content: center; align-items: center; height: 30px; width: 30px; padding: 0; text-align: center;">✖</button>
                                                </div>
                                            </div>
                                            <div class="items-container">
                                                <?php foreach (($program_detail['items'] ?? []) as $item_index => $item): ?>
                                                    <div class="item-row row">
                                                        <?php
                                                        $name = $item['name'] ?? '';
                                                        $info = $item['info'] ?? '';
                                                        ?>

                                                        <div class="col-md-5">
                                                            <input type="text" name="price_table_program_details[<?php echo $index; ?>][items][<?php echo $item_index; ?>][name]" value="<?php echo esc_attr( $name ); ?>" placeholder="Item Name" class="form-control" />
                                                        </div>
                                                        
                                                        <div class="col-md-5">
                                                        <?php
                                                        // Render del editor
                                                        $editor_id = 'price_table_program_details_' . $index . '_items_' . $item_index . '_info';
                                                        wp_editor( $info, $editor_id, [
                                                            'textarea_name' => "price_table_program_details[{$index}][items][{$item_index}][info]",
                                                            'textarea_rows' => 4,
                                                            'media_buttons' => false,
                                                            'tinymce' => [
                                                                'toolbar1' => 'bold italic underline | bullist numlist | link unlink',
                                                                'toolbar2' => '',
                                                            ],
                                                            'quicktags' => false,
                                                        ] );
                                                        ?>
                                                    </div>

                                                        <div class="col-md-1">
                                                            <button type="button" class="remove-item btn btn-danger" style="border-radius: 10px; display: flex; justify-content: center; align-items: center; height: 30px; width: 30px; padding: 0; text-align: center;">✖</button>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                            <div class="mt-2">
                                                <button type="button" class="add-item btn btn-primary" data-index="<?php echo $index; ?>" style="border-radius: 10px; padding: 2px 24px; font-size: 16px;">+ Add Item</button>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <h2><?php _e('Visibility Settings', self::TRANSLATION_DOMAIN); ?></h2>
                <table class="widefat">
                    <thead>
                        <tr>
                            <th><?php _e('Program Detail', self::TRANSLATION_DOMAIN); ?></th>
                            <th><?php _e('Item', self::TRANSLATION_DOMAIN); ?></th>
                            <?php foreach ($posts as $post): ?>
                                <th><?php echo esc_html($post->post_title); ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($program_details as $program_detail_index => $program_detail): ?>
                            <?php foreach (($program_detail['items'] ?? []) as $item_index => $item): ?>
                                <tr>
                                    <td><?php echo esc_html($program_detail['title'] ?? ''); ?></td>
                                    <td><?php echo esc_html($item['name'] ?? ''); ?></td>
                                    <?php foreach ($posts as $post): ?>
                                        <td>
                                            <input type="checkbox" name="price_table_visibility[<?php echo $program_detail_index; ?>][<?php echo $item_index; ?>][<?php echo $post->ID; ?>]" 
                                                <?php checked($visibility[$program_detail_index][$item_index][$post->ID] ?? false, true); ?> />
                                        </td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <button type="submit" class="button button-primary mt-4"><?php _e('Save Settings', self::TRANSLATION_DOMAIN); ?></button>
            </form>
        </div>
        
        <script>
        jQuery(document).ready(function($) {
            $('.upload-image-button').on('click', function(e) {
                e.preventDefault();
                var button = $(this);
                var custom_uploader = wp.media({
                    title: '<?php _e('Select Image', self::TRANSLATION_DOMAIN); ?>',
                    button: {
                        text: '<?php _e('Use this image', self::TRANSLATION_DOMAIN); ?>'
                    },
                    multiple: false
                }).on('select', function() {
                    var attachment = custom_uploader.state().get('selection').first().toJSON();
                    button.siblings('.image-url').val(attachment.url);
                    button.siblings('.image-preview').attr('src', attachment.url).show();
                }).open();
            });

            $('#add-program-detail').on('click', function() {
                var index = $('#program-detail-list .program-detail-item').length;
                var newDetail = `
                    <div class="program-detail-item mb-5">
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <input type="text" name="price_table_program_details[` + index + `][title]" placeholder="Program Detail Title" class="form-control" />
                            </div>
                            <div class="col-md-1">
                                <label>
                                    <input type="checkbox" name="price_table_program_details[` + index + `][visible]" /> Visible
                                </label>
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="remove-program-detail btn btn-danger" style="border-radius: 10px; display: flex; justify-content: center; align-items: center; height: 30px; width: 30px; padding: 0; text-align: center;">✖</button>
                            </div>
                        </div>
                        <div class="items-container"></div>
                        <div class="mt-2">
                            <button type="button" class="add-item btn btn-primary" data-index="` + index + `" style="border-radius: 10px; padding: 2px 24px; font-size: 16px;">+ Add Item</button>
                        </div>
                    </div>
                `;
                $('#program-detail-list').append(newDetail);
            });

            $(document).on('click', '.remove-program-detail', function() {
                $(this).closest('.program-detail-item').remove();
            });

            $(document).on('click', '.add-item', function() {
                var index = $(this).data('index');
                var itemIndex = $(this).closest('.program-detail-item').find('.item-row').length;
                var newItem = `
                    <div class="item-row row">
                        <div class="col-md-5">
                            <input type="text" name="price_table_program_details[` + index + `][items][` + itemIndex + `][name]" placeholder="Item Name" class="form-control" />
                        </div>
                        <div class="col-md-5">
                            <textarea name="price_table_program_details[` + index + `][items][` + itemIndex + `][info]" placeholder="Item Info" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-md-1">
                            <button type="button" class="remove-item btn btn-danger" style="border-radius: 10px; display: flex; justify-content: center; align-items: center; height: 30px; width: 30px; padding: 0; text-align: center;">✖</button>
                        </div>
                    </div>
                    `;

                $(this).closest('.program-detail-item').find('.items-container').append(newItem);
            });

            $(document).on('click', '.remove-item', function() {
                $(this).closest('.item-row').remove();
            });
        });
        </script>
        
        <?php
    }
}