<?php
/**
 * Example autoinstall template for Geeklog 2.2.2+
 */

$autoinstall['foobar'] = array(
    'info' => array(
        'pi_name'         => 'foobar',
        'pi_display_name' => 'Foo Bar',
        'pi_version'      => '0.0.0',
        'pi_gl_version'   => '2.2.2', // minimum Geeklog required
        'pi_homepage'     => 'http://www.example.com/'
    ),
    'groups' => array(
        'Foo Bar Admin' => 'Users in this group can administer the Foo Bar plugin'
    ),
    'features' => array(
        'foobar.admin' => 'Full access to Foo Bar plugin'
    ),
    'mappings' => array(
        'foobar.admin' => array('Foo Bar Admin')
    ),
    'tables' => array(
{optional:use_sql}
        'foobar'
{/optional:use_sql}
    ),
    // plugin dependencies (other plugins) - optional but recommended
    'requires' => array(
{optional:use_sql}
        // Example: require MySQL support (if you depend on a DB)
        // array('plugin' => 'staticpages', 'version' => '1.7.0')
{/optional:use_sql}
    ),
    // Alternative map-style dependency declaration:
    'plugin_dependencies' => array(
        // 'staticpages' => '1.7.0'
    )
);
?>
