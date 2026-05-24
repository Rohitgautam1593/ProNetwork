<?php
/**
 * Plugin Name: Registration Form Plugin
 * Description: Creates a custom registration form and stores submissions in a custom database table.
 * Version: 1.0
 * Author: Your Name
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Create database table on plugin activation
 */
register_activation_hook( __FILE__, 'tgd_create_registration_table' );

function tgd_create_registration_table() {

    global $wpdb;

    $table_name = $wpdb->prefix . 'custom_registrations';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        name varchar(100) NOT NULL,
        age int(3) NOT NULL,
        phone varchar(20) NOT NULL,
        email varchar(100) NOT NULL,
        registered_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';

    dbDelta( $sql );
}

/**
 * Registration Form Shortcode
 * Usage: [registration_form]
 */
add_shortcode( 'registration_form', 'tgd_render_registration_form' );

function tgd_render_registration_form() {

    ob_start();

    if ( isset( $_GET['reg_success'] ) ) {
        echo '<p style="color:green;font-weight:bold;">
                Registration successful! Thank you.
              </p>';
    }
    ?>

    <form action="" method="POST" style="
        max-width:500px;
        background:#f9f9f9;
        padding:20px;
        border:1px solid #ddd;
        border-radius:5px;
    ">

        <?php wp_nonce_field( 'tgd_submit_registration', 'tgd_nonce' ); ?>

        <p>
            <label>Full Name</label><br>
            <input
                type="text"
                name="user_name"
                required
                style="width:100%;padding:10px;"
            >
        </p>

        <p>
            <label>Age</label><br>
            <input
                type="number"
                name="user_age"
                min="1"
                max="120"
                required
                style="width:100%;padding:10px;"
            >
        </p>

        <p>
            <label>Phone Number</label><br>
            <input
                type="tel"
                name="user_phone"
                required
                style="width:100%;padding:10px;"
            >
        </p>

        <p>
            <label>Email Address</label><br>
            <input
                type="email"
                name="user_email"
                required
                style="width:100%;padding:10px;"
            >
        </p>

        <p>
            <input
                type="submit"
                name="tgd_submit_form"
                value="Register Now"
                style="
                    background:#0073aa;
                    color:#fff;
                    border:none;
                    padding:12px 20px;
                    cursor:pointer;
                    border-radius:4px;
                "
            >
        </p>

    </form>

    <?php

    echo tgd_get_registration_data_markup();

    return ob_get_clean();
}

/**
 * Handle form submission
 */
add_action( 'init', 'tgd_process_registration_form' );

function tgd_process_registration_form() {

    if ( ! isset( $_POST['tgd_submit_form'] ) ) {
        return;
    }

    if (
        ! isset( $_POST['tgd_nonce'] ) ||
        ! wp_verify_nonce(
            $_POST['tgd_nonce'],
            'tgd_submit_registration'
        )
    ) {
        wp_die( 'Security check failed.' );
    }

    global $wpdb;

    $table_name = $wpdb->prefix . 'custom_registrations';

    $name  = sanitize_text_field( $_POST['user_name'] );
    $age   = intval( $_POST['user_age'] );
    $phone = sanitize_text_field( $_POST['user_phone'] );
    $email = sanitize_email( $_POST['user_email'] );

    if ( empty( $name ) || empty( $email ) ) {
        return;
    }

    $wpdb->insert(
        $table_name,
        array(
            'name'  => $name,
            'age'   => $age,
            'phone' => $phone,
            'email' => $email
        ),
        array(
            '%s',
            '%d',
            '%s',
            '%s'
        )
    );

    wp_redirect(
        add_query_arg(
            'reg_success',
            '1',
            wp_get_referer()
        )
    );

    exit;
}

/**
 * Display registration records
 * Usage: [registration_data]
 */
add_shortcode( 'registration_data', 'tgd_display_registration_data' );

function tgd_display_registration_data() {

    return tgd_get_registration_data_markup();
}

function tgd_get_registration_data_markup() {

    global $wpdb;

    $table_name = $wpdb->prefix . 'custom_registrations';

    $results = $wpdb->get_results(
        "SELECT * FROM $table_name ORDER BY id DESC"
    );

    if ( empty( $results ) ) {
        return '<p>No registrations found.</p>';
    }

    $output = '
    <table border="1" cellpadding="10" cellspacing="0" style="width:100%; border-collapse:collapse;">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Age</th>
            <th>Phone</th>
            <th>Email</th>
            <th>Registered At</th>
        </tr>';

    foreach ( $results as $row ) {

        $output .= '
        <tr>
            <td>' . esc_html( $row->id ) . '</td>
            <td>' . esc_html( $row->name ) . '</td>
            <td>' . esc_html( $row->age ) . '</td>
            <td>' . esc_html( $row->phone ) . '</td>
            <td>' . esc_html( $row->email ) . '</td>
            <td>' . esc_html( $row->registered_at ) . '</td>
        </tr>';
    }

    $output .= '</table>';

    return $output;
}
