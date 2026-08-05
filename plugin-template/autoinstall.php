<?php
/**
 * Functional autoinstall template for Geeklog 2.2.2+
 *
 * This template uses the "function style" autoinstall API which is safer
 * because it does not execute code at include time. It returns an array
 * describing the plugin (info, groups, features, mappings, tables).
 *
 * Copy this file to plugins/<yourplugin>/autoinstall.php and rename the
 * function to plugin_autoinstall_<yourplugin>(). Do NOT execute logic at the
 * top level of this file. Save the file in UTF-8 without BOM. Do not add a
 * closing PHP tag.
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

    $tables = array(
        // list your plugin tables here (without prefix)
        // 'foobar'
    );

    return array(
        'info'     => $info,
        'groups'   => $groups,
        'features' => $features,
        'mappings' => $mappings,
        'tables'   => $tables
    );
}
