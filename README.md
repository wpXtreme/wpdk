# :pill: WPDK (WordPress Development Kit)

Awesome WordPress Development Kit framework

## CHANGELOG
See [here](CHANGELOG.md) for detail.

## Description

Welcome to WordPress Development Kit (WPDK), the first true MVC and Object Oriented framework for WordPress.
The WPDK has been create to improve WordPress kernel, enhanced its functions and class and dmake easy write plugin
and theme for WordPress evironment.

### Requirements

* Wordpress 3.4 or higher (last version is suggest)
* PHP version 5.2.4 or greater
* MySQL version 5.0 or greater

### Getting Started

1. Copy the `wpdk` folder in your plugin directory.
2. Include the main `wpdk.php`
3. Enjoy

### Organization of the file system

The file system structure is not binding: following this standard nomenclature and organization is strongly
recommended, in order to make it readable and compliant with other plugins.

Here it is:

**your_plugin_directory/**
* **wpdk/**
* index.php
* (main).php

### Folders

#### wpdk

Include the latest WPDK version.

#### index.php

_Silent is golden._

This file is here only for security reason.

#### (main).php

This is the main file of the plugin. You can named it as you like. In this file you have:

* Setting the stadanrd WordPress comments
* Including the WPDK framework
* Start your plugin

````php
require_once( trailingslashit( dirname( __FILE__ ) ) . 'wpdk/wpdk.php' );
````

### Issue and Bug Tracking

Have a :bug: bug? Please create an issue here on GitHub that conforms with [our guidelines](https://github.com/wpdk/wpdk/blob/master/ISSUE-GUIDELINES.md).

https://github.com/wpdk/wpdk/issues
