<?php
/**
 * @package PriceTable
 */

namespace SanasanaInit\Questionnaire;

use SanasanaInit\General\BaseController;
use WP_Query;

class QuestionnaireShortcode extends BaseController
{
    public function register() {
        add_shortcode('questionnaire_render', [$this, 'render_questionnaire_shortcode']);
		add_shortcode('cuestionario' , [$this, 'render_cuestionario']);
    }

    public function render_questionnaire_shortcode($atts) {
		$atts = shortcode_atts([
			'title' => '',
		], $atts, 'questionnaire_render');

		if (empty($atts['title'])) return '<p>No questionnaire title provided.</p>';

		$args = [
			'post_type'      => 'questionnaire',
			'title'          => $atts['title'],
			'posts_per_page' => 1,
		];
		$query = new WP_Query($args);
		if (!$query->have_posts()) return '<p>Questionnaire not found.</p>';

		$post = $query->posts[0];

		$mode = get_post_meta($post->ID, '_questionnaire_mode', true) ?: 'simple';
		$questions = get_post_meta($post->ID, '_questionnaire_questions', true);

		if (!is_array($questions)) return '<p>No questions defined.</p>';

		ob_start();
		?>
		<div class="questionnaire-wrapper questionnaire-mode-<?php echo esc_attr($mode); ?>" data-mode="<?php echo esc_attr($mode); ?>">
			<form class="questionnaire-form" method="post">
				<?php foreach ($questions as $index => $question): ?>
					<div class="questionnaire-question-block" data-question-index="<?php echo esc_attr($index); ?>">
						<div class="questionnaire-question-steps">
							<div><?php _e('Paso', 'textdomain'); ?></div>
							<span></span>
							<div><?php _e('De', 'textdomain'); ?></div>
							<span></span>
						</div>

						<div class="questionnaire-question-title">
							<h2 class="questionnaire-question-title"><?php echo esc_html($question['text'] ?? 'Untitled'); ?></h2>
						</div>

						<div class="questionnaire-columns">
							<?php if (!empty($question['image'])): ?>
								<div class="questionnaire-col-left">
									<img src="<?php echo esc_url($question['image']); ?>" alt="Question Image" class="questionnaire-question-image" />
								</div>
							<?php endif; ?>

							<div class="questionnaire-col-right">
								<?php
								$type = $question['type'] ?? 'text';
								$name = 'response[' . $index . ']';

								switch ($type) {
									case 'radio':
										if (!empty($question['options'])) {
											echo '<ul class="questionnaire-options-list">';
											foreach ($question['options'] as $optIndex => $option) {
												$option_id = 'q' . $index . '_opt' . $optIndex;
												echo '<li>';
												echo '<input type="radio" id="' . esc_attr($option_id) . '" name="' . esc_attr($name) . '" value="' . esc_attr($option) . '">';
												echo '<label for="' . esc_attr($option_id) . '">' . esc_html($option) . '</label>';
												echo '</li>';
											}
											echo '</ul>';
										}
										break;

									case 'checkbox':
										if (!empty($question['options'])) {
											echo '<ul class="questionnaire-options-list">';
											foreach ($question['options'] as $optIndex => $option) {
												$option_id = 'q' . $index . '_opt' . $optIndex;
												echo '<li>';
												echo '<input type="checkbox" id="' . esc_attr($option_id) . '" name="' . esc_attr($name) . '[]" value="' . esc_attr($option) . '">';
												echo '<label for="' . esc_attr($option_id) . '">' . esc_html($option) . '</label>';
												echo '</li>';
											}
											echo '</ul>';
										}
										break;

									case 'slider':
										$marks = [0, 18, 40, 60, '80+'];
										echo '<div class="questionnaire-slider-wrapper custom-slider">';
										echo '<div class="slider-tooltip">0</div>';
										echo '<div class="slider-track">';
										echo '<div class="slider-fill"></div>';
										echo '<div class="slider-range"></div>';
										echo '<div class="slider-thumb"></div>';
										echo '</div>';
										echo '<ul class="slider-marks">';
										foreach ($marks as $mark) {
											echo '<li>' . esc_html($mark) . '</li>';
										}
										echo '</ul>';
										echo '</div>';
										echo '<input type="hidden" name="slider_age" id="slider_age" class="custom-slider-value" value="">';
										break;

									case 'text':
									default:
										$enabled = $question['short_text_enabled'] ?? false;
										if ($enabled) {
											echo '<input type="text" name="' . esc_attr($name) . '" class="questionnaire-response" placeholder="Escribe tu respuesta...">';
										} else {
											echo '<p><em>Respuesta corta no permitida.</em></p>';
										}
										break;
								}
								?>
								<input type="hidden" name="question_ids[]" value="<?php echo esc_attr($index); ?>" />
							</div>
						</div>
						<div class="questionnaire-buttons-container">
							<div class="questionnaire-buttons-col-left">
								<input type="button" class="questionnaire-buttons-left" value="<?php _e('Volver', 'textdomain'); ?>">
							</div>
							<div class="questionnaire-buttons-col-right">
								<input type="button" class="questionnaire-buttons-right" value="<?php _e('Continuar', 'textdomain'); ?>">
							</div>
						</div>
					</div>
				<?php endforeach; ?>

				<div class="questionnaire-submit-wrapper">
					<button type="submit" class="questionnaire-submit-button"><?php _e('Enviar respuestas', 'sanasana'); ?></button>
				</div>
			</form>
		</div>
		<?php

		wp_reset_postdata();
		return ob_get_clean();
	}

	
	public function render_cuestionario(){
		?>
			<!-- HTML Structure -->
<div class="questionnaire-container">
    <!-- Step 1 -->
    <div class="questionnaire-step" data-step="1">
        <div class="questionnaire-step-header">
            <h2><?php _e('¿Cuál es tu rango de edad?','sanasana'); ?> </h2>
        </div>
        <div class="questionnaire-step-content">
            <div class="questionnaire-step-image">
                <img src="https://sanasanastoragews.blob.core.windows.net/blobwsdev/2025/06/SANASANA-ILUSTRACIONESquiz-1-6841f9b8b3ee7.webp" alt="" />
            </div>
            <div class="questionnaire-step-inputs">
                <div class="range-container">
                    <input 
                        type="range" 
                        id="ageRange" 
                        name="ageRange" 
                        min="0" 
                        max="80" 
                        step="1" 
                        value="0" 
                        list="ageMarks" 
                        aria-valuemin="18" 
                        aria-valuemax="80" 
                        aria-valuenow="18" 
                    >
                    <datalist id="ageMarks">
                        <div value="0" label="0">0</div>
                        <div value="18" label="18">18</div>
                        <div value="40" label="40">40</div>
                        <div value="60" label="60">60</div>
                        <div value="80" label="80+">80+</div>
                    </datalist>
                    <div class="range-value"><span id="rangeValue">0</span></div>
                </div>
            </div>
        </div>
        <div class="questionnaire-step-actions">
            <div class="action-left">
                <!--<button class="btn-back" data-action="back"><?php _e('Volver', 'sanasana'); ?> </button>-->
            </div>
            <div class="action-right">
                <button class="btn-next" data-action="next"><?php _e('Continuar', 'sanasana'); ?></button>
            </div>
        </div>
    </div>

    <!-- Step 2 (Duplicate structure for additional steps) -->
    <div class="questionnaire-step" data-step="2" style="display: none;">
        <div class="questionnaire-step-header">
            <h2><?php _e('¿Cuál es tu nivel de actividad física?','sanasana'); ?> </h2>
        </div>
        <div class="questionnaire-step-content">
            <div class="questionnaire-step-image">
                <img src="https://sanasanastoragews.blob.core.windows.net/blobwsdev/2025/06/SANASANA-ILUSTRACIONESquiz1-_Recuperado_.webp" alt="" />
            </div>
                <div class="questionnaire-step-inputs">
					
                    <ul class="choise-group activity">
                        <li>
                            <input type="radio" id="activity1" name="activity_level" value="pasos">
                            <label for="activity1"><?php _e('Los pasos que doy dentro de mi casa.','sanasana'); ?></label>
                        </li>
                        <li>
                            <input type="radio" id="activity2" name="activity_level" value="gym">
                            <label for="activity2"><?php _e('El gym es mi segundo hogar.','sanasana'); ?></label>
                        </li>
                        <li>
                            <input type="radio" id="activity3" name="activity_level" value="competencia">
                            <label for="activity3"><?php _e('Entrenando para una competencia.','sanasana'); ?></label>
                        </li>
                        <li>
                            <input type="radio" id="activity4" name="activity_level" value="algo">
                            <label for="activity4"><?php _e('Alguna actividad semanal.','sanasana'); ?></label>
                        </li>
                    </ul>
                </div>
        </div>
        <div class="questionnaire-step-actions">
            <div class="action-left">
                <button class="btn-back" data-action="back"><?php _e('Volver', 'sanasana'); ?> </button>
            </div>
            <div class="action-right">
                <button class="btn-next" data-action="next"><?php _e('Continuar', 'sanasana'); ?></button>
            </div>
        </div>
    </div>

    <!-- Step 3 (Duplicate structure for additional steps) -->
    <div class="questionnaire-step" data-step="3" style="display: none;">
        <div class="questionnaire-step-header">
            <h2><?php _e('¿Planéas ser mamá?','sanasana'); ?> </h2>
        </div>
        <div class="questionnaire-step-content">
            <div class="questionnaire-step-image">
                <img src="https://sanasanastoragews.blob.core.windows.net/blobwsdev/2025/06/SANASANA-ILUSTRACIONES3-_Recuperado_.webp" alt="" />
            </div>
            <div class="questionnaire-step-inputs">
                <ul class="choise-group  pregnancy">
                        <li>
                            <input type="radio" id="pregnancy1" name="pregnancy" value="si">
                            <label for="pregnancy1"><?php _e('Sí, estamos llamando a la cigüeña.','sanasana'); ?></label>
                        </li>
                        <li>
                            <input type="radio" id="pregnancy2" name="pregnancy" value="ya_viene">
                            <label for="pregnancy2"><?php _e('La cigüeña ya viene en camino.','sanasana'); ?></label>
                        </li>
                        <li>
                            <input type="radio" id="pregnancy3" name="pregnancy" value="no">
                            <label for="pregnancy3"><?php _e('No por el momento.','sanasana'); ?></label>
                        </li>
                        <li>
                            <input type="radio" id="pregnancy4" name="pregnancy" value="x">
                            <label for="pregnancy4"><?php _e('Ya no aplico.','sanasana'); ?></label>
                        </li>
                    </ul>
            </div>
        </div>
        <div class="questionnaire-step-actions">
            <div class="action-left">
                <button class="btn-back" data-action="back"><?php _e('Volver', 'sanasana'); ?> </button>
            </div>
            <div class="action-right">
                <button class="btn-next" data-action="next"><?php _e('Continuar', 'sanasana'); ?></button>
            </div>
        </div>
    </div>

    <!-- Step 4 (Duplicate structure for additional steps) -->
    <div class="questionnaire-step" data-step="4" style="display: none;">
        <div class="questionnaire-step-header">
            <h2><?php _e('¿Qué necesitas en tú día para ser saludable?','sanasana'); ?> </h2>
        </div>
        <div class="questionnaire-step-content">
            <div class="questionnaire-step-image">
                <img src="https://sanasanastoragews.blob.core.windows.net/blobwsdev/2025/06/SANASANA-ILUSTRACIONES3-_Recuperado_.webp" alt="" />
            </div>
            <div class="questionnaire-step-inputs">
                <ul class="choise-group healthy">
                        <li>
                            <input type="radio" id="healthy1" name="healthy" value="saludable">
                            <label for="healthy1"><?php _e('Nada, soy saludable desde que me levanto.','sanasana'); ?></label>
                        </li>
                        <li>
                            <input type="radio" id="healthy2" name="healthy" value="bicicleta">
                            <label for="healthy2"><?php _e('Una bicicleta de montaña.','sanasana'); ?></label>
                        </li>
                        <li>
                            <input type="radio" id="healthy3" name="healthy" value="pastilla">
                            <label for="healthy3"><?php _e('Mis 5 pastillas diarias.','sanasana'); ?></label>
                        </li>
                    </ul>
            </div>
        </div>
        <div class="questionnaire-step-actions">
            <div class="action-left">
                <button class="btn-back" data-action="back"><?php _e('Volver', 'sanasana'); ?> </button>
            </div>
            <div class="action-right">
                <button class="btn-next" data-action="next"><?php _e('Continuar', 'sanasana'); ?></button>
            </div>
        </div>
    </div>

    <!-- Final Step -->
    <div class="questionnaire-step" data-step="5" style="display: none;">
        <div class="questionnaire-step-header">
            <h2><?php _e('¿Cada cuanto visitás a tú médico?','sanasana'); ?> </h2>
        </div>
        <div class="questionnaire-step-content">
            <div class="questionnaire-step-image">
                <img src="https://sanasanastoragews.blob.core.windows.net/blobwsdev/2025/06/SANASANA-ILUSTRACIONES3-_Recuperado_.webp" alt="" />
            </div>
            <div class="questionnaire-step-inputs">
                <ul class="choise-group doctor">
                        <li>
                            <input type="radio" id="doctor1" name="doctor" value="nunca">
                            <label for="doctor1"><?php _e('Nunca hay espacio en mi agenda.','sanasana'); ?></label>
                        </li>
                        <li>
                            <input type="radio" id="doctor2" name="doctor" value="cumple">
                            <label for="doctor2"><?php _e('Cada Cumpleaños.','sanasana'); ?></label>
                        </li>
                        <li>
                            <input type="radio" id="doctor3" name="doctor" value="el_doctor">
                            <label for="doctor3"><?php _e('El doctor es parte de mi familia.','sanasana'); ?></label>
                        </li>
                    </ul>
            </div>
        </div>
        <div class="questionnaire-step-actions">
            <div class="action-left">
                <button class="btn-back" data-action="back"><?php _e('Volver', 'sanasana'); ?> </button>
            </div>
            <div class="action-right">
                <button class="btn-submit" data-action="submit"><?php _e('Finalizar', 'sanasana'); ?></button>
            </div>
        </div>
    </div>
</div>

<!-- Basic CSS -->
<style>
.questionnaire-container {
    width: 100%;
    max-width: 1440px;
    margin: 0 auto;
    padding: 20px;
    background-color: #f9f9f9;
    border-radius: 8px;
    /*box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);*/
}

.questionnaire-step {
    display: none;
}

.questionnaire-step.active {
    display: block;
}

.questionnaire-step-header {
    margin-bottom: 70px;
}
.questionnaire-step-header > h2 {
    text-align: center;
	font-family: "Moranga Medium", Verdana, Arial, sans-serif;
	font-weight: 500 ;
	font-size: 48px;
	line-height: 48px;
	letter-spacing: 0%;
	text-align: center;
	color: #5166EC;
}

.questionnaire-step-content {
    display: flex;
    gap: 20px;
    margin-bottom: 20px;
	flex-wrap: wrap;
}
.questionnaire-step-image {
    width: 45%;
	display: flex;
	justify-content: flex-start;
	align-items: center;
}
.questionnaire-step-image > img{
	min-width: 548px;
}
.questionnaire-step-inputs{
	width: 45%;
	display: flex;
	justify-content: flex-start;
	align-items: center;
}
.range-container {
    position: relative;
    width: 100%;
    max-width: 600px;
    margin: 20px auto;
}

input[type="range" i] {
    width: 100%;
    -webkit-appearance: none;
    background-color: transparent;
    height: 20px;
    border-radius: 4px;
    outline: none; /* Remove focus outline */
    cursor: pointer;
    position: relative;
    z-index: 1;
}
input[type="range" i]:focus {
    outline: none;
	border: none !important;
}

input[type="range"]::-webkit-slider-thumb {
    -webkit-appearance: none;
    width: 16px;
    height: 16px;
    background-color: #ffcc00;
    border-radius: 50%;
    cursor: pointer;
}

input[type="range"]::-moz-range-thumb {
    width: 16px;
    height: 16px;
    background-color: #ffcc00;
    border-radius: 50%;
    cursor: pointer;
}

.range-value {
    position: absolute;
    top: -40px;
    left: 0;
    background-color: #000;
    color: #fff;
    padding: 5px 10px;
    border-radius: 5px;
    font-size: 12px;
    transform: translateX(-50%);
    white-space: nowrap;
	visibility: hidden;
	opacity: 0;
	transition: visibility .5s, opacity .5s;
}

datalist#ageMarks {
    display: flex;
    width: 100%;
    justify-content: space-between;
    font-size: 16px;
    line-height: 24px;
    font-weight: 400;
    font-family: 'Poppins', sans-serif;
}
	
.questionnaire-step-actions {
    display: flex;
    justify-content: space-between;
    margin-top: 70px;
	min-width: 100%;
}
button.btn-back {
    font-size: 16px;
    background: transparent;
    width: 220px;
    border: solid 2px #5166EC;
    height: 48px;
    border-radius: 8px;
    color: #5166EC;
    font-weight: 400;
}
button.btn-submit,
button.btn-next{
    width: 220px;
    height: 48px;
    border-radius: 8px;
    background: #F9DE42;
    font-size: 16px;
    font-weight: 400;
	color: #000000;
}
	
/**/
ul.choise-group {
    list-style: none;
    margin: 0;
    padding: 0;
	min-width: 100%;
}
	ul.choise-group > li{
	height: 20px;
    display: flex;
    padding: 30px 0 20px 0;
    justify-content: flex-start;
    align-items: center;
    border-bottom: solid 1px #5166EC;
	}
	ul.choise-group > li > input[type="radio"]{
		width: 20px;
		height: 20px;
		margin-right: 20px;
	}
ul.choise-group > li > label{
	font-family: 'Poppins';
	font-weight: 400;
	font-size: 16px;
	line-height: 24px;
	letter-spacing: 0%;
	vertical-align: middle;

}
	@media(max-width : 1024px){
		.questionnaire-step-image,
		.questionnaire-step-inputs{
			width: 100%;
			max-width: 660px;
			justify-content: center;
		}
		.questionnaire-step-content {
			justify-content: center;
		}
	}
	
	@media(max-width : 768px){
		.questionnaire-step-header > h2{
			font-size: 36px;
			line-height: 40px;
		}
		.questionnaire-step-image > img {
			min-width: 100% !important;
		}
		.questionnaire-step-actions {
			flex-wrap: wrap-reverse;
		}
		.questionnaire-step-actions > .action-left,
		.questionnaire-step-actions > .action-right{
			width: 100%;
		}
		.questionnaire-step-actions > .action-left, .questionnaire-step-actions > .action-right {
			width: 100%;
			display: flex;
			justify-content: flex-end;
			padding: 10px 0;
		}
		ul.choise-group > li > label {
			font-size: 14px;
			line-height: 20px;
		}
	}
	
	
	
</style>

<!-- Basic jQuery -->
<script>
jQuery(document).ready(function() {
    var currentStep = 1;
    var totalSteps = jQuery('.questionnaire-step').length;
    var rangeInput = jQuery('#ageRange');
    var rangeValue = jQuery('.range-value');

    jQuery('.questionnaire-step[data-step="1"]').addClass('active');
	
		rangeInput.on('input', function() {
		const value = jQuery(this).val();
		const displayValue = value == 80 ? '80+' : value;

		rangeValue.text(displayValue);

		// Calcular posición del tooltip
		const inputWidth = jQuery(this).width();
		const offsetLeft = (value / 80) * inputWidth;
		rangeValue.css('left', offsetLeft + 'px');
		rangeValue.css({ 'visibility': 'visible', 'opacity': '1', 'transition': 'visibility 0.5s, opacity 0.5s' });
	});

    jQuery('.btn-next').on('click', function(e) {
		var currentStep = jQuery(this).closest('.questionnaire-step').data('step');
		var isValid = true;

		// Validación del rango de edad en el paso 1
		if (currentStep === 1) {
			var age = parseInt(jQuery("#ageRange").val());
			if (age === 0) {
				//alert("Por favor, selecciona un rango de edad válido.");
				Swal.fire({
				  icon: 'warning',
				  title: 'Oops...',
				  text: 'Por favor, selecciona un rango de edad válido.',
				  confirmButtonText: 'Entendido'
				});
				isValid = false;
			}
		}

		// Validación de opciones seleccionadas en los pasos 2, 3, 4 y 5
		var inputs = jQuery(`.questionnaire-step[data-step="${currentStep}"]`).find('input[type="radio"]');
		if (inputs.length > 0 && inputs.filter(':checked').length === 0) {
			//alert("Por favor, selecciona una opción antes de continuar.");
			Swal.fire({
				icon: 'warning',
				title: 'Oops...',
				text: 'Por favor, selecciona una opción antes de continuar.',
				confirmButtonText: 'Entendido'
			});
			isValid = false;
		}

		// Si es válido, avanzamos al siguiente paso
		if (isValid) {
			jQuery(`.questionnaire-step[data-step="${currentStep}"]`).removeClass('active').hide();
			currentStep++;
			jQuery(`.questionnaire-step[data-step="${currentStep}"]`).addClass('active').show();
		}

		// Prevenir el avance si no es válido
		e.preventDefault();
	});

    jQuery('.btn-back').on('click', function() {
		var currentStep = jQuery(this).closest('.questionnaire-step').data('step');

		if (currentStep > 1) {
			jQuery(`.questionnaire-step[data-step="${currentStep}"]`).removeClass('active').hide();
			currentStep--;
			jQuery(`.questionnaire-step[data-step="${currentStep}"]`).addClass('active').show();
		}
	});

	
	
	//Submit last steep
	jQuery('.btn-submit').on('click', function() {
		var age = jQuery("#ageRange").val();
		var selectedActivity = jQuery('input[name="activity_level"]:checked').val();
		var selectedPregnancy = jQuery('input[name="pregnancy"]:checked').val();
		var selectedHealthy = jQuery('input[name="healthy"]:checked').val();
		var selectedDoctor = jQuery('input[name="doctor"]:checked').val();

		var baseUrl = window.location.origin;
		var targetUrl = baseUrl + "/price/control-total/";

		// Jerarquía de condiciones
		if (age >= 65) {
			window.location.href = baseUrl + "/price/vitalidad-dorada/";
			return;
		}

		if (selectedPregnancy === "ya_viene") {
			window.location.href = baseUrl + "/price/mama-bebe/";
			return;
		}

		if (selectedHealthy === "saludable") {
			window.location.href = baseUrl + "/price/salud-en-equilibrio/";
			return;
		}

		if (selectedActivity === "gym" || selectedActivity === "competencia") {
			window.location.href = baseUrl + "/price/vida-activa/";
			return;
		}

		if (selectedHealthy === "bicicleta") {
			window.location.href = baseUrl + "/price/vida-activa/";
			return;
		}

		if (selectedDoctor === "nunca") {
			window.location.href = baseUrl + "/price/vida-activa/";
			return;
		}

		if (selectedHealthy === "pastilla") {
			window.location.href = baseUrl + "/price/vitalidad-dorada/";
			return;
		}

		if (selectedDoctor === "el_doctor") {
			window.location.href = baseUrl + "/price/control-total/";
			return;
		}

		// Si no se cumple ninguna condición, redireccionar a la URL por defecto
		window.location.href = targetUrl;
	});

});
</script>


		<?php
		return ob_get_clean();
	}
}
