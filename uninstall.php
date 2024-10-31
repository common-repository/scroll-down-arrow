<?php

// Exit if accessed directly
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) exit;


/**
 * Uninstall this plugin
 *
 */


/** Delete plugin options */
delete_option( 'epda_version' );
delete_option( 'epda_version_first' );
delete_option( 'epda_config' );
