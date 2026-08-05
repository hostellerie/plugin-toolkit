<?php
/**
 * Functional autoinstall template for Geeklog 2.2.2+
 *
 * This template uses the "function style" autoinstall API which is safer
 * because it does not execute code at include time. It returns an array
 * describing the plugin (info, groups, features, mappings, tables).
 *
 * Additionally this template provides helper stubs for loading the plugin
 * configuration from the database and for checking compatibility with the
 * current Geeklog installation. Replace 'foobar' with your plugin name and
 * rename the functions accordingly (plugin_autoinstall_<yourplugin>,
 * plugin_load_configuration_<yourplugin>, plugin_compatible_with_this_version_<yourplugin>).
 *
 * Do NOT execute logic at the top level of this file. Save the file in
 * UTF-8 without BOM. Do not add a closing PHP tag.
 */

/**
 * Return autoinstall information for the plugin
 */
function plugin_autoinstall_foobar($pi_name)
{
    // Replace the hardcoded values below with your plugin's values.
    $pi_name         = 'foobar';
    $pi_display_name = 'Foo Bar';

    $info = array(
        'pi_name'         => $pi_name,
        'pi_display_name' => $pi_display_name,
        'pi_version'      => '0.0.0',
        'pi_gl_version'   => '2.2.2', // minimum Geeklog required
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

    // Suggested table(s) for your plugin. List names WITHOUT the DB prefix.
    // The generator will replace 'foobar' with your plugin name when creating
    // a new plugin. You may change 'foobar_table' to the table name(s) you
    // need. Keep them unprefixed here.
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
 * Load plugin configuration from database (stub)
 *
 * This helper mirrors the pattern used by many core plugins: it requires the
 * install_defaults.php from the plugin path and calls plugin_initconfig_<plugin>().
 * Implement plugin_initconfig_<yourplugin>() in install_defaults.php.
 */
function plugin_load_configuration_foobar($pi_name)
{
    global $_CONF;

    $base_path = $_CONF['path'] . 'plugins/' . $pi_name . '/';

    require_once $base_path . 'install_defaults.php';

    return plugin_initconfig_foobar();
}


/**
 * Check if the plugin is compatible with this Geeklog version (stub)
 *
 * Returns true when the runtime provides the minimal functions/settings the
 * plugin expects. Adjust checks to your plugin's real requirements.
 */
function plugin_compatible_with_this_version_foobar($pi_name)
{
    global $_CONF, $_DB_dbms;

    // check if we support the DBMS the site is running on
    $dbFile = $_CONF['path'] . 'plugins/' . $pi_name . '/sql/' . $_DB_dbms . '_install.php';
    if (!file_exists($dbFile)) {
        return false;
    }

    // Example compatibility checks — adapt as needed
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
