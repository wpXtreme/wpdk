<?php

/**
 * Constant class for alert type
 *
 * @class              WPDKTwitterBootstrapAlertType
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
 * @date               2014-01-07
 * @version            1.1.1
 * @note               Updated to Bootstrap v3.0.0
 * @deprecated         since 1.4.21 - use WPDKUIAlert and WPDKUIAlertType
 *
 */
class WPDKTwitterBootstrapAlertType {
  // @deprecated const since 1.3.1 and Bootstrap v3.0.0
  const ALERT = 'alert-error';

  const SUCCESS     = 'alert-success';
  const INFORMATION = 'alert-info';
  const WARNING     = 'alert-warning';
  const DANGER      = 'alert-danger';

  // Since 1.4.8
  const WHITE = 'alert-white';
}

/**
 * Utility for Twitter Bootstrap Alert
 *
 * ## Overview
 * The WPDKTwitterBootstrapAlert class is a Twitter Bootstrap alert wrap.
 *
 * ### Create an Alert
 * To create and display an alert just coding:
 *
 *     $alert = new WPDKTwitterBootstrapAlert( 'my-alert', 'Hello World!' );
 *     $alert->display();
 *
 * OR
 *
 *     class myAlert extends WPDKTwitterBootstrapAlert {
 *
 *       // Internal construct
 *       public function __construct()
 *       {
 *          parent::__construct( $id, false, $type, $title );
 *       }
 *
 *       // Override content
 *       public function content()
 *       {
 *          echo 'Hello...';
 *       }
 *     }
 *
 * @class              WPDKTwitterBootstrapAlert
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2014-02-10
 * @version            1.7.0
 * @note               Updated HTML markup and CSS to Bootstrap v3.0.0
 * @deprecated         since 1.4.21 - use WPDKUIAlert
 *
 */
class WPDKTwitterBootstrapAlert extends WPDKUIAlert {
  // Keep for backward compatibility
}