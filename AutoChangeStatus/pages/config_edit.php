<?php
auth_reauthenticate( );
access_ensure_global_level( config_get( 'manage_plugin_threshold' ) );

$f_change_status_user = gpc_get_string('change_status_user');

if( plugin_config_get( 'change_status_user' ) != $f_change_status_user) {
	plugin_config_set( 'change_status_user', $f_change_status_user );
}

print_successful_redirect( plugin_page( 'config', true ) );
