<?php
/**
 * @package TabsTable
 */

 namespace SanasanaInit\TabsTable;

 use SanasanaInit\General\BaseController;
 use WP_Query;

class TabsTableMetaBox extends BaseController
{
    public function register()
    {
        add_action('add_meta_boxes', [$this, 'add_tabs_table_metaboxes']);
        add_action('save_post', [$this, 'save_tabs_table_meta']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_media_uploader']); // Cargar el script de la galería de medios
		
		// FAQ
		add_action('add_meta_boxes', [$this, 'add_faq_tab_metabox']);
        add_action('save_post', [$this, 'save_faq_tab_metabox']);
    }

    // Carga el script de la galería de medios
    public function enqueue_media_uploader()
    {
        wp_enqueue_media();
    }

    // Agrega el metabox
    public function add_tabs_table_metaboxes()
    {
        add_meta_box(
            'tabs_table_meta',
            __('Tabs Table Details', 'TabsTable'),
            [$this, 'render_tabs_table_metabox'],
            'tabs',
            'normal',
            'high'
        );
    }

    // Renderiza los campos del metabox
    public function render_tabs_table_metabox($post)
    {
        // Obtener los valores almacenados (si existen)
        $tabs_data = get_post_meta($post->ID, '_tabs_table_data', true);
        $tab_layout = get_post_meta($post->ID, '_tabs_table_layout', true) ?: 'horizontal'; // Asegurar un valor por defecto

        if (!is_array($tabs_data)) {
            $tabs_data = [];
        }

        wp_nonce_field('save_tabs_table_meta', 'tabs_table_nonce');
        ?>

        <!-- Opción de Vista Global -->
        <p>
            <label><strong><?php _e('Tab Layout', 'TabsTable'); ?></strong></label><br>
            <input type="radio" name="tabs_table_layout" value="horizontal" <?php checked($tab_layout, 'horizontal'); ?>> Horizontal
            <input type="radio" name="tabs_table_layout" value="vertical" <?php checked($tab_layout, 'vertical'); ?>> Vertical
        </p>

        <!-- Contenedor de los Bloques Dinámicos -->
        <div id="tabs-container">
            <?php foreach ($tabs_data as $index => $tab) : ?>
                <div class="tab-block">
                    <p>
                        <label><?php _e('Tab Title', 'TabsTable'); ?></label>
                        <input type="text" name="tabs_table_data[<?php echo $index; ?>][title]" value="<?php echo esc_attr($tab['title']); ?>" style="width:100%;" />
                    </p>
                    <p>
                        <label><?php _e('Tab Brief', 'TabsTable'); ?></label>
                        <input type="text" name="tabs_table_data[<?php echo $index; ?>][excerpt]" value="<?php echo esc_attr($tab['excerpt']); ?>" style="width:100%;" />
                    </p>
                    
                    <!-- Campo para subir imagen -->
                    <p>
                        <label><?php _e('Tab Image', 'TabsTable'); ?></label><br>
                        <img class="tab-image-preview" src="<?php echo esc_url($tab['image'] ?? ''); ?>" style="max-width: 150px; display: <?php echo empty($tab['image']) ? 'none' : 'block'; ?>;">
                        <input type="hidden" name="tabs_table_data[<?php echo $index; ?>][image]" value="<?php echo esc_url($tab['image'] ?? ''); ?>">
                        <button type="button" class="upload-tab-image button">Seleccionar Imagen</button>
                        <button type="button" class="remove-tab-image button" style="display: <?php echo empty($tab['image']) ? 'none' : 'inline-block'; ?>;">Eliminar</button>
                    </p>

                    <p>
                        <label><?php _e('Tab Content', 'TabsTable'); ?></label>
                        <textarea name="tabs_table_data[<?php echo $index; ?>][content]" style="width:100%; height:100px;"><?php echo esc_textarea($tab['content']); ?></textarea>
                    </p>
                    <button type="button" class="remove-tab">❌ Eliminar</button>
                </div>
            <?php endforeach; ?>
        </div>

        <button type="button" id="add-tab">➕ Agregar Nuevo Bloque</button>
		
<script>
        jQuery(document).ready(function($) {
    var tabIndex = <?php echo count($tabs_data); ?>;

    $("#add-tab").click(function() {
        var newBlock = `
            <div class="tab-block">
                <p>
                    <label>Tab Title</label>
                    <input type="text" name="tabs_table_data[\${tabIndex}][title]" style="width:100%;" />
                </p>
                <p>
                    <label>Tab Brief</label>
                    <input type="text" name="tabs_table_data[\${tabIndex}][excerpt]" style="width:100%;" />
                </p>

                <p>
                    <label>Tab Image</label><br>
                    <img class="tab-image-preview" src="" style="max-width: 150px; display:none;">
                    <input type="hidden" name="tabs_table_data[\${tabIndex}][image]" value="">
                    <button type="button" class="upload-tab-image button">Seleccionar Imagen</button>
                    <button type="button" class="remove-tab-image button" style="display: none;">Eliminar</button>
                </p>

                <p><label>Tab Content</label></p>
                <textarea name="tabs_table_data[\${tabIndex}][content]" style="width:100%; height:100px;"></textarea>
                <button type="button" class="remove-tab">❌ Eliminar</button>
            </div>
        `;
        $("#tabs-container").append(newBlock);
        tabIndex++;
    });

    // Agregar funcionalidad para subir imágenes dinámicamente
    $(document).on("click", ".upload-tab-image", function() {
        var button = $(this);
        var preview = button.siblings(".tab-image-preview");
        var input = button.siblings("input[type='hidden']");
        var removeButton = button.siblings(".remove-tab-image");

        var mediaUploader = wp.media({
            title: "Seleccionar Imagen",
            button: { text: "Usar esta imagen" },
            multiple: false
        }).on("select", function() {
            var attachment = mediaUploader.state().get("selection").first().toJSON();
            input.val(attachment.url);
            preview.attr("src", attachment.url).show();
            removeButton.show();
        }).open();
    });

    // Eliminar imagen seleccionada
    $(document).on("click", ".remove-tab-image", function() {
        var button = $(this);
        var preview = button.siblings(".tab-image-preview");
        var input = button.siblings("input[type='hidden']");

        input.val("");
        preview.hide().attr("src", "");
        button.hide();
    });

    // Eliminar bloque de tab
    $(document).on("click", ".remove-tab", function() {
        $(this).parent(".tab-block").remove();
    });
});

    </script>

        <?php
    }

    // Guarda los valores ingresados en los metaboxes
    public function save_tabs_table_meta($post_id)
    {
        if (!isset($_POST['tabs_table_nonce']) || !wp_verify_nonce($_POST['tabs_table_nonce'], 'save_tabs_table_meta')) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        // Guardar la selección de la vista de los tabs
        if (isset($_POST['tabs_table_layout']) && in_array($_POST['tabs_table_layout'], ['horizontal', 'vertical'])) {
            update_post_meta($post_id, '_tabs_table_layout', sanitize_text_field($_POST['tabs_table_layout']));
        } else {
            delete_post_meta($post_id, '_tabs_table_layout'); // Eliminar si no está definido
        }

        // Guardar los datos de los tabs
        if (isset($_POST['tabs_table_data']) && is_array($_POST['tabs_table_data'])) {
            $clean_data = [];
            foreach ($_POST['tabs_table_data'] as $tab) {
                $clean_data[] = [
                    'title'   => sanitize_text_field($tab['title']),
                    'excerpt' => sanitize_text_field($tab['excerpt']),
                    'image'   => esc_url($tab['image'] ?? ''), // Nuevo campo de imagen
                    'content' => sanitize_textarea_field($tab['content']),
                ];
            }
            update_post_meta($post_id, '_tabs_table_data', $clean_data);
        }
    }
	
	//FAQ
	public function add_faq_tab_metabox()
    {
        add_meta_box(
            'faq_tab_metabox',
            __('FAQ Tabs', 'sanasana'),
            [$this, 'render_faq_tab_metabox'],
            'faq-tab',
            'normal',
            'high'
        );
    }

    public function render_faq_tab_metabox($post)
    {
        $tabs = get_post_meta($post->ID, '_faq_tabs', true) ?: [];

        wp_nonce_field('faq_tab_nonce_action', 'faq_tab_nonce_field');
        ?>
        <div id="faq-tabs-wrapper">
            <?php foreach ($tabs as $tIndex => $tab) : ?>
                <div class="faq-tab-block" data-index="<?php echo esc_attr($tIndex); ?>" style="margin-bottom:30px; padding:15px; border:2px solid #0073aa;">
                    <p>
                        <label><strong>Section Label (Tab Title):</strong></label><br>
                        <input type="text" name="faq_tabs[<?php echo $tIndex; ?>][label]" value="<?php echo esc_attr($tab['label']); ?>" class="widefat" />
                    </p>

                    <div class="faq-items-wrapper">
                        <?php foreach ($tab['items'] as $fIndex => $faq) : ?>
                            <div class="faq-item" style="margin-bottom:20px; padding:10px; border:1px solid #ccc;">
                                <p>
                                    <label>FAQ Title:</label><br>
                                    <input type="text" name="faq_tabs[<?php echo $tIndex; ?>][items][<?php echo $fIndex; ?>][title]" value="<?php echo esc_attr($faq['title']); ?>" class="widefat" />
                                </p>
                                <p>
                                    <label>FAQ Content:</label><br>
                                    <textarea name="faq_tabs[<?php echo $tIndex; ?>][items][<?php echo $fIndex; ?>][content]" class="widefat" rows="3"><?php echo esc_textarea($faq['content']); ?></textarea>
                                </p>
                                <button type="button" class="button remove-faq">Remove FAQ</button>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <button type="button" class="button add-faq">Add FAQ</button>
                    <button type="button" class="button remove-tab" style="margin-top:10px;">Remove Tab</button>
                </div>
            <?php endforeach; ?>
        </div>

        <button type="button" class="button button-primary" id="add-tab">Add New Tab</button>

        <script>
            (function ($) {
                $(document).ready(function () {
                    var tabIndex = <?php echo count($tabs); ?>;

                    $('#add-tab').on('click', function () {
                        const html = `
                            <div class="faq-tab-block" data-index="\${tabIndex}" style="margin-bottom:30px; padding:15px; border:2px solid #0073aa;">
                                <p>
                                    <label><strong>Section Label (Tab Title):</strong></label><br>
                                    <input type="text" name="faq_tabs[\${tabIndex}][label]" class="widefat" />
                                </p>
                                <div class="faq-items-wrapper"></div>
                                <button type="button" class="button add-faq">Add FAQ</button>
                                <button type="button" class="button remove-tab" style="margin-top:10px;">Remove Tab</button>
                            </div>`;
                        $('#faq-tabs-wrapper').append(html);
                        tabIndex++;
                    });

                    $(document).on('click', '.add-faq', function () {
                        const tabBlock = $(this).closest('.faq-tab-block');
                        const tabIndex = tabBlock.data('index');
                        const faqCount = tabBlock.find('.faq-item').length;

                        const faqHtml = `
                            <div class="faq-item" style="margin-bottom:20px; padding:10px; border:1px solid #ccc;">
                                <p>
                                    <label>FAQ Title:</label><br>
                                    <input type="text" name="faq_tabs[\${tabIndex}][items][\${faqCount}][title]" class="widefat" />
                                </p>
                                <p>
                                    <label>FAQ Content:</label><br>
                                    <textarea name="faq_tabs[\${tabIndex}][items][\${faqCount}][content]" class="widefat" rows="3"></textarea>
                                </p>
                                <button type="button" class="button remove-faq">Remove FAQ</button>
                            </div>`;
                        tabBlock.find('.faq-items-wrapper').append(faqHtml);
                    });

                    $(document).on('click', '.remove-faq', function () {
                        $(this).closest('.faq-item').remove();
                    });

                    $(document).on('click', '.remove-tab', function () {
                        $(this).closest('.faq-tab-block').remove();
                    });
                });
            })(jQuery);
        </script>
        <?php
    }

    public function save_faq_tab_metabox($post_id)
    {
        if (!isset($_POST['faq_tab_nonce_field']) || !wp_verify_nonce($_POST['faq_tab_nonce_field'], 'faq_tab_nonce_action')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

        if (isset($_POST['faq_tabs'])) {
            $cleaned_tabs = [];

            foreach ($_POST['faq_tabs'] as $tab) {
                $cleaned_tab = [
                    'label' => sanitize_text_field($tab['label']),
                    'items' => [],
                ];

                if (!empty($tab['items'])) {
                    foreach ($tab['items'] as $faq) {
                        $cleaned_tab['items'][] = [
                            'title' => sanitize_text_field($faq['title']),
                            'content' => sanitize_textarea_field($faq['content']),
                        ];
                    }
                }

                $cleaned_tabs[] = $cleaned_tab;
            }

            update_post_meta($post_id, '_faq_tabs', $cleaned_tabs);
        } else {
            delete_post_meta($post_id, '_faq_tabs');
        }
    }
}
