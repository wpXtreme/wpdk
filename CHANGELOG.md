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

## Version 1.1.2
### 2013-07s-09

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