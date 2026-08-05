<?php
/**
 * Public page template for plugins (Geeklog 2.2.2+)
 *
 * Place in plugins/<plugin>/public_html/index.php
 * Uses COM_createHTMLDocument() instead of COM_siteHeader()/COM_siteFooter().
 */

require_once '../../../lib-common.php';

global $_CONF, $_USER, $_PLUGINS, $LANG_FOOBAR_1;
$plugin_name = 'foobar';
$plugin_path = $_CONF['path'] . 'plugins/' . $plugin_name . '/';

// Load plugin language (best-effort)
$langfile = $plugin_path . 'language/' . $_CONF['language'] . '.php';
if (file_exists($langfile)) {
    require_once $langfile;
} else {
    require_once $plugin_path . 'language/english.php';
}

// If plugin not active, return 404 or redirect
if (!in_array($plugin_name, $_PLUGINS)) {
    COM_handle404();
    exit;
}

// Build page content
$content = '';
$content .= COM_startBlock($LANG_FOOBAR_1['plugin_name']);
$content .= '<p>' . sprintf($LANG_FOOBAR_1['welcome_public'], isset($_USER['username']) ? $_USER['username'] : '') . '</p>';
$content .= COM_endBlock();

// Create full HTML document (Geeklog 2.2+)
echo COM_createHTMLDocument($content, [
    'pagetitle' => $LANG_FOOBAR_1['plugin_name']
]);
