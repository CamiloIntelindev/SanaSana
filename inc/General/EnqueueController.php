<?php
/**
* @package  Sanasana
 * Enqueues CSS and JS Files
 */

 namespace SanasanaInit\General;
 
 class EnqueueController extends BaseController
 {
     public function register()
     {
         add_action('wp_enqueue_scripts', [$this, 'enqueue_files']);
         add_action('admin_enqueue_scripts', [$this, 'enqueue_files']);
		 add_action('wp_enqueue_scripts', [$this, 'optimize_frontpage_css'], 100);
		 add_action('wp_head', [$this, 'inject_custom_seo'], 1);
		 add_action('wp_head',[$this, 'preconnect_cdn'], 0);
		 
		 add_action('wp_head', [$this, 'preload_poppins'], 1);
		 
		 //$this->add_defer_filters();
     }
 
     public function enqueue_files()
     {
        $this->version = '1.0.3';

        // Encolar Bootstrap CSS
        wp_enqueue_style('bootstrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css', [], '5.3.0', 'all');
        // Encolar Bootstrap JS
        wp_enqueue_script('bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js', [], '5.3.0', true);

        // Encolar tus estilos y scripts personalizados
        wp_enqueue_style('pricetable-styles-css', $this->plugin_url . 'assets/css/styles-price.css', [], $this->version, 'all');
        wp_enqueue_style('tabstable-styles-css', $this->plugin_url . 'assets/css/styles-tabs.css', [], $this->version, 'all');
        wp_enqueue_style('questionnaire-styles-css', $this->plugin_url . 'assets/css/styles-questionnaire.css', [], $this->version, 'all');
        wp_enqueue_style('form-styles-css', $this->plugin_url . 'assets/css/form-styles.css', [], $this->version, 'all');
    
        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-ui-sortable');
        wp_enqueue_media();

        wp_enqueue_script('scripts-price', $this->plugin_url . 'assets/js/script-price.min.js', ['jquery'], $this->version, true);
        wp_enqueue_script('scripts-tabs', $this->plugin_url . 'assets/js/script-tabs.min.js', ['jquery'], $this->version, true);
        wp_enqueue_script('scripts-tabs-horizontal', $this->plugin_url . 'assets/js/script-tabs-horizontal.min.js', ['jquery'], $this->version, true);
        wp_enqueue_script('scripts-tabs-vertical', $this->plugin_url . 'assets/js/script-tabs-vertical.js', ['jquery'], $this->version, true);
        //wp_enqueue_script('scripts-questionnaire', $this->plugin_url . 'assets/js/script-questionnaire.min.js', ['jquery'], $this->version, true);
		 
		 
		//Swalert 2
		wp_enqueue_script('sweetalert2', 'https://cdn.jsdelivr.net/npm/sweetalert2@11', [], null, true);
    	wp_enqueue_style('sweetalert2', 'https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css');
		 
		// International Telephone Input
		// https://intl-tel-input.com
		wp_enqueue_style('intl-tel-input', 'https://cdn.jsdelivr.net/npm/intl-tel-input@25.3.1/build/css/intlTelInput.css', [], '25.3.1');
	    wp_enqueue_script('intl-tel-input', 'https://cdn.jsdelivr.net/npm/intl-tel-input@25.3.1/build/js/intlTelInput.min.js', [], '25.3.1', true);
		 
		// Google reCAPTCHA
		wp_enqueue_script('google-recaptcha', 'https://www.google.com/recaptcha/api.js', [], null, true);
		 
		// Notyf toaster
		wp_enqueue_script('notyf-js','https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js',[],null,true);
		wp_enqueue_style('notyf-css','https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css',[],null);		 
        wp_add_inline_style('notyf-css','
                .notyf__toast{
                border-radius: 10px;
                max-width: 350px !important;
            }
                .notyf__toast--success {
                    background-color: #DBEBE3;
                    color: #61756B;
            }
            .notyf__toast--error {
                    background-color: #fee7ef;
                    color: #f31260;
            }
            .notyf__ripple{
                background: transparent !important;
            }
            
            .notyf__icon--error {
                position: relative;
                width: 25px;
                height: 25px;
                display: inline-block;
                background: #c20e4d;
                clip-path: polygon(
                    50% 0%,
                    93% 25%,
                    93% 75%,
                    50% 100%,
                    7% 75%,
                    7% 25%
                );
            }
            .notyf__icon--error::before {
                content: "";
                position: absolute;
                left: 50%;
                top: 28%;
                width: 1.5px;
                height: 7px;
                background: #fff;
                border-radius: 1px;
                transform: translateX(-50%);
            }
            .notyf__icon--error::after {
                content: "";
                position: absolute;
                left: 50%;
                top: 65%;
                width: 2px;
                height: 2px;
                background: #fff;
                border-radius: 50%;
                transform: translateX(-50%);
            }
            
            .notyf__icon--success {
                position: relative;
                top: 5px;
                width: 22px;
                height: 22px;
                display: inline-block;
                background: #61756b;
                border-radius: 50%;
            }
            .notyf__icon--success::before {
                content: "";
                position: absolute;
                left: 50%;
                top: 45%;
                width: 1.5px;
                height: 7px;
                background: #fff;
                border-radius: 1px;
                transform: translateX(-50%);
            }
            .notyf__icon--success::after {
                content: "";
                position: absolute;
                left: 50%;
                top: 28%;
                width: 2px;
                height: 2px;
                background: #fff;
                border-radius: 50%;
                transform: translateX(-50%);
            }			
    ');
		 
		wp_enqueue_script('script-general', $this->plugin_url . 'assets/js/script-general.js', ['jquery'], $this->version, true);
		 
     }
	 
	 public function add_defer_filters() {
		// Solo front y fuera de admin/AJAX/cron
		if ( is_admin() || wp_doing_ajax() || wp_doing_cron() ) {
			return;
		}

		// ----- JS: DEFER -----
		add_filter('script_loader_tag', function ($tag, $handle, $src) {

			if ( ! is_front_page() ) return $tag;

			// Nunca defer para estos (dependencias críticas)
			$never_defer = ['jquery', 'jquery-core', 'jquery-migrate'];

			// Lista blanca: solo estos se diferirán
			$defer_whitelist = [
				'sweetalert2',
				'notyf-js',
				'bootstrap-js',
				'slick-js',
				'fl-builder',           // si lo usas
				'fl-builder-layout',    // si lo usas
				'moxiejs',
				'plupload',
				'fl-builder-layout-12'
				// 'google-recaptcha'    // lo tratamos aparte
			];

			if ( in_array($handle, $never_defer, true) ) {
				return $tag;
			}

			// Google reCAPTCHA: async + defer
			if ( $handle === 'google-recaptcha' ) {
				// evita duplicar atributos
				if ( strpos($tag, 'async') === false ) {
					$tag = str_replace('<script ', '<script async ', $tag);
				}
				if ( strpos($tag, ' defer') === false ) {
					$tag = str_replace('<script ', '<script defer ', $tag);
				}
				return $tag;
			}

			// Defer para los de la whitelist
			if ( in_array($handle, $defer_whitelist, true) && strpos($tag, ' defer') === false ) {
				$tag = str_replace('<script ', '<script defer ', $tag);
			}

			return $tag;

		}, 10, 3);

		// ----- CSS: DEFER (print hack) -----
		add_filter('style_loader_tag', function ($html, $handle, $href, $media) {

			if ( ! is_front_page() ) return $html;

			// Lista blanca: solo estos CSS se difieren
			$defer_styles = [
				'sweetalert2',
				'notyf-css',
				'pricetable-styles-css',
				'tabstable-styles-css',
				'questionnaire-styles-css',
				'form-styles-css',
				'imgareaselect',
				'fl-builder-layout-12'
				
				// 'slick-css',
				// 'bootstrap-css',
			];

			if ( ! in_array($handle, $defer_styles, true) ) {
				return $html;
			}

			// Conservamos el id del handle
			$id = esc_attr("{$handle}-css");
			$href = esc_url($href);

			return "<link rel='preload' as='style' href='{$href}'>"
				 . "<link rel='stylesheet' id='{$id}' href='{$href}' media='print' onload=\"this.media='all'\">"
				 . "<noscript><link rel='stylesheet' id='{$id}-ns' href='{$href}'></noscript>";
		}, 10, 4);
	}

	 
	 /**
	 * Quita o difiere CSS SOLO en el frontpage.
	 */
	public function optimize_frontpage_css() {
		if ( !is_front_page() ) return;

		// 1) Dashicons: fuera para visitantes en frontpage
		if ( !is_user_logged_in() && !is_admin_bar_showing() ) {
			wp_deregister_style('dashicons');
		}

		// 2) Gutenberg block library (si la home NO usa bloques)
		$homepage_uses_blocks = false; // cámbialo a true si tu home tiene bloques WP
		if ( !$homepage_uses_blocks ) {
			wp_dequeue_style('wp-block-library');
			wp_dequeue_style('wp-block-library-theme');
			wp_dequeue_style('global-styles');          // theme.json
			wp_dequeue_style('classic-theme-styles');   // Twenty*, etc.
		}

		// 3) Font Awesome: handle desconocido -> detecta por URL y lo saca
		$styles = wp_styles();
		foreach ( (array) $styles->queue as $handle ) {
			$src = $styles->registered[$handle]->src ?? '';
			if ($src && (strpos($src, 'all.min.css') !== false || strpos($src, 'fontawesome') !== false)) {
				wp_dequeue_style($handle);
				wp_deregister_style($handle);
			}
		}

		// 4) Bootstrap CSS: si no lo usas en la home, quítalo.
		//    Si sí lo usas, NO lo quites: ya lo diferimos con style_loader_tag.
		$use_bootstrap_on_home = false; // <-- pon true si tu home sí lo necesita
		if (!$use_bootstrap_on_home) {
			wp_dequeue_style('bootstrap-css');
			wp_deregister_style('bootstrap-css');
		}

		// (Opcional) También puedes quitar el JS de Bootstrap si no se usa:
		if (!$use_bootstrap_on_home) {
			wp_dequeue_script('bootstrap-js');
			wp_deregister_script('bootstrap-js');
		}
		
		remove_action('wp_head','print_emoji_detection_script',7);
  		remove_action('wp_print_styles','print_emoji_styles');
	}
	 
	 public function preconnect_cdn(){
		if (!is_front_page()) return;
		 echo '<link rel="preconnect" href="https://sanasanastoragews.blob.core.windows.net" crossorigin />'."\n";
	 }

	public function preload_poppins() {
		if (is_admin()) return;

		// 1) Preconnect para bajar el RTT de las fuentes
		?>
		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<?php

		// 2) PRELOAD + carga no bloqueante del stylesheet de Google Fonts
		//    (usa tu URL actual o, mejor, la versión v2 con display=swap)
		$gf_v1 = 'https://fonts.googleapis.com/css?family=Poppins%3A400%2C700%2C500%2C300&ver=6.8.2';
		$gf_v2 = 'https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;700&display=swap';

		$href = $gf_v2; // usa v2 si puedes; si no, cambia a $gf_v1
		?>
		<link rel="preload" as="style" href="<?php echo esc_url($href); ?>">
		<link rel="stylesheet" href="<?php echo esc_url($href); ?>" media="print" onload="this.media='all'">
		<noscript><link rel="stylesheet" href="<?php echo esc_url($href); ?>"></noscript>

		<?php
	}

	 
	 //Seo Section
	 public function inject_custom_seo() {
        $current_url = untrailingslashit(get_permalink());
        $base_url = untrailingslashit(get_home_url());
		
        $seo_data = [
            $base_url . '/price/salud-en-equilibrio' => [
                'title'       => 'Programa Salud en Equilibrio – SanaSana',
                'description' => 'Disfruta del programa “Salud en Equilibrio”: valoración médica esencial, guía personalizada, monitoreo constante y ahorros exclusivos. Bienestar proactivo.',
            ],
            $base_url . '/price/vida-activa' => [
                'title'       => 'Programa Vida Activa – SanaSana',
                'description' => 'Activa tu salud con “Vida Activa”: plan preventivo con ejercicio guiado, apoyo especializado, monitoreo constante y energía renovada.',
            ],
            $base_url . '/price/control-total' => [
                'title'       => 'Programa Control Total – SanaSana',
                'description' => 'Toma el control total de tu salud con este plan: valoración médica avanzada, seguimiento personalizado y herramientas digitales para resultados duraderos.',
            ],
            $base_url . '/price/mama-bebe' => [
                'title'       => 'Programa Mamá & Bebé – SanaSana',
                'description' => 'Cuida de ti y tu bebé con nuestro plan “Mamá & Bebé”: valoración prenatal/postnatal, seguimiento personalizado y recursos para un vínculo saludable.',
            ],
            $base_url . '/price/vitalidad-dorada' => [
                'title'       => 'Programa Vitalidad Dorada – SanaSana',
                'description' => 'Revitaliza tu vida con “Vitalidad Dorada”: valoración médica especializada, plan de bienestar integral, seguimiento personalizado y energía renovada.',
            ],
            $base_url . '/price/balanced-health' => [
                'title'       => 'Balanced Health Program – SanaSana',
                'description' => 'Transform your wellness with Balanced Health: full medical evaluation, personalized health guide, ongoing monitoring and exclusive savings.',
            ],
            $base_url . '/price/active-life' => [
                'title'       => 'Active Life Program – SanaSana',
                'description' => 'Boost your health with Active Life: guided exercise plan, expert support, continuous monitoring and renewed energy.',
            ],
            $base_url . '/price/total-control' => [
                'title'       => 'Total Control Program – SanaSana',
                'description' => 'Take total control of your health with this program: advanced medical evaluation, personalized tracking, and digital tools for lasting results.',
            ],
            $base_url . '/price/mom-baby' => [
                'title'       => 'Mom & Baby Program – SanaSana',
                'description' => 'Support your journey with the Mom & Baby Program: prenatal and postnatal checkups, personalized care, and resources for a healthy bond.',
            ],
            $base_url . '/price/golden-vitality' => [
                'title'       => 'Golden Vitality Program – SanaSana',
                'description' => 'Revitalize your life with the Golden Vitality Program: specialized medical evaluation, full wellness plan, personalized support and renewed energy.',
            ],
        ];

        if (array_key_exists($current_url, $seo_data)) {
            //echo '<title>' . esc_html($seo_data[$current_url]['title']) . '</title>' . "\n";
            echo '<meta name="description" content="' . esc_attr($seo_data[$current_url]['description']) . '">' . "\n";
        }
    }
 }