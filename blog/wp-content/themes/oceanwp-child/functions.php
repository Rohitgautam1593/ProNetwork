<?php
add_action( 'wp_enqueue_scripts', 'enqueue_parent_styles' );

function enqueue_parent_styles() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri().'/style.css' );
}

// function create_books_cpt() { 
//     $args = array( 
//         'label'        => 'Books', 
//         'public'       => true, 
//         'has_archive'  => true, 
//         'menu_icon'    => 'dashicons-book', 
//         'supports'     => array('title', 'editor', 'thumbnail'), 
//         'show_in_rest' => true,  // enables Gutenberg editor 
//         'rewrite'      => array('slug' => 'books'), 
//     ); 
//     register_post_type('book', $args); 
// } 
// add_action('init', 'create_books_cpt'); 

// function books_meta_box() {
//     add_meta_box(
//         'book_details', // unique ID
//         'Book Details', // box title
//         'books_meta_box_html', // function that prints the form
//         'book' // post type
//     );
// }
// add_action('add_meta_boxes', 'books_meta_box');

// function books_meta_box_html($post) {
//     $isbn = get_post_meta($post->ID, 'isbn', true);
//     $author = get_post_meta($post->ID, 'book_author', true);
//     $price = get_post_meta($post->ID, 'book_price', true);
    
//     // Output security nonce field so save function can verify it
//     wp_nonce_field( 'save_book_details_nonce', 'book_details_nonce_field' );

// }

// function save_books_meta($post_id) {
//     // Verify nonce for security
//     if (!isset($_POST['book_details_nonce_field']) || 
//         !wp_verify_nonce($_POST['book_details_nonce_field'], 'save_book_details_nonce')) {
//         return;
//     }

//     // Update fields if they exist
//     if (isset($_POST['isbn'])) {
//         update_post_meta($post_id, 'isbn', sanitize_text_field($_POST['isbn']));
//     }
//     if (isset($_POST['book_author'])) {
//         update_post_meta($post_id, 'book_author', sanitize_text_field($_POST['book_author']));
//     }
//     if (isset($_POST['book_price'])) {
//         update_post_meta($post_id, 'book_price', sanitize_text_field($_POST['book_price']));
//     }
// }

// ==========================================
// PRONETWORK BRAND INTEGRATION
// ==========================================

// Force the ProNetwork SVG favicon across the WordPress blog
add_action('wp_head', function() {
    echo '<link rel="icon" type="image/svg+xml" href="/ProNetwork/public/favicon.svg">' . "\n";
}, 1);

add_action('admin_head', function() {
    echo '<link rel="icon" type="image/svg+xml" href="/ProNetwork/public/favicon.svg">' . "\n";
}, 1);

add_filter('get_site_icon_url', function($url) {
    return '/ProNetwork/public/favicon.svg';
});

// Force standardized ProNetwork logo branding markup in WordPress header
add_filter('has_custom_logo', '__return_true');

add_filter('get_custom_logo', function($html) {
    $logo_url = home_url('/');
    $html = '<a href="' . esc_url($logo_url) . '" class="pn-brand flex items-center gap-2 flex-shrink-0 rounded-xl px-1 -ml-1" aria-label="ProNetwork home" style="display: flex; align-items: center; gap: 8px; text-decoration: none;">';
    $html .= '<span class="pn-brand-mark" style="display: inline-flex; align-items: center; justify-content: center; width: 2.25rem; height: 2.25rem; border-radius: 12px; color: #fff; font-family: \'Manrope\', \'Inter\', sans-serif; font-weight: 900; background: linear-gradient(135deg, #0a66c2, #084a8f); box-shadow: 0 10px 22px rgba(10, 102, 194, 0.24); line-height: 1;">P</span>';
    $html .= '<span style="font-size: 1.25rem; font-weight: 900; color: #ffffff; font-family: \'Manrope\', \'Inter\', sans-serif; tracking-tight; line-height: 1;">ProNetwork</span>';
    $html .= '</a>';
    return $html;
});

// Enqueue Manrope font for logo consistency
add_action('wp_enqueue_scripts', function() {
    wp_enqueue_style('google-font-manrope', 'https://fonts.googleapis.com/css2?family=Manrope:wght@800;900&display=swap', array(), null);
});
// Force outgoing WordPress emails to use noreply.com branding
add_filter('wp_mail_from', function($email) {
    return 'noreply@pronetwork.com';
});

add_filter('wp_mail_from_name', function($name) {
    return 'noreply.com';
});
?>
