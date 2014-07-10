<?php

/**
 * Display a common useful table with column and rows. This table can be scrollable.
 * You usually subclass this class in your project.
 *
 * @class           WPDKUITableView
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
 * @date            2014-05-14
 * @version         1.0.0
 * @since           1.5.6
 *
 */
class WPDKUITableView extends WPDKView {

  /**
   * Height of scrollable area. Set to empty for no scroll.
   *
   * @brief Scrollable
   *
   * @var string $scrollable_height
   */
  public $scrollable_height = '';

  /**
   * List of column with attributes, as 'width', 'style', etc...
   *
   * @brief Column attrobutes
   *
   * @var array $column_atts
   */
  protected $column_atts = array();

  /**
   * Create an instance of WPDKUITableView class
   *
   * @patam string $id The view id.
   *
   * @return WPDKUITableView
   */
  public function __construct( $id )
  {
    parent::__construct( $id, 'wpdk-ui-table-view' );

    // Enqueue component
    WPDKUIComponents::init()->enqueue( WPDKUIComponents::TABLE );
  }

  /**
   * Display
   *
   * @brief Display
   */
  public function draw()
  {
    // Sharing column attributes
    $this->column_atts = $this->column_atts();

    // Check for scrollable
    $style = empty( $this->scrollable_height ) ? '' : 'style="overflow-y:auto;height:' . $this->scrollable_height . '"';

    ?>
    <table width="100%" cellpadding="0" cellspacing="0">
      <thead>
        <tr>
          <?php foreach ( $this->columns() as $column_key => $label ) : ?>
            <th <?php echo $this->get_atts( $column_key ) ?> class="<?php echo $column_key ?>"><?php echo $label ?></th>
          <?php endforeach; ?>
        </tr>
      </thead>
    </table>

    <div class="wpdk-table-container" <?php echo $style ?>>
      <table width="100%" cellpadding="0" cellspacing="0">
        <tbody>
          <?php foreach ( $this->items() as $item ) : ?>
            <?php $this->single_row( $item ) ?>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

  <?php
  }

  /**
   * Return the array with column list.
   *
   * @brief Columns
   */
  public function columns()
  {
    die( __METHOD__ . ' must be override in your subclass' );
  }

  /**
   * Return the array with the column attributes list.
   *
   * @brief Columns
   */
  public function column_atts()
  {
    return array();
  }

  /**
   * Return the inline attributes for the column.
   *
   * @brief Column attrobutes
   *
   * @param string $column_key The column key.
   *
   * @return string
   */
  protected  function get_atts( $column_key )
  {
    return isset( $this->column_atts[ $column_key ] ) ? WPDKHTMLTag::attributeInline( $this->column_atts[ $column_key ] ) : '';
  }

  /**
   * Draw a single row.
   *
   * @brief Row
   *
   * @param array  $item       List of column
   * @param string $column_key The column key
   */
  public function single_row( $item )
  {
    ?>
    <tr>
      <?php foreach ( array_keys( $this->columns() ) as $column_key ) : ?>
        <td <?php echo $this->get_atts( $column_key ) ?> class="column-<?php echo $column_key ?>">
          <?php echo $this->column( $item, $column_key ) ?>
        </td>
      <?php endforeach; ?>
    </tr>
  <?php
  }

  /**
   * Return the content of column.
   *
   * @brief Single column
   *
   * @param array  $item       The array key with content of column.
   * @param string $column_key Column key.
   */
  public function column( $item, $column_key )
  {
    die( __METHOD__ . ' must be override in your subclass' );
  }

  /**
   * Return an array with content of column table.
   *
   * @brief Items
   */
  public function items()
  {
    die( __METHOD__ . ' must be override in your subclass' );
  }

}