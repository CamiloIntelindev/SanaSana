<?php
/**
 * @package PriceTable
 *
 */

 namespace SanasanaInit\Programs;

 use SanasanaInit\General\BaseController;
 use WP_Query;

class ProgramsController extends BaseController
{    
    public function register()
    {
        add_shortcode('compare_programs', [$this, 'render_compare_programs']);
        add_shortcode('compare_programs_singular', [$this, 'render_compare_programs_singular']);
    }
    public function render_compare_programs(){
         ob_start();
         ?>
         <div class="pair_button_container">
            <div class="pair_button_left">
                <a href="<?php echo home_url().'/'.esc_html__('programas', 'sanasana'); ?>/" target="_self" class="">
                    <?php _e('Comparar', 'sanasana'); ?>
                </a>
            </div>
            <!--<div class="pair_button_right">
                <a href="<?php echo home_url().'/'. esc_html__('cuestionario', 'sanasana'); ?>/" target="_self" class="">
                    <?php _e('Dudas? Empezá el quiz', 'sanasana'); ?>
                </a>
            </div>-->
         </div>
         <?php
         return ob_get_clean();
    }
	
    public function render_compare_programs_singular(){
         ob_start();
         $post = $this->get_post_from_current_url('price'); 
         if (!$post) return ''; 
         $plan_crm_id = get_post_meta($post->ID, '_price_table_plan_crm_id', true);
         $affiliation_url = $this->get_affiliation_url($plan_crm_id);
         ?>
         <div class="pair_button_container">
            <div class="pair_button_left pair_button_left_singular">
                <a href="<?php echo home_url().'/'. esc_html__('programas', 'sanasana'); ?>/" target="_self" class="">
                    <?php _e('Comparar', 'sanasana'); ?>
                </a>
            </div>
            <div class="pair_button_right pair_button_right_singular">
                <a href="<?php echo esc_url($affiliation_url); ?>" target="_self" class="">
                    <?php echo esc_html__('Afiliate', 'sanasana'); ?>
                </a>
            </div>
         </div>
         <?php
         return ob_get_clean();
    }
	
}