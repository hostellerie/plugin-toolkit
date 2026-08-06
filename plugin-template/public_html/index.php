<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Foo Bar Plugin 0.0                                                        |
// +---------------------------------------------------------------------------+
// | public_html/index.php                                                     |
// |                                                                           |
// | Public page template for plugins (Geeklog 2.2.2+)                         |
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

// Load Geeklog core
require_once '../lib-common.php';

global $_CONF, $_USER, $_PLUGINS, $LANG_FOOBAR_1;

$plugin_name = 'foobar';
$plugin_path = $_CONF['path'] . 'plugins/' . $plugin_name . '/';

// Load plugin language file
$langfile = $plugin_path . 'language/' . $_CONF['language'] . '.php';

if (file_exists($langfile)) {
    require_once $langfile;
} else {
    require_once $plugin_path . 'language/english.php';
}

// Check if plugin is active
if (!in_array($plugin_name, $_PLUGINS)) {
    COM_handle404();
    exit;
}

$display = '';

// Build the public user interface
$display .= COM_startBlock($LANG_FOOBAR_1['plugin_name']);

$username = isset($_USER['username']) ? $_USER['username'] : '';
$display .= '<p>' . sprintf($LANG_FOOBAR_1['welcome_public'], $username) . '</p>';

$display .= COM_endBlock();

// Wrap the content in the site theme and output
$display = COM_createHTMLDocument($display, array(
    'pagetitle' => $LANG_FOOBAR_1['plugin_name']
));

COM_output($display);
