/**
 * This class manage a WPDKListTableViewController
 *
 * @class           WPDKListTableViewController
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
 * @date            2014-10-16
 * @version         1.0.1
 *
 * @history         1.0.1 - Cosmetic and improved ask confirm.
 *
 */

jQuery( function( $ )
{
  "use strict";

  if( typeof( window.WPDKListTableViewController ) === 'undefined' ) {

    window.WPDKListTableViewController = (function()
    {

      /**
       * This object
       *
       * @type {{version: string, init: _init}}
       * @private
       */
      var _WPDKListTableViewController = {
        version : '1.0.0',
        init    : _init
      };

      /**
       * Init
       *
       * @returns {{version: string, init: _init}}
       * @private
       */
      function _init()
      {

        // Ask confirm for delete
        _askConfirm();

        return _WPDKListTableViewController;
      }

      /**
       * Ask user confirm before delete a row
       *
       * @private
       */
      function _askConfirm()
      {
        // Get the main form
        var $form = $( 'form' );

        // Filter the confirm message for some bulk action.
        wpdk_add_filter( 'wpdk_list_table_bulk_action_confirm_message-action_delete', wpdk_list_table_bulk_action_confirm_message_action_delete );
        wpdk_add_filter( 'wpdk_list_table_row_action_confirm_message-action_delete', wpdk_list_table_bulk_action_confirm_message_action_delete );

        // Ask a confirm if bulk action is delete
        $form.submit( function()
        {
          var action  = $form.find( 'select[name="action"] option:selected' ).val();
          var action2 = $form.find( 'select[name="action2"] option:selected' ).val();

          // Set ! empty
          action = ( '-1' == action ) ? action2 : action;

          // If no bulk action found send for anyway
          if( '-1' == action ) {
            return true;
          }

          /**
           * Filter the confirm message for bulk actions.
           *
           * The dynamic portion of the hook name, action, refers to the bulk action select combo value.
           *
           * @param {string} confirm_message Confirm message, default empty.
           * @param {string} action          The action.
           */
          var confirm_message = wpdk_apply_filters( 'wpdk_list_table_bulk_action_confirm_message-' + action, '', action );

          /**
           * Filter the confirm message for bulk actions.
           *
           * @param {string} confirm_message Confirm message, default empty.
           * @param {string} action          The action.
           */
          confirm_message = wpdk_apply_filters( 'wpdk_list_table_bulk_action_confirm_message', confirm_message, action );

          if( '' !== confirm_message ) {
            return confirm( confirm_message );
          }
        } );

        // Select all tag a under the row actions
        $( document ).on( 'click', '.row-actions span a', function( e )
        {
          // The span class contains the 'action'
          var action = $( this ).parent( 'span' ).attr( 'class' );

          /**
           * Filter the confirm message for row actions.
           *
           * The dynamic portion of the hook name, action, refers to the row action class name.
           *
           * @param {string} confirm_message Confirm message, default empty.
           * @param {string} action          The action.
           */
          var confirm_message = wpdk_apply_filters( 'wpdk_list_table_row_action_confirm_message-' + action, '', action );

          /**
           * Filter the confirm message for row actions.
           *
           * @param {string} confirm_message Confirm message, default empty.
           * @param {string} action          The action.
           */
          confirm_message = wpdk_apply_filters( 'wpdk_list_table_row_action_confirm_message', confirm_message, action );

          if( '' !== confirm_message ) {
            return confirm( confirm_message );
          }

        } );

      }

      /**
       * Filter the confirm message for some bulk action.
       *
       * The dynamic portion of the hook name, action, refers to the bulk action select combo value.
       *
       * @param {string} confirm_message Confirm message, default empty.
       *
       * @return {string}
       */
      function wpdk_list_table_bulk_action_confirm_message_action_delete( confirm_message )
      {
        confirm_message = "Warning!!\n\nAre you sure to delete permately the rows selected?\n\nThis operation is not reversible!!";
        return confirm_message;
      }

      return _init();

    })();
  }

} );