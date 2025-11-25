<?php
/**
* @package  Sanasana
 * Enqueues CSS and JS Files
 */

namespace SanasanaInit\General;

class CustomFontsController {

    public function register() {
        add_filter( 'fl_builder_font_families_system', [ $this, 'agregar_fuentes_beaver' ] );
    }

    public function agregar_fuentes_beaver( $system_fonts ) {

        $system_fonts['Moranga'] = [
           // 'fallback' => 'sans-serif',
            'weights'  => [ '300', '400', '500', '700', '900' ],
        ];

        $system_fonts['Poppins'] = [
           // 'fallback' => 'sans-serif',
            'weights'  => [ '100', '200', '300', '400', '500', '600', '700', '800', '900' ],
        ];

        return $system_fonts;
    }
}
