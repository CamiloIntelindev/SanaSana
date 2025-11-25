<?php
/**
 * @package Programs
 *
 */

 namespace SanasanaInit\Programs;

 use SanasanaInit\General\BaseController;
 use WP_Query;

class ProgramsCompareController extends BaseController
{    
    // Nuevo Dominio de Traducción
    const TRANSLATION_DOMAIN = 'Sanasana Comparar programas';

    public function register()
    {
        add_shortcode('compare_programs', [$this, 'render_compare_programs']);
        add_shortcode('compare_programs_singular', [$this, 'render_compare_programs_singular']);
        add_shortcode('get_price_table_compare', [$this, 'render_price_table_compare']);
    }

    public function render_compare_programs() {
        ob_start();
        ?>
        <div class="pair_button_container">
            <div class="pair_button_left">
                <a href="<?php echo home_url() . '/' . esc_html__('programas', self::TRANSLATION_DOMAIN); ?>/" target="_self">
                    <?php esc_html_e('Comparar', self::TRANSLATION_DOMAIN); ?>
                </a>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    public function render_compare_programs_singular() {
        ob_start();
        $post = $this->get_post_from_current_url('programa'); 
        if (!$post) return ''; 
        $plan_crm_id = get_post_meta($post->ID, '_price_table_plan_crm_id', true);
        $affiliation_url = $this->get_affiliation_url($plan_crm_id);
        ?>
        <div class="pair_button_container">
            <div class="pair_button_left pair_button_left_singular">
                <a href="<?php echo trailingslashit( home_url( esc_html__('programas', self::TRANSLATION_DOMAIN) ) ); ?>" target="_self">
                    <?php esc_html_e('Comparar', self::TRANSLATION_DOMAIN); ?>
                </a>

            </div>
            <div class="pair_button_right pair_button_right_singular">
                <a href="<?php echo esc_url($affiliation_url); ?>" target="_self">
                    <?php esc_html_e('Afiliate', self::TRANSLATION_DOMAIN); ?>
                </a>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    public function render_price_table_compare(){
        $args = array(
            'post_type'       => 'programa',
            'posts_per_page'  => -1,
            'orderby'         => 'menu_order',
            'order'           => 'ASC',
        );

        $query = new \WP_Query($args);

        $program_details = get_option('price_table_program_details', []);
        $visibility = get_option('price_table_visibility', []);

        if (!$query->have_posts()) {
            // Dominio actualizado
            return '<p>' . esc_html__('No price plans available.', self::TRANSLATION_DOMAIN) . '</p>';
        }

        ob_start();
        ?>
        <div class="sanasana-accordion-container">
            <div class="flex-container-wrapper">
                <div class="flex-container">
                    <div class="row mb-5 row-cards">
                        <div class="col price-table-details-image">
                            <h2 class="programs_page_subtitle"><?php echo _e('Programas', self::TRANSLATION_DOMAIN); ?></h2>
                            <div class="price-annual-button price-annual-button-programs">
                                <div class="toggle-container">
                                    <span class="toggle-label"><?php echo _e('Mensual', self::TRANSLATION_DOMAIN); ?></span>
                                
                                    <div class="toggle" id="toggleSwitch-programs">
                                        <div class="slider"></div>
                                    </div>
                                    <span class="toggle-label"><?php echo _e('Anual', self::TRANSLATION_DOMAIN); ?></span>
                                </div>
                                </div>

                        </div>
                        <?php 
                            while ($query->have_posts()) : $query->the_post(); 
                                $description = get_post_meta(get_the_ID(), '_price_table_description', true);
                                $price = get_post_meta(get_the_ID(), '_price_table_price', true);
                                $annual_text = get_post_meta(get_the_ID(), '_price_table_annual_text', true);
                                $plan_crm_id = get_post_meta(get_the_ID(), '_price_table_plan_crm_id', true);
                                $plan_url = $this->get_affiliation_url($plan_crm_id);
                                $visible = get_post_meta(get_the_ID(), '_price_table_visible', true);
                        ?>
                            <?php if ($visible == "true"): ?>
                                <div class="col sanasana-accordion-col-item sanasana-accordion-col-item_programs">
                                    <div class="post-visible-status text-center mb-1">
                                    </div>
                                    <h2 class="sanasana-accordion-col-item_title hide-lines" ><?php echo esc_html(get_the_title(get_the_ID())); ?></h2>
                                
                                    <div class="sanasana-accordion-col-item_price" style="margin-bottom: 10px;" data-original="<?php echo esc_html($price); ?>">
                                        <div>$</div><?php echo esc_html($price); ?><span>+<?php esc_html_e('IVA incluido', self::TRANSLATION_DOMAIN); ?></span>
                                    </div>
                                    <p class="sanasana-accordion-col-item_annual" style="display: block;"><?php echo _e($annual_text, self::TRANSLATION_DOMAIN); ?></p>

                                    <a href="<?php echo esc_url($plan_url); ?>" class="price-button price-button-accordion"><?php _e('Afiliarme ahora', self::TRANSLATION_DOMAIN); ?></a>
                                </div>
                            <?php endif; ?>
                        <?php endwhile; ?>
                    </div>
                    <?php foreach ($program_details as $program_detail_index => $program_detail): ?>
                        <?php
                        // 1. Aplicar traducción al título del detalle del programa (Header del acordeón) - Dominio actualizado
                        $program_detail_title_original = $program_detail['title'] ?? '';
                        $program_detail_title_translated = apply_filters( 
                            'wpml_translate_single_string', 
                            $program_detail_title_original, 
                            self::TRANSLATION_DOMAIN, 
                            "program_detail_title_{$program_detail_index}" // Clave de registro
                        );
                        ?>
                        <div class="row sanasana-accordion-item">
                            <div class="col-12 sanasana-accordion-col-header">
                                <h2 class="sanasana-accordion-header" id="heading<?php echo $program_detail_index; ?>">
                                    <?php echo esc_html($program_detail_title_translated); ?>
                                </h2>
                                <button class="sanasana-accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#sanasana-collapse-<?php echo $program_detail_index; ?>" aria-expanded="true" aria-controls="sanasana-collapse-<?php echo $program_detail_index; ?>">
                                    <img src="<?php echo esc_url($this->plugin_url . 'assets/images/down-arrow.svg'); ?>" class="regular-arrow" alt="Arrow Icon">
                                </button>
                            </div>
                            <?php foreach ($program_detail['items'] as $index_detail => $item): ?>
                                <div class="sanasana-accordion-row-content">

                                <div class="col item-name">
                                    <div class="item-name-text">
                                        <?php 
                                        $item_name = [
                                            'Care and welleness', 'Care and welleness.', 
                                            'Atención Médica y de Bienestar', 'Atención Médica y de Bienestar.',
                                            'Servicios Hospitalarios', 'Servicios Hospitalarios.', 
                                            'Hospital Services', 'Hospital Services.',
                                            'Servicios Odontológicos', 'Servicios Odontológicos.', 
                                            'Dental Services', 'Dental Services.'
                                        ];

                                        $name = $item['name'] ?? '';

                                        // 2. Aplicar traducción al Nombre del Ítem - Dominio actualizado
                                        $translated_name = apply_filters( 
                                            'wpml_translate_single_string', 
                                            $name, 
                                            self::TRANSLATION_DOMAIN, 
                                            "item_name_{$program_detail_index}_{$index_detail}" // Clave de registro
                                        );

                                        if (in_array($translated_name, $item_name)) {
                                            echo '<strong><span style="font-size: 18px;">' . esc_html($translated_name) . '</span></strong>';
                                        } else {
                                            echo esc_html($translated_name);
                                        }
                                        ?>
                                    </div>


                                    <div class="col item-info">
                                        <div class="item-info-icon">
                                            <?php if (in_array($translated_name, $item_name) || (empty($item['info']))) { ?>
                                        
                                            <?php } else { ?>
                                                <img src="<?php echo esc_url($this->plugin_url . 'assets/images/info.svg'); ?>" alt="Info Icon" width="32" height="32"/>
                                            <?php } ?>
                                        </div>
                                        <div class="item-info-content">
                                            <?php
                                            $info = $item['info'] ?? '';

                                            // 3. Aplicar traducción a la Información del Ítem - Dominio actualizado
                                            $info_translated = apply_filters( 
                                                'wpml_translate_single_string', 
                                                $info, 
                                                self::TRANSLATION_DOMAIN, 
                                                "item_info_{$program_detail_index}_{$index_detail}" // Clave de registro
                                            );

                                            // Si contiene etiquetas HTML conocidas, lo imprimimos como HTML seguro
                                            if ( preg_match( '/<[^>]+>/', $info_translated ) ) {
                                                echo wp_kses_post( $info_translated );
                                            } else {
                                                // Si es texto plano, le aplicamos nl2br para los saltos de línea
                                                echo nl2br( esc_html( $info_translated ) );
                                            }
                                            ?>
                                        </div>

                                    </div>
                                </div>

                                <?php foreach ($query->posts as $post): ?>
                                    <?php $visible = get_post_meta($post->ID, '_price_table_visible', true); ?>
                                    <?php if ($visible == "true"): ?>
                                        <div class="col sanasana-accordion-col-item sanasana-accordion-col-item-check">
                                            <?php if (!empty($visibility[$program_detail_index][$index_detail][$post->ID])): ?>
                                                <img src="<?php echo esc_url($this->plugin_url . 'assets/images/check-sanasana.svg'); ?>" alt="Check Icon" width="30" height="25"/>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                        
                            </div> <?php endforeach; ?>
                            
                        
                        </div> <?php endforeach; ?>
                </div></div> </div>
        <?php

        wp_reset_postdata(); // buena práctica después del loop
        return ob_get_clean();
    }
    
}