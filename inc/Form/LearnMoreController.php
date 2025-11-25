<?php
/**
* @package  Sanasana
 * Enqueues CSS and JS Files
 */

 namespace SanasanaInit\Form;
use SanasanaInit\General\BaseController;
 use WP_Query;
 
 class LearnMoreController extends BaseController
 {
     public function register()
     {
		add_shortcode( 'learn_more_form', [$this, 'render_learn_more_form']);
        add_action( 'wp_enqueue_scripts', [$this, 'add_assets'] );
     }

	 public function add_assets(){
		$api_base_url = get_option('sanasana_api_base_url');
		$api_learn_more_form_path = '/api/v1/contacts/create-learn-more-lead';
		$full_learn_more_from_url = $api_base_url.$api_learn_more_form_path;
		$lang = $this->get_current_lang();
		$phoneCountrySearchPlaceholder = __('Buscar','conoce-mas');
		$inline_js = "
		 		let submittingLearnForm = false;
				let learnMoreintlTelObject = null;
				async function getIntlContries(){
					let countries = {}
					try{
						const res = await fetch('https://cdn.jsdelivr.net/npm/intl-tel-input@25.3.1/build/js/i18n/$lang/countries.js');
						const text = await res.text()
						const parsedText = text.replace('export default countryTranslations;','');
						countries = new Function(parsedText + ' return countryTranslations;')();
					}catch(error){
						console.log(`getIntlContries[ERROR]`,error)
					}
					return countries
				}

				function applyNameFilterLearnMore(){
					const nameInput = document.querySelector('#learn-more-name');
					if(nameInput){
						nameInput.addEventListener('input', () => {
							let value = nameInput.value.replace(/[^A-Za-z\s\u00B4\u02DCáéíóúÁÉÍÓÚñÑ']/g, ''); 
							nameInput.value = value;
						});	
					}
				}

				async function applyPhoneMaskLearnMore(){
					const phoneInput = document.querySelector('#learn-more-phone');
					const MAX_PHONE_LENGTH = 15; // E.164 standard
					const countries = await getIntlContries();
					if (phoneInput) {

						learnMoreintlTelObject = window.intlTelInput(phoneInput, {
							loadUtils: () => import('https://cdn.jsdelivr.net/npm/intl-tel-input@25.3.1/build/js/utils.js'),
							separateDialCode: true,
							initialCountry: 'auto',
							geoIpLookup: callback => {
								fetch('https://ipapi.co/json')
								  .then(res => res.json())
								  .then(data => callback(data.country_code))
								  .catch(() => callback('us'));
							},				
							i18n:{
								...countries,
								searchPlaceholder: '$phoneCountrySearchPlaceholder',
							}
						});
						phoneInput.addEventListener('input', () => {
							let value = phoneInput.value.replace(/[^0-9+\s]/g, ''); 
							if(value && value.length > MAX_PHONE_LENGTH){
								value = value.slice(0, -1);
							}
							phoneInput.value = value;
						});					
					}

				}				
				
				function validateNameInputLearnMore() {
					const nameInput = document.querySelector('#learn-more-name');
					const validationMessage = '".__('El nombre es requerido','conoce-mas')."';
					if (nameInput.validity.valueMissing) {
						nameInput.setCustomValidity(validationMessage);
					} else {
						nameInput.setCustomValidity('');
					}
				}

				function validatePhoneInputLearnMore() {
					const phoneInput = document.querySelector('#learn-more-phone');
					const validationMessage = '".__('El teléfono es requerido','conoce-mas')."';
					if (phoneInput.validity.valueMissing) {
						phoneInput.setCustomValidity(validationMessage);
					} else {
						phoneInput.setCustomValidity('');
					}
				}
				
				function applyCustomValidationsLearnMore() {
					document.querySelector('#learn-more-name').addEventListener('input', validateNameInputLearnMore);
					document.querySelector('#learn-more-phone').addEventListener('input', validatePhoneInputLearnMore);
				}						

				async function submitLearnMoreForm(formData){
					if(submittingLearnForm) return
					try {
						submittingLearnForm = true;
 						const response = await fetch('".esc_url($full_learn_more_from_url)."', {
 							method: 'POST',
 							headers: {
 								'Content-Type': 'application/json',
 								'Accept': 'application/json'
 							},
 							body: JSON.stringify(formData)
 						});
 						const data = await response.json();
 						if (!response.ok || data.StatusCode !== 200) {
 							throw new Error(data.Message || 'Error del servidor: ' + response.status);
 						}
 						return data;
					} catch (error) {
						console.error('Error submitting form:', error);
					} finally{
						submittingLearnForm = false;
					}
					return false;
				}
				
				function setLearnMoreFormDisabled(disabled) {
                  	const form = document.getElementById('learn-more-form');
					if (!form) return;

					for (const el of form.elements) {
						el.disabled = disabled;
					}

					if (typeof learnMoreintlTelObject?.enable === 'function') {
						disabled ? learnMoreintlTelObject.disable() : learnMoreintlTelObject.enable();
					}

				  	const captcha = form.querySelector('.g-recaptcha');
				  	if (captcha) {
						captcha.style.pointerEvents = disabled ? 'none' : 'auto';
						captcha.style.opacity       = disabled ? '0.5' : '1';
				  	}
					
					const submitButton = document.getElementById('submit-button');
					if(submitButton) {
						if(disabled){
							submitButton.setAttribute('disabled','');
							submitButton.textContent = '".__( 'Enviando…', 'conoce-mas' )."';
							submitButton.classList.add('loading');
						}else {
							submitButton.removeAttribute('disabled');
							submitButton.textContent = '".__( 'Enviar', 'conoce-mas' )."';
							submitButton.classList.remove('loading');
						}
					}
				}			
				function resetRecaptchaLearnMore(){
					if (grecaptcha) grecaptcha.reset();
				}
				
				function resetLearnMoreForm(){
                  	const form = document.getElementById('learn-more-form');
					if (!form) return;
					form.reset();
					if (typeof learnMoreintlTelObject?.setNumber === 'function') {
						learnMoreintlTelObject.setNumber('');
					}
				  	resetRecaptchaLearnMore()
				}
				
                document.addEventListener('DOMContentLoaded', () => {
                    const form = document.getElementById('learn-more-form');
					if(!form) return;
					applyNameFilterLearnMore();
					applyPhoneMaskLearnMore();      
					applyCustomValidationsLearnMore();
					
                    form.addEventListener('submit', async (e) => {
                        e.preventDefault();
						
						validateNameInputLearnMore();
						validatePhoneInputLearnMore();
						if(!form.checkValidity()){
							form.reportValidity();
							return;
						}						
						
						const recaptchaResponse = grecaptcha.getResponse();
						if (!recaptchaResponse) {
							notifyController.showMessage('".__('Por favor complete la verificación reCAPTCHA.', 'conoce-mas')."');
							return;
						}						
						const formData = new FormData(form);
						const phone = learnMoreintlTelObject !== null ? 
										learnMoreintlTelObject.getNumber() : 
										formData.get('learn-more-phone');
						const formObject = {
                            'fullName': formData.get('learn-more-name') || '',
                            'phone': phone,
                            'WhatsAppMessage': formData.has('whatsapp-message'),
                            'PhoneCall': formData.has('phone-message'),
                            'HealthCareCost': formData.has('less-money-healt'),
                            'ChronicDisease': formData.has('want-help'),
                            'TrackCheckups': formData.has('medical-control'),
                            'HealthyChanges': formData.has('health-transform')
                        };
						
						if(!formObject.PhoneCall && !formObject.WhatsAppMessage){
							notifyController.showMessage('".__('Por favor seleccione al menos un método de contacto.', 'conoce-mas')."');
							return;
						}

						if(!formObject.HealthCareCost 
						&& !formObject.ChronicDisease
						&& !formObject.TrackCheckups						
						&& !formObject.HealthyChanges						
						){
							notifyController.showMessage('".__('Por favor seleccione al menos una prioridad.', 'conoce-mas')."');
							return;
						}
						setLearnMoreFormDisabled(true);
                        const result = await submitLearnMoreForm(formObject);
						if(result === false){
							notifyController.showMessage('".__('Ha ocurrido un error, intente más tarde', 'conoce-mas')."');
							setLearnMoreFormDisabled(false);
							resetRecaptchaLearnMore();
							return;
						}
						notifyController.showMessage('".__('El mensaje ha sido enviado con éxito', 'conoce-mas')."', false);
						setLearnMoreFormDisabled(false);
						resetLearnMoreForm();
                    });
                });
		";

    	wp_add_inline_script( 'notyf-js', $inline_js, 'after' );
		wp_add_inline_style('intl-tel-input','
					.learn-more-form-container .iti{
						width:100%;
					}

					.learn-more-form-container .iti__selected-country{
						height: 40px !important;
						margin-top: 2px;
						border-radius: 12px 0px 0px 12px;
						left: 2px;					
					}

					.learn-more-form-container .iti__selected-country:hover,
					.learn-more-form-container .iti__selected-country:focus{
						background-color: #EEF0FD !important;
						border: 1px solid white;
						color: #000000 !important;
					}

					.learn-more-form-container .iti__selected-country:hover .iti__arrow,
					.learn-more-form-container .iti__selected-country:focus .iti__arrow{
						border-top-color: #000000 !important;
					}

					.learn-more-form-container .iti__selected-country:hover .iti__arrow--up,
					.learn-more-form-container .iti__selected-country:focus .iti__arrow--up{
						border-bottom-color: #000000 !important;
					}		
					
					.learn-more-form-container .iti__selected-country-primary{
						border-radius: 12px 0px 0px 12px;
					}
			');

	 }
	 
	 public function render_learn_more_form($atts){
		$recaptcha_site_key = get_option('sanasana_recaptcha_site_key');
        
		ob_start(); ?>
			<div class="learn-more-form-container">
				<form id="learn-more-form" name="learn-more-form" method="post" action="" novalidate>
					<div class="learn-more-form-fields">
						<!-- -->
						<div class="row mb-3">
							<div class="col-lg-6 col-md-6 col-sm-12">
								<input type="text" class="learn-more-text" id="learn-more-name" name="learn-more-name" required placeholder="<?php _e('Nombre', 'conoce-mas'); ?>">
							</div>
							<div class="col-lg-6 col-md-6 col-sm-12">
								<input type="text" class="learn-more-text" id="learn-more-phone" name="learn-more-phone" required placeholder="<?php _e('Teléfono', 'conoce-mas'); ?>">
							</div>
						</div>
						<!-- -->
						<div class="row ">
							<div class="col-lg-12 col-md-12 col-sm-12">
								 <h3 class="learn-more-sub-title">
									 <?php _e('¿Cómo deseás que te contactemos?', 'conoce-mas'); ?>
								</h3>
								<h4 class="learn-more-sub-title mb-2">
									 <?php _e('(Marcá todas las que correspondan)', 'conoce-mas'); ?>
								</h4>
							</div>
							
						</div>
						<!-- -->
						<div class="row ">
							<div class="col-lg-12 col-md-12 col-sm-12 learn-more-item">
								<input type="checkbox" name="whatsapp-message" id="whatsapp-message">
								<label for="whatsapp-message"><?php _e('Mensaje de WhatsApp.', 'conoce-mas'); ?></label>
							</div>
							<div class="col-lg-12 col-md-12 col-sm-12 learn-more-item">
								<input type="checkbox" name="phone-message" id="phone-message">
								<label for="phone-message"><?php _e('Teléfono.', 'conoce-mas'); ?></label>
							</div>
						</div>
						<!-- -->
						<!-- -->
						<div class="row ">
							<div class="col-lg-12 col-md-12 col-sm-12">
								 <h3 class="learn-more-sub-title">
									 <?php _e('¿Cuál es tu prioridad en este momento?', 'conoce-mas'); ?>
								</h3>
								<h4 class="learn-more-sub-title mb-2">
									 <?php _e('(Marcá todas las que correspondan)', 'conoce-mas'); ?>
								</h4>
							</div>
							
						</div>
						<!-- -->
						<div class="row">
							<div class="col-lg-12 col-md-12 col-sm-12 learn-more-item">
								<input type="checkbox" name="less-money-healt" id="less-money-healt">
								<label for="less-money-healt"><?php _e('Quiero bajar mis gastos de salud.', 'conoce-mas'); ?></label>
							</div>
							<div class="col-lg-12 col-md-12 col-sm-12 learn-more-item">
								<input type="checkbox" name="want-help" id="want-help">
								<label for="want-help"><?php _e('Quiero ayuda controlando una condición médica.', 'conoce-mas'); ?></label>
							</div>

							<div class="col-lg-12 col-md-12 col-sm-12 learn-more-item">
								<input type="checkbox" name="medical-control" id="medical-control">
								<label for="medical-control"><?php _e('Quiero mantenerme al día con mis chequeos 
y controles médicos.', 'conoce-mas'); ?></label>
							</div>
							<div class="col-lg-12 col-md-12 col-sm-12 learn-more-item">
								<input type="checkbox" name="health-transform" id="health-transform">
								<label for="health-transform"><?php _e('Quiero transformar mi salud.', 'conoce-mas'); ?></label>
							</div>
						</div>

						<div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <div class="g-recaptcha" 
									 data-sitekey="<?php echo esc_attr($recaptcha_site_key); ?>">
								</div>
                            </div>
                        </div>
						
					</div>


					<div class="learn-more-submit-container">
						<div class="learn-more-mouse-container">
							<img src="https://sanasanastoragews.blob.core.windows.net/blobwsdev/2025/08/blue-mouse.png" alt="mouse" class="learn-more-mouse">
						</div>
						<div class="learn-more-button-container">
							<button type="submit" id="submit-button" class="standart-yellow submit-button-loading-spin">
								<?php _e( 'Enviar', 'conoce-mas' ); ?>
							</button>
	 					</div>
						
					</div>
				</form>
			</div>

		<?php
    	return ob_get_clean();
	}
}