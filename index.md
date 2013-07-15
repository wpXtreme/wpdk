Welcome to the official documentation of **WPDK**.

*WPDK* is the acronym of **WordPress Development Kit**. It is a *PHP framework* written for WordPress environment, that improves WordPress kernel and enhances its base functions and classes. The primary goal of *WPDK* is **to make productive, solid and easy to maintain the creation and the evolution of your plugins and themes**.

If you want to read an overview of *WPDK* and its purpose, please [click here](@ref page_overview).

If you want to see examples and how-to about *WPDK* in action, please [click here](@ref page_howto).

These are the main features of *WPDK*:

* **Completely object oriented** - [read more](@ref section_object_oriented)
* **MVC pattern compliant** - [read more](@ref section_mvc_compliant)
* **Graphic Controls easy to use and customize** - [read more](@ref section_cla) 
* **Internal help and documentation in PHPDoc format compatible to Doxygen syntax** - [read more](@ref section_help_doxygen)
* **Availability of tons of useful classes and helpers for enhancing your WordPress creations** - [read more](@ref section_tons_classes)
* **Autoloading internal infrastructure of the sole PHP source code involved in the single HTTP transaction** - [read more](@ref section_autoload)

Using *WPDK* framework in your WordPress creations, these are the main advantages and facilities you will have:

* **Your WordPress develop becomes easier**, thanks to the evergrowing WordPress objects that *WPDK* makes immediately available for you: quick generation of a plugin infrastructure, shortcodes manipulation, filesystem and datetime helpers, ecc.

* **Your WordPress develop becomes productive**, because you stop writing the same source code for the same purpose in your WordPress creations. Basic infrastructures for developing plugins and themes for WordPress are embedded into *WPDK*, and thus immediately available to you in any creation you develop.

* **Your WordPress develop becomes more solid**, thanks to internal structure of *WPDK*: using your preferred IDE ( PHPStorm, Eclipse, Netbeans, ecc. ), the object oriented pattern of *WPDK* allows smart intellisense, and the internal documentation written in PHPDoc format allows you full and direct help inline during develop.

* **Your WordPress develop becomes enjoyable**, because of the improving of WordPress UI, that makes pleasant the user experience of your WordPress creations. But especially because *you can quickly get efficient and powerful results*. See the [WPDK how to](@ref page_howto) for demos, or [take a look to wpXtreme plugins](https://wpxtre.me/), all developed using the *WPDK* technology.

* **Your WordPress creations becomes easy to maintain**, thanks to object oriented internal infrastructure, and *MVC pattern* compatibility. Encapsulation, inheritance, physical separation between model and view in your develop approach: all these facts make clearer, more readable and easier to maintain all your WordPress creations.

* **Your WordPress creations become up to 50% faster**, thanks to the *WPDK autoloading technology*: you can load, parse and execute the sole PHP source code necessary to fulfil the HTTP request incoming from client. *Any other WPDK PHP source code that is not involved in the HTTP transaction is simply not loaded at all*, thus dramatically increasing the speed of loading and execution of your code.

@page page_overview Overview

Developing WordPress plugins and themes in a productive, solid form that is also easy to maintain, is one of the goals (dreams) of every WordPress developer.

For this reason, wpXtreme team has created *a PHP framework* that makes this goal more easy to achieve. This framework is called **WPDK**, an acronym for *WordPress Development Kit*. It is substantially a collection of objects expressly created to allow developer to focus the idea, instead of the environment in which the idea has to be manifested.

Through its object oriented infrastructure, *WPDK* encapsulates many aspects of developing a WordPress plugin or theme, and automatically performs tasks that in general are always demanded to the right approach of a developer. 

For example, in creating the main object that contains the basic infrastructure of your plugin, using *WPDK* you can simply extends the `WPDKWordPressPlugin` object, and with this simple action you will have a great series of advantages, including:

* The immediate availability of your plugin data, like name, version and textdomain.
* The immediate availability of useful paths related to your plugin, like the plugin folder, the plugin main file name, ecc.
* The automatic creation of hooks to WordPress filters related to activation, loading and deactivation of the plugin.
* A clear flow distinction between code executed in WordPress admin area, and WordPress front end. In this way, you can quickly and easily insert your code in the right place, thus increasing readability, and plugin performance.

But you have also a great number of useful extensions for your develop: an enhanced database handling, an infrastructure to handle plugin specific configuration, many helpers on array, date and time, crypting, a powerful and dynamic way to create HTML forms and sections for a better user experience, and so on.

You can get all documentation you need about *WPDK* framework through your PHP IDE ( all source code is documented in PHPDoc format compatible with the Doxygen tool ), or navigating this documentation.

@page page_features WPDK features

@section  section_object_oriented WPDK is completely object oriented

Any element of *WPDK* has been developed following **object oriented paradigm**, according to the current *PHP* model.

This choice ensures an easier maintenance of source code, and allows you to easily extend the basic features of this framework to create solid and stable custom results. For example, see the internal <a href="hierarchy.html">Class Hierarchy</a> page: you can see that all *WPDK* elements are always encapsulated in a specific dedicated object. So you can instantiate the object you need, or extend it to create a custom behaviour: object oriented paradigm helps you to do that in a cleaner and solid way.

@section  section_mvc_compliant WPDK follows MVC pattern

In any context this approach is possible and reasonable, *WPDK* follows **MVC pattern**, described and introduced <a href="http://en.wikipedia.org/wiki/Model%E2%80%93view%E2%80%93controller">in this document</a>.

As this document remind us, this developing pattern, well known to *Objective-C* developers, ensures clarity and solidity of source code, because *phisically separates the representation of information from the user's interaction with it*.

For example, *WPDK* implements a generic `WPDKViewController` object, that handles display of data in the large area in the middle of *WordPress Administration Screen*, formerly called **the work area**. A standard content in this area is like this:

* header with icon and title - optional button add
* one or more views implemented through `WPDKView` object instances.

Two specializations of this basic object are `WPDKAboutViewController`, that handles display of credits/info about a plugin, and `WPDKjQueryTabsViewController`, that handles display of jQuery tabs.

Following *MVC* approach, you can easily build complex and powerful views, separating from them the model that phisically contains data to be shown. Maybe you can have, at the end, more source code that you really need. But you gain clarity, solidity, ease in readability and maintenance of your source code.

This approach is not embedded at all in WordPress source code. *WPDK* aims to fill this gap.

@section  section_help_doxygen Full Doxygen compatibility of WPDK documentation

All *WPDK* documentation is embedded in source code, and is written in *PHPDoc* format compatible to <a href="http://www.stack.nl/~dimitri/doxygen/index.html">Doxygen</a> syntax.

The use of this standard format means that using your preferred IDE ( *PHPStorm, Netbeans, Eclipse, Aptana, ecc.* ), you will always have help inline during develop with *WPDK*. This help is constantly enriched from *WPDK* developers with examples, better explanation of methods and properties, better description of classes, ecc. whenever this framework is updated. No need to search on the net: all documentation about stable *WPDK* objects is always available, and aligned to a well known and popular format.

@section  section_cla WPDK has all graphic controls you need

In your WordPress creations, one of the most important aspect is the user interface.

Instead of creating by yourself everytime a brand new user interface, with forms, data saving and restoring, HTML code, CSS, and so on, you can use the numerous, ever growing, and powerful **WPDK graphic controls**, immediately available for your develop.

With a simple syntax, you can use and customize input text, buttons, checkbox, radio buttons, single and multiple combo, textarea, file selector, simple label, swipe buttons. But that's not all. You can also insert *TwitterBootstrap Alert* in your own code, following a specific operation that your code executes, and leaving to *WPDK* the internal handling.

This is a key point. Because using this approach, *you can create a standard and pleasant user interface for all your WordPress creations*, leaving to *WPDK* all the internal stuff. You don't have to worry about how to put a swipe button into your WordPress administrative page: *WPDK* can make this task for you in a while.

The entire [wpXtreme ecosystem](https://wpxtre.me/) uses this *WPDK* feature to standardize user interface, and to create efficient and powerful results in all its WordPress creations.

Go to the [WPDK How-To section](@ref page_howto), see how easy is creating and using a graphic control in your own code, get the source code that use this pattern, and customize it to your own needs.

@section  section_tons_classes WPDK useful classes and helpers

In addition to specific targeted objects, *WPDK* makes available a set of objects that embed some specific features that can enhance and simplify your WordPress develop. These evergrowing available objects are called *helpers*, and include:

* array manipulation - `WPDKArray`
* crypting data - `WPDKCrypt`
* local filesystem navigation - `WPDKFilesystem`
* math functions - `WPDKMath`
* basic WordPress screen content manipulation - `WPDKScreenHelp`

@section  section_autoload WPDK embeds object autoloading

*WPDK* natively implements <a href="http://www.php.net/manual/en/language.oop5.autoload.php">PHP autoloading classes</a> feature. This is a key point, in *WPDK* technology, that deserves additional information.

As you have seen, *WPDK* is totally object oriented: that is, all *WPDK* elements are always objects, defined as *PHP classes*. Thanks to *PHP autoloading feature*, whenever a client sends an *HTTP request* that involves one or more *WPDK* objects, **WPDK loads and execute the sole source code involved in the HTTP transaction**.

Consider this: in any *HTTP transaction*, WordPress loads always itself entirely, even if the transaction does not need the 80-90% of the source code loaded and executed. *WPDK* does not behave in this way: if your *HTTP request* is directed to a web page that uses, for example, `WPDKMenu` and `WPDKArray` objects, *only those two WPDK objects are loaded and parsed*. The entire remaining *WPDK* source code is not considered at all.

This is made possible through the embedded *WPDK autoloading technology*, based on *PHP autoloading classes* above.

But that's not all. If you develop your WordPress creation following some simple rules ( that include a full object oriented approach to your develop ), the *WPDK autoloading technology* becomes available also to your creation, even it is totally external to *WPDK*. This is another key point, that can dramatically increasing the speed of loading and execution of your code, *up to 50% faster than normal file inclusion*.

[Click here](questo link da fare) to see how easy is developing your WordPress creation using *WPDK* for automatically gaining *WPDK autoloading technology* in your own code.

@page page_view_controller A little in-depth analysis of View and View Controller sub-pattern

In *MVC pattern*, there is a separation between model and its views. In pure pattern, a model interacts with its views through a controller(s).

*WPDK* implements this pattern by the use of two fundamental classes, that a developer can (have to) specialize extending them with its own needs: `WPDKView`, and `WPDKViewController` classes.

`WPDKViewController` class exposes methods and properties to manage the whole *HTML* display into the work area of WordPress Administration Screen. Upon request, it provides a view that can be displayed or interacted with. Often, this view is the root view for a more complex hierarchy of views, implemented by specialized instances of `WPDKView` class. The view controller acts as the central coordinating agent for the view hierarchy, handling exchanges between the views and any relevant controller or data objects.

This is the typical hierarchy handled by a specialized instance of `WPDKViewController` class:

 * header with icon and title + optional button add = an instance of `WPDKHeaderView` class.
 * one or more views, that can be specialized instances of `WPDKView` class.

Overriding `display` method in specialized `WPDKViewController` class allows a developer to set a custom content for its instances. The same for `WPDKView` class.

Why using this complexity? For `WPDKView` class, to encapsulate specialized behaviours *that can be reusable anywhere*, simply creating a new instance of the specialized `WPDKView` class that implements them. For `WPDKViewController` class, to centralize handling of low-level *HTML* into the whole work area of WordPress Administration Screen; you don't need to create everytime by hand all the `<div></div>` parts of your *HTML* view into the work area: simply set your own specific *HTML* content overriding `display` method, and then call it, if necessary.

To see how all this theory turns to action, go to [WPDK How-To](@ref page_howto) and get the source code that use this pattern.

@page page_install Installing WPDK in your WordPress environment

To start developing with *WPDK framework*, first of all you have to install *WPDK* in your WordPress environment. Please follow these simple rules to accomplish this task.

1. Verify the system requirements:
  * Wordpress 3.4 or higher (last stable version is highly suggested) - **Note: WordPress MU is not yet supported.**
  * PHP version 5.2.4 or greater
  * MySQL version 5.0 or greater
  * We suggest to update your browser always at its last version, because *WPDK* uses *javascript* language to accomplish some tasks.
2. Download the zip of *WPDK* framework from official GitHub repository [clicking here](https://github.com/wpXtreme/wpdk/tree/production). This is the official stable (production) branch of the framework. If you want to download the framework from other branches of this repo, please remember that *their source code is still experimental*, and then it is not ready to be used in any production environment.
3. Unzip the framework. You will have its root directory named `wpdk-production`
4. Copy the entire `wpdk-production` folder in the `wp-content/plugins` directory of your WordPress environment.

Done. *WPDK* framework is installed in your environment.

**Please note that you won't see WPDK framework in your WordPress plugins dashboard, because WPDK does not need to be activated. Once installed, WPDK is immediately ready and available.**

@page page_howto WPDK How-tos

Here you can find a set of examples and how-tos about *WPDK* in action into a WordPress environment.

Any example and/or how-to is available to anyone through GitHub interface. If you want to download and use these examples in your environment, you naturally have to install *WPDK* first: please [follow these rules](@ref page_install) to do that.

* Hello World! WordPress plugin using *WPDK* - the basic - [click here](@ref section_hello_world_1)
* Hello World! WordPress plugin using *WPDK* - intermediate - [click here](@ref section_hello_world_2)
* Sample #1 of WordPress plugin using *WPDK* - [click here](@ref section_plugin_1)
* Sample #2 of WordPress plugin using *WPDK* - [click here](@ref section_plugin_2)

@section  section_hello_world_1 Hello World! WordPress plugin using WPDK - the basic

This how-to creates a simple WordPress plugin and generates, through *WPDK* object `WPDKMenu`, an `Hello World!` menu item in the administration area of your WordPress environment. This code is very simple, and shows a basic, not *invasive* way of using *WPDK* in developing a WordPress plugin.

Please follow these instructions to see this how-to in action in your WordPress environment.

1. If not already done, install *WPDK* in your environment - please [follow these rules](@ref page_install) to do that.
2. Download the zip of this how-to from official GitHub repository [clicking here](https://github.com/wpXtreme/wpdk-sample-menu-1).
3. Unzip this how-to. You will have its root directory named `wpdk-sample-menu-1-master`
4. Copy the entire `wpdk-sample-menu-1-master` folder in the `wp-content/plugins` directory of your WordPress environment.
5. Activate the plugin in your WordPress administration area: a new `Hello World!` menu item will appear in the main navigation menu at the left side of the screen.
6. The source code of this plugin is well documented, and can be an easy starting point for your develop with *WPDK*.

@section  section_hello_world_2 Hello World! WordPress plugin using WPDK - intermediate

This how-to creates a simple WordPress plugin and generates, through *WPDK* object `WPDKMenu`, an `Hello World!` menu item in the administration area of your WordPress environment. This code is relatively more complex than first Hello World! example: the plugin menu is made of two submenu items, each one with its own function called whenever the item is clicked; and the main menu item has an attached icon, as other main menu item in WordPress menu of Administration Screen.

The plugin menu is built through another *WPDK* way of creating menu in Administration Screen: as an array, with specific array items, processed by `renderByArray` static method of `WPDKMenu` class. It is another powerful, readable and easy way to accomplish this task with *WPDK*.

Please follow these instructions to see this how-to in action in your WordPress environment.

1. If not already done, install *WPDK* in your environment - please [follow these rules](@ref page_install) to do that.
2. Download the zip of this how-to from official GitHub repository [clicking here](https://github.com/wpXtreme/wpdk-sample-menu-2).
3. Unzip this how-to. You will have its root directory named `wpdk-sample-menu-2-master`
4. Copy the entire `wpdk-sample-menu-2-master` folder in the `wp-content/plugins` directory of your WordPress environment.
5. Activate the plugin in your WordPress administration area: a new `Hello World!` menu item will appear in the main navigation menu at the left side of the screen, with an icon at the left, and two related submenu items.
6. The source code of this plugin is well documented, and can be an easy starting point for your develop with *WPDK*.

@section  section_plugin_1 Sample #1 of WordPress plugin using WPDK

This how-to is the first basic sample of a WordPress plugin developed using *WPDK*: a little more complex sample than #1 and #2 above.

This is the structure:

* Create a main menu with two submenu items, through methods `addSubMenu` and `render` of `WPDKMenu` class.
* Connect each submenu item to a specific specialized `WPDKViewController` class instance. These specialized instances handle the *HTML* output shown in the work area of WordPress Administration Screen, and their source code is in `plugin_root_dir/classes/wpdk-sample-vc.php` file.
* Customize method `display` of any specialized `WPDKViewController` class instance, in order to show my own content whenever the related submenu item is clicked.

Please follow these instructions to see this how-to in action in your WordPress environment.

1. If not already done, install *WPDK* in your environment - please [follow these rules](@ref page_install) to do that.
2. Download the zip of this how-to from official GitHub repository [clicking here](https://github.com/wpXtreme/wpdk-sample-plugin-1).
3. Unzip this how-to. You will have its root directory named `wpdk-sample-plugin-1-master`
4. Copy the entire `wpdk-sample-plugin-1-master` folder in the `wp-content/plugins` directory of your WordPress environment.
5. Activate the plugin in your WordPress administration area: a new `WPDK Plug#1` menu item will appear in the main navigation menu at the left side of the screen, with an icon at the left, and two related submenu items. Click them to see result.
6. The source code of this plugin is well documented, and can be an easy starting point for your develop with *WPDK*.

@section  section_plugin_2 Sample #2 of WordPress plugin using WPDK

This how-to is the second sample of a WordPress plugin developed using *WPDK*. This is the structure:

* Create a main menu with two submenu items, through methods `addSubMenu` and `render` of `WPDKMenu` class.
* The first submenu item is connected to a specific specialized `WPDKViewController` class instance. This specialized instance embeds a specific `WPDKView` class that shows all the available **WPDK graphic controls** you can easily use in your own code. Following this simple use case, you can easily build and customize the views that control all aspects and parameters of your WordPress creation. This sample doesn't have a way to store user choices in a custom configuration: it is only a container of all available *WPDK graphic controls*. Next how-to will show you how to save and reload your own preferences using the same internal architecture.

Please follow these instructions to see this how-to in action in your WordPress environment.

1. If not already done, install *WPDK* in your environment - please [follow these rules](@ref page_install) to do that.
2. Download the zip of this how-to from official GitHub repository [clicking here](https://github.com/wpXtreme/wpdk-sample-plugin-2).
3. Unzip this how-to. You will have its root directory named `wpdk-sample-plugin-2-master`
4. Copy the entire `wpdk-sample-plugin-2-master` folder in the `wp-content/plugins` directory of your WordPress environment.
5. Activate the plugin in your WordPress administration area: a new `WPDK Plug#2` menu item will appear in the main navigation menu at the left side of the screen, with an icon at the left, and two related submenu items. Click them to see result.
6. The source code of this plugin is well documented, and can be an easy starting point for your develop with *WPDK*.

@page   page_writing_wpdk_plugin How to write a WordPress plugin with full WPDK support

@section page_how_to_plugin_1 Overview

Writing a WordPress plugin with full *WPDK* support means using all the *WPDK* features in your code to create a solid product, that is fast to execute, and easy to maintain and evolve.

For reaching this goal, you have to follow some simple rules and you have to use some simple tricks in your source code. All of them are described in this page, step by step. If you don't follow them, you can anyway use *WPDK* for specific tasks; but you loose all the major gains *WPDK* is ready to give you.

First of all, you have to completely develop your plugin in an object oriented approach, extending some basic *WPDK* object. This is necessary not only for a better encapsulation of data, but also to make a full use of [WPDK autoloading technology](@ref section_autoload).

Let's assume that you are creating a new WordPress plugin, named `Test Plugin`.

Your first step is defining the main class of your WordPress plugin, your *plugin* from now on. You can define it directly in the plugin main file, the one that contains *the comment header* used by WordPress to recognize and extract informations about any plugin.

In the root directory of your plugin, create a file named `testplugin.php`. Inside this file, insert the WordPress standard comment for recognize this plugin, and the definition of the main class:

    /**
      * Plugin Name: Test Plugin
      * Plugin URI: http://your-domain.com
      * Description: my WordPress plugin with full WPDK support
      * Version: 1.0.0
      * Author: You
      * Author URI: http://your-domain.com
      */

    // Include WPDK framework - the root directory name of WPDK may be different.
    // Please change the line below according to your environment.
    require_once( trailingslashit( dirname( __FILE__ ) ) . 'wpdk-production/wpdk.php' );

    // Define of include your main plugin class
    if( !class_exists( 'TestPlugin' ) ) {
      class TestPlugin extends WPDKWordPressPlugin {
      }
    }

Please note the declaration of a class named `TestPlugin`, that extends `WPDKWordPressPlugin` class. We have to deepen the behaviour of the `WPDKWordPressPlugin` class, because your main class inherits all things it needs directly from it.

The `WPDKWordPressPlugin` class is one of the most important class of the whole *WPDK framework*. It encapsulates all init procedures for a plugin, and provides the fundamental startup of a plugin in order to make it full compatible with the WPDK standard. You rarely (never) instantiate `WPDKWordPressPlugin` object directly. Instead, you instantiate subclasses of the `WPDKWordPressPlugin` class.

In *WPDK* environment, this class **must** be used to extend the main class of a plugin: its goal is to initialize the environment in which the plugin itself operates.

In addition to initializing, `WPDKWordPressPlugin` class performs automatically for you a large series of standard procedures needed in normal coding of a WordPress standard Plugin, and gives access to a lot of properties and methods really useful for your develop:

* Gets directly from WordPress comments, and stores in its properties, information about your plugin : plugin name, version, the text and the text domain path.
* Prepares a set of standard properties with most commonly used paths and urls.
* Provides a lot of hooks to wrap (filters and actions) among the most used in WordPress environment.
* Prepares an instance of `WPDKWatchDog` object for your own log.

All properties and methods of this class are documented in **PHPDoc** format compatible with *Doxygen* tool, so you can extract all detailed info and help through your PHP IDE.

The class `TestPlugin`, extending `WPDKWordPressPlugin`, is the main class of your plugin. Through it, you can control every aspect of your plugin and manage what to do
exactly where you want ( in WordPress front end, in WordPress admin area, in both worlds, and so on ). Using this class as a manager, you can write all code you need to fulfil the scope of your plugin.

When WordPress loads your plugin through this file, this class have also to be engaged, through the line:

    $GLOBALS['TestPlugin'] = new TestPlugin();

that you have to put at the end of the class definition. In this way, your plugin becomes up and running, obviously if it was previously activated. Now our test code becomes this:

    /**
      * Plugin Name: Test Plugin
      * Plugin URI: http://your-domain.com
      * Description: my WordPress plugin with full WPDK support
      * Version: 1.0.0
      * Author: You
      * Author URI: http://your-domain.com
      */

    // Include WPDK framework - the root directory name of WPDK may be different.
    // Please change the line below according to your environment.
    require_once( trailingslashit( dirname( __FILE__ ) ) . 'wpdk-production/wpdk.php' );

    // Define of include your main plugin class
    if( !class_exists( 'TestPlugin' ) ) {
      class TestPlugin extends WPDKWordPressPlugin {
 
        /**
          * Create an instance of TestPlugin class
          */
        public function __construct( $file ) {

          parent::__construct( $file );

        }

      }
    }

    $GLOBALS['TestPlugin'] = new TestPlugin();

Please note that creating an instance of `TestPlugin` class *does not mean activating the whole plugin into the WordPress environment*. The physical activation of plugin is as usual demanded to normal WordPress flow.

@section page_how_to_plugin_2_1 Filesystem guideline

In order to get all benefits from *WPDK framework* we suggest you to use a standard organization for your plugin filesystem. The *WPDK framework* prepares for you a set of standard folder reference properties accessible from `TestPlugin` main class instance. The standard plugin filesystem recognised from *WPDK* has this form:

* **Your Plugin main folder**
  * assets/
    * css/
      * images
    * js/
  * classes/
  * localization/

### Useful assets

In addition, *WPDK framework* also provides a `database` folder reference:

* **Your Plugin folder**
  * assets/
    * css/
      * images
    * js/
  * classes/
  * localization/
  * database/

This filesystem tree is mapped into the following properties of the `TestPlugin` main class:

    $this->path
    $this->classesPath
    $this->databasePath

In addition, you have also the following `http` URL properties:

    $this->url
    $this->assetsURL
    $this->cssURL
    $this->imagesURL
    $this->javascriptURL

All of them are accessible through the instance of `TestPlugin` class you defined above.

> **Remember that mapping is not dynamic!** Paths are not dynamically extracted from your own folder tree; for obvious reasons, all filesystem properties have a constant init. `$this->cssURL`, for example, is always equal to `trailingslashit( plugin_dir_url( $main_file ) ) . "css/";`.

@section page_how_to_plugin_4 Plugin activation

The method `activation` of your `TestPlugin` class is invoked every time your plugin is activated. Please note that **activation is not loading**: the activation of a WordPress plugin happens just once, normally through `plugin` page of WordPress admin area, when a user choose to activate a plugin. From that moment on, the plugin becomes *active*, and this method is not invoked anymore.

To override this method and put all the code you need to execute whenever your plugin is activated, you have to declare it into `TestPlugin` class definition, in this way:


    /**
      * Plugin Name: Test Plugin
      * Plugin URI: http://your-domain.com
      * Description: my WordPress plugin with full WPDK support
      * Version: 1.0.0
      * Author: You
      * Author URI: http://your-domain.com
      */

    // Include WPDK framework - the root directory name of WPDK may be different.
    // Please change the line below according to your environment.
    require_once( trailingslashit( dirname( __FILE__ ) ) . 'wpdk-production/wpdk.php' );

    // Define of include your main plugin class
    if( !class_exists( 'TestPlugin' ) ) {
      class TestPlugin extends WPDKWordPressPlugin {
      
        /**
          * Create an instance of TestPlugin class
          */
        public function __construct( $file ) {

          parent::__construct( $file );

        }

        /* Called when the plugin is activate - only first time. */
        function activation() {
          /* To override. */
        }

      }
    }

    $GLOBALS['TestPlugin'] = new TestPlugin();

In `activation` method you can insert the code your plugin eventually needs to execute in plugin activation phase.

@section page_how_to_plugin_5 Plugin deactivation

The method `deactivation` of your `TestPlugin` class is invoked every time your plugin is deactivated. The deactivation of a WordPress plugin happens just once, normally through `plugin` page of WordPress admin area, when a user choose to deactivate a plugin. From that moment on, the plugin becomes *inactive*, and this method is not invoked anymore.

To override this method and put all the code you need to execute whenever your plugin is deactivated, you have to declare it into `TestPlugin` class definition, in this way:

    /**
      * Plugin Name: Test Plugin
      * Plugin URI: http://your-domain.com
      * Description: my WordPress plugin with full WPDK support
      * Version: 1.0.0
      * Author: You
      * Author URI: http://your-domain.com
      */

    // Include WPDK framework - the root directory name of WPDK may be different.
    // Please change the line below according to your environment.
    require_once( trailingslashit( dirname( __FILE__ ) ) . 'wpdk-production/wpdk.php' );

    // Define of include your main plugin class
    if( !class_exists( 'TestPlugin' ) ) {
      class TestPlugin extends WPDKWordPressPlugin {
      
        /**
          * Create an instance of TestPlugin class
          */
        public function __construct( $file ) {

          parent::__construct( $file );

        }

        /* Called when the plugin is activate - only first time. */
        function activation() {
          /* To override. */
        }

        /* Called when the plugin is deactivated. */
        function deactivation() {
          /* To override. */
        }

      }
    }

    $GLOBALS['TestPlugin'] = new TestPlugin();

In `deactivation` method you can insert the code your plugin eventually needs to execute in plugin deactivation phase.

@section page_how_to_plugin_6 Plugin loaded

The method `loaded` of your `TestPlugin` class is invoked every time your plugin is loaded. Please note that **loading is not activation**: every single time this plugin is loaded by WordPress environment, this method will be invoked.

To override this method and put all the code you need to execute whenever your plugin is loaded, you have to declare it into `TestPlugin` class definition, in this way:

    /**
      * Plugin Name: Test Plugin
      * Plugin URI: http://your-domain.com
      * Description: my WordPress plugin with full WPDK support
      * Version: 1.0.0
      * Author: You
      * Author URI: http://your-domain.com
      */

    // Include WPDK framework - the root directory name of WPDK may be different.
    // Please change the line below according to your environment.
    require_once( trailingslashit( dirname( __FILE__ ) ) . 'wpdk-production/wpdk.php' );

    // Define of include your main plugin class
    if( !class_exists( 'TestPlugin' ) ) {
      class TestPlugin extends WPDKWordPressPlugin {
      
        /**
          * Create an instance of TestPlugin class
          */
        public function __construct( $file ) {

          parent::__construct( $file );

        }

        /* Called when the plugin is activate - only first time. */
        function activation() {
          /* To override. */
        }

        /* Called when the plugin is deactivated. */
        function deactivation() {
          /* To override. */
        }
        
        /* Called when the plugin is loaded. */
        function loaded() {
          /* To override. */
        }
        
      }
    }

    $GLOBALS['TestPlugin'] = new TestPlugin();

In `loaded` method you can insert the code your plugin eventually needs to execute in plugin loading.

@section page_how_to_plugin_7 Plugin configuration

The method `configuration` of your `TestPlugin` class is invoked every time your plugin is loaded. Please note that **loading is not activation**: every single time this plugin is loaded from WordPress environment, this method will be invoked.

Here you can put all stuffs about the configuration of your plugin: for example, the load of current plugin configuration from WordPress DB; it is a commodity: you can perform the same task in another way. Nevertheless, it can be really useful for you to use this hook, because this method is always executed **AFTER the plugin has been fully loaded from WordPress environment**.

To override this method and put all the code you need to execute whenever your plugin is loaded, you have to declare it into `TestPlugin` class definition, in this way:

    /**
      * Plugin Name: Test Plugin
      * Plugin URI: http://your-domain.com
      * Description: my WordPress plugin with full WPDK support
      * Version: 1.0.0
      * Author: You
      * Author URI: http://your-domain.com
      */

    // Include WPDK framework - the root directory name of WPDK may be different.
    // Please change the line below according to your environment.
    require_once( trailingslashit( dirname( __FILE__ ) ) . 'wpdk-production/wpdk.php' );

    // Define of include your main plugin class
    if( !class_exists( 'TestPlugin' ) ) {
      class TestPlugin extends WPDKWordPressPlugin {
      
        /**
          * Create an instance of TestPlugin class
          */
        public function __construct( $file ) {

          parent::__construct( $file );

        }

        /* Called when the plugin is activate - only first time. */
        function activation() {
          /* To override. */
        }

        /* Called when the plugin is deactivated. */
        function deactivation() {
          /* To override. */
        }
        
        /* Called when the plugin is loaded. */
        function loaded() {
          /* To override. */
        }

        /* Called when the plugin is fully loaded. */
        function configuration() {
          /* To override. */    
        }
    
      }
    }

    $GLOBALS['TestPlugin'] = new TestPlugin();

In `configuration` method you can insert the code your plugin eventually needs to execute about its configuration.

@section page_how_to_plugin_8 Commodity

Your `TestPlugin` main class has also some commodity methods, useful to group together some similar tasks.

For example, with `_defines` method, you can insert the definition of all *PHP* `define` used by your class. You can write your own *PHP* `define` directly in this method, or you can also put your `define` in file `defines.php`, stored whatever in your plugin folders tree, and included by this method.

`_defines()` internal method has to be invoked in the `TestPlugin` constructor. It is not attached to any WordPress action.

Your code, now:

    /**
      * Plugin Name: Test Plugin
      * Plugin URI: http://your-domain.com
      * Description: my WordPress plugin with full WPDK support
      * Version: 1.0.0
      * Author: You
      * Author URI: http://your-domain.com
      */

    // Include WPDK framework - the root directory name of WPDK may be different.
    // Please change the line below according to your environment.
    require_once( trailingslashit( dirname( __FILE__ ) ) . 'wpdk-production/wpdk.php' );

    // Define of include your main plugin class
    if( !class_exists( 'TestPlugin' ) ) {
      class TestPlugin extends WPDKWordPressPlugin {
      
        /**
          * Create an instance of TestPlugin class
          */
        public function __construct( $file ) {

          parent::__construct( $file );

          $this->_defines();

        }

        /**
          * Include the external file with my own defines
          */
        private function _defines() {
          include_once( 'defines.php' );
        }

        /* Called when the plugin is activate - only first time. */
        function activation() {
          /* To override. */
        }

        /* Called when the plugin is deactivated. */
        function deactivation() {
          /* To override. */
        }
        
        /* Called when the plugin is loaded. */
        function loaded() {
          /* To override. */
        }

        /* Called when the plugin is fully loaded. */
        function configuration() {
          /* To override. */    
        }
    
      }
    }

    $GLOBALS['TestPlugin'] = new TestPlugin( __FILE__ );

@section     page_how_to_plugin_9 Writing code in your plugin specifically related to WordPress frontend

The method `theme` of your `TestPlugin` class is called every time your plugin is loaded, after the invocation of `loaded` and `configuration` methods; but this calling happens **if, and only if, the web request is related to the front-end side of WordPress**: that is to say, not related in any way to the admin side of WordPress. Loading is not activation: every single time this plugin is loaded from WordPress environment, this method will be invoked.

To override this method and put all the code you need to execute whenever your plugin is loaded, you have to declare it into `TestPlugin` class definition, in this way:

    /**
      * Plugin Name: Test Plugin
      * Plugin URI: http://your-domain.com
      * Description: my WordPress plugin with full WPDK support
      * Version: 1.0.0
      * Author: You
      * Author URI: http://your-domain.com
      */

    // Include WPDK framework - the root directory name of WPDK may be different.
    // Please change the line below according to your environment.
    require_once( trailingslashit( dirname( __FILE__ ) ) . 'wpdk-production/wpdk.php' );

    // Define of include your main plugin class
    if( !class_exists( 'TestPlugin' ) ) {
      class TestPlugin extends WPDKWordPressPlugin {
      
        /**
          * Create an instance of TestPlugin class
          */
        public function __construct( $file ) {

          parent::__construct( $file );

          $this->_defines();

        }

        /**
          * Include the external file with my own defines
          */
        private function _defines() {
          include_once( 'defines.php' );
        }

        /* Called when the plugin is activate - only first time. */
        function activation() {
          /* To override. */
        }

        /* Called when the plugin is deactivated. */
        function deactivation() {
          /* To override. */
        }
        
        /* Called when the plugin is loaded. */
        function loaded() {
          /* To override. */
        }

        /* Called when the plugin is fully loaded. */
        function configuration() {
          /* To override. */    
        }

        /* Called only if the web request is related to the front-end side of WordPress */
        function theme() {
          /* To override. */
        }
    
      }
    }

    $GLOBALS['TestPlugin'] = new TestPlugin( __FILE__ );

In `theme` method, you can insert all code your plugin needs to execute in the front-end area of your WordPress environment. For example, you can insert here the declaration of a specific class instance that handles all stuffs about front-end area. Or you can directly add here some specific hooks related to front-end WordPress filters, like `the_title` or `the_content`. This gives your plugin more readability and flow comprehension.

@section     page_how_to_plugin_10 Writing code in your plugin specifically related to WordPress admin area

The method `admin` of your `TestPlugin` class is called every time your plugin is loaded, after the invocation of methods `loaded` and `configuration`; but this calling happens **if, and only if, the web request is related to the admin side of WordPress**. Loading is not activation: every single time this plugin is loaded from WordPress
environment, this method will be invoked.

To override this method and put all the code you need to execute whenever your plugin is loaded, you have to declare it into `TestPlugin` class definition, in this way:

    /**
      * Plugin Name: Test Plugin
      * Plugin URI: http://your-domain.com
      * Description: my WordPress plugin with full WPDK support
      * Version: 1.0.0
      * Author: You
      * Author URI: http://your-domain.com
      */

    // Include WPDK framework - the root directory name of WPDK may be different.
    // Please change the line below according to your environment.
    require_once( trailingslashit( dirname( __FILE__ ) ) . 'wpdk-production/wpdk.php' );

    // Define of include your main plugin class
    if( !class_exists( 'TestPlugin' ) ) {
      class TestPlugin extends WPDKWordPressPlugin {
      
        /**
          * Create an instance of TestPlugin class
          */
        public function __construct( $file ) {

          parent::__construct( $file );

          $this->_defines();

        }

        /**
          * Include the external file with my own defines
          */
        private function _defines() {
          include_once( 'defines.php' );
        }

        /* Called when the plugin is activate - only first time. */
        function activation() {
          /* To override. */
        }

        /* Called when the plugin is deactivated. */
        function deactivation() {
          /* To override. */
        }
        
        /* Called when the plugin is loaded. */
        function loaded() {
          /* To override. */
        }

        /* Called when the plugin is fully loaded. */
        function configuration() {
          /* To override. */    
        }

        /* Called only if the web request is related to the front-end side of WordPress */
        function theme() {
          /* To override. */
        }

        /* Called only if the web request is related to the admin side of WordPress */
        function admin() {
          /* To override. */
        }
    
      }
    }

    $GLOBALS['TestPlugin'] = new TestPlugin( __FILE__ );

In `admin` method, you can insert all code your plugin needs to execute in the admin area of your WordPress environment. For example, you can insert here the declaration of a specific class instance that handles all stuffs about admin area ( plugin main menu item creation, css and javascript enqueuing, ecc. ) . Or you can directly add here some specific hooks related to administration WordPress filters, like `menu_order` or `admin_head`.  This gives your plugin more readability and flow comprehension.

@section     page_how_to_plugin_11 Introduction to available plugin enhancements 

### WPDKWordPressAdmin class

*WPDK* has another most important class that encapsulates tasks to perform in the admin side of WordPress: `WPDKWordPressAdmin`. See related documentation or [the How-To section](@ref page_howto) for all details.

### WPDK autoloading technology

This is a basic guide, that shows you how to develop your plugin in order to made it full compatible with all *WPDK* features. To make use of powerful *WPDK autoloading technology*, see [the How-To section](@ref page_howto) for examples of use of this technology integrated in this plugin model.

@page            page_credits Credits
