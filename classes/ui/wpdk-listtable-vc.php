<?php
/// @cond private

/*
 * [DRAFT]
 *
 * THE FOLLOWING CODE IS A DRAFT. FEEL FREE TO USE IT TO MAKE SOME EXPERIMENTS, BUT DO NOT USE IT IN ANY CASE IN
 * PRODUCTION ENVIRONMENT. ALL CLASSES AND RELATIVE METHODS BELOW CAN CHNAGE IN THE FUTURE RELEASES.
 *
 */

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
 * @date               2013-03-05
 * @version            0.8.1
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
  private $args;


  /**
   * The child class should call this constructor from it's own constructor.
   * Create an instance of WPDKListTableViewController class
   *
   * @brief  Constructor
   *
   *
   * @param string $id    List table id
   * @param string $title Title of view controller
   * @param array $args  Standard WP_List_Table args
   *
   * @return WPDKListTableViewController
   *
   */
  public function __construct( $id, $title, $args ) {

    /* Create an instance of WP_List_Table class. */
    parent::__construct( $args );

    /* Set static properties. */
    $this->id          = sanitize_key( $id );
    $this->title       = $title;
    $this->args        = $args;
    $this->getStatusID = self::GET_STATUS;

    /* Init the internal view controller. */
    $this->viewController                = new WPDKViewController( $this->id, $this->title );
    $this->viewController->view->class[] = 'wpdk-list-table-box';

  }

  // -------------------------------------------------------------------------------------------------------------------
  // Ask information to override to custom
  // -------------------------------------------------------------------------------------------------------------------

  /**
   * Return a key value pairs array with the list of columns
   *
   * @brief Return the list of columns
   *
   * @note To override
   *
   * @return array
   */
  public function get_columns() {
    return array();
  }

  /**
   * Return a key value pairs array with statuses supported
   *
   * @brief Statuses
   *
   * @note To override
   *
   * @return array
   */
  public function get_statuses() {
    return array();
  }

  /**
   * Return the count of specific status
   *
   * @brief Count status
   * @note To override
   *
   * @param string $status
   *
   * @return int
   */
  public function get_status( $status ) {
    return;
  }

  /**
   * Return tha array with the action for the current status
   *
   * @brief Action with status
   * @note To override
   *
   * @param mixed  $item   The item
   * @param string $status Current status
   *
   * @return array
   */
  public function get_actions_with_status( $item, $status ) {
    return array();
  }

  /**
   * Return the array with the buk action for the combo menu for a status of view
   *
   * @brief Bulk actions
   * @note To override
   *
   * @param string $status Current status
   *
   * @return array
   */
  public function get_bulk_actions_with_status( $status ) {
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
  private function _defaultStatus() {
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
  private function _defaultSortableColumn() {
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
  private function _currentViewStatus() {
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
  private function _SQLDefaultOrder( $default_order = 'ASC' ) {
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
  private function _SQLStatus() {
    $status_field   = $this->getStatusID;
    $current_status = $this->_currentViewStatus();

    return sprintf( ' AND %s = "%s"', $status_field, $current_status );
  }


  // -------------------------------------------------------------------------------------------------------------------
  // WPDKViewController Interface
  // -------------------------------------------------------------------------------------------------------------------

  /**
   * @brief To override
   */
  public static function willLoad() {}

  /**
   * @brief To override
   */
  public static function didHeadLoad() {}

  // -----------------------------------------------------------------------------------------------------------------
  // Display
  // -----------------------------------------------------------------------------------------------------------------

  /**
   * This method override the default WP_List_Table display.
   *
   * @brief Display the list table view
   */
  public function display() {
    echo $this->html();
  }

  /**
   * Return the HTML markup for list table view.
   *
   * @brief Get the list table view
   *
   * @note This is a low level method that create the HTML markup for list table view. You'll notice that the display
   * method above has an input param for echo/display. For now this method html() is here for backward compatibility.
   *
   * @return string
   */
  public function html() {
    /* Buffering... */
    ob_start();

    /* Fetch, prepare, sort, and filter our data... */
    if ( !$this->prepare_items() ) :
      $this->views(); ?>

    <form id="<?php echo $this->id ?>" class="wpdk-list-table-form" method="get" action="">

      <?php if ( isset( $_REQUEST['page'] ) ) : ?>
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
      <?php endif; ?>

      <?php if ( isset( $_REQUEST['post_type'] ) ) : ?>
        <input type="hidden" name="post_type" value="<?php echo $_REQUEST['post_type'] ?>"/>
      <?php endif; ?>

      <?php parent::display() ?>
    </form>
    <?php endif; ?>
  <?php
    $content = ob_get_contents();
    ob_end_clean();

    $this->viewController->viewHead->content = $content;

    add_action( 'wpdk_header_view_title_did_appear', array( $this, 'wpdk_header_view_title_did_appear' ) );

    return $this->viewController->html();
  }

  /**
   * Called when the title has been drawed
   *
   * @brief Filter on title
   *
   * @param WPDKHeaderView $view The header view
   */
  public function wpdk_header_view_title_did_appear( $view ) {
    if ( 'new' != $this->current_action() ) {
      printf( '<a class="add-new-h2 button-primary" href="%s">%s</a>', $this->urlAddNew(), __( 'Add New', WPDK_TEXTDOMAIN ) );
    }
  }

  // -----------------------------------------------------------------------------------------------------------------
  // Override standard WP_List_Table
  // -----------------------------------------------------------------------------------------------------------------

  /**
   * The itens can be not found for two main reason: the query search has param tha t doesn't match with items, or the
   * items list (or the database query) return an empty list.
   *
   * @brief Called when no items found
   *
   */
  public function no_items() {
    /* Default message. */
    printf( __( 'No %s found.', WPDK_TEXTDOMAIN ), $this->title );

    /* If in search mode. */
    /* @todo Find a way to determine if we are in 'search' mode or not */
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
  public function get_views() {
    $views         = array();
    $get_status_id = $this->getStatusID;
    $filter_status = isset( $_GET[$get_status_id] ) ? $_GET[$get_status_id] : $this->_defaultStatus();

    foreach ( $this->get_statuses() as $key => $status ) {

      /* See _defaultStatus() for detail for this array. */
      $status = is_array( $status ) ? $status[0] : $status;

      /* Recompute! */
      $count = $this->get_status( $key );
      if ( ! empty( $count ) ) {

        $current = ( $filter_status == $key ) ? 'class="current"' : '';
        $href    = add_query_arg( array( 'status' => $key, 'action' => false, $this->_args['singular'] => false ) );

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
  public function statuses( $statuses ) {
    return $statuses;
  }

  /**
   * Processing data items to view.
   *
   * @brief Processing data to view
   *
   * @return boolean
   *
   */
  public function prepare_items() {

    /* First, lets decide how many records per page to show. */
    $id_user = get_current_user_id();

    /**
     * @var WP_Screen $screen
     */
    $screen = get_current_screen();

    $option   = $screen->get_option( 'per_page', 'option' );
    $per_page = get_user_meta( $id_user, $option, true );

    if ( empty ( $per_page ) || $per_page < 1 ) {
      $per_page = $screen->get_option( 'per_page', 'default' );
    }

    /* Columns Header */
    $this->_column_headers = $this->get_column_info();

    /**
     * Optional. You can handle your bulk actions however you see fit. In this
     * case, we'll handle them within our package just to keep things clean.
     */
    if ( $this->process_bulk_action() ) {
      return true;
    }
    /* This is required because some GET params keep in the url. */
    $remove = array(
      'action'
    );
    $_SERVER['REQUEST_URI'] = remove_query_arg( $remove, stripslashes( $_SERVER['REQUEST_URI'] ) );

    /* Get data. */
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
    $total_items = count( $data );

    /**
     * The WP_List_Table class does not handle pagination for us, so we need
     * to ensure that the data is trimmed to only the current page. We can use
     * array_slice() to
     */
    $data = array_slice( $data, ( ( $current_page - 1 ) * $per_page ), $per_page );

    /**
     * REQUIRED. Now we can add our *sorted* data to the items property, where
     * it can be used by the rest of the class.
     */
    $this->items = $data;

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

  // -----------------------------------------------------------------------------------------------------------------
  // To override
  // -----------------------------------------------------------------------------------------------------------------

  /**
   * Return the items data list. This method will be over-ridden in a sub-class.
   *
   * @brief Return the items data list
   *
   * @return array
   */
  public function data() {
    return array();
  }

  /**
   * Display a cel content for a column.
   *
   * @brief Process the content of a column
   *
   * @note I suggest to you to override this method
   *
   * @param array  $item        The single item
   * @param string $column_name Column name
   *
   * @return mixed
   */
  public function column_default( $item, $column_name ) {
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
  public function actions_column( $item, $column_name = 'description', $item_status = '', $custom_content = '' ) {

    /* Get the current view status. */
    $status = $this->_currentViewStatus();

    if( !empty( $item_status ) ) {
      $status = $item_status;
    }

    $stack = array();
    foreach( $this->get_actions_with_status( $item, $status ) as $action => $label ) {
      if( !empty( $action ) ) {
        $args = array(
          'action' => $action,
          $this->args['singular'] => $item[$this->args['singular']]
        );
        $href = add_query_arg( $args );
        $stack[$action] = sprintf( '<a href="%s">%s</a>', $href, $label );
      }
    }

    $description = empty( $custom_content ) ? sprintf( '<strong>%s</strong>', $item[$column_name] ) : $custom_content;
    return sprintf( '%s %s', $description, $this->row_actions( $stack ) );
  }

  /**
   * Return the HTML markup for the checkbox element used for multiple selections.
   *
   * @brief Standard checkbox for group actions
   *
   * @note You can override this method for your custom view. This method is called only there is a column named "cb"
   *
   * @param $item
   *
   * @return string
   */
  public function column_cb( $item ) {
    $name  = $this->args['singular'];
    $value = $item[$name];
    return sprintf( '<input type="checkbox" name="%s[]" value="%s" />', $name, $value );
  }

  /**
   * Return an array with the list of bulk actions used in combo menu select.
   *
   * @brief Get the bulk actions
   *
   * @return array
   */
  public function get_bulk_actions() {
    /* Get the current status, could be empty. */
    $current_status = isset( $_REQUEST[$this->getStatusID] ) ? $_REQUEST[$this->getStatusID] : $this->_defaultStatus();
    return $this->get_bulk_actions_with_status( $current_status );
  }

  // -----------------------------------------------------------------------------------------------------------------
  // Bulk and single actions
  // -----------------------------------------------------------------------------------------------------------------

  /**
   * This method is to override and you can use it to processed the action request sent from list table.
   * You can processed bulk and single action. This method must return a boolean in order to re-processed the items
   * list view.
   *
   * @brief Process the bulk actions and standard actions
   *
   * @sa prepare_items() for detail
   *
   * @return bool TRUE to stop display the list view, FALSE to display the list.
   */
  public function process_bulk_action() {
    die( __METHOD__ . ' must be over-ridden in a sub-class.' );
  }

  // -----------------------------------------------------------------------------------------------------------------
  // Utility for Bulk actions
  // -----------------------------------------------------------------------------------------------------------------

  /**
   * Utility for build a right actions list when the list table is showing trash items.
   *
   * @brief Return the only used action
   *
   * @param array  $args
   * @param string $status
   *
   * @deprecated Use the new engine subclass - used by SamrtShop
   *
   * @return mixed
   */
  public static function actions( $args, $status ) {

    $id       = key( $args );
    $id_value = $args[$id];
    $actions = array();

    foreach ( $args['actions'] as $key => $label ) {
      $href          = add_query_arg( array( 'action' => $key, $id => $id_value ) );
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

  // -----------------------------------------------------------------------------------------------------------------
  // Utility for build url
  // -----------------------------------------------------------------------------------------------------------------

  /**
   * Return the current URL without: `action` and singular id
   *
   * @brief URL
   *
   * @return string
   */
  public function urlRemveAction() {
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
  public function urlRemoveNonce() {
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
  public function urlAddNew() {
    $add = array(
      'action' => 'new',
      'page'   => $_REQUEST['page']
    );
    $url = add_query_arg( $add, $this->urlRemoveNonce() );
    return $url;
  }

  public function redirect() {
    $url = $this->urlRemveAction();
    wp_redirect( $url );
    exit;
  }

}

/// @endcond