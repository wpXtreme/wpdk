<?php
if ( !class_exists( 'WP_List_Table' ) ) {
  require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * An extension of WordPress WP_List_Table class.
 *
 * ## Overview
 * The WPDKListTableViewController class extends the WordPress WP_List_Table class. It add some useful methods to semplify
 * the common procedure.
 * This class is not a true view controller (WPDKViewController) but it is very similar.
 *
 * @class              WPDKListTableViewController
 * @author             =undo= <<info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2014-03-01
 * @version            1.1.0
 *
 */
class WPDKListTableViewController extends WP_List_Table {

  /**
   * @brief Default status key
   */
  const GET_STATUS = 'status';

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
   * @brief GET id for status
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
  protected  $args;

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

    // Set static properties.
    $this->id          = sanitize_key( $id );
    $this->title       = $title;
    $this->args        = $args;
    $this->getStatusID = self::GET_STATUS;

    // Init the internal view controller.
    $this->viewController                = new WPDKViewController( $this->id, $this->title );
    $this->viewController->view->class[] = 'wpdk-list-table-box';

    // Do an action used to get the post data from model
    $action = get_class( $this->model ) . '-listtable-viewcontroller';

    // This action must be call one time only
    if ( !did_action( $action ) ) {
      do_action( $action );
    }
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
    if( isset( $this->model ) && method_exists( $this->model, 'get_columns') ) {
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
    if( isset( $this->model ) && method_exists( $this->model, 'get_sortable_columns') ) {
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
    if( isset( $this->model ) && method_exists( $this->model, 'get_statuses') ) {
      return $this->model->get_statuses();
    }

    return array();
  }

  /**
   * Return the count of specific status
   *
   * @brief Count status
   * @note  To override
   *
   * @param string $status
   *
   * @return int
   */
  public function get_status( $status )
  {

    // Ask to the the model
    if( isset( $this->model ) && method_exists( $this->model, 'get_status') ) {
      return $this->model->get_status( $status );
    }

    return;
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
    if( isset( $this->model ) && method_exists( $this->model, 'get_actions_with_status') ) {
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
    if( isset( $this->model ) && method_exists( $this->model, 'get_bulk_actions_with_status') ) {
      return $this->model->get_bulk_actions_with_status( $status );
    }

    return array();
  }

  // -------------------------------------------------------------------------------------------------------------------
  // Private Utility
  // -------------------------------------------------------------------------------------------------------------------

  /**
   * Return the default status defined from return array by get_statuses() method.
   *
   *     array(
   *       'all'      => __( 'All' ),
   *       'active'   => __( 'Active' ),
   *       'inactive' => __( 'Inactive' ),
   *     );
   *
   * Default status is `all`
   *
   *     array(
   *       'all'      => __( 'All' ),
   *       'active'   => array( __( 'Active' ), true ),
   *       'inactive' => __( 'Inactive' ),
   *     );
   *
   * Default status is `active`
   *
   * @brief Default status
   *
   * @return string
   */
  private function _defaultStatus()
  {
    $default_status = '';
    $statuses       = $this->get_statuses();
    foreach ( $statuses as $status ) {
      if ( is_array( $status ) && true === $status[1] ) {
        $default_status = $status[0];
        break;
      }
      elseif ( empty( $default_status ) ) {
        $default_status = $status;
        break;
      }
    }
    return $default_status;
  }

  /**
   * Return the default sortable column name or empty when no column found.
   *
   * @brief Default sortable column name
   *
   * @return string
   */
  private function _defaultSortableColumn()
  {
    $sortable_columns = $this->get_sortable_columns();
    if ( !empty( $sortable_columns ) ) {
      foreach ( $sortable_columns as $column_name => $value ) {
        if ( true === $value[1] ) {
          return $column_name;
        }
      }
    }
    return false;
  }

  /**
   * Return the current status by default GET status id and get_statuses() override method.
   *
   * @brief Current Status
   *
   * @return string
   */
  private function _currentViewStatus()
  {
    $current_status = isset( $_REQUEST[$this->getStatusID] ) ? $_REQUEST[$this->getStatusID] : $this->_defaultStatus();
    return $current_status;
  }

  // -------------------------------------------------------------------------------------------------------------------
  // Utility for select
  // -------------------------------------------------------------------------------------------------------------------

  /**
   * Return a SQL string with default column name and order for "ORDER BY column_anme ASC"
   *
   * @brief Order for SQL
   *
   * @param string $default_order Optional. Default order if not set
   *
   * @return string
   */
  private function _SQLDefaultOrder( $default_order = 'ASC' )
  {
    $column_name = $this->_defaultSortableColumn();
    $order_by    = isset( $_GET['order_by'] ) ? $_GET['order_by'] : $column_name;
    $order       = isset( $_GET['order'] ) ? $_GET['order'] : $default_order;
    return sprintf( '%s %s', $order_by, $order );
  }

  /**
   * Utility to build the WHERE condiction on status field. If status is 'all' no where is set.
   *
   * @brief Status
   *
   * @return string
   */
  private function _SQLStatus()
  {
    $status_field   = $this->getStatusID;
    $current_status = $this->_currentViewStatus();

    return sprintf( ' AND %s = "%s"', $status_field, $current_status );
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
    if ( !$this->prepare_items() ) :

      // since 1.5.1 - action before views
      $this->before_views();

      $this->views();
      ?>

      <form id="<?php echo $this->id ?>" class="wpdk-list-table-form" method="get" action="">

      <?php $this->search_box_field() ?>

      <?php echo $this->html_filters() ?>

      <?php do_action( 'wpdk_list_table_form', $this ); // @deprecated action since 1.5.1 - use 'before_display()' instead ?>

      <?php unset( $_REQUEST['action'] ); ?>
      <?php $_SERVER['REQUEST_URI'] = isset( $_REQUEST['_wp_http_referer'] ) ? $_REQUEST['_wp_http_referer'] : $_SERVER['REQUEST_URI'] ?>

      <?php
      $filters = $this->get_filters();
      $filter_args = array();
      foreach ( $filters as $key => $value ) {
        if ( isset( $_REQUEST[ $key ] ) && !empty( $_REQUEST[ $key ] )) {
          $filter_args[ $key ] = urlencode( $_REQUEST[ $key ] );
        }
      }
      $_SERVER['REQUEST_URI'] = add_query_arg( $filter_args, $_SERVER['REQUEST_URI'] );
      ?>

      <?php $this->before_display(); // since 1.5.1 ?>

        <?php parent::display() ?>

      <?php $this->after_display(); // since 1.5.1 ?>

    </form>
    <?php endif; ?>
    <?php

    // Get the content
    $this->viewController->viewHead->content = WPDKHTML::endCompress();

    add_action( 'wpdk_header_view_title_did_appear', array( $this, 'wpdk_header_view_title_did_appear' ) );

    return $this->viewController->html();
  }

  /**
   * Return a set of registered filters
   *
   * @brief Brief
   */
  protected function get_filters()
  {
    $standard_filters = array(
      'page'      => array(),
      'post_type' => array(),
      'orderby'   => array(),
      'order'     => array(),
    );

    if ( !empty( $this->model ) && method_exists( $this->model, 'get_filters' ) ) {
      $standard_filters = array_merge( $standard_filters, (array)$this->model->get_filters() );
    }

    return $standard_filters;
  }

  /**
   * Return a set of input hidden fields for registered filters
   *
   * @brief Brief
   *
   * @return string
   */
  protected function html_filters()
  {
    WPDKHTML::startCompress();
    foreach ( $this->get_filters() as $request => $value ) {
      if ( isset( $_REQUEST[ $request ] ) && !empty( $_REQUEST[ $request ] ) ) :
        ?><input type="hidden" name="<?php echo $request ?>" value="<?php echo urlencode( $_REQUEST[ $request ] ) ?>" /><?php
      endif;
    }

    return WPDKHTML::endCompress();
  }



  /**
   * Called when the title has been drawed
   *
   * @brief Filter on title
   *
   * @param WPDKHeaderView $view The header view
   */
  public function wpdk_header_view_title_did_appear( $view )
  {
    if ( 'new' != $this->current_action() ) {
      $add_new = sprintf( '<a class="wpdk-add-new button button-primary" href="%s">%s</a>', $this->urlAddNew(), __( 'Add New', WPDK_TEXTDOMAIN ) );
      $add_new = apply_filters( 'wpdk_listtable_viewcontroller_add_new', $add_new, $this );
      if ( !empty( $add_new ) ) {
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
    $views         = array();

    // Status
    $get_status_id = $this->getStatusID;
    $filter_status = isset( $_GET[$get_status_id] ) ? $_GET[$get_status_id] : $this->_defaultStatus();

    foreach ( $this->get_statuses() as $key => $status ) {

      // See _defaultStatus() for detail for this array.
      $status = is_array( $status ) ? $status[0] : $status;

      // Recompute!
      $count = $this->get_status( $key );
      if ( !empty( $count ) ) {

        $current = ( $filter_status == $key ) ? 'class="current"' : '';

        // Clear URI
        $_SERVER['REQUEST_URI'] = remove_query_arg( array(
          '_action',
          '_action_result'
        ), wpdk_is_ajax() ? $_SERVER['HTTP_REFERER'] : $_SERVER['REQUEST_URI'] );

        $args = array(
          $get_status_id           => $key,
          'paged'                  => false,
          'action'                 => false,
          '_action'                => false,
          '_action_result'         => false,
          $this->_args['singular'] => false
        );

        $href = add_query_arg( $args, $_SERVER['REQUEST_URI'] );

        $views[$key] = sprintf( '<a %s href="%s">%s <span class="count">(%s)</span></a>', $current, $href, $status, $count );
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
    if( !empty( $option ) ) {
      $per_page = get_user_meta( $id_user, $option, true );
    }

    if ( empty ( $per_page ) || $per_page < 1 ) {
      $per_page = $screen->get_option( 'per_page', 'default' );
      if( empty( $per_page ) ) {
        $per_page = 10;
      }
    }

    // Columns Header
    $this->column_headers = $this->get_column_info();

    // This is required because some GET params keep in the url
    $remove  = array( 'action' );
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
    if( !empty( $this->model ) && method_exists( $this->model, 'select' ) ) {
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
   * The list view has a special comumn named ID. This is the primary column where are displayed some action when the
   * mouse is over the cel. This actions are usualy: Edit | Delete | Set Trash | etc...
   * This method return the HTML markup with description item and the list of actions.
   *
   * @brief Process the content of ID column
   *
   * @param array  $item           The single item
   * @param string $column_name    Optional. The column action id. Default 'description'
   * @param string $item_status    Optional. Overwrite the view status for item in a specific status
   * @param string $custom_content Optional. Useful tuo override `$custom_content`
   *
   * @note  You can override this method for your costum view. This method is called only there is a column named "id"
   *
   * @return string
   */
  public function actions_column( $item, $column_name = 'description', $item_status = '', $custom_content = '' )
  {
    // Get the current view status
    $status = $this->_currentViewStatus();

    if ( !empty( $item_status ) ) {
      $status = $item_status;
    }

    $stack = array();
    foreach ( $this->get_actions_with_status( $item, $status ) as $action => $label ) {
      if ( !empty( $action ) ) {

        // Clear URI
        $_SERVER['REQUEST_URI'] = remove_query_arg( array(
          '_action',
          '_action_result'
        ), $_SERVER['REQUEST_URI'] );

        $args = array(
          'action'                => $action,
          $this->args['singular'] => $item[ $this->args['singular'] ],
          '_wpnonce'              => wp_create_nonce( 'bulk-' . $this->args['plural'] ),
          '_wp_http_referer'      => esc_attr( wp_unslash( $_SERVER['REQUEST_URI'] ) ),
          '_action'               => false,
          '_action_result'        => false,
        );

        // url
        $url = add_query_arg( $args, $_SERVER['REQUEST_URI'] );

        $href             = apply_filters( 'wpdk_listtable_action_' . $action, $url, $args );
        $stack[ $action ] = sprintf( '<a href="%s">%s</a>', $href, $label );
      }
    }

    $description = empty( $custom_content ) ? sprintf( '<strong>%s</strong>', $item[ $column_name ] ) : $custom_content;

    return sprintf( '%s %s', $description, $this->row_actions( $stack ) );
  }

  /**
   * Return the HTML markup for the checkbox element used for multiple selections.
   *
   * @brief Standard checkbox for group actions
   *
   * @note  You can override this method for your custom view. This method is called only there is a column named "cb"
   *
   * @param $item
   *
   * @return string
   */
  public function column_cb( $item )
  {
    $name  = $this->args['singular'];
    $value = $item[ $name ];

    return sprintf( '<input type="checkbox" name="%s[]" value="%s" />', $name, $value );
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
    // Get the current status, could be empty
    $current_status = isset( $_REQUEST[$this->getStatusID] ) ? $_REQUEST[$this->getStatusID] : $this->_defaultStatus();
    return $this->get_bulk_actions_with_status( $current_status );
  }

  // -------------------------------------------------------------------------------------------------------------------
  // Bulk and single actions
  // -------------------------------------------------------------------------------------------------------------------

  /**
   * This method is to override and you can use it to processed the action request sent from list table.
   * You can processed bulk and single action. This method must return a boolean in order to re-processed the items
   * list view.
   *
   * @brief Process the bulk actions and standard actions
   *
   * @return bool TRUE to stop display the list view, FALSE to display the list.
   */
  public function process_bulk_action()
  {
    die( __METHOD__ . ' must be over-ridden in a sub-class.' );
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
    $id_value = $args[$id];
    $actions  = array();

    foreach ( $args['actions'] as $key => $label ) {
      $args          = array(
        'action' => $key,
        $id      => $id_value
      );
      $href          = add_query_arg( $args );
      $actions[$key] = sprintf( '<a href="%s">%s</a>', $href, $label );
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
   *
 	 * @return string|bool
 	 */
  public function current_action()
  {
    return isset( $_REQUEST['_action'] ) ? $_REQUEST['_action'] : parent::current_action();
  }

  /**
   * Return the action result
   *
   * @brief Action result
   *
   * @return bool
   */
  public function action_result()
  {
    return isset( $_REQUEST['_action_result'] ) ? $_REQUEST['_action_result'] : true;
  }

  /**
   * Return the action result
   *
   * @brief Action result
   *
   * @return bool
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

  // -------------------------------------------------------------------------------------------------------------------
  // Utility for build URL
  // TODO refator naming below
  // -------------------------------------------------------------------------------------------------------------------

  /**
   * Return the current URL without: `action` and singular id
   *
   * @brief URL
   *
   * @return string
   */
  public function urlRemveAction()
  {
    $remove = array(
      'action',
      $this->_args['singular']
    );
    $url    = remove_query_arg( $remove, stripslashes( $_SERVER['REQUEST_URI'] ) );
    return $url;
  }

  /**
   * Return the current URL without: `_wp_http_referer`, `_wpnonce` and singular id
   *
   * @brief URL
   *
   * @return string
   */
  public function urlRemoveNonce()
  {
    $remove = array(
      '_wp_http_referer',
      '_wpnonce',
      $this->_args['singular']
    );
    $url    = remove_query_arg( $remove, stripslashes( $_SERVER['REQUEST_URI'] ) );
    return $url;
  }

  /**
   * Return the URL to Add New item
   *
   * @brief URL
   *
   * @return string
   */
  public function urlAddNew()
  {
    $add = array(
      'action' => 'new',
      'page'   => $_REQUEST['page']
    );
    $url = add_query_arg( $add, $this->urlRemoveNonce() );
    return $url;
  }

  /**
   * Redirect
   *
   * @brief Redirect
   */
  public function redirect()
  {
    $url = $this->urlRemveAction();
    wp_redirect( $url );
    exit;
  }

}

/**
 * Interface definition for reminder dialog
 *
 * @interface       IWPDKListTableModel
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
 * @date            2014-03-27
 * @version         1.0.0
 * @since           1.5.2
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
   * Return the count of specific status
   *
   * @brief Count status
   *
   * @param string $status
   *
   * @return int
   */
  public function get_status( $status );

  /**
   * Return tha array with the action for the current status
   *
   * @brief Action with status
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
 * @date            2014-02-07
 * @version         1.0.0
 * @since           1.4.13
 *
 */
class WPDKListTableModel implements IWPDKListTableModel {

  /**
   * Used for check the action and bulk action results
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
    add_action( $action, array( $this, 'process_bulk_action' ) );

  }

  /**
   * Return a key values array with registered filters
   *
   * @brief Filters
   * @since 1.5.2
   *
   * @return array
   */
  public function get_filters()
  {
    return array();
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
   * Return the count of specific status
   *
   * @brief Count status
   *
   * @param string $status
   *
   * @return int
   */
  public function get_status( $status )
  {
    return;
  }

  /**
   * Return tha array with the action for the current status
   *
   * @brief Action with status
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
    if ( !empty( $nonce ) && !empty( $action ) ) {
      if ( wp_verify_nonce( $_REQUEST['_wpnonce'], 'bulk-' . $nonce ) ) {
        return $action;
      }
    }

    return $action;
  }

  /**
   * Process actions
   *
   * @brief Process actions
   * @since 1.4.21
   *
   */
  public function process_bulk_action()
  {
    // Override when you need to process actions before wp is loaded

    $action = $this->current_action();

    if ( $action ) {
      if ( isset( $_REQUEST['_wp_http_referer'] ) ) {
        $args = array(
          '_action_result'  => $this->action_result,
          '_action'         => $action,
          'action'          => false,
          'action2'         => false,
          'page'            => isset( $_REQUEST['page'] ) ? $_REQUEST['page'] : false,
        );

        // Previous selected filters
        $filters = $this->get_filters();
        $filter_args = array();
        foreach ( $filters as $key => $value ) {
          if ( isset( $_REQUEST[ $key ] ) && !empty( $_REQUEST[ $key ] )) {
            $filter_args[ $key ] = urlencode( $_REQUEST[ $key ] );
          }
        }

        //  merge standard args with filters args
        $args = array_merge( $args, $filter_args );

        // New referrer
        $uri  = add_query_arg( $args, $_REQUEST['_wp_http_referer'] );

        wp_safe_redirect( $uri );
      }
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