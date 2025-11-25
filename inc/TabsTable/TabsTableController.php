<?php
/**
 * @package TabsTable
 */

namespace SanasanaInit\TabsTable;

use SanasanaInit\General\BaseController;

class TabsTableController extends BaseController
{
    public function register()
    {
        add_action('init', [$this, 'tabs_table_post_type']); // prioridad default ok
    }

    public function tabs_table_post_type()
    {
        $textdomain = 'tabs-table';

        $labels = [
            'name'                  => __('Tabs Tables', $textdomain),
            'singular_name'         => __('Tabs Table', $textdomain),
            'menu_name'             => __('Tabs Tables', $textdomain),
            'name_admin_bar'        => __('Tabs Table', $textdomain),
            'add_new'               => __('Add New', $textdomain),
            'add_new_item'          => __('Add New Tabs Table', $textdomain),
            'new_item'              => __('New Tabs Table', $textdomain),
            'edit_item'             => __('Edit Tabs Table', $textdomain),
            'view_item'             => __('View Tabs Table', $textdomain),
            'all_items'             => __('All Tabs Tables', $textdomain),
            'search_items'          => __('Search Tabs Tables', $textdomain),
            'not_found'             => __('No Tabs Tables found.', $textdomain),
            'not_found_in_trash'    => __('No Tabs Tables found in Trash.', $textdomain),
        ];

        $args = [
            'labels'                => $labels,
            'public'                => false,
            'publicly_queryable'    => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'query_var'             => true,
            'rewrite'               => ['slug' => 'tabs', 'with_front' => false],
            'has_archive'           => false,          // creará /tabs/
            'hierarchical'          => false,
            'menu_position'         => 20,
            'menu_icon'             => 'dashicons-editor-table',
            'supports'              => ['title', 'editor'], // añade más si necesitas
            'show_in_rest'          => true,
            'capability_type'       => 'post',
        ];

        register_post_type('tabs', $args);
    }
}
