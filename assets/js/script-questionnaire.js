// script-questionnaire.js
/*jQuery(document).ready(function ($) {
    const $wrapper = $('#questionnaire-questions-wrapper');
    var qIndex = $wrapper.find('.questionnaire-question-block').length;

    // Agregar nueva pregunta
    $('#questionnaire-add-question').on('click', function () {
        const newQuestion = getQuestionBlock(qIndex);
        $wrapper.append(newQuestion);
        applyEvents($(newQuestion));
        qIndex++;
    });

    // Generar bloque de pregunta
    function getQuestionBlock(index) {
        return `
        <div class="questionnaire-question-block" data-index="${index}" style="margin-bottom: 20px; padding: 10px; border: 1px solid #ccc;">
            <label><strong>Pregunta:</strong></label><br>
            <input type="text" name="questions[${index}][text]" placeholder="Escribe la pregunta..." style="width: 100%;" />

            <label><strong>Tipo de campo:</strong></label><br>
            <select name="questions[${index}][type]" class="questionnaire-question-type">
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
                <label>Slider:</label>
                <input type="number" name="questions[${index}][min]" placeholder="Mínimo" style="width: 30%;" />
                <input type="number" name="questions[${index}][max]" placeholder="Máximo" style="width: 30%;" />
                <input type="number" name="questions[${index}][step]" placeholder="Paso" style="width: 30%;" />
            </div>

            <button type="button" class="questionnaire-remove-question">Eliminar pregunta</button>
        </div>
        `;
    }

    // Aplicar eventos a un bloque específico
    function applyEvents($block) {
        $block.find('.questionnaire-remove-question').on('click', function () {
            $(this).closest('.questionnaire-question-block').remove();
        });

        $block.find('.questionnaire-question-type').on('change', function () {
            const type = $(this).val();
            const $parent = $(this).closest('.questionnaire-question-block');

            $parent.find('.questionnaire-question-options, .questionnaire-question-slider').hide();
            if (type === 'radio' || type === 'checkbox') {
                $parent.find('.questionnaire-question-options').show();
            } else if (type === 'slider') {
                $parent.find('.questionnaire-question-slider').show();
            }
        });
    }

    // Aplicar eventos a los bloques existentes al cargar la página
    $wrapper.find('.questionnaire-question-block').each(function () {
        applyEvents($(this));
    });
});*/
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

