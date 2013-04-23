/**
 * @mainpage   Introducing WPDK
 *
 * @section    introduction Introduction
 *
 * Welcome to WordPress Development Kit (WPDK), the first true MVC and Object Oriented framework for WordPress.
 * The WPDK has been create to improve WordPress kernel, enhanced its functions and class and dmake easy write plugin
 * and theme for WordPress evironment.
 *
 * Disclaimer: IMPORTANT: This wpXtreme software is supplied to you by wpXtreme
 * Inc. ("wpXtreme") in consideration of your agreement to the following
 * terms, and your use, installation, modification or redistribution of
 * this wpXtreme software constitutes acceptance of these terms.  If you do
 * not agree with these terms, please do not use, install, modify or
 * redistribute this wpXtreme software.
 *
 * In consideration of your agreement to abide by the following terms, and
 * subject to these terms, wpXtreme grants you a personal, non-exclusive
 * license, under wpXtreme's copyrights in this original wpXtreme software (the
 * "wpXtreme Software"), to use, reproduce, modify and redistribute the wpXtreme
 * Software, with or without modifications, in source and/or binary forms;
 * provided that if you redistribute the wpXtreme Software in its entirety and
 * without modifications, you must retain this notice and the following
 * text and disclaimers in all such redistributions of the wpXtreme Software.
 * Neither the name, trademarks, service marks or logos of wpXtreme Inc. may
 * be used to endorse or promote products derived from the wpXtreme Software
 * without specific prior written permission from wpXtreme.  Except as
 * expressly stated in this notice, no other rights or licenses, express or
 * implied, are granted by wpXtreme herein, including but not limited to any
 * patent rights that may be infringed by your derivative works or by other
 * works in which the wpXtreme Software may be incorporated.
 *
 * The wpXtreme Software is provided by wpXtreme on an "AS IS" basis. WPXTREME
 * MAKES NO WARRANTIES, EXPRESS OR IMPLIED, INCLUDING WITHOUT LIMITATION
 * THE IMPLIED WARRANTIES OF NON-INFRINGEMENT, MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE, REGARDING THE WPXTREME SOFTWARE OR ITS USE AND
 * OPERATION ALONE OR IN COMBINATION WITH YOUR PRODUCTS.
 *
 * IN NO EVENT SHALL WPXTREME BE LIABLE FOR ANY SPECIAL, INDIRECT, INCIDENTAL
 * OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) ARISING IN ANY WAY OUT OF THE USE, REPRODUCTION,
 * MODIFICATION AND/OR DISTRIBUTION OF THE WPXTREME SOFTWARE, HOWEVER CAUSED
 * AND WHETHER UNDER THEORY OF CONTRACT, TORT (INCLUDING NEGLIGENCE),
 * STRICT LIABILITY OR OTHERWISE, EVEN IF WPXTREME HAS BEEN ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 *
 *
 * @page            page_1 Getting Started
 *
 * @section         section_1_1 How to use WPDK to write a Plugin
 *
 * These are the main rules to follow in order to write a WordPress plugin with WPDK framework.
 *
 * ### System Requirements
 *
 * * Wordpress 3.4 or higher (last version is suggest)
 * * PHP version 5.2.4 or greater
 * * MySQL version 5.0 or greater
 *
 * ### Browser compatibility
 *
 * * We suggest to update your browser always at its last version.
 *
 * ### Organization of the file system
 *
 * The file system structure is not binding: following this standard nomenclature and organization is strongly
 * recommended, in order to make it readable and compliant with other plugins.
 *
 * Here it is:
 *
 * **your_plugin_dire/**
 * * **wpdk/**
 * * index.php
 * * (main).php
 *
 * ### Folders
 *
 * #### WPDK
 *
 * Include the latest WPDK version.
 *
 * #### index.php
 *
 * _Silent is golden._
 *
 * This file is here only for security reason.
 *
 * #### (main).php
 *
 * This is the main file of the plugin. You can named it as you like. In this file you have:
 *
 * * Setting the stadanrd WordPress comments
 * * Including the WPDK framework
 * * Start your plugin
 *
 * ##### header
 * All WordPress plugins, by definition, must have a header - in the form of PHP comments - which allow them to be
 * recognized by WordPress core. In wpXtreme environment this header must be complete because some key information is
 * retrieved from this series of comments.
 *
 * A standard header detectable in an open plugin is so shaped:
 *
 *     /**
 *      * Plugin Name: wpx CleanFix
 *      * Plugin URI: https://wpxtre.me
 *      * Description: Clean and fix tools
 *      * Version: 1.0
 *      * Author: wpXtreme, Inc.
 *      * Author URI: https://wpxtre.me
 *      * /
 *
 * If you get the file containing this main header through Product Generator of wpXtreme Developer Center, you'll have
 * all header fields correctly filled with informations you choose: plugin name, plugin description, plugin author,
 * plugin author URI.
 *
 *     /**
 *      * Plugin Name: Your Plugin Name
 *      * Plugin URI: https://wpxtre.me
 *      * Description: Your Plugin Description
 *      * Version: 1.0.0
 *      * Author: You
 *      * Author URI: Your URI
 *      * ..........
 *      * /
 */