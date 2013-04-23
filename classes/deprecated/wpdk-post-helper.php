<?php
/// @cond private

/**
 *
 * Utility dedicate ai post. Attualmente viene usato per effettuare una seire di doping nel backend di WordPress
 * alterando le colonne delle viste dei post, page, media...
 *
 * @class              WPDKPost
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               13/03/12
 * @version            1.0.0
 * @deprecated         Since 0.6.2 - _WPDKPost in progress  Used yet by SmartShop, wpXtreme Server
 *
 */

class WPDKPost {

    /**
     * Crea un Post virtuale onfly
     *
     *
     * @param string $title     Titolo del post
     * @param string $content   Contenuto
     * @param string $post_type Tipo
     *
     * @return object stdClass del Post creato onfly
     */
    static function post( $title, $content, $post_type = 'post' ) {
        global $post, $wp_query;

        $post                = new stdClass();
        $post->ID            = 0;
        $post->post_category = array( '' ); //Add some categories. an array()???
        $post->post_content  = $content; //The full text of the post.
        $post->post_excerpt  = ''; //For all your post excerpt needs.
        $post->post_status   = 'publish'; //Set the status of the new post.
        $post->post_title    = $title; //The title of your post.
        $post->post_type     = $post_type; //Sometimes you might want to post a page.
        // @todo da controllare in quanto emette dei warning
        //$post->post_date          = date_i18n( get_option( 'date_format' ), date( 'Y-m-d' ) ); // Set date as today, using option setting
        $post->comment_status     = 'closed'; // open or closed
        $post->ping_status        = 'closed'; // open or closed

        $wp_query->queried_object = $post;
        $wp_query->post           = $post;
        $wp_query->found_posts    = 1;
        $wp_query->post_count     = 1;
        $wp_query->max_num_pages  = 1;
        $wp_query->is_single      = 1;
        $wp_query->is_404         = false;
        $wp_query->is_posts_page  = 1;
        $wp_query->posts          = array( $post );
        $wp_query->page           = true;
        $wp_query->is_post        = false;

        return $post;
    }

    // -----------------------------------------------------------------------------------------------------------------
    // Init
    // -----------------------------------------------------------------------------------------------------------------

    function init() {

//        /* Extends Posts List Table */
//        add_filter( 'manage_posts_columns', array( __CLASS__, 'manage_posts_columns' ) );
//        add_filter( 'manage_edit-post_sortable_columns', array( __CLASS__, 'manage_posts_sortable_columns' ) );
//        add_action( 'manage_posts_custom_column',  array( __CLASS__, 'manage_posts_custom_column' ), 10, 2);
//
//        /* Extends Pages List Table */
//        add_filter( 'manage_pages_columns', array( __CLASS__, 'manage_posts_columns' ) );
//        add_filter( 'manage_edit-page_sortable_columns', array( __CLASS__, 'manage_posts_sortable_columns' ) );
//        add_action( 'manage_pages_custom_column',  array( __CLASS__, 'manage_posts_custom_column' ), 10, 2);
//
//        /* Extends Media (upload) List Table */
//        add_filter( 'manage_media_columns', array( __CLASS__, 'manage_media_columns' ) );
//        add_filter( 'manage_upload_sortable_columns', array( __CLASS__, 'manage_upload_sortable_columns' ) );
//        add_action( 'manage_media_custom_column',  array( __CLASS__, 'manage_posts_custom_column' ), 10, 2);

    }

    // -----------------------------------------------------------------------------------------------------------------
    // Private
    // -----------------------------------------------------------------------------------------------------------------

    /// Unknown
//    private static function isDopable() {
//        global $typenow;
//
//        /* @todo Aggiungere impostazioni nel backend con la lista dei post type a cui applicare il dope */
//        $dopable = array( 'post', 'page', '' );
//
//        return in_array( $typenow, $dopable );
//    }
//
//    // -----------------------------------------------------------------------------------------------------------------
//    // WordPress hook
//    // -----------------------------------------------------------------------------------------------------------------
//
//    /**
//     * Altera le colonne della List table degli utenti di WordPress
//     *
////     *
//     * @param array $columns Elenco Key value pairs delle colonne
//     *
//     * @return array
//     */
//    public static function manage_posts_columns( $columns ) {
//        if ( !self::isDopable() ) {
//            return $columns;
//        }
//
//        $columns['wpdk_post_internal-publish'] = __( 'Published', WPDK_TEXTDOMAIN );
//
//        unset( $columns['author'] );
//
//        $columns = WPDKArray::insertKeyValuePairs( $columns, 'wpdk_post_internal-author', __( 'Author', WPDK_TEXTDOMAIN ), 2 );
//
//        return $columns;
//    }
//
//    /// Complemento di manage_posts_columns
//    public static function manage_posts_sortable_columns( $columns ) {
//        if ( !self::isDopable() ) {
//            return;
//        }
//
//        $columns = array(
//            'title'                       => 'title',
//            'wpdk_post_internal-author'   => 'author',
//            'parent'                      => 'parent',
//            'comments'                    => 'comment_count',
//            'date'                        => array(
//                'date',
//                true
//            )
//        );
//        return $columns;
//    }
//
//    /**
//     * Contenuto (render) di una colonna
//     *
////     *
//     * @param string $column_name Nome della colonna
//     * @param int    $post_id     Post ID
//     *
//     * @return string
//     */
//    public static function manage_posts_custom_column( $column_name, $post_id ) {
//        if ( !self::isDopable() ) {
//            return $column_name;
//        }
//
//        global $post;
//
//        if ( $column_name == 'wpdk_post_internal-publish' ) {
//
//            $item = array(
//                'type'       => WPDK_FORM_FIELD_TYPE_SWIPE,
//                'name'       => 'wpdk-post-publish',
//                'userdata'   => $post_id,
//                'afterlabel' => '',
//                'value'      => ( get_post_status( $post_id ) == 'publish' ) ? 'on' : 'off'
//            );
//            WPDKForm::htmlSwipe( $item );
//        }
//        elseif ( $column_name == 'wpdk_post_internal-author' ) {
//            echo WPDKUser::gravatar( get_the_author_meta( 'ID' ), 48 );
//            printf( '<br/><a href="%s">%s</a>', esc_url( add_query_arg( array( 'post_type'   => $post->post_type, 'author'      => get_the_author_meta( 'ID' ) ), 'edit.php' ) ), get_the_author() );
//        }
//        elseif ( $column_name == 'wpdk_post_internal-icon' ) {
//            if ( $thumb = wp_get_attachment_image( $post->ID, array( 80, 60 ), true )
//            ) {
//                $url = wp_get_attachment_image_src( $post->ID, 'full' );
//                printf( '<a class="thickbox" title="%s" href="%s">%s</a>', _draft_or_post_title( $post->ID ), $url[0], $thumb );
//            }
//        }
//    }
//
//    /// Media
//    public static function manage_media_columns( $columns ) {
//        if ( !self::isDopable() ) {
//            return $columns;
//        }
//
//        unset( $columns['author'] );
//        unset( $columns['icon'] );
//
//        $columns = WPDKArray::insertKeyValuePairs( $columns, 'wpdk_post_internal-icon', __( 'Icon', WPDK_TEXTDOMAIN ), 1 );
//        $columns = WPDKArray::insertKeyValuePairs( $columns, 'wpdk_post_internal-author', __( 'Author', WPDK_TEXTDOMAIN ), 3 );
//
//        return $columns;
//    }
//
//    /// Aggiunge thumbnail autore
//    public static function manage_upload_sortable_columns( $columns ) {
//        if ( !self::isDopable() ) {
//            return $columns;
//        }
//
//        $columns = array(
//            'title'                       => 'title',
//            'wpdk_post_internal-author'   => 'author',
//            'parent'                      => 'parent',
//            'comments'                    => 'comment_count',
//            'date'                        => array( 'date', true )
//        );
//        return $columns;
//    }


}

/// @endcond
