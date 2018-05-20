<?php
/*
Plugin Name: AudioVerse
Description: AudioVerse plugin
Author: Nathan Arthur
Version: 1.0
Author URI: http://NathanArthur.com/
*/

namespace Avorg;

if ( !\defined( 'ABSPATH' ) ) exit;

include_once( dirname(__FILE__) .  "/vendor/autoload.php" );

$factory = new Factory();
$plugin  = $factory->makePlugin();
$adminPanel = $factory->makeAdminPanel();
$router = $factory->makeRouter();

\register_activation_hook( __FILE__, array( $plugin, "activate" ) );
\register_deactivation_hook( __FILE__, "plugin_deactivate" );

\add_action( "admin_menu", array( $adminPanel, "register" ) );
\add_action( 'init', array( $router, "addRewriteRules" ) );
\add_action( 'init', array( $plugin, "init" ) );

\add_filter( 'the_content', array( $plugin, "addMediaPageUI" ) );

function plugin_deactivate() {}