<?php
/**
 * Uninstall handler. Deliberately does nothing to imported data — uninstalling the plugin
 * removes its code, not the client's Avec data. Use the admin "Limpar dados" action (or
 * `wp avec-clone clean`) beforehand if you actually want that data gone.
 *
 * @package AvecClone
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}
