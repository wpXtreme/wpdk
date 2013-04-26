Welcome to WordPress Development Kit (WPDK), the first true MVC and Object Oriented framework for WordPress.
The WPDK has been create to improve WordPress kernel, enhanced its functions and class and dmake easy write plugin
and theme for WordPress evironment.

@page page_overview Overview

These are the main rules to follow in order to write a WordPress plugin/theme compatible with the WPDK.


@page            page_getting_started Getting Started

To start to develop with WPDK framework you just follow these fews rules and suggestions.

@section         section_getting_started_1 System Requirements

* Wordpress 3.4 or higher (last version is suggest)
* PHP version 5.2.4 or greater
* MySQL version 5.0 or greater

### Browser compatibility

* We suggest to update your browser always at its last version.


@section         section_getting_started_2 Installation

1. Copy the `wpdk` folder in your plugin/theme tree
2. Include the main `wpdk.php`
3. Enjoy :)

To include the frame use a similar syntax:

    // Put this row in your main file (plugin or theme)
    require_once( trailingslashit( dirname( __FILE__ ) ) . 'wpdk/wpdk.php' );


### Init

The `wpdk.php` boot the WPDK framework just it loaded.


@page   page_how_to How to write a simple Plugin

@section page_how_to_plugin_1 Overview

The main class of your WPDK plugin for WordPress, your "plugin" from now on, is declared and engaged in the
plugin main file, the one that contains the comment header used by WordPress to recognize and extract informations
about any plugin.

Let's assume that you are creating a new plugin for WordPress, named `Test Plugin`.

In the root directory of your plugin create a file named `wpdk-testplugin.php`. Inside this file, insert the WordPress
standard comment for recognize this plugin as:

    /**
     * Plugin Name: CleanFix
     * Plugin URI: https://wpxtre.me
     * Description: Clean and fix tools
     * Version: 1.0.0
     * Author: You
     * Author URI: http://your-domain.com
     */

     /* 1. Include WPDK. */
     require_once( trailingslashit( dirname( __FILE__ ) ) . 'wpdk/wpdk.php' );

     /* 2. Define of include your main plugin class. */
     if ( !class_exists( 'WPDKTestPlugin' ) ) {
       class WPDKTestPlugin extends WPDKWordPressPlugin {
       ....
       }
     }

Please note  the declaration of a class named `WPDKTestPlugin`, that extends `WPDKWordPressPlugin` class.

This is the main class of your plugin. Through it, you can control every aspect of your plugin and manage what to do
exactly where you want ( in WordPress front end, in WordPress admin area, in both worlds, and so on ). Using this
class as a manager, you can write all code you need to fulfil the scope of your plugin.

When WordPress loads your plugin through this file, this class is also engaged, through the line:

    $GLOBALS['WPDKTestPlugin'] = new WPDKTestPlugin();

at the end of the class definition. In this way, your plugin becomes up and running, obviously if it was previously
activated.

For a better comprehension, we have to deepen the behaviour of the `WPDKWordPressPlugin` class, because your main
class inherits all things it needs directly from it.




@section page_how_to_plugin_2 WPDKWordPressPlugin class

The `WPDKWordPressPlugin` class is the most important class of the whole WordPress Development Kit (WPDK). It
performs all init procedures for a plugin in order to make it compatible with the WPDK standard.

This class provides the fundamental startup of a WPDK plugin. You rarely (never) instantiate `WPDKWordPressPlugin`
object directly. Instead, you instantiate subclasses of the `WPDKWordPressPlugin` class.

So, this class **must** be used to extend the main class of your WPDK plugin: its function is to initialize the
environment in which the plugin itself operates, and record the plugin for updates incoming from WPDK Store.

In addition to initializing and recording, `WPDKWordPressPlugin` class performs automatically for you a large series
of standard procedures needed in normal coding of a WordPress standard Plugin, and gives access to a lot of
properties and methods really useful:

 * Gets directly from WordPress comments information about your plugin : plugin name, version, the text and the text
domain path.

 * Prepares a set of standard properties with paths and urls most commonly used.
 * Provides a lot of hooks to wrap (filters and actions) among the most used in WordPress environment.
 * Prepare an instance of `WPDKWatchDog` object for your own log.

All properties and methods of this class has been documented in `PHPDoc` format compatible with `Doxygen` tool, so
you can extract all detailed info and help through your PHP IDE. Describing in details the WPDK framework is outside
the scope of this document.



@section page_how_to_plugin_2_1 Filesystem guideline

In order to get all benefit from WPDK framework we suggest to you to use the standard organization for the filesystem.
The WPDK framework prepare for you a set of standard folder reference properties to access to the main plugin or theme
resource. For instance see below a standard plugin filesystem structure:

* **Your Plugin folder**
  * assets/
    * css/
      * images
    * js/
  * classes/
  * localization/

### Useful assets

In addition WPDK framework also provides a `database` folder:

* **Your Plugin folder**
  * assets/
    * css/
      * images
    * js/
  * classes/
  * localization/
  * database/

This filesystem tree is mapped into the follow properties:

    $this->path
    $this->classesPath
    $this->databasePath

In addition you can use the `http` URL version of:

    $this->url
    $this->assetsURL
    $this->cssURL
    $this->imagesURL
    $this->javascriptURL


@section page_how_to_plugin_3 The basic execution flow of a WPDK plugin

Now that you have properly obtained and configured your basic WPDK plugin framework, it's time to write your code
in order to put the right things in the right place.

Let's always assume that you are created a new WPDK plugin for WordPress, named `Test Plugin`, through the
Product Generator of WPDK Developer Center. In the root directory of your plugin, you have a file named
`wpx-testplugin.php`. Inside this file, you have the declaration of a class named `WPDKTestPlugin`, that extends
`WPDKWordPressPlugin` class. In this way:

    if ( !class_exists( 'WPDKTestPlugin' ) ) {
      class WPDKTestPlugin extends WPDKWordPressPlugin {
      ....
      }
    }




@section page_how_to_plugin_4 Plugin activation

The method `activation` of your `WPDKTestPlugin` class is invoked every time your plugin is activated. Activation is
not loading: the activation of a WordPress plugin happens just once, normally through `plugin` page of WordPress
admin area, when a user choose to activate a plugin. From that moment on, the plugin becomes *active*, and this
method is not invoked anymore.

The basic code of this method prepared for you through the Product Generator of WPDK Developer Center is this:

    /* Hook when the plugin is activate - only first time. */
    function activation() {
      /* To override. */
    }

Here you can insert the code your plugin eventually needs to execute in plugin activation phase.




@section page_how_to_plugin_5 Plugin deactivation

The method `deactivation` of your `WPDKTestPlugin` class is invoked every time your plugin is deactivated. The
deactivation of a WordPress plugin happens just once, normally through `plugin` page of WordPress admin area, when a
user choose to deactivate a plugin. From that moment on, the plugin becomes *inactive*, and this method is not
invoked anymore.

The basic code of this method prepared for you through the Product Generator of WPDK Developer Center is this:

    /* Hook when the plugin is deactivated. */
    function deactivation() {
      /* To override. */
    }

You can insert here the code your plugin eventually needs to execute in plugin deactivation phase.




@section page_how_to_plugin_6 Plugin loaded

The method `loaded` of your `WPDKTestPlugin` class is invoked every time your plugin is loaded. Loading is not
activation: every single time this plugin is loaded from WordPress environment, this method will be invoked.

The basic code of this method is not directly included in your main class. Nevertheless, it is in
`WPDKWordPressPlugin` class, so you can override it. If you need to execute some tasks every time your plugin is
loaded, create this method in your main class:

    function loaded() {
      /* You code. */
    }

and then put your own specific code in it.




@section page_how_to_plugin_6 Plugin configuration

The method `configuration` of your `WPDKTestPlugin` class is invoked every time your plugin is loaded. Loading is not
activation: every single time this plugin is loaded from WordPress environment, this method will be invoked.

Here you can put all stuffs about the configuration of your plugin; it is a commodity: you can perform the same task
in another way. Nevertheless, it can be really useful for you to use this hook, because this method is executed
AFTER** the plugin has been fully loaded from WordPress environment.

The basic code of this method prepared for you through the Product Generator of WPDK Developer Center is this:

    function configuration() {
        $this->config = WPDKTestPluginConfig::init();
    }

The instance of `WPDKTestPluginConfig` is used to load and store the plugin settings on WordPress DB, according to
WPDK framework specs. But you can safely use your own code. In any case, you can insert here the code your plugin
eventually needs to execute in configuration phase.




@section page_how_to_plugin_7 Commodity

Your main class has also some commodity methods, useful to group together some similar tasks.

In the method `defines`, you can insert the definition of all *PHP* `define` used by your class.

The basic code of this method prepared for you through the Product Generator of WPDK Developer Center is this:

    public function defines() {
        include_once( 'defines.php' );
    }

You can write your own *PHP* `define` directly in this method, or you can also put your `define` in file
`defines.php`, stored in your plugin root directory, and included by this method.

In the method `includes`, you can include all *PHP* files used by your class, through *PHP* directives `include`,
`require`, `require_once`, ecc.

The basic code of this method prepared for you through the Product Generator of WPDK Developer Center is this:

    public static function includes() {
        /* Includes all your class file here. */

        /* Core. */
        require_once( $this->classesPath . 'core/wpdk-testplugin-configuration.php' );
    }

The line:

    require_once( $this->classesPath . 'core/wpdk-testplugin--configuration.php' );

is necessary for your plugin configuration core.

Both `includes()` and `defines()` methods are invoked in the `WPDKTestPlugin` constructor.



@section     page_how_to_plugin_8 Writing code in your plugin specifically related to WordPress frontend

Let's always assume that you created a new WPDK plugin for WordPress, named `Test Plugin`. In the root directory of your
plugin, you have a file named `wpdk-testplugin.php`. Inside this file, you have the declaration of a class named
`WPDKTestPlugin`, that extends `WPDKWordPressPlugin` class.

The method `theme` of your `WPDKTestPlugin` class is called every time your plugin is loaded, after the invocation of
methods `loaded` and `configuration`; but this calling happens *if, and only if, the web request is related to the
front-end side of WordPress*: that is to say, not related in any way to the admin side of WordPress. Loading is not
activation: every single time this plugin is loaded from WordPress environment, this method will be invoked.

The basic code of this method prepared for you through the Product Generator of WPDK Developer Center is this:

    function theme() {
         /* To override. */
    }

In this method, you can insert all code your plugin needs to execute in the front-end area of your WordPress
environment. For example, you can insert here the declaration of a specific class that handles all stuffs about
front-end area. Or you can directly add here some specific hooks related to front-end WordPress filters, like
`the_title` or `the_content`.

For example, the WPXtreme WordPress plugin overwrites this method in this way:

    function theme() {
      require_once( $this->classesPath . 'frontend/wpdk-testplugin-frontend.php' );
      $frontend = new WPDKTestPluginFrontend( $this );
    }

All code of this plugin related to the WordPress frontend area is hence encapsulated inside the `WPXtremeFrontend`
object, thus giving a plugin more readability and flow comprehension.


@section     page_how_to_plugin_8 Writing code in your plugin specifically related to WordPress admin area

Let's always assume that you created a new WPDK plugin for WordPress, named `Test Plugin`. In the root directory of your
plugin, you have a file named `wpdk-testplugin.php`. Inside this file, you have the declaration of a class named
`WPDKTestPlugin`, that extends `WPDKWordPressPlugin` class.

The method `admin` of your `WPDKTestPlugin` class is called every time your plugin is loaded, after the invocation of
methods `loaded` and `configuration`; but this calling happens *if, and only if, the web request is related to the
admin side of WordPress*. Loading is not activation: every single time this plugin is loaded from WordPress
environment, this method will be invoked.

The basic code of this method prepared for you through the Product Generator of WPDK Developer Center is this:

    function admin() {
      require_once( $this->classesPath . 'admin/wpdk-testplugin-admin.php' );
      $admin = new WPDKTestPluginAdmin( $this );
    }


In this method, you can insert all code your plugin needs to execute in the admin area of your WordPress environment.
For example, you can insert here the declaration of a specific class that handles all stuffs about admin area.
Or you can directly add here some specific hooks related to administration WordPress filters, like `menu_order` or
`admin_head`.

@page            page_sample Samples

@page            page_thanks Thanks