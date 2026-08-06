<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Foo Bar Plugin 0.0                                                        |
// +---------------------------------------------------------------------------+
// | admin/index.php                                                           |
// |                                                                           |
// | Admin page template for plugins (Geeklog 2.2.2+)                          |
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

// Load Geeklog core and authorization
require_once '../../../lib-common.php';
require_once '../../auth.inc.php';

global $_CONF, $_USER, $_PLUGINS, $MESSAGE, $LANG_FOOBAR_1;

// Load plugin language file
$plugin_path = $_CONF['path'] . 'plugins/foobar/';
$langfile = $plugin_path . 'language/' . $_CONF['language'] . '.php';

if (file_exists($langfile)) {
    require_once $langfile;
} else {
    require_once $plugin_path . 'language/english.php';
}

$display = '';

// Check for plugin administration rights
if (!SEC_hasRights('foobar.admin')) {
    $display .= COM_showMessageText($MESSAGE[29], $MESSAGE[30]);
    $display = COM_createHTMLDocument($display, array('pagetitle' => $MESSAGE[30]));
    
    $username = isset($_USER['username']) ? $_USER['username'] : 'Anonymous';
    COM_accessLog("User {$username} tried to illegally access the foobar administration screen.");
    
    COM_output($display);
    exit;
}

// Generate CSRF token for the form
$token = SEC_createToken();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Verify CSRF token
    if (!SEC_checkToken()) {
        COM_errorLog('Invalid CSRF token for foobar admin action');
        
        $display .= COM_startBlock($LANG_FOOBAR_1['plugin_name']);
        $display .= COM_showMessageText($LANG_FOOBAR_1['invalid_token'], $LANG_FOOBAR_1['plugin_name']);
        $display .= COM_endBlock();
        
        $display = COM_createHTMLDocument($display, array('pagetitle' => $LANG_FOOBAR_1['plugin_name']));
        COM_output($display);
        exit;
    }

    // Sanitize and process form data
    $title = isset($_POST['title']) ? COM_applyFilter($_POST['title']) : '';

    $display .= COM_startBlock($LANG_FOOBAR_1['plugin_name']);
    $display .= '<p>' . $LANG_FOOBAR_1['form_submitted'] . '</p>';
    $display .= COM_endBlock();

    $display = COM_createHTMLDocument($display, array('pagetitle' => $LANG_FOOBAR_1['plugin_name']));
    COM_output($display);
    exit;
}

// Build the administration user interface
$display .= COM_startBlock($LANG_FOOBAR_1['plugin_name']);

$username = isset($_USER['username']) ? $_USER['username'] : '';
$display .= '<p>' . sprintf($LANG_FOOBAR_1['welcome_admin'], $username) . '</p>';

$action = COM_buildUrl($_CONF['site_url'] . '/plugins/foobar/admin/index.php');

$display .= '<form method="post" action="' . $action . '">';
$display .= '<input type="hidden" name="gltoken" value="' . $token . '" />';
$display .= '<p><label for="title">' . $LANG_FOOBAR_1['label_title'] . '</label> <input type="text" name="title" id="title" value="" /></p>';
$display .= '<p><input type="submit" value="' . $LANG_FOOBAR_1['submit'] . '" class="uk-button uk-button-primary" /></p>';
$display .= '</form>';

$display .= COM_endBlock();

// Wrap the content in the site theme and output
$display = COM_createHTMLDocument($display, array(
    'pagetitle' => $LANG_FOOBAR_1['plugin_name'],
    'menu' => 'foobar'
));

COM_output($display);
