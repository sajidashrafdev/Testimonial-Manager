<?php
/*
 * Plugin Name:       Testimonial Manager
 * Description:       Manage and display testimonials on your site.
 * Version:           1.0.0
 * Author:            Sajid Ashraf
 * Text Domain:       wp-tm
 */

if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Function to display the main menu and submenu on sidebar in dashboard
add_action('admin_menu', 'wp_tm_menu');
function wp_tm_menu()
{
    add_menu_page(
        'Testimonial',                // Page title (shown in browser tab)
        'Testimonial',                // Menu title (shown in WP admin menu)
        'manage_options',             // Permission required
        'wp-tm',                      // Menu slug
        'wp_tm_page',                 // Callback function to display page content
        'dashicons-testimonial',      // Icon in admin menu
        20                            // Menu position
    );

    add_submenu_page(
        'wp-tm',                      // Parent slug
        'Add New Testimonial',        // Page title
        'Add New Testimonial',        // Menu title
        'manage_options',             // Capability
        'add_new_testimonial',        // Menu slug
        'add_new_testimonial_page'    // Callback function to display page content
    );
}

// Function to display the main page content
function wp_tm_page()
{
    echo '<h1>All Testimonials</h1>';
    echo '<p>Use the shortcode <code>[wp-tm-testimonials]</code> to display testimonials on any page or post.</p>';
    echo '<h2>View submitted testimonials below:</h2>';
    echo '<table border="1" cellpadding="10" cellspacing="0">
            <thead>
                <tr>
                    <th>Sr.</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Message</th>
                    <th>Rating</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>';
            $submitted_testimonials = get_option('wp-tm-testimonial');
            if (!empty($submitted_testimonials)) {
                for ($i = 0; $i < count($submitted_testimonials); $i++) {
                    echo '<tr>';
                    echo '<td>' . ($i + 1) . '</td>';
                    echo '<td>' . esc_html($submitted_testimonials[$i]['post_title']) . '</td>';
                    echo '<td>' . esc_html($submitted_testimonials[$i]['email']) . '</td>';
                    echo '<td>' . esc_html($submitted_testimonials[$i]['post_content']) . '</td>';
                    echo '<td>' . esc_html($submitted_testimonials[$i]['rating']) . '</td>';
                    echo '<td>' . esc_html($submitted_testimonials[$i]['post_status']) . '</td>';
                    echo '</tr>';
                }
            } else {
                echo '<tr><td colspan="6">No testimonials found.</td></tr>';
            }
            echo '</tbody></table>';
}

// Function to display the add new testimonial page content
function add_new_testimonial_page(){
    echo '<h1>Add New Testimonial</h1><br><br>';
    echo wp_tm_client_review_form();
}
//add shortcode to display the form on frontend using [wp-tm-form]
add_shortcode('wp-tm-form', 'wp_tm_client_review_form');

function wp_tm_client_review_form()
{
    $form  = '
    <form method="post" class="tm-testimonial-form">
        <p>
            <label for="tm_name"> Name</label>
            <input type="text" id="tm_name" name="tm_name" required>
        </p>
        <p>
            <label for="tm_email"> Email</label>
            <input type="email" id="tm_email" name="tm_email" required>
        </p>
        <p>
            <label for="tm_message"> Message</label>
            <textarea id="tm_message" name="tm_message" required></textarea>
        </p>
        <p>
            <label for="tm_rating"> Rating</label>
            <select id="tm_rating" name="tm_rating" required>
                <option value="Excellent">Excellent</option>
                <option value="Very Good">Very Good</option>
                <option value="Good">Good</option>
                <option value="Fair">Fair</option>
                <option value="Poor">Poor</option>
            </select>
        </p>
        <p>
            <input type="submit" name="tm_submit_testimonial" value="Submit Testimonial">
        </p>
    </form>';
    return $form;
}

// Handle form submission
add_action('init', 'wp_tm_handle_form_submission');
function wp_tm_handle_form_submission()
{
    if (isset($_POST['tm_submit_testimonial'])) {
        // Sanitize and validate form inputs
        $name    = sanitize_text_field($_POST['tm_name']);
        $email   = sanitize_email($_POST['tm_email']);
        $message = sanitize_textarea_field($_POST['tm_message']);
        $rating  = sanitize_text_field($_POST['tm_rating']);

        if (!is_email($email)) {
            echo '<p style="color:red;">Invalid email address.</p>';
            return;
        }

        // Create a new testimonial post
        if (!is_array(get_option('wp-tm-testimonial'))) {
            $testimonial_post = [];
        } else {
            $testimonial_post = get_option('wp-tm-testimonial');
        }
        
        $testimonial_post[] = array(
            'post_title'   => $name,
            'email'        => $email,
            'post_content' => $message,
            'rating'       => $rating,
            'post_status'  => 'pending', // Set to 'pending' for admin review
        );

        // Insert the post into the database
        $post_id = update_option("wp-tm-testimonial", $testimonial_post);

        if ($post_id) {
            echo '<p style="color:green;">Thank you for your testimonial! It is pending approval.</p>';
        } else {
            echo '<p style="color:red;">There was an error submitting your testimonial. Please try again.</p>';
        }
    }
}