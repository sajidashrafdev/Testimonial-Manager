<?php
/*
 * Plugin Name:       Testimonial Manager
 * Description:       Manage and display testimonials on your site.
 * Version:           1.0.0
 * Author:            Sajid Ashraf
 * Text Domain:       testimonial-manager
 */

if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

function testimonial_manager_custom_post_type()
{
    register_post_type(
        'testimonial',
        array(
            'labels'      => array(
                'name'          => __('Testimonials', 'testimonial-manager'),
                'singular_name' => __('Testimonial', 'testimonial-manager'),
                'add_new_item'  => __('Add New Testimonial', 'testimonial-manager'),
                'edit_item'     => __('Edit Testimonial', 'testimonial-manager'),
                'new_item'      => __('New Testimonial', 'testimonial-manager'),
                'view_item'     => __('View Testimonial', 'testimonial-manager'),
            ),
            'public'      => true,
            'has_archive' => true,
            'menu_position' => 5,
            'menu_icon'   => 'dashicons-testimonial',
        )
    );
}
add_action('init', 'testimonial_manager_custom_post_type');


// Add submenu under "Testimonials"

function all_testimonials_page()
{


    add_submenu_page(
        'edit.php?post_type=testimonial',
        'All Testimonials',
        'All Testimonials',
        'manage_options',
        'all-testimonials',
        'all_testimonials_page_callback'
    );
}

add_action('admin_menu', 'all_testimonials_page');

function all_testimonials_page_callback()
{
    echo '<div class="wrap"><h1>All Testimonials</h1></div>';
}