<?php
if ( ! class_exists( 'WP_List_Table' ) ) {
  require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * An extension of WordPress WP_List_Table class.
 *
 * ## Overview
 * The WPDKListTableViewController class extends the WordPress WP_List_Table class. It add some useful methods to
 * semplify the common procedure. This class is not a true view controller (WPDKViewController) but it is very similar.
 *
 * @class              WPDKListTableViewController
 * @author             =undo= <<info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
 * @date               2014-09-30
 * @version            1.2.2
 *
 * @history            1.2.1 - Several refactor, improvements and removed old and deprecated methods
 * @history            1.2.2 - Added method `print_styles` to conform interface
 *
 */
class WPDKListTableViewController extends WP_List_Table {

  /**
   * A string id for this list table view. This id is used in class HTML markup.
   *
   * @brief A string id for this list table
   *
   * @var string $id
   */
  public $id;

  /**
   * The top head  view controller title
   *
   * @brief View head title
   *
   * @var string $title
   */
  public $title;

  /**
   * The internal view controller for this list table
   *
   * @brief View controller
   *
   * @var WPDKViewController $viewController
   */
  public $viewController;

  /**
   * Name of GET parameter for status. Default `status`
   *
   * @brief      GET id for status
   * @deprecated sice 1.5.16 - Use `wpdk_` args instead
   *
   * @var string $getStatusID
   */
  public $getStatusID;

  /**
   * Internal copy of WP_List_Table args
   *
   * @brief WP_List_Table args
   *
   * @var array $args
   */
  protected $args;

  /**
   * An instance of WPDKListTableModel class
   *
   * @brief Model
   * @since 1.3.0
   *
   * @var WPDKListTableModel $model
   */
  public $model;

  /**
   * Column header
   *
   * @brief Column headers
   *
   * @var array $column_headers
   */
  public $column_headers = array();

  /**
   * The child class should call this constructor from it's own constructor.
   * Create an instance of WPDKListTableViewController class
   *
   * @brief  Constructor
   *
   * @param string $id    List table id
   * @param string $title Title of view controller
   * @param array  $args  Standard WP_List_Table args
   *
   * @return WPDKListTableViewController
   *
   */
  public function __construct( $id, $title, $args )
  {
    // Create an instance of WP_List_Table class
    parent::__construct( $args );

    // @since 1.5.16 - Default WPDK args
    $defaults = array(
      'wpdk_request_status' => 'status',
      'wpdk_default_status' => WPDKDBTableRowStatuses::ALL,
      'wpdk_default_action' => WPDKDBListTableModel::ACTION_EDIT,
    );

    // Set static properties.
    $this->id    = sanitize_key( $id );
    $this->title = $title;
    $this->args  = wp_parse_args( $args, $defaults );

    // Init the internal view controller.
    $this->viewController                = new WPDKViewController( $this->id, $this->title );
    $this->viewController->view->class[] = 'wpdk-list-table-box';

    // Do an action used to get the post data from model
    $action = get_class( $this->model ) . '-listtable-viewcontroller';

    // Filter the query args for redirect after an actions.
    add_filter( 'wpdk_list_table_remove_query_args_redirect', array( $this, 'wpdk_list_table_remove_query_args_redirect' ) );

    // This action must be call one time only
    if ( ! did_action( $action ) ) {

      /**
       * Fires when this view controller is loaded.
       *
       * This action is compose in '{view controller clss name}-listtable-viewcontroller'.
       *
       */
      do_action( $action );
    }

    // Enqueue components
    WPDKUIComponents::init()->enqueue( WPDKUIComponents::LIST_TABLE );

  }

  /**
   * Filter the query args for redirect after an actions.
   *
   * @since 1.5.17
   *
   * @param array $args Optional. Default query args to remove. Default `array()`
   */
  public function wpdk_list_table_remove_query_args_redirect( $args = array() )
  {
    // Sanitize redirect
    $remove = array(
      '_wp_http_referer'      => false,
      'action'                => false,
      'action2'               => false,
      '_wpnonce'              => false,
      $this->args['singular'] => false,
      $this->args['plural']   => false,
      'paged'                 => false,
      '_action_result'        => false,
    );

    $args = array_merge( $args, $remove );

    //WPXtreme::log( $args, '$args filter' );

    return $args;
  }

  // -------------------------------------------------------------------------------------------------------------------
  // Ask information to override to custom
  // -------------------------------------------------------------------------------------------------------------------

  /**
   * Return a key value pairs array with the list of columns
   *
   * @brief Return the list of columns
   *
   * @note  To override
   *
   * @return array
   */
  public function get_columns()
  {
    // Ask to the the model
    if ( isset( $this->model ) && method_exists( $this->model, 'get_columns' ) ) {
      return $this->model->get_columns();
    }

    return array();
  }

  /**
   * Return the sortable columns
   *
   * @brief Sortable columns
   *
   * @return array
   */
  public function get_sortable_columns()
  {
    // Ask to the the model
    if ( isset( $this->model ) && method_exists( $this->model, 'get_sortable_columns' ) ) {
      return $this->model->get_sortable_columns();
    }

    return array();
  }

  /**
   * Return a key value pairs array with statuses supported
   *
   * @brief Statuses
   *
   * @note  To override
   *
   * @return array
   */
  public function get_statuses()
  {
    // Ask to the the model
    if ( isset( $this->model ) && method_exists( $this->model, 'get_statuses' ) ) {
      return $this->model->get_statuses();
    }

    return array();
  }

  /**
   * Return the count of specific status
   *
   * @brief      Count status
   * @deprecated since 1.5.16 - Use `get_count_statuses()` instead
   * @note       To override
   *
   * @param string $status
   *
   * @return int
   */
  public function get_status( $status )
  {

    // Ask to the the model
    if ( isset( $this->model ) && method_exists( $this->model, 'get_status' ) ) {

      _deprecated_function( __CLASS__ . '::' . __FUNCTION__, '1.5.16', 'get_count_statuses()' );

      return $this->model->get_status( $status );
    }

    return false;
  }

  /**
   * Return the array with status key => count.
   *
   * @brief Count status
   * @since 1.5.16
   * @note  To override
   *
   * @return array
   */
  public function get_count_statuses()
  {

    // Ask to the the model
    if ( isset( $this->model ) && method_exists( $this->model, 'get_count_statuses' ) ) {
      return $this->model->get_count_statuses();
    }

    return false;
  }

  /**
   * Return tha array with the action for the current status
   *
   * @brief Action with status
   *
   * @param mixed  $item   The item
   * @param string $status Current status
   *
   * @return array
   */
  public function get_actions_with_status( $item, $status )
  {
    // Ask to the the model
    if ( isset( $this->model ) && method_exists( $this->model, 'get_actions_with_status' ) ) {
      return $this->model->get_actions_with_status( $item, $status );
    }

    return array();
  }

  /**
   * Return the array with the buk action for the combo menu for a status of view
   *
   * @brief Bulk actions
   *
   * @param string $status Current status
   *
   * @return array
   */
  public function get_bulk_actions_with_status( $status )
  {
    // Ask to the the model
    if ( isset( $this->model ) && method_exists( $this->model, 'get_bulk_actions_with_status' ) ) {
      return $this->model->get_bulk_actions_with_status( $status );
    }

    return array();
  }

  // -------------------------------------------------------------------------------------------------------------------
  // Private Utility
  // -------------------------------------------------------------------------------------------------------------------

  /**
   * Return the default status.
   *
   * @brief Default status
   * @since 1.5.16
   *
   * @param string $default Optional. A default status different by `$this->args['wpdk_default_status']`.
   *
   * @return string
   */
  private function current_status( $default = '' )
  {
    // Get the request key
    $request = $this->args['wpdk_request_status'];

    // Set the default
    $default = empty( $default ) ? $this->args['wpdk_default_status'] : $default;

    $current_status = isset( $_REQUEST[ $request ] ) ? $_REQUEST[ $request ] : $default;

    return $current_status;
  }


  // -------------------------------------------------------------------------------------------------------------------
  // WPDKViewController Interface
  // -------------------------------------------------------------------------------------------------------------------

  /**
   * @brief To override
   * @since 1.4.18
   */
  public function load()
  {
  }

  /**
   * Fires when styles are printed for a specific admin page based on $hook_suffix.
   *
   * @since WP 2.6.0
   * @since 1.6.0
   */
  public function admin_print_styles()
  {
    // To override
  }

  /**
   * @brief To override
   * @since 1.4.18
   */
  public function admin_head()
  {
  }

  /**
   * @brief To override
   * @since 1.4.21
   */
  public function _admin_head()
  {
  }

  /**
   * @brief To override
   */
  public static function willLoad()
  {
  }

  /**
   * @brief To override
   */
  public static function didHeadLoad()
  {
  }

  // -------------------------------------------------------------------------------------------------------------------
  // Display
  // -------------------------------------------------------------------------------------------------------------------

  /**
   * This delegate method is called before display views
   *
   * @brief Before views
   * @since 1.5.1
   */
  public function before_views()
  {
    // You can override
  }

  /**
   * This delegate method is called before display the table, inner the form
   *
   * @brief Before display
   * @since 1.5.1
   */
  public function before_display()
  {
    // You can override
  }

  /**
   * Display a search box field
   *
   * @brief Search Box
   * @since 1.5.1
   */
  public function search_box_field()
  {
    // Override
  }

  /**
   * This method override the default WP_List_Table display.
   *
   * @brief Display the list table view
   */
  public function display()
  {
    echo $this->html();
  }

  /**
   * This delegate method is called after display the table, inner the form.
   *
   * @brief After display
   * @since 1.5.1
   */
  public function after_display()
  {
    // You can override
  }

  /**
   * Return the HTML markup for list table view.
   *
   * @brief Get the list table view
   *
   * @note  This is a low level method that create the HTML markup for list table view. You'll notice that the display
   *        method above has an input param for echo/display. For now this method html() is here for backward compatibility.
   *
   * @return string
   */
  public function html()
  {
    // Buffering...
    WPDKHTML::startCompress();

    // Fetch, prepare, sort, and filter our data...
    if ( ! $this->prepare_items() ) : // since 1.5.1 - action before views
    {
      $this->before_views();

      $this->views();
      ?>

      <form id="<?php echo $this->id ?>" class="wpdk-list-table-form" method="get" action="">

      <?php $this->search_box_field() ?>

      <?php
      // Standard input hidden
      $input_hidden = array( 'page', 'post_type', 'orderby', 'order', '_action_result' );

      // TODO think to a filter

      foreach( $input_hidden as $name ) :
        if( isset( $_REQUEST[ $name ] ) ) :

          // TODO Warning!
          $value = is_array( $_REQUEST[ $name ] ) ? esc_attr( $_REQUEST[ $name ][0] ) : esc_attr( $_REQUEST[ $name ] ); ?>
          <input type="hidden" name="<?php echo $name ?>" value="<?php echo $value ?>"/>
        <?php endif; endforeach; ?>

      <?php $this->before_display(); // since 1.5.1 ?>

      <?php parent::display() ?>

      <?php $this->after_display(); // since 1.5.1 ?>

    </form>
    <?php } endif; ?>
    <?php

    // Get the content
    $this->viewController->viewHead->content = WPDKHTML::endCompress();

    // Fires into the the title TAG.
    add_action( 'wpdk_header_view_inner_title', array( $this, 'wpdk_header_view_inner_title' ) );

    return $this->viewController->html();
  }


  /**
   * Fires into the the title TAG.
   *
   * @param WPDKHeaderView $header_view An instance of WPDKHeaderView class.
   */
  public function wpdk_header_view_inner_title( $header_view )
  {
    if ( WPDKDBListTableModel::ACTION_NEW != $this->action() ) {

      $args = array(
        'action' => WPDKDBListTableModel::ACTION_NEW,
        'page'   => $_REQUEST['page'],
      );
      $href = add_query_arg( $args, self_admin_url( 'admin.php' ) );

      $add_new = sprintf( '<a class="wpdk-add-new button button-primary" href="%s">%s</a>', $href, __( 'Add New', WPDK_TEXTDOMAIN ) );

      /**
       * Filter the HTML markup for button 'Add New'
       *
       * @param string                      $add_new The HTML markup for button 'Add New'.
       * @param WPDKListTableViewController $vc      An instance of WPDKListTableViewController class.
       */
      $add_new = apply_filters( 'wpdk_listtable_viewcontroller_add_new', $add_new, $this );
      if ( ! empty( $add_new ) ) {
        echo $add_new;
      }
    }
  }

  // -------------------------------------------------------------------------------------------------------------------
  // Override standard WP_List_Table
  // -------------------------------------------------------------------------------------------------------------------

  /**
   * The itens can be not found for two main reason: the query search has param tha t doesn't match with items, or the
   * items list (or the database query) return an empty list.
   *
   * @brief Called when no items found
   *
   */
  public function no_items()
  {
    // Default message
    printf( __( 'No %s found.', WPDK_TEXTDOMAIN ), $this->title );

    // If in search mode
    // @todo Find a way to determine if we are in 'search' mode or not
    echo '<br/>';

    _e( 'Please, check again your search parameters.', WPDK_TEXTDOMAIN );
  }

  /**
   * Return an array with the HTML markup list of statuses. This method use the $statuses property format as:
   *
   *    $statuses = array(
   *      'all' => array(
   *          'label'     => __( 'All' ),
   *          'count'     => 0
   *      ),
   *
   *      'publish' => array(
   *          'label'     => __( 'Publish' ),
   *          'count'     => 0
   *      )
   *    );
   *
   * @brief Get the top head views statuses
   *
   * @return array
   *
   */
  public function get_views()
  {
    // Prepare return
    $views = array();

    // Get current Status
    $current_status = $this->current_status();

    // @since 1.5.16
    $counts = $this->get_count_statuses();

    // Loop into the statuses
    foreach ( $this->get_statuses() as $key => $status ) {

      // Count the status
      if ( empty( $counts ) ) {
        $count = $this->get_status( $key );
      }
      else {
        // @since 1.5.16
        $count = isset( $counts[ $key ] ) ? $counts[ $key ] : 0;
      }

      if ( ! empty( $count ) ) {

        $current = ( $current_status == $key ) ? 'class="current"' : '';

        // Get referer
        $referer = wpdk_is_ajax() ? $_SERVER['HTTP_REFERER'] : $_SERVER['REQUEST_URI'];

        // Avoid some params
        $args = $this->wpdk_list_table_remove_query_args_redirect( array( $this->args['wpdk_request_status'] => $key ) );

        // href
        $href = add_query_arg( $args, $referer );

        $views[ $key ] = sprintf( '<a %s href="%s">%s <span class="count">(%s)</span></a>', $current, $href, $status, $count );
      }
    }

    return $views;
  }

  /**
   * To override
   *
   * @brief Statuses
   *
   * @param $statuses
   */
  public function statuses( $statuses )
  {
    return $statuses;
  }

  /**
   * Processing data items to view.
   * You can override this method to customize your retrived data.
   *
   * @brief Processing data to view
   *
   * @return bool
   */
  public function prepare_items()
  {
    /**
     * Optional. You can handle your bulk actions however you see fit. In this
     * case, we'll handle them within our package just to keep things clean.
     */
    if ( $this->process_bulk_action( $this->action() ) ) {
      return true;
    }

    // First, lets decide how many records per page to show.
    $id_user = get_current_user_id();

    /**
     * @var WP_Screen $screen
     */
    $screen = get_current_screen();

    $option = $screen->get_option( 'per_page', 'option' );
    if ( ! empty( $option ) ) {
      $per_page = get_user_meta( $id_user, $option, true );
    }

    if ( empty ( $per_page ) || $per_page < 1 ) {
      $per_page = $screen->get_option( 'per_page', 'default' );
      if ( empty( $per_page ) ) {
        $per_page = 10;
      }
    }

    // Columns Header
    $this->column_headers = $this->get_column_info();

    // This is required because some GET params keep in the url
    $remove                 = array( 'action' );
    $_SERVER['REQUEST_URI'] = remove_query_arg( $remove, stripslashes( $_SERVER['REQUEST_URI'] ) );

    // Get data.
    $data = $this->data();

    /**
     * REQUIRED for pagination. Let's figure out what page the user is currently
     * looking at. We'll need this later, so you should always include it in
     * your own package classes.
     */
    $current_page = $this->get_pagenum();

    /**
     * REQUIRED for pagination. Let's check how many items are in our data array.
     * In real-world use, this would be the total number of items in your database,
     * without filtering. We'll need this later, so you should always include it
     * in your own package classes.
     */
    $total_items = apply_filters( 'wpdk_listtable_total_items_' . $this->id, count( $data ) );

    /**
     * The WP_List_Table class does not handle pagination for us, so we need
     * to ensure that the data is trimmed to only the current page. We can use
     * array_slice() to
     */
    $slice_data = array_slice( $data, ( ( $current_page - 1 ) * $per_page ), $per_page );

    /**
     * REQUIRED. Now we can add our *sorted* data to the items property, where
     * it can be used by the rest of the class.
     */
    $this->items = apply_filters( 'wpdk_listtable_items_' . $this->id, $slice_data, $data );

    /**
     * REQUIRED. We also have to register our pagination options & calculations.
     */
    $args = array(
      'total_items' => $total_items,
      'per_page'    => $per_page,
      'total_pages' => ceil( $total_items / $per_page )
    );
    $this->set_pagination_args( $args );

    return false;
  }

  // -------------------------------------------------------------------------------------------------------------------
  // To override
  // -------------------------------------------------------------------------------------------------------------------

  /**
   * Return the items data list. This method will be over-ridden in a sub-class.
   *
   * @brief Return the items data list
   *
   * @return array
   */
  public function data()
  {
    if ( ! empty( $this->model ) && method_exists( $this->model, 'select' ) ) {
      // TODO Replave $_REQUEST with $_GET ?
      return $this->model->select( $_REQUEST );
    }

    return array();
  }

  /**
   * Display a cel content for a column.
   *
   * @brief Process the content of a column
   *
   * @note  I suggest to you to override this method
   *
   * @param array  $item        The single item
   * @param string $column_name Column name
   *
   * @return mixed
   */
  public function column_default( $item, $column_name )
  {
    switch ( $column_name ) {
      default:
        return print_r( $item, true );
    }
  }

  /**
   * Return the HTML markup in order to display the content of column with row actions.
   * This actions are usualy: Edit | Delete | Set Trash | etc...
   *
   * @brief Row actions
   *
   * @param array  $item        The single item
   * @param string $content     The content to display and link.
   * @param string $item_status Optional. Overwrite the view status for item in a specific status.
   * @param string $description Optional. Additional description not linked.
   *
   * @return string
   */
  public function actions_column( $item, $content, $item_status = '', $description = '' )
  {
    // Get the current view status
    $current_status = empty( $item_status ) ? $this->current_status() : $item_status;

    // Prepare the url for description. See $action param.
    $url_description = sprintf( '<strong>%s</strong>%s', $content, $description );

    // Prepare stack for build rows action
    $stack = array();

    // Loop into the actions for current status
    foreach ( $this->get_actions_with_status( $item, $current_status ) as $action => $label ) {
      if ( ! empty( $action ) ) {

        // Get referer
        $referer = wpdk_is_ajax() ? $_SERVER['HTTP_REFERER'] : $_SERVER['REQUEST_URI'];

        // Remove
        $referer = remove_query_arg( '_action_result', $referer );

        $args = array(
          'action'                => $action,
          $this->args['singular'] => $item[ $this->args['singular'] ],
          '_wpnonce'              => wp_create_nonce( 'bulk-' . $this->args['plural'] ),
          '_wp_http_referer'      => urlencode( wp_unslash( $referer ) ),
        );

        // href
        $href = add_query_arg( $args, $referer );

        /**
         * Filter the url for an action.
         *
         * The dynamic portion of the hook name, $action, refers to the action as 'action_edit', 'action_trash', etc...
         *
         * @param string $href Current url with action.
         * @param array  $args Array argument
         *
         */

        $href = apply_filters( 'wpdk_listtable_action_' . $action, $href, $args );

        if ( ! empty( $href ) && $this->args['wpdk_default_action'] == $action ) {
          $url_description = sprintf( '<a href="%s"><strong>%s</strong></a>%s', $href, $content, $description );
        }

        $stack[ $action ] = sprintf( '<a href="%s">%s</a>', $href, $label );
      }
    }

    return sprintf( '%s %s', $url_description, $this->row_actions( $stack ) );
  }

  /**
   * Return the HTML markup for the checkbox element used for multiple selections.
   *
   * @brief Standard checkbox for group actions
   * @note  Please, do not override this method directly, but use `column_checbox` instead
   *
   * @param array $item The single item from items
   *
   * @return string
   */
  public function column_cb( $item )
  {
    if ( ! $this->column_checkbox( $item ) ) {
      return;
    }

    $name  = $this->args['singular'];
    $value = $item[ $name ];

    return sprintf( '<input type="checkbox" name="%s[]" value="%s" />', $name, $value );
  }

  /**
   * Return TRUE to display the checkbox, FALSE otherwise
   *
   * @brief Standard checkbox for group actions
   * @since 1.5.5
   * @note  You can override this method for your custom view. This method is called only there is a column named "cb"
   *
   * @param array $item The single item from items
   *
   * @return bool
   */
  public function column_checkbox( $item )
  {
    return true;
  }

  /**
   * Return an array with the list of bulk actions used in combo menu select.
   *
   * @brief Get the bulk actions
   *
   * @return array
   */
  public function get_bulk_actions()
  {
    return $this->get_bulk_actions_with_status( $this->current_status() );
  }

  // -------------------------------------------------------------------------------------------------------------------
  // Bulk and single actions
  // -------------------------------------------------------------------------------------------------------------------

  /**
   * Return TRUE to stop the display of list item table, FALSE otherwise. Default return FALSE if you do not override.
   *
   * You can override this method to process the action request sent from list table.
   * You can processing bulk and single action. This method must return a boolean in order to re-processed the items
   * list view.
   *
   * @brief Process the bulk actions and standard actions
   *
   * @return bool
   */
  public function process_bulk_action()
  {
    // Process the action result
    $action_result = $this->action_result();

    if ( is_object( $action_result ) ) {

      // Get content
      $content = $action_result->data;

      // Sanitize content
      if( ! is_string( $content ) ) {
        WPDKHTML::startCompress();
        var_dump( $action_result->data );
        $content = WPDKHTML::endCompress();
      }

      // Alert
      $alert = new WPDKUIAlert( false, $content, WPDKUIAlertType::WARNING, $action_result->message );
      $alert->display();
    }
    elseif ( ! empty( $action_result ) ) {
      $alert = new WPDKUIAlert( false, __( 'Operation successfully!', WPDK_TEXTDOMAIN ), WPDKUIAlertType::SUCCESS, __( 'Information', WPDK_TEXTDOMAIN ) );
      $alert->display();
    }

    return false;
  }

  // -------------------------------------------------------------------------------------------------------------------
  // Utility for Bulk actions
  // -------------------------------------------------------------------------------------------------------------------

  /**
   * Utility for build a right actions list when the list table is showing items.
   *
   * @brief      Return the only used action
   *
   * @param array  $args
   * @param string $status
   *
   * @deprecated Use the new engine subclass - used by SamrtShop
   *
   * @return mixed
   */
  public static function actions( $args, $status )
  {

    $id       = key( $args );
    $id_value = $args[ $id ];
    $actions  = array();

    foreach ( $args['actions'] as $key => $label ) {
      $args            = array( 'action' => $key, $id => $id_value );
      $href            = add_query_arg( $args );
      $actions[ $key ] = sprintf( '<a href="%s">%s</a>', $href, $label );
    }

    if ( empty( $status ) || $status != 'trash' ) {
      unset( $actions['untrash'] );
      unset( $actions['delete'] );
    }
    else if ( $status == 'trash' ) {
      unset( $actions['edit'] );
      unset( $actions['trash'] );
    }

    return $actions;
  }

  /**
   * Get the current action selected from the bulk actions dropdown.
   * Return the action name or FALSE if no action was selected.
   *
   * @brief Action
   * @since 1.5.1
   * @deprecated - not used
   *
   * @return string|bool
   */
  public function current_action()
  {
    return isset( $_REQUEST['_action'] ) ? $_REQUEST['_action'] : parent::current_action();
  }

  /**
   * Return the action result.
   *
   * @brief Action result
   *
   * @return bool|WP_Error
   */
  public function action_result()
  {
    $action_result = isset( $_REQUEST['_action_result'] ) ? $_REQUEST['_action_result'] : false;

    if( empty( $action_result ) || "1" === $action_result ) {
      return (bool)$action_result;
    }

    return json_decode( stripslashes( urldecode( $action_result ) ) );
  }

  /**
   * Return the current action or FALSE if no action defined.
   *
   * @brief Current action
   *
   * @return bool|string
   */
  public function action()
  {
    $action = false;

    if ( isset( $_REQUEST['_action'] ) ) {
      $action = $_REQUEST['_action'];
    }
    elseif ( isset( $_REQUEST['action'] ) && -1 != $_REQUEST['action'] ) {
      $action = $_REQUEST['action'];
    }
    elseif ( isset( $_REQUEST['action2'] ) && -1 != $_REQUEST['action2'] ) {
      $action = $_REQUEST['action2'];
    }

    return $action;
  }
}

/**
 * Interface for List Table Model.
 *
 * @interface       IWPDKListTableModel
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
 * @date            2014-09-12
 * @version         1.0.1
 * @since           1.5.2
 *
 * @history         1.0.1 - Added `get_count_statuses()` and deprecated `get_status()`
 */
interface IWPDKListTableModel {

  /**
   * Create an instance of WPDKListTableModel class
   *
   * @brief Construct
   *
   * @return WPDKListTableModel
   */
  public function __construct();

  /**
   * Return a key value pairs array with the list of columns
   *
   * @brief Return the list of columns
   *
   * @return array
   */
  public function get_columns();

  /**
   * Return a key value pairs array with statuses supported
   *
   * @brief Statuses
   *
   * @return array
   */
  public function get_statuses();

  /**
   * Return a key value pairs array with status key => count.
   *
   * @brief Counts
   * @since 1.5.16
   *
   * @return array
   */
  public function get_count_statuses();

  /**
   * Return tha array with the row actions for the current status.
   *
   * @brief Row actions with status
   *
   * @param array $item   The item
   * @param array $status Describe one or more status of single item
   *
   * @return array
   */
  public function get_actions_with_status( $item, $status );

  /**
   * Return the array with the buk action for the combo menu for a status of view
   *
   * @brief Bulk actions
   *
   * @param string $status Current status. Usually this is the status in the URI, when user select 'All', 'Publish', etc...
   *
   * @return array
   */
  public function get_bulk_actions_with_status( $status );

  /**
   * Get the current action selected from the bulk actions dropdown.
   *
   * @brief Current action
   *
   * @return string|bool The action name or False if no action was selected
   */
  public function current_action( $nonce = '' );

  /**
   * Process actions
   *
   * @brief Process actions
   * @since 1.4.21
   *
   */
  public function process_bulk_action();

  // -------------------------------------------------------------------------------------------------------------------
  // CRUD
  // -------------------------------------------------------------------------------------------------------------------

  /**
   * Return the items array. This is an array of key value pairs array
   *
   * @brief Items
   */
  public function select();

}

/**
 * This is a generic model to make easy a WPDKListTableViewController
 *
 * @class           WPDKListTableModel
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
 * @date            2014-09-12
 * @version         1.0.1
 * @since           1.4.13
 *
 * @history         1.0.1 - Added `get_count_statuses()` and deprecated `get_status()`
 *
 */
class WPDKListTableModel implements IWPDKListTableModel {

  /**
   * Used for check the action and bulk action results.
   *
   * @brief Action result
   *
   * @var bool $action_result
   */
  public $action_result = false;

  /**
   * Create an instance of WPDKListTableModel class
   *
   * @brief Construct
   *
   * @return WPDKListTableModel
   */
  public function __construct()
  {
    // Add action to get the post data
    $action = get_class( $this ) . '-listtable-viewcontroller';

    // This action is documented in classes/ui/wpdk-listtable-viewcontroller.php
    add_action( $action, array( $this, 'process_bulk_action' ) );

  }

  /**
   * Return a key value pairs array with the list of columns
   *
   * @brief Return the list of columns
   *
   * @return array
   */
  public function get_columns()
  {
    return array();
  }

  /**
   * Return the sortable columns
   *
   * @brief Sortable columns
   *
   * @return array
   */
  public function get_sortable_columns()
  {
    return array();
  }

  /**
   * Return a key value pairs array with statuses supported
   *
   * @brief Statuses
   *
   * @return array
   */
  public function get_statuses()
  {
    // Default return the common statuses
    return WPDKDBTableRowStatuses::statuses();
  }

  /**
   * Return a key value pairs array with statuses icons glyph
   *
   * @brief Icons
   *
   * @return array
   */
  public function get_icon_statuses()
  {
    // Default return the common statuses
    return WPDKDBTableRowStatuses::icon_statuses();
  }

  /**
   * Return a key value pairs array with status key => count.
   *
   * @brief Counts
   * @since 1.5.16
   *
   * @return array
   */
  public function get_count_statuses()
  {
    return array();
  }

  /**
   * Return tha array with the row actions for the current status.
   *
   * @brief Row action with status
   *
   * @param array $item   The item
   * @param array $status Describe one or more status of single item
   *
   * @return array
   */
  public function get_actions_with_status( $item, $status )
  {
    return array();
  }

  /**
   * Return the array with the buk action for the combo menu for a status of view
   *
   * @brief Bulk actions
   *
   * @param string $status Current status. Usually this is the status in the URI, when user select 'All', 'Publish', etc...
   *
   * @return array
   */
  public function get_bulk_actions_with_status( $status )
  {
    return array();
  }

  /**
   * Get the current action selected from the bulk actions dropdown.
   *
   * @brief Current action
   *
   * @return string|bool The action name or False if no action was selected
   */
  public function current_action( $nonce = '' )
  {
    // Ajax
    if ( wpdk_is_ajax() ) {
      return false;
    }

    // Action
    $action = false;

    if ( isset( $_REQUEST['action'] ) && -1 != $_REQUEST['action'] ) {
      $action = $_REQUEST['action'];
    }
    elseif ( isset( $_REQUEST['action2'] ) && -1 != $_REQUEST['action2'] ) {
      $action = $_REQUEST['action2'];
    }

    // Nonce
    if ( ! empty( $nonce ) && ! empty( $action ) && isset( $_REQUEST['_wpnonce'] ) ) {
      if ( wp_verify_nonce( $_REQUEST['_wpnonce'], 'bulk-' . $nonce ) ) {
        return $action;
      }
    }

    return $action;
  }

  /**
   * Set the action result.
   *
   * @since 1.6.0
   *
   * @param bool|WP_Error $result A result from an "action".
   */
  public function action_result( $result )
  {
    if ( is_wp_error( $result ) ) {
      $error               = array(
        'message' => $result->get_error_message(),
        'data'    => $result->get_error_data()
      );
      $this->action_result = urlencode( json_encode( $error ) );

    }
    else {
      $this->action_result = 1;
    }
  }

  /**
   * Process actions. Override when you need to process actions before wp is loaded.
   *
   * @brief Process actions
   * @since 1.4.21
   *
   */
  public function process_bulk_action()
  {
    // Get current action.
    $action = $this->current_action();

    // Avoid redirect for these actions
    $actions = array( WPDKDBListTableModel::ACTION_NEW, WPDKDBListTableModel::ACTION_EDIT );

    // TODO think to filter

    if( $action && in_array( $action, $actions ) ) {
      return;
    }

    /**
     * Filter the query args for redirect after an actions.
     *
     * @since 1.5.17
     *
     * @param array $args Optional. Default query args to remove. Default `array()`
     */
    $args = apply_filters( 'wpdk_list_table_remove_query_args_redirect', array() );

    // Set the action result
    $args['_action_result'] = $this->action_result;

    $reditect = add_query_arg( $args, $_SERVER['REQUEST_URI'] );

    if ( $action ) {
      wp_safe_redirect( $reditect );
      exit();
    }

  }

  // -------------------------------------------------------------------------------------------------------------------
  // CRUD
  // -------------------------------------------------------------------------------------------------------------------

  /**
   * Return the items array. This is an array of key value pairs array
   *
   * @brief Items
   */
  public function select()
  {
    die( __METHOD__ . ' must be override in your subclass' );
  }

}