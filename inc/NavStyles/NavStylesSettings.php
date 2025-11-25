<?php
/**
 * @package NavStyles
 */

namespace SanasanaInit\NavStyles;

use SanasanaInit\General\BaseController;

class NavStylesSettings extends BaseController
{
    public function register()
    {
        // Admin UI
        add_action('admin_menu', [$this, 'add_settings_page']);
        add_action('admin_init', [$this, 'register_settings']);

        // Cambiar logo por filtro de Astra solo en front
        add_filter('astra_logo', [$this, 'maybe_replace_logo_html'], 10, 1);

        // Estilos del header cuando aplique (gris)
        add_action('wp_head', [$this, 'maybe_print_header_styles']);
    }

    /* ---------------------------
     * Admin Settings Page
     * -------------------------- */

    public function add_settings_page()
    {
        add_menu_page(
            __('Ajustes de header de páginas', 'sanasana'),
            __('Header de páginas', 'sanasana'),
            'manage_options',
            'nav_styles_settings',
            [$this, 'render_settings_page'],
            'dashicons-hammer',
            20
        );
    }

    public function register_settings()
    {
        /* ===== Logo blanco por idioma ===== */
        register_setting('nav_styles_settings', 'nav_white_logo_slugs_es', ['sanitize_callback' => [$this, 'sanitize_slugs']]);
        register_setting('nav_styles_settings', 'nav_white_logo_slugs_en', ['sanitize_callback' => [$this, 'sanitize_slugs']]);
        register_setting('nav_styles_settings', 'nav_white_logo_src', ['sanitize_callback' => 'esc_url_raw']);

        add_settings_section(
            'nav_white_logo_section',
            __('Logo blanco por idioma', 'sanasana'),
            function () {
                echo '<p>'.esc_html__('Indica las páginas (por slug) donde quieres usar el logo blanco y define su URL.', 'sanasana').'</p>';
            },
            'nav_styles_settings'
        );

        add_settings_field(
            'nav_white_logo_slugs_es_field',
            __('Slugs ES (separados por comas)', 'sanasana'),
            [$this, 'render_slugs_textarea_es_white'],
            'nav_styles_settings',
            'nav_white_logo_section'
        );

        add_settings_field(
            'nav_white_logo_slugs_en_field',
            __('Slugs EN (separados por comas)', 'sanasana'),
            [$this, 'render_slugs_textarea_en_white'],
            'nav_styles_settings',
            'nav_white_logo_section'
        );

        add_settings_field(
            'nav_white_logo_src_field',
            __('URL del logo blanco (Azure/CDN)', 'sanasana'),
            [$this, 'render_logo_src_input_white'],
            'nav_styles_settings',
            'nav_white_logo_section'
        );

        /* ===== Logo negro (fondo gris) por idioma ===== */
        register_setting('nav_styles_settings', 'nav_gray_logo_slugs_es', ['sanitize_callback' => [$this, 'sanitize_slugs']]);
        register_setting('nav_styles_settings', 'nav_gray_logo_slugs_en', ['sanitize_callback' => [$this, 'sanitize_slugs']]);
        register_setting('nav_styles_settings', 'nav_gray_logo_src', ['sanitize_callback' => 'esc_url_raw']);

        add_settings_section(
            'nav_gray_logo_section',
            __('Logo negro por idioma (fondo gris)', 'sanasana'),
            function () {
                echo '<p>'.esc_html__('Indica las páginas (por slug) donde quieres fondo gris y usar el logo negro; define su URL.', 'sanasana').'</p>';
            },
            'nav_styles_settings'
        );

        add_settings_field(
            'nav_gray_logo_slugs_es_field',
            __('Slugs ES (separados por comas)', 'sanasana'),
            [$this, 'render_slugs_textarea_es_gray'],
            'nav_styles_settings',
            'nav_gray_logo_section'
        );

        add_settings_field(
            'nav_gray_logo_slugs_en_field',
            __('Slugs EN (separados por comas)', 'sanasana'),
            [$this, 'render_slugs_textarea_en_gray'],
            'nav_styles_settings',
            'nav_gray_logo_section'
        );

        add_settings_field(
            'nav_gray_logo_src_field',
            __('URL del logo negro (Azure/CDN)', 'sanasana'),
            [$this, 'render_logo_src_input_gray'],
            'nav_styles_settings',
            'nav_gray_logo_section'
        );

        /* ===== Compat: tu textarea previo (opcional) ===== */
        /*
        register_setting('nav_styles_settings', 'nav_styles_gray_slugs', ['sanitize_callback' => [$this, 'sanitize_slugs']]);

        add_settings_section(
            'nav_styles_section',
            __('Páginas con fondo gris (opcional, compatibilidad)', 'sanasana'),
            function () {
                echo '<p>'.esc_html__('Se mantiene por compatibilidad. Si usas también los slugs por idioma, estos tendrán prioridad.', 'sanasana').'</p>';
            },
            'nav_styles_settings'
        );

        add_settings_field(
            'nav_styles_gray_slugs_field',
            __('Slugs (separados por comas)', 'sanasana'),
            [$this, 'render_slugs_textarea_gray_legacy'],
            'nav_styles_settings',
            'nav_styles_section'
        );
        */
    }

    public function sanitize_slugs($input)
    {
        $slugs = array_map('trim', explode(',', (string) $input));
        $slugs = array_map('sanitize_title', $slugs);
        $slugs = array_filter($slugs);
        return implode(',', $slugs);
    }

    /* ===== Render fields: WHITE ===== */
    public function render_slugs_textarea_es_white()
    {
        $value = get_option('nav_white_logo_slugs_es', '');
        echo '<textarea name="nav_white_logo_slugs_es" rows="3" cols="80" style="width:100%;">' . esc_textarea($value) . '</textarea>';
        echo '<p class="description">'.esc_html__('Ej: inicio, nosotros, planes', 'sanasana').'</p>';
    }
    public function render_slugs_textarea_en_white()
    {
        $value = get_option('nav_white_logo_slugs_en', '');
        echo '<textarea name="nav_white_logo_slugs_en" rows="3" cols="80" style="width:100%;">' . esc_textarea($value) . '</textarea>';
        echo '<p class="description">'.esc_html__('Ej: home, about-us, plans', 'sanasana').'</p>';
    }
    public function render_logo_src_input_white()
    {
        $value = get_option('nav_white_logo_src', 'https://sanasanastoragews.blob.core.windows.net/blobwsdev/2025/02/cropped-logo_white_1x.webp');
        echo '<input type="url" name="nav_white_logo_src" value="' . esc_attr($value) . '" style="width:100%;" />';
        echo '<p class="description">'.esc_html__('Pega la URL del logo blanco (Azure/CDN).', 'sanasana').'</p>';
    }

    /* ===== Render fields: GRAY / BLACK ===== */
    public function render_slugs_textarea_es_gray()
    {
        $value = get_option('nav_gray_logo_slugs_es', '');
        echo '<textarea name="nav_gray_logo_slugs_es" rows="3" cols="80" style="width:100%;">' . esc_textarea($value) . '</textarea>';
        echo '<p class="description">'.esc_html__('Ej: contacto, blog, faq', 'sanasana').'</p>';
    }
    public function render_slugs_textarea_en_gray()
    {
        $value = get_option('nav_gray_logo_slugs_en', '');
        echo '<textarea name="nav_gray_logo_slugs_en" rows="3" cols="80" style="width:100%;">' . esc_textarea($value) . '</textarea>';
        echo '<p class="description">'.esc_html__('Ej: contact, blog, faq', 'sanasana').'</p>';
    }
    public function render_logo_src_input_gray()
    {
        $value = get_option('nav_gray_logo_src', 'https://sanasanastoragews.blob.core.windows.net/blobwsdev/2025/02/logo-black.png');
        echo '<input type="url" name="nav_gray_logo_src" value="' . esc_attr($value) . '" style="width:100%;" />';
        echo '<p class="description">'.esc_html__('Pega la URL del logo negro (Azure/CDN) para páginas con fondo gris.', 'sanasana').'</p>';
    }

    /* ===== Render field: LEGACY ===== */
    public function render_slugs_textarea_gray_legacy()
    {
        $value = get_option('nav_styles_gray_slugs', '');
        echo '<textarea name="nav_styles_gray_slugs" rows="3" cols="80" style="width:100%;">' . esc_textarea($value) . '</textarea>';
        echo '<p class="description">'.esc_html__('Slugs para el estilo de header gris (compatibilidad).', 'sanasana').'</p>';
    }

    public function render_settings_page()
    {
        ?>
        <div class="wrap">
            <h1><?php _e('Navigation Styles Settings', 'sanasana'); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('nav_styles_settings');
                do_settings_sections('nav_styles_settings');
                submit_button(__('Save Changes', 'sanasana'));
                ?>
            </form>
        </div>
        <?php
    }

    /* ---------------------------
     * Lógica de logo por página/idioma (con precedencia)
     * -------------------------- */

    public function maybe_replace_logo_html($logo_html)
    {
        if (is_admin()) return $logo_html;

        $lang = defined('ICL_LANGUAGE_CODE') ? ICL_LANGUAGE_CODE : 'es';

        $is_in = function (string $option_key): bool {
            $slugs_string = get_option($option_key, '');
            $slugs = array_filter(array_map('trim', explode(',', (string) $slugs_string)));
            if (empty($slugs)) return false;

            // Match en páginas o singles de CPT 'programa'
            return (
                $this->is_current_in_slugs($slugs) ||
                $this->is_current_in_slugs($slugs, ['programa'])
            );
        };

        $white_url = trim((string) get_option('nav_white_logo_src', ''));
        $gray_url  = trim((string) get_option('nav_gray_logo_src', ''));

        // 1) Precedencia: WHITE (por idioma)
        $white_key = ($lang === 'en') ? 'nav_white_logo_slugs_en' : 'nav_white_logo_slugs_es';
        if ($white_url && $is_in($white_key)) {
            return $this->build_logo_img($white_url);
        }

        // 2) GRAY/BLACK (por idioma) o legacy
        $gray_key = ($lang === 'en') ? 'nav_gray_logo_slugs_en' : 'nav_gray_logo_slugs_es';
        $in_gray  = $is_in($gray_key) || $this->is_in_legacy_gray();

        if ($in_gray) {
            if ($gray_url) {
                return $this->build_logo_img($gray_url);
            }
            return $logo_html; // sin URL configurada, deja el default
        }

        // 3) Default
        return $logo_html;
    }

    /* ---------------------------
     * Estilos del header para “gris”
     * -------------------------- */

    public function maybe_print_header_styles()
    {
        if (is_admin()) return;

        $lang = defined('ICL_LANGUAGE_CODE') ? ICL_LANGUAGE_CODE : 'es';

        $get_arr = function (string $opt_key): array {
            $str = get_option($opt_key, '');
            return array_filter(array_map('trim', explode(',', (string) $str)));
        };

        // Gray por idioma
        $arr_lang   = ($lang === 'en') ? $get_arr('nav_gray_logo_slugs_en') : $get_arr('nav_gray_logo_slugs_es');
        // Legacy
        $arr_legacy = $get_arr('nav_styles_gray_slugs');

        $match_lang = !empty($arr_lang) && (
            $this->is_current_in_slugs($arr_lang) ||
            $this->is_current_in_slugs($arr_lang, ['programa'])
        );

        $match_legacy = !empty($arr_legacy) && (
            $this->is_current_in_slugs($arr_legacy) ||
            $this->is_current_in_slugs($arr_legacy, ['programa'])
        );

        if ($match_lang || $match_legacy) {
            echo "
            <style>
                .ast-page-builder-template .entry-header { margin-top: 0; }
                .ast-main-header-wrap.main-header-bar-wrap { background: #F9F8F5 !important; }
                .menu-item > .menu-link > .menu-text { color: #000000; }
                .current-menu-item > .menu-link > .menu-text { color: #5166EC !important; }
                .menu-item > .menu-link > .menu-text:hover { color: #5166EC !important; }
                .menu-link > .dropdown-menu-toggle > .ast-icon > svg { fill: #000000; }
                svg.ast-mobile-svg.ast-menu-svg { fill: #000000 !important; }
                .ast-close-svg { fill: #5166ec !important; }
            </style>";
        }
    }

    /* ---------------------------
     * Helpers
     * -------------------------- */

    private function get_current_slug(): ?string
    {
        $id = get_queried_object_id();
        if (!$id) return null;
        $slug = get_post_field('post_name', $id);
        return $slug ?: null;
    }

    private function is_current_in_slugs(array $slugs, array $post_types = []): bool
    {
        if (!empty($post_types) && !is_singular($post_types)) {
            return false;
        }
        $current = $this->get_current_slug();
        return $current && in_array($current, $slugs, true);
    }

    private function is_in_legacy_gray(): bool
    {
        $legacy = get_option('nav_styles_gray_slugs', '');
        $slugs  = array_filter(array_map('trim', explode(',', (string) $legacy)));
        if (empty($slugs)) return false;

        return (
            $this->is_current_in_slugs($slugs) ||
            $this->is_current_in_slugs($slugs, ['programa'])
        );
    }

    private function build_logo_img(string $src): string
    {
        $alt = esc_attr(get_bloginfo('name'));
        $src = esc_url($src);

        // URL home según idioma (WPML)
        if (function_exists('icl_get_home_url')) {
            $home_url = esc_url(icl_get_home_url());
        } else {
            $home_url = esc_url(home_url('/'));
        }

        return '<a href="'.$home_url.'" class="custom-logo-link" rel="home">' .
                   '<img src="'.$src.'" class="custom-logo" alt="'.$alt.'" decoding="async" />' .
               '</a>';
    }
}
