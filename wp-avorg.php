<?php
/*
Plugin Name: AudioVerse
Description: AudioVerse plugin
Author: Nathan Arthur
Version: 1.0
Author URI: http://NathanArthur.com/
*/

namespace Avorg;

if (!\defined('ABSPATH')) exit;

define( "AVORG_BASE_PATH", dirname(__FILE__) );

include_once(AVORG_BASE_PATH . "/vendor/autoload.php");

$factory = new Factory();
$plugin = $factory->getPlugin();
$adminPanel = $factory->getAdminPanel();
$contentBits = $factory->getContentBits();
$mediaPage = $factory->getMediaPage();
$router = $factory->getRouter();

\register_activation_hook(__FILE__, array($plugin, "activate"));
\register_deactivation_hook(__FILE__, "plugin_deactivate");

\add_action("admin_menu", array($adminPanel, "register"));
\add_action('init', array($plugin, "init"));
\add_action("add_meta_boxes", array($contentBits, "addIdentifierMetaBox"));
\add_action('save_post', array($contentBits, "saveIdentifierMetaBox"));
\add_action("wp_enqueue_scripts", array($plugin, "enqueueScripts"));

\add_filter('the_content', array($mediaPage, "addMediaPageUI"));
\add_filter('locale', array($router, "setLocale"));

function plugin_deactivate()
{
}