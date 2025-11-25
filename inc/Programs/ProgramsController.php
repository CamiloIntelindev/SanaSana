<?php
/**
 * @package Programs
 *
 */

 namespace SanasanaInit\Programs;

 use SanasanaInit\General\BaseController;
 use WP_Query;

class ProgramsController
 extends BaseController
{    
    public function register()
    {
        add_action( 'init', [$this, 'programs_post_type'] );
    }

    public function programs_post_type()
    {
        $labels = array(
            'name'               => __('Programas', 'pricetable'),
            'singular_name'      => __('Programa', 'pricetable'),
            'menu_name'          => __('Programas', 'pricetable'),
            'name_admin_bar'     => __('Programa', 'pricetable'),
            'add_new'            => __('Add New', 'pricetable'),
            'add_new_item'       => __('Add New Programa', 'pricetable'),
            'new_item'           => __('New Programa', 'pricetable'),
            'edit_item'          => __('Edit Programa', 'pricetable'),
            'view_item'          => __('View Programa', 'pricetable'),
            'all_items'          => __('All Programas', 'pricetable'),
            'search_items'       => __('Search Programas', 'pricetable'),
            'not_found'          => __('No Programas found.', 'pricetable'),
            'not_found_in_trash' => __('No Programas found in Trash.', 'pricetable')
        );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array('slug' => 'programa'),
            'capability_type'    => 'post',
            'has_archive'        => false,
            'hierarchical'       => false,
            'menu_position'      => 20,
            'menu_icon'          => 'dashicons-editor-table',
            'supports'           => array('title', 'thumbnail'),
            'show_in_rest'       => true, // Para compatibilidad con Gutenberg
        );

        register_post_type('programa', $args);
    }
}
