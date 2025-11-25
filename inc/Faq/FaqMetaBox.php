<?php
/**
 * @package Faq
 *
 */
namespace SanasanaInit\Faq;

use SanasanaInit\General\BaseController;

class FaqMetaBox extends BaseController
{
    public function register()
    {
        add_action('add_meta_boxes', [$this, 'add_faq_tab_metabox']);
        add_action('save_post', [$this, 'save_faq_tab_metabox']);
    }

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

                    <p>
                        <label><strong>Icono (imagen):</strong></label><br>
                        <?php $icon_url = $tab['icon'] ?? ''; ?>
                        <img src="<?php echo esc_url($icon_url); ?>" class="faq-tab-icon-preview" style="max-height: 40px; display: <?php echo $icon_url ? 'block' : 'none'; ?>;" />
                        <input type="hidden" name="faq_tabs[<?php echo $tIndex; ?>][icon]" value="<?php echo esc_url($icon_url); ?>" />
                        <button type="button" class="button select-icon">Seleccionar Imagen</button>
                    </p>

                    <div class="faq-items-wrapper">
                        <?php foreach ($tab['faqs'] as $fIndex => $faq) : ?>
                            <div class="faq-item" style="margin-bottom:15px; padding:10px; border:1px solid #ccc;">
                                <p>
                                    <label><strong>FAQ Title:</strong></label><br>
                                    <input type="text" name="faq_tabs[<?php echo $tIndex; ?>][faqs][<?php echo $fIndex; ?>][title]" value="<?php echo esc_attr($faq['title']); ?>" class="widefat" />
                                </p>
                                <p>
                                    <label><strong>FAQ Content:</strong></label><br>
                                    <!--<textarea name="faq_tabs[<?php echo $tIndex; ?>][faqs][<?php echo $fIndex; ?>][content]" class="widefat" rows="3"><?php echo esc_textarea($faq['content']); ?></textarea>-->
									<textarea name="faq_tabs[<?php echo $tIndex; ?>][faqs][<?php echo $fIndex; ?>][content]" class="widefat" rows="3"><?php echo isset($faq['content']) ? $faq['content'] : ''; ?></textarea>

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

        <button type="button" class="button button-primary" id="add-tab">Add Tab</button>

        <script>
            (function ($) {
                $(document).ready(function () {
                    var tabIndex = <?php echo count($tabs); ?>;

                    $('#add-tab').on('click', function () {
                        const tabHtml = $(
                            `<div class="faq-tab-block" data-index="${tabIndex}" style="margin-bottom:30px; padding:15px; border:2px solid #0073aa;">
                                <p>
                                    <label><strong>Section Label (Tab Title):</strong></label><br>
                                    <input type="text" name="faq_tabs[${tabIndex}][label]" class="widefat" />
                                </p>
                                <p>
                                    <label><strong>Icono (imagen):</strong></label><br>
                                    <img src="" class="faq-tab-icon-preview" style="max-height: 40px; display: none;" />
                                    <input type="hidden" name="faq_tabs[${tabIndex}][icon]" value="" />
                                    <button type="button" class="button select-icon">Seleccionar Imagen</button>
                                </p>
                                <div class="faq-items-wrapper"></div>
                                <button type="button" class="button add-faq">Add FAQ</button>
                                <button type="button" class="button remove-tab" style="margin-top:10px;">Remove Tab</button>
                            </div>`
                        );
                        $('#faq-tabs-wrapper').append(tabHtml);
                        tabIndex++;
                    });

                    $(document).on('click', '.remove-tab', function () {
                        $(this).closest('.faq-tab-block').remove();
                    });

                    $(document).on('click', '.add-faq', function () {
                        const tabBlock = $(this).closest('.faq-tab-block');
                        const wrapper = tabBlock.find('.faq-items-wrapper');
                        const tIndex = tabBlock.data('index');
                        const fIndex = wrapper.children('.faq-item').length;

                        const faqHtml = $(
                            `<div class="faq-item" style="margin-bottom:15px; padding:10px; border:1px solid #ccc;">
                                <p>
                                    <label><strong>FAQ Title:</strong></label><br>
                                    <input type="text" name="faq_tabs[${tIndex}][faqs][${fIndex}][title]" class="widefat" />
                                </p>
                                <p>
                                    <label><strong>FAQ Content:</strong></label><br>
                                    <textarea name="faq_tabs[${tIndex}][faqs][${fIndex}][content]" class="widefat" rows="3"></textarea>
                                </p>
                                <button type="button" class="button remove-faq">Remove FAQ</button>
                            </div>`
                        );
                        wrapper.append(faqHtml);
                    });

                    $(document).on('click', '.remove-faq', function () {
                        $(this).closest('.faq-item').remove();
                    });

                    $(document).on('click', '.select-icon', function (e) {
                        e.preventDefault();

                        const button = $(this);
                        const input = button.siblings('input[type="hidden"]');
                        const preview = button.siblings('img');

                        const mediaUploader = wp.media({
                            title: 'Seleccionar Icono',
                            button: { text: 'Usar esta imagen' },
                            multiple: false
                        });

                        mediaUploader.on('select', function () {
                            const attachment = mediaUploader.state().get('selection').first().toJSON();
                            input.val(attachment.url);
                            preview.attr('src', attachment.url).show();
                        });

                        mediaUploader.open();
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

        foreach ($_POST['faq_tabs'] as $tIndex => $tab) {
            $label = sanitize_text_field($tab['label']);
            $icon  = isset($tab['icon']) ? esc_url_raw($tab['icon']) : '';
            $faqs  = [];

            if (isset($tab['faqs']) && is_array($tab['faqs'])) {
                foreach ($tab['faqs'] as $fIndex => $faq) {
                    $title   = sanitize_text_field($faq['title']);
                    $content = isset($faq['content']) ? wp_kses_post($faq['content']) : '';

                    // ✅ Registrar string en WPML
                    if (function_exists('wpml_register_single_string')) {
                        $string_id = "faq_{$post_id}_{$tIndex}_{$fIndex}";
                        do_action('wpml_register_single_string', 'sanasana_faqs', $string_id, $content);
                    }

                    $faqs[] = [
                        'title'   => $title,
                        'content' => $content,
                    ];
                }
            }

            $cleaned_tabs[] = [
                'label' => $label,
                'icon'  => $icon,
                'faqs'  => $faqs,
            ];
        }

        update_post_meta($post_id, '_faq_tabs', $cleaned_tabs);
    } else {
        delete_post_meta($post_id, '_faq_tabs');
    }
}

}
