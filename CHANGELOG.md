# CHANGELOG

---

## Versioning

For transparency and insight into our release cycle, and for striving to maintain backward compatibility, this code will be maintained under the Semantic Versioning guidelines as much as possible.

Releases will be numbered with the following format:

`<major>.<minor>.<patch>`

And constructed with the following guidelines:

* Breaking backward compatibility bumps the major (and resets the minor and patch)
* New additions without breaking backward compatibility bumps the minor (and resets the patch)
* Bug fixes and misc changes bumps the patch

For more information on SemVer, please visit http://semver.org/.

---

## Version 1.7.5
### 2014-12-??

* Minor fixes

## Version 1.7.4
### 2014-12-10

* Added 4 news font icons
* Added IP address core placeholder
* Minor fixes stability
* Performance improves

## Version 1.7.3
### 2014-11-28

* Added 14 news font icons
* Improved WPDK Preferences with `wpdk_preferences_reset_to_default_branch-{$branch}`.
* Improved WPDK Preferences with `wpdk_preferences_update_branch-{$branch}`.
* Added `wpdk_flush_cache_third_parties_plugins` action in order to flush third parties plugins.
* Added `WPDKUIControlType::inputTypeWithClass()` helper static method in order to retrive the HTML control type string.
* Fixed dynamic table css styles.
* Fixed potential Javascript warning in localize script on jQuery
* Minor stability fixes

## Version 1.7.2
### 2014-11-19

#### Improvements

* Improved WPDK user profile UI
* Minor CSS styles fixes and improvements
* Improved style sheets

#### Enhancements

* Renamed `wpdk_post_placeholders_array` filter in `wpdk_post_placeholders_replace`
* Added `WPDKUIControlURL`
* Added `composer.json`
* Added jQuery tabs vertical support

#### Bugs

* Fixed potential Javascript warning in localize script when no user logged in
* Fixed potential wrong json type in some autocomplete engine

## Version 1.7.1
### 2014-11-05

#### Bugs

* Fixed wrong enqueue components for preferences

#### Enhancements

* Improved `WPDKUIControlButton` class with toggle feature (see https://github.com/wpXtreme/wpdk/wiki/Button-Toggle-UI-Control)
* Added `wpdkProgressBars()` methods to manage the progress bar (see https://github.com/wpXtreme/wpdk/wiki/WPDKUIProgress)
* Added `WPDKUIProgress`
* Update WPDK UI Progress styles with Bootstrap v3.3.0

## Version 1.7.0
### 2014-10-29

#### Bugs

* Fixed potential error when open a modal dialog

#### Enhancements

* Added `WPDKGeo::reverseGeocoding()` mathod
* Added `wpdk_geo` shortcode
* Added `WPDKDB` as extension of WordPress wpdb class
* Added `add_clear` property for all input WPDK UI Control in order to display the icon clear
* Added `WPDKFilesystem::append()` method
* Added `wpdk_ui_modal_dialog_javascript_show-{$id}` action
* Added `wpdk_ui_modal_dialog_javascript_toogle-{$id}` action


## Version 1.6.1
### 2014-10-22

#### Enhancements

* Added Javascript `wpdk_add_filter()` and `wpdk_apply_filter()`
* Improved Javascript `wpdk_add_action()` and `wpdk_do_action()` in order to support "priority" features and avoid document trigger handler events.
* Added `WPDKMath::bytes()`

#### Bugs

* Fixed potential 'division by zero' in `WPDKFilesystem::fileSize()` method
* Minor fixes

## Version 1.6.0
### 2014-10-14

#### Enhancements

* Added `wpdk-load-scripts.php` and `wpdk-load-styles.php` in order to concatenate WPDK components
* Added `WPDKGeo` class
* Added `WPDKUserAgents` class

#### Improved

* Improved Javascript with `wpdk_add_action()` & `wpdk_do_action()`
* Improved & fix Javascript docs
* Improved performance
* Removed deprecated
* Improvements users class/filename organization

#### Bugs

* Fixed potential `__PHP_Incomplete_Class` in object delta

## Version 1.5.17
### 2014-09-24

* Several improved and fixed on list table view controller
* Improved action filter and post data processing in list table model and view controller

## Version 1.5.16
### 2014-09-19

#### Improvements

* Updated jQuery timepicker addon
* Added `WPDKPost::publish()`
* Added `WPDKPost::insert()`
* Added `WPDKPost::duplicate()`
* Added site option `wpdk_watchdog_log` to enable/disable watchdog log
* Added `WPDK_WATCHDOG_LOG` constant to enable/disable watchdog log
* Added `WPDKUsers::deleteUsersMetaWithKey()`
* Refresh `WPDKDBListTableModel` class
* Improved CSS style for alert and form rows
* Added W3 total cache plugin flush

#### Bugs

* Fixed date format
* Fixed 'Add New' url in `WPDKListTableViewController`

## Version 1.5.15
### 2014-09-05

#### Bugs

* Fixed potential warning in `WPDKListTableViewController` while build the url

## Version 1.5.14
### 2014-09-02

#### Bugs

* Fixed potential conflict with $.fn.swipe jQuery extension
* Fixed potential conflict with 'event', 'change', 'changed' internal Javascript events

---

## Version 1.5.13
### 2014-08-29

#### Enhancements

* Alignments for WordPress 4.0.RC1
* Removed (obsolete) jquery validate plugin
* Renamed elapsed_string() with elapsedString(), added params and deprecated.

#### Improvements

* Updated `WPDKPostMeta`
* Improved date format in user profile
* Improved timeNewLine() method
* Updated filter docs and cosmetic code

#### Bugs

* Minor fixes for WP multisite

---

## Version 1.5.12
### 2014-08-23

#### Improvements

* Updated localization
* Updated jQuery Cookie Plugin to v1.4.1 (https://github.com/carhartl/jquery-cookie)

#### Bugs

* Fixed jQuery UI CSS conflict

---

## Version 1.5.11
### 2014-08-20

#### Improvements

* Loading scripts and styles

#### Bugs

* Fixed potential incompatibility with WordPress Multisite menu

---

## Version 1.5.10
### 2014-07-31

#### Improvements

* Added a tooltip when an alert is in permanent dismiss mode
* Added a tooltip when a dialog is in permanent dismiss mode
* Introducing the `WPDKUIModalDialogTour` class
* Added Tour for new Placeholders
* Minor fixes stability

---

## Version 1.5.9
### 2014-07-28

#### Improvements

* Improved CSS base64 img

#### Bugs

* Fixed modal dialog width and height
* Fixed wrong data toggle on modal dialog button
* Fixed mail and user id params
* Fixed mail user id
* Fixed post placeholders

---

## Version 1.5.8
### 2014-07-22

#### Improvements

* Added `wpdk_post_placeholders_array` filter for placeholder
* Improved `wpdk_post_placeholders_content` filter and implementation

#### Bugs

* Fixed potential wrong alert id when empty

---

## Version 1.5.7
### 2014-07-13

#### Improvements

* Docs

---

## Version 1.5.6
### 2014-07-10

#### Improvements

* Added WPDKUITableView` component
* Added `href` ( TAG A instead BUTTON ) in `WPDKUIModalDialog` buttons

#### Bugs

* Fixed potential overwrite sidebar in `WPDKScreenHelp`

---

## Version 1.5.5
### 2014-04-30

#### Improvements

* Added `canceled` status in user
* Added `column_checkbox` method to display/hide the checkbox in list table view controller

---

## Version 1.5.4
### 2014-04-28

#### Enhancements

* Added Component Javascript for `WPDKListTableViewController`

#### Improvements

* Improved `WPDKMenu`
* Improved `WPDKUser`
* Improved `WPDKRoles`
* Improved `WPDKCapabilities`

#### Bugs

* Fixed potential missing dependence from WPDK (js and css)
* Fixed potential wrong URL on Ajax request in `WPDKListTableViewController`
* Fixed `WPDKRoles`
* Fixed potential bugs on filter `post_updated_messages` (for missing params) in Custom Post Type

---

## Version 1.5.3
### 2014-04-08

#### Bugs

* Fixed wrong sqp filename and table name in delta() database method

#### Improvements

* Improved and fixed `WPDKWidget` class
* Cosmetic


---

## Version 1.5.2
### 2014-03-28

#### Improvements

* Improved list view controller and model with filters

#### Bugs

* Fixed potential bugs with WP Super Cache when preferences saved
* Fixed potential wrong data-api on ui components
* Fixed potential wrong trigger on swipe control

---

## Version 1.5.1
### 2014-03-18

#### Enhancements

* Added `WPDKUser::deleteTransientWithUser()`
* Deprecated `wpdk_delete_user_transient` function
* Added search box field support in `WPDKListTableViewController`
* Added `WPDKDBTableModel`
* Added `WPDKDBTableModelListTable`
* Added `WPDKDBTableRowStatuses`
* Removed/moved deprecated database classes

#### Improvements

* Improved `WPDKListTableViewController`
* Improved `WPDKListTableModel`
* Improved and fixes user avatar
* Replaced deprecated

#### Bugs

* Try to avoid 'PHP Strict Standards: Declaration of ... should be con...' Thanks to [Mte90](https://github.com/Mte90)


---

## Version 1.5.0
### 2014-02-26

#### Enhancements

* Added `enqueue()` method in `WPDKUIComponents`
* Added `WPDKUIComponents` in Javascript
* Removed `WPDKTwitterBootstrapPopover` class (never used and replaced with `WPDKUIPopover`)
* Introducing `WPDKUIPopover`
* Improved `WPDKGlyphIcons` Javascript loading
* Updated Glyphs


---

## Version 1.4.23
### 2014-02-19

#### Enhancements

* Added `WPDKHTML::endCompress()`

#### Bugs

* Fixed wrong WPDKUIAlertType
* Fixed potential override WPDK alias WPDKUIPopover


---

## Version 1.4.22
### 2014-02-18

#### Improvements

* Cosmetic docs

#### Bugs

* Fixed dynamic table view controller components
* Fixed gravatar ssl url


---

## Version 1.4.21
### 2014-02-17

#### Enhancements

* Added permanent dismiss alert by user logged in
* Added `WPDKComponents` class
* Added `WPDKTwitterBootstrapPopover` class

#### Improvements

* Updated Glyph Icons
* Rewritten Javascript and CSS
* Added autoload components for several view controler
* Added static method `WPDKListTableViewController::action()` to return the current action

#### Bugs

* Fixes and stabilty


---

## Version 1.4.20
### 2014-02-06

#### Enhancements

* Introducing the static init method in view controller
* Introducing `WPDKCron` classes

#### Improvements

* Improved stability and speed in Custom Post Type
* Stability refactor
* Improved `WPDKScreenHelp`

---

## Version 1.4.16
### 2014-02-03

#### Bugs

* Fixed potential css conflict
* Fixed potential title output in `WPDKTwitterBootstrapAlert` when title property is empty

---

## Version 1.4.15
### 2014-01-31

#### Bugs

* Fixed static declaration

#### Experimental

* Added `WPDKScripts` class

---

## Version 1.4.14
### 2014-01-29

#### Bugs

* Fixed potential HTML strip in textarea


---

## Version 1.4.13
### 2014-01-29

#### Enhancements

* Introducing `WPDKListTableModel` class

#### Bugs

* Fixed missing style attribute in input type tag and control
* Minor stability fixes in css

---

## Version 1.4.12
### 2014-01-24

#### Bugs

* Fixed potential missing autoload classes
* Minor stable improvements on WordPress dbDelta() procedure


---

## Version 1.4.11
### 2014-01-23

#### Bugs

* Fixed wrong include `WPDKHTMLTagImg`
* Fixed `WPDKDynamicTableView` output


---

## Version 1.4.10
### 2014-01-23

#### Improvements

* Improved Javascript
* Improved `WPDKTerm::term()`

#### Experimental

* Added `WPDKDynamicTableView` class
* Added column styles


---

## Version 1.4.9
### 2014-01-17

#### Bugs

* Fixed wrong get page name in autocomplete

#### Improvements

* Introducing a new behaviour in `WPDKTwitterBootstrapModal` for subclassing
* Introducing a new behaviour in `WPDKTwitterBootstrapAlert` for subclassing
* Revision some filesystem file


---

## Version 1.4.8
### 2014-01-09

#### Enhancements

* Added `WPDKTwitterBootstrapAlertType::WHITE` for WordPress 3.8 style
* Added `dismissToolTip` property in Twitter Alert
* Added `WPDKArray::arrayMatch()`
* Added `WPDKArray::arrayMatchWithKeys()`
* Added `WPDKArray::arrayMatchWithValues()`
* Added `WPDKUser::getTransientTimeWithUser()`
* Added `WPDKUser::getTransientWithUser()`
* Added `WPDKUser::getTransient()`
* Added `WPDKUser::getTransientTime()`
* Improved menu divider color for WordPress 3.8 admin themes scheme
* Added Dutch localization by Frans Pronk beheer@ifra.nl

#### Bugs

* Fixed set current user hooks
* Fixed WordPress 3.8 menu

#### Deprecated

* `wpdk_get_user_transient()` use WPDKUser::getTransientWithUser()` instead


---

## Version 1.4.7
### 2013-12-17

#### Enhancements

* Added `WPDKHTMLTag::styleInline()`
* Added `WPDKHTMLTag::sanitizeStyles()`
* Added `WPDKGlyphIcons::GOOGLE_PLUS` icon

#### Bugs

* Fixed potential wrong replacement on css compressor

#### Improvements

* Start minor fixes and adjustment for WordPress 3.8
* Improved WPDKHTML classes
* Extends WPDKTheme with new WPDKThemeSetup
* Minor filesystem optimizations

---

## Version 1.4.6
### 2013-12-02

#### Bugs

* Fixed WPDKAjaxResponse data init
* Bugfix release

#### Improvements

* Added remote address ip in user information


---

## Version 1.4.5
### 2013-11-28

#### Enhancements

* Added 16 new Glyphs Icons

#### Improvements

* Improved `wpdk_enqueue_script_page_template()` function
* Minor stable fixes

#### Experimental

* Added `WPDKHTML::endJavascriptCompress()`


---

## Version 1.4.4
### 2013-11-22

#### Bugs

* Fixed potential conflict in Javascript


---

## Version 1.4.3
### 2013-11-21

#### Improvements

* Improved `WPDKMenu::menu()` with support of any callable function or view controller class name
* Improved WPDK Javascript

#### Bugs

* Fixed potential unload sequence in Javascript


---

## Version 1.4.2
### 2013-11-19

#### Enhancements

* Added `wpdk_watchdog_log` action filter

#### Bugs

* Fixed potential fatal error on removed `_WPDKPost` class
* Several fixes for docs


---

## Version 1.4.1
### 2013-11-18

#### Enhancements

* Improved layout of textarea in user profile
* Minor performance improvements
* Minor bugs fixes

#### Under the hood

* Removed old and deprecated `WPDKPost`
* Renamed new `_WPDKPost` in `WPDKPost`
* Removed unused and deprecated files
* Removed undocument docs


---

## Version 1.4.0
### 2013-11-14

#### Enhancements

* Improved triggerHandler on swipe control. Now the event 'change' can return TRUE or FALSE to avoid the change of state
* Added in `WPDKHTMLTag` several method to sanitize and manage classes, attribute and data attributes
* Purge and cleanup old Javascript and CSS
* Added `WPDKHTML` with experimental CSS compress
* Added `WPDKWidget` prototype model class

#### Experimental

* Added `WPDKCustomTaxonomy` prototype model class
* Added `WPDKCustomPostType` prototype model class
* Added `WPDKUIControlCheckboxes`
* Added `WPDKAjaxResponse` php/javascript

---

## Version 1.3.2
### 2013-11-07

#### Enhancements
* Added filter `wpdk_listtable_viewcontroller_add_new` to remove [Add New] button from header

#### Bugs

* Fixed Warning: trim() on /wpdk-ui.php on line 342
* Minor docs fixes


---

## Version 1.3.1
### 2013-11-04

#### Enhancements

* Added `_WPDKPost::imageContent()` and `_WPDKPost::imageContentWithID()` to get the first post content image
* Added `_WPDKPost::imageAttachments()` and `_WPDKPost::imageAttachmentsWithID()` to get attachments post images
* Several improvements (no conflict) to align to Bootstrap v3.0.0
* Added several utility static methods in `WPDKHTMLTag`
* Introducing `WPDKGlyphIcons` ( see assets/fonts/ folder for detail )
* Added `WPDKMenu::addSubMenusAt()` utility method
* Added `WPDKHTMLTagImg` class
* Added `_WPDKPost::metaValue()` and `_WPDKPost::metaValues()` utility methods
* Improved `wpdk_is_user_logged_in` shortcode with roles, caps, emails and ids attributes
* Minor stable enhancements


---

## Version 1.3.0
### 2013-10-05

#### Enhancements

* Added `wpdkTooltip` jQuery extension (ovveride Twitter Bootstrap tooltip)
* Added `WPDKAjax::add()` useful static method to add an action ajax hook
* Added `WPDKObject::dump()` Useful static method to dump any variable with a <pre> HTML tag wrap

#### Improvements

* Updated Twitter Bootstrap to v3.0.0

#### Experimental

* Introducing `stripKeys()` and `fit()` in `WPDKArray` class
* Began to extend base class with `WPDKObject`

#### Bugs

* Removed some javascript conflict
* Minor stable fixes


---

## Version 1.2.0
### 2013-09-12

#### Improvements

* Restyling some CSS
* Update jQuery cookie plugin to 1.3.1
* Minor Javascript improvement for WordPress 3.6
* Added Javascript version info by cookie

#### Bugs

* Fixed jQuery tabs cookie
* Minor fixes

#### Enhancements

* Added `WPDKObject::delta()` public static method to replace `wpdk_delta_object()` the inline (deprecated) function
* Added `WPDKResult::isError()` public static method to replace `wpdk_is_error()` the inline (deprecated) function
* Added `WPDKResult::isWarning()` public static method to replace `wpdk_is_warning()` the inline (deprecated) function
* Added `WPDKResult::isStatus()` public static method to replace `wpdk_is_status()` the inline (deprecated) function
* Added `WPDKMath::isInfinity()` public static method to replace `wpdk_is_infinity()` the inline (deprecated) function
* Added `WPDKHTMLTagSelect::selected()` public static method to replace `wpdk_selected()` the inline (deprecated) function
* Added `label` key for `WPDKUIControlSubmit` control
* Added new `WPDKPreferences`, `WPDKPreferencesBranch`, `WPDKPreferencesViewController` and `WPDKPreferencesView`
* Removed Ajax loader

#### Experimental

* Added `WPDKColors` helper class for RGB/HEX color conversion

#### Deprecated

* `WPDKConfiguration` use new `WPDKPreferences`
* `WPDKConfigurationView` use new `WPDKPreferencesViewController` and `WPDKPreferencesView`


---

## Version 1.1.2
### 2013-07-09

#### Enhancements

* Added static method `WPDKMenu::addSubMenuAt()` to add a sub menu
* Improved on `WPDKPosts` class
* Minor fixes
* Restyling swipe button color (flat)

#### Experimental

* Added `WPDKTerms` and `WPDKTerm` model class

#### Bugs

* Fixed notice/warning on shortcode `wpdk_gist` when missing attribute `file`


---

## Version 1.1.1
### 2013-06-18

#### Enhancements

* Replaced jQuery .live() with .on()
* Improved on `WPDKPosts` class

#### Bugs

* Fixed and improve menu uri


---

## Version 1.1.0
### 2013-06-11

#### Enhancements

* Added LICENSE.txt
* Added `isPercentage()` method in `WPDKMath` class
* Added utility functions to enqueue script for a list of pages or template pages
* (Re) Added locked control
* Minor improves in UI control
* Cleanup documentation (for Doxygen)

#### Experimental

* Added GuruMeditation class

#### Bugs

* Fixed bad post type sending in autocomplete Javascript engine
* Added check if twitter bootstrap is already loaded



---

## Version 1.0.1
### 2013-05-23

* Minor fixes and improvements


---

## Version 1.0.0
### 2013-04-23

* First open source