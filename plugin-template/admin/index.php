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
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the              |
// | GNU General Public License for more details.                              |
// |                                                                           |
// | You should have received a copy of the GNU General Public License         |
// | along with this program; if not, write to the Free Software Foundation,   |
// | Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.            |
// |                                                                           |
// +---------------------------------------------------------------------------+

// Load Geeklog core and administrator authentication
require_once '../../../lib-common.php';
require_once '../../auth.inc.php';

global $_CONF, $_USER, $_PLUGINS, $MESSAGE, $LANG_FOOBAR_1;

$pluginName = 'foobar';
$pluginPath = $_CONF['path'] . 'plugins/' . $pluginName . '/';
$display = '';

// Load the plugin language file
$languageFile = $pluginPath . 'language/' . $_CONF['language'] . '.php';
$englishLanguageFile = $pluginPath . 'language/english.php';

if (is_file($languageFile)) {
    require_once $languageFile;
} elseif (is_file($englishLanguageFile)) {
    require_once $englishLanguageFile;
} else {
    COM_errorLog(
        'Unable to load a language file for the ' . $pluginName . ' plugin.'
    );

    COM_handle404();
    exit;
}

// Check whether the plugin is active
if (!in_array($pluginName, $_PLUGINS, true)) {
    COM_handle404();
    exit;
}

// Check for plugin administration rights
if (!SEC_hasRights($pluginName . '.admin')) {
    $pageTitle = $MESSAGE[30];

    $display .= COM_showMessageText($MESSAGE[29], $pageTitle);

    $username = $_USER['username'] ?? 'Anonymous';

    COM_accessLog(
        'User ' . $username
        . ' tried to illegally access the '
        . $pluginName
        . ' administration screen.'
    );

    $display = COM_createHTMLDocument(
        $display,
        array(
            'pagetitle' => $pageTitle,
        )
    );

    COM_output($display);
    exit;
}

$pluginTitle = $LANG_FOOBAR_1['plugin_name'] ?? 'Foo Bar';

// Handle form submission before creating a new CSRF token
if (
    isset($_SERVER['REQUEST_METHOD'])
    && $_SERVER['REQUEST_METHOD'] === 'POST'
) {
    /*
     * SEC_checkToken() displays the Geeklog reauthentication page
     * and terminates the script automatically when the token is invalid.
     */
    SEC_checkToken();

    $title = isset($_POST['title'])
        ? trim(COM_applyFilter($_POST['title']))
        : '';

    /*
     * Process the submitted value here.
     *
     * Example:
     * PLG_saveTitle($title);
     */

    $display .= COM_startBlock($pluginTitle);
    $display .= '<p>'
        . htmlspecialchars(
            $LANG_FOOBAR_1['form_submitted'],
            ENT_QUOTES,
            COM_getEncodingt()
        )
        . '</p>';
    $display .= COM_endBlock();

    $display = COM_createHTMLDocument(
        $display,
        array(
            'pagetitle' => $pluginTitle,
            'menu' => $pluginName,
        )
    );

    COM_output($display);
    exit;
}

// Create a CSRF token only when displaying the form
$token = SEC_createToken();

// Build the administration interface
$username = $_USER['username'] ?? '';

$safePluginTitle = htmlspecialchars(
    $pluginTitle,
    ENT_QUOTES,
    COM_getEncodingt()
);

$safeUsername = htmlspecialchars(
    $username,
    ENT_QUOTES,
    COM_getEncodingt()
);

$welcomeMessage = sprintf(
    $LANG_FOOBAR_1['welcome_admin'],
    $safeUsername
);

$action = $_CONF['site_admin_url']
    . '/plugins/'
    . $pluginName
    . '/index.php';

$display .= COM_startBlock($safePluginTitle);

$display .= '<p>' . $welcomeMessage . '</p>';

$display .= '<form method="post" action="'
    . htmlspecialchars($action, ENT_QUOTES, COM_getEncodingt())
    . '">';

$display .= '<input type="hidden" name="'
    . CSRF_TOKEN
    . '" value="'
    . htmlspecialchars($token, ENT_QUOTES, COM_getEncodingt())
    . '">';

$display .= '<p>';
$display .= '<label for="title">'
    . htmlspecialchars(
        $LANG_FOOBAR_1['label_title'],
        ENT_QUOTES,
        COM_getEncodingt()
    )
    . '</label> ';

$display .= '<input type="text"'
    . ' name="title"'
    . ' id="title"'
    . ' value=""'
    . ' maxlength="255"'
    . '>';
$display .= '</p>';

$display .= '<p>';
$display .= '<button type="submit" class="uk-button uk-button-primary">';
$display .= htmlspecialchars(
    $LANG_FOOBAR_1['submit'],
    ENT_QUOTES,
    COM_getEncodingt()
);
$display .= '</button>';
$display .= '</p>';

$display .= '</form>';

$display .= COM_endBlock();

// Wrap the content in the Geeklog theme and output it
$display = COM_createHTMLDocument(
    $display,
    array(
        'pagetitle' => $pluginTitle,
        'menu' => $pluginName,
    )
);

COM_output($display);
