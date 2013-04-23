<?php
/// @cond private

/*
 * [DRAFT]
 *
 * THE FOLLOWING CODE IS A DRAFT. FEEL FREE TO USE IT TO MAKE SOME EXPERIMENTS, BUT DO NOT USE IT IN ANY CASE IN
 * PRODUCTION ENVIRONMENT. ALL CLASSES AND RELATIVE METHODS BELOW CAN CHNAGE IN THE FUTURE RELEASES.
 *
 */

/**
 * Gestisce una lista generica di elementi, simile ad un table view su iPhone.
 *
 * @class              WPDKTableView
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C)2012 wpXtreme, Inc.
 * @date               2012-11-28
 * @version            0.8.1
 * @deprecated         Since 0.6.2 - Used yes by SmartShop
 *
 */

//if (!class_exists('WP_List_Table')) {
//    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
//}

class WPDKTableView {

    public $_items;
    public $_columns;
    public $_args;
    public $_paged;
    public $_itemsPerPage;
    public $_totalItems;

    function __construct( $args = array() ) {
        $default             = array(
            'name'         => '',
            'title'        => '',
            'filter'       => '',
            'paged'        => 1,
            'itemsPerPage' => 10,
            'ajaxHook'     => ''
        );
        $args                = wp_parse_args( $args, $default );
        $this->_args         = $args;
        $this->_paged        = $args['paged'];
        $this->_itemsPerPage = $args['itemsPerPage'];
    }

    // -----------------------------------------------------------------------------------------------------------------
    // Get/Set pseudo-properties
    // -----------------------------------------------------------------------------------------------------------------

    function paged($value = null) {
        if(is_null($value)) {
            return $this->_paged;
        }
        $this->_paged = $value;
        $this->prepareItems();
    }

    function totalItems($value = null) {
        if(is_null($value)) {
            return $this->_totalItems;
        }
        $this->_totalItems = $value;
    }

    function itemsPerPage($value = null) {
        if(is_null($value)) {
            return $this->_itemsPerPage;
        }
        $this->_itemsPerPage = $value;
    }

    function columns($value = null) {
        if(is_null($value)) {
            return $this->_columns;
        }
        $this->_columns = $value;
    }

    function items($value = null) {
        if(is_null($value)) {
            return $this->_items;
        }
        $this->_items = $value;
    }

    // -----------------------------------------------------------------------------------------------------------------
    // To override
    // -----------------------------------------------------------------------------------------------------------------
    function prepareItems() {
        die('function WPDKTableView::prepareItems() must be over-ridden in a sub-class.');
    }

    function item($item, $column) {
        echo $item;
    }

    function moreItems() {
        $over = $this->_paged * $this->_itemsPerPage;
        if($over < $this->_totalItems) : ?>
        <tr id="" class="wpdk-tableview-moreitems">
            <td><?php $this->moreItemsText() ?></td>
        </tr>
        <?php endif;
    }

    function moreItemsText() {
        _e('More', WPDK_TEXTDOMAIN);
    }

    public function backButton() {}
    public function title() {}
    public function search() {}

    public function viewWillAppear() {}
    public function viewDidAppear() {}

    // -----------------------------------------------------------------------------------------------------------------
    // View
    // -----------------------------------------------------------------------------------------------------------------
    public function view() {
        $this->viewWillAppear();
        ?>
    <div class="wpdk-tableview" id="<?php echo $this->_args['name'] ?>">
        <input type="hidden" id="wpdk-tableview-ajaxhook" value="<?php echo $this->_args['ajaxHook'] ?>" />
        <?php $this->navigationController() ?>
        <table class="wpdk-tableview-table" cellpadding="0" cellspacing="0" width="100%">
            <thead>
                <tr><?php $this->head() ?></tr>
            </thead>
            <tbody>
                <?php $this->body() ?>
            </tbody>
            <tfoot>
                <?php $this->footer() ?>
            </tfoot>
        </table>
    </div>
    <?php
        $this->viewDidAppear();
    }

    function body() {
        foreach($this->_items as $keyitem => $item) : ?>
            <tr id="wpdk-tableview-row_<?php echo $keyitem ?>" class="<?php echo $keyitem ?>">
            <?php foreach($this->_columns as $keycolumn => $column) : ?>
                <td id="wpdk-tableview-item_<?php echo $keycolumn ?>" class="<?php echo $keycolumn ?>"><?php $this->item($item, $keycolumn) ?></td>
            <?php endforeach; ?>
            </tr>
        <?php endforeach;
        $this->moreItems();
    }

    // -----------------------------------------------------------------------------------------------------------------
    // Private internal use
    // -----------------------------------------------------------------------------------------------------------------
    private function head() {
        foreach($this->_columns as $key => $column) : ?>
            <th class="<?php echo $key ?>"><?php echo $column ?></th>
        <?php endforeach;
    }


    private function footer() {}

    // -----------------------------------------------------------------------------------------------------------------
    // Navigation Controller
    // -----------------------------------------------------------------------------------------------------------------

    public function navigationController() {
        ?>
    <table class="wpdk-navigationcontroller" cellpadding="0" cellspacing="0" width="100%">
        <tbody>
        <tr>
            <td><?php $this->backButton() ?></td>
            <td><?php $this->title() ?></td>
            <td><?php $this->search() ?></td>
        </tr>
        </tbody>
    </table>
        <?php
    }

}

/// @endcond
