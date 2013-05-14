<?php
/**
 * @file wpdk-functions.php
 *
 * Very useful functions for common cases. All that is missing in WordPress
 *
 * ## Overview
 * This file contains the pure inline function without class wrapper. You can use these function directly from code.
 *
 * @brief              Very useful functions for common cases.
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2013-01-21
 * @version            0.9.0
 */

// -----------------------------------------------------------------------------------------------------------------
// has/is zone
// -----------------------------------------------------------------------------------------------------------------

/**
 * Return TRUE if the string NOT contains '', 'false', '0', 'no', 'n', 'off', null.
 *
 * @brief Check for generic boolean
 *
 * @param string $str String to check
 *
 * @return bool
 *
 * @file wpdk-functions.php
 *
 */
function wpdk_is_bool( $str ) {
    return !in_array( strtolower( $str ), array( '', 'false', '0', 'no', 'n', 'off', null ) );
}

/**
 * Return TRUE if a url is a URI
 *
 * @brief Check URI
 *
 * @since 1.0.0.b2
 *
 * @param string $url
 *
 * @return bool
 *
 * @file wpdk-functions.php
 */
function wpdk_is_url( $url ) {
    if ( !empty( $url ) && is_string( $url ) ) {
        return ( '#' === substr( $url, 0, 1 ) || '/' === substr( $url, 0, 1 ) || 'http' === substr( $url, 0, 4 ) ||
            false !== strpos( $url, '?' ) || false !== strpos( $url, '&' ) );
    }
    return false;
}

/**
 * Check if infinity
 *
 * @brief Infinity
 *
 * @param float|string $value Check value
 *
 * @return bool TRUE if $value is equal to INF (php) or WPDKMath::INFINITY
 *
 * @file wpdk-functions.php
 *
 */
function wpdk_is_infinity( $value ) {
    return ( is_infinite( floatval( $value ) ) || ( is_string( $value ) && $value == WPDKMath::INFINITY ) );
}

/**
 * Return TRUE if we are called by Ajax. Used to be sure that we are responding to an HTTPRequest request and that
 * the WordPress define DOING_AJAX is defined.
 *
 * @brief Ajax validation
 *
 * @return bool TRUE if Ajax trusted
 */
function wpdk_is_ajax() {
    if ( defined( 'DOING_AJAX' ) ) {
        return true;
    }
    if ( isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) &&
        strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) == 'xmlhttprequest'
    ) {
        return true;
    } else {
        return false;
    }
}


/**
 * Returns TRUE if the current page is a child of another.
 *
 * @param array|int|string $parent Mixed format for parent page
 *
 * @return bool TRUE if the current page is a child of another
 */
function wpdk_is_child( $parent = '' ) {
    global $post;

    $parent_obj   = get_page( $post->post_parent, ARRAY_A );
    $parent       = (string)$parent;
    $parent_array = (array)$parent;

    if ( $parent_obj && isset( $parent_obj['ID'] ) ) {
        if ( in_array( (string)$parent_obj['ID'], $parent_array ) ) {
            return true;
        } elseif ( in_array( (string)$parent_obj['post_title'], $parent_array ) ) {
            return true;
        } elseif ( in_array( (string)$parent_obj['post_name'], $parent_array ) ) {
            return true;
        } else {
            return false;
        }
    }
    return false;
}

// -----------------------------------------------------------------------------------------------------------------
// Sanitize
// -----------------------------------------------------------------------------------------------------------------

/**
 * Return a possibile function name
 *
 * @brief Sanitize for function name
 * @since 1.0.0.b3
 *
 * @param string $key String key
 *
 * @return mixed
 */
function wpdk_sanitize_function( $key ) {
    return str_replace( '-', '_', sanitize_key( $key ) );
}

/**
 * Return registered image size information
 *
 * @param string $name Image size ID
 *
 * @return bool|array FALSE if not found Image size ID
 */
function wpdk_get_image_size( $name ) {
    global $_wp_additional_image_sizes;
    if ( isset( $_wp_additional_image_sizes[$name] ) ) {
        return $_wp_additional_image_sizes[$name];
    }
    return false;
}

/**
 * Commodity to extends checked() WordPress function with array check
 *
 * @param string|array    $haystack Single value or array
 * @param mixed           $current  (true) The other value to compare if not just true
 * @param bool            $echo     Whether to echo or just return the string
 *
 * @return string html attribute or empty string
 */
function wpdk_checked( $haystack, $current, $echo = true ) {
    if ( is_array( $haystack ) && in_array( $current, $haystack ) ) {
        $current = $haystack = 1;
    }
    return checked( $haystack, $current, $echo );
}

/**
 * Commodity to extends selected() WordPress function with array check
 *
 * @param string|array    $haystack Single value or array
 * @param mixed           $current  (true) The other value to compare if not just true
 * @param bool            $echo     Whether to echo or just return the string
 *
 * @return string html attribute or empty string
 */
function wpdk_selected( $haystack, $current, $echo = true ) {
    if ( is_array( $haystack ) && in_array( $current, $haystack ) ) {
        $current = $haystack = 1;
    }
    return selected( $haystack, $current, $echo );
}

/// @cond private
/*
 * TODO Il recupero dell'id per la compatibilità WPML è del tutto simile a quello usato in wpdk_permalink_page_with_slug
 *       si potrebbe portare fuori visto che sarebbe anche il caso di creare una funzione generica al riguardo, tipo una:
 *       wpdk_page_with_slug() che restituisca appunto l'oggetto da cui recuperare tutto quello che serve.
 */
/// @endcond

/**
 * Get the post content from the slug.
 *
 * @param string $slug             Post slug
 * @param string $post_type        Post type
 * @param string $alternative_slug Alternative slug if post not found
 *
 * @note WPML compatible
 * @sa get_page_by_path()
 *
 * @return string Text/html content post. FALSE not found or error.
 */
function wpdk_content_page_with_slug( $slug, $post_type, $alternative_slug = '' ) {
    global $wpdb;

    $page = get_page_by_path( $slug, OBJECT, $post_type );

    if ( is_null( $page ) ) {
        $page = get_page_by_path( $alternative_slug, OBJECT, $post_type );

        if ( is_null( $page ) ) {
            /* WPML? */
            if ( function_exists( 'icl_object_id' ) ) {
                $sql = <<< SQL
SELECT ID FROM {$wpdb->posts}
WHERE post_name = '{$slug}'
AND post_type = '{$post_type}'
AND post_status = 'publish'
SQL;
                $id  = $wpdb->get_var( $sql );
                $id  = icl_object_id( $id, $post_type, true );
            }
            else {
                return false;
            }
        }
        else {
            $id = $page->ID;
        }

        $page = get_post( $id );
    }

    return apply_filters( "the_content", $page->post_content );
}

/**
 * Get the post permalink from the slug.
 *
 * @param string $slug      Post slug
 * @param string $post_type Post type. Default 'page'
 *
 * @note WPML compatible
 * @sa get_page_by_path()
 *
 * @return mixed|string Return the post permalink trailed. FLASE if not found
 */
function wpdk_permalink_page_with_slug( $slug, $post_type = 'page' ) {
    global $wpdb;

    /* Cerco la pagina. */
    $page = get_page_by_path( $slug, OBJECT, $post_type );

    /* Se non la trovo, prima di restituire null eseguo un controllo per WPML. */
    if ( is_null( $page ) ) {

        /* WPML? */
        if ( function_exists( 'icl_object_id' ) ) {
            $sql = <<< SQL
SELECT ID FROM {$wpdb->posts}
WHERE post_name = '{$slug}'
AND post_type = '{$post_type}'
AND post_status = 'publish'
SQL;
            $id  = $wpdb->get_var( $sql );
            $id  = icl_object_id( $id, $post_type, true );
        }
        else {
            return false;
        }
    }
    else {
        $id = $page->ID;
    }

    $permalink = get_permalink( $id );

    return trailingslashit( $permalink );
}

/**
 * Do a merge/combine between two object tree.
 * If the old version not contains an object or property, that is added.
 * If the old version contains an object or property less in last version, that is deleted.
 *
 * @brief Object delta compare for combine
 *
 * @param mixed $last_version Object tree with new or delete object/value
 * @param mixed $old_version  Current Object tree, loaded from serialize or database for example
 *
 * @return Object the delta Object tree
 */
function wpdk_delta_object( $last_version, $old_version ) {

    $last_version_stack = array();
    $old_version_stack  = array();

    /* Creo un elenco di tutte le proprietà della classe $old_version. Questo elenco mi indica il nome della
     proprietà e il tipo.
    */
    foreach ( $old_version as $key => $value ) {
        $old_version_stack[$key] = $value;
    }

    /* Ora ciclo nella versione recente */
    foreach ( $last_version as $key => $value ) {
        /* Se la precedente versione non contiene la proprietà di quella nuova, la imposto con il valore di default. */
        if ( !isset( $old_version_stack[$key] ) ) {
            $old_version->$key = $value;
        }

        elseif ( empty( $old_version_stack[$key] ) || is_null( $old_version_stack[$key] ) ) {
            $old_version->$key = $value;
        }

        /* La proprietà esiste. */
        else {
            /* Se la proprietà c'è potrebbe essere a sua volta un oggeto, quindi controllo ed eventualmente ciclo su
             questo.
            */
            if ( is_object( $value ) ) {
                wpdk_delta_object( $value, $old_version->$key );
            }
        }
    }

    /* Precedentemente abbiamo controllato per 'mancanze' nella vecchia classe, ora facciamo un controllo speculare
    cioè verifichiamo che la nuova struttura non abbia eliminato qualcosa.
    Come nel caso precedente creo un elenco delle proprietà dell'ultima versione.
    */
    foreach ( $last_version as $key => $value ) {
        $last_version_stack[$key] = $value;
    }

    /* Ora ciclo nella vecchia versione */
    foreach ( $old_version as $key => $value ) {
        /* Se non esiste più questa proprietà... */
        if ( !isset( $last_version_stack[$key] ) ) {
            /* La elimino */
            unset( $old_version->$key );
        }
    }
    /* Ok, $old_version ora è allineata. */
    return $old_version;
}

/**
 * Get the img src value fron content of a post or page.
 *
 * @brief Get an img tag from the content
 *
 * @param int $id_post ID post
 *
 * @return mixed
 */
function wpdk_get_image_in_post_content( $id_post ) {
    ob_start();
    ob_end_clean();
    $output      = preg_match_all( '/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', get_post_field( 'post_content', $id_post ), $matches );
    if( !empty( $matches) && is_array( $matches) && isset( $matches[1][0] ) ) {
        $display_img = $matches[1][0];
        return $display_img;
    }
    return null;
}

/**
 * Function to find image using WP available function get_the_post_thumbnail().
 *
 * @brief Get thumbnail image
 *
 * @param int $id_post ID post
 *
 * @return mixed|null
 */
function wpdk_get_image_from_post_thumbnail( $id_post ) {
    if ( function_exists( 'has_post_thumbnail' ) ) {
        if ( has_post_thumbnail( $id_post ) ) {
            $image = wp_get_attachment_image_src( get_post_thumbnail_id( $id_post ), 'full' );
            return $image[0];
        }
    }
    return null;
}

/**
 * Get src url image from first image attachment post
 *
 * @brief Get image from post attachment
 *
 * @param int $id_post ID post
 *
 * @return array|bool
 */
function wpdk_get_image_from_attachments( $id_post ) {
    if ( function_exists( 'wp_get_attachment_image' ) ) {
        $children = get_children( array(
                                       'post_parent'    => $id_post,
                                       'post_type'      => 'attachment',
                                       'numberposts'    => 1,
                                       'post_status'    => 'inherit',
                                       'post_mime_type' => 'image',
                                       'order'          => 'ASC',
                                       'orderby'        => 'menu_order ASC'
                                  ) );

        if ( empty( $children ) || !is_array( $children ) ) {
            return false;
        }

        $item  = current( $children );

        if( is_object( $item ) && isset( $item->ID ) ) {
            $image = wp_get_attachment_image_src( $item->ID, 'full' );
            return $image[0];
        }
    }
    return false;
}

// -----------------------------------------------------------------------------------------------------------------
// WPDKResult check
// -----------------------------------------------------------------------------------------------------------------

/**
 * Looks at the object and if a WPDKError class. Does not check to see if the parent is also WPDKError or a WPDKResult,
 * so can't inherit both the classes and still use this function.
 *
 * @brief Check whether variable is a WPDK result error.
 *
 * @param mixed $thing Check if unknown variable is WPDKError object.
 *
 * @return bool TRUE, if WPDKError. FALSE, if not WPDKError.
 */
function is_wpdk_error( $thing ) {
    if ( is_object( $thing ) && is_a( $thing, 'WPDKError' ) ) {
        return true;
    }
    return false;
}

/**
 * Looks at the object and if a WPDKWarning class. Does not check to see if the parent is also WPDKWarning or a WPDKResult,
 * so can't inherit both the classes and still use this function.
 *
 * @brief Check whether variable is a WPDK result warning.
 *
 * @param mixed $thing Check if unknown variable is WPDKWarning object.
 *
 * @return bool TRUE, if WPDKWarning. FALSE, if not WPDKWarning.
 */
function is_wpdk_warning( $thing ) {
    if ( is_object( $thing ) && is_a( $thing, 'WPDKWarning' ) ) {
        return true;
    }
    return false;
}

/**
 * Looks at the object and if a WPDKStatus class. Does not check to see if the parent is also WPDKStatus or a WPDKResult,
 * so can't inherit both the classes and still use this function.
 *
 * @brief Check whether variable is a WPDK result status.
 *
 * @param mixed $thing Check if unknown variable is WPDKStatus object.
 *
 * @return bool TRUE, if WPDKStatus. FALSE, if not WPDKStatus.
 */
function is_wpdk_status( $thing ) {
    if ( is_object( $thing ) && is_a( $thing, 'WPDKStatus' ) ) {
        return true;
    }
    return false;
}

// -----------------------------------------------------------------------------------------------------------------
// WPDKResult check
// -----------------------------------------------------------------------------------------------------------------

/**
 * Add a custom hidden (without menu) page in the admin backend area and return the page's hook_suffix.
 *
 * @brief Add a page
 *
 * @param string          $page_slug  The slug name to refer to this hidden pahe by (should be unique)
 * @param string          $page_title The text to be displayed in the title tags of the page when the page is selected
 * @param string          $capability The capability required for this page to be displayed to the user.
 * @param callback|string $function   Optional. The function to be called to output the content for this page.
 * @param string          $hook_head  Optional. Callback when head is loaded
 * @param string          $hook_load  Optional. Callback when loaded
 *
 * @return string
 */
function wpdk_add_page( $page_slug, $page_title, $capability, $function = '', $hook_head = '', $hook_load = '' ) {
    global $admin_page_hooks, $_registered_pages, $_parent_pages;

    $hookname = '';

    if ( !empty( $function ) && current_user_can( $capability ) ) {
        $page_slug                    = plugin_basename( $page_slug );
        $admin_page_hooks[$page_slug] = $page_title;
        $hookname                     = get_plugin_page_hookname( $page_slug, '' );
        if ( !empty( $hookname ) ) {
            add_action( $hookname, $function );
            $_registered_pages[$hookname] = true;
            $_parent_pages[$page_slug]    = false;

            if ( !empty( $hook_head ) ) {
                add_action( 'admin_head-' . $hookname, $hook_head );
            }

            if ( !empty( $hook_load ) ) {
                add_action( 'load-' . $hookname, $hook_load );
            }
        }
    }
    return $hookname;
}

/**
 * Enqueue script for list of page template
 *
 * @brief Enqueue script
 *
 * @param array  $pages          Array of page slug
 * @param string $handle         The script /unique) handle
 * @param bool   $src            Optional. Source URI
 * @param array  $deps           Optional. Array of other handle
 * @param bool   $ver            Optional. Version to avoid cache
 * @param bool   $in_footer      Optional. Load in footer
 */
function wpdk_enqueue_script_page( $pages, $handle, $src = false, $deps = array(), $ver = false, $in_footer = false ) {
  foreach ( $pages as $slug ) {
    if ( is_page_template( $slug ) ) {
      wp_enqueue_script( $handle, $src, $deps, $ver, $in_footer );
      break;
    }
  }
}

/**
 * Enqueue script for list of page template
 *
 * @brief Enqueue script
 *
 * @param array  $page_templates Array of page slug template
 * @param string $handle         The script /unique) handle
 * @param bool   $src            Optional. Source URI
 * @param array  $deps           Optional. Array of other handle
 * @param bool   $ver            Optional. Version to avoid cache
 * @param bool   $in_footer      Optional. Load in footer
 */
function wpdk_enqueue_script_page_teplate( $page_templates, $handle, $src = false, $deps = array(), $ver = false, $in_footer = false ) {
  foreach ( $page_templates as $slug ) {
    if ( is_page_template( $slug ) ) {
      wp_enqueue_script( $handle, $src, $deps, $ver, $in_footer );
      break;
    }
  }
}