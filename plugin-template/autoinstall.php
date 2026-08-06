<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Foo Bar Plugin 0.0                                                        |
// +---------------------------------------------------------------------------+
// | autoinstall.php                                                           |
// |                                                                           |
// | Functional autoinstall template for Geeklog 2.2.2+                        |
// +---------------------------------------------------------------------------+
// | Copyright (C) yyyy by the following authors:                              |
// |                                                                           |
// | Authors: author name goes here                                            |
// +---------------------------------------------------------------------------+
// | Created with the Geeklog Plugin Toolkit.                                  |
// +---------------------------------------------------------------------------+
// |                                                                           |
// | This program is free software; you can redistribute it and/or             |
// | modify it under the terms of the GNU General Public License               |
// | as published by the Free Software Foundation; either version 2            |
// | of the License, or (at your option) any later version.                    |
// |                                                                           |
// | This program is distributed in the hope that it will be useful,           |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of            |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the             |
// | GNU General Public License for more details.                              |
// |                                                                           |
// | You should have received a copy of the GNU General Public License         |
// | along with this program; if not, write to the Free Software Foundation,   |
// | Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.           |
// |                                                                           |
// +---------------------------------------------------------------------------+

// Do not execute logic at the top level of this file.
// Save the file in UTF-8 without BOM. Do not add a closing PHP tag.

/**
 * Return autoinstall information for the plugin
 */
function plugin_autoinstall_foobar($pi_name)
{
    $pi_name         = 'foobar';
    $pi_display_name = 'Foo Bar';

    $info = array(
        'pi_name'         => $pi_name,
        'pi_display_name' => $pi_display_name,
        'pi_version'      => '0.0.0',
        'pi_gl_version'   => '2.2.2',
        'pi_homepage'     => 'http://www.example.com/'
    );

    $groups = array(
        $pi_display_name . ' Admin' => 'Users in this group can administer the ' . $pi_display_name . ' plugin'
    );

    $features = array(
        $pi_name . '.admin' => 'Full access to ' . $pi_display_name . ' plugin'
    );

    $mappings = array(
        $pi_name . '.admin' => array($pi_display_name . ' Admin')
    );

    $tables = array(
        'foobar_table'
    );

    return array(
        'info'     => $info,
        'groups'   => $groups,
        'features' => $features,
        'mappings' => $mappings,
        'tables'   => $tables
    );
}

/**
 * Load plugin configuration from database
 */
function plugin_load_configuration_foobar($pi_name)
{
    global $_CONF;

    $base_path = $_CONF['path'] . 'plugins/' . $pi_name . '/';

    require_once $base_path . 'install_defaults.php';

    return plugin_initconfig_foobar();
}

/**
 * Check if the plugin is compatible with this Geeklog version
 */
function plugin_compatible_with_this_version_foobar($pi_name)
{
    global $_CONF, $_DB_dbms;

    // Check if we support the DBMS the site is running on
    $dbFile = $_CONF['path'] . 'plugins/' . $pi_name . '/sql/' . $_DB_dbms . '_install.php';
    if (!file_exists($dbFile)) {
        return false;
    }

    // Check for required Geeklog core functions
    if (!function_exists('SEC_getGroupDropdown')) {
        return false;
    }

    if (!function_exists('SEC_createToken')) {
        return false;
    }

    if (!function_exists('COM_showMessageText')) {
        return false;
    }

    if (!isset($_CONF['meta_tags'])) {
        return false;
    }

    if (!function_exists('SEC_getTokenExpiryNotice')) {
        return false;
    }

    if (!function_exists('SEC_loginRequiredForm')) {
        return false;
    }

    if (!function_exists('CTL_plugin_templatePath')) {
        return false;
    }

    return true;
}
