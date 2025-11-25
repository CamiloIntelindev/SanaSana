<?php
/**
 * @package Questionnaire
 */

namespace SanasanaInit\Questionnaire;

use SanasanaInit\General\BaseController;

class QuestionnaireController extends BaseController
{
    public function register()
    {
        //add_action('init', [$this, 'register_questionnaire_post_type']);
    }

    public function register_questionnaire_post_type()
    {
        $labels = [
            'name'               => __('Cuestionarios', 'sanasanainit'),
            'singular_name'      => __('Cuestionario', 'sanasanainit'),
            'menu_name'          => __('Cuestionarios', 'sanasanainit'),
            'add_new'            => __('Agregar nuevo', 'sanasanainit'),
            'add_new_item'       => __('Nuevo Cuestionario', 'sanasanainit'),
            'edit_item'          => __('Editar Cuestionario', 'sanasanainit'),
            'new_item'           => __('Nuevo Cuestionario', 'sanasanainit'),
            'view_item'          => __('Ver Cuestionario', 'sanasanainit'),
            'search_items'       => __('Buscar Cuestionario', 'sanasanainit'),
            'not_found'          => __('No se encontraron cuestionarios', 'sanasanainit'),
            'not_found_in_trash' => __('No hay cuestionarios en la papelera', 'sanasanainit'),
        ];

        $args = [
            'labels'             => $labels,
            'public'             => true,
            'has_archive'        => false,
            'rewrite'            => ['slug' => 'questionnaire'],
            'show_in_rest'       => true,
            'supports'           => ['title', 'editor'],
            'menu_icon'          => 'dashicons-feedback',
            'show_ui'            => true,
            'show_in_menu'       => true,
            'hierarchical'       => false,
            'publicly_queryable' => true,
        ];

        register_post_type('questionnaire', $args);
    }
}
