<?php

/**
 * Admin page template for plugins (Geeklog 2.2.2+)
 *
 * Place in plugins/<plugin>/admin/index.php
 * Uses COM_createHTMLDocument() instead of COM_siteHeader()/COM_siteFooter().
 */

require_once '../../../lib-common.php';
require_once '../../auth.inc.php';

global $_CONF, $_USER, $_PLUGINS, $LANG_ACCESS, $LANG_FOOBAR_1;

// Load plugin language if not already loaded (best-effort)
$plugin_path = $_CONF['path'] . 'plugins/foobar/';
$langfile = $plugin_path . 'language/' . $_CONF['language'] . '.php';
if (file_exists($langfile)) {
    require_once $langfile;
} else {
    require_once $plugin_path . 'language/english.php';
}

// Ensure user has admin rights for this plugin
if (!SEC_hasRights('foobar.admin')) {
    $content = '';
    $content .= COM_startBlock($LANG_ACCESS['accessdenied']);
    $content .= COM_showMessageText($LANG_ACCESS['accessdeniedmsg'], $LANG_ACCESS['accessdenied']);
    $content .= COM_endBlock();

    echo COM_createHTMLDocument($content, array('pagetitle' => $LANG_ACCESS['accessdenied']));
    exit;
}

// Create token for forms
$token = SEC_createToken();

// Handle POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!SEC_checkToken()) {
        COM_errorLog('Invalid CSRF token for foobar admin action');
        $content = '';
        $content .= COM_startBlock($LANG_FOOBAR_1['plugin_name']);
        $content .= COM_showMessageText($LANG_FOOBAR_1['invalid_token'], $LANG_FOOBAR_1['plugin_name']);
        $content .= COM_endBlock();
        echo COM_createHTMLDocument($content, array('pagetitle' => $LANG_FOOBAR_1['plugin_name']));
        exit;
    }

    // Example processing (sanitize inputs)
    $title = isset($_POST['title']) ? COM_applyFilter($_POST['title']) : '';

    $content = '';
    $content .= COM_startBlock($LANG_FOOBAR_1['plugin_name']);
    $content .= '<p>' . $LANG_FOOBAR_1['form_submitted'] . '</p>';
    $content .= COM_endBlock();

    echo COM_createHTMLDocument($content, array('pagetitle' => $LANG_FOOBAR_1['plugin_name']));
    exit;
}

// MAIN: admin UI
$content = '';
$content .= COM_startBlock($LANG_FOOBAR_1['plugin_name']);
$content .= '<p>' . sprintf($LANG_FOOBAR_1['welcome_admin'], isset($_USER['username']) ? $_USER['username'] : '') . '</p>';

$action = COM_buildUrl($_CONF['site_url'] . '/plugins/foobar/admin/index.php');
$content .= '<form method="post" action="' . $action . '">';
$content .= '<input type="hidden" name="token" value="' . $token . '" />';
$content .= '<p><label for="title">' . $LANG_FOOBAR_1['label_title'] . '</label> <input type="text" name="title" id="title" value="" /></p>';
$content .= '<p><input type="submit" value="' . $LANG_FOOBAR_1['submit'] . '" /></p>';
$content .= '</form>';

$content .= COM_endBlock();

echo COM_createHTMLDocument($content, array('pagetitle' => $LANG_FOOBAR_1['plugin_name']));
