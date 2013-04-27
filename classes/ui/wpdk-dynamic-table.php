<?php
/**
 * The WPDKDynamicTable class create and manage a special amazing table list view where you can add/remove rows.
 * In addition you can drag & drop a single row to sort.
 *
 * ## Overview
 *
 * ## Javascript
 *
 * @class              WPDKDynamicTable
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2012-12-05
 * @version            0.8.5
 */

class WPDKDynamicTable {

  /**
   * This is the internal code name of the column (last column) used for add/del a row.
   *
   * @brief Column add/del
   */
  const COLUMN_ROW_MANAGE = '_wpdk_dt_column_row_manage';

  /**
   * Set TRUE for sortable rows
   *
   * @brief Sortable
   *
   * @var bool $sortable
   */
  public $sortable;

  /**
   * The dynamic table ID
   *
   * @brief Dynamic table ID
   *
   * @var string $_id
   *
   */
  private $_id;

  /**
   * List of class (space separated)
   *
   * @brief Addition classes
   *
   * @var string $_class
   */
  private $_class;

  /**
   * This is a key value pairs array with the column name and type
   *
   * @brief Columns list
   *
   * @var array $_columns
   */
  private $_columns;

  /**
   * This is the items list for preload the dynamic table view
   *
   * @brief Items
   *
   * @var array $_items
   */
  private $_items;


  /**
   * Create an instance of WPDKDynamicTable class
   *
   * @brief Construct
   *
   * @param string $id      ID for this dynamic table
   * @param array  $columns Key value pairs array with column name and type
   * @param array  $items   Key value pairs array with column and value. Used for preload the table.
   *
   * @return WPDKDynamicTable
   */
  public function __construct( $id, $columns, $items ) {

    $this->_columns = $columns;
    $this->_items   = $items;
    $this->_class   = '';

    $this->sortable = false;

    /* Added dynamic + */
    $this->_columns[self::COLUMN_ROW_MANAGE] = '';
  }

  // -----------------------------------------------------------------------------------------------------------------
  // Items
  // -----------------------------------------------------------------------------------------------------------------

  /**
   * Get/Set the items list for this dynamic table. If not set parameters this method return the items array.
   *
   * @brief Dynamic table items
   *
   * @return array
   */
  public function items() {
    if ( func_num_args() > 0 ) {
      $this->_items = func_get_arg( 0 );
    }
    else {
      return $this->_items;
    }
    return false;
  }

  // -----------------------------------------------------------------------------------------------------------------
  // Display
  // -----------------------------------------------------------------------------------------------------------------

  /**
   * Display the HTML markup of the dynamic table
   *
   * @brief Display the dynamic table
   */
  public function display() {
    echo $this->html();
  }

  /**
   * Return the HTML markup for dynamic table
   *
   * @brief Get the HTML markup for dynamic table
   *
   * @return string
   */
  public function html() {

    $html_thead = $this->thead();
    $html_tbody = $this->tbody();
    $html_tfoot = $this->tfoot();
    $id         = $this->_id;
    $class      = $this->classes();
    $data       = $this->data();

    $html = <<< HTML
    <table id="{$id}" {$data} class="wpdk-dynamic-table {$class}" border="0" cellpadding="0" cellspacing="0">
        {$html_thead}
        {$html_tbody}
        {$html_tfoot}
    </table>
HTML;
    return $html;
  }

  // -----------------------------------------------------------------------------------------------------------------
  // HTML assets
  // -----------------------------------------------------------------------------------------------------------------

  /**
   * Return a string with all additional class
   *
   * @brief Additional Classes
   *
   * @return string
   *
   */
  private function classes() {
    $stack = array( $this->_class );
    if ( true == $this->sortable ) {
      $stack[] = 'wpdk-dynamic-table-sortable';
    }
    return join( ' ', $stack );
  }

  /**
   * Return a string with all additional data/attribute
   *
   * @brief Data attribute
   *
   * @return string
   */
  private function data() {
    $stack = array();
    if ( true == $this->sortable ) {
      $stack[] = sprintf( 'data-sortable="true"' );
    }
    return join( ' ', $stack );
  }

  /**
   * Return the HTML markup to display the add row button.
   *
   * @brief Button add row
   *
   * @return string
   */
  private function buttonAdd() {
    $label = __( 'Add', WPDK_TEXTDOMAIN );
    $title = __( 'Add a new empty row', WPDK_TEXTDOMAIN );

    $html = <<< HTML
    <input data-placement="left" title="{$title}" title-backup="{$title}" type="button" value="{$label}" class="wpdk-tooltip wpdk-dt-add-row">
HTML;
    return $html;
  }

  /**
   * Return the HTML markup to display the delete row button.
   *
   * @brief Button delete row
   *
   * @return string
   */
  private function buttonDelete() {
    $label = __( 'Delete', WPDK_TEXTDOMAIN );
    $title = __( 'Delete entire row', WPDK_TEXTDOMAIN );

    $html = <<< HTML
    <input data-placement="left" title="{$title}" content="{$title}" type="button" value="{$label}" class="wpdk-tooltip wpdk-dt-delete-row">
HTML;
    return $html;
  }

  // -----------------------------------------------------------------------------------------------------------------
  // HTML assets table
  // -----------------------------------------------------------------------------------------------------------------

  /**
   * Return the HTML markup for the head of the Dynamic table
   *
   * @brief Dynamic table head
   *
   * @return string
   */
  private function thead() {

    $ths = '';
    foreach ( $this->_columns as $key => $column ) {
      if ( $key != self::COLUMN_ROW_MANAGE ) {
        $ths .= sprintf( '<th class="wpdk-dynamic-table-column-%s">%s</th>', $key, $column['table_title'] );
      }
      else {
        //$ths .= sprintf( '<th class="%s"></th>', $key );
      }
    }

    $html = <<< HTML
    <thead>
        <tr>
            {$ths}
        </tr>
    </thead>
HTML;
    return $html;
  }

  /**
   * Return the HTML markup for the body of the dynamic table
   *
   * @brief Dynamic table body
   *
   * @return string
   */
  private function tbody() {
    $trs = '';

    /* Il primo è sempre display none e usato per la clonazione */
    $trs .= sprintf( '<tr class="wpdk-dt-clone">%s</tr>', $this->tbodyRow() );

    if ( !empty( $this->_items ) ) {
      foreach ( $this->_items as $item ) {
        $trs .= sprintf( '<tr>%s</tr>', $this->tbodyRow( $item ) );
      }
    }
    $trs .= sprintf( '<tr>%s</tr>', $this->tbodyRow() );

    $html = <<< HTML
    <tbody>
        {$trs}
    </tbody>
HTML;
    return $html;
  }

  /**
   * Return the HTML markup with value for the single cel of body table
   *
   * @brief Process column and value
   *
   * @param null $item
   *
   * @return string
   */
  private function tbodyRow( $item = null ) {
    $tds = '';
    foreach ( $this->_columns as $key => $column ) {
      if ( self::COLUMN_ROW_MANAGE != $key ) {

        if ( !is_null( $item ) && is_array( $item ) ) {
          $column['value'] = isset( $item[$key] ) ? $item[$key] : '';
        }

        /* Get a single field. */
        $field = WPDKUIControlsLayout::item( $column );

        $tds .= sprintf( '<td class="wpdk-dynamic-table-cel-%s">%s</th>', $key, $field );
      }
      else {
        if ( is_null( $item ) ) {
          $tds .= sprintf( '<td class="%s">%s<span class="wpdk-dt-clone delete">%s</span></th>', $key, $this->buttonAdd(), $this->buttonDelete() );
        }
        else {
          $tds .= sprintf( '<td class="%s">%s</th>', $key, $this->buttonDelete() );
        }

      }
    }
    return $tds;
  }

  /**
   * return the HTML markup for the footer of dynamic table
   *
   * @brief Dynamic table footer
   *
   * @return string
   */
  private function tfoot() {

    $tds = '';
    foreach ( $this->_columns as $key => $column ) {
      if ( $key != self::COLUMN_ROW_MANAGE ) {
        $tds .= sprintf( '<td class="wpdk-dynamic-table-cel-%s"></th>', $key );
      }
      else {
      }
    }
    $html = <<< HTML
    <tfoot>
        <tr>
            {$tds}
        </tr>
    </tfoot>
HTML;
    return $html;
  }

}