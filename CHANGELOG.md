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

## Version 1.3.3
### 2013-11-??

#### Enhancements

* Added `WPDKHTML` with experimental CSS compress
* Added `WPDKWidget` prototype model class
* Added `WPDKCustomPostType` prototype model class


## Version 1.3.2
### 2013-11-07

#### Enhancements
* Added filter `wpdk_listtable_viewcontroller_add_new` to remove [Add New] button from header

#### Bugs

* Fixed Warning: trim() on /wpdk-ui.php on line 342
* Minor docs fixes


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


## Version 1.1.1
### 2013-06-18

#### Enhancements

* Replaced jQuery .live() with .on()
* Improved on `WPDKPosts` class

#### Bugs

* Fixed and improve menu uri


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



## Version 1.0.1
### 2013-05-23

* Minor fixes and improvements


## Version 1.0.0
### 2013-04-23

* First open source