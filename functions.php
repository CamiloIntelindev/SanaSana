<?php
/**
 * Astra Child Theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Astra Child
 * @since 1.0.0
 */

/**
 * Define Constants
 */
define( 'CHILD_THEME_ASTRA_CHILD_VERSION', '1.0.0' );

/**
 * Enqueue styles
 */
function child_enqueue_styles() {

	wp_enqueue_style( 'astra-child-theme-css', get_stylesheet_directory_uri() . '/style.css', array('astra-theme-css'), CHILD_THEME_ASTRA_CHILD_VERSION, 'all' );

}

add_action( 'wp_enqueue_scripts', 'child_enqueue_styles', 15 );

// Permitir subida de SVG
function allow_svg_upload($mimes) {
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
}
add_filter('upload_mimes', 'allow_svg_upload');

// Evitar error al ver la imagen SVG en la librería
function fix_svg_display() {
    echo '<style>
        .attachment-266x266, 
        .thumbnail img[src$=".svg"] {
            width: 100% !important;
            height: auto !important;
        }
    </style>';
}
add_action('admin_head', 'fix_svg_display');

// Validación del tipo MIME (opcional, pero más seguro)
function svg_mime_type_check($data, $file, $filename, $mimes) {
    $ext = pathinfo($filename, PATHINFO_EXTENSION);

    if ($ext === 'svg') {
        $data['ext'] = 'svg';
        $data['type'] = 'image/svg+xml';
    }

    return $data;
}
add_filter('wp_check_filetype_and_ext', 'svg_mime_type_check', 10, 4);



// Hotjar configuration
function hotjar() {
    $hotjar_id = get_option('sanasana_hotjar_id'); // Get ID from options
    $hotjar_sv = 6;
    $hotjar_script = <<<EOT
<script>
    (function(h,o,t,j,a,r){
        h.hj=h.hj||function(){(h.hj.q=h.hj.q||[]).push(arguments)};
        h._hjSettings={hjid:{$hotjar_id},hjsv:{$hotjar_sv}};
        a=o.getElementsByTagName('head')[0];
        r=o.createElement('script');r.async=1;
        r.src="https://static.hotjar.com/c/hotjar-{$hotjar_id}.js?sv={$hotjar_sv}";
        a.appendChild(r);
    })(window,document,'https://static.hotjar.com/c/hotjar-','.js?sv=');
</script>
EOT;

    echo $hotjar_script;
}
add_action('wp_head', 'hotjar');

// Add Hotjar Settings Menu
function add_hotjar_admin_menu() {
    add_submenu_page(
        'options-general.php', // Parent menu (Settings)
        __('Hotjar Settings', 'sanasana'),
        __('Hotjar', 'sanasana'),
        'manage_options',
        'hotjar-settings',
        'render_hotjar_settings_page'
    );
}
add_action('admin_menu', 'add_hotjar_admin_menu');

// Register Hotjar Settings
function register_hotjar_settings() {
    register_setting('hotjar_settings', 'sanasana_hotjar_id');

    add_settings_section(
        'hotjar_main_section',
        __('Hotjar Configuration', 'sanasana'),
        'render_hotjar_section_info',
        'hotjar-settings'
    );

    add_settings_field(
        'sanasana_hotjar_id',
        __('Hotjar ID', 'sanasana'),
        'render_hotjar_id_field',
        'hotjar-settings',
        'hotjar_main_section'
    );
}
add_action('admin_init', 'register_hotjar_settings');

// Render Hotjar Settings Page
function render_hotjar_settings_page() {
    if (!current_user_can('manage_options')) {
        return;
    }
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <form action="options.php" method="post">
            <?php
            settings_fields('hotjar_settings');
            do_settings_sections('hotjar-settings');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

// Render Hotjar Section Info
function render_hotjar_section_info() {
    echo '<p>' . __('Configure your Hotjar settings below:', 'sanasana') . '</p>';
}

// Render Hotjar ID Field
function render_hotjar_id_field() {
    $value = get_option('sanasana_hotjar_id');
    ?>
    <input type="text" 
           name="sanasana_hotjar_id" 
           value="<?php echo esc_attr($value); ?>" 
           class="regular-text"
           placeholder="Hotjar ID">
    <p class="description">
        <?php _e('Enter your Hotjar ID here.', 'sanasana'); ?>
    </p>
    <?php
}
// End hotjar configuration

function add_custom_breakpoint(){
	?>
<script>
	jQuery(document).ready(function() {
		function checkWidth() {
			if (jQuery(window).width() < 1228) {
				jQuery('body').addClass('ast-header-break-point');
				jQuery('body').removeClass('ast-desktop');
				jQuery('#ast-desktop-header').css('display', 'none');
				//console.log(jQuery(window).width());
			} else {
				jQuery('body').removeClass('ast-header-break-point');
				jQuery('body').addClass('ast-desktop');
				jQuery('#ast-desktop-header').css('display', 'block');
			}
		}

		// Verificamos el ancho al cargar la página
		checkWidth();

		// Verificamos el ancho cada vez que se redimensiona la ventana
		jQuery(window).resize(checkWidth);
	});


</script>

<?php
}
add_action('wp_head', 'add_custom_breakpoint');

//Allow svg
function add_file_types_to_uploads($file_types){
$new_filetypes = array();
$new_filetypes['svg'] = 'image/svg+xml';
$file_types = array_merge($file_types, $new_filetypes );
return $file_types;
}
add_filter('upload_mimes', 'add_file_types_to_uploads');

// Add Frontend Redirection Settings Menu
function add_frontend_redirection_admin_menu() {
    add_submenu_page(
        'options-general.php', // Parent menu (Settings)
        __('Frontend Redirection Settings', 'sanasana'),
        __('Frontend Redirection', 'sanasana'),
        'manage_options',
        'frontend-redirection-settings',
        'render_frontend_redirection_settings_page'
    );
}
add_action('admin_menu', 'add_frontend_redirection_admin_menu');

// Register Frontend Redirection Settings
function register_frontend_redirection_settings() {
    register_setting('frontend_redirection_settings', 'sanasana_frontend_base_url');
    register_setting('frontend_redirection_settings', 'sanasana_plan_id_parameter');
    register_setting('frontend_redirection_settings', 'sanasana_affiliation_path');
    register_setting('frontend_redirection_settings', 'sanasana_login_path');

    add_settings_section(
        'frontend_redirection_main_section',
        __('Frontend Redirection Configuration', 'sanasana'),
        'render_frontend_redirection_section_info',
        'frontend-redirection-settings'
    );

    add_settings_field(
        'sanasana_frontend_base_url',
        __('Frontend Base URL', 'sanasana'),
        'render_frontend_base_url_field',
        'frontend-redirection-settings',
        'frontend_redirection_main_section'
    );

    add_settings_field(
        'sanasana_affiliation_path',
        __('Affiliation Path', 'sanasana'),
        'render_affiliation_path_field',
        'frontend-redirection-settings',
        'frontend_redirection_main_section'
    );
	
    add_settings_field(
        'sanasana_plan_id_parameter',
        __('Plan ID Parameter', 'sanasana'),
        'render_plan_id_parameter_field',
        'frontend-redirection-settings',
        'frontend_redirection_main_section'
    );
	
	add_settings_field(
        'sanasana_login_path',
        __('Login Path', 'sanasana'),
        'render_login_path_field',
        'frontend-redirection-settings',
        'frontend_redirection_main_section'
    );	
}
add_action('admin_init', 'register_frontend_redirection_settings');

// Render Frontend Redirection Settings Page
function render_frontend_redirection_settings_page() {
    if (!current_user_can('manage_options')) {
        return;
    }
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <form action="options.php" method="post">
            <?php
            settings_fields('frontend_redirection_settings');
            do_settings_sections('frontend-redirection-settings');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

// Render Frontend Redirection Section Info
function render_frontend_redirection_section_info() {
    echo '<p>' . __('Configure your Frontend Redirection settings below:', 'sanasana') . '</p>';
}

// Render Frontend Base URL Field
function render_frontend_base_url_field() {
    $value = get_option('sanasana_frontend_base_url', '');
    ?>
    <input type="url" 
           name="sanasana_frontend_base_url" 
           value="<?php echo esc_attr($value); ?>" 
           class="regular-text"
           placeholder="https://example.com">
    <p class="description">
        <?php _e('Enter the base URL for your frontend application.', 'sanasana'); ?>
    </p>
    <?php
}

// Render Plan ID Parameter Field
function render_plan_id_parameter_field() {
    $value = get_option('sanasana_plan_id_parameter', '');
    ?>
    <input type="text" 
           name="sanasana_plan_id_parameter" 
           value="<?php echo esc_attr($value); ?>" 
           class="regular-text"
           placeholder="plan_id">
    <p class="description">
        <?php _e('Enter the parameter name used for plan identification.', 'sanasana'); ?>
    </p>
    <?php
}

// Render Affiliation Path Field
function render_affiliation_path_field() {
    $value = get_option('sanasana_affiliation_path', '');
    ?>
    <input type="text" 
           name="sanasana_affiliation_path" 
           value="<?php echo esc_attr($value); ?>" 
           class="regular-text"
           placeholder="/affiliate">
    <p class="description">
        <?php _e('Enter the path used for affiliation redirection.', 'sanasana'); ?>
    </p>
    <?php
}

// Render Login Path Field  
function render_login_path_field() {
    $value = get_option('sanasana_login_path', '');
    ?>
    <input type="text" 
           name="sanasana_login_path" 
           value="<?php echo esc_attr($value); ?>"  
           class="regular-text"
           placeholder="/login">
    <p class="description">
        <?php _e('Enter the path used for login redirection.', 'sanasana'); ?>
    </p>
    <?php
}


//Add Contact Form Settings Menu
function add_contact_form_admin_menu()
{
    add_submenu_page(
        'options-general.php', // Parent menu (Settings)
        __('Contact Form Settings', 'sanasana'),
        __('Contact Form', 'sanasana'),
        'manage_options',
        'contact-form-settings',
        'render_settings_page',
    );
}
add_action('admin_menu', 'add_contact_form_admin_menu');


//Register Contact Form Settings
function register_contact_form_settings()
{
    register_setting('contact_form_settings', 'sanasana_recaptcha_site_key');
    register_setting('contact_form_settings', 'sanasana_api_base_url');
    register_setting('contact_form_settings', 'sanasana_api_contact_form_path');
    register_setting('contact_form_settings', 'sanasana_api_learn_more_form_path');

    add_settings_section(
        'contact_form_main_section',
        __('Contact Form Configuration', 'sanasana'),
        'render_section_info',
        'contact-form-settings'
    );

    add_settings_field(
        'sanasana_recaptcha_site_key',
        __('reCAPTCHA Site Key', 'sanasana'),
        'render_recaptcha_field',
        'contact-form-settings',
        'contact_form_main_section'
    );

    add_settings_field(
        'sanasana_api_base_url',
        __('API Base URL', 'sanasana'),
		'render_api_url_field',
        'contact-form-settings',
        'contact_form_main_section'
    );

    add_settings_field(
        'sanasana_api_contact_form_path',
        __('API Contact Form Path', 'sanasana'),
        'render_api_contact_form_path_field',
        'contact-form-settings',
        'contact_form_main_section'
    );
	
    add_settings_field(
        'sanasana_api_learn_more_form_path',
        __('API Learn More Form Path', 'sanasana'),
        'render_api_learn_more_form_path_field',
        'contact-form-settings',
        'contact_form_main_section'
    );
}
add_action('admin_init', 'register_contact_form_settings');


function render_settings_page()
{
    if (!current_user_can('manage_options')) {
        return;
    }
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <form action="options.php" method="post">
            <?php
            settings_fields('contact_form_settings');
            do_settings_sections('contact-form-settings');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

function render_section_info()
{
    echo '<p>' . __('Configure your contact form settings below:', 'sanasana') . '</p>';
}

function render_recaptcha_field()
{
    $value = get_option('sanasana_recaptcha_site_key');
    ?>
    <input type="text" 
           name="sanasana_recaptcha_site_key" 
           value="<?php echo esc_attr($value); ?>" 
           class="regular-text"
           placeholder="reCAPTCHA public key">
    <p class="description">
        <?php _e('Enter your reCAPTCHA site key here.', 'sanasana'); ?>
    </p>
    <?php
}

function render_api_url_field()
{
    $value = get_option('sanasana_api_base_url');
    ?>
    <input type="url" 
           name="sanasana_api_base_url" 
           value="<?php echo esc_attr($value); ?>" 
           class="regular-text"
           placeholder="API base URL">
    <p class="description">
        <?php _e('Enter your API base URL here.', 'sanasana'); ?>
    </p>
    <?php
}

function render_api_contact_form_path_field()
{
    $value = get_option('sanasana_api_contact_form_path');
    ?>
    <input type="text" 
           name="sanasana_api_contact_form_path" 
           value="<?php echo esc_attr($value); ?>" 
           class="regular-text"
           placeholder="API contact form path">
    <p class="description">
        <?php _e('Enter your API contact form path here.', 'sanasana'); ?>
    </p>
    <?php
}

function render_api_learn_more_form_path_field()
{
    $value = get_option('sanasana_api_learn_more_form_path');
    ?>
    <input type="text" 
           name="sanasana_api_learn_more_form_path" 
           value="<?php echo esc_attr($value); ?>" 
           class="regular-text"
           placeholder="API learn more form path">
    <p class="description">
        <?php _e('Enter your API learn more form path here.', 'sanasana'); ?>
    </p>
    <?php
}


// Add Google Settings Menu
function add_google_admin_menu() {
    add_submenu_page(
        'options-general.php', // Parent menu (Settings)
        __('Google Settings', 'sanasana'),
        __('Google', 'sanasana'),
        'manage_options',
        'google-settings',
        'render_google_settings_page'
    );
}
add_action('admin_menu', 'add_google_admin_menu');

// Register Google Settings
function register_google_settings() {
    register_setting('google_settings', 'sanasana_gtm_id');
    register_setting('google_settings', 'sanasana_ga_id');

    add_settings_section(
        'google_main_section',
        __('Google Configuration', 'sanasana'),
        'render_google_section_info',
        'google-settings'
    );

    add_settings_field(
        'sanasana_gtm_id',
        __('Google Tag Manager ID', 'sanasana'),
        'render_gtm_id_field',
        'google-settings',
        'google_main_section'
    );
	
	add_settings_field(
        'sanasana_ga_id',
        __('Google Analytics ID', 'sanasana'),
        'render_ga_id_field',
        'google-settings',
        'google_main_section'
    );	
}
add_action('admin_init', 'register_google_settings');

// Render Google Settings Page
function render_google_settings_page() {
    if (!current_user_can('manage_options')) {
        return;
    }
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <form action="options.php" method="post">
            <?php
            settings_fields('google_settings');
            do_settings_sections('google-settings');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

// Render Google Section Info
function render_google_section_info() {
    echo '<p>' . __('Configure your Google settings below:', 'sanasana') . '</p>';
}

// Render GTM ID Field
function render_gtm_id_field() {
    $value = get_option('sanasana_gtm_id');
    ?>
    <input type="text" 
           name="sanasana_gtm_id" 
           value="<?php echo esc_attr($value); ?>" 
           class="regular-text"
           placeholder="GTM-XXXXXX">
    <p class="description">
        <?php _e('Enter your Google Tag Manager ID (e.g., GTM-XXXXXX).', 'sanasana'); ?>
    </p>
    <?php
}

// Render GA ID Field
function render_ga_id_field() {
    $value = get_option('sanasana_ga_id');
    ?>
    <input type="text" 
           name="sanasana_ga_id" 
           value="<?php echo esc_attr($value); ?>" 
           class="regular-text"
           placeholder="UA-XXXXXXXXX-X or G-XXXXXXXXXX">
    <p class="description">
        <?php _e('Enter your Google Analytics ID (e.g., UA-XXXXXXXXX-X or G-XXXXXXXXXX).', 'sanasana'); ?>
    </p>
    <?php
}

function add_gtm_to_header() {
    ?>
    <!-- Google Tag Manager -->
    <script>
        (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id=' + i + dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','<?php echo get_option('sanasana_gtm_id'); ?>'); 
    </script>
    <!-- End Google Tag Manager -->
    <?php
}

function add_gtm_to_body() {
    ?>
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?php echo get_option('sanasana_gtm_id'); ?>"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
    <?php
}

function add_google_analytics() {
    ?>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo get_option('sanasana_ga_id'); ?>"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '<?php echo get_option('sanasana_ga_id'); ?>'); 
    </script>
    <?php
}

// Add GTM to header and body only if domain is sanasana.com
if (strtolower($_SERVER['HTTP_HOST']) === 'sanasana.com') {
    add_action('wp_head', 'add_gtm_to_header');
    add_action('wp_head', 'add_google_analytics');
    add_action('wp_body_open', 'add_gtm_to_body');
}

