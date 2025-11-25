<?php
/**
 * @package PriceTable
 *
 */

 namespace SanasanaInit\Form;

 use SanasanaInit\General\BaseController;
 use WP_Query;

class FormController extends BaseController
{    
    public function register()
    {
       add_shortcode( 'contact_form', [$this, 'render_contact_form']);
    }

	public function render_contact_form($atts)
	{
		// Get the options
		$recaptcha_site_key = get_option('sanasana_recaptcha_site_key');
		$api_base_url = get_option('sanasana_api_base_url');
		$api_contact_form_path = get_option('sanasana_api_contact_form_path');
		$lang = $this->get_current_lang();
		$phoneCoutrySearchPlaceholder = '';
		switch($lang){
			case 'es':
				$phoneCoutrySearchPlaceholder = 'Buscar';
				break;
			default:
				$phoneCoutrySearchPlaceholder = 'Search';
				break;
		}

		// Check if options are set
		if (empty($recaptcha_site_key) || empty($api_base_url) || empty($api_contact_form_path)) {
			return '<p class="error">' . 
				   __('Error: Please configure the contact form settings in the WordPress admin panel.', 'sanasana') . 
				   '</p>';
		}
		
		$full_contact_from_url = $api_base_url.$api_contact_form_path;
		ob_start();
		?>
		<div class="contact-form_sanasana">
			<style>
				.contact-form_sanasana .result-message {
	                margin-top: 10px;
					color: #FFFFFF;
					font-family: Poppins, sans-serif;
					font-weight: 700;
					font-size: 18px;
					line-height: 20px;            
				}
				.iti__tel-input::placeholder,
				.iti__tel-input::-webkit-input-placeholder{
					color: #5166EC;  
					font-size: 12px;
					font-weight: 400;
					line-height: 16px;
					font-family: Poppins, sans-serif;
					opacity: 1;  
				}

				.iti__selected-country{
					height: 46px !important;
					height: 46px !important;
					margin-top: 7px;
					border-radius: 12px 0px 0px 12px;
					left: -1px;					
				}

				.iti__selected-country:hover,
				.iti__selected-country:focus{
					background-color: #5166ec !important;
					border: 1px solid white;
				}

				.iti__selected-country:hover .iti__arrow,
				.iti__selected-country:focus .iti__arrow{
					border-top-color: white !important;
				}

				.iti__selected-country:hover .iti__arrow--up,
				.iti__selected-country:focus .iti__arrow--up{
					border-bottom-color: white !important;
				}
				
			</style>

			<form id="contact-form" name="contact-form" method="post" action="" novalidate>
				<input type="text" id="fullName" name="fullName" required placeholder="<?php _e('Nombre y Apellido', 'sanasana'); ?>">
				<input type="email" id="email" name="email" required placeholder="<?php _e('Correo electrónico', 'sanasana'); ?>">
				<input type="tel" id="phone" name="phone" style="margin: 8px 0 !important;border-radius: 12px;height: 46px;" 
					   required placeholder="<?php _e('Teléfono', 'sanasana_stage'); ?>">
				<textarea id="message" name="message" required placeholder="<?php _e('Mensaje', 'sanasana'); ?>"></textarea>

				<!-- Add reCAPTCHA -->
				<div class="g-recaptcha mb-4" data-sitekey="<?php echo esc_attr($recaptcha_site_key); ?>"></div>

				<button type="submit"><?php _e('Contactame', 'sanasana'); ?></button>

				<p id="successMessage" class="hidden result-message"><?php _e('Mensaje enviado correctamente', 'sanasana'); ?></p>
				<p id="errorMessage" class="hidden result-message"><?php _e('Error al enviar el mensaje', 'sanasana'); ?></p>
				
			</form>
		</div>

		<script>
			let intlTelObject = null;
			async function submitContactForm(formData) {
				try {
					const response = await fetch('<?php echo esc_url($full_contact_from_url); ?>', {
						method: 'POST',
						headers: {
							'Content-Type': 'application/json',
							'Accept': 'application/json'
						},
						body: JSON.stringify(formData)
					});
					const data = await response.json();
					if (!response.ok || data.StatusCode !== 200) {
						throw new Error(data.Message || `Error del servidor: ${response.status}`);
					}
					return data;
				} catch (error) {
					console.error('Error submitting form:', error);
					throw error;
				}
			}

			function setFormFieldsState(disabled) {
				const form = document.querySelector('#contact-form');
				const inputs = form.querySelectorAll('input, textarea, button');
				inputs.forEach(input => {
					input.disabled = disabled;
				});
			}

			function showError(message) {
				const errorMessage = document.getElementById('errorMessage');
				errorMessage.textContent = message;
				errorMessage.classList.remove('hidden');
				setTimeout(() => {
					errorMessage.classList.add('hidden');
				}, 4000);
			}
			
			async function getIntlContries(){
				let countries = {}
			    try{
					const res = await fetch("https://cdn.jsdelivr.net/npm/intl-tel-input@25.3.1/build/js/i18n/<?=$lang?>/countries.js");
                	const text = await res.text()
					const parsedText = text.replace('export default countryTranslations;','');
					countries = new Function(`${parsedText} return countryTranslations;`)();
				}catch(error){
					console.log(`getIntlContries[ERROR]`,error)
				}
				return countries
			}
			
			function applyNameFilterContactUs(){
				const nameInput = document.querySelector('#fullName');
				if(nameInput){
					nameInput.addEventListener('input', () => {
						let value = nameInput.value.replace(/[^A-Za-z\s\u00B4\u02DCáéíóúÁÉÍÓÚñÑ']/g, ''); 
						nameInput.value = value;
					});	
				}
			}
			
			
			async function applyPhoneMask(){
				const phoneInput = document.querySelector("#phone");
		        const MAX_PHONE_LENGTH = 15; // E.164 standard
				const countries = await getIntlContries();
				if (phoneInput) {
					
					intlTelObject = window.intlTelInput(phoneInput, {
						loadUtils: () => import("https://cdn.jsdelivr.net/npm/intl-tel-input@25.3.1/build/js/utils.js"),
						separateDialCode: true,
						initialCountry: "auto",
						geoIpLookup: callback => {
							fetch("https://ipapi.co/json")
							  .then(res => res.json())
							  .then(data => callback(data.country_code))
							  .catch(() => callback("us"));
						},				
						i18n:{
							...countries,
							searchPlaceholder: "<?=$phoneCoutrySearchPlaceholder?>",
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
			
			function validateNameInput() {
				const nameInput = document.querySelector("#fullName");
				const validationMessage = '<?=$lang === 'es' ? 'El nombre es requerido' : 'The name is required'?>';
				if (nameInput.validity.valueMissing) {
					nameInput.setCustomValidity(validationMessage);
				} else {
					nameInput.setCustomValidity('');
				}
			}

			function validateEmailInput() {
				const emailInput = document.querySelector("#email");
				const inputEmptyMessage = '<?=$lang === 'es' ? 'El email es requerido' : 'The e-mail is required'?>';
				const invalidEmailMessage = '<?=$lang === 'es' ? 'Ingrese un email válido' : 'The e-mail format is not valid'?>';
			    const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
				
				if (emailInput.validity.valueMissing) {
					emailInput.setCustomValidity(inputEmptyMessage);
			    } else if (!emailRegex.test(emailInput.value)) {
					emailInput.setCustomValidity(invalidEmailMessage);
				} else {
					emailInput.setCustomValidity('');
				}
			}

			function validatePhoneInput() {
				const phoneInput = document.querySelector("#phone");
				const validationMessage = '<?=$lang === 'es' ? 'El teléfono es requerido' : 'The phone is required'?>';
				if (phoneInput.validity.valueMissing) {
					phoneInput.setCustomValidity(validationMessage);
				} else {
					phoneInput.setCustomValidity('');
				}
			}

			function validateMessageInput() {
				const messageInput = document.querySelector("#message");
				const validationMessage = '<?=$lang === 'es' ? 'El mensaje es requerido' : 'The message is required'?>';
				if (messageInput.validity.valueMissing) {
					messageInput.setCustomValidity(validationMessage);
				} else {
					messageInput.setCustomValidity('');
				}
			}

			function applyCustomValidations() {
				document.querySelector("#fullName").addEventListener('input', validateNameInput);
				document.querySelector("#email").addEventListener('input', validateEmailInput);
				document.querySelector("#phone").addEventListener('input', validatePhoneInput);
				document.querySelector("#message").addEventListener('input', validateMessageInput);
			}			
			
			function applyEmailSanitizationFilter(){
				const emailInput = document.querySelector("#email");
				emailInput.addEventListener('beforeinput', (e) => {
					if (e.inputType !== 'insertText' || !e.data) return;
					if (/[^a-zA-Z0-9@._%+\-]/.test(e.data)) {
					  e.preventDefault();               
					}
				});
			}
			
			document.addEventListener('DOMContentLoaded', () => {
				const form = document.querySelector('#contact-form');
				if(!form) return;
				const successMessage = document.getElementById('successMessage');
				const errorMessage = document.getElementById('errorMessage');
				applyNameFilterContactUs();
				applyPhoneMask();
				applyEmailSanitizationFilter();
				applyCustomValidations();
				if (form) {
					form.addEventListener('submit', async (e) => {
						e.preventDefault();
						validateNameInput();
						validateEmailInput();
						validatePhoneInput();
						validateMessageInput();						
						
						if(!form.checkValidity()){
							form.reportValidity();
							return;
						}
						successMessage.classList.add('hidden');
						errorMessage.classList.add('hidden');

						// Verify reCAPTCHA
						const recaptchaResponse = grecaptcha.getResponse();
						if (!recaptchaResponse) {
							showError('<?php _e('Por favor complete la verificación reCAPTCHA.', 'sanasana'); ?>');
							return;
						}

						// Validate form fields
						const formData = new FormData(form);
						const phone = intlTelObject !== null ? intlTelObject.getNumber() : formData.get('phone');
						const formDataObject = {
							fullName: formData.get('fullName'),
							email: formData.get('email'),
							phone,
							message: formData.get('message'),
							recaptchaResponse: recaptchaResponse
						};
																		
						// Validate required fields
						if (!formDataObject.fullName || !formDataObject.email || !formDataObject.phone || !formDataObject.message) {
							showError('<?php _e('Por favor complete todos los campos requeridos.', 'sanasana'); ?>');
							return;
						}

						// Validate email format
						const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
						if (!emailRegex.test(formDataObject.email)) {
							showError('<?php _e('Por favor ingrese un correo electrónico válido.', 'sanasana'); ?>');
							return;
						}

						// Disable form fields during submission
						setFormFieldsState(true);

						try {
							await submitContactForm(formDataObject);
							form.reset();
							grecaptcha.reset();
							successMessage.classList.remove('hidden');
							setTimeout(() => {
								successMessage.classList.add('hidden');
							}, 4000);
						} catch (error) {
							showError(error.message || '<?php _e('Error al enviar el mensaje. Por favor intente nuevamente.', 'sanasana'); ?>');
						} finally {
							// Re-enable form fields after submission attempt
							setFormFieldsState(false);
						}
					});
				}
				
			});
		</script>
		<?php
		return ob_get_clean();
	}	
    
}