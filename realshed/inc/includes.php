<?php
/**
 * Theme Configurations
 *
 * Path: wp-content/themes/realshed/inc/includes.php
 * @package Realshed
 */

// =========================================================================
// LOAD CLASSES
// =========================================================================

if ( file_exists( REALSHED_CLASSES_DIR . '/class-comments-helper.php' ) ) {
    require_once REALSHED_CLASSES_DIR . '/class-comments-helper.php';
}
if ( file_exists( REALSHED_CLASSES_DIR . '/class-realshed-bs-nav.php' ) ) {
    require_once REALSHED_CLASSES_DIR . '/class-realshed-bs-nav.php';
}
if ( file_exists( REALSHED_CLASSES_DIR . '/class-realshed-pagination.php' ) ) {
    require_once REALSHED_CLASSES_DIR . '/class-realshed-pagination.php';
}

if ( file_exists( REALSHED_INC_DIR . '/register-scripts.php' ) ) {
    require_once REALSHED_INC_DIR . '/register-scripts.php';
}
if ( file_exists( REALSHED_INC_DIR . '/register-widget-areas.php' ) ) {
    require_once REALSHED_INC_DIR . '/register-widget-areas.php';
}
if ( file_exists( REALSHED_META_DIR . '/breadcrumb-background.php' ) ) {
    require_once REALSHED_META_DIR . '/breadcrumb-background.php';
}
if ( file_exists( REALSHED_CUSTOMIZER_DIR . '/page-header.php' ) ) {
    require_once REALSHED_CUSTOMIZER_DIR . '/page-header.php';
}
if ( file_exists( REALSHED_INC_DIR . '/customizer.php' ) ) {
    require_once REALSHED_INC_DIR . '/customizer.php';
}
if ( file_exists( REALSHED_INC_DIR . '/tgm/tgm-config.php' ) ) {
    require_once REALSHED_INC_DIR . '/tgm/tgm-config.php';
}

// =========================================================================
// DISABLE GUTENBERG
// =========================================================================

add_filter( 'use_block_editor_for_post', '__return_false', 10 );
add_filter( 'use_widgets_block_editor',  '__return_false' );

// =========================================================================
// THEME SETUP
// =========================================================================

add_action( 'after_setup_theme', 'realshed_setup' );
if ( ! function_exists( 'realshed_setup' ) ) {
    function realshed_setup() {

        load_theme_textdomain( 'realshed', REALSHED_THEME_DIR . '/languages' );
        add_theme_support( 'automatic-feed-links' );
        add_theme_support( 'title-tag' );
        add_theme_support( 'post-thumbnails' );

        add_image_size( 'service-thumb',       800, 450, true );
        add_image_size( 'team-thumb',          260, 260, true );
        add_image_size( 'realshed-grid-thumb', 400, 300, true );

        $post_types = array( 'post', 'page', 'sliders' );
        $supports   = array(
            'title', 'editor', 'author', 'thumbnail', 'excerpt',
            'trackbacks', 'custom-fields', 'comments', 'revisions',
            'page-attributes', 'post-formats',
        );
        foreach ( $post_types as $pt ) {
            foreach ( $supports as $s ) {
                add_post_type_support( $pt, $s );
            }
        }

        add_theme_support( 'html5', array(
            'search-form', 'comment-form', 'comment-list',
            'gallery', 'caption', 'style', 'script',
        ) );

        add_theme_support( 'custom-logo', array(
            'header-text'  => array( 'site-title', 'site-description' ),
            'height'       => 55,
            'width'        => 215,
            'flex-width'   => true,
            'flex-height'  => true,
        ) );

        add_theme_support( 'customize-selective-refresh-widgets' );

        register_nav_menus( array(
            'primary'       => esc_html__( 'Primary Menu',  'realshed' ),
            'topbar'        => esc_html__( 'Topbar Menu',   'realshed' ),
            'services'      => esc_html__( 'Services Menu', 'realshed' ),
            'footer-menu-1' => esc_html__( 'Footer Menu 1', 'realshed' ),
            'footer-menu-2' => esc_html__( 'Footer Menu 2', 'realshed' ),
        ) );
    }
}

// =========================================================================
// META BOXES
// =========================================================================

function realshed_default_visible_meta_boxes( $hidden, $screen ) {
    if ( 'post' === $screen->id || 'page' === $screen->id ) {
        $to_show = array(
            'postexcerpt', 'trackbacksdiv', 'postcustom',
            'commentstatusdiv', 'commentsdiv', 'slugdiv', 'authordiv',
        );
        $hidden = array_diff( $hidden, $to_show );
    }
    return $hidden;
}
add_filter( 'default_hidden_meta_boxes', 'realshed_default_visible_meta_boxes', 10, 2 );

// =========================================================================
// EXCERPT
// =========================================================================

add_filter( 'excerpt_length', function() { return 25; }, 999 );
add_filter( 'excerpt_more',   function() { return ' ....'; } );

// =========================================================================
// PAGINATION HELPERS
// =========================================================================

function realshed_numbering_paginate( $args = array() ) {
    echo Realshed_Pagination::numbering_paginate( $args );
}
function realshed_get_pagination( $args = array() ) {
    return Realshed_Pagination::numbering_paginate( $args );
}
function realshed_simple_pagination() {
    echo Realshed_Pagination::simple_pagination();
}
function realshed_pagination_with_info() {
    echo Realshed_Pagination::pagination_with_info();
}
function realshed_ajax_load_more() {
    echo Realshed_Pagination::ajax_load_more();
}
function realshed_custom_pagination( $query, $args = array() ) {
    echo Realshed_Pagination::custom_query_pagination( $query, $args );
}

function realshed_core_pagination_sync( $query ) {
    if ( ! is_admin() && $query->is_main_query() ) {
        if ( $query->is_home() || $query->is_archive() || $query->is_search() ) {
            $query->set( 'posts_per_page', get_option( 'posts_per_page', 10 ) );
        }
    }
}
add_action( 'pre_get_posts', 'realshed_core_pagination_sync' );

// =========================================================================
// ARCHIVE LINK STYLING
// =========================================================================

function realshed_style_post_counts( $html ) {
    $html = str_replace( array( '&nbsp;', ' ' ), ' ', $html );
    return preg_replace( '#\s*</a>\s*\((\d+)\)#', '<span>($1)</span></a>', $html );
}
add_filter( 'get_archives_link',  'realshed_style_post_counts' );
add_filter( 'wp_list_categories', 'realshed_style_post_counts' );

// =========================================================================
// NAVIGATION
// =========================================================================

function realshed_default_menu_fallback( $args ) {
    if ( 'primary' === $args['theme_location'] ) {
        echo '<ul class="navigation clearfix">';
        $home_class = is_front_page() ? 'current' : '';
        echo '<li class="' . $home_class . '"><a href="' . esc_url( home_url( '/' ) ) . '"><span>' . __( 'Home', 'realshed' ) . '</span></a></li>';
        echo '</ul>';
    }
}

function realshed_create_default_menu() {
    $primary_menu = wp_get_nav_menu_object( 'Primary Menu' );
    if ( ! $primary_menu ) {
        $menu_id = wp_create_nav_menu( 'Primary Menu' );
        wp_update_nav_menu_item( $menu_id, 0, array(
            'menu-item-title'  => __( 'Home', 'realshed' ),
            'menu-item-url'    => home_url( '/' ),
            'menu-item-status' => 'publish',
            'menu-item-type'   => 'custom',
        ) );
        $locations             = get_theme_mod( 'nav_menu_locations' );
        $locations['primary']  = $menu_id;
        set_theme_mod( 'nav_menu_locations', $locations );
    }
}
add_action( 'after_switch_theme', 'realshed_create_default_menu' );

// add_filter( 'nav_menu_css_class', 'realshed_add_current_class', 10, 3 );
// function realshed_add_current_class( $classes, $item, $args ) {
//     $active_classes = array(
//         'current-menu-item', 'current-menu-parent', 'current-menu-ancestor',
//         'current_page_item', 'current_page_parent', 'current_page_ancestor',
//     );
//     $current_post_type = get_post_type();
//     $blog_page_id      = (int) get_option( 'page_for_posts' );

//     $is_custom_context = (
//         ( $current_post_type && ! in_array( $current_post_type, array( 'post', 'page', 'attachment' ) ) ) ||
//         is_tax() ||
//         is_post_type_archive()
//     );

//     if ( $is_custom_context ) {
//         if ( $blog_page_id > 0 && (int) $item->object_id === $blog_page_id ) {
//             $classes = array_diff( $classes, $active_classes );
//         }
//         if ( $item->type === 'post_type_archive' && $item->object === $current_post_type ) {
//             $classes[] = 'current-menu-item';
//         }
//         if ( is_page() && (int) $item->object_id === get_the_ID() ) {
//             $classes[] = 'current-menu-item';
//         }
//     }

//     if ( is_array( $classes ) && array_intersect( $classes, $active_classes ) ) {
//         $classes[] = 'current';
//     }

//     return array_unique( $classes );
// }

add_filter( 'nav_menu_css_class', 'realshed_add_current_class', 10, 3 );
function realshed_add_current_class( $classes, $item, $args ) {
    $active_classes = array(
        'current-menu-item', 'current-menu-parent', 'current-menu-ancestor',
        'current_page_item', 'current_page_parent', 'current_page_ancestor',
    );
    $current_post_type = get_post_type();
    $blog_page_id      = (int) get_option( 'page_for_posts' );

    $is_custom_context = (
        ( $current_post_type && ! in_array( $current_post_type, array( 'post', 'page', 'attachment' ) ) ) ||
        is_tax() ||
        is_post_type_archive()
    );

    if ( $is_custom_context ) {
        if ( $blog_page_id > 0 && (int) $item->object_id === $blog_page_id ) {
            $classes = array_diff( $classes, $active_classes );
        }
        if ( $item->type === 'post_type_archive' && $item->object === $current_post_type ) {
            $classes[] = 'current-menu-item';
        }
        if ( is_page() && (int) $item->object_id === get_the_ID() ) {
            $classes[] = 'current-menu-item';
        }
        // Match menu items whose URL equals the current CPT archive URL
        // Handles page-type links pointing to the archive (e.g. Properties page link)
        if ( is_post_type_archive() && ! empty( $item->url ) ) {
            $archive_url = get_post_type_archive_link( $current_post_type );
            if ( $archive_url && untrailingslashit( $item->url ) === untrailingslashit( $archive_url ) ) {
                $classes[] = 'current-menu-item';
            }
        }
    }

    if ( is_array( $classes ) && array_intersect( $classes, $active_classes ) ) {
        $classes[] = 'current';
    }

    return array_unique( $classes );
}

// =========================================================================
// USER PROFILE FIELDS
// =========================================================================

function realshed_add_user_social_fields( $user_contact ) {
    $user_contact['job_title'] = __( 'Job Title',     'realshed' );
    $user_contact['phone']     = __( 'Phone Number',  'realshed' );
    $user_contact['address']   = __( 'Address',       'realshed' );
    $user_contact['facebook']  = __( 'Facebook URL',  'realshed' );
    $user_contact['twitter']   = __( 'Twitter URL',   'realshed' );
    $user_contact['instagram'] = __( 'Instagram URL', 'realshed' );
    $user_contact['linkedin']  = __( 'LinkedIn URL',  'realshed' );
    return $user_contact;
}
add_filter( 'user_contactmethods', 'realshed_add_user_social_fields' );

// =========================================================================
// COMMENTS
// =========================================================================

add_filter( 'comment_form_default_fields', function( $fields ) {
    $fields['phone']   = '<div class="col-lg-6 col-md-6 col-sm-12 form-group"><input type="text" name="phone" placeholder="' . esc_attr__( 'Phone', 'realshed' ) . '"></div>';
    $fields['subject'] = '<div class="col-lg-6 col-md-6 col-sm-12 form-group"><input type="text" name="subject" placeholder="' . esc_attr__( 'Subject', 'realshed' ) . '"></div>';
    return $fields;
} );

add_action( 'comment_post', function( $comment_id ) {
    if ( isset( $_POST['phone'] ) )   update_comment_meta( $comment_id, 'phone',   sanitize_text_field( $_POST['phone'] ) );
    if ( isset( $_POST['subject'] ) ) update_comment_meta( $comment_id, 'subject', sanitize_text_field( $_POST['subject'] ) );
} );

add_filter( 'comment_text', function( $comment_text, $comment = null ) {
    if ( ! $comment ) return $comment_text;
    $phone   = get_comment_meta( $comment->comment_ID, 'phone',   true );
    $subject = get_comment_meta( $comment->comment_ID, 'subject', true );
    if ( $phone )   $comment_text .= '<p><strong>Phone:</strong> '   . esc_html( $phone )   . '</p>';
    if ( $subject ) $comment_text .= '<p><strong>Subject:</strong> ' . esc_html( $subject ) . '</p>';
    return $comment_text;
}, 10, 2 );

// =========================================================================
// TAG CLOUD WIDGET
// =========================================================================

function realshed_custom_tag_cloud_widget() {
    unregister_widget( 'WP_Widget_Tag_Cloud' );
    register_widget( 'Realshed_Custom_Tag_Cloud_Widget' );
}
add_action( 'widgets_init', 'realshed_custom_tag_cloud_widget', 20 );

class Realshed_Custom_Tag_Cloud_Widget extends WP_Widget_Tag_Cloud {
    public function widget( $args, $instance ) {
        $current_taxonomy = $this->_get_current_taxonomy( $instance );
        $title            = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Tags', 'realshed' );
        $before_widget    = str_replace( 'default-widget ', '', $args['before_widget'] );
        echo $before_widget;
        if ( $title ) echo $args['before_title'] . $title . $args['after_title'];
        $tag_cloud = wp_tag_cloud( array(
            'taxonomy'   => $current_taxonomy,
            'echo'       => false,
            'format'     => 'array',
            'smallest'   => 8,
            'largest'    => 8,
            'show_count' => true,
        ) );
        echo '<div class="widget-content"><ul class="tags-list clearfix">';
        foreach ( (array) $tag_cloud as $tag_link ) echo '<li>' . $tag_link . '</li>';
        echo '</ul></div>' . $args['after_widget'];
    }
}

add_filter( 'dynamic_sidebar_params', 'realshed_force_tags_widget_class' );
function realshed_force_tags_widget_class( $params ) {
    if ( strpos( $params[0]['widget_id'], 'tag_cloud' ) !== false ) {
        $params[0]['before_widget'] = preg_replace( '/class="([^"]*)"/', 'class="$1 tags-widget"', $params[0]['before_widget'] );
    }
    return $params;
}

// =========================================================================
// SVG UPLOAD
// =========================================================================

/**
 * Allow SVG uploads only for administrators.
 *
 * This keeps SVG support available for trusted site owners while avoiding the
 * overhead of bundling an SVG sanitizer library inside the theme. For stricter
 * multi-author sites, use a dedicated SVG sanitization plugin/library later.
 *
 * @param array $mimes Allowed upload MIME types.
 * @return array
 */
function realshed_allow_admin_svg_uploads( $mimes ) {
    if ( current_user_can( 'manage_options' ) ) {
        $mimes['svg'] = 'image/svg+xml';
    }

    return $mimes;
}
add_filter( 'upload_mimes', 'realshed_allow_admin_svg_uploads' );

/**
 * Harden SVG file type detection for administrator SVG uploads.
 *
 * @param array  $data     File type data.
 * @param string $file     Full path to the uploaded file.
 * @param string $filename Uploaded file name.
 * @param array  $mimes    Allowed MIME types.
 * @return array
 */
function realshed_check_admin_svg_filetype( $data, $file, $filename, $mimes ) {
    unset( $file, $mimes );

    $file_ext = strtolower( pathinfo( $filename, PATHINFO_EXTENSION ) );

    if ( 'svg' !== $file_ext ) {
        return $data;
    }

    if ( ! current_user_can( 'manage_options' ) ) {
        return array(
            'ext'             => false,
            'type'            => false,
            'proper_filename' => false,
        );
    }

    $data['ext']  = 'svg';
    $data['type'] = 'image/svg+xml';

    return $data;
}
add_filter( 'wp_check_filetype_and_ext', 'realshed_check_admin_svg_filetype', 10, 4 );

// =========================================================================
// THEME OPTION HELPERS
// =========================================================================

/**
 * Get a Realshed Redux option with a safe fallback.
 *
 * @param string $key     Redux option key.
 * @param mixed  $default Default value.
 * @return mixed
 */
function realshed_get_option( $key, $default = '' ) {
    global $realshed_options;

    if ( isset( $realshed_options[ $key ] ) && '' !== $realshed_options[ $key ] ) {
        return $realshed_options[ $key ];
    }

    return $default;
}

/**
 * Get the active site logo URL.
 *
 * @return string
 */
function realshed_get_logo_url() {
    $logo = realshed_get_option( 'site_logo', array() );

    if ( is_array( $logo ) && ! empty( $logo['url'] ) ) {
        return esc_url_raw( $logo['url'] );
    }

    return REALSHED_IMAGES_URI . '/logo.png';
}

/**
 * Convert a display phone number into a tel: URL.
 *
 * @param string $phone Phone number.
 * @return string
 */
function realshed_get_phone_url( $phone ) {
    $phone = preg_replace( '/[^0-9+]/', '', $phone );

    return $phone ? 'tel:' . $phone : '';
}

// =========================================================================
// TEMPLATE ROUTING
// =========================================================================
//
// Two property pages exist:
//
//   /properties/         → CPT native archive
//                          Loads archive-property.php
//                          Context flag: 'browse'
//                          Layout: controlled by Redux option
//
//   /search-properties/  → Custom rewrite (class-property-cpt.php)
//                          Loads archive-property.php
//                          Context flag: 'search'
//                          Layout: always layout-list-sidebar (fixed)
//
// archive-property.php reads the 'realshed_archive_context' query var
// to know which page it is rendering and behaves accordingly.
//
// =========================================================================

add_filter( 'template_include', 'realshed_property_template_redirect' );
function realshed_property_template_redirect( $template ) {

    $archive_file = REALSHED_THEME_DIR . '/property/archive-property.php';
    $single_file  = REALSHED_THEME_DIR . '/property/single-property.php';

    // --- /search-properties/ page ---
    // Detected via the custom query var set by the rewrite rule.
    if ( get_query_var( 'realshed_page' ) === 'search-properties' ) {
        set_query_var( 'realshed_archive_context', 'search' );
        if ( file_exists( $archive_file ) ) {
            return $archive_file;
        }
    }

    // --- /properties/ browse page and taxonomy pages ---
    if (
        is_post_type_archive( 'property' ) ||
        is_page( 'properties' ) ||
        is_tax( 'property_type' ) ||
        is_tax( 'property_status' ) ||
        is_tax( 'property_location' )
    ) {
        set_query_var( 'realshed_archive_context', 'browse' );
        if ( file_exists( $archive_file ) ) {
            return $archive_file;
        }
    }

    // --- Single property page ---
    if ( is_singular( 'property' ) ) {
        if ( file_exists( $single_file ) ) {
            return $single_file;
        }
    }

    return $template;
}

add_action( 'wp_enqueue_scripts', 'realshed_ajax_localize_script' );
function realshed_ajax_localize_script() {
    if ( is_post_type_archive( 'property' ) || get_query_var( 'realshed_page' ) === 'search-properties' || is_page( 'properties' ) ) {
        wp_localize_script(
            'realshed-custom-script',
            'realshedAjax',
            array(
                'ajaxurl' => esc_url_raw( admin_url( 'admin-ajax.php' ) ),
                'nonce'   => wp_create_nonce( 'realshed_ajax_filter' ),
            )
        );
    }
}

// if ( ! function_exists( 'validate_gravatar' ) ) {
//     function validate_gravatar( $email ) {
//         $hashkey = md5( strtolower( trim( $email ) ) );
//         $uri = 'https://www.gravatar.com/avatar/' . $hashkey . '?d=404';
//         $response = wp_remote_head( $uri, array( 'timeout' => 5 ) );
//         if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) !== 200 ) {
//             return false;
//         }
//         return true;
//     }
// }
// ====================== 1. DISABLE GRAVATAR GLOBALLY + SMART AVATAR ======================

// add_filter( 'get_avatar', 'force_local_avatar', 10, 6 );
// add_filter( 'avatar_defaults', '__return_empty_array' ); // Remove Gravatar options

// function force_local_avatar( $avatar, $id_or_email, $size, $default, $alt, $args ) {

//     $local_fallback = REALSHED_IMAGES_URI . '/icons/profile-picture.png';

//     // Get user ID
//     $user_id = false;
//     if ( is_numeric( $id_or_email ) ) {
//         $user_id = (int) $id_or_email;
//     } elseif ( $id_or_email instanceof WP_User ) {
//         $user_id = $id_or_email->ID;
//     } elseif ( is_string( $id_or_email ) && is_email( $id_or_email ) ) {
//         $user = get_user_by( 'email', $id_or_email );
//         $user_id = $user ? $user->ID : false;
//     }

//     if ( $user_id ) {
//         // Get uploaded profile image (we will use this meta key)
//         $user_avatar = get_user_meta( $user_id, 'custom_profile_image', true );

//         if ( ! empty( $user_avatar ) && filter_var( $user_avatar, FILTER_VALIDATE_URL ) ) {
//             return '<img src="' . esc_url( $user_avatar ) . '"
//                          width="' . esc_attr( $size ) . '"
//                          height="' . esc_attr( $size ) . '"
//                          class="avatar photo rounded-circle"
//                          alt="' . esc_attr( $alt ) . '"
//                          loading="lazy">';
//         }
//     }

//     // Final fallback: Local default image
//     return '<img src="' . esc_url( $local_fallback ) . '"
//                  width="' . esc_attr( $size ) . '"
//                  height="' . esc_attr( $size ) . '"
//                  class="avatar photo rounded-circle"
//                  alt="' . esc_attr( $alt ) . '"
//                  loading="lazy">';
// }

// ====================== DISABLE GRAVATAR + SMART LOCAL AVATAR ======================

add_filter( 'get_avatar', 'force_local_avatar', 10, 6 );
add_filter( 'avatar_defaults', '__return_empty_array' );

function force_local_avatar( $avatar, $id_or_email, $size, $default, $alt, $args ) {

    // Safe way to get theme images folder
    $local_fallback = get_template_directory_uri() . '/assets/images/icons/profile-picture.png';

    // Get user ID
    $user_id = false;
    if ( is_numeric( $id_or_email ) ) {
        $user_id = (int) $id_or_email;
    } elseif ( $id_or_email instanceof WP_User ) {
        $user_id = $id_or_email->ID;
    } elseif ( is_string( $id_or_email ) && is_email( $id_or_email ) ) {
        $user = get_user_by( 'email', $id_or_email );
        $user_id = $user ? $user->ID : false;
    }

    if ( $user_id ) {
        // Check if user has uploaded custom profile image
        $user_avatar = get_user_meta( $user_id, 'custom_profile_image', true );

        if ( ! empty( $user_avatar ) && filter_var( $user_avatar, FILTER_VALIDATE_URL ) ) {
            return '<img src="' . esc_url( $user_avatar ) . '"
                         width="' . esc_attr( $size ) . '"
                         height="' . esc_attr( $size ) . '"
                         class="avatar photo rounded-circle"
                         alt="' . esc_attr( $alt ) . '"
                         loading="lazy">';
        }
    }

    // Final fallback: Local default image
    return '<img src="' . esc_url( $local_fallback ) . '"
                 width="' . esc_attr( $size ) . '"
                 height="' . esc_attr( $size ) . '"
                 class="avatar photo rounded-circle"
                 alt="' . esc_attr( $alt ) . '"
                 loading="lazy">';
}

// END
