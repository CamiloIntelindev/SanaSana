<?php
/**
 * @package Questionnaire
 */

namespace SanasanaInit\Questionnaire;

use SanasanaInit\General\BaseController;

class QuestionnaireMetaBox extends BaseController
{
    public function register()
    {
        add_action('add_meta_boxes', [$this, 'add_questionnaire_metaboxes']);
        add_action('save_post', [$this, 'save_questionnaire_meta']);
    }

    public function add_questionnaire_metaboxes()
    {
        add_meta_box(
            'questionnaire_meta',
            __('Configuración del Cuestionario', 'sanasanainit'),
            [$this, 'render_questionnaire_metabox'],
            'questionnaire',
            'normal',
            'high'
        );
    }

    public function render_questionnaire_metabox($post)
    {
        $questions = get_post_meta($post->ID, '_questionnaire_questions', true);
        $mode = get_post_meta($post->ID, '_questionnaire_mode', true) ?: 'simple';
        if (!is_array($questions)) $questions = [];

        wp_nonce_field('questionnaire_nonce_action', 'questionnaire_nonce_field');
        ?>

        <div id="questionnaire-wrapper">
            <p>
                <label for="questionnaire_mode"><strong>Modo de Visualización:</strong></label>
                <select name="questionnaire_mode" id="questionnaire_mode">
                    <option value="simple" <?php selected($mode, 'simple'); ?>>Cuestionario Simple</option>
                    <option value="steps" <?php selected($mode, 'steps'); ?>>Cuestionario por Pasos</option>
                </select>
            </p>

            <?php foreach ($questions as $qIndex => $question) : ?>
                <div class="questionnaire-block" data-index="<?php echo esc_attr($qIndex); ?>" style="margin-bottom:30px; padding:15px; border:2px solid #0073aa;">

                    <label><strong>Imagen:</strong></label><br>
                    <input type="text" name="questions_img[<?php echo  $qIndex; ?>]" value="<?php echo esc_url($question['image'] ?? ''); ?>" class="questionnaire-image-url" />
                    <button type="button" class="button select-questions_img">Seleccionar Imagen</button>
                    <br>

                    <label><strong>Pregunta:</strong></label><br>
                    <input type="text" name="questions[<?php echo  $qIndex; ?>][text]" value="<?php echo esc_attr($question['text'] ?? ''); ?>" />

                    <label><strong>Tipo de campo:</strong></label><br>
                    <select name="questions[<?php echo  $qIndex; ?>][type]" class="questionnaire-question-type">
                        <option value="radio" <?php selected($question['type'] ?? '', 'radio'); ?>>Opciones (una sola respuesta)</option>
                        <option value="checkbox" <?php selected($question['type'] ?? '', 'checkbox'); ?>>Opciones múltiples</option>
                        <option value="text" <?php selected($question['type'] ?? '', 'text'); ?>>Respuesta corta</option>
                        <option value="slider" <?php selected($question['type'] ?? '', 'slider'); ?>>Slider de rango</option>
                    </select>

                    <div class="questionnaire-question-options" style="display: none;">
                        <label>Opciones:</label><br>
                        <div class="options-wrapper"></div>
                        <button type="button" class="questionnaire-add-option">Agregar opción</button>
                    </div>

                    <div class="questionnaire-question-slider" style="display: none;">
                        <label>Slider:</label><br>
                        <input type="number" name="questions[<?php echo  $qIndex; ?>][min]" placeholder="Mínimo" />
                        <input type="number" name="questions[<?php echo  $qIndex; ?>][max]" placeholder="Máximo" />
                        <input type="number" name="questions[<?php echo  $qIndex; ?>][step]" placeholder="Paso" />
                    </div>

                    <button type="button" class="remove-question">Eliminar pregunta</button>

                </div>
            <?php endforeach; ?>
        </div>

        <button type="button" class="add-question" id="add-question">Agregar pregunta</button>

        <script>
            (function ($) {
                $(document).ready(function () {
                    var qIndex = <?php echo count($questions); ?>;

                    $(document).on('click', '#add-question', function () {
                        const newQuestion = `
                        <div class="questionnaire-block" data-index="${qIndex}" style="margin-bottom:30px; padding:15px; border:2px solid #0073aa;">
                            <input type="text" name="questions_img[${qIndex}]" class="questionnaire-image-url" placeholder="URL de la imagen" />
                            <button type="button" class="button select-questions_img">Seleccionar Imagen</button><br>

                            <input type="text" name="questions[${qIndex}][text]" placeholder="Escribe la pregunta..." />

                            <select name="questions[${qIndex}][type]" class="questionnaire-question-type">
                                <option value="radio">Opciones (una sola respuesta)</option>
                                <option value="checkbox">Opciones múltiples</option>
                                <option value="text">Respuesta corta</option>
                                <option value="slider">Slider de rango</option>
                            </select>

                            <div class="questionnaire-question-options" style="display: none;">
                                <label>Opciones:</label>
                                <div class="options-wrapper"></div>
                                <button type="button" class="questionnaire-add-option">Agregar opción</button>
                            </div>

                            <div class="questionnaire-question-slider" style="display: none;">
                                <input type="number" name="questions[${qIndex}][min]" placeholder="Mínimo" />
                                <input type="number" name="questions[${qIndex}][max]" placeholder="Máximo" />
                                <input type="number" name="questions[${qIndex}][step]" placeholder="Paso" />
                            </div>

                            <button type="button" class="remove-question">Eliminar pregunta</button>
                        </div>`;

                        $('#questionnaire-wrapper').append(newQuestion);
                        qIndex++;
                    });

                    $(document).on('click', '.remove-question', function () {
                        $(this).closest('.questionnaire-block').remove();
                    });

                    $(document).on('click', '.select-questions_img', function (e) {
                        e.preventDefault();
                        const button = $(this);
                        const input = button.siblings('.questionnaire-image-url');

                        const mediaUploader = wp.media({
                            title: 'Seleccionar Imagen',
                            button: { text: 'Usar esta imagen' },
                            multiple: false
                        });

                        mediaUploader.on('select', function () {
                            const attachment = mediaUploader.state().get('selection').first().toJSON();
                            input.val(attachment.url);
                        });

                        mediaUploader.open();
                    });
                });
            })(jQuery);
        </script>
        <?php
    }

    public function save_questionnaire_meta($post_id)
    {
        if (!isset($_POST['questionnaire_nonce_field']) || !wp_verify_nonce($_POST['questionnaire_nonce_field'], 'questionnaire_nonce_action')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    }
}
