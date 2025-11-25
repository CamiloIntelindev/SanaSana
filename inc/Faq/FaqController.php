<?php
/**
 * @package Faq
 *
 */

namespace SanasanaInit\Faq;

use SanasanaInit\General\BaseController;
use WP_Query;

class FaqController extends BaseController
{    
    public function register()
    {
        add_action( 'init', [$this, 'tabs_faq_table_post_type'] );
    }

    public function tabs_faq_table_post_type()
    {
        $labels = array(
            'name'               => __('Preguntas fecuentes', 'Faq'),
            'singular_name'      => __('Pregunta frecuente', 'Faq'),
            'menu_name'          => __('Preguntas fecuentes', 'Faq'),
            'name_admin_bar'     => __('Pregunta frecuente', 'Faq'),
            'add_new'            => __('Add New Pregunta frecuente', 'Faq'),
            'add_new_item'       => __('Add New Pregunta frecuente', 'Faq'),
            'new_item'           => __('New Pregunta frecuente', 'Faq'),
            'edit_item'          => __('Edit Pregunta frecuente', 'Faq'),
            'view_item'          => __('View Pregunta frecuente', 'Faq'),
            'all_items'          => __('All Preguntas fecuentes', 'Faq'),
            'search_items'       => __('Search Preguntas fecuentes', 'Faq'),
            'not_found'          => __('No Preguntas fecuentes found.', 'Faq'),
            'not_found_in_trash' => __('No Preguntas fecuentes found in Trash.', 'Faq')
        );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array('slug' => 'faq-tab'),
            'capability_type'    => 'post',
            'has_archive'        => false,
            'hierarchical'       => false,
            'menu_position'      => 20,
            'menu_icon'          => 'dashicons-editor-table',
            'supports'           => array('title'),
            'show_in_rest'       => true, // Para compatibilidad con Gutenberg
        );

        register_post_type('faq-tab', $args);
    }

}
