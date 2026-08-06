<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Foo Bar Plugin 0.0                                                        |
// +---------------------------------------------------------------------------+
// | functions.inc                                                             |
// |                                                                           |
// | This file does two things: 1) it implements the necessary Geeklog Plugin  |
// | API methods and 2) implements all the common code needed by this plugin.  |
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

/**
 * @package FooBar
 */

if (strpos(strtolower($_SERVER['PHP_SELF']), 'functions.inc') !== false) {
    die ('This file can not be used on its own.');
}

/**
 * Language file include
 */
$plugin_path = $_CONF['path'] . 'plugins/foobar/';
$langfile = $plugin_path . 'language/' . $_CONF['language'] . '.php';

if (file_exists($langfile)) {
    require_once $langfile;
} else {
    require_once $plugin_path . 'language/english.php';
}

{optional:use_config_ui}
/**
 * Check and see if we need to load the plugin configuration
 */
if (!isset($_FOOBAR_CONF)) {
    require_once $_CONF['path_system'] . 'classes/config.class.php';

    $foobar_config = config::get_instance();
    $_FOOBAR_CONF = $foobar_config->get_config('foobar');
}
{/optional:use_config_ui}
{optional:use_sql}
/**
 * Foo Bar plugin table(s)
 */
$_TABLES['foobar'] = $_DB_table_prefix . 'foobar';

{/optional:use_sql}

// +---------------------------------------------------------------------------+
// | Geeklog Plugin API Implementation                                         |
// +---------------------------------------------------------------------------+

/**
 * Returns the item(s) for this plugin that should appear on the main menu
 *
 * NOTE: this MUST return the url/value pairs in the following format
 * $<arrayname>[<label>] = <url>
 *
 * @return   mixed   menu entry, or boolean false if disabled / hidden
 *
 */
function plugin_getmenuitems_foobar()
{
    global $_CONF, $LANG_FOOBAR_1;
    
    // CORRECTIF PHP 8 : Initialisation stricte de la variable
    $menuitems = array(); 

    $url = $_CONF['site_url'] . '/foobar/index.php';
    $menuitems[$LANG_FOOBAR_1['plugin_name']] = $url;

    return $menuitems;
}

{optional:use_sql}
/**
 * Geeklog is asking us to provide any items that show up in the type
 * drop-down on search.php.  Let's users search for links.
 *
 * @return   array   (plugin name/entry title) pair for the dropdown
 *
 */
/*
function plugin_searchtypes_foobar()
{
    global $LANG_FOOBAR_1;

    $tmp['foobar'] = $LANG_FOOBAR_1['plugin_name'];

    return $tmp;
}
*/

/**
 * This searches for entries matching the user query and returns an array for the
 * header and table rows back to search.php where it will be formated and printed
 *
 * @param    string  $query      Keywords user is looking for
 * @param    date    $datestart  Start date to get results for
 * @param    date    $dateend    End date to get results for
 * @param    string  $topic      The topic they were searching in
 * @param    string  $type       Type of items they are searching, or 'all' (deprecated)
 * @param    int     $author     Get all results by this author
 * @param    string  $keyType    search key type: 'all', 'phrase', 'any'
 * @param    int     $page       page number of current search (deprecated)
 * @param    int     $perpage    number of results per page (deprecated)
 * @return   object              search result object
 *
 */
/*
function plugin_dopluginsearch_foobar($query, $datestart, $dateend, $topic, $type, $author, $keyType, $page, $perpage)
{
    global $_TABLES, $LANG_FOOBAR_1;

    // CORRECTIF SÉCURITÉ : Utilisation de DB_escape_string au lieu de addslashes
    $query = trim(DB_escape_string($query));

    // NOTE: Update SQL query to match the actual table used by the
    //       Foo Bar plugin!

    $sql = "SELECT fbid AS id, title, description, UNIX_TIMESTAMP(date) AS date, owner_id AS uid, hits, ";
    $sql .= "CONCAT('/foobar/index.php?item=', fbid) AS url ";
    $sql .= "FROM {$_TABLES['foobar']} WHERE date <> 1 ";
    $sql .= COM_getPermSQL('AND') . ' ';

    if (!empty($datestart) && !empty($dateend)) {
        $delim = substr($datestart, 4, 1);
        if (!empty($delim)) {
            $DS = explode($delim, $datestart);
            $DE = explode($delim, $dateend);
            $startdate = mktime(0,0,0,$DS[1],$DS[2],$DS[0]);
            $enddate = mktime(23,59,59,$DE[1],$DE[2],$DE[0]);
            $sql .= "AND (UNIX_TIMESTAMP(date) BETWEEN '$startdate' AND '$enddate') ";
        }
    }
    if (!empty ($author)) {
        $sql .= "AND (owner_id = '$author') ";
    }

    $search = new SearchCriteria('foobar', $LANG_FOOBAR_1['plugin_name']);
    $columns = array('title', 'description');
    list($sql,$ftsql) = $search->buildSearchSQL($keyType, $query, $columns, $sql);
    $search->setSQL($sql);
    $search->setFTSQL($ftsql);
    $search->setRank(3);

    return $search;
}
*/

{/optional:use_sql}
/**
 * Implements a [foobar:] autotag.
 *
 * @param    string  $op         operation to perform
 * @param    string  $content    item (e.g. story text), including the autotag
 * @param    array   $autotag    parameters used in the autotag
 * @param    mixed               tag names (for $op='tagname') or formatted content
 *
 */
/*
function plugin_autotags_foobar($op, $content = '', $autotag = '')
{
    global $_CONF, $_TABLES, $LANG_FOOBAR_1;

    if ($op == 'tagname') {
        return 'foobar';
    } elseif ($op == 'parse') {
        $fbid = COM_applyFilter($autotag['parm1']);
        if (! empty($fbid)) {
            $url = $_CONF['site_url'] . '/foobar/index.php?item=' . $fbid;
            if (empty($autotag['parm2'])) {
                $linktext = 'Foo Bar'; // or get title from db
            } else {
                $linktext = $autotag['parm2'];
            }
            $link = COM_createLink($linktext, $url);
            $content = str_replace($autotag['tagstr'], $link, $content);
        }

        return $content;
    }
}
*/

/**
 * Return plugin entry for "Command and Control" (moderation.php)
 *
 * @return  array  Array containing (plugin name, admin url, url of plugin icon)
 *
 */
function plugin_cclabel_foobar()
{
    global $_CONF, $LANG_FOOBAR_1;

    $retval = array();
    if (SEC_hasRights('foobar.admin')) {
        $retval = array($LANG_FOOBAR_1['plugin_name'],
            $_CONF['site_admin_url'] . '/plugins/foobar/index.php',
            plugin_geticon_foobar());
    }

    return $retval;
}

/**
 * Return plugin entry for the Admins Only block
 *
 * @return array Array containing (plugin name, plugin admin url, # of plugin items or '')
 */
function plugin_getadminoption_foobar()
{
    global $_CONF, $LANG_FOOBAR_1;

    if (SEC_hasRights('foobar.admin')) {
        return array($LANG_FOOBAR_1['plugin_name'],
            $_CONF['site_admin_url'] . '/plugins/foobar/index.php', 0);
    }
}

/**
 * Returns the URL of the plugin's icon
 *
 * @return   string      URL of the icon
 *
 */
function plugin_geticon_foobar()
{
    global $_CONF;

    // popular alternative location:
    // return $_CONF['site_url'] . '/foobar/images/foobar.png';

    return $_CONF['site_admin_url'] . '/plugins/foobar/images/foobar.png';
}

/**
 * Returns the current plugin code version
 *
 * @return string    plugin version
 */
function plugin_chkVersion_foobar()
{
    global $_CONF;

    require_once $_CONF['path'] . 'plugins/foobar/autoinstall.php';

    $inst_parms = plugin_autoinstall_foobar('foobar');

    return $inst_parms['info']['pi_version'];
}

/**
 * Update the plugin
 *
 * @return   int     Number of message to display or true for success
 *
 */
function plugin_upgrade_foobar()
{
    global $_CONF, $_TABLES;

    $installed_version = DB_getItem($_TABLES['plugins'], 'pi_version', "pi_name = 'foobar'");
    $code_version = plugin_chkVersion_foobar();
    
    if ($installed_version == $code_version) {
        // nothing to do
        return true;
    }

    require_once $_CONF['path'] . 'plugins/foobar/autoinstall.php';

    if (! plugin_compatible_with_this_version_foobar('foobar')) {
        return 3002;
    }

    // CORRECTIF : Modern Upgrade Matrix en cascade per Geeklog 2.2.2 specs
    switch ($installed_version) {
        case '0.0.0': // Remplacer par l'ancienne version si besoin
            // Placer ici la logique SQL d'upgrade
            $installed_version = $code_version;
            // Ne pas mettre de break pour permettre la cascade
        case $code_version:
            break;
        default:
            return false;
    }

    // update plugin version number
    $inst_parms = plugin_autoinstall_foobar('foobar');
    $pi_gl_version = $inst_parms['info']['pi_gl_version'];

    DB_query("UPDATE {$_TABLES['plugins']} SET pi_version = '$code_version', pi_gl_version = '$pi_gl_version' WHERE pi_name = 'foobar'");

    return true;
}

/**
 * Automatic uninstall function for plugins
 *
 * This code is automatically uninstalling the plugin.
 * It passes an array to the core code function that removes
 * tables, groups, features and php blocks from the tables.
 * Additionally, this code can perform special actions that cannot be
 * foreseen by the core code (interactions with other plugins for example)
 *
 * @return   array   Plugin information
 *
 */
function plugin_autouninstall_foobar()
{
    $out = array (
        /* give the name of the tables, without $_TABLES[] */
{optional:use_sql}
        'tables' => array('foobar'),
{!optional:use_sql}
        'tables' => array(/* e.g. 'foobar' */),
{/optional:use_sql}
        /* give the full name of the group, as in the db */
        'groups' => array('Foo Bar Admin'),
        /* give the full name of the feature, as in the db */
        'features' => array('foobar.admin'),
        /* give the full name of the block, including 'phpblock_', etc */
        'php_blocks' => array(),
        /* give all vars with their name */
        'vars' => array()
    );

    return $out;
}
